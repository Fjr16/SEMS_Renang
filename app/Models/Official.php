<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Official extends Model
{
    protected $fillable = [
        'club_id',
        'role',
        'foto',
        'name',
        'gender',
        'license',
    ];

    public function club() {
        return $this->belongsTo(Club::class);
    }

}
