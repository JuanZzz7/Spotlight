<?php

namespace App\Services\Billing\Providers;

use App\Models\BillingConfiguration;
use App\Models\CompanyFiscalProfile;
use App\Models\ElectronicDocument;
use App\Services\Billing\Contracts\BillingProviderInterface;
use App\Services\Billing\DTOs\BillingProviderResponse;
use Illuminate\Support\Str;

class SandboxBillingProvider implements BillingProviderInterface
{
    /**
     * Valida la conectividad técnica y formato de la resolución en el ambiente de pruebas.
     */
    public function validateConnection(CompanyFiscalProfile $profile, BillingConfiguration $config): BillingProviderResponse
    {
        // Validar que exista perfil fiscal
        if (!$profile->isVerified()) {
            return BillingProviderResponse::failure(
                'El comercio debe estar verificado legalmente antes de configurar el ambiente técnico.',
                '400',
                ['verification' => ['Comercio no verificado']]
            );
        }

        // Validar campos mínimos de la resolución
        if (empty($config->resolution_number) || empty($config->technical_key)) {
            return BillingProviderResponse::failure(
                'La resolución DIAN y la Clave Técnica son obligatorias para el ambiente de pruebas.',
                '422',
                ['resolution' => ['Faltan datos de la resolución DIAN o Clave Técnica']]
            );
        }

        if ($config->range_to < $config->range_from) {
            return BillingProviderResponse::failure(
                'El rango final de la resolución no puede ser menor que el rango inicial.',
                '422',
                ['range' => ['Rango de numeración no válido']]
            );
        }

        return BillingProviderResponse::success(
            'Conexión exitosa con el ambiente de pruebas DIAN (Sandbox Anexo Técnico 1.9). Software ID y Clave Técnica validados.',
            null,
            null,
            null,
            null,
            'DIAN_HABILITADO',
            'TEST-SESSION-' . strtoupper(Str::random(12)),
            [
                'status' => 'OK',
                'environment' => 'sandbox',
                'provider' => 'Spotlight DIAN Sandbox Provider',
                'timestamp' => now()->toIso8601String(),
                'software_id' => $config->software_id ?: 'SPOTLIGHT-SOFTWARE-01',
            ]
        );
    }

    /**
     * Emite una factura electrónica de venta simulando las validaciones del Anexo Técnico 1.9 de la DIAN.
     */
    public function emitInvoice(ElectronicDocument $document, BillingConfiguration $config): BillingProviderResponse
    {
        // Validación de rangos de resolución
        if ($document->consecutive < $config->range_from || $document->consecutive > $config->range_to) {
            return BillingProviderResponse::failure(
                "El consecutivo {$document->consecutive} está fuera del rango autorizado por la DIAN ({$config->range_from} - {$config->range_to}).",
                '422',
                ['consecutive' => ['Consecutivo fuera de rango autorizado']]
            );
        }

        // Validación de fecha de resolución
        if ($config->resolution_date_to && now()->gt($config->resolution_date_to)) {
            return BillingProviderResponse::failure(
                'La vigencia de la resolución de facturación DIAN ha expirado.',
                '422',
                ['resolution_date' => ['Resolución expirada']]
            );
        }

        // Cálculo del CUFE reglamentario DIAN SHA-384
        $cufe = $this->calculateCufe($document, $config);

        // Generar XML UBL 2.1 DIAN
        $xmlContent = $this->generateUblXml($document, $cufe, $config);

        // URL reglamentaria para el código QR
        $qrUrl = config('billing.dian.qr_base_url') . $cufe;

        // Track ID simulado de la DIAN
        $trackId = 'DIAN-TRACK-' . strtoupper(Str::random(24));

        return BillingProviderResponse::success(
            "Factura electrónica {$document->full_number} validada exitosamente por la DIAN.",
            $cufe,
            $qrUrl,
            $xmlContent,
            null, // El PDF se genera localmente
            'DIAN_ACEPTADO',
            $trackId,
            [
                'statusCode' => '0',
                'statusDescription' => 'Procesado Correctamente.',
                'statusMessage' => 'La Factura electrónica ha sido autorizada y validada por la DIAN.',
                'cufe' => $cufe,
                'trackId' => $trackId,
                'issueDate' => $document->issue_date->format('Y-m-d'),
                'issueTime' => $document->issue_time,
                'rulesValidated' => [
                    'Rule-FAD01' => 'Formato de NIT emisor correcto',
                    'Rule-FAD02' => 'Fecha de generación dentro del plazo admitido',
                    'Rule-FAD03' => 'Cálculo de impuestos aritméticamente correcto',
                    'Rule-FAD04' => 'CUFE calculado y firmado según Anexo Técnico 1.9',
                ]
            ]
        );
    }

