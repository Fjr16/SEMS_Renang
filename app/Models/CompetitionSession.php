<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionSession extends Model
{
    protected $fillable = [
        'competition_id',
        'pool_id',
        'name',
        'session_order',
        'session_date',
    ];

    public function pool(){
        return $this->belongsTo(Pool::class);
    }
    public function competition(){
        return $this->belongsTo(Competition::class);
    }
    public function competitionEvents(){
        return $this->hasMany(CompetitionEvent::class);
    }
}
