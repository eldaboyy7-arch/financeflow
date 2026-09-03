<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TransactionResource;
use App\Services\DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct(private DashboardService $dashboardService) {}

    public function index(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        return response()->json([
            'summary'             => $this->dashboardService->getSummaryCards($userId),
            'recent_transactions' => $this->dashboardService->getRecentTransactions($userId),
        ]);
    }

    public function incomeExpenseChart(Request $request): JsonResponse
    {
        $period = in_array($request->get('period'), ['daily', 'monthly']) ? $request->get('period') : 'monthly';

        return response()->json(
            $this->dashboardService->getIncomeExpenseChart($request->user()->id, $period)
        );
    }

    public function expenseBreakdown(Request $request): JsonResponse
    {
        return response()->json(
            $this->dashboardService->getExpenseBreakdown($request->user()->id)
        );
    }
}
