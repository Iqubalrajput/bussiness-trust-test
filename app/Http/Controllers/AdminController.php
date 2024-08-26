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
use App\Models\Leave;
use App\Models\AdvanceSalary;
use App\Models\Salary;

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
    public function addEmployee(Request $request)
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

            $employee = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            return response()->json([
                'message' => 'Employee added successfully',
                'status' => 201,
                'data' => $employee
            ], 201);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    // Update an existing employee
    public function updateEmployee(Request $request, $id)
    {
        try {
            
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:users,id', 
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 404);
            }
            $validator = Validator::make($request->all(), [
                'name' => 'sometimes|required|string|max:255',
                'email' => ['sometimes','required','string','email','max:255', Rule::unique('users')->ignore($id)],
                'password' => 'sometimes|required|string|min:6',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $employee = User::findOrFail($id);
            $employee->update($request->only(['name', 'email', 'password']));

            if ($request->filled('password')) {
                $employee->password = Hash::make($request->password);
                $employee->save();
            }

            return response()->json([
                'message' => 'Employee updated successfully',
                'status' => 200,
                'data' => $employee
            ], 200);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function deleteEmployee($id)
    {
        try {
            $employee = User::findOrFail($id);
            $employee->delete();

            return response()->json([
                'message' => 'Employee deleted successfully',
                'status' => 200
            ], 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function manageLeave(Request $request, $id)
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:leaves,id',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 404);
            }
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:approved,rejected',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $leave = Leave::findOrFail($id);
            $leave->status = $request->status;
            $leave->save();

            return response()->json([
                'message' => 'Leave status updated successfully',
                'status' => 200,
                'data' => $leave
            ], 200);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function manageAdvanceSalary(Request $request, $id)
    {
        try {
            $validator = Validator::make(['id' => $id], [
                'id' => 'required|exists:advance_salaries,id',
            ]);
    
            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 404);
            }
            $validator = Validator::make($request->all(), [
                'status' => 'required|in:approved,rejected',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }
            $advanceSalary = AdvanceSalary::findOrFail($id);
            if (!$advanceSalary) {
                return response()->json([
                    'message' => 'No advance salary record found',
                    'status' => 404,
                    'data' => []
                ], 404);
            }
            $advanceSalary->status = $request->status;
            $advanceSalary->save();

            return response()->json([
                'message' => 'Advance salary status updated successfully',
                'status' => 200,
                'data' => $advanceSalary
            ], 200);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function generateMonthlySalary()
    {
        try {
            $employees = User::all();
            foreach ($employees as $employee) {
                $salary = new Salary();
                $salary->user_id = $employee->id;
                $salary->amount = $this->calculateMonthlySalary($employee);
                $salary->save();
            }

            return response()->json([
                'message' => 'Monthly salaries generated successfully',
                'status' => 200
            ], 200);

        } catch (Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    private function calculateMonthlySalary(User $employee)
    {
        // Implement your salary calculation logic here
        return 1000; // Placeholder amount
    }
}
