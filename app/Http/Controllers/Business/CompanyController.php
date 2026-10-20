<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CompanyController extends Controller
{
    public function edit()
    {
        $company = Auth::user()->company ?? new Company();
        return view('business.company.edit', compact('company'));
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
            $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'category'    => 'nullable|string|max:100',
                'logo'        => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp|max:2048',
                'banner'      => 'nullable|image|mimes:jpeg,png,jpg,gif,webp,bmp|max:2048',
            ], [
                'name.required'   => 'El nombre de la empresa es obligatorio.',
                'banner.image'    => 'El banner debe ser una imagen.',
                'banner.mimes'    => 'El banner debe ser JPG, PNG, GIF, WebP o BMP.',
                'banner.max'      => 'El banner no puede superar los 2 MB por límite del servidor.',
                'logo.max'        => 'El logo no puede superar los 2 MB.',
            ]);
        } elseif ($section === 'legal') {
            $isMexico = ($user->company && $user->company->isMexican()) || $request->input('country') === 'MX';
            if ($isMexico) {
                $request->validate([
                    'country'             => 'nullable|in:CO,MX',
                    'tipo_negocio'        => 'required|in:formal,informal',
                    'habeas_data_accepted'=> 'accepted',
                    'direccion_fisica'    => 'required|string|max:255',
                    'state'               => 'nullable|string|max:100',
                    'city'                => 'nullable|string|max:100',
                    'postal_code'         => 'nullable|string|max:20',
                    'tax_id'              => 'nullable|string|max:50',
                    'razon_social'        => 'nullable|string|max:255',
                    'nombre_comercial'    => 'nullable|string|max:255',
                ], [
                    'tipo_negocio.required'         => 'Debe seleccionar el tipo de negocio.',
                    'habeas_data_accepted.accepted' => 'Debe aceptar la política de privacidad.',
                    'direccion_fisica.required'     => 'La dirección física es obligatoria.',
                ]);
            } else {
                $request->validate([
                    'tipo_negocio'        => 'required|in:formal,informal',
                    'habeas_data_accepted'=> 'accepted',
                    'direccion_fisica'    => 'required|string|max:255',
                    'razon_social'        => 'required_if:tipo_negocio,formal|nullable|string|max:255',
                    'nit'                 => ['required_if:tipo_negocio,formal', 'nullable', 'string', 'regex:/^[0-9]+-[0-9]$/'],
                    'nombre_comercial'    => 'required_if:tipo_negocio,informal|nullable|string|max:255',
                    'cedula_propietario'  => 'required_if:tipo_negocio,informal|nullable|string|max:20',
                    'camara_comercio_file'=> 'nullable|file|mimes:pdf|max:5120',
                    'rut_file'            => 'nullable|file|mimes:pdf|max:5120',
                    'rut_personal_file'   => 'nullable|file|mimes:pdf|max:5120',
                ], [
                    'tipo_negocio.required'          => 'Debe seleccionar el tipo de negocio.',
                    'habeas_data_accepted.accepted'  => 'Debe aceptar la politica de datos.',
                    'direccion_fisica.required'      => 'La direccion fisica es obligatoria.',
                    'razon_social.required_if'       => 'La razon social es obligatoria para empresas formales.',
                    'nit.required_if'                => 'El NIT es obligatorio para empresas formales.',
                    'nit.regex'                      => 'El NIT debe tener el formato valido (ej. 123456789-1).',
                    'nombre_comercial.required_if'   => 'El nombre comercial es obligatorio.',
                    'cedula_propietario.required_if' => 'La cedula es obligatoria.',
                ]);
            }
        } elseif ($section === 'contact') {
            $request->validate([
                'phone'          => 'nullable|string|max:20',
                'phone_prefix'   => 'nullable|string|max:10',
                'email'          => 'nullable|email',
                'instagram'      => 'nullable|string|max:100',
                'facebook'       => 'nullable|string|max:100',
                'website'        => 'nullable|url',
                'opening_time'   => 'nullable|string|max:10',
                'closing_time'   => 'nullable|string|max:10',
                'operating_days' => 'nullable|array',
                'latitude'       => 'nullable|numeric',
                'longitude'      => 'nullable|numeric',
                'address'        => 'nullable|string|max:500',
            ]);
        }

        // ─── Datos a guardar ────────────────────────────────────
        $data = [];

        if ($section === 'general') {
            $data = $request->only(['name', 'description', 'category', 'country', 'currency', 'state', 'city', 'postal_code']);

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

        } elseif ($section === 'legal') {
            $company = $user->company;
            $data = $request->only([
                'tipo_negocio', 'razon_social', 'nit', 'nombre_comercial',
                'cedula_propietario', 'habeas_data_accepted', 'direccion_fisica',
                'country', 'state', 'city', 'postal_code', 'tax_id', 'currency', 'phone_prefix'
            ]);
            
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
                'latitude', 'longitude', 'address'
            ]);
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
            ->with('success', "Seccion '{$sectionLabel}' guardada correctamente.");
    }
}
