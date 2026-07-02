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
        'base_price',
        'price_per_km',
        'minimum_price',
        'commission_percentage',
        'currency',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'price_per_km' => 'decimal:2',
        'minimum_price' => 'decimal:2',
        'commission_percentage' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Admin who created the pricing rule.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Admin who last updated the pricing rule.
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Find active pricing rule for a vehicle type.
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
     * Calculate delivery price, commission, and rider earning.
     */
    public function calculatePrice(float $distanceKm): array
    {
        $basePrice = (float) $this->base_price;
        $pricePerKm = (float) $this->price_per_km;
        $minimumPrice = (float) $this->minimum_price;
        $commissionPercentage = (float) $this->commission_percentage;

        $rawPrice = $basePrice + ($distanceKm * $pricePerKm);
        $deliveryPrice = max($rawPrice, $minimumPrice);

        $commissionAmount = ($deliveryPrice * $commissionPercentage) / 100;
        $riderEarning = $deliveryPrice - $commissionAmount;

        return [
            'distance_km' => round($distanceKm, 2),
            'base_price' => round($basePrice, 2),
            'price_per_km' => round($pricePerKm, 2),
            'minimum_price' => round($minimumPrice, 2),
            'raw_price' => round($rawPrice, 2),
            'delivery_price' => round($deliveryPrice, 2),
            'commission_percentage' => round($commissionPercentage, 2),
            'commission_amount' => round($commissionAmount, 2),
            'rider_earning' => round($riderEarning, 2),
            'currency' => $this->currency,
        ];
    }
}