<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Rider;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class RiderManagementController extends BaseController
{
    /**
     * List all riders.
     */
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user()->load('role');

        if (!$admin->isAdmin()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only admin can view riders.'],
            ], 403);
        }

        $query = Rider::with(['user.role', 'approvedBy', 'rejectedBy']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_online')) {
            $query->where('is_online', filter_var($request->is_online, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $search = $request->search;

            $query->whereHas('user', function ($userQuery) use ($search) {
                $userQuery->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%')
                    ->orWhere('username', 'like', '%' . $search . '%');
            });
        }

        $riders = $query->latest()->paginate($request->input('per_page', 15));

        return $this->sendResponse([
            'riders' => $riders,
        ], 'Riders fetched successfully.');
    }

    /**
     * Show one rider.
     */
    public function show(Request $request, Rider $rider): JsonResponse
    {
        $admin = $request->user()->load('role');

        if (!$admin->isAdmin()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only admin can view rider details.'],
            ], 403);
        }

        return $this->sendResponse([
            'rider' => $rider->load(['user.role', 'approvedBy', 'rejectedBy']),
        ], 'Rider details fetched successfully.');
    }

    /**
     * Approve rider.
     */
    public function approve(Request $request, Rider $rider): JsonResponse
    {
        $admin = $request->user()->load('role');

        if (!$admin->isAdmin()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only admin can approve riders.'],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $rider->update([
            'status' => Rider::STATUS_APPROVED,
            'approved_by' => $admin->id,
            'approved_at' => now(),
            'rejected_by' => null,
            'rejected_at' => null,
            'rejection_reason' => null,
            'admin_notes' => $request->admin_notes,
        ]);

        return $this->sendResponse([
            'rider' => $rider->fresh()->load(['user.role', 'approvedBy']),
        ], 'Rider approved successfully.');
    }

    /**
     * Reject rider.
     */
    public function reject(Request $request, Rider $rider): JsonResponse
    {
        $admin = $request->user()->load('role');

        if (!$admin->isAdmin()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only admin can reject riders.'],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'rejection_reason' => 'required|string|max:1000',
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $rider->update([
            'status' => Rider::STATUS_REJECTED,
            'is_online' => false,
            'rejected_by' => $admin->id,
            'rejected_at' => now(),
            'rejection_reason' => $request->rejection_reason,
            'admin_notes' => $request->admin_notes,
        ]);

        return $this->sendResponse([
            'rider' => $rider->fresh()->load(['user.role', 'rejectedBy']),
        ], 'Rider rejected successfully.');
    }

    /**
     * Suspend rider.
     */
    public function suspend(Request $request, Rider $rider): JsonResponse
    {
        $admin = $request->user()->load('role');

        if (!$admin->isAdmin()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only admin can suspend riders.'],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $rider->update([
            'status' => Rider::STATUS_SUSPENDED,
            'is_online' => false,
            'admin_notes' => $request->admin_notes,
        ]);

        return $this->sendResponse([
            'rider' => $rider->fresh()->load(['user.role']),
        ], 'Rider suspended successfully.');
    }
}