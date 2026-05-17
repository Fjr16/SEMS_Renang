<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Hasil Kejuaraan</title>
    <style>

        @page {
            size: A4 portrait;
            margin: 15mm 15mm 20mm 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
            color: #000;
            background: #fff;
        }

        /* ================================================
           PAGE HEADER (dipakai di rekap & hasil)
        ================================================ */
        .page-header {
            display: table;
            width: 100%;
            margin-bottom: 4px;
        }

        .logo-left {
            display: table-cell;
            width: 110px;
            vertical-align: middle;
            text-align: left;
        }

        .logo-left img {
            height: 52px;
            margin-right: 3px;
        }

        .header-center {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
            padding: 0 8px;
        }

        .header-center .ev-name {
            font-size: 11pt;
            font-weight: bold;
        }

        .header-center .ev-venue {
            font-size: 9pt;
            margin-top: 1px;
        }

        .header-center .ev-date {
            font-size: 9pt;
            font-weight: bold;
            margin-top: 1px;
        }

        .header-center .ev-section {
            font-size: 13pt;
            font-weight: bold;
            text-decoration: underline;
            margin-top: 6px;
            letter-spacing: 0.5px;
        }

        .logo-right {
            display: table-cell;
            width: 110px;
            vertical-align: middle;
            text-align: right;
        }

        .logo-right img {
            height: 52px;
            margin-left: 3px;
        }

        .header-divider-top {
            border-top: 1.5pt solid #000;
            margin: 6px 0 0 0;
        }

        .header-divider-bottom {
            border-top: 1pt solid #000;
            margin: 0 0 4px 0;
        }

        /* ================================================
           REKAP MEDALI TABLE
        ================================================ */
        .rekap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5pt;
            margin-top: 2px;
        }

        .rekap-table thead tr th {
            padding: 4px 6px;
            text-align: left;
            font-weight: bold;
            border-bottom: 0.5pt solid #000;
        }

        .rekap-table thead tr th.center {
            text-align: center;
        }

        .rekap-table tbody tr td {
            padding: 2.5px 6px;
            vertical-align: middle;
        }

        .rekap-table tbody tr td.center {
            text-align: center;
        }

        .rekap-table tbody tr td.bold {
            font-weight: bold;
        }

        /* Baris total di bawah */
        .rekap-table tfoot tr td {
            padding: 4px 6px;
            border-top: 1pt solid #000;
            font-weight: bold;
        }

        .rekap-table tfoot tr td.center {
            text-align: center;
        }

        /* Col widths rekap */
        .col-rek-pos    { width: 8%; }
        .col-rek-nama   { width: 52%; }
        .col-rek-emas   { width: 8%; text-align: center; }
        .col-rek-perak  { width: 8%; text-align: center; }
        .col-rek-perunggu { width: 9%; text-align: center; }
        .col-rek-total  { width: 8%; text-align: center; }
        .col-rek-poin   { width: 7%; text-align: center; }

        /* ================================================
           HASIL PERLOMBAAN
        ================================================ */

        /* Acara sub-header */
        .acara-header {
            display: table;
            width: 100%;
            margin-top: 10px;
            margin-bottom: 1px;
        }

        .acara-header-left {
            display: table-cell;
            vertical-align: bottom;
        }

        .acara-header-right {
            display: table-cell;
            vertical-align: bottom;
            text-align: right;
        }

        .acara-nomor {
            font-size: 9pt;
            font-weight: bold;
            font-style: italic;
        }

        .acara-tanggal {
            font-size: 8pt;
        }

        .acara-nama {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .acara-status {
            font-size: 9pt;
            font-weight: bold;
        }

        /* Result table */
        .result-wrapper {
            page-break-inside: avoid;
        }

        .result-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }

        .result-table thead tr {
            border-top: 1pt solid #000;
            border-bottom: 1pt solid #000;
        }

        .result-table thead th {
            padding: 3px 4px;
            text-align: left;
            font-weight: bold;
        }

        .result-table thead th.center {
            text-align: center;
        }

        .result-table tbody tr.limit-row td {
            font-size: 7pt;
            font-style: italic;
            padding: 2px 4px;
            color: #444;
            border-bottom: 0.3pt solid #ccc;
        }

        .result-table tbody tr.data-row td {
            padding: 2px 4px;
            border-bottom: 0.3pt solid #ddd;
            vertical-align: middle;
        }

        .result-table tbody tr.data-row:nth-child(even) td {
            background-color: #f5f5f5;
        }

        /* Col widths hasil - portrait A4 */
        .col-pos    { width: 5%; }
        .col-id     { width: 7%; }
        .col-nama   { width: 22%; }
        .col-ket    { width: 4%; }
        .col-lahir  { width: 10%; }
        .col-ku     { width: 9%; }
        .col-tim    { width: 24%; }
        .col-best   { width: 10%; }
        .col-hasil  { width: 9%; }

        /* Rank colors */
        td.rank     { text-align: center; font-weight: bold; }
        td.rank-1   { color: #8B6914; font-weight: bold; }
        td.rank-2   { color: #4a4a4a; font-weight: bold; }
        td.rank-3   { color: #7B3F00; font-weight: bold; }

        td.nama-atlet {
            font-weight: bold;
            text-transform: uppercase;
        }

        td.waktu {
            text-align: right;
            font-family: 'Courier New', monospace;
        }

        td.special {
            text-align: right;
            font-style: italic;
            color: #666;
            font-family: 'Courier New', monospace;
        }

        /* ================================================
           PAGE FOOTER
        ================================================ */
        .page-footer-label {
            /* dipakai sebagai referensi posisi saja,
               nomor halaman di-inject via page_text Dompdf */
            border-top: 0.5pt solid #000;
            margin-top: 8px;
            padding-top: 3px;
            display: table;
            width: 100%;
            font-size: 7pt;
        }

        .pf-left   { display: table-cell; text-align: left; }
        .pf-center { display: table-cell; text-align: center; }
        .pf-right  { display: table-cell; text-align: right; }

        /* ================================================
           PAGE BREAK
        ================================================ */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>

    {{-- =============================================
         DOMPDF: inject page number di setiap halaman
    ============================================= --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Arial", "normal");
            $w    = $pdf->get_width();
            $h    = $pdf->get_height();

            // Kiri: tanggal cetak
            $pdf->page_text(
                28, $h - 16,
                "Dicetak {{ \Carbon\Carbon::now()->translatedFormat('l d F Y H:i') }}",
                $font, 7, [0,0,0]
            );

            // Tengah: nomor halaman
            $pdf->page_text(
                $w / 2 - 45, $h - 16,
                "Halaman {PAGE_NUM}/{PAGE_COUNT}",
                $font, 7, [0,0,0]
            );

            // Kanan: sponsor
            $pdf->page_text(
                $w - 85, $h - 16,
                "Sponsor Resmi",
                $font, 7, [0,0,0]
            );
        }
    </script>


    {{-- =============================================
         HALAMAN 1: REKAP MEDALI PER KONTINGEN
    ============================================= --}}
    <div class="page-header">
        <div class="logo-left">
            @if(!empty($logoKiri))
                @foreach($logoKiri as $logo)
                    <img src="{{ $logo }}" alt="logo">
                @endforeach
            @endif
        </div>
        <div class="header-center">
            <div class="ev-name">{{ $namaEvent }}</div>
            <div class="ev-venue">{{ $venue }}</div>
            <div class="ev-date">{{ $tanggal }}</div>
            <div class="ev-section">REKAP MEDALI PER KONTINGEN</div>
        </div>
        <div class="logo-right">
            @if(!empty($logoKanan))
                @foreach($logoKanan as $logo)
                    <img src="{{ $logo }}" alt="logo">
                @endforeach
            @endif
        </div>
    </div>

    <div class="header-divider-top"></div>
    <div class="header-divider-bottom"></div>

    <table class="rekap-table">
        <thead>
            <tr>
                <th class="col-rek-pos">Posisi</th>
                <th class="col-rek-nama">Nama Tim</th>
                <th class="col-rek-emas center">Emas</th>
                <th class="col-rek-perak center">Perak</th>
                <th class="col-rek-perunggu center">Perunggu</th>
                <th class="col-rek-total center">Jumlah Medali</th>
                <th class="col-rek-poin center">Jumlah Poin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapMedali as $row)
            <tr>
                <td>{{ $row['posisi'] ?? '' }}</td>
                <td>{{ $row['nama_tim'] }}</td>
                <td class="center">{{ $row['emas'] ?: '' }}</td>
                <td class="center">{{ $row['perak'] ?: '' }}</td>
                <td class="center">{{ $row['perunggu'] ?: '' }}</td>
                <td class="center">{{ ($row['emas'] + $row['perak'] + $row['perunggu']) ?: '' }}</td>
                <td class="center">{{ $row['poin'] ?? 0 }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td></td>
                <td><strong>Jumlah</strong></td>
                <td class="center">{{ $rekapMedali->sum('emas') }}</td>
                <td class="center">{{ $rekapMedali->sum('perak') }}</td>
                <td class="center">{{ $rekapMedali->sum('perunggu') }}</td>
                <td class="center">{{ $rekapMedali->sum('emas') + $rekapMedali->sum('perak') + $rekapMedali->sum('perunggu') }}</td>
                <td class="center">{{ $rekapMedali->sum('poin') }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Page break sebelum hasil perlombaan --}}
    <div class="page-break"></div>


    {{-- =============================================
         HALAMAN 2+: HASIL PERLOMBAAN PER ACARA
    ============================================= --}}
    <div class="page-header">
        <div class="logo-left">
            @if(!empty($logoKiri))
                @foreach($logoKiri as $logo)
                    <img src="{{ $logo }}" alt="logo">
                @endforeach
            @endif
        </div>
        <div class="header-center">
            <div class="ev-name">{{ $namaEvent }}</div>
            <div class="ev-venue">{{ $venue }}</div>
            <div class="ev-date">{{ $tanggal }}</div>
            <div class="ev-section">HASIL PERLOMBAAN</div>
        </div>
        <div class="logo-right">
            @if(!empty($logoKanan))
                @foreach($logoKanan as $logo)
                    <img src="{{ $logo }}" alt="logo">
                @endforeach
            @endif
        </div>
    </div>

    <div class="header-divider-top"></div>
    <div class="header-divider-bottom"></div>

    {{-- Loop setiap acara --}}
    @foreach($acaraList as $acara)
    <div class="result-wrapper">

        {{-- Sub-header acara --}}
        <div class="acara-header">
            <div class="acara-header-left">
                <span class="acara-nomor">Acara {{ $acara['nomor'] }}</span>
                &nbsp;&nbsp;
                <span class="acara-tanggal">{{ strtoupper($acara['tanggal'] ?? '') }}</span>
            </div>
            <div class="acara-header-right">
                <span class="acara-nama">{{ $acara['nama'] }}</span>
                &nbsp;&nbsp;
                <span class="acara-status">{{ $acara['status'] ?? 'AKHIR' }}</span>
            </div>
        </div>

        {{-- Tabel hasil --}}
        <table class="result-table">
            <thead>
                <tr>
                    <th class="col-pos center">Pos</th>
                    <th class="col-id center">ID</th>
                    <th class="col-nama">Nama Atlet</th>
                    <th class="col-ket center">Ket</th>
                    <th class="col-lahir center">Lahir</th>
                    <th class="col-ku center">KU</th>
                    <th class="col-tim">Nama Tim</th>
                    <th class="col-best center">Prestasi</th>
                    <th class="col-hasil center">Akhir</th>
                </tr>
            </thead>
            <tbody>
                {{-- Baris limit --}}
                <tr class="limit-row">
                    <td colspan="9">
                        Limit Perlombaan &nbsp;&nbsp;&nbsp;
                        {{ $acara['limit'] ?? 'NO LIMIT' }}
                        ({{ $acara['kategori'] ?? '' }})
                    </td>
                </tr>

                {{-- Data atlet --}}
                @forelse($acara['hasil'] as $atlet)
                <tr class="data-row">
                    {{-- Posisi --}}
                    <td class="rank
                        @if(($atlet['pos'] ?? '') == '1') rank-1
                        @elseif(($atlet['pos'] ?? '') == '2') rank-2
                        @elseif(($atlet['pos'] ?? '') == '3') rank-3
                        @endif">
                        {{ $atlet['pos'] ?? '' }}
                    </td>

                    <td class="center" style="font-size:7pt;">{{ $atlet['id'] ?? '' }}</td>

                    <td class="nama-atlet">{{ $atlet['nama'] ?? '' }}</td>

                    <td class="center" style="font-size:6.5pt; color:#555;">
                        {{ $atlet['ket'] ?? '' }}
                    </td>

                    <td class="center">
                        {{ $atlet['lahir'] ?? '' }}
                        @if(!empty($atlet['umur']))
                            <span style="color:#555;">({{ $atlet['umur'] }})</span>
                        @endif
                    </td>

                    <td class="center">{{ $atlet['ku'] ?? '' }}</td>

                    <td>{{ $atlet['tim'] ?? '' }}</td>

                    <td class="waktu center">{{ $atlet['prestasi'] ?? '-' }}</td>

                    @php
                        $hasil   = strtoupper($atlet['hasil'] ?? '');
                        $special = in_array($hasil, ['DNS','DQ','DNF','NS','NF','SP']);
                    @endphp
                    <td class="{{ $special ? 'special' : 'waktu' }} center">
                        {{ $atlet['hasil'] ?? '' }}
                    </td>
                </tr>
                @empty
                <tr class="data-row">
                    <td colspan="9" style="text-align:center; color:#999; font-style:italic; padding:4px;">
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
    @endforeach

</body>
</html>
