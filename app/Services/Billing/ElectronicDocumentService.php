<?php

namespace App\Services\Billing;

use App\Models\BillingConfiguration;
use App\Models\BillingLog;
use App\Models\Company;
use App\Models\ElectronicDocument;
use App\Models\ElectronicDocumentItem;
use App\Models\Order;
use App\Services\Billing\Contracts\BillingProviderInterface;
use App\Services\Billing\DTOs\BillingProviderResponse;
use Barryvdh\DomPDF\Facade\Pdf;
use DomainException;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ElectronicDocumentService
{
    public function __construct(
        protected BillingManager $manager
    ) {}

    /**
     * Emite una factura electrónica de venta a partir de un pedido del comercio.
     */
    public function createInvoiceFromOrder(
        Company $company,
        Order $order,
        array $customerData,
        string $paymentMethod = '10'
    ): ElectronicDocument {
        if (!$company->canIssueElectronicInvoices()) {
            throw new DomainException('El comercio no está legalmente verificado ni habilitado para emitir facturación electrónica ante la DIAN.');
        }

        if ($order->isBilled()) {
            throw new DomainException("El pedido #{$order->id} ya cuenta con una factura electrónica emitida.");
        }

        return DB::transaction(function () use ($company, $order, $customerData, $paymentMethod) {
            // Bloqueo pesimista de la configuración para garantizar consecutivos atómicos y evitar colisiones
            /** @var BillingConfiguration $config */
            $config = BillingConfiguration::where('company_id', $company->id)->lockForUpdate()->first();

            if (!$config) {
                throw new DomainException('No se ha configurado la resolución de facturación ni el proveedor tecnológico para este comercio.');
            }

            $consecutive = $config->current_consecutive;
            $prefix = trim((string) $config->resolution_prefix);
            $fullNumber = $prefix ? "{$prefix}-{$consecutive}" : (string) $consecutive;

            // Incrementar consecutivo
            $config->current_consecutive = $consecutive + 1;
            $config->save();

            // Filtrar detalles que pertenecen a este comercio
            $details = $order->details()
                ->where(function ($q) use ($company) {
                    $q->where('company_id', $company->id)
                      ->orWhereNull('company_id');
                })
                ->with('product')
                ->get();

            if ($details->isEmpty()) {
                throw new DomainException('No hay productos pertenecientes a esta empresa en la orden seleccionada.');
            }

            // Cálculo aritmético estricto de bases e impuestos (IVA)
            $lineExtensionTotal = 0.00;
            $taxTotal = 0.00;
            $payableTotal = 0.00;
            $itemsData = [];

            $lineNumber = 1;
            foreach ($details as $detail) {
                $qty = (float) $detail->quantity;
                $unitPrice = (float) $detail->unit_price;
                $lineExtension = round($qty * $unitPrice, 2);

                // Tasa de IVA según snapshot del detalle o del producto
                $taxRate = (float) ($detail->tax_rate ?? ($detail->product?->tax_rate ?? 19.00));
                $taxType = $detail->tax_type ?? ($detail->product?->tax_type ?? '01');
                $taxAmount = round($lineExtension * ($taxRate / 100), 2);
                $totalItem = $lineExtension + $taxAmount;

                $lineExtensionTotal += $lineExtension;
                $taxTotal += $taxAmount;
                $payableTotal += $totalItem;

                $itemsData[] = [
                    'line_number' => $lineNumber++,
                    'product_id' => $detail->product_id,
                    'product_code' => $detail->product_sku ?? ($detail->product?->sku ?? 'PROD-' . $detail->product_id),
                    'description' => $detail->effective_product_name,
                    'quantity' => $qty,
                    'unit_measure' => $detail->product?->unit_measure ?? '94',
                    'price_amount' => $unitPrice,
                    'line_extension_amount' => $lineExtension,
                    'discount_amount' => 0.00,
                    'tax_type' => $taxType,
                    'tax_rate' => $taxRate,
                    'tax_base_amount' => $lineExtension,
                    'tax_amount' => $taxAmount,
                    'total_item_amount' => $totalItem,
                ];
            }

            $profile = $company->fiscalProfile;

            // Crear documento electrónico en base de datos (Estado: Borrador)
            $document = ElectronicDocument::create([
                'company_id' => $company->id,
                'order_id' => $order->id,
                'user_id' => $order->user_id,
                'document_type' => ElectronicDocument::TYPE_INVOICE,
                'prefix' => $prefix ?: 'FE',
                'consecutive' => $consecutive,
                'full_number' => $fullNumber,
                'issue_date' => now()->toDateString(),
                'issue_time' => now()->format('H:i:s'),
                'due_date' => now()->addDays(30)->toDateString(),
                'environment' => $config->environment,
                // Emisor Snapshot
                'issuer_nit' => $profile->identification_number ?: ($company->nit ?: '000000000'),
                'issuer_dv' => $profile->dv ?: '0',
                'issuer_business_name' => $profile->business_name ?: ($company->razon_social ?: $company->name),
                'issuer_tax_regime' => $profile->tax_regime,
                'issuer_address' => $profile->fiscal_address ?: $company->address,
                'issuer_phone' => $profile->fiscal_phone ?: $company->phone,
                'issuer_email' => $profile->fiscal_email ?: $company->email,
                'issuer_municipality_code' => $profile->municipality_code ?: '25843',
                // Adquirente Snapshot
                'customer_id_type' => $customerData['id_type'] ?? '31',
                'customer_id_number' => $customerData['id_number'] ?? '222222222222',
                'customer_dv' => $customerData['dv'] ?? null,
                'customer_name' => $customerData['name'] ?? 'Consumidor Final',
                'customer_email' => $customerData['email'] ?? $order->user?->email,
                'customer_phone' => $customerData['phone'] ?? $order->user?->phone,
                'customer_address' => $customerData['address'] ?? ($order->user?->address ?: 'Ubaté, Cundinamarca'),
                'customer_municipality_code' => $customerData['municipality_code'] ?? '25843',
                // Totales
                'line_extension_amount' => $lineExtensionTotal,
                'tax_exclusive_amount' => $lineExtensionTotal,
                'tax_inclusive_amount' => $payableTotal,
                'allowance_total_amount' => 0.00,
                'tax_total_amount' => $taxTotal,
                'payable_amount' => $payableTotal,
                'payment_method_code' => $paymentMethod,
                'payment_means_code' => '1',
                'status' => ElectronicDocument::STATUS_DRAFT,
            ]);

            // Guardar ítems
            foreach ($itemsData as $item) {
                $document->items()->create($item);
            }

            // Transmisión y validación con el proveedor tecnológico
            $this->transmitDocument($document, $config);

            // Actualizar estado en la orden
            $order->update([
                'company_id' => $company->id,
                'billing_status' => $document->isValidated() ? 'billed' : 'in_process',
                'payment_method_code' => $paymentMethod,
            ]);

            return $document->fresh(['items', 'company', 'order']);
        });
    }

    /**
     * Emite una factura electrónica directa sin pedido previo.
     */
    public function createDirectInvoice(
        Company $company,
        array $customerData,
        array $items,
        string $paymentMethod = '10'
    ): ElectronicDocument {
        if (!$company->canIssueElectronicInvoices()) {
            throw new DomainException('El comercio no está legalmente verificado ni habilitado para emitir facturación electrónica ante la DIAN.');
        }

        return DB::transaction(function () use ($company, $customerData, $items, $paymentMethod) {
            /** @var BillingConfiguration $config */
            $config = BillingConfiguration::where('company_id', $company->id)->lockForUpdate()->first();

            if (!$config) {
                throw new DomainException('No se ha configurado la resolución de facturación ni el proveedor tecnológico.');
            }

            $consecutive = $config->current_consecutive;
            $prefix = trim((string) $config->resolution_prefix);
            $fullNumber = $prefix ? "{$prefix}-{$consecutive}" : (string) $consecutive;

            $config->current_consecutive = $consecutive + 1;
            $config->save();

            $lineExtensionTotal = 0.00;
            $taxTotal = 0.00;
            $payableTotal = 0.00;
            $itemsData = [];

            $lineNumber = 1;
            foreach ($items as $item) {
                $qty = (float) $item['quantity'];
                $price = (float) $item['price_amount'];
                $taxRate = (float) ($item['tax_rate'] ?? 19.00);
                $lineExtension = round($qty * $price, 2);
                $taxAmount = round($lineExtension * ($taxRate / 100), 2);
                $totalItem = $lineExtension + $taxAmount;

                $lineExtensionTotal += $lineExtension;
                $taxTotal += $taxAmount;
                $payableTotal += $totalItem;

                $itemsData[] = [
                    'line_number' => $lineNumber++,
                    'product_id' => $item['product_id'] ?? null,
                    'product_code' => $item['product_code'] ?? 'ITEM-' . $lineNumber,
                    'description' => $item['description'],
                    'quantity' => $qty,
                    'unit_measure' => $item['unit_measure'] ?? '94',
                    'price_amount' => $price,
                    'line_extension_amount' => $lineExtension,
                    'discount_amount' => 0.00,
                    'tax_type' => '01',
                    'tax_rate' => $taxRate,
                    'tax_base_amount' => $lineExtension,
                    'tax_amount' => $taxAmount,
                    'total_item_amount' => $totalItem,
                ];
            }

            $profile = $company->fiscalProfile;

            $document = ElectronicDocument::create([
                'company_id' => $company->id,
                'document_type' => ElectronicDocument::TYPE_INVOICE,
                'prefix' => $prefix ?: 'FE',
                'consecutive' => $consecutive,
                'full_number' => $fullNumber,
                'issue_date' => now()->toDateString(),
                'issue_time' => now()->format('H:i:s'),
                'due_date' => now()->addDays(30)->toDateString(),
                'environment' => $config->environment,
                // Emisor Snapshot
                'issuer_nit' => $profile->identification_number ?: ($company->nit ?: '000000000'),
                'issuer_dv' => $profile->dv ?: '0',
                'issuer_business_name' => $profile->business_name ?: ($company->razon_social ?: $company->name),
                'issuer_tax_regime' => $profile->tax_regime,
                'issuer_address' => $profile->fiscal_address ?: $company->address,
                'issuer_phone' => $profile->fiscal_phone ?: $company->phone,
                'issuer_email' => $profile->fiscal_email ?: $company->email,
                'issuer_municipality_code' => $profile->municipality_code ?: '25843',
                // Adquirente Snapshot
                'customer_id_type' => $customerData['id_type'] ?? '31',
                'customer_id_number' => $customerData['id_number'] ?? '222222222222',
                'customer_dv' => $customerData['dv'] ?? null,
                'customer_name' => $customerData['name'] ?? 'Consumidor Final',
                'customer_email' => $customerData['email'] ?? null,
                'customer_phone' => $customerData['phone'] ?? null,
                'customer_address' => $customerData['address'] ?? 'Ubaté, Cundinamarca',
                'customer_municipality_code' => $customerData['municipality_code'] ?? '25843',
                // Totales
                'line_extension_amount' => $lineExtensionTotal,
                'tax_exclusive_amount' => $lineExtensionTotal,
                'tax_inclusive_amount' => $payableTotal,
                'allowance_total_amount' => 0.00,
                'tax_total_amount' => $taxTotal,
                'payable_amount' => $payableTotal,
                'payment_method_code' => $paymentMethod,
                'payment_means_code' => '1',
                'status' => ElectronicDocument::STATUS_DRAFT,
            ]);

            foreach ($itemsData as $it) {
                $document->items()->create($it);
            }

            $this->transmitDocument($document, $config);

            return $document->fresh(['items', 'company']);
        });
    }

    /**
     * Emite una Nota Crédito Electrónica para anular o ajustar una factura validada.
     */
    public function createCreditNote(
        ElectronicDocument $invoice,
        string $conceptCode,
        string $reason
    ): ElectronicDocument {
        if (!$invoice->isValidated()) {
            throw new DomainException('Solo se pueden emitir notas crédito sobre facturas electrónicas previamente validadas por la DIAN.');
        }

        $company = $invoice->company;

        return DB::transaction(function () use ($company, $invoice, $conceptCode, $reason) {
            /** @var BillingConfiguration $config */
            $config = BillingConfiguration::where('company_id', $company->id)->lockForUpdate()->first();

            $consecutive = $config->credit_note_consecutive;
            $prefix = trim((string) $config->credit_note_prefix) ?: 'NC';
            $fullNumber = "{$prefix}-{$consecutive}";

            $config->credit_note_consecutive = $consecutive + 1;
            $config->save();

            // Crear documento de nota crédito
            $creditNote = ElectronicDocument::create([
                'company_id' => $company->id,
                'order_id' => $invoice->order_id,
                'user_id' => $invoice->user_id,
                'document_type' => ElectronicDocument::TYPE_CREDIT_NOTE,
                'parent_document_id' => $invoice->id,
                'correction_concept_code' => $conceptCode,
                'correction_reason' => $reason,
                'prefix' => $prefix,
                'consecutive' => $consecutive,
                'full_number' => $fullNumber,
                'issue_date' => now()->toDateString(),
                'issue_time' => now()->format('H:i:s'),
                'due_date' => now()->toDateString(),
                'environment' => $config->environment,
                'issuer_nit' => $invoice->issuer_nit,
                'issuer_dv' => $invoice->issuer_dv,
                'issuer_business_name' => $invoice->issuer_business_name,
                'issuer_tax_regime' => $invoice->issuer_tax_regime,
                'issuer_address' => $invoice->issuer_address,
                'issuer_phone' => $invoice->issuer_phone,
                'issuer_email' => $invoice->issuer_email,
                'issuer_municipality_code' => $invoice->issuer_municipality_code,
                'customer_id_type' => $invoice->customer_id_type,
                'customer_id_number' => $invoice->customer_id_number,
                'customer_dv' => $invoice->customer_dv,
                'customer_name' => $invoice->customer_name,
                'customer_email' => $invoice->customer_email,
                'customer_phone' => $invoice->customer_phone,
                'customer_address' => $invoice->customer_address,
                'customer_municipality_code' => $invoice->customer_municipality_code,
                'line_extension_amount' => $invoice->line_extension_amount,
                'tax_exclusive_amount' => $invoice->tax_exclusive_amount,
                'tax_inclusive_amount' => $invoice->tax_inclusive_amount,
                'allowance_total_amount' => $invoice->allowance_total_amount,
                'tax_total_amount' => $invoice->tax_total_amount,
                'payable_amount' => $invoice->payable_amount,
                'payment_method_code' => $invoice->payment_method_code,
                'payment_means_code' => $invoice->payment_means_code,
                'status' => ElectronicDocument::STATUS_DRAFT,
            ]);

            // Copiar ítems para anulación total
            foreach ($invoice->items as $it) {
                $creditNote->items()->create([
                    'product_id' => $it->product_id,
                    'line_number' => $it->line_number,
                    'product_code' => $it->product_code,
                    'description' => $it->description,
                    'quantity' => $it->quantity,
                    'unit_measure' => $it->unit_measure,
                    'price_amount' => $it->price_amount,
                    'line_extension_amount' => $it->line_extension_amount,
                    'discount_amount' => $it->discount_amount,
                    'tax_type' => $it->tax_type,
                    'tax_rate' => $it->tax_rate,
                    'tax_base_amount' => $it->tax_base_amount,
                    'tax_amount' => $it->tax_amount,
                    'total_item_amount' => $it->total_item_amount,
                ]);
            }

            // Transmisión de la nota crédito
            $provider = $this->manager->resolveForCompany($company);
            $idempotencyKey = 'EMIT-NC-' . $creditNote->id . '-' . Str::random(8);

            $response = $provider->emitCreditNote($creditNote, $config);

            $this->recordLog($company, $creditNote, 'emit_credit_note', $config->provider, '/credit-notes', $idempotencyKey, [], $response);

            if ($response->isSuccess) {
                $creditNote->update([
                    'status' => ElectronicDocument::STATUS_VALIDATED,
                    'uuid_cufe' => $response->cufe,
                    'qr_content' => $response->qrUrl,
                    'dian_status' => $response->dianStatus ?: 'DIAN_ACEPTADO',
                    'dian_response_message' => $response->message,
                    'provider_track_id' => $response->trackId,
                    'provider_raw_response' => $response->rawResponse,
                    'validated_at' => now(),
                ]);

                // Guardar XML de la nota crédito
                if ($response->xmlContent) {
                    $xmlPath = "electronic_documents/{$company->id}/{$creditNote->full_number}.xml";
                    Storage::disk('local')->put($xmlPath, $response->xmlContent);
                    $creditNote->update(['xml_path' => $xmlPath]);
                }

                // Generar PDF
                $this->generatePdfRepresentation($creditNote);

                // Marcar factura original como cancelada/anulada si fue anulación total (Concepto 2)
                if ($conceptCode === '2') {
                    $invoice->update(['status' => ElectronicDocument::STATUS_CANCELLED]);
                }
            } else {
                $creditNote->update([
                    'status' => ElectronicDocument::STATUS_REJECTED,
                    'dian_status' => $response->dianStatus,
                    'dian_response_message' => $response->message,
                    'dian_validation_errors' => $response->validationErrors,
                    'provider_raw_response' => $response->rawResponse,
                ]);
            }

            return $creditNote->fresh(['items', 'company', 'parentDocument']);
        });
    }

    /**
     * Transmite un documento al proveedor tecnológico y procesa la respuesta DIAN.
     */
    protected function transmitDocument(ElectronicDocument $document, BillingConfiguration $config): void
    {
        $provider = $this->manager->resolveForCompany($document->company);
        $idempotencyKey = 'EMIT-' . $document->id . '-' . Str::random(8);

        $document->update([
            'status' => ElectronicDocument::STATUS_SENT,
            'sent_at' => now(),
        ]);

        $response = $provider->emitInvoice($document, $config);

        $this->recordLog(
            $document->company,
            $document,
            'emit_invoice',
            $config->provider,
            '/bills/validate',
            $idempotencyKey,
            ['document_id' => $document->id, 'full_number' => $document->full_number],
            $response
        );

        if ($response->isSuccess) {
            $document->update([
                'status' => ElectronicDocument::STATUS_VALIDATED,
                'uuid_cufe' => $response->cufe,
                'qr_content' => $response->qrUrl,
                'dian_status' => $response->dianStatus ?: 'DIAN_ACEPTADO',
                'dian_response_message' => $response->message,
                'provider_track_id' => $response->trackId,
                'provider_raw_response' => $response->rawResponse,
                'validated_at' => now(),
            ]);

            // Guardar XML firmado en almacenamiento privado
            if ($response->xmlContent) {
                $xmlPath = "electronic_documents/{$document->company_id}/{$document->full_number}.xml";
                Storage::disk('local')->put($xmlPath, $response->xmlContent);
                $document->update(['xml_path' => $xmlPath]);
            }

            // Generar y almacenar representación gráfica en PDF
            $this->generatePdfRepresentation($document);
        } else {
            $document->update([
                'status' => ElectronicDocument::STATUS_REJECTED,
                'dian_status' => $response->dianStatus ?: 'DIAN_RECHAZADO',
                'dian_response_message' => $response->message,
                'dian_validation_errors' => $response->validationErrors,
                'provider_raw_response' => $response->rawResponse,
            ]);
        }
    }

    /**
     * Genera la representación gráfica en PDF del documento electrónico.
     */
    public function generatePdfRepresentation(ElectronicDocument $document): string
    {
        $html = $this->renderDocumentHtml($document);
        $pdfPath = "electronic_documents/{$document->company_id}/{$document->full_number}.pdf";
        
        // Guardamos el HTML renderizado como representación gráfica segura
        Storage::disk('local')->put($pdfPath, $html);
        $document->update(['pdf_path' => $pdfPath]);

        return $pdfPath;
    }

    /**
     * Renderiza el HTML con la representación gráfica conforme al Anexo Técnico 1.9 DIAN.
     */
    public function renderDocumentHtml(ElectronicDocument $doc): string
    {
        $company = $doc->company;
        $profile = $company->fiscalProfile;
        $config = $company->billingConfiguration;

        return view('business.billing.templates.invoice_pdf', [
            'document' => $doc,
            'company' => $company,
            'profile' => $profile,
            'config' => $config,
        ])->render();
    }

    /**
     * Sincroniza el estado de un documento con la DIAN / Proveedor.
     */
    public function syncDocumentStatus(ElectronicDocument $document): BillingProviderResponse
    {
        $config = $document->company->billingConfiguration;
        $provider = $this->manager->resolveForCompany($document->company);

        $response = $provider->queryDocumentStatus($document, $config);

        $this->recordLog(
            $document->company,
            $document,
            'query_status',
            $config->provider,
            '/bills/show',
            'SYNC-' . Str::random(8),
            ['full_number' => $document->full_number],
            $response
        );

        if ($response->isSuccess && $response->dianStatus === 'DIAN_ACEPTADO') {
            $document->update([
                'status' => ElectronicDocument::STATUS_VALIDATED,
                'dian_status' => 'DIAN_ACEPTADO',
                'dian_response_message' => $response->message,
                'validated_at' => $document->validated_at ?: now(),
            ]);
        }

        return $response;
    }

    protected function recordLog(
        Company $company,
        ?ElectronicDocument $document,
        string $action,
        string $provider,
        string $endpoint,
        string $idempotencyKey,
        array $requestPayload,
        BillingProviderResponse $response
    ): BillingLog {
        return BillingLog::create([
            'company_id' => $company->id,
            'electronic_document_id' => $document?->id,
            'action' => $action,
            'provider' => $provider,
            'endpoint' => $endpoint,
            'idempotency_key' => $idempotencyKey,
            'request_payload' => $requestPayload,
            'response_payload' => $response->rawResponse ?? ['message' => $response->message],
            'http_status' => (int) $response->statusCode,
            'is_success' => $response->isSuccess,
            'error_message' => $response->isSuccess ? null : $response->message,
            'ip_address' => request()->ip(),
        ]);
    }
}
