<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Atlet Terbaik</title>
    <style>

        @page {
            size: A4 landscape;
            margin: 15mm 15mm 20mm 15mm;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8.5pt;
            color: #000;
            background: #fff;
        }

        /* ================================================
           PAGE HEADER
        ================================================ */
        .page-header {
            display: table;
            width: 100%;
            margin-bottom: 6px;
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
            font-size: 12pt;
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
            font-size: 14pt;
            font-weight: bold;
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

        /* ================================================
           ATLET TERBAIK TABLE
        ================================================ */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8pt;
            margin-top: 4px;
        }

        table thead tr {
            border-bottom: 0.5pt solid #000;
        }

        table thead th {
            padding: 4px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 8pt;
        }

        table thead th.center {
            text-align: center;
        }

        table tbody tr {
            border-bottom: 0.5pt solid #ddd;
        }

        table tbody tr td {
            padding: 3px 5px;
            vertical-align: middle;
        }

        table tbody tr td.center {
            text-align: center;
        }

        td.nama-atlet {
            font-weight: bold;
            text-transform: uppercase;
        }

        td.medal-val {
            text-align: center;
            font-weight: bold;
        }

        td.medal-zero {
            text-align: center;
            color: #bbb;
        }

        /* Col widths - landscape */
        .col-pos    { width: 4%; }
        .col-id     { width: 6%; }
        .col-nama   { width: 20%; }
        .col-sex    { width: 6%; }
        .col-ku     { width: 8%; }
        .col-tim    { width: 22%; }
        .col-emas   { width: 5%; }
        .col-perak  { width: 5%; }
        .col-perunggu { width: 6%; }
        .col-poin   { width: 5%; }
        .col-extra  { width: 4%; } /* kolom - (4 buah) */

        /* ================================================
           PAGE FOOTER via Dompdf page_text
        ================================================ */
    </style>
</head>
<body>

    {{-- Dompdf: page number di setiap halaman --}}
    <script type="text/php">
        if (isset($pdf)) {
            $font = $fontMetrics->getFont("Arial", "normal");
            $w    = $pdf->get_width();
            $h    = $pdf->get_height();

            $pdf->page_text(
                28, $h - 16,
                "Dicetak {{ \Carbon\Carbon::now()->translatedFormat('l d F Y H:i') }}",
                $font, 7, [0,0,0]
            );

            $pdf->page_text(
                $w - 85, $h - 16,
                "Halaman {PAGE_NUM}/{PAGE_COUNT}",
                $font, 7, [0,0,0]
            );

            $pdf->page_text(
                $w / 2 - 30, $h - 10,
                "Sponsor Resmi",
                $font, 7, [0,0,0]
            );
        }
    </script>

    {{-- PAGE HEADER --}}
    <div class="page-header">
        {{-- <div class="logo-left">
            @if(!empty($logoKiri))
                @foreach($logoKiri as $logo)
                    <img src="{{ $logo }}" alt="logo">
                @endforeach
            @endif
        </div> --}}
        <div class="header-center">
            <div class="ev-name">{{ $namaEvent }}</div>
            <div class="ev-venue">{{ $venue }}</div>
            <div class="ev-date">{{ $tanggal }}</div>
            <div class="ev-section">DAFTAR ATLET TERBAIK</div>
        </div>
        {{-- <div class="logo-right">
            @if(!empty($logoKanan))
                @foreach($logoKanan as $logo)
                    <img src="{{ $logo }}" alt="logo">
                @endforeach
            @endif
        </div> --}}
    </div>

    {{-- TABLE --}}
    <table>
        <thead>
            <tr>
                <th class="col-pos center">Pos</th>
                <th class="col-id center">ID</th>
                <th class="col-nama">Nama Atlet</th>
                <th class="col-sex center">Sex</th>
                <th class="col-ku center">KU</th>
                <th class="col-tim">Nama Tim</th>
                <th class="col-emas center">Emas</th>
                <th class="col-perak center">Perak</th>
                <th class="col-perunggu center">Perunggu</th>
                <th class="col-poin center">Poin</th>
                <th class="col-extra center">-</th>
                <th class="col-extra center">-</th>
                <th class="col-extra center">-</th>
                <th class="col-extra center">-</th>
            </tr>
        </thead>
        <tbody>
            @foreach($atletTerbaik as $atlet)
            <tr>
                <td class="center">{{ $atlet['pos'] }}</td>
                <td class="center" style="font-size:7.5pt;">{{ $atlet['id'] }}</td>
                <td class="nama-atlet">{{ $atlet['nama'] }}</td>
                <td class="center">{{ $atlet['sex'] }}</td>
                <td class="center">{{ $atlet['ku'] }}</td>
                <td>{{ $atlet['tim'] }}</td>

                <td class="{{ $atlet['emas'] > 0 ? 'medal-val' : 'medal-zero' }}">
                    {{ $atlet['emas'] }}
                </td>

                <td class="{{ $atlet['perak'] > 0 ? 'medal-val' : 'medal-zero' }}">
                    {{ $atlet['perak'] }}
                </td>

                <td class="{{ $atlet['perunggu'] > 0 ? 'medal-val' : 'medal-zero' }}">
                    {{ $atlet['perunggu'] }}
                </td>

                <td class="medal-zero center">{{ $atlet['poin'] ?? 0 }}</td>

                <td class="medal-zero">0</td>
                <td class="medal-zero">0</td>
                <td class="medal-zero">0</td>
                <td class="medal-zero">0</td>
            </tr>
            @endforeach
        </tbody>
    </table>

</body>
</html>
