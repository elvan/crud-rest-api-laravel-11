<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     *
     * @return \Illuminate\Http\JsonResponse
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
