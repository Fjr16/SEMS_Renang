<?php

namespace App\Enums;

enum RecordTypeEnum :string
{
    case personal_best = "pb";
    case meet_record = "mr";
    case regional_record = "reg";
    case national_record = "nas";

    public function longLabel():string{
        return match($this){
            self::personal_best => 'PB (Personal Best)',
            self::meet_record => 'MR (Meet Record)',
            self::regional_record => 'Reg (Regional Record)',
            self::national_record => 'Nas (Nasional Record)'
        };
    }
    public function shortLabel():string{
        return match($this){
            self::personal_best => 'PB',
            self::meet_record => 'MR',
            self::regional_record => 'Reg',
            self::national_record => 'Nas'
        };
    }
}
