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
        Schema::table('site_names', function (Blueprint $table) {
            $table->unsignedBigInteger('protected_area_id')->nullable()->after('name');
            $table->foreign('protected_area_id')
                  ->references('id')
                  ->on('protected_areas')
                  ->onUpdate('cascade')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('site_names', function (Blueprint $table) {
            $table->dropForeign(['protected_area_id']);
            $table->dropColumn('protected_area_id');
        });
    }
};
