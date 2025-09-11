<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_collections', function (Blueprint $table) {
            $table->string('banner_video')->nullable()->after('banner_image');
        });
    }

    public function down(): void
    {
        Schema::table('product_collections', function (Blueprint $table) {
            $table->dropColumn(['banner_video']);
        });
    }
};
