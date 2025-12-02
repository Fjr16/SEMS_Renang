<?php

namespace App\Enums;

enum EventType:string
{
    case individual = 'individual';
    case estafet = 'relay';

    public function label(){
        return match ($this) {
            self::individual => 'Individual',
            self::estafet => 'Relay',
        };
    }
    public function class(){
        return match ($this) {
            self::individual => 'bg-primary text-white',
            self::estafet => 'bg-secondary text-white',
        };
    }
}
