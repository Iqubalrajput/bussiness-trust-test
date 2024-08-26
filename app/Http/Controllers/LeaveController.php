<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Http\Resources\LeaveResource;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helpers;
use Illuminate\Validation\ValidationException;

class LeaveController extends Controller
{
    public function getEmployeeLeaves($user_id)
    {
        try {
            $leaves = Leave::where('user_id', $user_id)->get();

            if ($leaves->isEmpty()) {
                return response()->json([
                    'message' => 'No leaves found for this employee',
                    'status' => 404
                ], 404);
            }

            return LeaveResource::collection($leaves)->additional([
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'leave_date' => 'required|date',
                'leave_type' => 'required|string',
                'reason' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $leave = Leave::create([
                'user_id' => auth()->id(),
                'leave_date' => $request->leave_date,
                'leave_type' => $request->leave_type,
                'reason' => $request->reason,
            ]);

            return new LeaveResource($leave);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'leave_date' => 'sometimes|required|date',
                'leave_type' => 'sometimes|required|string',
                'reason' => 'sometimes|nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $leave = Leave::findOrFail($id);
            $leave->update($request->all());

            return new LeaveResource($leave);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $leave = Leave::findOrFail($id);
            $leave->delete();

            return response()->json([
                'message' => 'Leave deleted successfully',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function showProfile()
    {
        try {
            $leaves = Leave::where('user_id', auth()->id())->get();

            return LeaveResource::collection($leaves)->additional([
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}
