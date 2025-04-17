<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Authentication",
 *     description="Authentication endpoints"
 * )
 */
class AuthController extends Controller
{
    /**
     * Handle user login
     *
     * @param \App\Http\Requests\LoginRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Post(
     *     path="/login",
     *     summary="User login",
     *     description="Authenticate user and get access token",
     *     tags={"Authentication"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email", "password"},
     *             @OA\Property(property="email", type="string", format="email", example="admin@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful login",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="token", type="string", example="1|laravel_token_2023_example"),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Admin"),
     *                     @OA\Property(property="email", type="string", example="admin@example.com"),
     *                     @OA\Property(property="role", type="string", example="admin")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The provided credentials are incorrect.")
     *         )
     *     )
     * )
     */
    public function login(\App\Http\Requests\LoginRequest $request)
    {
        // Validate credentials
        $credentials = $request->only('email', 'password');
        
        if (!auth()->attempt($credentials)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid credentials'
            ], 401);
        }
        
        // Get authenticated user
        $user = auth()->user();
        
        // Check if user is active
        if (!$user->is_active) {
            auth()->logout();
            return response()->json([
                'status' => 'error',
                'message' => 'Account is inactive. Please contact an administrator.'
            ], 401);
        }
        
        // Create token
        $token = $user->createToken('auth-token')->plainTextToken;
        
        return response()->json([
            'status' => 'success',
            'message' => 'Login successful',
            'data' => [
                'token' => $token,
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                ]
            ]
        ], 200);
    }
    
    /**
     * Get authenticated user profile
     *
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Get(
     *     path="/me",
     *     summary="Get current user profile",
     *     description="Get the profile of the currently authenticated user",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="name", type="string", example="Admin"),
     *                     @OA\Property(property="email", type="string", example="admin@example.com"),
     *                     @OA\Property(property="role", type="string", example="admin"),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function me()
    {
        $user = auth()->user();
        
        return response()->json([
            'status' => 'success',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]
            ]
        ], 200);
    }
    
    /**
     * Logout user
     *
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Post(
     *     path="/logout",
     *     summary="Logout user",
     *     description="Logout the authenticated user and revoke tokens",
     *     tags={"Authentication"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful logout",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="Logged out successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function logout()
    {
        // Revoke all tokens
        auth()->user()->tokens()->delete();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Logged out successfully'
        ], 200);
    }
}
