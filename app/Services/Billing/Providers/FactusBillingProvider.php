<?php

namespace App\Services\Billing\Providers;

use App\Models\BillingConfiguration;
use App\Models\CompanyFiscalProfile;
use App\Models\ElectronicDocument;
use App\Services\Billing\Contracts\BillingProviderInterface;
use App\Services\Billing\DTOs\BillingProviderResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FactusBillingProvider implements BillingProviderInterface
{
    private function getBaseUrl(BillingConfiguration $config): string
    {
        return $config->environment === 'production'
            ? config('billing.providers.factus.prod_base_url', 'https://api.factus.co')
            : config('billing.providers.factus.sandbox_base_url', 'https://api-sandbox.factus.co');
    }

    private function getAccessToken(BillingConfiguration $config): ?string
    {
        // Si la empresa tiene su propio token configurado
        if (!empty($config->api_token)) {
            return $config->api_token;
        }

        $clientId = $config->api_secret ?: config('billing.providers.factus.client_id');
        $clientSecret = $config->technical_key ?: config('billing.providers.factus.client_secret');

        if (empty($clientId) || empty($clientSecret)) {
            return null;
        }

        try {
            $baseUrl = $this->getBaseUrl($config);
            $response = Http::asForm()->timeout(15)->post("{$baseUrl}/oauth/token", [
                'grant_type'    => 'client_credentials',
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('Factus OAuth failed', ['response' => $response->body()]);
            return null;
        } catch (\Throwable $e) {
            Log::error('Factus OAuth exception: ' . $e->getMessage());
            return null;
        }
    }

    public function validateConnection(CompanyFiscalProfile $profile, BillingConfiguration $config): BillingProviderResponse
    {
        $token = $this->getAccessToken($config);

        if (!$token) {
            return BillingProviderResponse::failure(
                'No se pudo autenticar con la API de Factus. Verifique Client ID y Client Secret / API Token.',
                '401',
                ['auth' => ['Credenciales de Factus inválidas o no configuradas']]
            );
        }

        try {
            $baseUrl = $this->getBaseUrl($config);
            $response = Http::withToken($token)->timeout(15)->get("{$baseUrl}/v1/numbering-ranges");

            if ($response->successful()) {
                return BillingProviderResponse::success(
                    'Conexión exitosa con la API de Factus Colombia. Rangos de numeración verificados.',
                    null,
                    null,
                    null,
                    null,
                    'CONECTADO',
                    'FACTUS-CONN-' . now()->timestamp,
                    $response->json()
                );
            }

            return BillingProviderResponse::failure(
                'Factus respondió con error: ' . $response->body(),
                (string) $response->status(),
                ['api_error' => [$response->body()]]
            );
        } catch (\Throwable $e) {
            return BillingProviderResponse::failure(
                'Excepción de red al conectar con Factus: ' . $e->getMessage(),
                '500'
            );
        }
    }

    public function emitInvoice(ElectronicDocument $document, BillingConfiguration $config): BillingProviderResponse
    {
        $token = $this->getAccessToken($config);

        if (!$token) {
            return BillingProviderResponse::failure(
                'Credenciales de API de Factus no configuradas.',
                '401',
                ['auth' => ['Falta configurar API Token o Client Credentials en Factus']]
            );
        }

        $baseUrl = $this->getBaseUrl($config);
        $payload = $this->buildFactusInvoicePayload($document, $config);

        try {
            $response = Http::withToken($token)
                ->timeout(30)
                ->post("{$baseUrl}/v1/bills/validate", $payload);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'OK') {
                $billData = $data['data']['bill'] ?? [];
                $cufe = $billData['cufe'] ?? null;
                $qr = $billData['qr'] ?? (config('billing.dian.qr_base_url') . $cufe);
                $trackId = $billData['track_id'] ?? null;

                return BillingProviderResponse::success(
                    'Factura emitida y validada con éxito por Factus / DIAN.',
                    $cufe,
                    $qr,
                    null,
                    $billData['public_url'] ?? null,
                    'DIAN_ACEPTADO',
                    $trackId,
                    $data
                );
            }

            // Manejo detallado de errores de validación de la DIAN
            $errors = $data['errors'] ?? [$data['message'] ?? 'Error desconocido al validar con Factus'];
            return BillingProviderResponse::failure(
                'Rechazo de validación en Factus / DIAN: ' . json_encode($errors, JSON_UNESCAPED_UNICODE),
                (string) $response->status(),
                $errors,
                $data,
                'DIAN_RECHAZADO'
            );

        } catch (\Throwable $e) {
            Log::error('Factus emitInvoice exception: ' . $e->getMessage());
            return BillingProviderResponse::failure(
                'Fallo de conexión con el proveedor Factus: ' . $e->getMessage(),
                '500'
            );
        }
    }

    public function emitCreditNote(ElectronicDocument $document, BillingConfiguration $config): BillingProviderResponse
    {
        $token = $this->getAccessToken($config);
        if (!$token) {
            return BillingProviderResponse::failure('Credenciales de Factus no disponibles.', '401');
        }

        $baseUrl = $this->getBaseUrl($config);
        $payload = [
            'numbering_range_id' => $config->credit_note_prefix,
            'reference_code' => $document->full_number,
            'bill_number' => $document->parentDocument ? $document->parentDocument->full_number : '',
            'discrepancy_code' => $document->correction_concept_code ?: '1',
            'notes' => $document->correction_reason ?: 'Nota Crédito por ajuste comercial',
        ];

        try {
            $response = Http::withToken($token)->timeout(30)->post("{$baseUrl}/v1/credit-notes/validate", $payload);
            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? '') === 'OK') {
                $noteData = $data['data']['credit_note'] ?? [];
                return BillingProviderResponse::success(
                    'Nota Crédito validada por Factus / DIAN.',
                    $noteData['cude'] ?? null,
                    $noteData['qr'] ?? null,
                    null,
                    $noteData['public_url'] ?? null,
                    'DIAN_ACEPTADO',
                    $noteData['track_id'] ?? null,
                    $data
                );
            }

            return BillingProviderResponse::failure(
                'Rechazo en Factus: ' . json_encode($data['errors'] ?? $data['message'] ?? 'Error', JSON_UNESCAPED_UNICODE),
                (string) $response->status(),
                $data['errors'] ?? null,
                $data
            );
        } catch (\Throwable $e) {
            return BillingProviderResponse::failure('Error al emitir Nota Crédito: ' . $e->getMessage(), '500');
        }
    }

    public function emitDebitNote(ElectronicDocument $document, BillingConfiguration $config): BillingProviderResponse
    {
        return BillingProviderResponse::failure('Emisión de notas débito no configurada para este endpoint de Factus.', '501');
    }

    public function queryDocumentStatus(ElectronicDocument $document, BillingConfiguration $config): BillingProviderResponse
    {
        $token = $this->getAccessToken($config);
        if (!$token) {
            return BillingProviderResponse::failure('Credenciales de Factus no disponibles.', '401');
        }

        $baseUrl = $this->getBaseUrl($config);
        try {
            $response = Http::withToken($token)->timeout(15)->get("{$baseUrl}/v1/bills/show/{$document->full_number}");
            $data = $response->json();

            if ($response->successful()) {
                $bill = $data['data']['bill'] ?? [];
                $dianStatus = ($bill['status'] ?? '') === 'validated' ? 'DIAN_ACEPTADO' : 'DIAN_PENDIENTE';
                return BillingProviderResponse::success(
                    'Estado del documento consultado en Factus.',
                    $bill['cufe'] ?? null,
                    $bill['qr'] ?? null,
                    null,
                    $bill['public_url'] ?? null,
                    $dianStatus,
                    $bill['track_id'] ?? null,
                    $data
                );
            }

            return BillingProviderResponse::failure('No se encontró el documento en Factus.', (string) $response->status());
        } catch (\Throwable $e) {
            return BillingProviderResponse::failure('Error consultando estado: ' . $e->getMessage(), '500');
        }
    }

    public function downloadXml(ElectronicDocument $document, BillingConfiguration $config): ?string
    {
        $token = $this->getAccessToken($config);
        if (!$token) return null;

        $baseUrl = $this->getBaseUrl($config);
        try {
            $response = Http::withToken($token)->timeout(20)->get("{$baseUrl}/v1/bills/download-xml/{$document->full_number}");
            return $response->successful() ? $response->body() : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function downloadPdf(ElectronicDocument $document, BillingConfiguration $config): ?string
    {
        $token = $this->getAccessToken($config);
        if (!$token) return null;

        $baseUrl = $this->getBaseUrl($config);
        try {
            $response = Http::withToken($token)->timeout(20)->get("{$baseUrl}/v1/bills/download-pdf/{$document->full_number}");
            return $response->successful() ? $response->body() : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function buildFactusInvoicePayload(ElectronicDocument $doc, BillingConfiguration $config): array
    {
        $items = [];
        foreach ($doc->items as $it) {
            $items[] = [
                'code_reference' => $it->product_code ?: 'SKU-' . $it->id,
                'name' => $it->description,
                'quantity' => (float) $it->quantity,
                'price' => (float) $it->price_amount,
                'tax_rate' => (string) (float) $it->tax_rate,
                'unit_measure_id' => 70, // Código 94 en DIAN / 70 en Factus (Unidad)
                'standard_code_id' => 1,
                'is_excluded' => 0,
                'tribute_id' => 1, // IVA
            ];
        }

        return [
            'numbering_range_id' => $config->resolution_prefix,
            'reference_code' => $doc->full_number,
            'customer' => [
                'identification' => $doc->customer_id_number,
                'dv' => $doc->customer_dv ?: null,
                'graphic_representation_name' => $doc->customer_name,
                'email' => $doc->customer_email ?: 'consumidor@spotlight.local',
                'phone' => $doc->customer_phone ?: '3000000000',
                'legal_organization_id' => $doc->customer_id_type === '31' ? 1 : 2,
                'tribute_id' => 21, // No responsable de IVA
                'identification_document_id' => $doc->customer_id_type === '31' ? 6 : 3, // 6: NIT, 3: Cédula
                'municipality_id' => 980, // Ubaté
            ],
            'payment_form' => [
                'payment_method_code' => $doc->payment_method_code,
                'payment_form_id' => $doc->payment_means_code === '2' ? 2 : 1,
            ],
            'items' => $items,
        ];
    }
}
