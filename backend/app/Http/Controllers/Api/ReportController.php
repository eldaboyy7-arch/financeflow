<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function monthly(Request $request): JsonResponse
    {
        $request->validate([
            'year'  => 'required|integer|min:2000|max:2100',
            'month' => 'required|integer|min:1|max:12',
        ]);

        return response()->json(
            $this->reportService->getMonthlyReport(
                $request->user()->id,
                (int) $request->year,
                (int) $request->month
            )
        );
    }
}
