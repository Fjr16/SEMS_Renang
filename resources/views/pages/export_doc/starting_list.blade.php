<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Starting List — Export Excel</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue-deep:   #0D47A1;
            --blue-mid:    #1565C0;
            --blue-light:  #1E88E5;
            --blue-pale:   #E3F2FD;
            --accent:      #00BCD4;
            --gold:        #FFD600;
            --text:        #1A1A2E;
            --text-muted:  #5C6BC0;
            --surface:     #F8FAFF;
            --border:      #C5CAE9;
            --white:       #FFFFFF;
            --check:       #00897B;
            --shadow:      0 4px 24px rgba(13,71,161,0.10);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--surface);
            color: var(--text);
            min-height: 100vh;
        }

        /* ── HERO HEADER ─────────────────────────────────────────── */
        .hero {
            background: linear-gradient(135deg, var(--blue-deep) 0%, var(--blue-light) 70%, var(--accent) 100%);
            padding: 40px 32px 32px;
            position: relative;
            overflow: hidden;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -60px; right: -60px;
            width: 260px; height: 260px;
            background: rgba(255,255,255,0.06);
            border-radius: 50%;
        }
        .hero::after {
            content: '';
            position: absolute;
            bottom: -40px; left: 10%;
            width: 160px; height: 160px;
            background: rgba(255,255,255,0.04);
            border-radius: 50%;
        }
        .hero-inner {
            max-width: 1200px;
            margin: 0 auto;
            position: relative;
            z-index: 1;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(255,255,255,0.15);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 100px;
            padding: 4px 14px;
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--gold);
            margin-bottom: 16px;
        }
        .hero-badge span { font-size: 16px; }
        .hero h1 {
            font-family: 'Bebas Neue', sans-serif;
            font-size: clamp(36px, 5vw, 58px);
            letter-spacing: 2px;
            color: var(--white);
            line-height: 1;
            margin-bottom: 8px;
        }
        .hero h1 em {
            font-style: normal;
            color: var(--gold);
        }
        .hero p {
            color: rgba(255,255,255,0.75);
            font-size: 14px;
            font-weight: 400;
            max-width: 480px;
        }

        /* ── MAIN LAYOUT ─────────────────────────────────────────── */
        .main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 20px 60px;
        }

        /* ── FORM CARD ───────────────────────────────────────────── */
        .card {
            background: var(--white);
            border-radius: 16px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            overflow: hidden;
            margin-bottom: 28px;
        }
        .card-header {
            background: var(--blue-pale);
            border-bottom: 1px solid var(--border);
            padding: 18px 24px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .card-header .icon {
            width: 36px; height: 36px;
            background: var(--blue-mid);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            font-size: 18px;
        }
        .card-header h2 {
            font-size: 15px;
            font-weight: 600;
            color: var(--blue-deep);
        }
        .card-body { padding: 24px; }

        /* ── FORM GRID ───────────────────────────────────────────── */
        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 20px;
        }
        .form-group { display: flex; flex-direction: column; gap: 6px; }
        .form-group label {
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
            color: var(--text-muted);
            text-transform: uppercase;
        }
        .form-group input, .form-group select {
            border: 1.5px solid var(--border);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 14px;
            font-family: inherit;
            color: var(--text);
            background: var(--surface);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }
        .form-group input:focus, .form-group select:focus {
            border-color: var(--blue-light);
            box-shadow: 0 0 0 3px rgba(30,136,229,0.12);
        }
        .form-group.full { grid-column: 1 / -1; }

        /* ── TABLE ───────────────────────────────────────────────── */
        .table-wrap {
            overflow-x: auto;
            border-radius: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            min-width: 900px;
        }
        thead tr:first-child th {
            background: var(--blue-deep);
            color: var(--white);
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 10px 8px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.1);
        }
        thead tr:last-child th {
            background: var(--blue-mid);
            color: rgba(255,255,255,0.9);
            font-size: 10px;
            padding: 6px 4px;
            text-align: center;
            border: 1px solid rgba(255,255,255,0.15);
        }
        tbody tr:nth-child(even) { background: #F5F7FF; }
        tbody tr:nth-child(odd)  { background: var(--white); }
        tbody tr:hover { background: var(--blue-pale); transition: background 0.15s; }
        tbody td {
            padding: 8px 6px;
            border: 1px solid #E8EBF5;
            text-align: center;
            vertical-align: middle;
        }
        tbody td.nama { text-align: left; padding-left: 10px; font-weight: 500; min-width: 180px; }
        .check-mark { color: var(--check); font-weight: 700; font-size: 14px; }
        .jumlah-cell { font-weight: 700; color: var(--blue-deep); }
        .biaya-cell  { font-weight: 600; color: #2E7D32; }

        /* ── FOOTER TABLE ────────────────────────────────────────── */
        tfoot td {
            background: var(--blue-pale);
            font-weight: 700;
            padding: 10px 8px;
            border: 1px solid var(--border);
        }
        tfoot tr:last-child td {
            background: var(--blue-deep);
            color: var(--white);
        }

        /* ── BUTTONS ─────────────────────────────────────────────── */
        .btn-row {
            display: flex;
            gap: 14px;
            flex-wrap: wrap;
            margin-top: 24px;
            align-items: center;
        }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border-radius: 12px;
            font-size: 14px;
            font-family: inherit;
            font-weight: 600;
            cursor: pointer;
            border: none;
            transition: all 0.2s;
            text-decoration: none;
        }
        .btn-primary {
            background: linear-gradient(135deg, var(--blue-mid), var(--blue-light));
            color: var(--white);
            box-shadow: 0 4px 14px rgba(21,101,192,0.35);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(21,101,192,0.45);
        }
        .btn-primary:active { transform: translateY(0); }
        .btn-outline {
            background: transparent;
            color: var(--blue-mid);
            border: 2px solid var(--blue-mid);
        }
        .btn-outline:hover { background: var(--blue-pale); }

        /* ── LEGEND ──────────────────────────────────────────────── */
        .legend {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            padding: 16px 24px;
            background: var(--blue-pale);
            border-top: 1px solid var(--border);
        }
        .legend-item {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 12px;
            color: var(--text-muted);
        }
        .legend-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
        }

        /* ── INFO BOX ────────────────────────────────────────────── */
        .info-box {
            display: flex;
            gap: 12px;
            background: #FFF8E1;
            border: 1px solid #FFE082;
            border-radius: 12px;
            padding: 14px 18px;
            margin-bottom: 24px;
            font-size: 13px;
            color: #5D4037;
        }
        .info-box .icon { font-size: 20px; flex-shrink: 0; }

        @media (max-width: 640px) {
            .hero { padding: 28px 16px 24px; }
            .main { padding: 20px 12px 40px; }
            .card-body { padding: 16px; }
        }
    </style>
