<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('sku')->after('name')->nullable();
            $table->string('slug')->unique();
            $table->longText('short_description')->nullable();
            $table->longText('description')->nullable();
            $table->boolean('is_popular')->default(false);
            $table->integer('count')->default(0);
            $table->longText('synonims')->nullable();
            $table->foreignId('product_param_item_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('sku');
            $table->dropColumn('slug');
            $table->dropColumn('short_description');
            $table->dropColumn('description');
            $table->dropColumn('is_popular');
            $table->dropColumn('count');
            $table->dropColumn('synonims');

        });
    }
};
