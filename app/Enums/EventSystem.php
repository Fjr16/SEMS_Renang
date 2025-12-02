<?php

namespace App\Enums;

enum EventSystem:string
{
    case final = 'final';
    case penyisihan = 'penyisihan';

    public function label() {
        return match ($this){
            self::final => 'Final',
            self::penyisihan => 'Penyisihan',
        };
    }
    public function class() {
        return match ($this){
            self::final => 'bg-primary text-white',
            self::penyisihan => 'bg-secondary text-white',
        };
    }
}
