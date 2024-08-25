<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helpers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Laravel\Passport\Passport;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard'); 
    }
    public function Login(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'password' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $user = User::where('email', $request->email)->where('role', 'admin')->first();

            if (!$user || !Hash::check($request->password, $user->password)) {
                throw ValidationException::withMessages([
                    'email' => ['The provided credentials are incorrect.'],
                ]);
            }

            $tokenName = $user->role . 'Token';
            $expiryTime = $user->role === 'admin' ? now()->addHours(2) : now()->addHours(1);
    
            // Generate the token
            $token = $user->createToken($tokenName)->plainTextToken;

            return response()->json([
                'token' => $token,
                'token_expiry' => $expiryTime->toDateTimeString(),
                'user' => $user->email,
                'user_role' => $user->role,
                'status' => 200
            ], 200);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
    
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully','user-role'=>'Admin']);
    }
}
