<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetitionEvent extends Model
{
    protected $fillable = [
        'competition_id',
        'competition_session_id',
        'age_group_id',
        'event_number', //format sesi_id + nomor event mulai dari 01
        'distance', //dalam meter
        'stroke',
        'gender',
        'event_type',
        'event_system',
        'remarks',
        // 'min_dob',
        // 'max_dob',
        'registration_fee',
    ];

    protected static function booted()
    {
        static::creating(function ($event){
            $lastRecord = self::lockForUpdate()
            ->where('competition_session_id', $event->competition_session_id)
            ->orderByDesc('id')
            ->first();

            $lastNumber = $lastRecord ? $lastRecord->event_number : null;
            $nextNumber = $lastNumber ? (int) $lastNumber + 1 : 1;

            $event->event_number = str_pad($nextNumber,2,'0',STR_PAD_LEFT);
        });
    }

    public function competition(){
        return $this->belongsTo(Competition::class);
    }
    public function competitionSession(){
        return $this->belongsTo(CompetitionSession::class);
    }
    public function ageGroup(){
        return $this->belongsTo(AgeGroup::class);
    }
}
