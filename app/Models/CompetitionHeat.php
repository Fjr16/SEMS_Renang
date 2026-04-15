<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionHeat extends Model
{
    protected $fillable = [
        'competition_event_id',
        'round_type',//prelim,semi,final,timed_final
        'heat_number',
    ];

    public function event(){
        return $this->belongsTo(CompetitionEvent::class);
    }
    public function heatLanes(){
        return $this->hasMany(CompetitionHeatLane::class);
    }
}
