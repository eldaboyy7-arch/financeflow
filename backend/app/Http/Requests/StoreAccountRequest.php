<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'            => 'required|string|max:100',
            'type'            => 'required|in:cash,bank,e_wallet,credit_card,other',
            'icon'            => 'nullable|string|max:10',
            'color'           => 'nullable|string|max:7',
            'opening_balance' => 'nullable|numeric',
            'is_active'       => 'nullable|boolean',
        ];
    }
}
