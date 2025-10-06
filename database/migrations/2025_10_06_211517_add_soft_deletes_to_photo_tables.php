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
        Schema::table('order_photos', function (Blueprint $table) {
            $table->softDeletes();
        });
        
        Schema::table('pallet_photos', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_photos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
        
        Schema::table('pallet_photos', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
