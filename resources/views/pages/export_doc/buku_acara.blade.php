<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buku Acara</title>
    <style>
        @page {
            size: A4 portrait;
            margin-top: 28mm;
            margin-bottom: 15mm;
            margin-left: 15mm;
            margin-right: 15mm;
        }

        @page :first {
            margin-top: 0;
            margin-bottom: 0;
            margin-left: 0;
            margin-right: 0;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
            color: #000;
            background: #fff;
        }

        /* ================================================
           HALAMAN 1: COVER
        ================================================ */

        .cover-page {
            width: 210mm;
            height: 297mm;
            position: relative;
            overflow: hidden;
            page-break-after: always;
            background-image: url('{{ public_path("assets/cover-doc.png") }}');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .cover-logos {
            position: absolute;
            /* top: 360px; */
            top: 220px;
            left: 0;
            width: 100%;
            text-align: center;   /* ganti dari display:flex + justify-content */
        }
        .cover-logos img {
            height: 90px;
            display: inline-block;   /* supaya img mengikuti text-align dari parent */
            vertical-align: middle;
            margin: 0 4px;            /* pengganti gap, karena inline-block tidak support gap */
        }

        .cover-title {
            position: absolute;
            top: 490px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .cover-title .meet,
        .cover-title .program {
            font-size: 76pt;
            font-weight: 900;
            color: #1B2B6B;
            line-height: 1.0;
            letter-spacing: -2px;
        }

        .cover-event-info {
            position: absolute;
            top: 720px;
            left: 0;
            right: 0;
            text-align: center;
        }

        .cover-event-info .ev-name {
            font-size: 19pt;
            font-weight: bold;
            color: #1B2B6B;
            line-height: 1.3;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .cover-event-info .ev-detail {
            font-size: 11.5pt;    /* dari 10pt */
            border-top: 1px solid #F5C400;
            border-bottom: 1px solid #F5C400;
            padding: 7px 22px;
            display: inline-block;
            line-height: 1.7;
        }

        /* ================================================
           HALAMAN 2-3: SUSUNAN ACARA
        ================================================ */
        .schedule-page {
            /* padding: 30mm 15mm 20mm 15mm; */
            page-break-after: always;
        }

        /* Header halaman 2+ */
        .page-header {
            display: table;
            width: 100%;
            margin-bottom: 6px;
        }

        .logo-left {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
            text-align: left;
        }

        .logo-left img {
            height: 48px;
            margin-right: 3px;
        }

        .header-center {
            display: table-cell;
            vertical-align: middle;
            text-align: center;
        }

        .header-center .ev-name { font-size: 10pt; font-weight: bold; }
        .header-center .ev-venue { font-size: 8.5pt; margin-top: 1px; }
        .header-center .ev-date { font-size: 8.5pt; font-weight: bold; margin-top: 1px; }
        .header-center .ev-section {
            font-size: 12pt;
            font-weight: bold;
            margin-top: 5px;
            letter-spacing: 0.5px;
        }

        .logo-right {
            display: table-cell;
            width: 100px;
            vertical-align: middle;
            text-align: right;
        }

        .logo-right img {
            height: 48px;
            margin-left: 3px;
        }

        .header-divider {
            border-top: 1pt solid #000;
            margin-bottom: 8px;
        }

        /* Judul hari */
        .schedule-day-title {
            font-size: 9pt;
            font-weight: bold;
            text-decoration: underline;
            margin-bottom: 6px;
        }

        /* Tabel susunan acara */
        .schedule-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-bottom: 14px;
        }

        .schedule-table .session-header td {
            background-color: #f0f0f0;
            font-weight: bold;
            font-size: 8.5pt;
            padding: 3px 6px;
            border: 0.5pt solid #888;
            text-align: center;
        }

        .schedule-table thead tr th {
            background-color: #fff;
            font-weight: bold;
            padding: 3px 6px;
            border: 0.5pt solid #888;
            /* text-align: center; */
            font-size: 8pt;
        }

        .schedule-table tbody tr td {
            padding: 2.5px 6px;
            border: 0.5pt solid #bbb;
            /* text-align: center; */
            vertical-align: middle;
        }

        .schedule-table tbody tr td.td-nomor {
            font-weight: bold;
            text-align: center;
        }

        .schedule-table tbody tr td.td-nama {
            /* text-align: center; */
            font-style: italic;
        }

        .schedule-table tbody tr td.td-ku {
            font-weight: bold;
            text-align: center;
        }

        /* ================================================
           HALAMAN 4+: DETAIL ACARA
        ================================================ */
        /* Acara sub-header */
        .acara-header {
            display: table;
            width: 100%;
            margin-bottom: 2px;
            margin-top: 8px;
        }

        .acara-left {
            display: table-cell;
            vertical-align: bottom;
        }

        .acara-right {
            display: table-cell;
            vertical-align: bottom;
            text-align: right;
        }

        .acara-nomor {
            font-size: 9pt;
            font-weight: bold;
            font-style: italic;
        }

        .acara-tanggal { font-size: 8pt; }

        .acara-nama {
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }

        .acara-status { font-size: 9pt; font-weight: bold; }

        /* Limit row */
        .limit-row {
            font-size: 7.5pt;
            font-style: italic;
            margin-bottom: 2px;
            color: #444;
        }

        /* Detail table */
        .detail-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }

        .detail-table thead tr {
            border-top: 1pt solid #000;
            border-bottom: 1pt solid #000;
        }

        .detail-table thead th {
            padding: 3px 3px;
            text-align: left;
            font-weight: bold;
        }

        .detail-table thead th.center { text-align: center; }
        .detail-table thead th.right  { text-align: right; }

        /* Seri header */
        .detail-table thead tr.seri-header th {
            background-color: #fff;
            font-size: 7pt;
            border-top: none;
            border-bottom: 0.5pt solid #ccc;
        }

        .seri-label {
            display: inline-block;
            border: 0.5pt solid #000;
            padding: 1px 5px;
            font-size: 7pt;
            font-weight: bold;
            /* float: center; */
        }

        .detail-table tbody tr td {
            padding: 2px 3px;
            border-bottom: 0.3pt solid #ddd;
            vertical-align: middle;
        }

        .detail-table tbody tr.empty-row td {
            color: #999;
            text-align: center;
        }

        /* ID Lomba box */
        td.id-lomba {
            text-align: right;
            font-family: 'Courier New', monospace;
            white-space: nowrap;
        }

        td.nama-atlet {
            font-weight: bold;
            text-transform: uppercase;
        }

        td.prestasi {
            text-align: right;
            font-family: 'Courier New', monospace;
            white-space: nowrap;
        }

        /* MOSC label */
        .mosc {
            font-size: 5.5pt;
            color: #888;
            display: block;
        }

        /* ================================================
           PAGE BREAK
        ================================================ */
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    {{-- =============================================
         HALAMAN 1: COVER
    ============================================= --}}
    <div class="cover-page">
        <div class="cover-logos">
            @if(!empty($logoKiri))
                @foreach($logoKiri as $logo)
                    <img src="{{ $logo }}" alt="logo">
                @endforeach
            @endif
        </div>
        <div class="cover-title">
            <div class="meet">MEET</div>
            <div class="program">PROGRAM</div>
        </div>

        <div class="cover-event-info">
            <div class="ev-name">{{ $namaEvent }}</div>
            <div class="ev-detail">
                {{ $tanggal }}<br>
                {{ $venue }}
            </div>
        </div>
    </div>


    {{-- =============================================
         HALAMAN 2-3: SUSUNAN ACARA (per hari)
    ============================================= --}}
    @foreach($jadwalHari as $hari)
    <div class="schedule-page">

        <div class="schedule-day-title">{{ $loop->iteration }}) {{ $hari['label'] ?? '' }}</div>

        {{-- Ganti bagian schedule-table --}}
        <table class="schedule-table">
            <thead>
                <tr>
                    {{-- <th style="width:15%;">ACARA PA</th> --}}
                    <th rowspan="2" style="width:45%; text-align:center;">NOMOR</th>
                    <th rowspan="2" style="width:15%;">KU</th>
                    <th colspan="3" style="width:40%;">ACARA</th>
                    <tr>
                        <th>PA</th>
                        <th>PI</th>
                        <th>MIX</th>
                    </tr>
                </tr>
            </thead>
            <tbody>
                @foreach($hari['sesi'] as $sesi)

                    <tr class="session-header">
                        <td colspan="5">
                            {{ $sesi['nama'] }}
                        </td>
                    </tr>

                    @foreach($sesi['acara'] as $acara)
                        @foreach($acara['ku_list'] as $i => $ku)
                        <tr>
                            @if($i === 0)
                            <td class="td-nama" rowspan="{{ count($acara['ku_list']) }}">
                                {{ $acara['nomor'] }}
                            </td>
                            @endif

                            <td class="td-ku">{{ $ku['ku'] }}</td>
                            <td class="td-nomor">{{ $ku['pa'] ?? '-' }}</td>
                            <td class="td-nomor">{{ $ku['pi'] ?? '-' }}</td>
                            <td class="td-nomor">{{ $ku['mix'] ?? '-' }}</td>
                        </tr>
                        @endforeach
                    @endforeach

                @endforeach
            </tbody>
        </table>

    </div>
    @endforeach


    {{-- =============================================
         HALAMAN 4+: DETAIL PER ACARA
    ============================================= --}}
    <div>
        {{-- Loop per acara --}}
        @foreach($acaraList as $acara)
        <div class="acara-header">
            <div class="acara-left">
                <span class="acara-nomor">Acara {{ $acara['nomor'] ?? '-' }}</span>
                &nbsp;&nbsp;
                <span class="acara-tanggal">{{ strtoupper($acara['tanggal'] ?? '') }}</span>
            </div>
            <div class="acara-right">
                <span class="acara-nama">{{ $acara['nama'] }}</span>
                &nbsp;&nbsp;
                <span class="acara-status">{{ $acara['status'] ?? 'AKHIR' }}</span>
            </div>
        </div>

        <div class="limit-row">
            Limit Perlombaan &nbsp;&nbsp;&nbsp;
            {{ $acara['limit'] ?? 'NO LIMIT' }} ({{ $acara['kategori'] ?? '' }})
        </div>

        {{-- Loop per seri --}}
        @foreach($acara['seri'] as $seri)
        <table class="detail-table">
            <thead>
                <tr>
                    <th style="width: 3%;">Ln</th>
                    <th style="width: 7%;">ID</th>
                    <th style="width: 18%;">Nama Atlet</th>
                    <th style="width: 5%;">Ket</th>
                    <th style="width: 10%;">Lahir</th>
                    <th style="width: 7%;">KU</th>
                    <th style="width: 20%;">Nama Tim</th>
                    <th style="width: 8%" class="center">Prestasi</th>
                    <th style="width: 12%;" class="center">ID Lomba</th>
                    <th style="width: 10%; text-align: center; vertical-align: middle;"><span class="seri-label">Seri {{ str_pad($seri['nomor'], 2, '0', STR_PAD_LEFT) }}</span></th>
                </tr>
            </thead>
            <tbody>
                @foreach($seri['atlet'] as $atlet)
                <tr>
                    <td class="center">{{ $atlet['ln'] }}</td>
                    <td class="center" style="font-size:7pt;">{{ $atlet['id'] ?? '' }}</td>
                    <td>
                        @if(!empty($atlet['nama']))
                            <span class="nama-atlet">{{ $atlet['nama'] }}</span>
                            @if(!empty($atlet['ket']))
                                <span style="font-size:6pt; color:#777;">{{ $atlet['ket'] }}</span>
                            @endif
                        @else
                            <span style="color:#bbb;">-</span>
                        @endif
                    </td>
                    <td class="center">{{ $atlet['ket'] ?? '-' }}</td>
                    <td class="center">
                        @if(!empty($atlet['lahir']))
                            {{ $atlet['lahir'] }}
                            @if(!empty($atlet['umur']))
                                ({{ $atlet['umur'] }})
                            @endif
                            @if(!empty($atlet['ket_mosc']))
                                <span class="mosc">{{ $atlet['ket_mosc'] }}</span>
                            @endif
                        @endif
                    </td>
                    <td class="center">{{ $atlet['ku'] ?? '' }}</td>
                    <td>{{ $atlet['tim'] ?? '' }}</td>

                    @php
                        $prestasi  = $atlet['prestasi'] ?? '-';
                        $idLomba   = $atlet['id_lomba'] ?? '-';
                    @endphp
                    <td class="prestasi">{{ $prestasi }}</td>
                    <td class="id-lomba">{{ empty($idLomba) ? '-' : $idLomba }}</td>
                    <td style="width:10%; text-align: center;"> [ . . . . . . ]</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endforeach

        @endforeach

    </div>

</body>
</html>
