<?php

namespace App\Services;

use App\Models\Transaction;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    public function exportTransactionsCsv(int $userId, array $filters = []): StreamedResponse
    {
        $query = Transaction::with(['account', 'category'])
            ->where('user_id', $userId)
            ->whereNull('transfer_id')
            ->orderByDesc('date')
            ->orderByDesc('id');

        if (!empty($filters['type']) && in_array($filters['type'], ['income', 'expense'])) {
            $query->where('type', $filters['type']);
        }
        if (!empty($filters['account_id'])) {
            $query->where('account_id', $filters['account_id']);
        }
        if (!empty($filters['category_id'])) {
            $query->where('category_id', $filters['category_id']);
        }
        if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
            $query->whereBetween('date', [$filters['date_from'], $filters['date_to']]);
        }

        $filename = 'FinanceFlow_Transaksi_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($query) {
            $handle = fopen('php://output', 'w');

            // 1. Add UTF-8 BOM so Excel opens UTF-8 characters properly
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            // 2. Add sep=, directive so Excel on all locales (including Indonesian) automatically splits columns!
            fwrite($handle, "sep=,\r\n");

            // 3. Clean professional headers
            fputcsv($handle, [
                'No',
                'Tanggal',
                'Tipe Transaksi',
                'Kategori',
                'Rekening',
                'Nominal (Rp)',
                'Deskripsi / Catatan'
            ]);

            $no = 1;
            $query->chunk(200, function ($transactions) use ($handle, &$no) {
                foreach ($transactions as $t) {
                    fputcsv($handle, [
                        $no++,
                        $t->date?->format('d/m/Y') ?? '',
                        ($t->type?->value ?? $t->type) === 'income' ? 'Pemasukan' : 'Pengeluaran',
                        $t->category?->name ?? 'Lainnya',
                        $t->account?->name ?? 'Rekening',
                        (float) $t->amount,
                        $t->description ?? '-',
                    ]);
                }
            });

            fclose($handle);
        }, 200, $headers);
    }
}
