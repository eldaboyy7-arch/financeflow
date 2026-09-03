<?php

namespace App\Services;

use App\Models\Account;
use App\Models\Transfer;
use Illuminate\Support\Facades\DB;

class TransferService
{
    public function create(array $data, int $userId): Transfer
    {
        return DB::transaction(function () use ($data, $userId) {
            $transfer = Transfer::create(array_merge($data, ['user_id' => $userId]));
            $transfer->fromAccount->recalculateBalance();
            $transfer->toAccount->recalculateBalance();

            return $transfer->load(['fromAccount', 'toAccount']);
        });
    }

    public function update(Transfer $transfer, array $data): Transfer
    {
        return DB::transaction(function () use ($transfer, $data) {
            $oldFromId = $transfer->from_account_id;
            $oldToId   = $transfer->to_account_id;

            $transfer->update($data);

            // Recalculate all affected accounts
            $accountIds = array_unique([$oldFromId, $oldToId, $transfer->from_account_id, $transfer->to_account_id]);
            foreach ($accountIds as $accountId) {
                Account::find($accountId)?->recalculateBalance();
            }

            return $transfer->load(['fromAccount', 'toAccount']);
        });
    }

    public function delete(Transfer $transfer): void
    {
        DB::transaction(function () use ($transfer) {
            $fromAccount = $transfer->fromAccount;
            $toAccount   = $transfer->toAccount;
            $transfer->delete();
            $fromAccount->recalculateBalance();
            $toAccount->recalculateBalance();
        });
    }
}
