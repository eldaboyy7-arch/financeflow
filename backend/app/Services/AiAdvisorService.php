<?php

namespace App\Services;

use App\Models\User;
use App\Models\Transaction;
use App\Models\Account;
use App\Models\Goal;
use App\Models\RecurringTransaction;
use App\Services\BudgetService;
use App\Services\InsightService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAdvisorService
{
    public function __construct(
        private BudgetService $budgetService,
        private InsightService $insightService
    ) {
    }

    public function askAdvisor(int $userId, string $userMessage, array $chatHistory = []): string
    {
        $apiKeys = config('services.gemini.keys', []);
        if (empty($apiKeys)) {
            $fallback = config('services.gemini.key') ?? env('GEMINI_API_KEY');
            if ($fallback) {
                $apiKeys = [$fallback];
            }
        }

        if (empty($apiKeys)) {
            return "Maaf, API Key Gemini belum dikonfigurasi di server. Silakan tambahkan GEMINI_API_KEY pada backend.";
        }

        // 1. Construct user financial snapshot safely
        $snapshot = $this->buildFinancialSnapshot($userId);

        // 2. Build system instructions
        $systemInstruction = <<<TEXT
Kamu adalah "FinanceFlow AI", asisten konsultan perencana keuangan pribadi (Certified Financial Planner) yang ramah, bijak, cerdas, dan suportif berbahasa Indonesia.

Konteks Finansial Pengguna Saat Ini:
{$snapshot}

Panduan Memberikan Saran:
1. Berikan jawaban yang praktis, realistis, dan relevan langsung dengan data keuangan pengguna di atas.
2. Gunakan format mata uang Rupiah yang mudah dibaca (contoh: Rp1.500.000 atau Rp1,5 juta).
3. Jika pengguna bertanya apakah aman membeli suatu barang, bandingkan dengan sisa arus kas (cashflow), tabungan, atau batas budget terkait.
4. Gunakan poin-poin singkat atau bullet points jika memberikan langkah-langkah rekomendasi.
5. Jaga nada bicara tetap positif, solutif, dan tidak menghakimi. Jawab maksimal 3-4 paragraf singkat agar nyaman dibaca di mobile.
TEXT;

        // 3. Format contents for Gemini
        $contents = [];

        // Append recent chat history (last 6 messages)
        foreach (array_slice($chatHistory, -6) as $msg) {
            $contents[] = [
                'role' => ($msg['role'] ?? 'user') === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => $msg['text'] ?? $msg['content'] ?? '']],
            ];
        }

        // Add current user prompt
        $contents[] = [
            'role' => 'user',
            'parts' => [['text' => $userMessage]],
        ];

        // 4. Query Gemini with multi-key fallback pool
        $lastError = null;
        foreach ($apiKeys as $index => $apiKey) {
            try {
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                ])->timeout(30)->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key={$apiKey}", [
                    'system_instruction' => [
                        'parts' => [
                            ['text' => $systemInstruction]
                        ]
                    ],
                    'contents' => $contents,
                    'generationConfig' => [
                        'temperature' => 0.7,
                        'maxOutputTokens' => 3500,
                    ],
                ]);

                if ($response->successful()) {
                    $body = $response->json();
                    $reply = $body['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($reply) {
                        return trim($reply);
                    }
                }

                $lastError = $response->body();
                Log::warning("Gemini Advisor key [{$index}] returned status {$response->status()}. Trying next fallback key...");
            } catch (\Throwable $e) {
                $lastError = $e->getMessage();
                Log::warning("Gemini Advisor key [{$index}] exception: {$e->getMessage()}. Trying next fallback key...");
            }
        }

        Log::error('All Gemini Advisor keys failed. Last error: ' . $lastError);
        return "Maaf, terjadi kendala saat menganalisis data keuanganmu. Silakan coba ajukan pertanyaan kembali.";
    }

    private function buildFinancialSnapshot(int $userId): string
    {
        $year = now()->year;
        $month = now()->month;

        $totalBalance = Account::where('user_id', $userId)->where('is_active', true)->sum('current_balance');
        $incomeThisMonth = Transaction::where('user_id', $userId)->where('type', 'income')->whereNull('transfer_id')->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
        $expenseThisMonth = Transaction::where('user_id', $userId)->where('type', 'expense')->whereNull('transfer_id')->whereMonth('date', $month)->whereYear('date', $year)->sum('amount');
        $netCashFlow = $incomeThisMonth - $expenseThisMonth;
        $savingRate = $incomeThisMonth > 0 ? round(($netCashFlow / $incomeThisMonth) * 100, 1) : 0;

        $budgets = $this->budgetService->getBudgetsForPeriod($userId, $year, $month);
        $budgetList = [];
        foreach ($budgets as $b) {
            $budgetList[] = "- {$b['category']['name']}: Budget Rp" . number_format($b['amount']) . " | Terpakai Rp" . number_format($b['spent']) . " ({$b['percentage']}%) [Status: {$b['status']}]";
        }
        $budgetString = !empty($budgetList) ? implode("\n", $budgetList) : "- Belum ada budget aktif bulan ini.";

        $goals = Goal::where('user_id', $userId)->where('is_completed', false)->get();
        $goalList = [];
        foreach ($goals as $g) {
            $goalList[] = "- {$g->name}: Target Rp" . number_format($g->target_amount) . " | Terkumpul Rp" . number_format($g->current_amount) . " ({$g->percentage}%)";
        }
        $goalString = !empty($goalList) ? implode("\n", $goalList) : "- Belum ada target menabung.";

        $upcomingBills = RecurringTransaction::where('user_id', $userId)->where('is_active', true)->where('next_due_date', '<=', now()->addDays(14)->toDateString())->get();
        $billList = [];
        foreach ($upcomingBills as $bill) {
            $billList[] = "- {$bill->name}: Rp" . number_format($bill->amount) . " (Jatuh tempo {$bill->next_due_date->format('d M')})";
        }
        $billString = !empty($billList) ? implode("\n", $billList) : "- Tidak ada tagihan jatuh tempo dalam 14 hari.";

        return <<<DATA
- Total Saldo Semua Rekening: Rp{$totalBalance}
- Pemasukan Bulan Ini: Rp{$incomeThisMonth}
- Pengeluaran Bulan Ini: Rp{$expenseThisMonth}
- Arus Kas Bersih (Net Cashflow): Rp{$netCashFlow}
- Rasio Tabungan: {$savingRate}%

Status Anggaran (Budget) Bulan Ini:
{$budgetString}

Target Menabung (Goals):
{$goalString}

Tagihan Mendatang (14 Hari ke Depan):
{$billString}
DATA;
    }
}
