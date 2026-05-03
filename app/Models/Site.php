<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Site extends Model
{
    protected $table = 'site_names';

    protected $fillable = [
        'name',
        'protected_area_id',
    ];

    public function protectedArea(): BelongsTo
    {
        return $this->belongsTo(ProtectedArea::class);
    }

    public function speciesObservations(): HasMany
    {
        return $this->hasMany(SpeciesObservation::class);
    }
}
