<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\DataValidation;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AthleteTemplate implements
FromArray,
WithHeadings,
WithColumnFormatting,
WithColumnWidths,
WithStyles,
WithEvents
{
    private string $lastColumn  = 'G';
    private int    $dataRows    = 100;

    public function headings() : array
    {
        return [
            'kode_klub', 'nama', 'tanggal_lahir', 'jenis_kelamin',
            'status', 'kota', 'provinsi'
        ];
    }

    public function array() : array
    {
        return [];
    }

    public function columnFormats() : array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 16,
            'D' => 12,
            'E' => 12,
            'F' => 18,
            'G' => 20,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $lastData = $this->dataRows + 1;
        return [
            1 => [
                'font' => [
                    'bold'  => true,
                    'color' => ['argb' => 'FFFFFFFF'],
                    'size'  => 11,
                ],
                'fill' => [
                    'fillType'   => Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E2D6B'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],

            "A2:{$this->lastColumn}" . ($lastData) => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical'   => Alignment::VERTICAL_CENTER,
                ],
            ],

            "C2:C" . ($lastData) => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],

            "D2:E" . ($lastData) => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastData = $this->dataRows + 1;

                // Freeze baris heading agar tidak ikut scroll
                $sheet->freezePane('A2');

                // Tinggi baris heading
                $sheet->getRowDimension(1)->setRowHeight(28);

                // Tinggi baris data
                for ($i = 2; $i <= $lastData; $i++) {
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }

                // Border seluruh tabel
                $sheet->getStyle("A1:{$this->lastColumn}{$lastData}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_THIN)
                    ->getColor()
                    ->setARGB('FFD0D5E8');

                // Border heading lebih tebal
                $sheet->getStyle("A1:{$this->lastColumn}1")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(Border::BORDER_MEDIUM)
                    ->getColor()
                    ->setARGB('FF1E2D6B');

                // Kunci format tanggal di kolom C agar tidak bisa diubah user
                $sheet->getStyle('C2:C'. ($lastData))
                    ->getNumberFormat()
                    ->setFormatCode('DD/MM/YYYY');

                //data validation
                $dateValidation = $sheet->getCell('C2')->getDataValidation();
                $dateValidation->setType(DataValidation::TYPE_DATE);
                $dateValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $dateValidation->setOperator(DataValidation::OPERATOR_BETWEEN);
                $dateValidation->setFormula1('DATE(1950,1,1)');
                $dateValidation->setFormula2('TODAY()');

                $dateValidation->setAllowBlank(false);

                $dateValidation->setShowInputMessage(true);
                $dateValidation->setPromptTitle('Tanggal Lahir');
                $dateValidation->setPrompt('Masukkan tanggal dengan format DD/MM/YYYY');

                $dateValidation->setShowErrorMessage(true);
                $dateValidation->setErrorTitle('Format Salah');
                $dateValidation->setError('Tanggal harus valid dan menggunakan format DD/MM/YYYY.');

                $dateValidation->setSqref('C2:C' . $lastData);

                // gender
                $genderValidation = new DataValidation();
                $genderValidation->setType(DataValidation::TYPE_LIST);
                $genderValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $genderValidation->setAllowBlank(false);
                $genderValidation->setShowDropDown(true);

                $genderValidation->setShowInputMessage(true);
                $genderValidation->setPromptTitle('Jenis Kelamin');
                $genderValidation->setPrompt('Pilih Pria atau Wanita');

                $genderValidation->setShowErrorMessage(true);
                $genderValidation->setErrorTitle('Input Tidak Valid');
                $genderValidation->setError('Pilih salah satu nilai yang tersedia.');

                $genderValidation->setFormula1('"Pria,Wanita"');

                for ($row = 2; $row <= $lastData; $row++) {
                    $sheet->getCell("D{$row}")
                        ->setDataValidation(clone $genderValidation);
                }

                //status
                $statusValidation = new DataValidation();
                $statusValidation->setType(DataValidation::TYPE_LIST);
                $statusValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $statusValidation->setAllowBlank(false);
                $statusValidation->setShowDropDown(true);

                $statusValidation->setShowInputMessage(true);
                $statusValidation->setPromptTitle('Status');
                $statusValidation->setPrompt('Pilih Aktif atau Nonaktif');

                $statusValidation->setShowErrorMessage(true);
                $statusValidation->setErrorTitle('Input Tidak Valid');
                $statusValidation->setError('Pilih salah satu nilai yang tersedia.');

                $statusValidation->setFormula1('"Aktif,Nonaktif"');

                for ($row = 2; $row <= $lastData; $row++) {
                    $sheet->getCell("E{$row}")
                        ->setDataValidation(clone $statusValidation);
                }

                // Nama sheet
                $event->sheet->setTitle('Data Atlet');
            },
        ];
    }
}
