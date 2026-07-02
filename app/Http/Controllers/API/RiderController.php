<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Rider;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RiderController extends BaseController
{
    /**
     * Show logged-in rider profile.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role', 'rider']);

        if (!$user->isRider()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only rider users can access rider profile.'],
            ], 403);
        }

        return $this->sendResponse([
            'rider' => $user->rider,
            'user' => [
                'id' => $user->id,
                'role' => $user->role?->name,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ], 'Rider profile fetched successfully.');
    }

    /**
     * Create or update rider profile.
     */
    public function storeOrUpdate(Request $request): JsonResponse
    {
        $user = $request->user()->load('role');

        if (!$user->isRider()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only rider users can create or update rider profile.'],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'vehicle_type' => 'required|string|in:moto,bicycle,car,van',
            'vehicle_plate_number' => 'nullable|string|max:50',
            'vehicle_color' => 'nullable|string|max:50',
            'national_id' => 'nullable|string|max:100',
            'driving_license_number' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $rider = Rider::updateOrCreate(
            [
                'user_id' => $user->id,
            ],
            [
                'vehicle_type' => $request->vehicle_type,
                'vehicle_plate_number' => $request->vehicle_plate_number,
                'vehicle_color' => $request->vehicle_color,
                'national_id' => $request->national_id,
                'driving_license_number' => $request->driving_license_number,

                /*
                 * If rider profile is newly created, it starts as pending.
                 * Admin must approve it before rider can go online.
                 */
                'status' => $user->rider?->status ?? Rider::STATUS_PENDING,
            ]
        );

        return $this->sendResponse([
            'rider' => $rider->fresh()->load('user.role'),
        ], 'Rider profile saved successfully. Waiting for admin approval.');
    }

    /**
     * Rider goes online.
     */
    public function goOnline(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role', 'rider']);

        if (!$user->isRider()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only rider users can go online.'],
            ], 403);
        }

        if (!$user->rider) {
            return $this->sendError('Rider Error.', [
                'rider' => ['Please create rider profile first.'],
            ], 404);
        }

        if (!$user->rider->isApproved()) {
            return $this->sendError('Approval Error.', [
                'status' => ['Your rider account is not approved yet.'],
            ], 403);
        }

        $user->rider->update([
            'is_online' => true,
        ]);

        return $this->sendResponse([
            'rider' => $user->rider->fresh(),
        ], 'Rider is now online.');
    }

    /**
     * Rider goes offline.
     */
    public function goOffline(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role', 'rider']);

        if (!$user->isRider()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only rider users can go offline.'],
            ], 403);
        }

        if (!$user->rider) {
            return $this->sendError('Rider Error.', [
                'rider' => ['Please create rider profile first.'],
            ], 404);
        }

        $user->rider->update([
            'is_online' => false,
        ]);

        return $this->sendResponse([
            'rider' => $user->rider->fresh(),
        ], 'Rider is now offline.');
    }

    /**
     * Update rider GPS location.
     */
    public function updateLocation(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role', 'rider']);

        if (!$user->isRider()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only rider users can update rider location.'],
            ], 403);
        }

        if (!$user->rider) {
            return $this->sendError('Rider Error.', [
                'rider' => ['Please create rider profile first.'],
            ], 404);
        }

        if (!$user->rider->isApproved()) {
            return $this->sendError('Approval Error.', [
                'status' => ['Your rider account is not approved yet.'],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'current_latitude' => 'required|numeric|between:-90,90',
            'current_longitude' => 'required|numeric|between:-180,180',
            'current_address' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user->rider->update([
            'current_latitude' => $request->current_latitude,
            'current_longitude' => $request->current_longitude,
            'current_address' => $request->current_address,
            'last_location_updated_at' => now(),
        ]);

        return $this->sendResponse([
            'rider' => $user->rider->fresh(),
        ], 'Rider location updated successfully.');
    }
}