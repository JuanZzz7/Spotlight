<?php

namespace App\Http\Controllers\Business\Billing;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CompanyFiscalProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FiscalProfileController extends Controller
{
    private function getCompany(): Company
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403, 'No tiene una empresa asociada.');
        }
        return $company;
    }

    public function show()
    {
        $company = $this->getCompany();

        // Si es comercio informal, no puede acceder a la habilitación fiscal electrónica
        if ($company->isInformal()) {
            return view('business.billing.informal_notice', compact('company'));
        }

        $profile = $company->fiscalProfile ?: new CompanyFiscalProfile([
            'company_id' => $company->id,
            'fiscal_status' => CompanyFiscalProfile::STATUS_NOT_REQUESTED,
            'business_name' => $company->razon_social ?: $company->name,
            'identification_number' => $company->nit ? explode('-', $company->nit)[0] : '',
            'fiscal_address' => $company->address,
            'fiscal_phone' => $company->phone,
            'fiscal_email' => $company->email,
            'municipality_code' => '25843', // Ubaté
            'department_code' => '25', // Cundinamarca
            'postal_code' => '250420',
            'country_code' => 'CO',
            'tax_regime' => 'responsable_iva',
            'tax_responsibilities' => ['O-13', 'O-47'],
        ]);

        return view('business.billing.profile', compact('company', 'profile'));
    }

    public function update(Request $request)
    {
        $company = $this->getCompany();

        if ($company->isInformal()) {
            return back()->with('error', 'Los comercios informales no pueden solicitar habilitación de facturación electrónica. Debe formalizar su negocio en Cámara de Comercio primero.');
        }

        $request->validate([
            'person_type'           => 'required|in:persona_juridica,persona_natural',
            'id_type'               => 'required|string|in:31,13,22,41',
            'identification_number' => 'required|string|max:20|regex:/^[0-9]+$/',
            'business_name'         => 'required|string|max:255',
            'commercial_name'       => 'nullable|string|max:255',
            'tax_regime'            => 'required|string|in:responsable_iva,no_responsable_iva,regimen_simple,gran_contribuyente',
            'tax_responsibilities'  => 'required|array|min:1',
            'tax_responsibilities.*'=> 'string',
            'ciiu_code'             => 'required|string|max:10|regex:/^[0-9]{4}$/',
            'fiscal_address'        => 'required|string|max:255',
            'fiscal_email'          => 'required|email|max:255',
            'fiscal_phone'          => 'required|string|max:30',
            'municipality_code'     => 'required|string|max:10',
            'department_code'       => 'required|string|max:10',
            'postal_code'           => 'nullable|string|max:10',
            'rut_file'              => 'nullable|file|mimes:pdf|max:5120',
            'camara_comercio_file'  => 'nullable|file|mimes:pdf|max:5120',
            'legal_rep_id_file'     => 'nullable|file|mimes:pdf|max:5120',
        ], [
            'identification_number.required' => 'El NIT o número de documento fiscal es obligatorio.',
            'identification_number.regex'    => 'El NIT debe contener únicamente dígitos.',
            'ciiu_code.regex'                => 'El código de actividad CIIU debe contener 4 dígitos.',
            'tax_responsibilities.required'  => 'Debe seleccionar al menos una responsabilidad fiscal DIAN.',
        ]);

        $cleanNit = $request->identification_number;
        $calculatedDv = CompanyFiscalProfile::calculateDv($cleanNit);

        $profile = $company->fiscalProfile ?: new CompanyFiscalProfile(['company_id' => $company->id]);

        $data = [
            'person_type'           => $request->person_type,
            'id_type'               => $request->id_type,
            'identification_number' => $cleanNit,
            'dv'                    => $calculatedDv,
            'business_name'         => $request->business_name,
            'commercial_name'       => $request->commercial_name ?: $company->name,
            'tax_regime'            => $request->tax_regime,
            'tax_responsibilities'  => $request->tax_responsibilities,
            'ciiu_code'             => $request->ciiu_code,
            'fiscal_address'        => $request->fiscal_address,
            'fiscal_email'          => $request->fiscal_email,
            'fiscal_phone'          => $request->fiscal_phone,
            'municipality_code'     => $request->municipality_code,
            'department_code'       => $request->department_code,
            'postal_code'           => $request->postal_code ?: '250420',
            'country_code'          => 'CO',
        ];

        // Archivos adjuntos
        if ($request->hasFile('rut_file')) {
            $data['rut_document_path'] = $request->file('rut_file')->store('fiscal_documents', 'local');
        } elseif (!$profile->rut_document_path && $company->rut_file) {
            $data['rut_document_path'] = $company->rut_file;
        }

        if ($request->hasFile('camara_comercio_file')) {
            $data['camara_comercio_document_path'] = $request->file('camara_comercio_file')->store('fiscal_documents', 'local');
        } elseif (!$profile->camara_comercio_document_path && $company->camara_comercio_file) {
            $data['camara_comercio_document_path'] = $company->camara_comercio_file;
        }

        if ($request->hasFile('legal_rep_id_file')) {
            $data['legal_rep_id_document_path'] = $request->file('legal_rep_id_file')->store('fiscal_documents', 'local');
        }

        // Si el estado era 'not_requested' o 'rejected', al enviar pasa a 'in_review'
        if (in_array($profile->fiscal_status, [
            CompanyFiscalProfile::STATUS_NOT_REQUESTED,
            CompanyFiscalProfile::STATUS_PENDING_DOCUMENTATION,
            CompanyFiscalProfile::STATUS_REJECTED,
            null
        ], true)) {
            $data['fiscal_status'] = CompanyFiscalProfile::STATUS_IN_REVIEW;
            $data['submitted_at'] = now();
            $data['status_notes'] = null; // Limpiar motivo de rechazo previo
        }

        $profile->fill($data);
        $profile->save();

        return redirect()->route('business.billing.profile')
            ->with('success', 'Perfil fiscal guardado y solicitud de verificación enviada a revisión.');
    }
}
