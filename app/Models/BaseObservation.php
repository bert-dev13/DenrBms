<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseObservation extends Model
{
    protected $fillable = [
        'protected_area_id',
        'transaction_code',
        'station_code',
        'patrol_year',
        'patrol_semester',
        'bio_group',
        'common_name',
        'scientific_name',
        'recorded_count',
    ];

    protected $casts = [
        'patrol_year' => 'integer',
        'patrol_semester' => 'integer',
        'recorded_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getPatrolSemesterTextAttribute()
    {
        return $this->patrol_semester == 1 ? '1st' : '2nd';
    }

    public function protectedArea()
    {
        return $this->belongsTo(ProtectedArea::class);
    }
}
