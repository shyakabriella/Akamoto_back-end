<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends BaseController
{
    /**
     * Register API
     *
     * Customer will register using:
     * - username
     * - email
     * - phone
     * - password
     * - c_password
     */
    public function register(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|string|email|max:255|unique:users,email',
            'phone' => 'required|string|max:30|unique:users,phone',
            'password' => 'required|string|min:6',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        /*
         * Public registration creates customer account by default.
         * Admin is created using UserSeeder.
         * Rider registration can be added later with rider approval system.
         */
        $customerRole = Role::where('name', User::ROLE_CUSTOMER)->first();

        if (!$customerRole) {
            return $this->sendError('Role Error.', [
                'role' => ['Customer role not found. Please run RoleSeeder first.']
            ], 500);
        }

        $user = User::create([
            'role_id' => $customerRole->id,
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        $user->load('role');

        $success = [
            'token' => $user->createToken('AkamotoApp')->plainTextToken,
            'user' => [
                'id' => $user->id,
                'role' => $user->role?->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ];

        return $this->sendResponse($success, 'User registered successfully.');
    }

    /**
     * Login API
     *
     * User can login using:
     * - email
     * - phone
     * - username
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $login = $request->login;

        $user = User::where('email', $login)
            ->orWhere('phone', $login)
            ->orWhere('username', $login)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->sendError('Unauthorised.', [
                'error' => 'Invalid login details.'
            ], 401);
        }

        Auth::login($user);

        $user->load('role');

        $success = [
            'token' => $user->createToken('AkamotoApp')->plainTextToken,
            'user' => [
                'id' => $user->id,
                'role' => $user->role?->name,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
        ];

        return $this->sendResponse($success, 'User logged in successfully.');
    }
}