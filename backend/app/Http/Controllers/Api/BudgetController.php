<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreBudgetRequest;
use App\Models\Budget;
use App\Services\BudgetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BudgetController extends Controller
{
    public function __construct(
        protected BudgetService $budgetService
    ) {}

    /**
     * Display a listing of budgets for the specified month & year with real-time calculated spending.
     */
    public function index(Request $request): JsonResponse
    {
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $budgets = $this->budgetService->getBudgetsForPeriod($request->user()->id, $year, $month);
        $summary = $this->budgetService->getBudgetSummary($request->user()->id, $year, $month);

        return response()->json([
            'data'    => $budgets,
            'summary' => $summary,
        ]);
    }

    /**
     * Get summary metrics for budgets.
     */
    public function summary(Request $request): JsonResponse
    {
        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $summary = $this->budgetService->getBudgetSummary($request->user()->id, $year, $month);

        return response()->json(['summary' => $summary]);
    }

    /**
     * Calculate budget impact for a potential new transaction (Interconnected UX).
     */
    public function impact(Request $request): JsonResponse
    {
        $userId = $request->user()->id;

        $request->validate([
            'category_id' => [
                'required',
                'integer',
                \Illuminate\Validation\Rule::exists('categories', 'id')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)->orWhereNull('user_id');
                }),
            ],
            'amount'      => 'required|numeric|min:0.01',
            'month'       => 'nullable|integer|between:1,12',
            'year'        => 'nullable|integer',
        ]);

        $year  = (int) $request->get('year', now()->year);
        $month = (int) $request->get('month', now()->month);

        $impact = $this->budgetService->getBudgetImpact(
            $request->user()->id,
            (int) $request->category_id,
            (float) $request->amount,
            $year,
            $month
        );

        return response()->json(['impact' => $impact]);
    }

    /**
     * Store a newly created budget or update existing if already set for this period.
     */
    public function store(StoreBudgetRequest $request): JsonResponse
    {
        $userId = $request->user()->id;
        $data   = $request->validated();

        // Upsert logic: if user already has a budget for this category & period, update it
        $budget = Budget::where('user_id', $userId)
            ->where('category_id', $data['category_id'])
            ->where('year', $data['year'])
            ->where('month', $data['month'])
            ->first();

        if ($budget) {
            $budget->update(['amount' => $data['amount']]);
            $budget->load('category');
        } else {
            $budget = $this->budgetService->create($data, $userId);
        }

        return response()->json([
            'message' => 'Budget berhasil disimpan.',
            'data'    => $budget,
        ], 201);
    }

    /**
     * Update the specified budget amount.
     */
    public function update(Request $request, Budget $budget): JsonResponse
    {
        abort_if($budget->user_id !== $request->user()->id, 403, 'Unauthorized');

        $validated = $request->validate([
            'amount' => 'required|numeric|min:1000',
        ]);

        $budget = $this->budgetService->update($budget, $validated);

        return response()->json([
            'message' => 'Budget berhasil diperbarui.',
            'data'    => $budget,
        ]);
    }

    /**
     * Remove the specified budget.
     */
    public function destroy(Request $request, Budget $budget): JsonResponse
    {
        abort_if($budget->user_id !== $request->user()->id, 403, 'Unauthorized');

        $this->budgetService->delete($budget);

        return response()->json(['message' => 'Budget berhasil dihapus.']);
    }
}
