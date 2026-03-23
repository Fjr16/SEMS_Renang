<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompetitionTeam extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'competition_id',
        'team_id',
        'status',
        'total_fee',
        'payment_status',
    ];

    public function competition(){
        return $this->belongsTo(Competition::class);
    }
    public function team(){
        return $this->belongsTo(Club::class,'team_id', 'id');
    }
    public function competitionEntries(){
        return $this->hasMany(CompetitionEntry::class);
    }
    public function competitionTeamOfficials(){
        return $this->hasMany(CompetitionTeamOfficial::class);
    }
}
