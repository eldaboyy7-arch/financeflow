<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBudgetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->user()?->id;

        return [
            'category_id' => [
                'required',
                Rule::exists('categories', 'id')->where(function ($query) use ($userId) {
                    $query->where('user_id', $userId)->orWhereNull('user_id');
                }),
            ],
            'amount'      => 'required|numeric|min:1000',
            'month'       => 'required|integer|between:1,12',
            'year'        => 'required|integer|between:2020,2030',
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak valid atau bukan milik akun Anda.',
            'amount.required'      => 'Nominal budget wajib diisi.',
            'amount.min'           => 'Nominal budget minimal Rp1.000.',
            'month.required'       => 'Bulan wajib dipilih.',
            'year.required'        => 'Tahun wajib diisi.',
        ];
    }
}
