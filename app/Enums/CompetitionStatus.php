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
            self::register => 'bg-success text-white',
            self::running => 'bg-secondary text-white',
            self::closed => 'bg-danger text-white'
        };
    }
}
