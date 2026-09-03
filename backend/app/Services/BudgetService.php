<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class BudgetService
{
    /**
     * Get all budgets for a specific month & year with real-time calculated spending.
     */
    public function getBudgetsForPeriod(int $userId, int $year, int $month): Collection
    {
        $budgets = Budget::with('category')
            ->where('user_id', $userId)
            ->where('year', $year)
            ->where('month', $month)
            ->get();

        // Get spending per category for this user in this month (strictly expense, excluding transfers and rental)
        $expenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereNull('transfer_id')
            ->whereNull('vehicle_id')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->groupBy('category_id')
            ->selectRaw('category_id, SUM(amount) as total_spent')
            ->pluck('total_spent', 'category_id')
            ->toArray();

        return $budgets->map(function (Budget $budget) use ($expenses) {
            $amount = (float) $budget->amount;
            $spent  = (float) ($expenses[$budget->category_id] ?? 0);
            $remaining = max(0, $amount - $spent);
            $overspent = max(0, $spent - $amount);
            $percentage = $amount > 0 ? round(($spent / $amount) * 100, 1) : 0;

            $status = 'normal';
            if ($percentage >= Budget::THRESHOLD_EXCEEDED) {
                $status = 'exceeded';
            } elseif ($percentage >= Budget::THRESHOLD_WARNING) {
                $status = 'warning';
            }

            return [
                'id'               => $budget->id,
                'category_id'      => $budget->category_id,
                'category'         => [
                    'id'    => $budget->category?->id,
                    'name'  => $budget->category?->name ?? 'Kategori Dihapus',
                    'icon'  => $budget->category?->icon ?? '📦',
                    'color' => $budget->category?->color ?? '#6366F1',
                ],
                'amount'           => $amount,
                'spent'            => $spent,
                'remaining'        => $remaining,
                'overspent'        => $overspent,
                'percentage'       => $percentage,
                'status'           => $status,
                'month'            => $budget->month,
                'year'             => $budget->year,
                'created_at'       => $budget->created_at,
            ];
        });
    }

    /**
     * Get overall budget summary metrics for a given period.
     */
    public function getBudgetSummary(int $userId, int $year, int $month): array
    {
        $budgetsWithSpending = $this->getBudgetsForPeriod($userId, $year, $month);

        $totalBudgeted = $budgetsWithSpending->sum('amount');
        $totalSpent    = $budgetsWithSpending->sum('spent');
        $totalRemaining = max(0, $totalBudgeted - $totalSpent);
        $overallPercentage = $totalBudgeted > 0 ? round(($totalSpent / $totalBudgeted) * 100, 1) : 0;

        $warningCount  = $budgetsWithSpending->where('status', 'warning')->count();
        $exceededCount = $budgetsWithSpending->where('status', 'exceeded')->count();

        return [
            'month'              => $month,
            'year'               => $year,
            'total_budgeted'     => (float) $totalBudgeted,
            'total_spent'        => (float) $totalSpent,
            'total_remaining'    => (float) $totalRemaining,
            'overall_percentage' => $overallPercentage,
            'warning_count'      => $warningCount,
            'exceeded_count'     => $exceededCount,
            'budget_count'       => $budgetsWithSpending->count(),
        ];
    }

    /**
     * Calculate budget impact for a potential new transaction (Interconnected UX).
     */
    public function getBudgetImpact(int $userId, int $categoryId, float $newAmount, int $year, int $month): ?array
    {
        $budget = Budget::with('category')
            ->where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('year', $year)
            ->where('month', $month)
            ->first();

        if (!$budget) {
            return null;
        }

        $currentSpent = (float) Transaction::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('type', 'expense')
            ->whereNull('transfer_id')
            ->whereNull('vehicle_id')
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->sum('amount');

        $budgetAmount = (float) $budget->amount;
        $projectedSpent = $currentSpent + $newAmount;
        $projectedPercentage = $budgetAmount > 0 ? round(($projectedSpent / $budgetAmount) * 100, 1) : 0;

        $status = 'normal';
        $message = "Aman dalam batas budget ({$projectedPercentage}% terpakai).";

        if ($projectedPercentage >= Budget::THRESHOLD_EXCEEDED) {
            $status = 'exceeded';
            $over = number_format($projectedSpent - $budgetAmount, 0, ',', '.');
            $message = "🚨 Transaksi ini akan melebihi budget {$budget->category?->name} sebesar Rp{$over} ({$projectedPercentage}% terpakai)!";
        } elseif ($projectedPercentage >= Budget::THRESHOLD_WARNING) {
            $status = 'warning';
            $message = "⚠️ Kamu sudah mendekati batas budget {$budget->category?->name} ({$projectedPercentage}% terpakai).";
        }

        return [
            'category_name'        => $budget->category?->name,
            'budget_amount'        => $budgetAmount,
            'current_spent'        => $currentSpent,
            'projected_spent'      => $projectedSpent,
            'projected_percentage' => $projectedPercentage,
            'status'               => $status,
            'message'              => $message,
        ];
    }

    /**
     * Create a new budget.
     */
    public function create(array $data, int $userId): Budget
    {
        return Budget::create(array_merge($data, ['user_id' => $userId]))
            ->load('category');
    }

    /**
     * Update an existing budget.
     */
    public function update(Budget $budget, array $data): Budget
    {
        $budget->update($data);
        return $budget->fresh()->load('category');
    }

    /**
     * Delete a budget.
     */
    public function delete(Budget $budget): void
    {
        $budget->delete();
    }
}
