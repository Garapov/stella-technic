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
        Schema::create('cross_sells', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->foreignId('related_variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['product_variant_id', 'related_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cross_sells');
    }
};
