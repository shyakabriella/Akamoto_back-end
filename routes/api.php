<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProfileController;
use App\Http\Controllers\API\RiderController;
use App\Http\Controllers\API\Admin\RiderManagementController;

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

    /*
    |--------------------------------------------------------------------------
    | Logged-in User
    |--------------------------------------------------------------------------
    */
    Route::get('/me', function (Request $request) {
        $user = $request->user()->load(['role', 'profile', 'rider']);

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
                    'rider' => $user->rider,
                ],
            ],
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    */
    Route::post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'User logged out successfully.',
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Customer/User Profile
    |--------------------------------------------------------------------------
    */
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::post('/profile', [ProfileController::class, 'update']);
    Route::delete('/profile/image', [ProfileController::class, 'deleteImage']);

    /*
    |--------------------------------------------------------------------------
    | Rider Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('rider')->group(function () {
        Route::get('/profile', [RiderController::class, 'show']);
        Route::post('/profile', [RiderController::class, 'storeOrUpdate']);
        Route::post('/go-online', [RiderController::class, 'goOnline']);
        Route::post('/go-offline', [RiderController::class, 'goOffline']);
        Route::post('/location', [RiderController::class, 'updateLocation']);
    });

    /*
    |--------------------------------------------------------------------------
    | Admin Rider Management Routes
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->group(function () {
        Route::get('/riders', [RiderManagementController::class, 'index']);
        Route::get('/riders/{rider}', [RiderManagementController::class, 'show']);
        Route::post('/riders/{rider}/approve', [RiderManagementController::class, 'approve']);
        Route::post('/riders/{rider}/reject', [RiderManagementController::class, 'reject']);
        Route::post('/riders/{rider}/suspend', [RiderManagementController::class, 'suspend']);
    });
});