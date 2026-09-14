<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TourPackage extends Model
{
    use HasFactory;

    protected static bool $isSyncing = false;

    protected static function booted(): void
    {
        static::saving(function (TourPackage $package) {
            if (empty($package->badge_color)) {
                $package->badge_color = 'blue';
            }
            if (empty($package->price_label)) {
                $package->price_label = 'HARGA MULAI';
            }
            if (empty($package->duration)) {
                $package->duration = 'Full Day Tour (8 - 10 Jam)';
            }
            if (empty($package->capacity)) {
                $package->capacity = '15 Person';
            }
            if (is_null($package->sort_order)) {
                $package->sort_order = 0;
            }
            if (is_null($package->is_active)) {
                $package->is_active = true;
            }
        });

        static::saved(function (TourPackage $package) {
            if (static::$isSyncing) return;

            $sharedUserIds = [3, 18];
            if (!in_array($package->user_id, $sharedUserIds, true)) return;

            $targetUserId = ($package->user_id === 3) ? 18 : 3;

            static::$isSyncing = true;
            try {
                if (!empty($package->slug)) {
                    $baseSlug = preg_replace('/-admin$/', '', $package->slug);
                    $targetSlug = ($targetUserId === 3) ? ($baseSlug . '-admin') : $baseSlug;

                    $other = static::where('user_id', $targetUserId)
                        ->where(function ($q) use ($package, $targetSlug, $baseSlug) {
                            $q->where('slug', $targetSlug)
                              ->orWhere('slug', $baseSlug)
                              ->orWhere('slug', $package->slug)
                              ->orWhere('title', $package->title);
                        })
                        ->first();

                    // Resolve vehicle_id for target user if package has vehicle
                    $targetVehicleId = null;
                    if ($package->vehicle_id) {
                        $vehicle = $package->vehicle ?: Vehicle::find($package->vehicle_id);
                        if ($vehicle && !empty($vehicle->plate_number)) {
                            $targetVehicle = Vehicle::where('user_id', $targetUserId)
                                ->where('plate_number', $vehicle->plate_number)
                                ->first();
                            $targetVehicleId = $targetVehicle?->id;
                        }
                    }

                    $fillable = (new static)->getFillable();
                    $data = collect($package->only($fillable))
                        ->except(['user_id', 'slug', 'vehicle_id'])
                        ->toArray();

                    $data['badge_color'] = $data['badge_color'] ?: 'blue';
                    $data['price_label'] = $data['price_label'] ?: 'HARGA MULAI';
                    $data['duration']    = $data['duration'] ?: 'Full Day Tour (8 - 10 Jam)';
                    $data['capacity']    = $data['capacity'] ?: '15 Person';
                    $data['sort_order']  = $data['sort_order'] ?? 0;
                    $data['is_active']   = (bool) ($data['is_active'] ?? true);

                    if ($targetVehicleId !== null) {
                        $data['vehicle_id'] = $targetVehicleId;
                    } elseif ($package->vehicle_id === null) {
                        $data['vehicle_id'] = null;
                    }

                    if ($other) {
                        // Do not overwrite $other->slug to avoid unique constraint collisions
                        $other->update($data);
                    } else {
                        // Generate a unique slug for target user
                        $newSlug = $targetSlug;
                        $counter = 1;
                        while (static::where('slug', $newSlug)->exists()) {
                            $newSlug = $targetSlug . '-' . $counter++;
                        }

                        static::create(array_merge($data, [
                            'user_id' => $targetUserId,
                            'slug'    => $newSlug,
                        ]));
                    }
                }
            } finally {
                static::$isSyncing = false;
            }
        });

        static::deleted(function (TourPackage $package) {
            if (static::$isSyncing) return;

            $sharedUserIds = [3, 18];
            if (!in_array($package->user_id, $sharedUserIds, true)) return;

            $targetUserId = ($package->user_id === 3) ? 18 : 3;

            static::$isSyncing = true;
            try {
                if (!empty($package->slug)) {
                    $baseSlug = preg_replace('/-admin$/', '', $package->slug);
                    $targetSlug = ($targetUserId === 3) ? ($baseSlug . '-admin') : $baseSlug;

                    $other = static::where('user_id', $targetUserId)
                        ->where(function ($q) use ($package, $targetSlug, $baseSlug) {
                            $q->where('slug', $targetSlug)
                              ->orWhere('slug', $baseSlug)
                              ->orWhere('slug', $package->slug)
                              ->orWhere('title', $package->title);
                        })
                        ->first();

                    if ($other) {
                        $other->delete();
                    }
                }
            } finally {
                static::$isSyncing = false;
            }
        });
    }

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

    protected $attributes = [
        'badge_color' => 'blue',
        'price_label' => 'HARGA MULAI',
        'duration'    => 'Full Day Tour (8 - 10 Jam)',
        'capacity'    => '15 Person',
        'sort_order'  => 0,
        'is_active'   => true,
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
