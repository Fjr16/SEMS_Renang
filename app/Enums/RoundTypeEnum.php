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

    public function background(): string {
        return match($this){
            self::prelim => "#E6F1FB",
            self::semi => "#FAEEDA",
            self::final => "#EAF3DE",
            self::timed_final => "#EAF3DE"
        };
    }

    public function color(): string {
        return match($this){
            self::prelim => "#0C447C",
            self::semi => "#633806",
            self::final => "#27500A",
            self::timed_final => "#27500A"
        };
    }
}
