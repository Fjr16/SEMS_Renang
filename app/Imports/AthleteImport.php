<?php

namespace App\Imports;

use App\Models\Athlete;
use App\Models\Club;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class AthleteImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, SkipsEmptyRows
{
    use SkipsFailures;

    public function prepareForValidation($data, $index)
    {
        if (!empty($data['tanggal_lahir']) && is_numeric($data['tanggal_lahir'])) {
            $data['tanggal_lahir'] = Carbon::instance(
                Date::excelToDateTimeObject($data['tanggal_lahir'])
            )->format('Y-m-d');
        }

        return $data;
    }

    public function model(array $row)
    {
        $club = Club::where('club_code', $row['kode_klub'])->first();
        $skip = Athlete::where('name', $row['nama'])
                ->where('bod', $row['tanggal_lahir'])
                ->where('gender', $this->parseGender($row['jenis_kelamin']))
                ->exists();
        if($skip) return null;

        return new Athlete([
            'club_id' => $club->id,
            'code' => $row['kode_klub'],
            'name' => $row['nama'],
            'bod' => $row['tanggal_lahir'],
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
