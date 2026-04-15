<?php

namespace App\Enums;

enum RoundTypeEnum :string
{
    case prelim = 'PRELIM';
    case semi = 'SEMI';
    case final = 'FINAL';
    case timed_final = 'TIMED_FINAL';


    public function label(): string {
        return match($this){
            self::prelim => "Penyisihan",
            self::semi => "Semi Final",
            self::final => "Final",
            self::timed_final => "Langsung Final"
        };
    }
}
