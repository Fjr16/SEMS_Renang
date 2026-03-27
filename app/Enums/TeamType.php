<?php

namespace App\Enums;

enum TeamType : string
{
    case school = 'SCHOOL';
    case club = 'CLUB';
    case city = 'CITY';
    case province = 'PROVINCE';
    case nation = 'NATION';

    public function label(){
        return match ($this) {
            self::school => 'Sekolah',
            self::club => 'Klub',
            self::city => 'Kota',
            self::province => 'Provinsi',
            self::nation => 'Negara',
        };
    }
}