    /**
     * Emite una nota crédito referenciando la factura electrónica previa.
     */
    public function emitCreditNote(ElectronicDocument $document, BillingConfiguration $config): BillingProviderResponse
    {
        if (!$document->parentDocument) {
            return BillingProviderResponse::failure(
                'La Nota Crédito requiere un documento padre referenciado (Factura de Venta).',
                '422',
                ['parent_document' => ['Factura de venta referenciada no encontrada']]
            );
        }

        // Cálculo del CUDE (Código Único de Documento Electrónico)
        $cude = $this->calculateCude($document, $config);

        // Generación de XML UBL 2.1 para Nota Crédito
        $xmlContent = $this->generateCreditNoteXml($document, $cude, $config);
        $qrUrl = config('billing.dian.qr_base_url') . $cude;
        $trackId = 'DIAN-NC-TRACK-' . strtoupper(Str::random(24));

        return BillingProviderResponse::success(
            "Nota Crédito Electrónica {$document->full_number} validada exitosamente por la DIAN.",
            $cude,
            $qrUrl,
            $xmlContent,
            null,
            'DIAN_ACEPTADO',
            $trackId,
            [
                'statusCode' => '0',
                'statusDescription' => 'Nota Crédito Aceptada.',
                'referencedInvoice' => $document->parentDocument->full_number,
                'cude' => $cude,
                'trackId' => $trackId,
            ]
        );
    }

    /**
     * Emite una nota débito referenciando la factura previa.
     */
    public function emitDebitNote(ElectronicDocument $document, BillingConfiguration $config): BillingProviderResponse
    {
        if (!$document->parentDocument) {
            return BillingProviderResponse::failure(
                'La Nota Débito requiere una factura electrónica referenciada.',
                '422',
                ['parent_document' => ['Factura de venta referenciada no encontrada']]
            );
        }

        $cude = $this->calculateCude($document, $config);
        $xmlContent = $this->generateCreditNoteXml($document, $cude, $config);
        $qrUrl = config('billing.dian.qr_base_url') . $cude;
        $trackId = 'DIAN-ND-TRACK-' . strtoupper(Str::random(24));

        return BillingProviderResponse::success(
            "Nota Débito Electrónica {$document->full_number} validada exitosamente por la DIAN.",
            $cude,
            $qrUrl,
            $xmlContent,
            null,
            'DIAN_ACEPTADO',
            $trackId,
            [
                'statusCode' => '0',
                'statusDescription' => 'Nota Débito Aceptada.',
                'referencedInvoice' => $document->parentDocument->full_number,
                'cude' => $cude,
                'trackId' => $trackId,
            ]
        );
    }

    /**
     * Consulta el estado de validación en la DIAN.
     */
    public function queryDocumentStatus(ElectronicDocument $document, BillingConfiguration $config): BillingProviderResponse
    {
        return BillingProviderResponse::success(
            "Documento {$document->full_number} verificado en el catálogo DIAN.",
            $document->uuid_cufe,
            $document->qr_content,
            null,
            null,
            'DIAN_ACEPTADO',
            $document->provider_track_id,
            [
                'status' => 'DIAN_ACEPTADO',
                'fullNumber' => $document->full_number,
                'cufe' => $document->uuid_cufe,
                'validatedAt' => $document->validated_at ? $document->validated_at->toIso8601String() : now()->toIso8601String(),
            ]
        );
    }

    public function downloadXml(ElectronicDocument $document, BillingConfiguration $config): ?string
    {
        return $this->generateUblXml($document, $document->uuid_cufe, $config);
    }

    public function downloadPdf(ElectronicDocument $document, BillingConfiguration $config): ?string
    {
        return null; // Generado internamente por el ElectronicDocumentService
    }

