<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'province',
        'country',
        'notes',
        'is_active',
    ];

    public function pools(){
        return $this->hasMany(Pool::class);
    }
}
