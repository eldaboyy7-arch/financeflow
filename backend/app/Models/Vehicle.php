<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vehicle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'plate_number',
        'brand',
        'model_year',
        'status',
        'daily_rate',
        'color',
        'notes',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
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
     * Get income (transactions of type 'income') for a given month/year.
     */
    public function incomeForPeriod(int $month, int $year): float
    {
        return (float) $this->transactions()
            ->where('type', 'income')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');
    }

    /**
     * Get expenses (transactions of type 'expense') for a given month/year.
     */
    public function expenseForPeriod(int $month, int $year): float
    {
        return (float) $this->transactions()
            ->where('type', 'expense')
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->sum('amount');
    }

    /**
     * Net profit for a given month/year.
     */
    public function profitForPeriod(int $month, int $year): float
    {
        return $this->incomeForPeriod($month, $year) - $this->expenseForPeriod($month, $year);
    }
}
