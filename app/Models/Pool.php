<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pool extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'venue_id',
        'code',
        'name',
        'pool_role',
        'course_type', //SCM = 25m/LCM = 50m/SCY = 25yd
        'length_meter',
        'total_lanes',
        'depth',
        // 'is_available',
        'status',
    ];

    public function venue(){
        return $this->belongsTo(Venue::class);
    }
}

