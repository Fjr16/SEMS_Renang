<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventResult extends Model
{
    protected $fillable = [
        'competition_event_id',
        'competition_entry_id',
        'round_type',
        'rank_overral',
        'time_in_cs',
        'time_display',
        'points',
    ];

    public function competitionEvent(){
        return $this->belongsTo(CompetitionEvent::class);
    }
    public function competitionEntry(){
        return $this->belongsTo(CompetitionEntry::class);
    }
}
