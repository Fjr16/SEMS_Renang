<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionEntryRelayMember extends Model
{
    protected $fillable = [
        'competition_entry_id',
        'athlete_id',
        'leg_order',
        'status',
    ];

    public function competitionEntry(){
        return $this->belongsTo(CompetitionEntry::class);
    }
    public function athlete(){
        return $this->belongsTo(Athlete::class);
    }
}
