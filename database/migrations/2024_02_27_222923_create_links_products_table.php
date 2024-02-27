<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('link_products', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('link_id');
            $table->unsignedBigInteger('product_id');
            $table->timestamps();

            $table->foreign('link_id')->references('id')->on('links');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('link_products');
    }
};