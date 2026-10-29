<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:2000'],
            'price' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999.99'],
            'currency_id' => ['required', 'integer', 'exists:currencies,id'],
            'tax_cost' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999.99'],
            'manufacturing_cost' => ['required', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999.99'],
        ];
    }
}