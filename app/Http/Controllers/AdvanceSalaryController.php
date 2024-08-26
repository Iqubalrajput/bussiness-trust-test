<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdvanceSalary;
use App\Http\Resources\AdvanceSalaryResource;
use Illuminate\Support\Facades\Validator;
use App\Helpers\Helpers;
use Illuminate\Validation\ValidationException;

class AdvanceSalaryController extends Controller
{
    // Store a new advance salary
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'required|numeric',
                'advance_date' => 'required|date',
                'reason' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $advanceSalary = AdvanceSalary::create([
                'user_id' => auth()->id(),
                'amount' => $request->amount,
                'advance_date' => $request->advance_date,
                'reason' => $request->reason,
            ]);

            return new AdvanceSalaryResource($advanceSalary);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    // Update an existing advance salary
    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'amount' => 'sometimes|required|numeric',
                'advance_date' => 'sometimes|required|date',
                'reason' => 'sometimes|nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => Helpers::error_processor($validator)], 403);
            }

            $advanceSalary = AdvanceSalary::findOrFail($id);
            $advanceSalary->update($request->all());

            return new AdvanceSalaryResource($advanceSalary);

        } catch (ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    // Delete an advance salary
    public function destroy($id)
    {
        try {
            $advanceSalary = AdvanceSalary::findOrFail($id);
            $advanceSalary->delete();

            return response()->json([
                'message' => 'Advance salary deleted successfully',
                'status' => 200
            ], 200);

        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}
