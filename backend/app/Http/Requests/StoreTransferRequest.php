<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'from_account_id' => [
                'required',
                'different:to_account_id',
                Rule::exists('accounts', 'id')->where('user_id', $userId),
            ],
            'to_account_id'   => [
                'required',
                Rule::exists('accounts', 'id')->where('user_id', $userId),
            ],
            'amount'          => 'required|numeric|min:0.01',
            'fee'             => 'nullable|numeric|min:0',
            'date'            => 'required|date',
            'description'     => 'nullable|string|max:500',
        ];
    }

    public function messages(): array
    {
        return [
            'from_account_id.exists'    => 'Rekening asal tidak valid atau bukan milik akun Anda.',
            'to_account_id.exists'      => 'Rekening tujuan tidak valid atau bukan milik akun Anda.',
            'from_account_id.different' => 'Rekening asal dan tujuan tidak boleh sama.',
        ];
    }
}
