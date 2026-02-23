<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;
    protected $fillable = [
        'club_role_category_id',
        'club_name',
        'club_code',
        'club_city',
        'club_province',
        'club_lead',
        'lead_phone',
        'team_type',
        'club_logo'
    ];

    public function clubRoleCategory() {
        return $this->belongsTo(ClubRoleCategory::class);
    }
    public function athletes(){
        return $this->hasMany(Athlete::class);
    }
    public function officials(){
        return $this->hasMany(Official::class);
    }
}
