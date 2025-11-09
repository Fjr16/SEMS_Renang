<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubRoleCategory extends Model
{
    protected $fillable = [
        'code',
        'name'
    ];

    public function clubs(){
        return $this->hasMany(Club::class);
    }
}
