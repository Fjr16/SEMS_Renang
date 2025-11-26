<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    protected $fillable = [
        'name',
        'organizer',
        'start_date',
        'end_date',
        'location',
        'registration_start',
        'registration_end',
        'status',
    ];

    public function sessions(){
        return $this->hasMany(CompetitionSession::class);
    }
}
