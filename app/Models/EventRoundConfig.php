<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventRoundConfig extends Model
{
    protected $fillable = [
        'competition_event_id',
        'round_type',
        'used_lanes',
        'qualify_count',
        'order',
    ];

    public function event(){
        return $this->belongsTo(CompetitionEvent::class);
    }
}
