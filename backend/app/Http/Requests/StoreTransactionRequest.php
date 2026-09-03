<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'type'        => 'required|in:income,expense',
            'amount'      => 'required|numeric|min:0.01',
            'date'        => 'required|date',
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)->orWhereNull('user_id');
                }),
            ],
            'account_id'  => [
                'required',
                Rule::exists('accounts', 'id')->where('user_id', $userId),
            ],
            'description' => 'nullable|string|max:500',
            'vehicle_id'  => [
                'nullable',
                Rule::exists('vehicles', 'id')->where('user_id', $userId),
            ],
            'receipt_path'=> [
                'nullable',
                'string',
                'max:255',
                // Strict regex: must strictly be receipts/{authenticated_user_id}/{uuid}.{ext}
                'regex:/^receipts\/' . $userId . '\/[a-f0-9\-]+\.(jpg|jpeg|png|webp)$/i',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'account_id.exists'    => 'Rekening yang dipilih tidak valid atau bukan milik akun Anda.',
            'category_id.exists'   => 'Kategori yang dipilih tidak valid atau tidak dapat diakses.',
            'receipt_path.regex'   => 'Format file struk tidak valid atau bukan milik akun Anda.',
        ];
    }
}
