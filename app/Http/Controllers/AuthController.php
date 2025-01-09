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
     * @OA\Post(
     *     path="/api/register",
     *     summary="Register a new user",
     *     description="Registers a new user and returns an access token along with the user's name.",
     *     operationId="registerUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"name", "email", "password", "password_confirmation"},
     *             @OA\Property(property="name", type="string", example="John Doe", description="The name of the user"),
     *             @OA\Property(property="email", type="string", example="johndoe@example.com", description="The email address of the user"),
     *             @OA\Property(property="password", type="string", format="password", example="password123", description="The password for the user"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="password123", description="The password confirmation")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="name", type="string", example="John Doe", description="The name of the registered user"),
     *             @OA\Property(property="access_token", type="string", example="token1234abcd5678", description="The access token for the user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation Error"),
     *             @OA\Property(property="errors", type="object", description="Validation error details")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/login",
     *     summary="Login a user",
     *     description="Logs in a user and returns an access token if credentials are valid.",
     *     operationId="loginUser",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="johndoe@example.com", description="The email address of the user"),
     *             @OA\Property(property="password", type="string", format="password", example="password123", description="The password for the user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successfully logged in",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="access_token", type="string", example="token1234abcd5678", description="The access token for the user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="message", type="string", example="Invalid credentials", description="Error message")
     *             )
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/logout",
     *     summary="Logout the authenticated user",
     *     description="Logs out the currently authenticated user and returns their details.",
     *     operationId="logoutUser",
     *     tags={"Authentication"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="User successfully logged out",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1, description="The ID of the user"),
     *                 @OA\Property(property="name", type="string", example="John Doe", description="The name of the user"),
     *                 @OA\Property(property="email", type="string", example="johndoe@example.com", description="The email address of the user")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Unauthenticated", description="Error message indicating the user is not logged in")
     *         )
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/reset-password",
     *     summary="Reset user password",
     *     description="Allows a user to reset their password using their email address and a new password.",
     *     operationId="resetPassword",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", example="johndoe@example.com", description="The email address of the user"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123", description="The new password for the user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successful",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true, description="Indicates that the password reset was successful")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Validation Error", description="Error message"),
     *             @OA\Property(property="errors", type="object", description="Details about the validation errors")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User not found", description="Error message indicating the user was not found")
     *         )
     *     )
     * )
     */
    public function resetPassword(ResetPasswordRequest $request)
    {
        $this->authService->resetPassword(...$request->only(['email', 'password']));

        return response()->json([
            'success' => true
        ], Response::HTTP_OK);
    }
}
