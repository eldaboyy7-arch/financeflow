<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'target_amount',
        'current_amount',
        'target_date',
        'icon',
        'color',
        'account_id',
        'is_completed',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_amount' => 'decimal:2',
        'target_date' => 'date',
        'is_completed' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class);
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(GoalContribution::class);
    }

    public function getPercentageAttribute(): float
    {
        if ($this->target_amount <= 0) return 0;
        return min(round(($this->current_amount / $this->target_amount) * 100, 1), 100);
    }

    public function recalculateCurrentAmount(): void
    {
        $deposits = $this->contributions()->where('type', 'deposit')->sum('amount');
        $withdrawals = $this->contributions()->where('type', 'withdraw')->sum('amount');
        $this->current_amount = max(0, (float) ($deposits - $withdrawals));
        $this->is_completed = $this->current_amount >= $this->target_amount;
        $this->saveQuietly();
    }
}
