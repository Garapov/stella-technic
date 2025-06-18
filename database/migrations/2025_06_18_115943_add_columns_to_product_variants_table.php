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
        Schema::table('product_variants', function (Blueprint $table) {
            $table->string('selected_width')->nullable();
            $table->string('selected_height')->nullable();
            $table->string('selected_desk_type')->nullable();
            $table->string('selected_position')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropColumn('selected_width');
            $table->dropColumn('selected_height');
            $table->dropColumn('selected_desk_type');
            $table->dropColumn('selected_position');
        });
    }
};