    /**
     * Calcula el CUFE según el algoritmo oficial de la DIAN:
     * CUFE = SHA-384(NumFac + FecFac + HorFac + ValFac + CodImp1 + ValImp1 + CodImp2 + ValImp2 + CodImp3 + ValImp3 + ValTot + NitOfe + DocAdq + ClaveTecnica + TipoAmbiente)
     */
    public function calculateCufe(ElectronicDocument $document, BillingConfiguration $config): string
    {
        $numFac = $document->full_number;
        $fecFac = $document->issue_date->format('Y-m-d');
        $horFac = $document->issue_time . '-05:00';
        $valFac = number_format((float) $document->tax_exclusive_amount, 2, '.', '');
        
        // Impuestos (01: IVA)
        $codImp1 = '01';
        $valImp1 = number_format((float) $document->tax_total_amount, 2, '.', '');
        $codImp2 = '04'; // Consumo (0 si no aplica)
        $valImp2 = '0.00';
        $codImp3 = '03'; // ICA
        $valImp3 = '0.00';
        
        $valTot = number_format((float) $document->payable_amount, 2, '.', '');
        $nitOfe = preg_replace('/\D/', '', $document->issuer_nit);
        $docAdq = preg_replace('/\D/', '', $document->customer_id_number);
        $claveTecnica = $config->technical_key ?: 'SPOTLIGHT_SANDBOX_TECH_KEY_2026';
        $tipoAmbiente = ($config->environment === 'production') ? '1' : '2';

        $rawChain = $numFac . $fecFac . $horFac . $valFac . $codImp1 . $valImp1 . $codImp2 . $valImp2 . $codImp3 . $valImp3 . $valTot . $nitOfe . $docAdq . $claveTecnica . $tipoAmbiente;

        return hash('sha384', $rawChain);
    }

    /**
     * Calcula el CUDE para Notas Crédito y Débito:
     * CUDE = SHA-384(NumDoc + FecDoc + HorDoc + ValDoc + CodImp1 + ValImp1 + ... + NitOfe + DocAdq + PinSoftware + TipoAmbiente)
     */
    public function calculateCude(ElectronicDocument $document, BillingConfiguration $config): string
    {
        $numDoc = $document->full_number;
        $fecDoc = $document->issue_date->format('Y-m-d');
        $horDoc = $document->issue_time . '-05:00';
        $valDoc = number_format((float) $document->tax_exclusive_amount, 2, '.', '');
        $valTot = number_format((float) $document->payable_amount, 2, '.', '');
        $nitOfe = preg_replace('/\D/', '', $document->issuer_nit);
        $docAdq = preg_replace('/\D/', '', $document->customer_id_number);
        $pinSoftware = $config->software_pin ?: '12345';
        $tipoAmbiente = ($config->environment === 'production') ? '1' : '2';

        $rawChain = $numDoc . $fecDoc . $horDoc . $valDoc . '01' . number_format((float) $document->tax_total_amount, 2, '.', '') . '040.00030.00' . $valTot . $nitOfe . $docAdq . $pinSoftware . $tipoAmbiente;

        return hash('sha384', $rawChain);
    }

