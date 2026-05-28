<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionHeatLane extends Model
{
    protected $fillable = [
        'competition_heat_id',
        'competition_entry_id',
        'lane_number',
        'lane_order',
        'swim_time',
        'status',
        'rank_in_heat',
        'record_type',
    ];

    public function heat(){
        return $this->belongsTo(CompetitionHeat::class);
    }
    public function entry(){
        return $this->belongsTo(CompetitionEntry::class,'competition_entry_id', 'id');
    }
}
