<?php

namespace App\Helpers;

use Illuminate\Support\Str;

class CodeGenerator
{
    /**
     * Normalisasi jadi UPPERCASE, hanya A-Z0-9, tanpa spasi.
     */
    public static function normalize(string $value): string
    {
        $value = Str::upper($value);
        // Ubah non-alnum jadi spasi, lalu hapus spasi
        $value = preg_replace('/[^A-Z0-9]+/u', ' ', $value);
        $value = preg_replace('/\s+/u', '', trim($value));
        return $value ?: 'X';
    }

    /**
     * Ambil city prefix 3 huruf (fallback: XXX).
     * Kamu bisa ganti map sesuai kebutuhan (JKT, PDG, DPS, dll).
     */
    public static function cityPrefix(?string $city): string
    {
        if (!$city) return 'XXX';

        $c = self::normalize($city);

        // Map opsional (lebih “manusiawi”)
        $map = [
            'JAKARTA' => 'JKT',
            'PADANG'  => 'PDG',
            'DENPASAR'=> 'DPS',
            'BANDUNG' => 'BDG',
            'MEDAN'   => 'MDN',
            'SURABAYA'=> 'SBY',
        ];

        foreach ($map as $k => $v) {
            if (Str::contains($c, $k)) return $v;
        }

        return Str::padRight(substr($c, 0, 3), 3, 'X');
    }

    /**
     * Buat key dari nama venue, mis:
     * "Gelora Bung Karno Aquatic Center" -> "GBKAC"
     */
    public static function venueKey(string $name, int $maxLen = 10): string
    {
        $name = trim($name);

        // Ambil huruf awal tiap kata (maks 6 kata), contoh: GBKAC
        $words = preg_split('/\s+/u', $name) ?: [];
        $initials = '';
        foreach (array_slice($words, 0, 8) as $w) {
            $w = self::normalize($w);
            if ($w !== '') $initials .= substr($w, 0, 1);
        }

        // Jika terlalu pendek, fallback ambil substring normalisasi
        $base = self::normalize($initials ?: $name);

        return substr($base, 0, $maxLen) ?: 'VENUE';
    }

    /**
     * Generate venue code: CITY-KEY (contoh: JKT-GBKAC)
     */
    public static function makeVenueBaseCode(string $name, ?string $city = null): string
    {
        $cityCode = self::cityPrefix($city);
        $key = self::venueKey($name, 10);
        return "{$cityCode}-{$key}";
    }

    /**
     * Role singkat untuk pool.
     */
    public static function poolRoleShort(string $poolRole): string
    {
        $poolRole = Str::lower($poolRole);
        return match ($poolRole) {
            'competition' => 'COMP',
            'warmup'      => 'WARM',
            'training'    => 'TRN',
            'diving'      => 'DIV',
            default       => 'UNK',
        };
    }

    /**
     * Course type dibatasi SCM/LCM/SCY.
     */
    public static function normalizeCourse(string $courseType): string
    {
        $courseType = Str::upper(trim($courseType));
        return in_array($courseType, ['SCM', 'LCM', 'SCY'], true) ? $courseType : 'UNK';
    }

    /**
     * Generate pool code: VENUECODE-ROLE-COURSE-LANES
     * contoh: JKT-GBKAC-COMP-LCM-10
     */
    public static function makePoolBaseCode(
        string $venueCode,
        string $poolRole,
        string $courseType,
        int $lanes
    ): string {
        $venueCode = self::normalize(str_replace('-', '', $venueCode));
        // balikin ke format dengan dash sesuai venue (opsional). Cara simpel:
        // kalau venueCode awalnya "JKT-GBKAC", normalisasi di atas akan jadi "JKTGBKAC"
        // jadi kita terima venueCode as-is lebih aman:
        // -> lebih baik: jangan hilangkan dash, cukup sanitize:
        $venueCode = Str::upper(preg_replace('/[^A-Z0-9-]/', '', $venueCode));

        $role = self::poolRoleShort($poolRole);
        $course = self::normalizeCourse($courseType);
        $lanes = max(1, min(99, (int)$lanes));

        return "{$venueCode}-{$role}-{$course}-{$lanes}";
    }

    /**
     * Buat kode unik dengan suffix -2, -3, dst.
     *
     * @param callable $existsFn function(string $code): bool
     */
    public static function uniqueCode(string $baseCode, callable $existsFn, int $maxTry = 50): string
    {
        $baseCode = Str::upper($baseCode);

        if (!$existsFn($baseCode)) return $baseCode;

        for ($i = 2; $i <= $maxTry; $i++) {
            $candidate = "{$baseCode}-{$i}";
            if (!$existsFn($candidate)) return $candidate;
        }

        // Fallback kalau “tabrakan” terus
        return "{$baseCode}-" . Str::upper(Str::random(4));
    }
}