    /**
     * Genera XML UBL 2.1 estándar según Anexo Técnico DIAN 1.9.
     */
    private function generateUblXml(ElectronicDocument $doc, ?string $cufe, BillingConfiguration $config): string
    {
        $cufeStr = $cufe ?: 'PENDING_CUFE';
        $issueDate = $doc->issue_date->format('Y-m-d');
        $issueTime = $doc->issue_time . '-05:00';
        $env = ($config->environment === 'production') ? '1' : '2';

        $itemsXml = '';
        foreach ($doc->items as $item) {
            $itemsXml .= "
    <cac:InvoiceLine>
        <cbc:ID>{$item->line_number}</cbc:ID>
        <cbc:InvoicedQuantity unitCode=\"{$item->unit_measure}\">{$item->quantity}</cbc:InvoicedQuantity>
        <cbc:LineExtensionAmount currencyID=\"COP\">{$item->line_extension_amount}</cbc:LineExtensionAmount>
        <cac:TaxTotal>
            <cbc:TaxAmount currencyID=\"COP\">{$item->tax_amount}</cbc:TaxAmount>
            <cac:TaxSubtotal>
                <cbc:TaxableAmount currencyID=\"COP\">{$item->tax_base_amount}</cbc:TaxableAmount>
                <cbc:TaxAmount currencyID=\"COP\">{$item->tax_amount}</cbc:TaxAmount>
                <cac:TaxCategory>
                    <cbc:Percent>{$item->tax_rate}</cbc:Percent>
                    <cac:TaxScheme>
                        <cbc:ID>{$item->tax_type}</cbc:ID>
                        <cbc:Name>IVA</cbc:Name>
                    </cac:TaxScheme>
                </cac:TaxCategory>
            </cac:TaxSubtotal>
        </cac:TaxTotal>
        <cac:Item>
            <cbc:Description>" . htmlspecialchars($item->description, ENT_XML1) . "</cbc:Description>
            <cac:SellersItemIdentification>
                <cbc:ID>" . htmlspecialchars($item->product_code ?: 'ITEM-' . $item->id, ENT_XML1) . "</cbc:ID>
            </cac:SellersItemIdentification>
        </cac:Item>
        <cac:Price>
            <cbc:PriceAmount currencyID=\"COP\">{$item->price_amount}</cbc:PriceAmount>
        </cac:Price>
    </cac:InvoiceLine>";
        }

        return "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>
<Invoice xmlns=\"urn:oasis:names:specification:ubl:schema:xsd:Invoice-2\"
         xmlns:cac=\"urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2\"
         xmlns:cbc=\"urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2\"
         xmlns:ds=\"http://www.w3.org/2000/09/xmldsig#\"
         xmlns:ext=\"urn:oasis:names:specification:ubl:schema:xsd:CommonExtensionComponents-2\">
    <ext:UBLExtensions>
        <ext:UBLExtension>
            <ext:ExtensionContent>
                <DianExtensions>
                    <SoftwareSecurityCode>{$cufeStr}</SoftwareSecurityCode>
                    <SoftwareProvider>
                        <ProviderID schemeAgencyID=\"195\" schemeID=\"4\">{$doc->issuer_nit}</ProviderID>
                    </SoftwareProvider>
                </DianExtensions>
            </ext:ExtensionContent>
        </ext:UBLExtension>
    </ext:UBLExtensions>
    <cbc:UBLVersionID>UBL 2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>{$env}</cbc:CustomizationID>
    <cbc:ProfileID>DIAN 2.1: Factura Electrónica de Venta</cbc:ProfileID>
    <cbc:ID>{$doc->full_number}</cbc:ID>
    <cbc:UUID schemeID=\"{$env}\" schemeName=\"CUFE-SHA384\">{$cufeStr}</cbc:UUID>
    <cbc:IssueDate>{$issueDate}</cbc:IssueDate>
    <cbc:IssueTime>{$issueTime}</cbc:IssueTime>
    <cbc:InvoiceTypeCode>01</cbc:InvoiceTypeCode>
    <cbc:DocumentCurrencyCode>COP</cbc:DocumentCurrencyCode>
    <cac:AccountingSupplierParty>
        <cbc:AdditionalAccountID>1</cbc:AdditionalAccountID>
        <cac:Party>
            <cac:PartyTaxScheme>
                <cbc:RegistrationName>" . htmlspecialchars($doc->issuer_business_name, ENT_XML1) . "</cbc:RegistrationName>
                <cbc:CompanyID schemeAgencyID=\"195\" schemeAgencyName=\"CO, DIAN (Dirección de Impuestos y Aduanas Nacionales)\" schemeID=\"{$doc->issuer_dv}\">{$doc->issuer_nit}</cbc:CompanyID>
                <cac:TaxScheme>
                    <cbc:ID>01</cbc:ID>
                    <cbc:Name>IVA</cbc:Name>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
        </cac:Party>
    </cac:AccountingSupplierParty>
    <cac:AccountingCustomerParty>
        <cbc:AdditionalAccountID>2</cbc:AdditionalAccountID>
        <cac:Party>
            <cac:PartyTaxScheme>
                <cbc:RegistrationName>" . htmlspecialchars($doc->customer_name, ENT_XML1) . "</cbc:RegistrationName>
                <cbc:CompanyID schemeAgencyID=\"195\" schemeAgencyName=\"CO, DIAN\" schemeName=\"{$doc->customer_id_type}\">{$doc->customer_id_number}</cbc:CompanyID>
                <cac:TaxScheme>
                    <cbc:ID>01</cbc:ID>
                    <cbc:Name>IVA</cbc:Name>
                </cac:TaxScheme>
            </cac:PartyTaxScheme>
        </cac:Party>
    </cac:AccountingCustomerParty>
    <cac:PaymentMeans>
        <cbc:ID>{$doc->payment_means_code}</cbc:ID>
        <cbc:PaymentMeansCode>{$doc->payment_method_code}</cbc:PaymentMeansCode>
    </cac:PaymentMeans>
    <cac:TaxTotal>
        <cbc:TaxAmount currencyID=\"COP\">{$doc->tax_total_amount}</cbc:TaxAmount>
        <cac:TaxSubtotal>
            <cbc:TaxableAmount currencyID=\"COP\">{$doc->tax_exclusive_amount}</cbc:TaxableAmount>
            <cbc:TaxAmount currencyID=\"COP\">{$doc->tax_total_amount}</cbc:TaxAmount>
            <cac:TaxCategory>
                <cac:TaxScheme>
                    <cbc:ID>01</cbc:ID>
                    <cbc:Name>IVA</cbc:Name>
                </cac:TaxScheme>
            </cac:TaxCategory>
        </cac:TaxSubtotal>
    </cac:TaxTotal>
    <cac:LegalMonetaryTotal>
        <cbc:LineExtensionAmount currencyID=\"COP\">{$doc->line_extension_amount}</cbc:LineExtensionAmount>
        <cbc:TaxExclusiveAmount currencyID=\"COP\">{$doc->tax_exclusive_amount}</cbc:TaxExclusiveAmount>
        <cbc:TaxInclusiveAmount currencyID=\"COP\">{$doc->tax_inclusive_amount}</cbc:TaxInclusiveAmount>
        <cbc:AllowanceTotalAmount currencyID=\"COP\">{$doc->allowance_total_amount}</cbc:AllowanceTotalAmount>
        <cbc:PayableAmount currencyID=\"COP\">{$doc->payable_amount}</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
    {$itemsXml}
</Invoice>";
    }

