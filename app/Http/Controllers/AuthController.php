<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helpers;
use Carbon\Carbon;
use Laravel\Passport\Passport;
use Illuminate\Validation\Rule;



class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);
            return response()->json([
                'message' => 'User registered successfully',
                'status' => 200
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function login(Request $request)
    {
        
        try {
            // Validate the request data
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }
            $user = User::where('email', $request->email)->where('role', 'employee')->first();
            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }
            // Generate the token
            $token = $user->createToken('authToken')->plainTextToken;
            $expiry = now()->addHours(1)->toDateTimeString(); 

            return response()->json([
                'token' => $token,
                'token_expiry' => $expiry,
                'user' => $user->name,
                'user_role' => $user->role,
                'status'=>200
            ], 200);

        } catch (ValidationException $e) {
            // Handle validation exceptions
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
    public function update(Request $request)
    {
        try {          
            $validator = Validator::make($request->all(), [
                'id' => 'required|integer|exists:users,id',
                'name' => 'sometimes|string|max:255',
                'email' => ['sometimes','string','email','max:255', Rule::unique('users')->ignore($request->id)],
                'password' => 'sometimes|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }
            $user = User::findOrFail($request->id);
            if ($user->role === 'admin') {
                return response()->json([
                    'message' => 'Admin users cannot be updated.',
                    'status' => 403
                ], 403);
            }
            // Update user details
            if ($request->has('name')) {
                $user->name = $request->name;
            }

            if ($request->has('email')) {
                $user->email = $request->email;
            }

            if ($request->has('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            // Return success response
            return response()->json([
                'message' => 'User updated successfully',
                'status' => 200
            ], 200);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function checkProfileDetails(Request $request)
    {
        try {
             $user = auth()->user();
            if (!$user) {
                return response()->json([
                    'message' => 'User not found',
                    'status' => 404
                ], 404);
            }
            return response()->json([
                'status' => 200,
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->role,
                    'created_at' => $user->created_at->format('Y-m-d H:i:s'),
                    'updated_at' => $user->updated_at->format('Y-m-d H:i:s'),
                ]
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'An unexpected error occurred.',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully','user-role'=>'Employee']);
    }
}
