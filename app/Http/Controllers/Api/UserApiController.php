<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class UserApiController extends Controller
{
    // GET /api/users
    public function index()
    {
        try {
            $users = User::with(['detailStudent', 'detailSupervisor', 'role'])->get();

            return response()->json([
                'status' => 'success',
                'message' => 'Users retrieved successfully',
                'data' => $users
            ], 200);

        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    // GET /api/users/{id}
    public function show($id)
    {
        try {
            $user = User::with(['detailStudent', 'detailSupervisor', 'role'])->findOrFail($id);

            return response()->json([
                'status' => 'success',
                'message' => 'User retrieved successfully',
                'data' => $user
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);

        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    // POST /api/users
    public function store(Request $request)
    {
        try {
            $request->validate([
                'role_id' => 'required|integer',
                'user_name' => 'required|string|max:255',
                'user_username' => 'required|string|max:255|unique:users,user_username',
                'user_password' => 'required|min:8'
            ]);

            $user = User::create([
                'role_id' => $request->role_id,
                'user_name' => $request->user_name,
                'user_username' => $request->user_username,
                'user_password' => Hash::make($request->user_password),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => $user
            ], 201);

        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    // PUT /api/users/{id}
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'user_username' => "unique:users,user_username,{$id},user_id",
            ]);

            $user->update([
                'user_name' => $request->user_name ?? $user->user_name,
                'user_username' => $request->user_username ?? $user->user_username,
                'user_password' => $request->user_password 
                    ? Hash::make($request->user_password) 
                    : $user->user_password,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'data' => $user
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);

        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }

    // DELETE /api/users/{id}
    public function destroy($id)
    {
        try {
            $user = User::findOrFail($id);
            $user->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'User deleted successfully'
            ], 200);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'User not found'
            ], 404);

        } catch (Exception $e) {
            return $this->errorResponse($e);
        }
    }


    /**
     * Helper function: Error Response
     */
    private function errorResponse($e)
    {
        return response()->json([
            'status' => 'error',
            'message' => 'Internal Server Error',
            'error' => $e->getMessage()
        ], 500);
    }
}
