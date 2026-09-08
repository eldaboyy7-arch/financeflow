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
        'gallery_photos',
        'video_url',
        'video_path',
        'transmission',
        'capacity',
        'fuel_type',
        'description',
        'is_featured',
    ];

    protected $casts = [
        'daily_rate'     => 'decimal:2',
        'capacity'       => 'integer',
        'is_featured'    => 'boolean',
        'gallery_photos' => 'array',
    ];

    protected $appends = [
        'photo_url',
        'gallery_photo_urls',
        'safe_video_embed_url',
    ];

    public function getPhotoUrlAttribute(): ?string
    {
        return app(\App\Services\SupabaseStorageService::class)->getPublicUrl($this->photo_path);
    }

    public function getGalleryPhotoUrlsAttribute(): array
    {
        if (empty($this->gallery_photos) || !is_array($this->gallery_photos)) {
            return [];
        }

        $storage = app(\App\Services\SupabaseStorageService::class);
        $result = [];

        foreach ($this->gallery_photos as $item) {
            if (is_array($item)) {
                $path = $item['path'] ?? $item['url'] ?? '';
                $label = $item['label'] ?? '';
                $id = $item['id'] ?? '';
            } else {
                $path = (string) $item;
                $label = 'Foto';
                $id = '';
            }

            if (empty($path)) continue;

            $url = (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/'))
                ? $path
                : ($storage->getPublicUrl($path) ?? $path);

            $result[] = [
                'id'    => $id,
                'label' => $label,
                'path'  => $path,
                'url'   => $url,
            ];
        }

        return $result;
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

    public function tourPackages(): HasMany
    {
        return $this->hasMany(TourPackage::class);
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
