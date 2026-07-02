<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rider extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';
    public const STATUS_APPROVED = 'approved';
    public const STATUS_REJECTED = 'rejected';
    public const STATUS_SUSPENDED = 'suspended';

    protected $fillable = [
        'user_id',
        'vehicle_type',
        'vehicle_plate_number',
        'vehicle_color',
        'national_id',
        'driving_license_number',
        'status',
        'is_online',
        'current_latitude',
        'current_longitude',
        'current_address',
        'last_location_updated_at',
        'approved_by',
        'approved_at',
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'admin_notes',
    ];

    protected $casts = [
        'is_online' => 'boolean',
        'current_latitude' => 'decimal:7',
        'current_longitude' => 'decimal:7',
        'last_location_updated_at' => 'datetime',
        'approved_at' => 'datetime',
        'rejected_at' => 'datetime',
    ];

    /**
     * Rider belongs to one user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Admin who approved the rider.
     */
    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    /**
     * Admin who rejected the rider.
     */
    public function rejectedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    /**
     * Check if rider is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === self::STATUS_APPROVED;
    }

    /**
     * Check if rider is available for delivery.
     */
    public function isAvailable(): bool
    {
        return $this->status === self::STATUS_APPROVED && $this->is_online;
    }
}