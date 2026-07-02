<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PricingRule extends Model
{
    use HasFactory;

    public const VEHICLE_MOTO = 'moto';
    public const VEHICLE_BICYCLE = 'bicycle';
    public const VEHICLE_CAR = 'car';
    public const VEHICLE_VAN = 'van';

    protected $fillable = [
        'name',
        'vehicle_type',

        /*
         * Akamoto tariff:
         * first base_distance_km is charged base_price.
         * extra distance is charged extra_price_per_km.
         */
        'base_distance_km',
        'base_price',
        'price_per_km',
        'extra_price_per_km',
        'minimum_price',

        /*
         * Commission is 20% of completed journey price.
         */
        'commission_percentage',

        'currency',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'base_distance_km' => 'decimal:2',
        'base_price' => 'decimal:2',
        'price_per_km' => 'decimal:2',
        'extra_price_per_km' => 'decimal:2',
        'minimum_price' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Find active pricing rule for vehicle type.
     *
     * Priority:
     * 1. Active rule for exact vehicle type
     * 2. Active default rule where vehicle_type is null
     * 3. Any active rule
     */
    public static function findActiveForVehicle(?string $vehicleType = null): ?self
    {
        if ($vehicleType) {
            $exactRule = self::where('is_active', true)
                ->where('vehicle_type', $vehicleType)
                ->latest()
                ->first();

            if ($exactRule) {
                return $exactRule;
            }
        }

        $defaultRule = self::where('is_active', true)
            ->whereNull('vehicle_type')
            ->latest()
            ->first();

        if ($defaultRule) {
            return $defaultRule;
        }

        return self::where('is_active', true)
            ->latest()
            ->first();
    }

    /**
     * Akamoto pricing formula:
     *
     * First 3 km = 1000 RWF
     * Every extra started km = 200 RWF
     *
     * Example:
     * 3 km = 1000
     * 4 km = 1200
     * 6.2 km = 1800 because extra 3.2 km is rounded to 4 km
     */
    public function calculatePrice(float $distanceKm): array
    {
        $distanceKm = max($distanceKm, 0);

        $baseDistanceKm = (float) ($this->base_distance_km ?? 3);
        $basePrice = (float) ($this->base_price ?? 1000);
        $extraPricePerKm = (float) ($this->extra_price_per_km ?? 200);
        $minimumPrice = (float) ($this->minimum_price ?? 0);
        $commissionPercentage = (float) ($this->commission_percentage ?? 20);

        $extraDistanceKm = max($distanceKm - $baseDistanceKm, 0);

        /*
         * We charge every started extra KM.
         * Example: extra 0.1 km = 1 extra km charge.
         */
        $chargedExtraKm = (int) ceil($extraDistanceKm);

        $rawPrice = $basePrice + ($chargedExtraKm * $extraPricePerKm);

        /*
         * Minimum price is optional.
         * If minimum_price is 0, raw price is used.
         */
        $deliveryPrice = $minimumPrice > 0
            ? max($rawPrice, $minimumPrice)
            : $rawPrice;

        /*
         * This is estimated commission.
         * In the orders module, commission should be saved only when journey is completed.
         */
        $commissionAmount = ($deliveryPrice * $commissionPercentage) / 100;
        $riderEarning = $deliveryPrice - $commissionAmount;

        return [
            'distance_km' => round($distanceKm, 2),
            'base_distance_km' => round($baseDistanceKm, 2),
            'base_price' => round($basePrice, 2),
            'extra_distance_km' => round($extraDistanceKm, 2),
            'charged_extra_km' => $chargedExtraKm,
            'extra_price_per_km' => round($extraPricePerKm, 2),
            'minimum_price' => round($minimumPrice, 2),
            'delivery_price' => round($deliveryPrice, 2),
            'commission_percentage' => round($commissionPercentage, 2),
            'commission_amount' => round($commissionAmount, 2),
            'rider_earning' => round($riderEarning, 2),
            'currency' => $this->currency ?? 'RWF',
            'commission_note' => 'Commission is applied when the journey is completed.',
        ];
    }
}