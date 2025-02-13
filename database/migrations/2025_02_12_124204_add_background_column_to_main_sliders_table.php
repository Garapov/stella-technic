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
        Schema::table('main_sliders', function (Blueprint $table) {
            $table->string('background')->after('link')->default('#ffffff');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('main_sliders', function (Blueprint $table) {
            $table->dropColumn('background');
        });
    }
};
