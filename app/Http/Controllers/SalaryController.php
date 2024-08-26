<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Salary;
use App\Http\Resources\SalaryResource;
use Illuminate\Support\Facades\Validator;
use Barryvdh\DomPDF\Facade as PDF;

class SalaryController extends Controller
{
    public function index()
    {
        try {
            $salaries = Salary::where('user_id', auth()->id())->get();

            if ($salaries->isEmpty()) {
                return response()->json([
                    'message' => 'No salary records found',
                    'status' => 404
                ], 404);
            }

            return SalaryResource::collection($salaries)->additional([
                'status' => 200
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }

    public function generatePDF(Request $request)
    {
        try {
            $salaries = Salary::where('user_id', auth()->id())->get();

            if ($salaries->isEmpty()) {
                return response()->json([
                    'message' => 'No salary records found for PDF generation',
                    'status' => 404
                ], 404);
            }

            $pdf = PDF::loadView('salary_pdf', ['salaries' => $salaries]);
            return $pdf->download('salary_report.pdf');

        } catch (\Exception $e) {
            return response()->json(['error' => 'An unexpected error occurred.'], 500);
        }
    }
}
