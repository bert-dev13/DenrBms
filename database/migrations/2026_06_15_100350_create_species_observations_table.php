<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('species_observations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('protected_area_id')
                ->constrained('protected_areas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('site_id')
                ->constrained('site_names')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('species_id')
                ->constrained('species')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();

            $table->index(['protected_area_id', 'site_id'], 'idx_species_obs_pa_site');
            $table->index('species_id', 'idx_species_obs_species_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('species_observations');
    }
};
