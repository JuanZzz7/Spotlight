<?php

namespace App\Http\Controllers\Business\Billing;

use App\Http\Controllers\Controller;
use App\Models\BillingConfiguration;
use App\Models\Company;
use App\Models\CompanyFiscalProfile;
use App\Services\Billing\BillingManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BillingConfigurationController extends Controller
{
    public function __construct(
        protected BillingManager $manager
    ) {}

    private function getCompany(): Company
    {
        $company = Auth::user()->company;
        if (!$company) {
            abort(403, 'No tiene una empresa asociada.');
        }
        return $company;
    }

    public function edit()
    {
        $company = $this->getCompany();

        if ($company->isInformal()) {
            return view('business.billing.informal_notice', compact('company'));
        }

        $profile = $company->fiscalProfile;
        if (!$profile || !$profile->isVerified()) {
            return redirect()->route('business.billing.profile')
                ->with('error', 'Su comercio debe completar y aprobar la verificación legal antes de configurar los parámetros técnicos de facturación.');
        }

        $config = $company->billingConfiguration ?: new BillingConfiguration([
            'company_id' => $company->id,
            'provider' => 'sandbox',
            'environment' => 'sandbox',
            'resolution_number' => '18760000001',
            'resolution_prefix' => 'SETP',
            'range_from' => 1,
            'range_to' => 5000,
            'current_consecutive' => 1,
            'technical_key' => 'fc8eac422eba16e22ffd8c6f94b3f40a6e38162c',
            'resolution_date_from' => now()->toDateString(),
            'resolution_date_to' => now()->addYear()->toDateString(),
            'credit_note_prefix' => 'NC',
            'credit_note_consecutive' => 1,
            'debit_note_prefix' => 'ND',
            'debit_note_consecutive' => 1,
        ]);

        return view('business.billing.configuration', compact('company', 'profile', 'config'));
    }

    public function update(Request $request)
    {
        $company = $this->getCompany();
        $profile = $company->fiscalProfile;

        if (!$profile || !$profile->isVerified()) {
            return back()->with('error', 'Comercio no verificado.');
        }

        $config = $company->billingConfiguration ?: new BillingConfiguration(['company_id' => $company->id]);

        if (!$request->has('provider')) {
            $request->merge(['provider' => $config->provider ?: 'sandbox']);
        }
        if (!$request->has('environment')) {
            $request->merge(['environment' => $config->environment ?: 'sandbox']);
        }

        $request->validate([
            'provider'             => 'required|in:sandbox,factus',
            'environment'          => 'required|in:sandbox,production',
            'resolution_number'    => 'required|string|max:50',
            'resolution_prefix'    => 'nullable|string|max:10',
            'range_from'           => 'required|integer|min:1',
            'range_to'             => 'required|integer|gte:range_from',
            'current_consecutive'  => 'required|integer|gte:range_from',
            'technical_key'        => 'required|string',
            'resolution_date_from' => 'required|date',
            'resolution_date_to'   => 'required|date|after:resolution_date_from',
            'credit_note_prefix'   => 'nullable|string|max:10',
            'api_token'            => 'nullable|string',
            'api_secret'           => 'nullable|string',
            'software_id'          => 'nullable|string|max:100',
            'software_pin'         => 'nullable|string|max:20',
            'test_set_id'          => 'nullable|string|max:100',
        ]);

        $config = $company->billingConfiguration ?: new BillingConfiguration(['company_id' => $company->id]);
        $config->fill($request->all());
        $config->save();

        return redirect()->route('business.billing.configuration')
            ->with('success', 'Parámetros técnicos de facturación guardados. Ahora realice la prueba de conectividad técnica.');
    }

    public function testConnection()
    {
        $company = $this->getCompany();
        $profile = $company->fiscalProfile;
        $config  = $company->billingConfiguration;

        if (!$config) {
            return back()->with('error', 'Debe guardar la configuración técnica antes de probar la conexión.');
        }

        $provider = $this->manager->resolveForCompany($company);
        $response = $provider->validateConnection($profile, $config);

        $config->last_connection_test_at = now();
        $config->is_connected = $response->isSuccess;
        $config->last_connection_error = $response->isSuccess ? null : $response->message;
        $config->save();

        if ($response->isSuccess) {
            // Si la verificación legal ya fue aprobada, la prueba técnica exitosa promueve al comercio a 'enabled'
            if ($profile->fiscal_status === CompanyFiscalProfile::STATUS_PENDING_TECHNICAL_SETUP || $profile->fiscal_status === CompanyFiscalProfile::STATUS_VERIFIED) {
                $profile->update([
                    'fiscal_status' => CompanyFiscalProfile::STATUS_ENABLED,
                    'enabled_at' => now(),
                ]);
            }

            return back()->with('success', '¡Conexión técnica exitosa! ' . $response->message . ' Su comercio está ahora HABILITADO para emitir facturación electrónica.');
        }

        return back()->with('error', 'Fallo en la validación técnica: ' . $response->message);
    }
}
