<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;
    protected $fillable = [
        'club_role_category_id',
        'club_code',
        'club_name',
        'club_logo',
        'club_address',
        'club_province',
        'club_lead',
        'lead_phone'
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
