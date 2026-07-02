<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProfileController;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

Route::controller(RegisterController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

/*
|--------------------------------------------------------------------------
| Protected API Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    Route::get('/me', function (Request $request) {
        $user = $request->user()->load(['role', 'profile']);

        return response()->json([
            'success' => true,
            'message' => 'Logged-in user fetched successfully.',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'role' => $user->role?->name,
                    'name' => $user->name,
                    'username' => $user->username,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'profile' => $user->profile,
                ],
            ],
        ]);
    });

    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User logged out successfully.',
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Profile Routes
    |--------------------------------------------------------------------------
    */

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile/image', [ProfileController::class, 'deleteImage']);
});