    /**
     * Genera XML UBL 2.1 para Nota Crédito Electrónica.
     */
    private function generateCreditNoteXml(ElectronicDocument $doc, ?string $cude, BillingConfiguration $config): string
    {
        $cudeStr = $cude ?: 'PENDING_CUDE';
        $parentNumber = $doc->parentDocument ? $doc->parentDocument->full_number : 'FE-000';
        $parentCufe = $doc->parentDocument ? $doc->parentDocument->uuid_cufe : '';

        return "<?xml version=\"1.0\" encoding=\"UTF-8\" standalone=\"no\"?>
<CreditNote xmlns=\"urn:oasis:names:specification:ubl:schema:xsd:CreditNote-2\"
            xmlns:cac=\"urn:oasis:names:specification:ubl:schema:xsd:CommonAggregateComponents-2\"
            xmlns:cbc=\"urn:oasis:names:specification:ubl:schema:xsd:CommonBasicComponents-2\">
    <cbc:UBLVersionID>UBL 2.1</cbc:UBLVersionID>
    <cbc:CustomizationID>2</cbc:CustomizationID>
    <cbc:ProfileID>DIAN 2.1: Nota Crédito de Factura Electrónica</cbc:ProfileID>
    <cbc:ID>{$doc->full_number}</cbc:ID>
    <cbc:UUID schemeName=\"CUDE-SHA384\">{$cudeStr}</cbc:UUID>
    <cbc:IssueDate>{$doc->issue_date->format('Y-m-d')}</cbc:IssueDate>
    <cbc:IssueTime>{$doc->issue_time}-05:00</cbc:IssueTime>
    <cac:BillingReference>
        <cac:InvoiceDocumentReference>
            <cbc:ID>{$parentNumber}</cbc:ID>
            <cbc:UUID>{$parentCufe}</cbc:UUID>
        </cac:InvoiceDocumentReference>
    </cac:BillingReference>
    <cac:DiscrepancyResponse>
        <cbc:ReferenceID>{$parentNumber}</cbc:ReferenceID>
        <cbc:ResponseCode>{$doc->correction_concept_code}</cbc:ResponseCode>
        <cbc:Description>" . htmlspecialchars($doc->correction_reason ?: 'Corrección comercial', ENT_XML1) . "</cbc:Description>
    </cac:DiscrepancyResponse>
    <cac:LegalMonetaryTotal>
        <cbc:PayableAmount currencyID=\"COP\">{$doc->payable_amount}</cbc:PayableAmount>
    </cac:LegalMonetaryTotal>
</CreditNote>";
    }
}
