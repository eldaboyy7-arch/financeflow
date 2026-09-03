<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'              => $this->id,
            'name'            => $this->name,
            'type'            => $this->type?->value,
            'type_label'      => $this->type?->label(),
            'icon'            => $this->icon,
            'color'           => $this->color,
            'opening_balance' => (float) $this->opening_balance,
            'current_balance' => (float) $this->current_balance,
            'is_active'       => $this->is_active,
            'created_at'      => $this->created_at,
        ];
    }
}
