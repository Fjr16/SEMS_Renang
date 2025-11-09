<?php

namespace App\Enums;

enum Gender:string
{
    case pria = 'male';
    case wanita = 'female';


    public function label(): string {
        return match($this){
            self::pria => "MALE",
            self::wanita => "FEMALE"
        };
    }

    public function class():string {
        return match ($this) {
            self::pria => 'bg-primary text-white',
            self::wanita => 'bg-danger text-white',
        };
    }
}
