<?php

namespace App\Http\Requests;

class UpdateProductRequest extends StoreProductRequest
{
    /**
     * The same rules as when creating, but allowing you to send only the fields to be modified.
     */
    public function rules(): array
    {
        return collect(parent::rules())
            ->map(fn(array $rules) => ['sometimes', ...$rules])
            ->all();
    }
}
