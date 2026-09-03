<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\InsightService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InsightController extends Controller
{
    public function __construct(
        protected InsightService $insightService
    ) {}

    /**
     * Get 3–4 actionable financial spending insights for the user.
     */
    public function index(Request $request): JsonResponse
    {
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $insights = $this->insightService->getSmartInsights($request->user()->id, $year, $month);

        return response()->json([
            'data'  => $insights,
            'month' => $month,
            'year'  => $year,
        ]);
    }
}
