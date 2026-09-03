<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'type'       => $this->type?->value,
            'icon'       => $this->icon,
            'color'      => $this->color,
            'is_default' => $this->is_default,
            'is_rental'  => (bool) $this->is_rental,
            'user_id'    => $this->user_id,
        ];
    }
}
