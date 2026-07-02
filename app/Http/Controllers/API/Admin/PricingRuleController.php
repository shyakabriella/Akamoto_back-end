<?php

namespace App\Http\Controllers\API\Admin;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\PricingRule;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PricingRuleController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user()->load('role');

        if (!$admin->isAdmin()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only admin can view pricing rules.'],
            ], 403);
        }

        $query = PricingRule::with(['createdBy', 'updatedBy']);

        if ($request->filled('vehicle_type')) {
            $query->where('vehicle_type', $request->vehicle_type);
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $pricingRules = $query->latest()->paginate($request->input('per_page', 15));

        return $this->sendResponse([
            'pricing_rules' => $pricingRules,
        ], 'Pricing rules fetched successfully.');
    }

    public function store(Request $request): JsonResponse
    {
        $admin = $request->user()->load('role');

        if (!$admin->isAdmin()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only admin can create pricing rules.'],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'vehicle_type' => 'nullable|string|in:moto,bicycle,car,van',

            /*
             * Akamoto default:
             * first 3 km = 1000
             * extra 1 km = 200
             * commission = 20%
             */
            'base_distance_km' => 'nullable|numeric|min:0.1',
            'base_price' => 'nullable|numeric|min:0',
            'extra_price_per_km' => 'nullable|numeric|min:0',
            'minimum_price' => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',

            'currency' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $pricingRule = DB::transaction(function () use ($request, $admin) {
            $isActive = $request->boolean('is_active');

            if ($isActive) {
                PricingRule::where('vehicle_type', $request->vehicle_type)
                    ->where('is_active', true)
                    ->update(['is_active' => false]);
            }

            return PricingRule::create([
                'name' => $request->name,
                'vehicle_type' => $request->vehicle_type,

                'base_distance_km' => $request->input('base_distance_km', 3),
                'base_price' => $request->input('base_price', 1000),
                'price_per_km' => 0,
                'extra_price_per_km' => $request->input('extra_price_per_km', 200),
                'minimum_price' => $request->input('minimum_price', 0),
                'commission_percentage' => $request->input('commission_percentage', 20),

                'currency' => $request->input('currency', 'RWF'),
                'is_active' => $isActive,
                'created_by' => $admin->id,
                'updated_by' => $admin->id,
            ]);
        });

        return $this->sendResponse([
            'pricing_rule' => $pricingRule->load(['createdBy', 'updatedBy']),
        ], 'Pricing rule created successfully.');
    }

    public function show(Request $request, PricingRule $pricingRule): JsonResponse
    {
        $admin = $request->user()->load('role');

        if (!$admin->isAdmin()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only admin can view pricing rule details.'],
            ], 403);
        }

        return $this->sendResponse([
            'pricing_rule' => $pricingRule->load(['createdBy', 'updatedBy']),
        ], 'Pricing rule details fetched successfully.');
    }

    public function update(Request $request, PricingRule $pricingRule): JsonResponse
    {
        $admin = $request->user()->load('role');

        if (!$admin->isAdmin()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only admin can update pricing rules.'],
            ], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'vehicle_type' => 'nullable|string|in:moto,bicycle,car,van',

            'base_distance_km' => 'nullable|numeric|min:0.1',
            'base_price' => 'nullable|numeric|min:0',
            'extra_price_per_km' => 'nullable|numeric|min:0',
            'minimum_price' => 'nullable|numeric|min:0',
            'commission_percentage' => 'nullable|numeric|min:0|max:100',

            'currency' => 'nullable|string|max:10',
            'is_active' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        DB::transaction(function () use ($request, $admin, $pricingRule) {
            $data = $request->only([
                'name',
                'vehicle_type',
                'base_distance_km',
                'base_price',
                'extra_price_per_km',
                'minimum_price',
                'commission_percentage',
                'currency',
            ]);

            if ($request->has('is_active')) {
                $data['is_active'] = $request->boolean('is_active');

                if ($data['is_active']) {
                    PricingRule::where('id', '!=', $pricingRule->id)
                        ->where('vehicle_type', $request->input('vehicle_type', $pricingRule->vehicle_type))
                        ->where('is_active', true)
                        ->update(['is_active' => false]);
                }
            }

            $data['updated_by'] = $admin->id;

            $pricingRule->update($data);
        });

        return $this->sendResponse([
            'pricing_rule' => $pricingRule->fresh()->load(['createdBy', 'updatedBy']),
        ], 'Pricing rule updated successfully.');
    }

    public function activate(Request $request, PricingRule $pricingRule): JsonResponse
    {
        $admin = $request->user()->load('role');

        if (!$admin->isAdmin()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only admin can activate pricing rules.'],
            ], 403);
        }

        DB::transaction(function () use ($pricingRule, $admin) {
            PricingRule::where('id', '!=', $pricingRule->id)
                ->where('vehicle_type', $pricingRule->vehicle_type)
                ->where('is_active', true)
                ->update(['is_active' => false]);

            $pricingRule->update([
                'is_active' => true,
                'updated_by' => $admin->id,
            ]);
        });

        return $this->sendResponse([
            'pricing_rule' => $pricingRule->fresh()->load(['createdBy', 'updatedBy']),
        ], 'Pricing rule activated successfully.');
    }

    public function destroy(Request $request, PricingRule $pricingRule): JsonResponse
    {
        $admin = $request->user()->load('role');

        if (!$admin->isAdmin()) {
            return $this->sendError('Permission Error.', [
                'role' => ['Only admin can delete pricing rules.'],
            ], 403);
        }

        $pricingRule->delete();

        return $this->sendResponse([], 'Pricing rule deleted successfully.');
    }
}