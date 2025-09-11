<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_collections', function (Blueprint $table) {
            $table->string('heading')->nullable()->after('name');
            $table->text('description')->nullable()->after('heading');
        });
    }

    public function down(): void
    {
        Schema::table('product_collections', function (Blueprint $table) {
            $table->dropColumn(['heading', 'description']);
        });
    }
};
