<?php

namespace App\Imports;

use App\Models\Club;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ClubImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use SkipsFailures;

    public function model(array $row)
    {

        $skip = Club::where('club_code', $row['kode'])
                ->where('club_name', $row['nama'])
                ->exists();
        if($skip) return null;

        return new Club([
            'club_name' => $row['nama'],
            'club_code' => $row['kode'],
            'club_city' => $row['kota'],
            'club_province' => $row['provinsi'],
            'club_lead' => $row['penanggung_jawab'],
            'lead_phone' => $row['hp_penanggung_jawab'],
            'team_type' => $this->parseTeamType($row['jenis_klub']),
        ]);
    }

    public function rules(): array {
        return [
            'nama' => 'required|string|max:255',
            'kode' => 'required|string|max:255',
            'kota' => 'nullable|string|max:255',
            'provinsi' => 'nullable|string|max:255',
            'penanggung_jawab' => 'nullable|string|max:255',
            'hp_penanggung_jawab' => 'nullable|string|max:255',
            'jenis_klub' => 'required|in:Sekolah,Klub,Kota,Provinsi,Negara',
        ];
    }
    public function customValidationMessages(): array
    {
        return [
            'nama.required'  => 'Kolom nama wajib diisi.',
            'kode.required'  => 'Kolom kode wajib diisi.',
            'jenis_klub.in'  => 'Jenis klub yang diterima hanya Sekolah, klub, Kota, Provinsi, dan Negara.',
        ];
    }

    private function parseTeamType(?string $value): string
    {
        return match(strtolower(trim($value ?? ''))) {
            'sekolah' => 'SCHOOL',
            'klub' => 'CLUB',
            'kota' => 'CITY',
            'provinsi' => 'PROVINCE',
            'negara' => 'NATION',
            default => $value ?? ''
        };
    }
}
