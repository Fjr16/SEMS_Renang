<?php

namespace App\Enums;

enum CompetitionTeamEntryStatus : string
{
    case Pending      = 'pending';
    case Active       = 'active';
    case Scratched    = 'scratched';
    case Withdrawn    = 'withdrawn';
    case Disqualified = 'disqualified';

    public function label(): string
    {
        return match($this) {
            self::Pending      => 'Menunggu Verifikasi',
            self::Active       => 'Aktif',
            self::Scratched    => 'Scratch', //ketika entry sebelumnya diganti dengan entry yang berbeda maka otomatis jadi scratch atau soft delet
            self::Withdrawn    => 'Mengundurkan Diri',
            self::Disqualified => 'Didiskualifikasi',
        };
    }
}
