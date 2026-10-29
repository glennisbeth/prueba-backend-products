<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductPriceRequest;
use App\Http\Resources\ProductPriceResource;
use App\Models\Currency;
use App\Models\Product;
use App\Services\CurrencyConverter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ProductPriceController extends Controller
{
    public function index(Product $product): AnonymousResourceCollection
    {
        return ProductPriceResource::collection(
            $product->prices()->with('currency')->get()
        );
    }

    public function store(StoreProductPriceRequest $request, Product $product, CurrencyConverter $converter): JsonResponse
    {
        $currency = Currency::findOrFail($request->validated('currency_id'));
        
        //If the price is not provided, it is calculated
        $price = $request->validated('price')
            ?? $converter->convert($product->price, $product->currency, $currency);

        $productPrice = $product->prices()->create([
            'currency_id' => $currency->id,
            'price' => $price,
        ]);

        return ProductPriceResource::make($productPrice->load('currency'))
            ->response()
            ->setStatusCode(Response::HTTP_CREATED);
    }
}
