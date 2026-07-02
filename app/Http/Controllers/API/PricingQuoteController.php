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
     * Calculate delivery price using active pricing rule.
     */
    public function calculate(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'distance_km' => 'required|numeric|min:0.01',
            'vehicle_type' => 'nullable|string|in:moto,bicycle,car,van',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $pricingRule = PricingRule::findActiveForVehicle($request->vehicle_type);

        if (!$pricingRule) {
            return $this->sendError('Pricing Error.', [
                'pricing_rule' => ['No active pricing rule found. Please contact admin.'],
            ], 404);
        }

        $calculation = $pricingRule->calculatePrice((float) $request->distance_km);

        return $this->sendResponse([
            'pricing_rule' => [
                'id' => $pricingRule->id,
                'name' => $pricingRule->name,
                'vehicle_type' => $pricingRule->vehicle_type,
                'currency' => $pricingRule->currency,
            ],
            'calculation' => $calculation,
        ], 'Delivery price calculated successfully.');
    }
}