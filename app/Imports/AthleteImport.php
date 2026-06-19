<?php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\Club;
use Carbon\Carbon;
use Carbon\Traits\Date;
use Maatwebsite\Excel\Concerns\ToModel;

class AthleteImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public function model(array $row)
    {
        $club = Club::where('club_code', $row['kode_klub'])->first();
        return new Athlete([
            'club_id' => $club->id,
            'code' => $row['kode_klub'],
            'name' => $row['nama'],
            'bod' => $this->parseDate($row['tanggal_lahir']),
            'gender' => $this->parseGender($row['jenis_kelamin']),
            'status' => $this->parseStatus($row['status']),
            'kota' => $row['kota'],
            'provinsi' => $row['provinsi'],
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_klub' => ['required','exists:clubs,club_code'],
            'nama' => ['required', 'string', 'max:255'],
            'tanggal_lahir' => ['required', 'date', 'before_or_equal:today'],
            'jenis_kelamin' => ['required', 'in:Pria,Wanita'],
            'status' => ['required','in:Aktif,Nonaktif'],
            'kota' => ['nullable','string','max:50'],
            'provinsi' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'kode_klub.required'  => 'Kolom kode_klub wajib diisi.',
            'kode_klub.exists'    => 'kode klub ":input" tidak ditemukan di database.',
            'nama.required'     => 'Kolom nama wajib diisi.',
            'tanggal_lahir.required'      => 'Kolom tanggal_lahir wajib diisi.',
            'tanggal_lahir.date'          => 'Format tanggal tanggal_lahir tidak valid.',
            'tanggal_lahir.before_or_equal' => 'Tanggal lahir tidak boleh lebih dari hari ini.',
            'jenis_kelamin.required'   => 'Kolom jenis_kelamin wajib diisi.',
            'jenis_kelamin.in'         => 'Jenis_kelamin harus Pria atau Wanita.',
            'status.in'         => 'Status harus Aktif atau Nonaktif.',
        ];
    }

    private function parseDate($value): ?string
    {
        if (empty($value)) return null;

        // Angka serial dari Excel (misal: 34714)
        if (is_numeric($value)) {
            return Carbon::instance(Date::excelToDateTimeObject($value))->format('Y-m-d');
        }

        // String biasa: "15/01/1995" atau "1995-01-15"
        return Carbon::parse($value)->format('Y-m-d');
    }

    private function parseGender(?string $value): string
    {
        return match(strtolower(trim($value ?? ''))) {
            'pria', 'laki-laki', 'l' => 'male',
            'wanita', 'perempuan', 'p' => 'female',
            default => $value ?? ''
        };
    }

    private function parseStatus(?string $value): string
    {
        return match(strtolower(trim($value ?? ''))) {
            'aktif', 'active'      => 'active',
            'nonaktif', 'inactive' => 'inactive',
            default                => 'active' // fallback default
        };
    }
}
