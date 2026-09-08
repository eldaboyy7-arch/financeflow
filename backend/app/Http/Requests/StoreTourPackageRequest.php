<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTourPackageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vehicle_id'        => 'nullable|exists:vehicles,id',
            'title'             => 'required|string|max:255',
            'slug'              => 'nullable|string|max:255|unique:tour_packages,slug',
            'subtitle'          => 'nullable|string|max:255',
            'badge'             => 'nullable|string|max:50',
            'badge_color'       => 'required|in:blue,indigo,amber,emerald',
            'price'             => 'required|numeric|min:0',
            'price_label'       => 'nullable|string|max:50',
            'duration'          => 'nullable|string|max:100',
            'capacity'          => 'nullable|string|max:50',
            'vehicle_name'      => 'nullable|string|max:100',
            'description'       => 'nullable|string',
            'tour_route'        => 'nullable|string|max:1000',
            'cover_photo_path'  => 'nullable|string|max:500',
            'gallery_photos'    => 'nullable|array',
            'gallery_photos.*'  => 'nullable',
            'facilities'        => 'nullable|array',
            'facilities.*'      => 'nullable|string|max:150',
            'itinerary'         => 'nullable|array',
            'itinerary.*'       => 'nullable|string|max:255',
            'included'          => 'nullable|array',
            'included.*'        => 'nullable|string|max:255',
            'excluded'          => 'nullable|array',
            'excluded.*'        => 'nullable|string|max:255',
            'cta_whatsapp_text' => 'nullable|string|max:500',
            'sort_order'        => 'nullable|integer|min:0',
            'is_active'         => 'nullable|boolean',
        ];
    }
}
