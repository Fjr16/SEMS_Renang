<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionEntry extends Model
{
    protected $fillable = [
        'athlete_id',
        'competition_event_id',
        'seed_time',
        'status',
        'feedback'
    ];

    public function athletes(){
        return $this->hasMany(Athlete::class);
    }
    public function competitionEvents(){
        return $this->hasMany(CompetitionEvent::class);
    }
}
