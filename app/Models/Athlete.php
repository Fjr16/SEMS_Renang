<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Athlete extends Model
{
    protected $fillable = [
        'club_id',
        'code',
        'foto',
        'name',
        'bod',
        'gender',
        'school_name',
        'club_name',
        'city_name',
        'province_name',
        // 'prsi_id',
    ];

    public function club() {
        return $this->belongsTo(Club::class);
    }
}
