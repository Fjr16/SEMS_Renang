<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionSession extends Model
{
    protected $fillable = [
        'competition_id',
        'name',
        'date',
        'start_time',
        'end_time',
    ];

    public function competition(){
        return $this->belongsTo(Competition::class);
    }
}
