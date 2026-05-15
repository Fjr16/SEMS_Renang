<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StartingListExport implements WithEvents, ShouldAutoSize
{

    // protected $data;
    protected array $peserta;
    protected array $eventGroups;
    protected $clubName;
    protected $competitionName;
    protected $year;

    public function __construct(
        array  $peserta,
        array  $eventGroups,
        string $clubName,
        string $competitionName,
        string $year
    )
    {
        $this->peserta     = $peserta;
        $this->eventGroups = $eventGroups;
        $this->clubName  = $clubName;
        $this->competitionName = $competitionName;
        $this->year      = $year;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $fixedCols     = 4;
                $totalEvent    = collect($this->eventGroups)->sum(fn($g) => count($g['events']));
                $totalCols     = $fixedCols + $totalEvent + 2;
                $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);

                // Deklarasi sekali di atas, dipakai di bawah
                $startRow      = 7;
                $biayaPerNomor = 75000;
                $dataStartLet  = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($fixedCols + 1);
                $dataEndLet    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($fixedCols + $totalEvent);

                // ── JUDUL ─────────────────────────────────────────────────────
                $sheet->mergeCells("A1:{$lastColLetter}1"); // ← double quote
                $sheet->setCellValue('A1', 'STARTING LIST');
                $sheet->getStyle('A1')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->mergeCells("A2:{$lastColLetter}2");
                $sheet->setCellValue('A2', $this->competitionName);
                $sheet->getStyle('A2')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->mergeCells("A3:{$lastColLetter}3");
                $sheet->setCellValue('A3', 'TAHUN ' . $this->year);
                $sheet->getStyle('A3')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                ]);

                $sheet->mergeCells("A4:{$lastColLetter}4");
                $sheet->setCellValue('A4', 'NAMA CLUB : ' . $this->clubName);
                $sheet->getStyle('A4')->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 11],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
                ]);

                // ── HEADER TETAP ──────────────────────────────────────────────
                $sheet->mergeCells('A5:A6'); $sheet->setCellValue('A5', 'NO');
                $sheet->mergeCells('B5:B6'); $sheet->setCellValue('B5', 'NAMA');
                $sheet->mergeCells('C5:C6'); $sheet->setCellValue('C5', 'KU');
                $sheet->mergeCells('D5:D6'); $sheet->setCellValue('D5', 'PA/PI');

                // ── HEADER DINAMIS ────────────────────────────────────────────
                $colIndex = $fixedCols + 1;

                foreach ($this->eventGroups as $group) {
                    $groupStartCol = $colIndex;
                    $subCount      = count($group['events']);

                    foreach ($group['events'] as $ev) {
                        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                        $sheet->setCellValue("{$colLetter}6", $ev['label']);
                        $colIndex++;
                    }

                    $groupEndCol   = $colIndex - 1;
                    $groupStartLet = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($groupStartCol);
                    $groupEndLet   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($groupEndCol);

                    if ($subCount > 1) {
                        $sheet->mergeCells("{$groupStartLet}5:{$groupEndLet}5");
                    }
                    $sheet->setCellValue("{$groupStartLet}5", strtoupper($group['name']));
                }

                $jmlColLetter   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
                $biayaColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);

                $sheet->mergeCells("{$jmlColLetter}5:{$jmlColLetter}6");
                $sheet->setCellValue("{$jmlColLetter}5", 'JUMLAH');

                $sheet->mergeCells("{$biayaColLetter}5:{$biayaColLetter}6");
                $sheet->setCellValue("{$biayaColLetter}5", 'BIAYA');

                // Style header — SEKALI saja, pakai $lastColLetter
                $sheet->getStyle("A5:{$lastColLetter}6")->applyFromArray([
                    'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                        'vertical'   => Alignment::VERTICAL_CENTER,
                        'wrapText'   => true,
                    ],
                    'borders' => [
                        'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']],
                    ],
                ]);

                // ── EVENT COL MAP ─────────────────────────────────────────────
                $eventColMap = [];
                $ci = $fixedCols + 1;
                foreach ($this->eventGroups as $group) {
                    foreach ($group['events'] as $ev) {
                        $eventColMap[$ev['id']] = $ci++;
                    }
                }

                // ── DATA ROWS ─────────────────────────────────────────────────
                foreach ($this->peserta as $i => $p) {
                    $row     = $startRow + $i;
                    $bgColor = ($i % 2 === 0) ? 'F5F7FF' : 'FFFFFF';

                    $sheet->setCellValue("A{$row}", $p['no']);
                    $sheet->setCellValue("B{$row}", $p['nama']);
                    $sheet->setCellValue("C{$row}", $p['ku']);
                    $sheet->setCellValue("D{$row}", $p['pa_pi']);

                    foreach ($p['event_ids'] as $eventId) {
                        if (!isset($eventColMap[$eventId])) continue;

                        $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($eventColMap[$eventId]);
                        $sheet->setCellValue("{$cLetter}{$row}", 'V');
                        $sheet->getStyle("{$cLetter}{$row}")->applyFromArray([
                            'font'      => ['bold' => true, 'color' => ['rgb' => '00897B']],
                            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                        ]);
                    }

                    $sheet->setCellValue("{$jmlColLetter}{$row}",   "=COUNTIF({$dataStartLet}{$row}:{$dataEndLet}{$row},\"V\")");
                    $sheet->setCellValue("{$biayaColLetter}{$row}", "={$jmlColLetter}{$row}*{$biayaPerNomor}");
                    $sheet->getStyle("{$biayaColLetter}{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

                    $sheet->getStyle("A{$row}:{$lastColLetter}{$row}")->applyFromArray([
                        'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                        'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
                    ]);
                }

                // ── FOOTER ───────────────────────────────────────────────────
                $lastDataRow = $startRow + count($this->peserta) - 1;
                $totalRow    = $lastDataRow + 1;
                $biayaRow    = $totalRow + 1;

                $sheet->mergeCells("A{$totalRow}:{$dataEndLet}{$totalRow}");
                $sheet->setCellValue("A{$totalRow}", 'TOTAL NOMOR PERLOMBAAN YANG DI IKUTI');
                $sheet->setCellValue("{$jmlColLetter}{$totalRow}", "=SUM({$jmlColLetter}{$startRow}:{$jmlColLetter}{$lastDataRow})");

                $sheet->mergeCells("A{$biayaRow}:{$dataEndLet}{$biayaRow}");
                $sheet->setCellValue("A{$biayaRow}", 'JUMLAH UANG PENDAFTARAN     Rp.');
                $sheet->setCellValue("{$biayaColLetter}{$biayaRow}", "=SUM({$biayaColLetter}{$startRow}:{$biayaColLetter}{$lastDataRow})");
                $sheet->getStyle("{$biayaColLetter}{$biayaRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

                $sheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->applyFromArray([
                    'font'    => ['bold' => true],
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1565C0']]],
                ]);

                $sheet->getStyle("A{$biayaRow}:{$lastColLetter}{$biayaRow}")->applyFromArray([
                    'font'    => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D47A1']],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'FFFFFF']]],
                ]);

                dd([
                'totalEvent'    => $totalEvent,
                'totalCols'     => $totalCols,
                'lastColLetter' => $lastColLetter,
                'dataStartLet'  => $dataStartLet,
                'dataEndLet'    => $dataEndLet,
                'jmlColLetter'  => $jmlColLetter,
                'biayaColLetter'=> $biayaColLetter,
            ]);

                $sheet->freezePane('E7');
                $sheet->getPageSetup()->setOrientation(
                    \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
                );
            },
        ];
    }
}
