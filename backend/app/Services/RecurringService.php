<?php

namespace App\Services;

use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Services\TransactionService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class RecurringService
{
    public function __construct(private TransactionService $transactionService)
    {
    }

    public function getRecurringForUser(int $userId)
    {
        return RecurringTransaction::with(['account', 'category'])
            ->where('user_id', $userId)
            ->orderBy('next_due_date')
            ->get()
            ->map(function ($rec) {
                $dueDate = Carbon::parse($rec->next_due_date);
                $daysUntilDue = (int) now()->diffInDays($dueDate, false);
                $isDueToday = $dueDate->isToday();
                $isOverdue = $dueDate->isPast() && !$isDueToday;

                return [
                    'id'             => $rec->id,
                    'name'           => $rec->name,
                    'amount'         => (float) $rec->amount,
                    'type'           => $rec->type,
                    'frequency'      => $rec->frequency,
                    'start_date'     => $rec->start_date?->format('Y-m-d'),
                    'next_due_date'  => $rec->next_due_date?->format('Y-m-d'),
                    'last_run_date'  => $rec->last_run_date?->format('Y-m-d'),
                    'is_active'      => $rec->is_active,
                    'auto_create'    => $rec->auto_create,
                    'notes'          => $rec->notes,
                    'days_until_due' => $daysUntilDue,
                    'is_due_today'   => $isDueToday,
                    'is_overdue'     => $isOverdue,
                    'account'        => $rec->account ? [
                        'id'   => $rec->account->id,
                        'name' => $rec->account->name,
                        'icon' => $rec->account->icon,
                    ] : null,
                    'category'       => $rec->category ? [
                        'id'    => $rec->category->id,
                        'name'  => $rec->category->name,
                        'icon'  => $rec->category->icon,
                        'color' => $rec->category->color,
                    ] : null,
                ];
            });
    }

    public function getUpcomingBills(int $userId, int $days = 30)
    {
        $all = $this->getRecurringForUser($userId);
        return $all->filter(function ($item) use ($days) {
            return $item['is_active'] && ($item['days_until_due'] <= $days || $item['is_overdue']);
        })->values();
    }

    public function create(array $data, int $userId): RecurringTransaction
    {
        return RecurringTransaction::create(array_merge($data, [
            'user_id' => $userId,
            'next_due_date' => $data['start_date'],
            'is_active' => true,
        ]));
    }

    public function update(RecurringTransaction $rec, array $data): RecurringTransaction
    {
        $rec->update($data);
        return $rec->fresh(['account', 'category']);
    }

    public function delete(RecurringTransaction $rec): void
    {
        $rec->delete();
    }

    public function pay(RecurringTransaction $rec, int $userId): Transaction
    {
        return DB::transaction(function () use ($rec, $userId) {
            // 1. Create real transaction
            $transaction = $this->transactionService->create([
                'account_id'  => $rec->account_id,
                'category_id' => $rec->category_id,
                'type'        => $rec->type,
                'amount'      => $rec->amount,
                'date'        => now()->toDateString(),
                'description' => "Pembayaran Tagihan Rutin: {$rec->name}",
            ], $userId);

            // 2. Advance next due date
            $rec->advanceNextDueDate();

            return $transaction;
        });
    }
}
