<?php

namespace App\Services\Payment;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WompiGatewayService
{
    protected string $publicKey;
    protected string $privateKey;
    protected string $integritySecret;
    protected string $eventSecret;
    protected bool $sandbox;
    protected string $currency;
    protected string $apiBaseUrl;
    protected string $widgetJsUrl;

    public function __construct()
    {
        $this->publicKey       = config('wompi.public_key', env('WOMPI_PUBLIC_KEY', ''));
        $this->privateKey      = config('wompi.private_key', env('WOMPI_PRIVATE_KEY', ''));
        $this->integritySecret = config('wompi.integrity_secret', env('WOMPI_INTEGRITY_SECRET', ''));
        $this->eventSecret     = config('wompi.event_secret', env('WOMPI_EVENT_SECRET', ''));
        $this->sandbox         = (bool) config('wompi.sandbox', env('WOMPI_SANDBOX', true));
        $this->currency        = config('wompi.currency', 'COP');
        $this->apiBaseUrl      = config('wompi.api_base_url', 'https://sandbox.wompi.co/v1');
        $this->widgetJsUrl     = config('wompi.widget_js_url', 'https://checkout.wompi.co/widget.js');
    }

    public function getPublicKey(): string
    {
        return $this->publicKey;
    }

    public function getPrivateKey(): string
    {
        return $this->privateKey;
    }

    public function getIntegritySecret(): string
    {
        return $this->integritySecret;
    }

    public function getEventSecret(): string
    {
        return $this->eventSecret;
    }

    public function isSandbox(): bool
    {
        return $this->sandbox;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getWidgetJsUrl(): string
    {
        return $this->widgetJsUrl;
    }

    /**
     * Genera la firma criptográfica de integridad SHA-256 para el Checkout Web de Wompi.
     * Fórmula oficial Wompi: SHA256(reference + amount_in_cents + currency + integrity_secret)
     */
    public function generateIntegritySignature(string $reference, int $amountInCents, ?string $currency = null): string
    {
        $curr = $currency ?: $this->currency;
        $chain = $reference . $amountInCents . $curr . $this->integritySecret;

        return hash('sha256', $chain);
    }

    /**
     * Valida el checksum criptográfico del Webhook enviado por Wompi usando WOMPI_EVENT_SECRET.
     *
     * Wompi concatena en orden los valores de 'signature.properties', seguido de 'timestamp' y el 'event_secret'.
     */
    public function validateWebhookSignature(array $payload): bool
    {
        if (empty($payload['signature']['checksum']) || empty($payload['signature']['properties'])) {
            Log::warning('Wompi Webhook: Firma o propiedades no presentes en el payload');
            return false;
        }

        $receivedChecksum = (string) $payload['signature']['checksum'];
        $properties = $payload['signature']['properties'];
        $timestamp = (string) ($payload['timestamp'] ?? '');

        $concatenated = '';
        foreach ($properties as $property) {
            $parts = explode('.', $property);
            $val = $payload['data'] ?? [];

            foreach ($parts as $part) {
                if (is_array($val) && array_key_exists($part, $val)) {
                    $val = $val[$part];
                } else {
                    $val = '';
                    break;
                }
            }

            // Si es booleano o número, convertir a string exacto
            if (is_bool($val)) {
                $concatenated .= $val ? 'true' : 'false';
            } else {
                $concatenated .= (string) $val;
            }
        }

        $concatenated .= $timestamp . $this->eventSecret;
        $calculatedChecksum = hash('sha256', $concatenated);

        return hash_equals($calculatedChecksum, $receivedChecksum);
    }

    /**
     * Consulta el estado de una transacción directamente en los servidores de Wompi.
     */
    public function fetchTransaction(string $transactionId): ?array
    {
        try {
            $response = Http::withToken($this->privateKey)
                ->timeout(10)
                ->get("{$this->apiBaseUrl}/transactions/{$transactionId}");

            if ($response->successful()) {
                return $response->json('data');
            }

            Log::error("Wompi API: Error al consultar transacción {$transactionId}", [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
        } catch (\Throwable $e) {
            Log::error("Wompi API: Excepción al consultar transacción {$transactionId}: {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Construye la configuración lista para abrir el Widget de Wompi de forma segura.
     */
    public function buildWidgetConfig(
        string $reference,
        int $amountInCents,
        string $redirectUrl,
        array $customerData = [],
        array $shippingAddress = []
    ): array {
        return [
            'currency'         => $this->currency,
            'amountInCents'    => $amountInCents,
            'reference'        => $reference,
            'publicKey'        => $this->publicKey,
            'signature'        => [
                'integrity' => $this->generateIntegritySignature($reference, $amountInCents, $this->currency),
            ],
            'redirectUrl'      => $redirectUrl,
            'customerData'     => [
                'email'             => $customerData['email'] ?? '',
                'fullName'          => $customerData['fullName'] ?? '',
                'phoneNumber'       => $customerData['phoneNumber'] ?? '',
                'phoneNumberPrefix' => '+57',
                'legalId'           => $customerData['legalId'] ?? '',
                'legalIdType'       => $customerData['legalIdType'] ?? 'CC',
            ],
            'shippingAddress'  => [
                'addressLine1' => $shippingAddress['address'] ?? '',
                'city'         => $shippingAddress['city'] ?? 'Ubaté',
                'phoneNumber'  => $customerData['phoneNumber'] ?? '',
                'region'       => $shippingAddress['department'] ?? 'Cundinamarca',
                'country'      => 'CO',
            ],
        ];
    }
}
