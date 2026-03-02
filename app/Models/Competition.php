<?php

namespace App\Models;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;

class Competition extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'organization_id',
        'venue_id',
        'name', //Kejuaraan Renang Antar Klub DKI 2026
        'code', //JKT-OPEN-2026
        'description',
        'start_date',
        'end_date',
        'registration_start',
        'registration_end',
        'sanction_number',  //PRSI-2026-001
        'status',
    ];

    public function sessions(){
        return $this->hasMany(CompetitionSession::class);
    }
    public function events(){
        return $this->hasMany(CompetitionEvent::class);
    }

    public function organization(){
        return $this->belongsTo(Organization::class);
    }
    public function venue(){
        return $this->belongsTo(Venue::class);
    }


    protected static function booted()
    {
        static::creating(function ($comp) {
            $last = self::lockForUpdate()->orderByDesc('id')->first();
            $lastCode = $last ? $last->code : null;
            $next = $lastCode ? ((int) str_replace('COMP-','', $lastCode) + 1) : 1;

            $comp->code = 'COMP-' . str_pad($next, 2, '0', STR_PAD_LEFT);
        });
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
