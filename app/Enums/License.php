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
}
