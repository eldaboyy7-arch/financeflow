<?php

namespace App\Services;

use App\Models\Goal;
use App\Models\GoalContribution;
use App\Models\Account;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GoalService
{
    public function getGoalsForUser(int $userId)
    {
        return Goal::with(['account', 'contributions' => function($q) {
                $q->orderByDesc('date')->orderByDesc('id');
            }])
            ->where('user_id', $userId)
            ->orderBy('is_completed')
            ->orderBy('target_date')
            ->get()
            ->map(function ($goal) {
                $targetDate = $goal->target_date ? Carbon::parse($goal->target_date) : null;
                $daysRemaining = $targetDate ? max(0, (int) now()->diffInDays($targetDate, false)) : null;
                $isOverdue = $targetDate && $targetDate->isPast() && !$goal->is_completed;
                $remainingAmount = max(0, (float) ($goal->target_amount - $goal->current_amount));

                return [
                    'id'              => $goal->id,
                    'name'            => $goal->name,
                    'target_amount'   => (float) $goal->target_amount,
                    'current_amount'  => (float) $goal->current_amount,
                    'remaining_amount'=> $remainingAmount,
                    'percentage'      => $goal->percentage,
                    'target_date'     => $goal->target_date?->format('Y-m-d'),
                    'target_date_formatted' => $goal->target_date?->translatedFormat('d M Y'),
                    'days_remaining'  => $daysRemaining,
                    'is_overdue'      => $isOverdue,
                    'icon'            => $goal->icon,
                    'color'           => $goal->color,
                    'is_completed'    => $goal->is_completed,
                    'account'         => $goal->account ? [
                        'id'   => $goal->account->id,
                        'name' => $goal->account->name,
                        'icon' => $goal->account->icon,
                    ] : null,
                    'recent_contributions' => $goal->contributions->take(5)->map(fn($c) => [
                        'id'     => $c->id,
                        'amount' => (float) $c->amount,
                        'type'   => $c->type,
                        'date'   => $c->date?->format('Y-m-d'),
                        'notes'  => $c->notes,
                    ]),
                ];
            });
    }

    public function create(array $data, int $userId): Goal
    {
        return Goal::create(array_merge($data, [
            'user_id' => $userId,
            'current_amount' => 0,
            'is_completed' => false,
        ]));
    }

    public function update(Goal $goal, array $data): Goal
    {
        $goal->update($data);
        $goal->recalculateCurrentAmount();
        return $goal->fresh(['account']);
    }

    public function delete(Goal $goal): void
    {
        $goal->delete();
    }

    public function contribute(Goal $goal, array $data, int $userId): GoalContribution
    {
        return DB::transaction(function () use ($goal, $data, $userId) {
            $contribution = GoalContribution::create([
                'goal_id'    => $goal->id,
                'user_id'    => $userId,
                'account_id' => $data['account_id'] ?? null,
                'amount'     => $data['amount'],
                'type'       => $data['type'] ?? 'deposit',
                'date'       => $data['date'] ?? now()->toDateString(),
                'notes'      => $data['notes'] ?? null,
            ]);

            $goal->recalculateCurrentAmount();

            return $contribution;
        });
    }
}
