<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransferResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'amount'       => (float) $this->amount,
            'fee'          => (float) $this->fee,
            'date'         => $this->date?->format('Y-m-d'),
            'description'  => $this->description,
            'from_account' => new AccountResource($this->whenLoaded('fromAccount')),
            'to_account'   => new AccountResource($this->whenLoaded('toAccount')),
            'created_at'   => $this->created_at,
        ];
    }
}
