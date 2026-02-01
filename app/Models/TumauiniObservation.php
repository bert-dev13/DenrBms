<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TumauiniObservation extends BaseObservation
{
    use HasFactory;

    protected $table = 'tumauini_tbl';
}
