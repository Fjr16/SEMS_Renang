<?php

namespace App\Models;

use App\Enums\EventType;
use App\Enums\Gender;
use App\Enums\Stroke;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

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
        'limit_waktu'
    ];

    private static function generateEventNumber($event, bool $isUpdate = false): string
    {
        return DB::transaction(function () use ($event, $isUpdate) {
            $competitionId = $event->competitionSession->competition->id;

            $dayOrder = CompetitionSession::where('competition_id', $competitionId)
                ->select('session_date')
                ->distinct()
                ->orderBy('session_date')
                ->pluck('session_date')
                ->search($event->competitionSession->session_date);

            // search() return index (0-based), +1 untuk hari ke-1, ke-2, dst
            $dayNumber = $dayOrder + 1;
            $prefix = (string) $dayNumber;

            $sessionIdsOnSameDay = CompetitionSession::where('competition_id', $competitionId)
            ->where('session_date', $event->competitionSession->session_date)
            ->pluck('id');

            if ($isUpdate) {
                // Ambil semua nomor yang sudah ada dengan prefix sesi ini
                $existingNumbers = self::lockForUpdate()
                    ->whereIn('competition_session_id', $sessionIdsOnSameDay)
                    ->where('id', '!=', $event->id) // exclude diri sendiri
                    ->pluck('event_number')
                    ->map(fn($n) => (int) substr($n, strlen($prefix))) // ambil bagian belakang saja
                    ->sort()
                    ->values();

                // Cari gap: nomor yang belum terpakai mulai dari 1
                $nextNumber = 1;
                foreach ($existingNumbers as $num) {
                    if ($num == $nextNumber) {
                        $nextNumber++;
                    } else {
                        break; // ada gap, pakai nomor ini
                    }
                }
            } else {
                // Creating: cukup count + 1
                $total = self::lockForUpdate()
                    ->whereIn('competition_session_id', $sessionIdsOnSameDay)
                    ->count();
                $nextNumber = $total + 1;
            }

            return $prefix . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);
        });
    }

    protected static function booted()
    {
        static::creating(function ($event) {
            $event->event_number = self::generateEventNumber($event, false);
        });

        static::updating(function ($event) {
            if ($event->isDirty('competition_session_id')) {
                // Unset cache relasi agar reload dengan session_id yang baru
                unset($event->relations['competitionSession']);
                $event->event_number = self::generateEventNumber($event, true);
            }
        });
    }

    public function competitionSession(){
        return $this->belongsTo(CompetitionSession::class);
    }
    public function ageGroup(){
        return $this->belongsTo(AgeGroup::class);
    }
    public function entries(){
        return $this->hasMany(CompetitionEntry::class);
    }
    public function heats(){
        return $this->hasMany(CompetitionHeat::class);
    }
    public function configs(){
        return $this->hasMany(EventRoundConfig::class);
    }
    public function getLabel(){
        return 'Event ' . $this->event_number . ' - '
            . $this->distance . ' M '
            . ($this->stroke ? Stroke::from($this->stroke)->label() : '###') . ' • '
            . ($this->gender === 'mixed' ? 'Campuran' : Gender::from($this->gender)->label()) . ' • '
            . $this->ageGroup->label . ' / '
            . ($this->event_type ? EventType::from($this->event_type)->label() : '-')
        ;
    }
}
