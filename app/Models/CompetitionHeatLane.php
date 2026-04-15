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
    ];

    public function heat(){
        return $this->belongsTo(CompetitionHeat::class);
    }
    public function entry(){
        return $this->belongsTo(CompetitionEntry::class);
    }
}
