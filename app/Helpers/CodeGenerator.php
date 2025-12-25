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

    public static function normalizeWords(string $value): string
    {
        $value = Str::upper($value);
        $value = preg_replace('/[^A-Z0-9]+/u', ' ', $value);
        $value = trim(preg_replace('/\s+/u', ' ', $value));
        return $value ?: 'X';
    }

    /**
     * Ambil city prefix 3 huruf (fallback: XXX).
     */

    public static function cityPrefix(?string $city): string
    {
        if (blank($city)) return 'XXX';

        $c = self::normalizeWords($city);

        // 2) buang stopword administratif (unicode-safe)
        // pakai \s+ pengganti agar tidak nempel
        $c = preg_replace('/\b(KOTA|KAB|KEP|PROV|KABUPATEN|PROVINSI|DAERAH|ADM|KEPULAUAN|DKI|DI)\b/u', ' ', $c);
        $c = trim(preg_replace('/\s+/u', ' ', $c));

        if ($c === '') return 'XXX';

        // 2) mapping khusus (opsional, cukup yang benar-benar perlu)
        $special = [
            'JAKARTA' => 'JKT',
            'YOGYAKARTA' => 'DIY',
            'DENPASAR' => 'DPS',
            'SURABAYA' => 'SBY',
            'BANDUNG' => 'BDG',
            'PADANG' => 'PDG',
            'MEDAN' => 'MDN',
        ];
        foreach ($special as $k => $v) {
            if (Str::contains($c, $k)) return $v;
        }

        // 3) fallback cerdas: ambil inisial kata sampai 3 huruf
        $words = array_values(array_filter(explode(' ', $c)));

        // kalau banyak kata, ambil huruf depan tiap kata (maks 3)
        if (count($words) >= 2) {
            $prefix = '';
            foreach ($words as $w) {
                $prefix .= substr($w, 0, 1);
                if (strlen($prefix) >= 3) break;
            }
            return Str::padRight($prefix, 3, 'X');
        }

        // kalau satu kata, ambil 3 huruf pertama
        return Str::padRight(substr($words[0], 0, 3), 3, 'X');
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
        $venueCode = Str::upper(preg_replace('/[^A-Z0-9-]/', '', $venueCode));

        $course = self::normalizeCourse($courseType);
        $lanes = max(1, min(12, (int)$lanes));

        // return "{$venueCode}-{$role}-{$course}-{$lanes}";
        return "{$venueCode}-{$course}-{$lanes}";
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
