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
        'photo_path',
        'video_url',
        'video_path',
        'transmission',
        'capacity',
        'fuel_type',
        'description',
    ];

    protected $casts = [
        'daily_rate' => 'decimal:2',
        'capacity'   => 'integer',
    ];

    protected $appends = [
        'photo_url',
        'safe_video_embed_url',
    ];

    public function getPhotoUrlAttribute(): ?string
    {
        return app(\App\Services\SupabaseStorageService::class)->getPublicUrl($this->photo_path);
    }

    public function getSafeVideoEmbedUrlAttribute(): ?string
    {
        return app(\App\Services\VideoEmbedService::class)->toSafeEmbedUrl($this->video_url);
    }

    public function getVideoStorageUrlAttribute(): ?string
    {
        return app(\App\Services\SupabaseStorageService::class)->getPublicUrl($this->video_path);
    }

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
