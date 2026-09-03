<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class InsightService
{
    public function __construct(
        protected BudgetService $budgetService
    ) {}

    /**
     * Generate 3–4 highly actionable, deterministic financial insights based on real data.
     */
    public function getSmartInsights(int $userId, int $year, int $month): array
    {
        $insights = [];

        // 1. Current Month and Previous Month Totals
        $currDate = Carbon::createFromDate($year, $month, 1);
        $prevDate = $currDate->copy()->subMonth();

        $currExpenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereNull('transfer_id')
            ->whereYear('date', $currDate->year)
            ->whereMonth('date', $currDate->month)
            ->sum('amount');

        $prevExpenses = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereNull('transfer_id')
            ->whereYear('date', $prevDate->year)
            ->whereMonth('date', $prevDate->month)
            ->sum('amount');

        $currIncome = Transaction::where('user_id', $userId)
            ->where('type', 'income')
            ->whereNull('transfer_id')
            ->whereYear('date', $currDate->year)
            ->whereMonth('date', $currDate->month)
            ->sum('amount');

        // ── INSIGHT A: Budget Alerts (Highest Priority) ─────────────────────
        $budgets = $this->budgetService->getBudgetsForPeriod($userId, $year, $month);
        $exceededBudget = $budgets->firstWhere('status', 'exceeded');
        $warningBudget  = $budgets->firstWhere('status', 'warning');

        if ($exceededBudget) {
            $catName = $exceededBudget['category']['name'] ?? 'Kategori';
            $over = number_format($exceededBudget['overspent'], 0, ',', '.');
            $pct = $exceededBudget['percentage'];
            $insights[] = [
                'id'       => 'budget_exceeded_' . $exceededBudget['id'],
                'type'     => 'danger',
                'icon'     => '🚨',
                'title'    => "Budget {$catName} Melebihi Limit",
                'message'  => "Pengeluaran {$catName} telah melebihi budget sebesar Rp{$over} ({$pct}% terpakai).",
                'category' => $catName,
            ];
        } elseif ($warningBudget) {
            $catName = $warningBudget['category']['name'] ?? 'Kategori';
            $rem = number_format($warningBudget['remaining'], 0, ',', '.');
            $pct = $warningBudget['percentage'];
            $insights[] = [
                'id'       => 'budget_warning_' . $warningBudget['id'],
                'type'     => 'warning',
                'icon'     => '⚠️',
                'title'    => "Peringatan Budget {$catName}",
                'message'  => "Budget {$catName} sudah terpakai {$pct}%, tersisa Rp{$rem} untuk sisa bulan ini.",
                'category' => $catName,
            ];
        }

        // ── INSIGHT B: Highest Spending Category ───────────────────────────
        $topCategory = Transaction::with('category')
            ->where('user_id', $userId)
            ->where('type', 'expense')
            ->whereNull('transfer_id')
            ->whereYear('date', $currDate->year)
            ->whereMonth('date', $currDate->month)
            ->select('category_id', DB::raw('SUM(amount) as total'))
            ->groupBy('category_id')
            ->orderByDesc('total')
            ->first();

        if ($topCategory && $currExpenses > 0) {
            $catName = $topCategory->category?->name ?? 'Lainnya';
            $catIcon = $topCategory->category?->icon ?? '📦';
            $catTotal = (float) $topCategory->total;
            $catPct = round(($catTotal / $currExpenses) * 100, 1);

            $insights[] = [
                'id'       => 'top_spending_category',
                'type'     => 'primary',
                'icon'     => $catIcon,
                'title'    => "Pengeluaran Terbesar: {$catName}",
                'message'  => "{$catName} adalah pos pengeluaran terbesar kamu bulan ini sebesar Rp" . number_format($catTotal, 0, ',', '.') . " ({$catPct}% dari total pengeluaran).",
                'category' => $catName,
            ];
        }

        // ── INSIGHT C: Month-over-Month Category Trend ──────────────────────
        $currCatBreakdown = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereNull('transfer_id')
            ->whereYear('date', $currDate->year)
            ->whereMonth('date', $currDate->month)
            ->groupBy('category_id')
            ->pluck(DB::raw('SUM(amount) as total'), 'category_id');

        $prevCatBreakdown = Transaction::where('user_id', $userId)
            ->where('type', 'expense')
            ->whereNull('transfer_id')
            ->whereYear('date', $prevDate->year)
            ->whereMonth('date', $prevDate->month)
            ->groupBy('category_id')
            ->pluck(DB::raw('SUM(amount) as total'), 'category_id');

        $notableChange = null;
        $maxDiffPct = 0;

        foreach ($currCatBreakdown as $catId => $currTotal) {
            $prevTotal = (float) ($prevCatBreakdown[$catId] ?? 0);
            if ($prevTotal > 50000) { // minimum threshold to be meaningful
                $diff = (float) $currTotal - $prevTotal;
                $pctChange = round(($diff / $prevTotal) * 100, 1);

                if (abs($pctChange) > abs($maxDiffPct)) {
                    $maxDiffPct = $pctChange;
                    $catObj = \App\Models\Category::find($catId);
                    $notableChange = [
                        'name'       => $catObj?->name ?? 'Kategori',
                        'icon'       => $catObj?->icon ?? '📊',
                        'pct'        => abs($pctChange),
                        'is_up'      => $pctChange > 0,
                        'curr_total' => (float) $currTotal,
                    ];
                }
            }
        }

        if ($notableChange && $notableChange['pct'] >= 10) {
            $cName = $notableChange['name'];
            $cPct  = $notableChange['pct'];
            $cTot  = number_format($notableChange['curr_total'], 0, ',', '.');

            if ($notableChange['is_up']) {
                $insights[] = [
                    'id'       => 'category_trend_up_' . strtolower($cName),
                    'type'     => 'warning',
                    'icon'     => '💡',
                    'title'    => "Pengeluaran {$cName} Naik {$cPct}%",
                    'message'  => "Kamu menghabiskan Rp{$cTot} untuk {$cName} bulan ini, naik {$cPct}% dibanding bulan sebelumnya.",
                    'category' => $cName,
                ];
            } else {
                $insights[] = [
                    'id'       => 'category_trend_down_' . strtolower($cName),
                    'type'     => 'success',
                    'icon'     => '🎉',
                    'title'    => "Penghematan {$cName} Berhasil!",
                    'message'  => "Pengeluaran {$cName} kamu turun {$cPct}% dibanding bulan lalu. Kerja bagus!",
                    'category' => $cName,
                ];
            }
        }

        // ── INSIGHT D: Savings Ratio & Cashflow Health ──────────────────────
        if ($currIncome > 0) {
            $netSavings = (float) ($currIncome - $currExpenses);
            $savingsRatio = round(($netSavings / $currIncome) * 100, 1);

            if ($savingsRatio >= 30) {
                $insights[] = [
                    'id'       => 'savings_ratio_healthy',
                    'type'     => 'success',
                    'icon'     => '🌟',
                    'title'    => 'Rasio Tabungan Sangat Sehat',
                    'message'  => "Kamu berhasil menyisihkan {$savingsRatio}% dari total pemasukan bulan ini. Pertahankan!",
                    'category' => 'Tabungan',
                ];
            } elseif ($netSavings < 0) {
                $deficit = number_format(abs($netSavings), 0, ',', '.');
                $insights[] = [
                    'id'       => 'negative_cashflow',
                    'type'     => 'danger',
                    'icon'     => '⚠️',
                    'title'    => 'Arus Kas Defisit',
                    'message'  => "Pengeluaran bulan ini melebihi pemasukan sebesar Rp{$deficit}. Pertimbangkan mengurangi pos pengeluaran non-esensial.",
                    'category' => 'Arus Kas',
                ];
            }
        }

        // Return strictly 3–4 highest quality insights
        return array_slice($insights, 0, 4);
    }
}
