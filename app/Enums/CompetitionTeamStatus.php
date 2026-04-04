<?php

namespace App\Enums;

enum CompetitionTeamStatus : string
{
    case Pending      = 'pending';
    case Active       = 'active';
    case Rejected     = 'rejected';
    case Withdrawn    = 'withdrawn';
    case Disqualified = 'disqualified';

    public function label(): string
    {
        return match($this) {
            self::Pending      => 'Menunggu Verifikasi',
            self::Active       => 'Aktif',
            self::Rejected     => 'Ditolak',
            self::Withdrawn    => 'Batal Tanding',
            self::Disqualified => 'Didiskualifikasi',
        };
    }

    public function class(): string
    {
        return match($this) {
            self::Pending      => 'bg-warning',
            self::Active       => 'bg-success',
            self::Rejected     => 'bg-danger',
            self::Withdrawn    => 'bg-secondary',
            self::Disqualified => 'bg-dark',
        };
    }
    public function icon(): string
    {
        return match($this) {
            self::Pending      => 'bi bi-hourglass-split',   // Menunggu verifikasi
            self::Active       => 'bi bi-patch-check-fill',  // Aktif / terverifikasi
            self::Rejected     => 'bi bi-x-octagon-fill',    // Ditolak
            self::Withdrawn    => 'bi bi-dash-circle-fill',  // Mengundurkan diri
            self::Disqualified => 'bi bi-slash-circle-fill', // Didiskualifikasi
        };
    }
}
