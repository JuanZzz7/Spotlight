<?php

namespace App\Services\Shipping\Contracts;

interface ShipmentCarrierInterface
{
    /**
     * Generate an official format tracking guide number for the carrier.
     */
    public function generateTrackingNumber(string $carrier): string;

    /**
     * Fetch real-time status and checkpoint payload from carrier API.
     */
    public function fetchTrackingData(string $trackingNumber): array;

    /**
     * Estimate delivery date based on origin, destination and carrier.
     */
    public function estimateDeliveryDate(string $origin, string $destination, string $carrier): \Carbon\Carbon;
}
