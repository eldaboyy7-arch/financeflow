<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function getSummaryCards(int $userId): array
    {
        $totalBalance = Account::where('user_id', $userId)
            ->where('is_active', true)
            ->sum('current_balance');

        $year = now()->year;
        $month = now()->month;

        // Single query for both income and expense of this month (General Finance only)
        $thisMonthSums = Transaction::where('user_id', $userId)
            ->whereNull('transfer_id')
            ->whereNull('vehicle_id')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->select('type', DB::raw('SUM(amount) as total'))
            ->groupBy('type')
            ->pluck('total', 'type');

        $thisMonthIncome  = (float) ($thisMonthSums['income'] ?? 0);
        $thisMonthExpense = (float) ($thisMonthSums['expense'] ?? 0);

        return [
            'total_balance'      => (float) $totalBalance,
            'income_this_month'  => $thisMonthIncome,
            'expense_this_month' => $thisMonthExpense,
            'net_cash_flow'      => (float) ($thisMonthIncome - $thisMonthExpense),
        ];
    }

    public function getRecentTransactions(int $userId, int $limit = 10): \Illuminate\Support\Collection
    {
        $txs = Transaction::with(['account', 'category'])
            ->where('user_id', $userId)
            ->whereNull('transfer_id')
            ->whereNull('vehicle_id')
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function ($t) {
                return (object) [
                    'id'          => $t->id,
                    'type'        => $t->type instanceof \App\Enums\TransactionType ? $t->type->value : (string) $t->type,
                    'amount'      => (float) $t->amount,
                    'date'        => $t->date?->format('Y-m-d') ?? (string) $t->date,
                    'description' => $t->description,
                    'transfer_id' => null,
                    'account'     => $t->account ? (object) [
                        'id' => $t->account->id,
                        'name' => $t->account->name,
                        'color' => $t->account->color,
                        'icon' => $t->account->icon,
                    ] : null,
                    'category'    => $t->category ? (object) [
                        'id' => $t->category->id,
                        'name' => $t->category->name,
                        'color' => $t->category->color,
                        'icon' => $t->category->icon,
                    ] : null,
                    'created_at'  => (string) $t->created_at,
                ];
            });

        $transfers = \App\Models\Transfer::with(['fromAccount', 'toAccount'])
            ->where('user_id', $userId)
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->limit($limit)
            ->get()
            ->map(function ($t) {
                $fromName = $t->fromAccount?->name ?? 'Rekening';
                $toName   = $t->toAccount?->name ?? 'Rekening';
                return (object) [
                    'id'          => 'tr-' . $t->id,
                    'type'        => 'transfer',
                    'amount'      => (float) $t->amount,
                    'date'        => $t->date?->format('Y-m-d') ?? substr((string) $t->created_at, 0, 10),
                    'description' => $t->description ?: "Transfer dari {$fromName} ke {$toName}",
                    'transfer_id' => $t->id,
                    'account'     => (object) [
                        'id' => $t->from_account_id,
                        'name' => "{$fromName} ➔ {$toName}",
                        'color' => '#0066FF',
                        'icon' => '⇄',
                    ],
                    'category'    => (object) [
                        'id' => 0,
                        'name' => 'Transfer Rekening',
                        'color' => '#0066FF',
                        'icon' => '⇄',
                    ],
                    'created_at'  => (string) $t->created_at,
                ];
            });

        return $txs->concat($transfers)
            ->sortByDesc(fn ($item) => $item->date . '_' . (is_numeric($item->id) ? str_pad((string) $item->id, 8, '0', STR_PAD_LEFT) : $item->id))
            ->take($limit)
            ->values();
    }

    public function getIncomeExpenseChart(int $userId, string $period = 'monthly'): array
    {
        if ($period === 'daily') {
            return $this->getDailyChart($userId);
        }

        return $this->getMonthlyChart($userId);
    }

    private function getDailyChart(int $userId): array
    {
        $monthsId = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $from = now()->subDays(29)->startOfDay()->toDateString();
        $to   = now()->endOfDay()->toDateString();

        $results = Transaction::where('user_id', $userId)
            ->whereNull('transfer_id')
            ->whereNull('vehicle_id')
            ->whereBetween('date', [$from, $to])
            ->selectRaw("TO_CHAR(date, 'YYYY-MM-DD') as date_str, type, SUM(amount) as total")
            ->groupByRaw("TO_CHAR(date, 'YYYY-MM-DD'), type")
            ->get();

        $dates   = [];
        $income  = [];
        $expense = [];

        for ($i = 29; $i >= 0; $i--) {
            $d       = now()->subDays($i);
            $rawDate = $d->format('Y-m-d');
            $dates[] = $d->format('d') . ' ' . $monthsId[$d->month];

            $inc = $results->first(fn($r) => $r->date_str === $rawDate && ($r->type?->value ?? $r->type) === 'income');
            $exp = $results->first(fn($r) => $r->date_str === $rawDate && ($r->type?->value ?? $r->type) === 'expense');

            $income[]  = $inc ? (float) $inc->total : 0;
            $expense[] = $exp ? (float) $exp->total : 0;
        }

        $start = now()->subDays(29);
        $end = now();
        $rangeLabel = $start->format('d') . ' ' . $monthsId[$start->month] . ' — ' . $end->format('d') . ' ' . $monthsId[$end->month] . ' ' . $end->year;

        return [
            'labels'      => $dates,
            'income'      => $income,
            'expense'     => $expense,
            'range_label' => $rangeLabel,
        ];
    }

    private function getMonthlyChart(int $userId): array
    {
        $monthsId = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'
        ];

        $startDate = now()->subMonths(5)->startOfMonth()->toDateString();
        $endDate   = now()->endOfMonth()->toDateString();

        // Single optimized query for all 6 months
        $results = Transaction::where('user_id', $userId)
            ->whereNull('transfer_id')
            ->whereNull('vehicle_id')
            ->whereBetween('date', [$startDate, $endDate])
            ->selectRaw("TO_CHAR(date, 'YYYY-MM') as ym, type, SUM(amount) as total")
            ->groupByRaw("TO_CHAR(date, 'YYYY-MM'), type")
            ->get();

        $labels  = [];
        $income  = [];
        $expense = [];

        for ($i = 5; $i >= 0; $i--) {
            $d = now()->subMonths($i);
            $ym = $d->format('Y-m');
            $labels[] = $monthsId[$d->month];

            $inc = $results->first(fn($r) => $r->ym === $ym && ($r->type?->value ?? $r->type) === 'income');
            $exp = $results->first(fn($r) => $r->ym === $ym && ($r->type?->value ?? $r->type) === 'expense');

            $income[]  = $inc ? (float) $inc->total : 0;
            $expense[] = $exp ? (float) $exp->total : 0;
        }

        $start = now()->subMonths(5);
        $end = now();
        $rangeLabel = $monthsId[$start->month] . ' ' . $start->year . ' — ' . $monthsId[$end->month] . ' ' . $end->year;

        return [
            'labels'      => $labels,
            'income'      => $income,
            'expense'     => $expense,
            'range_label' => $rangeLabel,
        ];
    }

    public function getExpenseBreakdown(int $userId): array
    {
        $data = Transaction::with('category:id,name,icon,color')
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->whereNull('transfer_id')
            ->whereNull('vehicle_id')
            ->thisMonth()
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->get();

        return $data->map(fn ($item) => [
            'category' => $item->category?->name ?? 'Lainnya',
            'icon'     => $item->category?->icon ?? '📦',
            'color'    => $item->category?->color ?? '#6366F1',
            'total'    => (float) $item->total,
        ])->values()->toArray();
    }
}
