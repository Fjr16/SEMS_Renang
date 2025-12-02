<?php

namespace App\Enums;

enum Stroke:string
{
    case gaya_bebas = "freestyle";
    case gaya_dada = 'breast stroke';
    case gaya_kupu = 'butterfly stroke';
    case gaya_punggung = 'back stroke';
    case gaya_campuran = 'medley stroke';

    public function label(){
        return match ($this) {
            self::gaya_bebas => 'Freestyle',
            self::gaya_dada => 'Breast Stroke',
            self::gaya_kupu => 'Butterfly Stroke',
            self::gaya_punggung => 'Back Stroke',
            self::gaya_campuran => 'Medley Stroke',
        };
    }

    public function class(){
        return match ($this){
            self::gaya_bebas => 'bg-primary text-white',
            self::gaya_dada => 'bg-info text-white',
            self::gaya_kupu => 'bg-success text-white',
            self::gaya_punggung => 'bg-warning text-white',
            self::gaya_campuran => 'bg-danger text-white',
        };
    }
}
