<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TourPackageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                 => $this->id,
            'user_id'            => $this->user_id,
            'vehicle_id'         => $this->vehicle_id,
            'vehicle'            => $this->whenLoaded('vehicle'),
            'title'              => $this->title,
            'slug'               => $this->slug,
            'subtitle'           => $this->subtitle,
            'badge'              => $this->badge,
            'badge_color'        => $this->badge_color ?? 'blue',
            'price'              => (float) $this->price,
            'formatted_price'    => $this->formatted_price,
            'price_label'        => $this->price_label ?? 'HARGA MULAI',
            'duration'           => $this->duration,
            'capacity'           => $this->capacity,
            'vehicle_name'       => $this->vehicle_name,
            'description'        => $this->description,
            'tour_route'         => $this->tour_route,
            'cover_photo_path'   => $this->cover_photo_path,
            'cover_photo_url'    => $this->cover_photo_url,
            'gallery_photos'     => $this->gallery_photos ?? [],
            'gallery_photo_urls' => $this->gallery_photo_urls,
            'facilities'         => $this->facilities ?? [],
            'itinerary'          => $this->itinerary ?? [],
            'included'           => $this->included ?? [],
            'excluded'           => $this->excluded ?? [],
            'cta_whatsapp_text'  => $this->cta_whatsapp_text,
            'sort_order'         => (int) ($this->sort_order ?? 0),
            'is_active'          => (bool) $this->is_active,
            'created_at'         => $this->created_at?->toIso8601String(),
            'updated_at'         => $this->updated_at?->toIso8601String(),
        ];
    }
}
