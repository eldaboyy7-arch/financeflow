<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'type'         => $this->type?->value,
            'amount'       => (float) $this->amount,
            'date'         => $this->date?->format('Y-m-d'),
            'description'  => $this->description,
            'receipt_path' => $this->receipt_path,
            'receipt_url'  => $this->receipt_path ? url("/api/transactions/{$this->id}/receipt") : null,
            'transfer_id'  => $this->transfer_id,
            'account'      => new AccountResource($this->whenLoaded('account')),
            'category'     => new CategoryResource($this->whenLoaded('category')),
            'created_at'   => $this->created_at,
        ];
    }
}
