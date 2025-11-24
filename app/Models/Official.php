<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Official extends Model
{
    protected $fillable = [
        'club_id',
        'foto',
        'name',
        'gender',
        'license',
        // 'current_club',
        'current_city',
        'current_province',
        // 'certificate',
    ];

    public function club() {
        return $this->belongsTo(Club::class);
    }

}
