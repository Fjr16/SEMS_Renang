<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionEntry extends Model
{
    protected $fillable = [
        'competition_team_id',
        'athlete_id',
        'competition_event_id',
        'is_relay',
        'entry_time',
        'seed_time',
        'status',
    ];

    public function competitionTeam(){
        return $this->belongsTo(CompetitionTeam::class);
    }
    public function athlete(){
        return $this->belongsTo(Athlete::class);
    }
    public function competitionEvent(){
        return $this->belongsTo(CompetitionEvent::class);
    }
    public function competitionEntryRelayMembers(){
        return $this->hasMany(CompetitionEntryRelayMember::class);
    }
}
