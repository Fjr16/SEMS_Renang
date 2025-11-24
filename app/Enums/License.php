<?php

namespace App\Enums;

enum License:string
{
    case A = "Level A International";
    case B = "Level B National";
    case C = "Level C Province";
    case D = "Level D City";
    case Z = "other";

    public function label() : string {
        return match($this) {
            self::A => 'Level A International',
            self::B => 'Level B National',
            self::C => 'Level C Province',
            self::D => 'Level D City',
            self::Z => 'Other',
        };
    }
    public function class() : string {
        return match($this) {
            self::A => 'bg-primary text-white',
            self::B => 'bg-danger text-white',
            self::C => 'bg-success text-white',
            self::D => 'bg-info text-white',
            self::Z => 'bg-secondary text-white',
        };
    }
}
