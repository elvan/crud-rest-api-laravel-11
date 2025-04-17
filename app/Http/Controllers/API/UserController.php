<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

/**
 * @OA\Tag(
 *     name="Users",
 *     description="User management endpoints"
 * )
 */
class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Get(
     *     path="/users",
     *     summary="Get all users",
     *     description="Get a list of all users",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="count", type="integer", example=2),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="users",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer", example=1),
     *                         @OA\Property(property="name", type="string", example="Admin"),
     *                         @OA\Property(property="email", type="string", example="admin@example.com"),
     *                         @OA\Property(property="role", type="string", example="admin"),
     *                         @OA\Property(property="is_active", type="boolean", example=true),
     *                         @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
     *                     )
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
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized. Admin role required.")
     *         )
     *     )
     * )
     */
    public function index()
    {
        $users = \App\Models\User::select(['id', 'name', 'email', 'role', 'is_active', 'created_at', 'updated_at'])
            ->orderBy('id', 'desc')
            ->get();

        return response()->json([
            'status' => 'success',
            'count' => $users->count(),
            'data' => ['users' => $users]
        ], 200);
    }

    /**
     * Store a newly created user in the database.
     *
     * @param \App\Http\Requests\StoreUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Post(
     *     path="/users",
     *     summary="Create a new user",
     *     description="Create a new user with the provided data",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "password"},
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"admin", "user"}, example="user"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User created successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="John Doe"),
     *                     @OA\Property(property="email", type="string", example="john@example.com"),
     *                     @OA\Property(property="role", type="string", example="user"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
     *                     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00.000000Z")
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
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized. Admin role required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The email field must be a valid email address.")
     *         )
     *     )
     * )
     */
    public function store(\App\Http\Requests\StoreUserRequest $request)
    {
        $user = \App\Models\User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => $request->role ?? 'user',
            'is_active' => $request->is_active ?? true,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'User created successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'is_active' => $user->is_active,
                    'created_at' => $user->created_at,
                ]
            ]
        ], 201);
    }

    /**
     * Display the specified user.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Get(
     *     path="/users/{id}",
     *     summary="Get a specific user",
     *     description="Get details of a specific user by ID",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
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
     *                     @OA\Property(property="is_active", type="boolean", example=true),
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
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized. Admin role required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function show($id)
    {
        $user = \App\Models\User::select(['id', 'name', 'email', 'role', 'is_active', 'created_at', 'updated_at'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => ['user' => $user]
        ], 200);
    }

    /**
     * Update the specified user in the database.
     *
     * @param \App\Http\Requests\UpdateUserRequest $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Put(
     *     path="/users/{id}",
     *     summary="Update a specific user",
     *     description="Update details of a specific user by ID",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe Updated"),
     *             @OA\Property(property="email", type="string", format="email", example="john.updated@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="role", type="string", enum={"admin", "user"}, example="admin"),
     *             @OA\Property(property="is_active", type="boolean", example=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User updated successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=2),
     *                     @OA\Property(property="name", type="string", example="John Doe Updated"),
     *                     @OA\Property(property="email", type="string", example="john.updated@example.com"),
     *                     @OA\Property(property="role", type="string", example="admin"),
     *                     @OA\Property(property="is_active", type="boolean", example=true),
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
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized. Admin role required.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="The email field must be a valid email address.")
     *         )
     *     )
     * )
     */
    public function update(\App\Http\Requests\UpdateUserRequest $request, $id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        // Update fields that are present in the request
        if ($request->has('name')) $user->name = $request->name;
        if ($request->has('email')) $user->email = $request->email;
        if ($request->has('password')) $user->password = $request->password;
        if ($request->has('role')) $user->role = $request->role;
        if ($request->has('is_active')) $user->is_active = $request->is_active;
        
        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'User updated successfully',
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'is_active' => $user->is_active,
                    'updated_at' => $user->updated_at,
                ]
            ]
        ], 200);
    }

    /**
     * Remove the specified user from the database.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     * 
     * @OA\Delete(
     *     path="/users/{id}",
     *     summary="Delete a specific user",
     *     description="Delete a specific user by ID",
     *     tags={"Users"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="success"),
     *             @OA\Property(property="message", type="string", example="User deleted successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="Unauthorized. Admin role required."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="string", example="error"),
     *             @OA\Property(property="message", type="string", example="User not found")
     *         )
     *     )
     * )
     */
    public function destroy($id)
    {
        $user = \App\Models\User::findOrFail($id);
        
        // Prevent deleting the authenticated user
        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'You cannot delete your own account'
            ], 403);
        }
        
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'User deleted successfully'
        ], 200);
    }
}
