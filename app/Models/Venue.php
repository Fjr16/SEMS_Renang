<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Venue extends Model
{
    use SoftDeletes, HasFactory;
    protected $fillable = [
        'code',
        'name',
        'address',
        'city',
        'province',
        'country',
        'is_active',
    ];

    public function pools(){
        return $this->hasMany(Pool::class);
    }
}
