<?php

namespace App\Services\Billing\Contracts;

use App\Models\BillingConfiguration;
use App\Models\CompanyFiscalProfile;
use App\Models\ElectronicDocument;
use App\Services\Billing\DTOs\BillingProviderResponse;

interface BillingProviderInterface
{
    /**
     * Valida las credenciales y conectividad técnica con el proveedor / DIAN.
     */
    public function validateConnection(CompanyFiscalProfile $profile, BillingConfiguration $config): BillingProviderResponse;

    /**
     * Emite una factura electrónica de venta (01) ante el proveedor y la DIAN.
     */
    public function emitInvoice(ElectronicDocument $document, BillingConfiguration $config): BillingProviderResponse;

    /**
     * Emite una nota crédito electrónica (91) referenciando una factura previa.
     */
    public function emitCreditNote(ElectronicDocument $document, BillingConfiguration $config): BillingProviderResponse;

    /**
     * Emite una nota débito electrónica (92) referenciando una factura previa.
     */
    public function emitDebitNote(ElectronicDocument $document, BillingConfiguration $config): BillingProviderResponse;

    /**
     * Consulta el estado de validación de un documento ante el proveedor / DIAN.
     */
    public function queryDocumentStatus(ElectronicDocument $document, BillingConfiguration $config): BillingProviderResponse;

    /**
     * Descarga el archivo XML firmado reglamentario del documento.
     */
    public function downloadXml(ElectronicDocument $document, BillingConfiguration $config): ?string;

    /**
     * Descarga la representación gráfica en PDF del documento.
     */
    public function downloadPdf(ElectronicDocument $document, BillingConfiguration $config): ?string;
}
