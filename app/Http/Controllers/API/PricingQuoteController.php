<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\PricingRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PricingQuoteController extends BaseController
{
    /**
     * Calculate delivery price using pickup and dropoff coordinates.
     *
     * Recommended request:
     * - pickup_latitude
     * - pickup_longitude
     * - dropoff_latitude
     * - dropoff_longitude
     *
     * For testing, you can also send:
     * - distance_km
     */
    public function calculate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'distance_km' => 'nullable|numeric|min:0.01',

            'pickup_latitude' => 'nullable|numeric|between:-90,90',
            'pickup_longitude' => 'nullable|numeric|between:-180,180',
            'dropoff_latitude' => 'nullable|numeric|between:-90,90',
            'dropoff_longitude' => 'nullable|numeric|between:-180,180',

            'vehicle_type' => 'nullable|string|in:moto,bicycle,car,van',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $hasCoordinates = $request->filled('pickup_latitude')
            && $request->filled('pickup_longitude')
            && $request->filled('dropoff_latitude')
            && $request->filled('dropoff_longitude');

        if (!$request->filled('distance_km') && !$hasCoordinates) {
            return $this->sendError('Validation Error.', [
                'location' => [
                    'Provide either distance_km or pickup/dropoff latitude and longitude.',
                ],
            ], 422);
        }

        if ($hasCoordinates) {
            $distanceKm = $this->calculateDistanceKm(
                (float) $request->pickup_latitude,
                (float) $request->pickup_longitude,
                (float) $request->dropoff_latitude,
                (float) $request->dropoff_longitude
            );

            $distanceSource = 'coordinates_haversine';
        } else {
            $distanceKm = (float) $request->distance_km;
            $distanceSource = 'manual_distance_km';
        }

        $pricingRule = PricingRule::findActiveForVehicle($request->vehicle_type);

        if (!$pricingRule) {
            return $this->sendError('Pricing Error.', [
                'pricing_rule' => ['No active pricing rule found. Please contact admin.'],
            ], 404);
        }

        $calculation = $pricingRule->calculatePrice($distanceKm);

        return $this->sendResponse([
            'pricing_rule' => [
                'id' => $pricingRule->id,
                'name' => $pricingRule->name,
                'vehicle_type' => $pricingRule->vehicle_type,
                'currency' => $pricingRule->currency,
            ],
            'distance_source' => $distanceSource,
            'pickup' => $hasCoordinates ? [
                'latitude' => (float) $request->pickup_latitude,
                'longitude' => (float) $request->pickup_longitude,
            ] : null,
            'dropoff' => $hasCoordinates ? [
                'latitude' => (float) $request->dropoff_latitude,
                'longitude' => (float) $request->dropoff_longitude,
            ] : null,
            'calculation' => $calculation,
        ], 'Delivery price calculated successfully.');
    }

    /**
     * Calculate straight-line distance between two GPS points.
     *
     * Important:
     * This uses Haversine formula.
     * Later, for more accurate road distance, connect Google Maps Distance Matrix API.
     */
    private function calculateDistanceKm(
        float $pickupLatitude,
        float $pickupLongitude,
        float $dropoffLatitude,
        float $dropoffLongitude
    ): float {
        $earthRadiusKm = 6371;

        $latFrom = deg2rad($pickupLatitude);
        $lonFrom = deg2rad($pickupLongitude);
        $latTo = deg2rad($dropoffLatitude);
        $lonTo = deg2rad($dropoffLongitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2)
            + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return round($earthRadiusKm * $angle, 2);
    }
}