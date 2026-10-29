<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('currency_id')->constrained()->restrictOnDelete();
            $table->decimal('price', 12, 2);
            $table->timestamps();

            // one currency price per product
            $table->unique(['product_id', 'currency_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};