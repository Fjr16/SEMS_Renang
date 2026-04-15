<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionResult extends Model
{
    protected $fillable = [
        'competition_heat_lane_id',
        'reaction_time',
        'swim_time',
        'status',
        'rank_in_heat',
        'rank_overral',
        'points',
        'record_type'
    ];

    public function heatLane(){
        return $this->belongsTo(CompetitionHeatLane::class);
    }
}
