<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\RecurringTransaction;
use App\Models\Budget;
use App\Services\BudgetService;
use Carbon\Carbon;

class NotificationService
{
    public function __construct(private BudgetService $budgetService)
    {
    }

    public function syncSmartAlerts(int $userId): void
    {
        // 1. Check upcoming bills in next 3 days
        $upcomingBills = RecurringTransaction::where('user_id', $userId)
            ->where('is_active', true)
            ->where('next_due_date', '<=', now()->addDays(3)->toDateString())
            ->get();

        foreach ($upcomingBills as $bill) {
            $due = Carbon::parse($bill->next_due_date);
            $days = (int) now()->diffInDays($due, false);
            $timeText = $due->isToday() ? 'hari ini' : ($due->isTomorrow() ? 'besok' : ($days < 0 ? 'sudah lewat' : "dalam {$days} hari"));
            $type = $days < 0 ? 'danger' : 'warning';
            $title = "Tagihan {$bill->name} Jatuh Tempo";
            $message = "Tagihan Rp" . number_format($bill->amount, 0, ',', '.') . " jatuh tempo {$timeText}.";
            $key = "bill_{$bill->id}_{$bill->next_due_date->format('Ymd')}";

            $exists = Notification::where('user_id', $userId)
                ->where('data->key', $key)
                ->exists();

            if (!$exists) {
                Notification::create([
                    'user_id' => $userId,
                    'type'    => $type,
                    'title'   => $title,
                    'message' => $message,
                    'data'    => [
                        'key'     => $key,
                        'bill_id' => $bill->id,
                        'link'    => '/langganan',
                    ],
                ]);
            }
        }

        // 2. Check exceeded or warning budgets this month
        $budgets = $this->budgetService->getBudgetsForPeriod($userId, now()->year, now()->month);
        foreach ($budgets as $b) {
            if ($b['status'] === 'exceeded') {
                $key = "budget_exc_{$b['id']}_{$b['month']}_{$b['year']}";
                $exists = Notification::where('user_id', $userId)
                    ->where('data->key', $key)
                    ->exists();

                if (!$exists) {
                    Notification::create([
                        'user_id' => $userId,
                        'type'    => 'danger',
                        'title'   => "Budget {$b['category']['name']} Melebihi Batas!",
                        'message' => "Pengeluaran Rp" . number_format($b['spent'], 0, ',', '.') . " telah melebihi batas anggaran Rp" . number_format($b['amount'], 0, ',', '.') . " ({$b['percentage']}%).",
                        'data'    => [
                            'key'       => $key,
                            'budget_id' => $b['id'],
                            'link'      => '/anggaran',
                        ],
                    ]);
                }
            } elseif ($b['status'] === 'warning') {
                $key = "budget_warn_{$b['id']}_{$b['month']}_{$b['year']}";
                $exists = Notification::where('user_id', $userId)
                    ->where('data->key', $key)
                    ->exists();

                if (!$exists) {
                    Notification::create([
                        'user_id' => $userId,
                        'type'    => 'warning',
                        'title'   => "Budget {$b['category']['name']} Mendekati Limit",
                        'message' => "Pengeluaran telah mencapai {$b['percentage']}% dari anggaran Rp" . number_format($b['amount'], 0, ',', '.') . ".",
                        'data'    => [
                            'key'       => $key,
                            'budget_id' => $b['id'],
                            'link'      => '/anggaran',
                        ],
                    ]);
                }
            }
        }
    }

    public function getNotificationsForUser(int $userId): array
    {
        $this->syncSmartAlerts($userId);

        $dbNotifs = Notification::where('user_id', $userId)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $unreadCount = Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->count();

        $items = $dbNotifs->map(fn($n) => [
            'id'         => (string) $n->id,
            'type'       => $n->type,
            'title'      => $n->title,
            'message'    => $n->message,
            'created_at' => $n->created_at->format('Y-m-d H:i:s'),
            'read'       => !is_null($n->read_at),
            'link'       => $n->data['link'] ?? null,
        ])->toArray();

        return [
            'unread_count' => $unreadCount,
            'items'        => $items,
        ];
    }

    public function markAsRead(int $userId, int $notificationId): void
    {
        Notification::where('user_id', $userId)
            ->where('id', $notificationId)
            ->update(['read_at' => now()]);
    }

    public function markAllAsRead(int $userId): void
    {
        Notification::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
