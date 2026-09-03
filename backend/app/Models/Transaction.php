<?php

namespace App\Models;

use App\Enums\TransactionType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'account_id',
        'category_id',
        'vehicle_id',
        'type',
        'amount',
        'date',
        'description',
        'receipt_path',
        'transfer_id',
    ];

    protected $casts = [
        'type'   => TransactionType::class,
        'amount' => 'decimal:2',
        'date'   => 'date',
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

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('date', now()->month)
                     ->whereYear('date', now()->year);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeByDateRange(Builder $query, string $from, string $to): Builder
    {
        return $query->whereBetween('date', [$from, $to]);
    }

    public function scopeExcludeTransfers(Builder $query): Builder
    {
        return $query->whereNull('transfer_id');
    }
}
