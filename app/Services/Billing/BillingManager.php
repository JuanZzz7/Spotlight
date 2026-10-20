<?php

namespace App\Services\Billing;

use App\Models\BillingConfiguration;
use App\Models\Company;
use App\Services\Billing\Contracts\BillingProviderInterface;
use App\Services\Billing\Providers\FactusBillingProvider;
use App\Services\Billing\Providers\SandboxBillingProvider;
use InvalidArgumentException;

class BillingManager
{
    /**
     * Resuelve el proveedor tecnológico adecuado para una empresa.
     */
    public function resolveForCompany(Company $company): BillingProviderInterface
    {
        $config = $company->billingConfiguration;
        $providerKey = $config ? $config->provider : config('billing.default_provider', 'sandbox');

        return $this->resolve($providerKey);
    }

    /**
     * Resuelve un proveedor por su clave ('sandbox', 'factus', etc.).
     */
    public function resolve(string $providerKey): BillingProviderInterface
    {
        return match (strtolower($providerKey)) {
            'sandbox' => app(SandboxBillingProvider::class),
            'factus'  => app(FactusBillingProvider::class),
            default   => throw new InvalidArgumentException("Proveedor de facturación electrónica '{$providerKey}' no soportado."),
        };
    }
}