</head>
<body>

<!-- HERO -->
<header class="hero">
    <div class="hero-inner">
        <div class="hero-badge"><span>🏊</span> Aquatic Management</div>
        <h1>Starting <em>List</em></h1>
        <p>Export daftar peserta perlombaan renang ke format Excel sesuai template resmi kejuaraan.</p>
    </div>
</header>

<main class="main">

    <!-- INFO -->
    <div class="info-box">
        <span class="icon">💡</span>
        <div>
            <strong>Cara penggunaan:</strong> Isi informasi event di bawah, pastikan data peserta sudah tersedia di database, lalu klik <strong>Export Excel</strong> untuk mengunduh file Starting List sesuai format kejuaraan.
        </div>
    </div>

    <!-- FORM EXPORT -->
    <div class="card">
        <div class="card-header">
            <div class="icon">⚙️</div>
            <h2>Konfigurasi Export</h2>
        </div>
        <div class="card-body">
            <form action="{{ route('starting-list.export') }}" method="POST">
                @csrf
                <div class="form-grid">
                    <div class="form-group full">
                        <label>Nama Event / Kejuaraan</label>
                        <input type="text" name="event_name"
                               value="KEJUARAN RENANG GB Open Swimming Championship"
                               placeholder="Nama kejuaraan..." required>
                    </div>
                    <div class="form-group">
                        <label>Nama Club</label>
                        <input type="text" name="club_name"
                               value="SeaRIA Aquatic PADANG"
                               placeholder="Nama club..." required>
                    </div>
                    <div class="form-group">
                        <label>Tahun</label>
                        <input type="text" name="year"
                               value="{{ date('Y') }}"
                               maxlength="4" placeholder="2024" required>
                    </div>
                    {{--
                    ── SESUAIKAN ──
                    Tambahkan filter tambahan sesuai kebutuhan, misal:
                    <div class="form-group">
                        <label>Kelompok Umur</label>
                        <select name="ku">
                            <option value="">Semua KU</option>
                            <option value="II">KU II</option>
                            <option value="III">KU III</option>
                            <option value="IV">KU IV</option>
                            <option value="V">KU V</option>
                            <option value="VI">KU VI</option>
                        </select>
                    </div>
                    --}}
                </div>

                <div class="btn-row">
                    <button type="submit" class="btn btn-primary">
                        📥 Export Excel
                    </button>
                    <a href="{{ route('starting-list.index') }}" class="btn btn-outline">
                        🔄 Refresh Data
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- PREVIEW TABLE -->
    <div class="card">
        <div class="card-header">
            <div class="icon">👁️</div>
            <h2>Preview Data Peserta ({{ count($peserta) }} orang)</h2>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th rowspan="2">NO</th>
                        <th rowspan="2">NAMA</th>
                        <th rowspan="2">KU</th>
                        <th rowspan="2">PA/PI</th>
                        <th colspan="6">GAYA BEBAS</th>
                        <th colspan="4">GAYA KUPU-KUPU</th>
                        <th colspan="3">GAYA PUNGGUNG</th>
                        <th colspan="4">GAYA DADA</th>
                        <th rowspan="2">GG<br>200M</th>
                        <th rowspan="2">JML</th>
                        <th rowspan="2">BIAYA</th>
                    </tr>
                    <tr>
                        <th>PPN75</th><th>25M</th><th>50M</th><th>100M</th><th>200M</th><th>400M</th>
                        <th>25M</th><th>50M</th><th>100M</th><th>200M</th>
                        <th>50M</th><th>100M</th><th>200M</th>
                        <th>25M</th><th>50M</th><th>100M</th><th>200M</th>
                    </tr>
                </thead>
                <tbody>
                    @php $totalNomor = 0; $totalBiaya = 0; $biayaPerNomor = 75000; @endphp
                    @foreach($peserta as $p)
                        @php
                            $fields = ['gb_ppn75','gb_25m','gb_50m','gb_100m','gb_200m','gb_400m',
                                       'gk_25m','gk_50m','gk_100m','gk_200m',
                                       'gp_50m','gp_100m','gp_200m',
                                       'gd_25m','gd_50m','gd_100m','gd_200m',
                                       'gg_200m'];
                            $jumlah = collect($fields)->filter(fn($f) => ($p[$f] ?? '') === 'V')->count();
                            $biaya  = $jumlah * $biayaPerNomor;
                            $totalNomor += $jumlah;
                            $totalBiaya += $biaya;
                        @endphp
                        <tr>
                            <td>{{ $p['no'] }}</td>
                            <td class="nama">{{ $p['nama'] }}</td>
                            <td>{{ $p['ku'] }}</td>
                            <td>{{ $p['pa_pi'] }}</td>
                            @foreach($fields as $f)
                                <td>
                                    @if(($p[$f] ?? '') === 'V')
                                        <span class="check-mark">✓</span>
                                    @endif
                                </td>
                            @endforeach
                            <td class="jumlah-cell">{{ $jumlah }}</td>
                            <td class="biaya-cell">{{ number_format($biaya, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="21" style="text-align:right;">TOTAL NOMOR PERLOMBAAN:</td>
                        <td class="jumlah-cell">{{ $totalNomor }}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="21" style="text-align:right;">JUMLAH UANG PENDAFTARAN:</td>
                        <td></td>
                        <td>Rp {{ number_format($totalBiaya, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="legend">
            <div class="legend-item">
                <div class="legend-dot" style="background:#00897B"></div>
                <span>✓ = Mengikuti nomor ini</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#E0E0E0"></div>
                <span>Kosong = Tidak mengikuti</span>
            </div>
            <div class="legend-item">
                <div class="legend-dot" style="background:#1565C0"></div>
                <span>Biaya per nomor: Rp 75.000</span>
            </div>
        </div>
    </div>

</main>

</body>
</html>
