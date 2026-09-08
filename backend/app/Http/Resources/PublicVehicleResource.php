<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicVehicleResource extends JsonResource
{
    /**
     * Transform the resource into a public read-only array.
     * Completely omits user_id, internal notes, financial metrics, and timestamps.
     */
    public function toArray(Request $request): array
    {
        $statusLabel = match ($this->status) {
            'available'   => 'Tersedia',
            'rented'      => 'Sedang Disewa',
            'maintenance' => 'Perawatan',
            default       => 'Tersedia',
        };

        return [
            'id'                   => $this->id,
            'name'                 => $this->name,
            'brand'                => $this->brand,
            'model_year'           => $this->model_year,
            'transmission'         => $this->transmission ?? 'matic',
            'transmission_label'   => ucfirst($this->transmission ?? 'matic'),
            'capacity'             => (int) ($this->capacity ?? 7),
            'fuel_type'            => $this->fuel_type ?? 'bensin',
            'fuel_type_label'      => ucfirst($this->fuel_type ?? 'bensin'),
            'daily_rate'           => (float) $this->daily_rate,
            'daily_rate_formatted' => 'Rp ' . number_format((float) $this->daily_rate, 0, ',', '.'),
            'status'               => $this->status,
            'status_label'         => $statusLabel,
            'color'                => $this->color ?? '#3B82F6',
            'photo_url'            => $this->photo_url,
            'gallery_photos'       => $this->gallery_photo_urls ?? [],
            'video_url'            => $this->video_url,
            'safe_video_embed_url' => $this->safe_video_embed_url,
            'description'          => $this->description,
            'is_featured'          => (bool) $this->is_featured,
        ];
    }
}
