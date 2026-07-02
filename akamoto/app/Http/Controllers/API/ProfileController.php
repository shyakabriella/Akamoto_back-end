<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends BaseController
{
    /**
     * Show logged-in user profile.
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user()->load(['role', 'profile']);

        return $this->sendResponse([
            'user' => [
                'id' => $user->id,
                'role' => $user->role?->name,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile' => $user->profile ? [
                    'id' => $user->profile->id,
                    'image' => $user->profile->image,
                    'image_url' => $user->profile->image
                        ? asset('storage/' . $user->profile->image)
                        : null,
                    'location_address' => $user->profile->location_address,
                    'street_code' => $user->profile->street_code,
                    'created_at' => $user->profile->created_at,
                    'updated_at' => $user->profile->updated_at,
                ] : null,
            ],
        ], 'Profile fetched successfully.');
    }

    /**
     * Create or update logged-in user profile.
     *
     * User can update:
     * - image
     * - location_address
     * - street_code
     */
    public function update(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'location_address' => 'nullable|string|max:255',
            'street_code' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $user = $request->user();

        $profile = Profile::firstOrNew([
            'user_id' => $user->id,
        ]);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($profile->image && Storage::disk('public')->exists($profile->image)) {
                Storage::disk('public')->delete($profile->image);
            }

            $profile->image = $request->file('image')->store('profiles', 'public');
        }

        if ($request->has('location_address')) {
            $profile->location_address = $request->location_address;
        }

        if ($request->has('street_code')) {
            $profile->street_code = $request->street_code;
        }

        $profile->save();

        $user->load(['role', 'profile']);

        return $this->sendResponse([
            'user' => [
                'id' => $user->id,
                'role' => $user->role?->name,
                'name' => $user->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
                'profile' => [
                    'id' => $profile->id,
                    'image' => $profile->image,
                    'image_url' => $profile->image
                        ? asset('storage/' . $profile->image)
                        : null,
                    'location_address' => $profile->location_address,
                    'street_code' => $profile->street_code,
                    'created_at' => $profile->created_at,
                    'updated_at' => $profile->updated_at,
                ],
            ],
        ], 'Profile updated successfully.');
    }

    /**
     * Delete logged-in user profile image only.
     */
    public function deleteImage(Request $request): JsonResponse
    {
        $user = $request->user();

        $profile = $user->profile;

        if (!$profile || !$profile->image) {
            return $this->sendError('Image Error.', [
                'image' => ['No profile image found.'],
            ], 404);
        }

        if (Storage::disk('public')->exists($profile->image)) {
            Storage::disk('public')->delete($profile->image);
        }

        $profile->image = null;
        $profile->save();

        return $this->sendResponse([
            'profile' => [
                'id' => $profile->id,
                'image' => null,
                'image_url' => null,
                'location_address' => $profile->location_address,
                'street_code' => $profile->street_code,
            ],
        ], 'Profile image deleted successfully.');
    }
}