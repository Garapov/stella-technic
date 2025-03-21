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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('image')->nullable();
            $table->string('slug')->unique();
            $table->longText('gallery')->nullable();
            $table->longText('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->integer('price')->default(0);
            $table->integer('new_price')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->integer('count')->default(0);
            $table->longText('synonims')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
