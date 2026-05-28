<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    {{-- <title>Hasil Kejuaraan Aquatik</title> --}}
    <style>
        @page {
            margin: 25px 50px 40px 50px;
        }

        body {
            font-family: Arial, sans-serif;
            font-size: 8pt;
            color: #000;
            background: #fff;
        }

        /* ===== HEADER ===== */
        .page-header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 6px;
        }

        .page-header .title-main {
            font-size: 11.5pt;
            font-weight: bold;
            font-style: italic;
            letter-spacing: 0.5px;
        }

        .page-header .title-sub {
            font-size: 11.5pt;
            font-weight: bold;
            font-style: italic;
            letter-spacing: 0.5px;
        }

        .page-header .title-date {
            font-size: 10pt;
            font-weight: bold;
            margin-top: 2px;
            color: #000;
        }

        /* ===== EVENT TABLE ===== */
        .event-wrapper {
            margin-bottom: 14px;
            page-break-inside: avoid;
        }

        .event-header-row {
            width: 100%;
        }

        .event-title {
            float: left;
            font-size: 8pt;
            font-weight: bold;
        }

        .event-category {
            float: right;
            font-size: 8pt;
            font-weight: bold;
        }

        .clear {
            clear: both;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 7.5pt;
        }

        table thead tr {
            background-color: #2c3e7a;
            color: #ffffff;
        }

        table thead th {
            padding: 4px 5px;
            text-align: left;
            font-weight: bold;
            font-size: 7.5pt;
            border: 0.5pt solid #1a2560;
            white-space: nowrap;
        }

        table thead th.center {
            text-align: center;
        }

        table tbody tr:nth-child(even) {
            background-color: #f0f4ff;
        }

        table tbody tr:nth-child(odd) {
            background-color: #ffffff;
        }

        table tbody tr:hover {
            background-color: #dce6ff;
        }

        table tbody td {
            padding: 3px 5px;
            border: 0.5pt solid #c8d0e8;
            color: #000;
            vertical-align: middle;
        }

        table tbody td.rank {
            font-weight: bold;
            font-style: italic;
            text-align: center;
            color: #1a1a1a;
        }

        table tbody td.rank-1 { color: #b8860b; font-weight: bold; }
        table tbody td.rank-2 { color: #606060; font-weight: bold; }
        table tbody td.rank-3 { color: #8B4513; font-weight: bold; }

        table tbody td.nama {
            font-weight: bold;
            text-transform: uppercase;
        }

        table tbody td.time {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            white-space: nowrap;
        }

        table tbody td.hasil {
            text-align: center;
            font-family: 'Courier New', monospace;
            font-weight: bold;
            color: #cc0000;
        }

        table tbody td.ns {
            text-align: center;
            color: #999;
            font-style: italic;
        }

        table tbody td.dq {
            text-align: center;
            color: #cc0000;
            font-weight: bold;
        }

        table tbody td.nf {
            text-align: center;
            color: #cc0000;
            font-weight: bold;
        }

        table tbody td.center {
            text-align: center;
        }

        /* Col widths */
        .col-line  { width: 4%; }
        .col-nama  { width: 24%; }
        .col-papi  { width: 5%; }
        .col-yob   { width: 5%; }
        .col-age   { width: 7%; }
        .col-club  { width: 20%; }
        .col-kota  { width: 16%; }
        .col-best  { width: 9%; }
        .col-hasil { width: 10%; }

        /* ===== PAGE BREAK ===== */
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
{{-- <body style="margin: 25px 50px;"> --}}
<body>
    {{-- ===== PAGE HEADER ===== --}}
    <div class="page-header">
        <div class="title-main">KEJUARAAN DAERAH AQUATIK INDONESIA</div>
        <div class="title-sub">SUMATERA BARAT</div>
        <div class="title-date">{{ strtoupper($hariLabel ?? '----') }}</div>
    </div>

    {{-- ===== LOOP EVENTS ===== --}}
    @foreach ($events ?? collect() as $event)
    <div class="event-wrapper">

        <div class="event-header-row">
            <div class="event-title">{{ $event['event_label'] }}</div>
            <div class="event-category">{{ $event['category_label'] }}</div>
            <div class="clear"></div>
        </div>

        {{-- Results Table --}}
        <table>
            <thead>
                <tr>
                    <th class="col-line center">{{ $event['line_label'] ?? 'RANK' }}</th>
                    <th class="col-nama">NAMA</th>
                    <th class="col-papi center">PA/PI</th>
                    <th class="col-yob center">YOB</th>
                    <th class="col-age center">AGE</th>
                    <th class="col-club">CLUB</th>
                    <th class="col-kota">KOTA</th>
                    <th class="col-best center">BEST TIME</th>
                    <th class="col-hasil center">HASIL</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($event['results'] as $index => $row)
                <tr>
                    {{-- Rank / Line --}}
                    <td class="rank
                        @if($index === 0) rank-1
                        @elseif($index === 1) rank-2
                        @elseif($index === 2) rank-3
                        @endif
                    ">
                        {{ $row['rank'] ?? ($index + 1) }}
                    </td>

                    <td class="nama">{{ $row['nama'] }}</td>
                    <td class="center">{{ $row['papi'] }}</td>
                    <td class="center">{{ $row['yob'] }}</td>
                    <td class="center">{{ $row['age'] }}</td>
                    <td>{{ $row['club'] }}</td>
                    <td>{{ $row['kota'] }}</td>
                    <td class="time">{{ $row['best_time'] }}</td>
                    <td class="
                        @if(in_array(strtoupper($row['hasil']), ['NS', 'DNF', 'NF'])) ns
                        @elseif(strtoupper($row['hasil']) === 'DQ') dq
                        @else hasil
                        @endif
                    ">{{ $row['hasil'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center; color:#999; font-style:italic;">
                        Tidak ada data
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

    </div>
    @endforeach

    {{-- ===== PAGE FOOTER ===== --}}
    {{-- <div class="page-number">Halaman {{ $pageNumber ?? 1 }}</div> --}}

</body>
</html>
