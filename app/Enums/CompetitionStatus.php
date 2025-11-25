<?php

namespace App\Enums;

enum CompetitionStatus:string
{
    case register = 'REGISTRATION';
    case running = 'RUNNING';
    case closed = 'CLOSED';

    public function label(){
        return match($this){
            self::register => 'Registrasi',
            self::running => 'Sedang Berjalan',
            self::closed => 'Ditutup'
        };
    }
    public function class(){
        return match($this){
            self::register => 'bg-primary text-white',
            self::running => 'bg-warning text-white',
            self::closed => 'bg-secondary text-white'
        };
    }
}
