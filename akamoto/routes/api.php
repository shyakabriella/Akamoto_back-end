<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\RegisterController;
use App\Http\Controllers\API\ProductController;

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
| These routes do not need login token.
| Used for creating account and login.
*/

Route::controller(RegisterController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
});

/*
|--------------------------------------------------------------------------
| Protected API Routes
|--------------------------------------------------------------------------
| These routes need Sanctum token.
| In Postman, add:
| Authorization: Bearer YOUR_TOKEN_HERE
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Logged-in User Profile
    |--------------------------------------------------------------------------
    | Used to check current logged-in user.
    */
    Route::get('/me', function (Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Logged-in user fetched successfully.',
            'data' => [
                'user' => $request->user()->load('role'),
            ],
        ]);
    });

    /*
    |--------------------------------------------------------------------------
    | Logout
    |--------------------------------------------------------------------------
    | Deletes only the current user token.
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
    | Products API
    |--------------------------------------------------------------------------
    | This is only for testing now.
    | Later in Akamoto, we shall replace products with:
    | orders, riders, pricing, commissions, payments, etc.
    */
    Route::apiResource('products', ProductController::class);
});