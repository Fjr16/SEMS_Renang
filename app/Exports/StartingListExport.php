<?php

namespace App\Exports;

use App\Enums\Stroke;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StartingListExport implements WithEvents, ShouldAutoSize
{

    private const COLOR_HEADER_BG    = '1565C0';
    private const COLOR_HEADER_DARK  = '0D47A1';
    private const COLOR_HEADER_LIGHT = 'E3F2FD';
    private const COLOR_ROW_ODD      = 'F5F7FF';
    private const COLOR_ROW_EVEN     = 'FFFFFF';
    private const COLOR_MARK         = '00897B';
    private const COLOR_BORDER       = 'E0E0E0';
    private const START_ROW          = 7;
    private const FIXED_COLS         = 4;

    public function __construct(
        private readonly array  $peserta,
        private readonly array  $eventGroups,
        private readonly array  $entryRelay,
        private readonly array  $eventGroupsRelay,
        private readonly string $clubName,
        private readonly string $competitionName,
        private readonly string $year,
    ){}

    // public function registerEvents(): array
    // {
    //     return [
    //         AfterSheet::class => function (AfterSheet $event) {
    //             $sheet1 = $event->sheet->getDelegate();
    //             $sheet1->setTitle('Individual');

    //             // Sheet 2: Relay
    //             $spreadsheet = $sheet1->getParent();
    //             $sheet2 = $spreadsheet->createSheet();
    //             $sheet2->setTitle('Estafet');

    //             $fixedCols     = 4;
    //             $totalEvent    = collect($this->eventGroups)->sum(fn($g) => count($g['events']));
    //             $totalCols     = $fixedCols + $totalEvent + 2;
    //             $lastColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($totalCols);

    //             $startRow      = 7;
    //             $dataEndLet    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($fixedCols + $totalEvent);

    //             // ── JUDUL ─────────────────────────────────────────────────────
    //             $sheet1->mergeCells("A1:{$lastColLetter}1"); // ← double quote
    //             $sheet1->setCellValue('A1', 'STARTING LIST INDIVIDUAL');
    //             $sheet1->getStyle('A1')->applyFromArray([
    //                 'font'      => ['bold' => true, 'size' => 14],
    //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    //             ]);

    //             $sheet1->mergeCells("A2:{$lastColLetter}2");
    //             $sheet1->setCellValue('A2', $this->competitionName);
    //             $sheet1->getStyle('A2')->applyFromArray([
    //                 'font'      => ['bold' => true, 'size' => 12],
    //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    //             ]);

    //             $sheet1->mergeCells("A3:{$lastColLetter}3");
    //             $sheet1->setCellValue('A3', 'TAHUN ' . $this->year);
    //             $sheet1->getStyle('A3')->applyFromArray([
    //                 'font'      => ['bold' => true, 'size' => 11],
    //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    //             ]);

    //             $sheet1->mergeCells("A4:{$lastColLetter}4");
    //             $sheet1->setCellValue('A4', 'NAMA CLUB : ' . $this->clubName);
    //             $sheet1->getStyle('A4')->applyFromArray([
    //                 'font'      => ['bold' => true, 'size' => 11],
    //                 'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT],
    //             ]);

    //             // ── HEADER TETAP ──────────────────────────────────────────────
    //             $sheet1->mergeCells('A5:A6'); $sheet1->setCellValue('A5', 'NO');
    //             $sheet1->mergeCells('B5:B6'); $sheet1->setCellValue('B5', 'NAMA');
    //             $sheet1->mergeCells('C5:C6'); $sheet1->setCellValue('C5', 'KU');
    //             $sheet1->mergeCells('D5:D6'); $sheet1->setCellValue('D5', 'PA/PI');

    //             // ── HEADER DINAMIS ────────────────────────────────────────────
    //             $colIndex = $fixedCols + 1;

    //             foreach ($this->eventGroups as $group) {
    //                 $groupStartCol = $colIndex;
    //                 $subCount      = count($group['events']);

    //                 foreach ($group['events'] as $ev) {
    //                     $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
    //                     $sheet1->setCellValue("{$colLetter}6", $ev['label']);
    //                     $colIndex++;
    //                 }

    //                 $groupEndCol   = $colIndex - 1;
    //                 $groupStartLet = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($groupStartCol);
    //                 $groupEndLet   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($groupEndCol);

    //                 if ($subCount > 1) {
    //                     $sheet1->mergeCells("{$groupStartLet}5:{$groupEndLet}5");
    //                 }
    //                 $sheet1->setCellValue("{$groupStartLet}5", Stroke::tryFrom($group['name'])->label() ?? '-');
    //             }

    //             $jmlColLetter   = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex);
    //             $biayaColLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($colIndex + 1);

    //             $sheet1->mergeCells("{$jmlColLetter}5:{$jmlColLetter}6");
    //             $sheet1->setCellValue("{$jmlColLetter}5", 'JUMLAH');

    //             $sheet1->mergeCells("{$biayaColLetter}5:{$biayaColLetter}6");
    //             $sheet1->setCellValue("{$biayaColLetter}5", 'BIAYA');

    //             // Style header — SEKALI saja, pakai $lastColLetter
    //             $sheet1->getStyle("A5:{$lastColLetter}6")->applyFromArray([
    //                 'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
    //                 'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1565C0']],
    //                 'alignment' => [
    //                     'horizontal' => Alignment::HORIZONTAL_CENTER,
    //                     'vertical'   => Alignment::VERTICAL_CENTER,
    //                     'wrapText'   => true,
    //                 ],
    //                 'borders' => [
    //                     'allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']],
    //                 ],
    //             ]);

    //             // ── EVENT COL MAP ─────────────────────────────────────────────
    //             $eventColMap = [];
    //             $ci = $fixedCols + 1;
    //             foreach ($this->eventGroups as $group) {
    //                 foreach ($group['events'] as $ev) {
    //                     $eventColMap[$ev['id']] = $ci++;
    //                 }
    //             }

    //             $totBiaya = 0;
    //             $totJml = 0;
    //             // ── DATA ROWS ─────────────────────────────────────────────────
    //             foreach ($this->peserta as $i => $p) {
    //                 $row     = $startRow + $i;
    //                 $bgColor = ($i % 2 === 0) ? 'F5F7FF' : 'FFFFFF';

    //                 $sheet1->setCellValue("A{$row}", $p['no']);
    //                 $sheet1->setCellValue("B{$row}", $p['nama']);
    //                 $sheet1->setCellValue("C{$row}", $p['ku']);
    //                 $sheet1->setCellValue("D{$row}", $p['pa_pi']);

    //                 foreach ($p['event_ids'] as $eventId) {
    //                     if (!isset($eventColMap[$eventId])) continue;

    //                     $cLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($eventColMap[$eventId]);
    //                     $sheet1->setCellValue("{$cLetter}{$row}", 'V');
    //                     $sheet1->getStyle("{$cLetter}{$row}")->applyFromArray([
    //                         'font'      => ['bold' => true, 'color' => ['rgb' => '00897B']],
    //                         'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
    //                     ]);
    //                 }

    //                 $sheet1->setCellValue("{$jmlColLetter}{$row}", count($p['event_ids']));
    //                 $sheet1->setCellValue("{$biayaColLetter}{$row}", $p['biaya']);
    //                 $sheet1->getStyle("{$biayaColLetter}{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

    //                 $sheet1->getStyle("A{$row}:{$lastColLetter}{$row}")->applyFromArray([
    //                     'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
    //                     'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'E0E0E0']]],
    //                 ]);

    //                 $totJml += count($p['event_ids']);
    //                 $totBiaya += $p['biaya'];
    //             }

    //             // ── FOOTER ───────────────────────────────────────────────────
    //             $lastDataRow = $startRow + count($this->peserta) - 1;
    //             $totalRow    = $lastDataRow + 1;
    //             $biayaRow    = $totalRow + 1;

    //             $sheet1->mergeCells("A{$totalRow}:{$dataEndLet}{$totalRow}");
    //             $sheet1->setCellValue("A{$totalRow}", 'TOTAL NOMOR PERLOMBAAN YANG DI IKUTI');
    //             $sheet1->setCellValue("{$jmlColLetter}{$totalRow}", $totJml);

    //             $sheet1->mergeCells("A{$biayaRow}:{$dataEndLet}{$biayaRow}");
    //             $sheet1->setCellValue("A{$biayaRow}", 'JUMLAH UANG PENDAFTARAN     Rp.');
    //             $sheet1->setCellValue("{$biayaColLetter}{$biayaRow}", $totBiaya);
    //             $sheet1->getStyle("{$biayaColLetter}{$biayaRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

    //             $sheet1->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->applyFromArray([
    //                 'font'    => ['bold' => true],
    //                 'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E3F2FD']],
    //                 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => '1565C0']]],
    //             ]);

    //             $sheet1->getStyle("A{$biayaRow}:{$lastColLetter}{$biayaRow}")->applyFromArray([
    //                 'font'    => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
    //                 'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0D47A1']],
    //                 'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'FFFFFF']]],
    //             ]);

    //             $sheet1->freezePane('E7');
    //             $sheet1->getPageSetup()->setOrientation(
    //                 \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
    //             );
    //         },
    //     ];
    // }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet1 = $event->sheet->getDelegate();
                $sheet1->setTitle('Individual');

                $sheet2 = $sheet1->getParent()->createSheet();
                $sheet2->setTitle('Estafet');

                $this->buildIndividualSheet($sheet1);
                $this->buildRelaySheet($sheet2);
            },
        ];
    }
    private function buildIndividualSheet(Worksheet $sheet): void
    {
        $totalEvent    = $this->countTotalEvents($this->eventGroups);
        $totalCols     = self::FIXED_COLS + $totalEvent + 2;
        $lastColLetter = Coordinate::stringFromColumnIndex($totalCols);
        $dataEndLet    = Coordinate::stringFromColumnIndex(self::FIXED_COLS + $totalEvent);

        $this->writeTitle($sheet, $lastColLetter, 'STARTING LIST INDIVIDUAL');
        $this->writeFixedHeader($sheet);

        [$jmlColLetter, $biayaColLetter] = $this->writeDynamicHeader($sheet, $this->eventGroups);

        $this->applyHeaderStyle($sheet, $lastColLetter);

        $eventColMap = $this->buildEventColMap($this->eventGroups);

        [$totJml, $totBiaya] = $this->writeIndividualRows(
            $sheet, $eventColMap, $jmlColLetter, $biayaColLetter, $lastColLetter
        );

        $this->writeFooter(
            $sheet,
            count($this->peserta),
            $jmlColLetter,
            $biayaColLetter,
            $dataEndLet,
            $lastColLetter,
            $totJml,
            $totBiaya
        );

        $this->applySheetSettings($sheet);
    }
    private function writeIndividualRows(
        Worksheet $sheet,
        array     $eventColMap,
        string    $jmlColLetter,
        string    $biayaColLetter,
        string    $lastColLetter,
    ): array {
        $totJml   = 0;
        $totBiaya = 0;

        foreach ($this->peserta as $i => $p) {
            $row     = self::START_ROW + $i;
            $bgColor = $i % 2 === 0 ? self::COLOR_ROW_ODD : self::COLOR_ROW_EVEN;

            $sheet->setCellValue("A{$row}", $p['no']);
            $sheet->setCellValue("B{$row}", $p['nama']);
            $sheet->setCellValue("C{$row}", $p['ku']);
            $sheet->setCellValue("D{$row}", $p['pa_pi']);

            foreach ($p['event_ids'] as $eventId) {
                if (!isset($eventColMap[$eventId])) continue;
                $col = Coordinate::stringFromColumnIndex($eventColMap[$eventId]);
                $this->writeMarkCell($sheet, "{$col}{$row}");
            }

            $jml = count($p['event_ids']);
            $sheet->setCellValue("{$jmlColLetter}{$row}", $jml);
            $sheet->setCellValue("{$biayaColLetter}{$row}", $p['biaya']);
            $sheet->getStyle("{$biayaColLetter}{$row}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

            $this->applyRowStyle($sheet, "A{$row}:{$lastColLetter}{$row}", $bgColor);

            $totJml   += $jml;
            $totBiaya += $p['biaya'];
        }

        return [$totJml, $totBiaya];
    }
    private function buildRelaySheet(Worksheet $sheet): void
    {
        $totalEvent    = $this->countTotalEvents($this->eventGroupsRelay);
        $totalCols     = self::FIXED_COLS + $totalEvent + 2;
        $lastColLetter = Coordinate::stringFromColumnIndex($totalCols);
        $dataEndLet    = Coordinate::stringFromColumnIndex(self::FIXED_COLS + $totalEvent);

        $this->writeTitle($sheet, $lastColLetter, 'STARTING LIST ESTAFET');
        $this->writeFixedHeader($sheet, namaKolom: 'NAMA TIM');

        [$jmlColLetter, $biayaColLetter] = $this->writeDynamicHeader($sheet, $this->eventGroupsRelay);

        $this->applyHeaderStyle($sheet, $lastColLetter);

        $eventColMap = $this->buildEventColMap($this->eventGroupsRelay);

        [$totJml, $totBiaya, $lastRow] = $this->writeRelayRows(
            $sheet, $eventColMap, $jmlColLetter, $biayaColLetter, $lastColLetter
        );

        $this->writeFooter(
            $sheet,
            rowCount: null,
            jmlColLetter: $jmlColLetter,
            biayaColLetter: $biayaColLetter,
            dataEndLet: $dataEndLet,
            lastColLetter: $lastColLetter,
            totJml: $totJml,
            totBiaya: $totBiaya,
            overrideLastDataRow: $lastRow - 1
        );

        $this->applySheetSettings($sheet);
    }
    private function writeRelayRows(
        Worksheet $sheet,
        array     $eventColMap,
        string    $jmlColLetter,
        string    $biayaColLetter,
        string    $lastColLetter,
    ): array {
        $totJml     = 0;
        $totBiaya   = 0;
        $currentRow = self::START_ROW;

        foreach ($this->entryRelay as $i => $tim) {
            $bgColor = $i % 2 === 0 ? self::COLOR_ROW_ODD : self::COLOR_ROW_EVEN;

            // Baris utama tim
            $sheet->setCellValue("A{$currentRow}", $tim['no']);
            $sheet->setCellValue("B{$currentRow}", $tim['nama_tim']);
            $sheet->setCellValue("C{$currentRow}", $tim['ku']);
            $sheet->setCellValue("D{$currentRow}", $tim['pa_pi']);

            $eventId = $tim['event_ids'][0] ?? null;
            if ($eventId && isset($eventColMap[$eventId])) {
                $col = Coordinate::stringFromColumnIndex($eventColMap[$eventId]);
                $this->writeMarkCell($sheet, "{$col}{$currentRow}");
            }

            $sheet->setCellValue("{$jmlColLetter}{$currentRow}", 1);
            $sheet->setCellValue("{$biayaColLetter}{$currentRow}", $tim['biaya']);
            $sheet->getStyle("{$biayaColLetter}{$currentRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

            $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->applyFromArray([
                'font'    => ['bold' => true],
                'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
                'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BORDER]]],
            ]);

            $totJml++;
            $totBiaya += $tim['biaya'];
            $currentRow++;

            // Baris anggota tim
            foreach ($tim['anggota'] as $j => $anggota) {
                $sheet->setCellValue("A{$currentRow}", '');
                $sheet->setCellValue("B{$currentRow}", $anggota['nama']);
                $sheet->getStyle("A{$currentRow}:{$lastColLetter}{$currentRow}")->applyFromArray([
                    'font'    => ['size' => 9, 'italic' => true, 'color' => ['rgb' => '777777']],
                    'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_ROW_EVEN]],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BORDER]]],
                ]);
                $currentRow++;
            }
        }

        return [$totJml, $totBiaya, $currentRow];
    }
    private function writeTitle(Worksheet $sheet, string $lastColLetter, string $judul): void
    {
        $sheet->mergeCells("A1:{$lastColLetter}1");
        $sheet->setCellValue('A1', $judul);
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
    }
    private function writeFixedHeader(Worksheet $sheet, string $namaKolom = 'NAMA'): void
    {
        $sheet->mergeCells('A5:A6'); $sheet->setCellValue('A5', 'NO');
        $sheet->mergeCells('B5:B6'); $sheet->setCellValue('B5', $namaKolom);
        $sheet->mergeCells('C5:C6'); $sheet->setCellValue('C5', 'KU');
        $sheet->mergeCells('D5:D6'); $sheet->setCellValue('D5', 'PA/PI');
    }
    private function writeDynamicHeader(Worksheet $sheet, array $eventGroups): array
    {
        $colIndex = self::FIXED_COLS + 1;

        foreach ($eventGroups as $group) {
            $groupStartCol = $colIndex;
            $subCount      = count($group['events']);

            foreach ($group['events'] as $ev) {
                $colLetter = Coordinate::stringFromColumnIndex($colIndex);
                $sheet->setCellValue("{$colLetter}6", $ev['label']);
                $colIndex++;
            }

            $groupStartLet = Coordinate::stringFromColumnIndex($groupStartCol);
            $groupEndLet   = Coordinate::stringFromColumnIndex($colIndex - 1);

            if ($subCount > 1) {
                $sheet->mergeCells("{$groupStartLet}5:{$groupEndLet}5");
            }
            $sheet->setCellValue("{$groupStartLet}5", Stroke::tryFrom($group['name'])?->label() ?? $group['name']);
        }

        $jmlColLetter   = Coordinate::stringFromColumnIndex($colIndex);
        $biayaColLetter = Coordinate::stringFromColumnIndex($colIndex + 1);

        $sheet->mergeCells("{$jmlColLetter}5:{$jmlColLetter}6");
        $sheet->setCellValue("{$jmlColLetter}5", 'JUMLAH');

        $sheet->mergeCells("{$biayaColLetter}5:{$biayaColLetter}6");
        $sheet->setCellValue("{$biayaColLetter}5", 'BIAYA');

        return [$jmlColLetter, $biayaColLetter];
    }
    private function applyHeaderStyle(Worksheet $sheet, string $lastColLetter): void
    {
        $sheet->getStyle("A5:{$lastColLetter}6")->applyFromArray([
            'font'      => ['bold' => true, 'size' => 9, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_HEADER_BG]],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical'   => Alignment::VERTICAL_CENTER,
                'wrapText'   => true,
            ],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
    }
    private function writeFooter(
        Worksheet $sheet,
        ?int      $rowCount,
        string    $jmlColLetter,
        string    $biayaColLetter,
        string    $dataEndLet,
        string    $lastColLetter,
        int       $totJml,
        int|float $totBiaya,
        ?int      $overrideLastDataRow = null,
    ): void {
        $lastDataRow = $overrideLastDataRow ?? (self::START_ROW + $rowCount - 1);
        $totalRow    = $lastDataRow + 1;
        $biayaRow    = $lastDataRow + 2;

        $sheet->mergeCells("A{$totalRow}:{$dataEndLet}{$totalRow}");
        $sheet->setCellValue("A{$totalRow}", 'TOTAL NOMOR PERLOMBAAN YANG DI IKUTI');
        $sheet->setCellValue("{$jmlColLetter}{$totalRow}", $totJml);

        $sheet->mergeCells("A{$biayaRow}:{$dataEndLet}{$biayaRow}");
        $sheet->setCellValue("A{$biayaRow}", 'JUMLAH UANG PENDAFTARAN     Rp.');
        $sheet->setCellValue("{$biayaColLetter}{$biayaRow}", $totBiaya);
        $sheet->getStyle("{$biayaColLetter}{$biayaRow}")->getNumberFormat()->setFormatCode('"Rp "#,##0');

        $sheet->getStyle("A{$totalRow}:{$lastColLetter}{$totalRow}")->applyFromArray([
            'font'    => ['bold' => true],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_HEADER_LIGHT]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => self::COLOR_HEADER_BG]]],
        ]);

        $sheet->getStyle("A{$biayaRow}:{$lastColLetter}{$biayaRow}")->applyFromArray([
            'font'    => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => self::COLOR_HEADER_DARK]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_MEDIUM, 'color' => ['rgb' => 'FFFFFF']]],
        ]);
    }
    private function writeMarkCell(Worksheet $sheet, string $cell): void
    {
        $sheet->setCellValue($cell, 'V');
        $sheet->getStyle($cell)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => self::COLOR_MARK]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }
    private function applyRowStyle(Worksheet $sheet, string $range, string $bgColor): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'fill'    => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $bgColor]],
            'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => self::COLOR_BORDER]]],
        ]);
    }
    private function applySheetSettings(Worksheet $sheet): void
    {
        $sheet->freezePane('E7');
        $sheet->getPageSetup()->setOrientation(
            \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
        );
    }
    private function buildEventColMap(array $eventGroups): array
    {
        $map = [];
        $ci  = self::FIXED_COLS + 1;
        foreach ($eventGroups as $group) {
            foreach ($group['events'] as $ev) {
                $map[$ev['id']] = $ci++;
            }
        }
        return $map;
    }
    private function countTotalEvents(array $eventGroups): int
    {
        return collect($eventGroups)->sum(fn($g) => count($g['events']));
    }
}
