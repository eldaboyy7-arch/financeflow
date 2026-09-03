<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Category;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ReceiptScannerService
{
    /**
     * Scan and parse a receipt/payment image using Gemini Vision AI.
     *
     * @param string $imagePath Local absolute path to the uploaded image
     * @param string $mimeType  e.g. image/jpeg, image/png, image/webp
     * @param User   $user      The authenticated user
     * @return array Structured transaction draft
     */
    public function scan(string $imagePath, string $mimeType, User $user): array
    {
        // 1. Get user's active categories and accounts for AI context matching
        $categories = Category::forUser($user->id)->get(['id', 'name', 'type'])->toArray();
        $accounts   = Account::where('user_id', $user->id)
                             ->where('is_active', true)
                             ->get(['id', 'name', 'type'])
                             ->toArray();

        // 2. Read image and encode to base64
        $imageData = base64_encode(file_get_contents($imagePath));

        $apiKeys = config('services.gemini.keys', []);
        if (empty($apiKeys)) {
            $fallback = config('services.gemini.key', env('GEMINI_API_KEY', ''));
            if ($fallback) {
                $apiKeys = [$fallback];
            }
        }

        // If no API key configured, use intelligent rule-based fallback draft
        if (empty($apiKeys)) {
            Log::warning('GEMINI_API_KEY is not configured. Falling back to local heuristic draft.');
            return $this->createFallbackDraft($user, $categories, $accounts);
        }

        try {
            $aiResult = $this->callGeminiVision($apiKeys, $imageData, $mimeType, $categories, $accounts);
            return $this->normalizeDraft($aiResult, $user, $categories, $accounts);
        } catch (\Throwable $e) {
            Log::error('Gemini Vision Scanner Error: ' . $e->getMessage(), [
                'exception' => $e,
            ]);

            // Graceful fallback: provide partial editable draft instead of crashing
            return $this->createFallbackDraft($user, $categories, $accounts, 'Gagal terhubung ke AI Vision. Silakan lengkapi data manual.');
        }
    }

    /**
     * Call Google Gemini Vision API with structured JSON response schema and multi-key fallback.
     */
    protected function callGeminiVision(array $apiKeys, string $imageBase64, string $mimeType, array $categories, array $accounts): array
    {
        $categoriesJson = json_encode($categories);
        $accountsJson   = json_encode($accounts);

        $systemInstruction = <<<PROMPT
Anda adalah asisten AI finansial profesional untuk aplikasi "FinanceFlow".
Tugas Anda adalah menganalisis foto struk belanja, bon restoran, kasir toko (Indomaret, Alfamart, SPBU, dll), atau screenshot bukti transfer/pembayaran (QRIS BCA, Mandiri Livin, BRImo, GoPay, OVO, ShopeePay, DANA).

Instruksi Penting:
1. Cari TOTAL AKHIR pembayaran dalam Rupiah (amount). Jangan terkecoh dengan subtotal, diskon, atau uang kembalian (change).
2. Tentukan nama Merchant / Toko / Nama Penerima Transfer.
3. Tentukan Tanggal transaksi dalam format YYYY-MM-DD. Jika tanggal tahun terpotong, gunakan tahun berjalan 2026.
4. Tentukan Jenis transaksi: biasanya "expense" (pengeluaran), kecuali jika struk adalah bukti terima uang/gaji/refund maka "income".
5. Pilih Kategori paling cocok dari daftar Kategori User berikut:
{$categoriesJson}
Pilih category_id yang paling tepat.
6. Deteksi Rekening pembayaran jika terlihat logo atau teks bank/e-wallet (misal BCA, Mandiri, GoPay, Tunai) dari daftar Rekening User berikut:
{$accountsJson}
Pilih account_id yang paling cocok. Jika tidak yakin, isi null.
7. Buat Deskripsi ringkas yang jelas (misal: "Nama Toko (Item 1, Item 2)").
8. Jika struk memiliki daftar item, ekstrak ke array "items" (nama item, harga satuan/total, qty).
9. Nilai tingkat keyakinan (confidence): "high", "medium", atau "low".

Keluarkan HANYA JSON valid sesuai skema yang diminta.
PROMPT;

        // Active Google Gemini Vision models
        $models = ['gemini-3.6-flash', 'gemini-3.5-flash', 'gemini-flash-latest'];
        $lastException = null;

        foreach ($apiKeys as $keyIndex => $apiKey) {
            foreach ($models as $model) {
                try {
                    $endpoint = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

                    $response = Http::timeout(30)
                        ->withHeaders(['Content-Type' => 'application/json'])
                        ->post($endpoint, [
                            'contents' => [
                                [
                                    'role'  => 'user',
                                    'parts' => [
                                        ['text' => $systemInstruction],
                                        [
                                            'inline_data' => [
                                                'mime_type' => $mimeType,
                                                'data'      => $imageBase64,
                                            ],
                                        ],
                                    ],
                                ],
                            ],
                            'generationConfig' => [
                                'response_mime_type' => 'application/json',
                                'temperature'        => 0.1,
                            ],
                        ]);

                    if ($response->successful()) {
                        $json = $response->json();
                        $content = $json['candidates'][0]['content']['parts'][0]['text'] ?? '{}';
                        $parsed = json_decode($content, true);

                        if (is_array($parsed) && !empty($parsed)) {
                            return $parsed;
                        }
                    } else {
                        Log::warning("Gemini Vision key [{$keyIndex}] model [{$model}] returned status: " . $response->status());
                    }
                } catch (\Throwable $e) {
                    $lastException = $e;
                    Log::warning("Gemini Vision key [{$keyIndex}] model [{$model}] error: " . $e->getMessage());
                }
            }
        }

        throw $lastException ?? new \RuntimeException('Semua API key dan model Gemini gagal memproses gambar.');
    }

    /**
     * Normalize, validate, and sanitize AI output against database constraints.
     */
    protected function normalizeDraft(array $ai, User $user, array $categories, array $accounts): array
    {
        // 1. Amount
        $rawAmount = $ai['amount'] ?? $ai['total_amount'] ?? $ai['total'] ?? 0;
        $amount = (float) filter_var($rawAmount, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $amount = abs($amount);

        // 2. Date
        $rawDate = $ai['date'] ?? null;
        $date = now()->format('Y-m-d');
        if ($rawDate) {
            try {
                $parsedDate = Carbon::parse($rawDate);
                // Ensure date is reasonable (between 2020 and 2030)
                if ($parsedDate->year >= 2020 && $parsedDate->year <= 2030) {
                    $date = $parsedDate->format('Y-m-d');
                }
            } catch (\Throwable) {
                $date = now()->format('Y-m-d');
            }
        }

        // 3. Type
        $type = in_array(strtolower($ai['type'] ?? ''), ['income', 'expense']) ? strtolower($ai['type']) : 'expense';

        // 4. Category ID matching
        $validCatIds = array_column($categories, 'id');
        $categoryId = isset($ai['category_id']) && in_array((int) $ai['category_id'], $validCatIds)
            ? (int) $ai['category_id']
            : null;

        if (!$categoryId) {
            // Fuzzy match by category name
            $catName = strtolower($ai['category_name'] ?? $ai['category'] ?? '');
            foreach ($categories as $cat) {
                if ($cat['type'] === $type && str_contains(strtolower($cat['name']), $catName)) {
                    $categoryId = $cat['id'];
                    break;
                }
            }
        }

        // Fallback to first matching type category
        if (!$categoryId && !empty($categories)) {
            $filtered = array_filter($categories, fn ($c) => $c['type'] === $type);
            $first = reset($filtered);
            $categoryId = $first ? $first['id'] : $categories[0]['id'];
        }

        // 5. Account ID matching
        $validAccIds = array_column($accounts, 'id');
        $accountId = isset($ai['account_id']) && in_array((int) $ai['account_id'], $validAccIds)
            ? (int) $ai['account_id']
            : null;

        // Fallback to user's first active account
        if (!$accountId && !empty($accounts)) {
            $accountId = $accounts[0]['id'];
        }

        // 6. Description & Merchant
        $merchant = trim($ai['merchant'] ?? $ai['store'] ?? $ai['payee'] ?? '');
        $description = trim($ai['description'] ?? '');

        if (empty($description) && !empty($merchant)) {
            $description = $merchant;
        }

        // 7. Items list
        $items = [];
        if (isset($ai['items']) && is_array($ai['items'])) {
            foreach ($ai['items'] as $item) {
                if (is_array($item) && (!empty($item['name']) || !empty($item['item']))) {
                    $items[] = [
                        'name'  => $item['name'] ?? $item['item'] ?? 'Item',
                        'price' => (float) ($item['price'] ?? 0),
                        'qty'   => (int) ($item['qty'] ?? $item['quantity'] ?? 1),
                    ];
                }
            }
        }

        return [
            'merchant'    => $merchant,
            'amount'      => $amount,
            'date'        => $date,
            'type'        => $type,
            'category_id' => $categoryId,
            'account_id'  => $accountId,
            'description' => $description,
            'items'       => $items,
            'confidence'  => in_array($ai['confidence'] ?? '', ['high', 'medium', 'low']) ? $ai['confidence'] : 'medium',
            'notes'       => $ai['notes'] ?? null,
        ];
    }

    /**
     * Fallback draft for offline development or error handling.
     */
    protected function createFallbackDraft(User $user, array $categories, array $accounts, ?string $note = null): array
    {
        $firstCat = !empty($categories) ? $categories[0]['id'] : 1;
        $firstAcc = !empty($accounts) ? $accounts[0]['id'] : 1;

        return [
            'merchant'    => '',
            'amount'      => 0,
            'date'        => now()->format('Y-m-d'),
            'type'        => 'expense',
            'category_id' => $firstCat,
            'account_id'  => $firstAcc,
            'description' => '',
            'items'       => [],
            'confidence'  => 'low',
            'notes'       => $note ?? 'Gambar siap. Silakan periksa atau sesuaikan nominal dan kategori.',
        ];
    }
}
