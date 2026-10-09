<?php

namespace App\Models;

use App\Enums\AccountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Account extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'icon',
        'color',
        'opening_balance',
        'current_balance',
        'is_active',
    ];

    protected $casts = [
        'type'            => AccountType::class,
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'is_active'       => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Recalculate the current balance based on opening balance,
     * all transactions, and all transfers. Does NOT create income/expense
     * records for transfers — balance is adjusted directly.
     *
     * NOTE: Income from rental transactions (vehicle_id != null) IS included
     * in the account balance — the money still lands in the physical account.
     * Only general (non-rental) expenses are subtracted here; rental-linked
     * expenses are tracked separately in vehicle/rental reports.
     */
    public function recalculateBalance(): void
    {
        // All income (general + rental) counts toward the account balance.
        $income     = $this->transactions()->where('type', 'income')->whereNull('transfer_id')->sum('amount');
        // Only non-rental expenses reduce the account balance directly.
        $expense    = $this->transactions()->where('type', 'expense')->whereNull('transfer_id')->whereNull('vehicle_id')->sum('amount');
        $transferIn = Transfer::where('to_account_id', $this->id)->sum('amount');
        $transferOut = Transfer::where('from_account_id', $this->id)->sum('amount')
                    + Transfer::where('from_account_id', $this->id)->sum('fee');

        $this->current_balance = $this->opening_balance + $income - $expense + $transferIn - $transferOut;
        $this->saveQuietly();
    }
}
