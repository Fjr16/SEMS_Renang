<?php

namespace App\Enums;

enum CompetitionTeamPaymentStatus : string
{
    case Unpaid   = 'unpaid';
    case Paid     = 'paid';
    // case Refunded = 'refunded';

    public function label(): string
    {
        return match($this) {
            self::Unpaid   => 'Belum Bayar',
            self::Paid     => 'Lunas',
            // self::Refunded => 'Dikembalikan',
        };
    }
    public function class(): string
    {
        return match($this) {
            self::Paid   => 'bg-success',
            self::Unpaid     => 'bg-danger',
            // self::Refunded => 'bg-warning',
        };
    }
    public function icon(): string
    {
        return match($this) {
            self::Paid   => 'bi-check-circle',
            self::Unpaid     => 'bi-x-circle',
            // self::Refunded => 'bi-arrow-counterclockwise',
        };
    }
}
