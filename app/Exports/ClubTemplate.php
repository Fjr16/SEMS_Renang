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

class ClubTemplate implements FromArray, WithHeadings, WithColumnFormatting, WithColumnWidths, WithStyles, WithEvents
{

    private string $lastColumn  = 'G';
    private int    $dataRows    = 100;

    public function headings(): array
    {
        return [
            'nama',
            'kode',
            'kota',
            'provinsi',
            'penanggung_jawab',
            'hp_penanggung_jawab',
            'jenis_klub'
        ];
    }

    public function array() :array
    {
        return [];
    }

    public function columnFormats() : array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
            'F' => NumberFormat::FORMAT_TEXT,
            'G' => NumberFormat::FORMAT_TEXT,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 20,
            'D' => 25,
            'E' => 20,
            'F' => 20,
            'G' => 15,
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

                //format text untuk no hp
                // $sheet->getStyle("F:{$this->lastColumn}{$lastData}")
                $sheet->getStyle("F:F")
                    ->getNumberFormat()
                    ->setFormatCode('@');

                // team type
                $typeValidation = new DataValidation();
                $typeValidation->setType(DataValidation::TYPE_LIST);
                $typeValidation->setErrorStyle(DataValidation::STYLE_STOP);
                $typeValidation->setAllowBlank(false);
                $typeValidation->setShowDropDown(true);

                $typeValidation->setShowInputMessage(true);
                $typeValidation->setPromptTitle('Jenis Klub');
                $typeValidation->setPrompt('Pilih Jenis Klub');

                $typeValidation->setShowErrorMessage(true);
                $typeValidation->setErrorTitle('Input Tidak Valid');
                $typeValidation->setError('Pilih salah satu nilai yang tersedia.');

                $typeValidation->setFormula1('"Sekolah, Klub, Kota, Provinsi, Negara"');

                for ($row = 2; $row <= $lastData; $row++) {
                    $sheet->getCell("G{$row}")
                        ->setDataValidation(clone $typeValidation);
                }

                // Nama sheet
                $event->sheet->setTitle('Data Klub');
            },
        ];
    }
}
