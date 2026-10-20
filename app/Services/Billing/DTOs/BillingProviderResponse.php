<?php

namespace App\Services\Billing\DTOs;

class BillingProviderResponse
{
    public function __construct(
        public bool $isSuccess,
        public string $statusCode,
        public string $message,
        public ?string $cufe = null,
        public ?string $qrUrl = null,
        public ?string $xmlContent = null,
        public ?string $pdfUrl = null,
        public ?string $dianStatus = null,
        public ?string $trackId = null,
        public ?array $rawResponse = null,
        public ?array $validationErrors = null,
    ) {}

    public static function success(
        string $message,
        ?string $cufe = null,
        ?string $qrUrl = null,
        ?string $xmlContent = null,
        ?string $pdfUrl = null,
        ?string $dianStatus = 'DIAN_ACEPTADO',
        ?string $trackId = null,
        ?array $rawResponse = null
    ): self {
        return new self(
            isSuccess: true,
            statusCode: '200',
            message: $message,
            cufe: $cufe,
            qrUrl: $qrUrl,
            xmlContent: $xmlContent,
            pdfUrl: $pdfUrl,
            dianStatus: $dianStatus,
            trackId: $trackId,
            rawResponse: $rawResponse,
            validationErrors: null
        );
    }

    public static function failure(
        string $message,
        string $statusCode = '422',
        ?array $validationErrors = null,
        ?array $rawResponse = null,
        ?string $dianStatus = 'DIAN_RECHAZADO'
    ): self {
        return new self(
            isSuccess: false,
            statusCode: $statusCode,
            message: $message,
            cufe: null,
            qrUrl: null,
            xmlContent: null,
            pdfUrl: null,
            dianStatus: $dianStatus,
            trackId: null,
            rawResponse: $rawResponse,
            validationErrors: $validationErrors
        );
    }
}
