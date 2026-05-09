<?php

namespace App\Enums;

enum CompetitionResultStatus :string
{
    case valid = 'valid';
    case dns = 'dns';
    case dnf = 'dnf';
    case dq = 'dq';

    public function label(){
        return match($this){
            self::valid => 'VALID',
            self::dns => 'DNS (Did Not Start)',
            self::dnf => 'DNF (Did Not Finish)',
            self::dq => 'DQ (Disqualified)'
        };
    }
    public function shortLabel(){
        return match($this){
            self::valid => 'OK',
            self::dns => 'DNS',
            self::dnf => 'DNF',
            self::dq => 'DQ'
        };
    }
    public function styles(){
        return match($this){
            self::valid => 'border-color:#16A34A; color:#15803D; background:#DCFCE7',
            self::dns => 'border-color:#888;color:#5F5E5A;background:#F1EFE8',
            self::dnf => 'border-color:#854F0B;color:#854F0B;background:#FAEEDA',
            self::dq => 'border-color:#A32D2D;color:#A32D2D;background:#FCEBEB'
        };
    }
}
