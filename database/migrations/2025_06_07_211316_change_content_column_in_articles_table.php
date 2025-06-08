<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("articles", function (Blueprint $table) {
            $table->json("content")->change();
            $table->text("short_content")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("articles", function (Blueprint $table) {
            // $table->dropColumn("content");
            $table->dropColumn("short_content");
        });
    }
};
