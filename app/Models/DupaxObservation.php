<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DupaxObservation extends BaseObservation
{
    use HasFactory;

    protected $table = 'dupax_tbl';
}
