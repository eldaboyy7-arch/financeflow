<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TransactionService
{
    public function create(array $data, int $userId): Transaction
    {
        return DB::transaction(function () use ($data, $userId) {
            $transaction = Transaction::create(array_merge($data, ['user_id' => $userId]));
            $transaction->account->recalculateBalance();

            return $transaction->load(['account', 'category']);
        });
    }

    public function update(Transaction $transaction, array $data): Transaction
    {
        return DB::transaction(function () use ($transaction, $data) {
            $oldAccountId = $transaction->account_id;
            $oldReceiptPath = $transaction->receipt_path;

            $transaction->update($data);

            // Clean up old receipt file if changed/replaced
            if (isset($data['receipt_path']) && $oldReceiptPath && $oldReceiptPath !== $data['receipt_path']) {
                if (Storage::disk('local')->exists($oldReceiptPath)) {
                    Storage::disk('local')->delete($oldReceiptPath);
                }
            }

            // Recalculate old account if account changed
            if (isset($data['account_id']) && (int) $data['account_id'] !== $oldAccountId) {
                Account::find($oldAccountId)?->recalculateBalance();
            }

            $transaction->account->recalculateBalance();

            return $transaction->load(['account', 'category']);
        });
    }

    public function delete(Transaction $transaction): void
    {
        DB::transaction(function () use ($transaction) {
            $account = $transaction->account;
            $receiptPath = $transaction->receipt_path;

            $transaction->delete();
            $account->recalculateBalance();

            // Clean up private receipt file from local disk
            if ($receiptPath && Storage::disk('local')->exists($receiptPath)) {
                Storage::disk('local')->delete($receiptPath);
            }
        });
    }
}
