<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'title',
        'slug',
        'subtitle',
        'badge',
        'badge_color',
        'price',
        'price_label',
        'duration',
        'capacity',
        'vehicle_name',
        'description',
        'tour_route',
        'cover_photo_path',
        'gallery_photos',
        'facilities',
        'itinerary',
        'included',
        'excluded',
        'cta_whatsapp_text',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'gallery_photos' => 'array',
        'facilities'     => 'array',
        'itinerary'      => 'array',
        'included'       => 'array',
        'excluded'       => 'array',
        'sort_order'     => 'integer',
        'is_active'      => 'boolean',
    ];

    protected $appends = [
        'cover_photo_url',
        'gallery_photo_urls',
        'formatted_price',
    ];

    public function getCoverPhotoUrlAttribute(): ?string
    {
        if (empty($this->cover_photo_path)) {
            return null;
        }

        // If path is already an absolute URL or local relative path starting with '/'
        if (str_starts_with($this->cover_photo_path, 'http://') || str_starts_with($this->cover_photo_path, 'https://') || str_starts_with($this->cover_photo_path, '/')) {
            return $this->cover_photo_path;
        }

        return app(\App\Services\SupabaseStorageService::class)->getPublicUrl($this->cover_photo_path);
    }

    public function getGalleryPhotoUrlsAttribute(): array
    {
        if (empty($this->gallery_photos) || !is_array($this->gallery_photos)) {
            return [];
        }

        $storage = app(\App\Services\SupabaseStorageService::class);
        $urls = [];

        foreach ($this->gallery_photos as $item) {
            $path = is_array($item) ? ($item['path'] ?? $item['url'] ?? '') : (string) $item;
            if (empty($path)) continue;

            if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://') || str_starts_with($path, '/')) {
                $urls[] = $path;
            } else {
                $publicUrl = $storage->getPublicUrl($path);
                if ($publicUrl) {
                    $urls[] = $publicUrl;
                }
            }
        }

        return $urls;
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp ' . number_format((float) $this->price, 0, ',', '.');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }
}
