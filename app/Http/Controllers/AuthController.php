<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Register a new user
     *
     * @param RegistrationRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegistrationRequest $request)
    {
        $plainTextToken = $this->authService->register(...$request->only(['name', 'email', 'password']));

        return response()->json([
            'name' => $request->get('name'),
            'access_token' => $plainTextToken,
        ], Response::HTTP_CREATED);
    }

    /**
     * Login an existing user
     *
     * @param LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(LoginRequest $request)
    {
        $plainTextToken = $this->authService->login(...$request->only(['email', 'password']));
        if (!$plainTextToken) {
            return response()->json([
                "errors" => [
                    "message" => "Invalid credentials"
                ]
            ], Response::HTTP_BAD_REQUEST);
        }

        return response()->json([
            'access_token' => $plainTextToken
        ], Response::HTTP_OK);
    }

    /**
     * Log a user out by deleting all their active tokens
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        $user = Auth::user();
        $this->authService->logout($user);

        return response()->json([
            "user" => UserResource::make($user)
        ], Response::HTTP_OK);
    }

    /**
     * Resets a user's password
     *
     * Usually, this flow is more complex i.e. user receives an email with a recovery code/link
     * they can use to verify that they are the ones requesting the password reset and then can proceed resetting it
     * but for the sake of this assessment, we will not be doing that.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword(...$request->only(['email', 'password']));

        return response()->json([
            'success' => true
        ], Response::HTTP_OK);
    }
}
