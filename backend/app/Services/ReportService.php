<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{
    public function getMonthlyReport(int $userId, int $year, int $month): array
    {
        $totalIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')->whereNull('transfer_id')
            ->whereMonth('date', $month)->whereYear('date', $year)
            ->sum('amount');

        $totalExpense = Transaction::where('user_id', $userId)
            ->where('type', 'expense')->whereNull('transfer_id')
            ->whereMonth('date', $month)->whereYear('date', $year)
            ->sum('amount');

        $netCashFlow = $totalIncome - $totalExpense;
        $savingRate  = $totalIncome > 0 ? ($netCashFlow / $totalIncome) * 100 : 0;

        $incomeBreakdown = Transaction::with('category')
            ->where('user_id', $userId)->where('type', 'income')->whereNull('transfer_id')
            ->whereMonth('date', $month)->whereYear('date', $year)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')->orderByDesc('total')->get()
            ->map(fn ($t) => [
                'category' => $t->category?->name,
                'icon'     => $t->category?->icon,
                'color'    => $t->category?->color,
                'total'    => (float) $t->total,
            ]);

        $expenseBreakdown = Transaction::with('category')
            ->where('user_id', $userId)->where('type', 'expense')->whereNull('transfer_id')
            ->whereMonth('date', $month)->whereYear('date', $year)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')->orderByDesc('total')->get()
            ->map(fn ($t) => [
                'category' => $t->category?->name,
                'icon'     => $t->category?->icon,
                'color'    => $t->category?->color,
                'total'    => (float) $t->total,
            ]);

        $accountBalances = Account::where('user_id', $userId)->where('is_active', true)->get()
            ->map(fn ($a) => [
                'name'    => $a->name,
                'type'    => $a->type?->value,
                'balance' => (float) $a->current_balance,
                'icon'    => $a->icon,
                'color'   => $a->color,
            ]);

        // Previous month comparison
        $prevDate    = Carbon::createFromDate($year, $month, 1)->subMonth();
        $prevIncome  = Transaction::where('user_id', $userId)->where('type', 'income')->whereNull('transfer_id')
            ->whereMonth('date', $prevDate->month)->whereYear('date', $prevDate->year)->sum('amount');
        $prevExpense = Transaction::where('user_id', $userId)->where('type', 'expense')->whereNull('transfer_id')
            ->whereMonth('date', $prevDate->month)->whereYear('date', $prevDate->year)->sum('amount');

        return [
            'year'             => $year,
            'month'            => $month,
            'total_income'     => (float) $totalIncome,
            'total_expense'    => (float) $totalExpense,
            'net_cash_flow'    => (float) $netCashFlow,
            'saving_rate'      => round($savingRate, 2),
            'income_breakdown' => $incomeBreakdown->values(),
            'expense_breakdown' => $expenseBreakdown->values(),
            'account_balances' => $accountBalances->values(),
            'comparison'       => [
                'prev_income'     => (float) $prevIncome,
                'prev_expense'    => (float) $prevExpense,
                'income_change'   => $prevIncome > 0 ? round((($totalIncome - $prevIncome) / $prevIncome) * 100, 2) : 0,
                'expense_change'  => $prevExpense > 0 ? round((($totalExpense - $prevExpense) / $prevExpense) * 100, 2) : 0,
            ],
        ];
    }
}
