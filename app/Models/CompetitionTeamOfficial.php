<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionTeamOfficial extends Model
{
    protected $fillable = [
        'competition_team_id',
        'official_id',
        'role_override',  //role official pada kompetisi ini, misal pelatih
    ];

    public function competitionTeam(){
        return $this->belongsTo(CompetitionTeam::class);
    }
    public function official(){
        return $this->belongsTo(Official::class);
    }
}
