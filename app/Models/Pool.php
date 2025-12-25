<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pool extends Model
{
    protected $fillable = [
        'venue_id',
        'code',
        'name',
        'pool_role',
        'course_type', //SCM = 25m/LCM = 50m/SCY = 25yd
        'length_meter',
        'total_lanes',
        'is_available',
        'notes',
    ];
    
    public function venue(){
        return $this->belongsTo(Venue::class);
    }
}

