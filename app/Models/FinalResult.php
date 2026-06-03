<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinalResult extends Model
{
    protected $fillable = [
        'competition_id',
        'competition_event_id',
        'competition_entry_id',
        'competition_heat_lane_id',
        'is_relay',
        'athlete_id',
        'competition_team_id',
        'entry_time',
        'swim_time',
        'rank_in_event',
        'status',
        'round_type',
    ];

    public function competition()
    {
        return $this->belongsTo(Competition::class);
    }

    public function event()
    {
        return $this->belongsTo(CompetitionEvent::class);
    }

    public function entry()
    {
        return $this->belongsTo(CompetitionEntry::class);
    }

    public function heatLane()
    {
        return $this->belongsTo(CompetitionHeatLane::class);
    }

    public function athlete()
    {
        return $this->belongsTo(Athlete::class);
    }

    public function team()
    {
        return $this->belongsTo(CompetitionTeam::class);
    }

    public function relayEntryMember()
    {
        return $this->belongsTo(CompetitionEntryRelayMember::class);
    }
}
