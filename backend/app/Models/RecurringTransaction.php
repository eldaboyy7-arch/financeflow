<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class RecurringTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'type',
        'name',
        'amount',
        'frequency',
        'start_date',
        'next_due_date',
        'last_run_date',
        'is_active',
        'auto_create',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'next_due_date' => 'date',
        'last_run_date' => 'date',
        'is_active' => 'boolean',
        'auto_create' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function advanceNextDueDate(): void
    {
        $current = Carbon::parse($this->next_due_date);
        $next = match ($this->frequency) {
            'daily'   => $current->copy()->addDay(),
            'weekly'  => $current->copy()->addWeek(),
            // addMonthsNoOverflow prevents date overflow (e.g. Jan 31 -> Feb 28, not Mar 3)
            'monthly' => $current->copy()->addMonthsNoOverflow(1),
            'yearly'  => $current->copy()->addYear(),
            default   => $current->copy()->addMonthsNoOverflow(1),
        };

        $this->last_run_date = now()->toDateString();
        $this->next_due_date = $next->toDateString();
        $this->saveQuietly();
    }
}
