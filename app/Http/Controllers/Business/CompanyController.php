<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CompanyController extends Controller
{
    public function edit()
    {
        $company = Auth::user()->company ?? new Company();
        $tags = Tag::availableForCompany($company?->id)->orderBy('label')->get();
        $myCustomTags = $company->exists ? $company->customTags()->latest()->get() : collect();
        return view('business.company.edit', compact('company', 'tags', 'myCustomTags'));
    }

    public function requestDocumentUpdate(Request $request)
    {
        $request->validate([
            'document_update_reason' => 'required|string|max:1000',
        ], [
            'document_update_reason.required' => 'Debe explicar el motivo para cambiar sus documentos.'
        ]);

        $company = Auth::user()->company;
        
        if (!$company) {
            return back()->withErrors('No tienes una empresa registrada.');
        }

        $company->update([
            'document_update_status' => 'requested',
            'document_update_reason' => $request->document_update_reason,
        ]);

        return back()->with('success', 'Solicitud enviada. Espera a que el administrador habilite la edición.');
    }

    public function update(Request $request)
    {
        $user    = Auth::user();
        $section = $request->input('section', 'all');

        // ─── Validación por sección ────────────────────────────
        if ($section === 'general') {
            // Manejo preventivo de fallos de subida a nivel PHP antes de que Laravel tire el mensaje en inglés
            if ($request->hasFile('logo') && !$request->file('logo')->isValid()) {
                return back()->withInput()->withErrors(['logo' => 'El logo seleccionado no pudo procesarse o supera el límite del servidor. Por favor selecciona una imagen de hasta 5 MB.']);
            }
            if ($request->hasFile('banner') && !$request->file('banner')->isValid()) {
                return back()->withInput()->withErrors(['banner' => 'El banner seleccionado no pudo procesarse o supera el límite del servidor. Por favor selecciona una imagen de hasta 10 MB.']);
            }

            $request->validate([
                'name'                  => 'required|string|max:255',
                'description'           => 'nullable|string',
                'category'              => 'nullable|string|max:100',
                'logo'                  => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,bmp,svg,avif,jfif,heic,heif|max:5120',
                'banner'                => 'nullable|file|mimes:jpeg,png,jpg,gif,webp,bmp,svg,avif,jfif,heic,heif|max:10240',
                'establishment_photos.*'=> 'nullable|file|mimes:jpeg,png,jpg,webp,svg,avif|max:5120',
            ], [
                'name.required'   => 'El nombre de la empresa es obligatorio.',
                'banner.file'     => 'El banner debe ser un archivo de imagen.',
                'banner.mimes'    => 'El banner admite formatos JPG, PNG, WebP, SVG, GIF, AVIF o BMP.',
                'banner.max'      => 'El banner no puede superar los 10 MB.',
                'banner.uploaded' => 'No se pudo subir el banner. Verifica que el archivo no supere el tamaño permitido.',
                'logo.file'       => 'El logo debe ser un archivo de imagen.',
                'logo.mimes'      => 'El logo admite formatos JPG, PNG, WebP, SVG, GIF, AVIF o BMP.',
                'logo.max'        => 'El logo no puede superar los 5 MB.',
                'logo.uploaded'   => 'No se pudo subir el logo. Verifica que el archivo no supere el tamaño permitido.',
            ]);
        } elseif ($section === 'legal') {
            $isMexico = ($user->company && $user->company->isMexican()) || $request->input('country') === 'MX';
            if ($isMexico) {
                $request->validate([
                    'country'              => 'nullable|in:CO,MX',
                    'tipo_negocio'         => 'required|in:formal,informal',
                    'habeas_data_accepted' => 'accepted',
                    'direccion_fisica'     => 'required|string|max:255',
                    'state'                => 'nullable|string|max:100',
                    'city'                 => 'nullable|string|max:100',
                    'postal_code'          => 'nullable|string|max:20',
                    'tax_id'               => 'nullable|string|max:50',
                    'razon_social'         => ['nullable', 'string', 'max:255', 'regex:/^[\pL\pN\s\.\,\&\'\-\#\/]+$/u'],
                    'nombre_comercial'     => ['nullable', 'string', 'max:255', 'regex:/^[\pL\pN\s\.\,\&\'\-\#\/]+$/u'],
                    'nombre_representante' => ['nullable', 'string', 'max:255', 'regex:/^[\pL\s\.\'\-]+$/u'],
                    'birthdate'            => ['nullable', 'date', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
                ], [
                    'tipo_negocio.required'         => 'Debe seleccionar el tipo de negocio.',
                    'habeas_data_accepted.accepted' => 'Debe aceptar la política de privacidad.',
                    'direccion_fisica.required'     => 'La dirección física es obligatoria.',
                    'nombre_representante.regex'    => 'El nombre del representante legal solo puede contener letras y espacios (sin números).',
                    'birthdate.before_or_equal'     => 'El representante o propietario de la empresa debe ser mayor de 18 años.',
                ]);
            } else {
                $request->validate([
                    'tipo_negocio'         => 'required|in:formal,informal',
                    'habeas_data_accepted' => 'accepted',
                    'direccion_fisica'     => 'required|string|max:255',
                    'razon_social'         => ['required_if:tipo_negocio,formal', 'nullable', 'string', 'max:255', 'regex:/^[\pL\pN\s\.\,\&\'\-\#\/]+$/u'],
                    'nit'                  => ['required_if:tipo_negocio,formal', 'nullable', 'string', 'regex:/^[0-9]+(-[0-9])?$/'],
                    'nombre_comercial'     => ['required_if:tipo_negocio,informal', 'nullable', 'string', 'max:255', 'regex:/^[\pL\pN\s\.\,\&\'\-\#\/]+$/u'],
                    'nombre_representante' => ['required_if:tipo_negocio,formal', 'nullable', 'string', 'max:255', 'regex:/^[\pL\s\.\'\-]+$/u'],
                    'cedula_propietario'   => ['required', 'string', 'min:5', 'max:20', 'regex:/^[0-9]+$/'],
                    'birthdate'            => ['nullable', 'date', 'before_or_equal:' . now()->subYears(18)->format('Y-m-d')],
                    'camara_comercio_file' => 'nullable|file|mimes:pdf|max:5120',
                    'rut_file'             => 'nullable|file|mimes:pdf|max:5120',
                    'rut_personal_file'    => 'nullable|file|mimes:pdf|max:5120',
                ], [
                    'tipo_negocio.required'          => 'Debe seleccionar el tipo de negocio.',
                    'habeas_data_accepted.accepted'  => 'Debe aceptar la politica de datos.',
                    'direccion_fisica.required'      => 'La direccion fisica es obligatoria.',
                    'razon_social.required_if'       => 'La razon social es obligatoria para empresas formales.',
                    'razon_social.regex'             => 'La razón social puede contener letras, números y signos comerciales.',
                    'nit.required_if'                => 'El NIT es obligatorio para empresas formales.',
                    'nit.regex'                      => 'El NIT solo puede contener números y opcionalmente guion con dígito de verificación (ej. 123456789-1).',
                    'nombre_comercial.required_if'   => 'El nombre comercial es obligatorio.',
                    'nombre_comercial.regex'         => 'El nombre comercial puede contener letras, números y signos comerciales.',
                    'nombre_representante.regex'     => 'El nombre del representante legal solo puede contener letras y espacios (sin números).',
                    'cedula_propietario.required'    => 'La cédula es obligatoria.',
                    'cedula_propietario.regex'       => 'La cédula solo puede contener números (0-9).',
                    'cedula_propietario.min'         => 'La cédula debe tener al menos 5 dígitos numéricos.',
                    'birthdate.before_or_equal'      => 'El representante o propietario de la empresa debe ser mayor de 18 años.',
                ]);
            }
        } elseif ($section === 'contact') {
            $request->validate([
                'phone'                 => 'nullable|string|max:20',
                'phone_prefix'          => 'nullable|string|max:10',
                'email'                 => 'nullable|email',
                'instagram'             => 'nullable|string|max:100',
                'facebook'              => 'nullable|string|max:100',
                'website'               => 'nullable|url',
                'opening_time'          => 'nullable|string|max:10',
                'closing_time'          => 'nullable|string|max:10',
                'operating_days'        => 'nullable|array',
                'has_weekend_schedule'  => 'nullable|boolean',
                'weekend_opening_time'  => 'nullable|string|max:10',
                'weekend_closing_time'  => 'nullable|string|max:10',
                'latitude'              => 'nullable|numeric',
                'longitude'             => 'nullable|numeric',
                'address'               => 'nullable|string|max:500',
                'state'                 => 'nullable|string|max:100',
                'city'                  => 'nullable|string|max:100',
            ]);
        }

        // ─── Datos a guardar ────────────────────────────────────
        $data = [];

        if ($section === 'general') {
            $data = $request->only(['name', 'description', 'category', 'country', 'currency', 'state', 'city', 'postal_code']);

            // Crear categoría personalizada al guardar si se llenó el campo
            if ($request->filled('new_category_label')) {
                $company = $user->company;
                $label = trim($request->input('new_category_label'));
                $slugBase = Str::slug($label);
                $slug = $slugBase;
                $counter = 1;
                while (Tag::where('name', $slug)->exists()) {
                    $slug = $slugBase . '-' . ($company ? $company->id : '0') . ($counter > 1 ? "-{$counter}" : '');
                    $counter++;
                }
                $newTag = Tag::create([
                    'company_id' => $company?->id,
                    'name'       => $slug,
                    'label'      => $label,
                    'icon'       => $request->input('new_category_icon', 'bi-tag-fill'),
                    'theme'      => ['accent' => $request->input('new_category_color', '#7F1D3F')],
                ]);
                $data['category'] = $newTag->label;
            }

            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                $data['logo'] = $request->file('logo')->store('logos', 'public');
            }

            if ($request->hasFile('banner') && $request->file('banner')->isValid()) {
                $path = $request->file('banner')->store('banners', 'public');
                $data['banner'] = $path;
                Log::info("Banner guardado en: {$path}");
            } else {
                Log::warning("Banner no encontrado en request. hasFile=" . ($request->hasFile('banner') ? 'true' : 'false'));
            }

            // Gestión de fotos del establecimiento comercial
            $company = $user->company;
            $currentPhotos = ($company && is_array($company->establishment_photos)) ? $company->establishment_photos : [];

            // Eliminar fotos marcadas
            if ($request->has('delete_establishment_photos')) {
                $toDelete = $request->input('delete_establishment_photos');
                if (is_array($toDelete)) {
                    $currentPhotos = array_values(array_diff($currentPhotos, $toDelete));
                }
            }

            // Agregar nuevas fotos subidas
            if ($request->hasFile('establishment_photos')) {
                foreach ($request->file('establishment_photos') as $photoFile) {
                    if ($photoFile && $photoFile->isValid()) {
                        $currentPhotos[] = $photoFile->store('establishments', 'public');
                    }
                }
            }
            $data['establishment_photos'] = $currentPhotos;

        } elseif ($section === 'legal') {
            $company = $user->company;
            $data = $request->only([
                'tipo_negocio', 'razon_social', 'nit', 'nombre_comercial',
                'nombre_representante', 'email_representante',
                'cedula_propietario', 'habeas_data_accepted', 'direccion_fisica',
                'country', 'state', 'city', 'postal_code', 'tax_id', 'currency', 'phone_prefix'
            ]);

            if ($request->filled('birthdate')) {
                $user->update(['birthdate' => $request->birthdate]);
            }
            
            $canUpdateDocuments = (!$company || $company->status !== 'active' || $company->document_update_status === 'granted');
            
            if ($canUpdateDocuments) {
                $documentsUpdated = false;
                if ($request->hasFile('camara_comercio_file') && $request->file('camara_comercio_file')->isValid()) {
                    $data['camara_comercio_file'] = $request->file('camara_comercio_file')->store('documents', 'public');
                    $documentsUpdated = true;
                }
                if ($request->hasFile('rut_file') && $request->file('rut_file')->isValid()) {
                    $data['rut_file'] = $request->file('rut_file')->store('documents', 'public');
                    $documentsUpdated = true;
                }
                if ($request->hasFile('rut_personal_file') && $request->file('rut_personal_file')->isValid()) {
                    $data['rut_personal_file'] = $request->file('rut_personal_file')->store('documents', 'public');
                    $documentsUpdated = true;
                }

                if ($documentsUpdated && $company && $company->status === 'active' && $company->document_update_status === 'granted') {
                    $data['status'] = 'pending';
                    $data['document_update_status'] = null;
                    session()->flash('warning', 'Has actualizado tus documentos. Tu cuenta está en revisión nuevamente y tu tienda no será visible hasta ser aprobada.');
                }
            }

        } elseif ($section === 'contact') {
            $data = $request->only([
                'phone', 'email', 'instagram', 'facebook', 'website',
                'opening_time', 'closing_time', 'operating_days',
                'has_weekend_schedule', 'weekend_opening_time', 'weekend_closing_time',
                'latitude', 'longitude', 'address', 'state', 'city'
            ]);
            $data['has_weekend_schedule'] = $request->has('has_weekend_schedule');
            if ($request->has('operating_days')) {
                $data['operating_days'] = is_array($request->operating_days) ? $request->operating_days : json_decode($request->operating_days, true);
            }
        } elseif ($section === 'banking') {
            $request->validate([
                'bank_name'                 => 'nullable|string|max:100',
                'account_type'              => 'nullable|string|in:ahorros,corriente',
                'account_number'            => 'nullable|string|max:50',
                'account_holder_name'       => 'nullable|string|max:150',
                'account_holder_doc_type'   => 'nullable|string|in:CC,NIT,CE,PP',
                'account_holder_doc_number' => 'nullable|string|max:30',
                'wompi_merchant_id'         => 'nullable|string|max:100',
                'split_rule_id'             => 'nullable|string|max:100',
            ]);
            $data = $request->only([
                'bank_name', 'account_type', 'account_number', 'account_holder_name',
                'account_holder_doc_type', 'account_holder_doc_number', 'wompi_merchant_id', 'split_rule_id'
            ]);
        }

        // ─── Guardar en BD ──────────────────────────────────────
        // Si no se asignó en legal, lo obtenemos aquí
        $company = $company ?? $user->company;

        if ($company) {
            $company->update($data);
        } else {
            if (empty($data['name'])) {
                $data['name'] = $user->name;
            }
            $data['user_id'] = $user->id;
            Company::create($data);
        }

        $sectionLabel = match($section) {
            'general' => 'Vista general',
            'legal'   => 'Aspecto legal',
            'contact' => 'Redes y Contacto',
            'banking' => 'Datos Bancarios y Dispersión',
            default   => 'Perfil empresarial',
        };

        return redirect()
            ->route('business.company.edit')
            ->with('success', "Sección '{$sectionLabel}' guardada correctamente.")
            ->with('active_section', $section);
    }
}
