<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionEvent extends Model
{
    protected $fillable = [
        'competition_session_id',
        'age_group_id',
        'event_number', //format sesi_id + nomor event mulai dari 01
        'distance', //dalam meter
        'stroke',
        'gender',
        'event_type',
        'max_relay_athletes',
        'registration_fee',
    ];

    protected static function booted()
    {
        static::creating(function ($event){
            $total = self::lockForUpdate()
            ->where('competition_session_id', $event->competition_session_id)
            ->count();
            $nextNumber = $total + 1;

            $event->event_number = $event->competitionSession->session_order . str_pad($nextNumber,2,'0',STR_PAD_LEFT);
        });
    }

    public function competitionSession(){
        return $this->belongsTo(CompetitionSession::class);
    }
    public function ageGroup(){
        return $this->belongsTo(AgeGroup::class);
    }
}
