<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Competition extends Model
{
    protected $fillable = [
        'name',
        'organizer',
        'start_date',
        'end_date',
        'location',
        'registration_start',
        'registration_end',
        'status',
    ];

    public function sessions(){
        return $this->hasMany(CompetitionSession::class);
    }

    // untuk membuat route model binding menampilkan parameter id yang di encrypt
    public function getRouteKey()
    {
        return encrypt($this->getKey());
    }

    public function resolveRouteBinding($value, $field = null)
    {
        try {
            $id = decrypt($value);
        } catch (DecryptException $e) {
            throw (new ModelNotFoundException)->setModel(self::class);
        }
        return $this->where($this->getKeyName(), $id)->firstOrFail();
    }
}
