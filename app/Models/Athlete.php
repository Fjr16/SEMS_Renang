<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class Athlete extends Model
{
    use HasFactory;
    protected $fillable = [
        'club_id',
        'code',
        'foto',
        'name',
        'bod',
        'gender',
        'school_name',
        'club_name',
        'city_name',
        'province_name',
        // 'prsi_id',
    ];

    protected static function booted()
    {
        static::creating(function ($athlete) {
            $last = self::lockForUpdate()->orderByDesc('id')->first();
            $lastCode = $last ? $last->code : null;
            $next = $lastCode ? ((int) str_replace('ATH-','', $lastCode) + 1) : 1;

            $athlete->code = 'ATH-' . str_pad($next, 2, '0', STR_PAD_LEFT);
        });
        static::updated(function ($athlete) {
            if ($athlete->wasChanged('foto')) {
                $old = $athlete->getOriginal('foto');
                if ($old) {
                    DB::afterCommit(function () use ($old) {
                        Storage::disk('public')->delete($old);
                    });
                }
            }
        });
        static::deleting(function ($athlete) {
            $old = $athlete->foto;

            if ($old) {
                DB::afterCommit(function () use ($old) {
                    Storage::disk('public')->delete($old);
                });
            }
        });
    }

    public function club() {
        return $this->belongsTo(Club::class);
    }
}
