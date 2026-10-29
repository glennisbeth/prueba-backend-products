<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductPriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $product = $this->route('product');

        return [
            'currency_id' => [
                'required',
                'integer',
                'exists:currencies,id',
                Rule::notIn([$product->currency_id]),
                Rule::unique('product_prices')->where('product_id', $product->id),
            ],
            // when missing, it is calculated from the base price and the exchange rates
            'price' => ['nullable', 'numeric', 'decimal:0,2', 'min:0', 'max:9999999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'currency_id.not_in' => 'This is the base currency of the product, its price is already set on the product.',
            'currency_id.unique' => 'The product already has a price registered in this currency.',
        ];
    }
}
