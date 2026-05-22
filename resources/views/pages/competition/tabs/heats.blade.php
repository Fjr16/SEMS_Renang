<script>
    // Inject ke window agar accessible dari parent dan setelah re-render
    window.HEAT_CONFIG = {
        poolLanes:   {{ $totalLanes }},
        totalAtlet:  {{ $totalEntries }},
        eventId:     {{ $event?->id }},
        generateUrl: "{{ route('competition.heats.generate', $competition) }}",
        resetUrl: "{{ route('competition.heats.resetByEvent', $competition) }}",
        generateByRound: "{{ route('competition.heats.generateByRound', $competition) }}",
        reloadUrl:   "{{ route('competition.heats.partial', $competition) }}",
        saveResultUrl:   "{{ route('competition.heats.saveResult', $competition) }}",
        promoteAtletUrl:   "{{ route('competition.heats.promoteAthletes', $competition) }}",
    };
</script>

@php
    $resultStatuses = collect(App\Enums\CompetitionResultStatus::cases())
    ->mapWithKeys(fn($case) => [
        $case->value => [
            'val' => $case->value,
            'label' => $case->shortLabel(),
            'style' => $case->styles(),
        ]
    ]);
    $recordTypes = collect(App\Enums\RecordTypeEnum::cases())
    ->mapWithKeys(fn($case) => [$case->value => $case->shortLabel()]);

    $heatsData = $heatsByRound?->map(fn($heats) =>
        $heats?->sortBy('heat_number')->map(fn($heat) => [
            'id'     => $heat->id,
            'number' => $heat->heat_number,
            'lanes'  => $heat->heatLanes->map(fn($lane) => [
                'lane_id'     => $lane->id,
                'lane_number' => $lane->lane_number,
                'lane_order'  => $lane->lane_order,
                'athlete'     => $lane->entry?->athlete?->name ?? null,
                'club'        => $lane->entry?->athlete?->club?->club_name ?? null,
                'entry_time'  => $lane?->entry?->seed_time ?? null,
                'swim_time'     => $lane->swim_time,
                'status'        => $lane->status ?? $resultStatuses->keys()->first(),
                'rank_heat'     => $lane->rank_in_heat,
                'record_types'  => explode(',', ($lane->record_type ?? '')),
            ])->values()->toArray(),
        ])->values()->toArray()
    )->toArray();
@endphp

<div id="heatMainContent"
    data-pool-lanes="{{ $totalLanes }}"
    data-total-atlet="{{ $totalEntries }}"
    data-event-id="{{ $event?->id }}"
>

    <script id="heatDataScript">
        var HEATS_DATA      = (@json($heatsData));
        var RESULT_STATUSES = (@json($resultStatuses));
        var RECORD_TYPES    = (@json($recordTypes));
    </script>


    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
        <div>
            <h5 class="fw-bold mb-1">Manajemen Seri Perlombaan</h5>
            <p class="text-muted mb-0" style="font-size:13px">Kelola seri perlombaan untuk tiap event</p>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            {{-- select event --}}
            <div class="d-flex align-items-center gap-2 px-3 py-2"
                 style="background:#f8f9fa; border-bottom:1px solid #dee2e6">
                <select id="heat_competition_event_id"
                        name="heat_competition_event_id"
                        class="form-control form-control-sm flex-grow-1"
                        style="font-size:13px">
                    @foreach ($selectEvents as $e)
                        <option value="{{ $e->id }}" {{ $e->id === $event?->id ? 'selected' : '' }}>
                            {{ $e->getLabel() }}
                        </option>
                    @endforeach
                </select>
            </div>
            {{-- end select event --}}

            {{-- Info Bar --}}
            <div class="d-flex align-items-center gap-4 px-3 py-2 flex-wrap"
                 style="background:#E6F1FB; border-bottom:1px solid #B5D4F4; font-size:12px; color:#0C447C">
                <div>
                    <div style="font-size:10px; text-transform:uppercase; letter-spacing:.04em; color:#185FA5">Total Peserta</div>
                    <div style="font-size:14px; font-weight:500">{{ $totalEntries ?? '0' }} Entri</div>
                </div>
                <div>
                    <div style="font-size:10px; text-transform:uppercase; letter-spacing:.04em; color:#185FA5">Kapasitas Kolam</div>
                    <div style="font-size:14px; font-weight:500">{{ $totalLanes ?? '-' }} Lintasan</div>
                    <div style="font-size:10px; color:#185FA5">dari venue</div>
                </div>
                <div>
                    <div style="font-size:10px; text-transform:uppercase; letter-spacing:.04em; color:#185FA5">Estimasi Seri</div>
                    <div style="font-size:14px; font-weight:500" id="estimasiSeri">—</div>
                </div>
                <div class="ms-auto">
                    <button class="btn btn-sm btn-primary" style="font-size:11px"
                        onclick="resetHeatConfig()">
                        <i class="bi bi-grid me-1"></i> Regenerate
                    </button>
                </div>
            </div>

            @if($selectEvents->isEmpty())
            <div class="px-3 py-5 text-center">
                <i class="bi bi-people" style="font-size:32px;color:#adb5bd"></i>
                <p class="fw-semibold mt-2 mb-1" style="font-size:14px">Belum ada event yang ditambahkan</p>
                <p class="text-muted" style="font-size:12px">Tambahkan event / acara ke kompetisi ini terlebih dahulu.</p>
            </div>
            @elseif($selectEvents->isNotEmpty() && !$totalEntries)
            <div class="px-3 py-5 text-center">
                <i class="bi bi-people" style="font-size:32px;color:#adb5bd"></i>
                <p class="fw-semibold mt-2 mb-1" style="font-size:14px">Belum ada peserta terdaftar</p>
                <p class="text-muted" style="font-size:12px">Tambahkan peserta ke event ini terlebih dahulu sebelum membuat seri perlombaan.</p>
            </div>
            @else
                @if ($heatsByRound?->isEmpty())
                    <div class="px-3 py-3" style="border-bottom:1px solid #dee2e6">
                        <div class="text-uppercase fw-semibold mb-2"
                            style="font-size:11px; color:#6c757d; letter-spacing:.05em">
                            <span class="badge bg-primary rounded-circle me-1" style="font-size:10px">1</span>
                            Pilih struktur ronde
                        </div>
                        <div class="d-flex gap-2 flex-wrap" id="roundOptions">
                            @foreach([
                                'final'         => ['label' => 'Final saja',                       'sub' => 'Langsung final, tanpa penyisihan'],
                                'pre_final'     => ['label' => 'Penyisihan + Final',                'sub' => '2 ronde'],
                                'pre_semi_final'=> ['label' => 'Penyisihan + Semifinal + Final',    'sub' => '3 ronde'],
                            ] as $type => $opt)
                                <div class="round-option p-3 border rounded"
                                    style="cursor:pointer; min-width:160px; font-size:13px"
                                    data-type="{{ $type }}"
                                    onclick="selectRoundOption('{{ $type }}', this)">
                                    <div class="fw-semibold">{{ $opt['label'] }}</div>
                                    <div class="text-muted" style="font-size:11px">{{ $opt['sub'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Step 2: Konfigurasi per ronde --}}
                    <div class="px-3 py-3" id="roundConfigSection" style="display:none; border-bottom:1px solid #dee2e6">
                        <div class="text-uppercase fw-semibold mb-2"
                            style="font-size:11px; color:#6c757d; letter-spacing:.05em">
                            <span class="badge bg-primary rounded-circle me-1" style="font-size:10px">2</span>
                            Konfigurasi per ronde
                        </div>
                        <div class="table-responsive">
                            <table class="table table-sm table-bordered align-middle mb-1" style="font-size:12px">
                                <thead class="table-light">
                                    <tr>
                                        <th>Ronde</th>
                                        <th>Lane digunakan</th>
                                        <th>Preview lane aktif</th>
                                        <th>Atlet lolos ke ronde berikutnya</th>
                                    </tr>
                                </thead>
                                <tbody id="roundConfigRows"></tbody>
                            </table>
                        </div>
                        <div class="text-muted" style="font-size:11px">
                            * Lane aktif dihitung dari tengah pool (circle seeding). Maksimal {{ $totalLanes }} lane.
                        </div>
                    </div>

                    {{-- Actions --}}
                    <div class="d-flex justify-content-end px-3 py-2" style="background:#f8f9fa">
                        <button class="btn btn-primary btn-sm" id="btnGenerateHeat"
                                onclick="submitGenerate()" disabled style="font-size:12px">
                            <i class="bi bi-grid me-1"></i> Generate Seri
                        </button>
                    </div>
                @else
                    <div class="d-flex border-bottom" style="background:#fff">
                        @foreach ($roundConfig as $round_type => $round)
                            <button class="btn btn-link btn-sm text-decoration-none fw-semibold px-3 py-2 tab-round-btn"
                                data-round="{{ $round_type }}"
                                style="font-size:13px; border-bottom:2px solid transparent; border-radius:0; color:#6c757d"
                                onclick="switchRoundTab('{{ $round_type }}', this)">
                                {{ App\Enums\RoundTypeEnum::tryFrom($round_type)->label() }}
                                <span class="badge rounded-pill ms-1" style="font-size: 10px; background: #E6F1FB; color:#0C447C;">
                                    {{ $heatsByRound->has($round_type) ? $heatsByRound[$round_type]->count() : '0' }} seri
                                </span>
                            </button>
                        @endforeach
                    </div>

                    {{-- Panel per Ronde --}}
                    @foreach ($roundConfig as $roundType => $round)
                        @php $isFirst = $loop->first; @endphp
                        <div class="round-panel" data-round="{{ $roundType }}"
                            style="display:{{ $isFirst ? 'block' : 'none' }}">

                            {{-- Toolbar --}}
                            <div class="d-flex align-items-center gap-2 px-3 py-2 flex-wrap"
                                style="background:#f8f9fa; border-bottom:1px solid #dee2e6; font-size:12px">
                                <span class="text-muted">
                                    {{ $heatsByRound->has($roundType) ? $heatsByRound[$roundType]->sum(fn($h) => $h->heatLanes->count()) : '0' }} entri / atlet ·
                                    {{ $round->used_lanes ?? '-' }} lintasan digunakan ·
                                    {{ $heatsByRound->has($roundType) ? $heatsByRound[$roundType]->count() : '0' }} seri ·
                                    Jumlah lolos : {{ $round->qualify_count ?? '-' }} entri / atlet
                                </span>
                                <div class="ms-auto d-flex gap-2">
                                    @if (!$loop->first)
                                        <button class="btn btn-sm btn-outline-success" style="font-size:11px"
                                            onclick="promoteAthletes('{{ $roundType }}')">
                                            <i class="bi bi-arrow-repeat me-1"></i> Promosi Atlet
                                        </button>
                                    @endif
                                    {{-- Tombol Input Hasil --}}
                                    <button class="btn btn-sm btn-success" style="font-size:11px"
                                        onclick="openResultDrawer('{{ $roundType }}')">
                                        <i class="bi bi-pencil-square me-1"></i> Input Hasil
                                    </button>
                                </div>
                            </div>

                            {{-- Tab Heat --}}
                            <div class="d-flex align-items-center border-bottom px-2" style="background:#fff; gap:2px">
                                <button class="nav-btn btn btn-link btn-sm text-muted p-1"
                                    onclick="slideHeatTab('{{ $roundType }}', -1)">
                                    <i class="bi bi-chevron-left" style="font-size:11px"></i>
                                </button>
                                <div style="overflow:hidden; flex:1">
                                    <div class="heat-tab-inner d-flex" id="heat-inner-{{ $roundType }}">
                                        @if ($heatsByRound->has($roundType))
                                        @foreach ($heatsByRound[$roundType]->sortBy('heat_number') as $heat)
                                            <button class="btn btn-link btn-sm text-decoration-none px-3 py-2 tab-heat-btn flex-shrink-0"
                                                data-round="{{ $roundType }}"
                                                data-heat="{{ $heat->heat_number }}"
                                                style="font-size:12px; border-bottom:2px solid transparent; border-radius:0; color:#6c757d; white-space:nowrap"
                                                onclick="switchHeatTab('{{ $roundType }}', {{ $heat->heat_number }}, this)">
                                                Seri {{ $heat->heat_number }}
                                            </button>
                                        @endforeach
                                        @endif
                                    </div>
                                </div>
                                <button class="nav-btn btn btn-link btn-sm text-muted p-1"
                                    onclick="slideHeatTab('{{ $roundType }}', 1)">
                                    <i class="bi bi-chevron-right" style="font-size:11px"></i>
                                </button>
                            </div>

                            {{-- Panel per Heat --}}
                            @if ($heatsByRound->has($roundType))
                            @foreach ($heatsByRound[$roundType]->sortBy('heat_number') as $heat)
                                <div class="heat-panel table-responsive"
                                    data-round="{{ $roundType }}"
                                    data-heat="{{ $heat->heat_number }}"
                                    style="display:{{ $loop->first ? 'block' : 'none' }}">
                                    <table class="table table-hover mb-0" style="font-size:13px">
                                        <thead>
                                            <tr class="text-center">
                                                {{-- <th>Peringkat</th> --}}
                                                <th>Lintasan</th>
                                                <th>Atlet</th>
                                                <th>Tim / Klub</th>
                                                <th>Waktu Entri</th>
                                                <th>Hasil</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($heat->heatLanes as $lane)
                                            @php
                                                // $result = $lane->result ?? null;
                                                $status = $lane->status ?? null;
                                                $statusBadge = match($status) {
                                                    $resultStatuses['dns']['val'] => '<span class="badge" style="background:#F1EFE8;color:#5F5E5A;font-size:10px">'.$resultStatuses['dns']['label'].'</span>',
                                                    $resultStatuses['dnf']['val'] => '<span class="badge" style="background:#FAEEDA;color:#854F0B;font-size:10px">'.$resultStatuses['dnf']['label'].'</span>',
                                                    $resultStatuses['dq']['val']  => '<span class="badge" style="background:#FCEBEB;color:#A32D2D;font-size:10px">'.$resultStatuses['dq']['label'].'</span>',
                                                    $resultStatuses['valid']['val']  => '<span class="badge" style="background:#FCEBEB;color:#15803D;font-size:10px">'.$resultStatuses['valid']['label'].'</span>',
                                                    default => '-',
                                                };
                                            @endphp
                                                <tr class="text-center">
                                                    {{-- <td>{{ $lane->lane_order ?? '-' }}</td> --}}
                                                    <td><strong>{{ $lane->lane_number }}</strong></td>
                                                    <td>{{ $lane->entry?->athlete?->name ?? '-' }}</td>
                                                    <td class="text-muted">{{ $lane->entry?->athlete?->club?->club_name ?? '-' }}</td>
                                                    <td style="font-family:monospace; font-size:12px">
                                                        {{ $lane->entry->seed_time ?? 'NT' }}
                                                    </td>
                                                    <td style="font-family:monospace;font-size:12px">
                                                        @if($status === $resultStatuses['dns']['val'] || $status === $resultStatuses['dnf']['val'] || $status === $resultStatuses['dq']['val'])
                                                            {!! $statusBadge !!}
                                                        @else
                                                            {{ $lane->swim_time ?? '—' }}
                                                        @endif
                                                    </td>
                                                    <td>{!! $statusBadge !!}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">
                                                        Belum ada atlet di seri ini.
                                                    </td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach
                            @endif

                        </div>
                    @endforeach
                @endif
            @endif
        </div>
    </div>
</div>

{{-- ============================================================
     DRAWER: Input Hasil Seri
     ============================================================ --}}
<div id="resultDrawerOverlay"
    style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.2); z-index:1040"
    onclick="closeResultDrawer()">
</div>

<div id="resultDrawer"
    style="
        position: fixed;
        top: 0; right: 0;
        width: min(620px, 95vw);
        height: 100vh;
        background: #fff;
        border-left: 1px solid #dee2e6;
        z-index: 1050;
        display: flex;
        flex-direction: column;
        transform: translateX(100%);
        transition: transform .25s ease;
        box-shadow: -4px 0 24px rgba(0,0,0,0.08);
    ">

    {{-- Drawer Header --}}
    <div style="padding:14px 18px; border-bottom:1px solid #dee2e6; flex-shrink:0; background:#fff">
        <div class="d-flex align-items-start justify-content-between mb-2">
            <div>
                <div id="drawerTitle" style="font-size:15px; font-weight:600; color:#212529">
                    Input Hasil — Seri 1
                </div>
                <div id="drawerSubtitle" style="font-size:12px; color:#6c757d; margin-top:2px">
                    —
                </div>
            </div>
            <button onclick="closeResultDrawer()"
                style="background:none; border:none; font-size:22px; line-height:1; color:#6c757d; cursor:pointer; padding:0 4px">
                &times;
            </button>
        </div>

        {{-- Heat tabs dalam drawer --}}
        <div class="d-flex align-items-center gap-1" style="overflow-x:auto; padding-bottom:2px">
            <button onclick="drawerSlideHeat(-1)"
                style="background:none; border:0.5px solid #dee2e6; border-radius:4px; padding:2px 7px; font-size:13px; color:#6c757d; cursor:pointer; flex-shrink:0">
                &#8249;
            </button>
            <div id="drawerHeatTabs" style="display:flex; gap:2px; overflow:hidden; flex:1"></div>
            <button onclick="drawerSlideHeat(1)"
                style="background:none; border:0.5px solid #dee2e6; border-radius:4px; padding:2px 7px; font-size:13px; color:#6c757d; cursor:pointer; flex-shrink:0">
                &#8250;
            </button>
        </div>

        {{-- Stat bar --}}
        <div class="d-flex gap-3 mt-2" id="drawerStatBar" style="font-size:12px; color:#6c757d"></div>
    </div>

    {{-- Drawer Body --}}
    <div style="flex:1; overflow-y:auto; padding:0">
        <div class="table-responsive">
            <table style="width:100%; border-collapse:collapse; font-size:12px; table-layout:fixed">
                <colgroup>
                    <col style="width:36px">  {{-- No Lane --}}
                    <col style="width:120px"> {{-- Atlet --}}
                    <col style="width:64px">  {{-- Entry Time --}}
                    <col style="width:66px">  {{-- Status --}}
                    <col style="width:76px">  {{-- Waktu Finish --}}
                    <col style="width:38px">  {{-- Rank --}}
                    <col>                     {{-- Rekor --}}
                </colgroup>
                <thead>
                    <tr style="background:#f8f9fa; border-bottom:1px solid #dee2e6">
                        <th style="padding:7px 8px; font-size:10px; font-weight:600; color:#6c757d; text-transform:uppercase; letter-spacing:.04em">Lane</th>
                        <th style="padding:7px 8px; font-size:10px; font-weight:600; color:#6c757d; text-transform:uppercase; letter-spacing:.04em">Atlet</th>
                        <th style="padding:7px 8px; font-size:10px; font-weight:600; color:#6c757d; text-transform:uppercase; letter-spacing:.04em">Entri</th>
                        <th style="padding:7px 8px; font-size:10px; font-weight:600; color:#6c757d; text-transform:uppercase; letter-spacing:.04em">Status</th>
                        <th style="padding:7px 8px; font-size:10px; font-weight:600; color:#6c757d; text-transform:uppercase; letter-spacing:.04em">Finish</th>
                        <th style="padding:7px 8px; font-size:10px; font-weight:600; color:#6c757d; text-transform:uppercase; letter-spacing:.04em; text-align:center">Rank</th>
                        <th style="padding:7px 8px; font-size:10px; font-weight:600; color:#6c757d; text-transform:uppercase; letter-spacing:.04em">Rekor</th>
                    </tr>
                </thead>
                <tbody id="drawerTableBody">
                    <tr>
                        <td colspan="8" style="text-align:center; padding:32px; color:#adb5bd; font-size:13px">
                            Pilih seri untuk mulai input hasil
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Drawer Footer --}}
    <div style="padding:10px 18px; border-top:1px solid #dee2e6; background:#f8f9fa; flex-shrink:0">
        <div class="d-flex align-items-center gap-2">
            <span id="drawerFooterNote" style="font-size:11px; color:#6c757d; flex:1">
                Rank dihitung otomatis dari waktu finish. Bisa di-override manual.
            </span>
            <button onclick="closeResultDrawer()"
                style="padding:6px 14px; font-size:12px; border-radius:6px; border:1px solid #dee2e6; background:#fff; color:#6c757d; cursor:pointer">
                Batal
            </button>
            <button id="drawerSaveBtn" onclick="saveHeatResult()"
                style="padding:6px 18px; font-size:12px; border-radius:6px; border:none; background:#185FA5; color:#fff; cursor:pointer; font-weight:500">
                Simpan
            </button>
        </div>
    </div>
</div>

{{-- ============================================================
     SCRIPT: Drawer Logic
     ============================================================ --}}
<script>
(function () {

    /* ── State ─────────────────────────────────────────────── */
    let _activeRound   = null;
    let _activeHeatIdx = 0;
    let _heatsInRound  = [];   // [{id, number, lanes:[{...}]}]
    let _drawerOffset  = 0;

    /* ── Data dari Blade ───────────────────────────────────── */
    // const HEATS_DATA = @json($heatsData);
    // const RESULT_STATUSES = @json($resultStatuses);
    // const RECORD_TYPES = @json($recordTypes);

    /* ── Buka Drawer ───────────────────────────────────────── */
    window.openResultDrawer = function(roundType) {
        _activeRound   = roundType;
        _heatsInRound  = HEATS_DATA[roundType] ?? [];
        _activeHeatIdx = 0;
        _drawerOffset  = 0;

        buildDrawerHeatTabs();
        renderDrawerHeat(_activeHeatIdx);

        document.getElementById('resultDrawerOverlay').style.display = 'block';
        document.getElementById('resultDrawer').style.transform       = 'translateX(0)';
    };

    /* ── Tutup Drawer ──────────────────────────────────────── */
    window.closeResultDrawer = function() {
        document.getElementById('resultDrawer').style.transform       = 'translateX(100%)';
        document.getElementById('resultDrawerOverlay').style.display = 'none';
    };

    /* ── Tab Heat di Drawer ────────────────────────────────── */
    function buildDrawerHeatTabs() {
        const container = document.getElementById('drawerHeatTabs');
        container.innerHTML = _heatsInRound.map((h, i) => {
            const dotColor = h.status === 'complete' ? '#1D9E75'
                           : h.status === 'partial'  ? '#EF9F27'
                           : '#B4B2A9';
            return `<button
                id="dht-${i}"
                onclick="drawerSwitchHeat(${i})"
                style="
                    padding:4px 12px; font-size:12px; white-space:nowrap; flex-shrink:0;
                    border:none; border-bottom:2px solid transparent;
                    background:transparent; color:#6c757d; cursor:pointer; border-radius:0
                ">
                <span style="display:inline-block;width:7px;height:7px;border-radius:50%;
                    background:${dotColor};margin-right:4px;vertical-align:middle"></span>
                Seri ${h.number}
            </button>`;
        }).join('');
        highlightDrawerTab(0);
    }

    function highlightDrawerTab(idx) {
        document.querySelectorAll('#drawerHeatTabs button').forEach((b, i) => {
            b.style.borderBottomColor = i === idx ? '#185FA5' : 'transparent';
            b.style.color             = i === idx ? '#185FA5' : '#6c757d';
            b.style.fontWeight        = i === idx ? '600' : '400';
        });
    }

    window.drawerSwitchHeat = function(idx) {
        _activeHeatIdx = idx;
        highlightDrawerTab(idx);
        renderDrawerHeat(idx);
    };

    window.drawerSlideHeat = function(dir) {
        const next = _activeHeatIdx + dir;
        if (next < 0 || next >= _heatsInRound.length) return;
        drawerSwitchHeat(next);
        const btn = document.getElementById(`dht-${next}`);
        if (btn) btn.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'nearest' });
    };

    /* ── Render Tabel Heat ─────────────────────────────────── */
    function renderDrawerHeat(idx) {
        const heat = _heatsInRound[idx];
        if (!heat) return;

        // Update header
        document.getElementById('drawerTitle').textContent =
            `Input Hasil — Seri ${heat.number}`;

        // Update subtitle (ambil dari label ronde)
        const roundBtn = document.querySelector(`.tab-round-btn[data-round="${_activeRound}"]`);
        const roundLabel = roundBtn ? roundBtn.textContent.trim().split('\n')[0].trim() : _activeRound;
        document.getElementById('drawerSubtitle').textContent =
            `${roundLabel} · ${document.getElementById('heat_competition_event_id')?.selectedOptions[0]?.text ?? ''}`;

        // Stat bar
        const filled = heat.lanes.filter(l => l.swim_time || l.status).length;
        document.getElementById('drawerStatBar').innerHTML = `
            <span>Lintasan terisi: <strong style="color:#212529">${heat.lanes.length}</strong></span>
            <span>Hasil diinput: <strong style="color:#212529">${filled}</strong> / ${heat.lanes.length}</span>
            <span id="drawerStatusLabel">${statusLabel(filled, heat.lanes.length)}</span>
        `;

        // Body tabel
        // calcRanks(heat.lanes);
        document.getElementById('drawerTableBody').innerHTML =
            heat.lanes.map((lane, li) => buildLaneRow(lane, li, idx)).join('');
    }

    function statusLabel(filled, total) {
        if (filled === 0)     return `<span style="color:#888">Belum diisi</span>`;
        if (filled < total)   return `<span style="color:#854F0B">Sebagian</span>`;
        return `<span style="color:#0F6E56;font-weight:500">Lengkap</span>`;
    }

    /* ── Build Baris Lane ──────────────────────────────────── */
    function buildLaneRow(lane, li, heatIdx) {
        const r      = lane ?? {};
        const status = r.status ?? RESULT_STATUSES['valid'].val;
        const isDis  = status !== RESULT_STATUSES['valid'].val ? 'disabled' : '';

        const statusOpts = Object.entries(RESULT_STATUSES).map(([value, data]) =>
            `<option value="${value}" ${status === value ? 'selected' : ''}>${data.label}</option>`
        ).join('');

        const selectStyle = RESULT_STATUSES[status]?.style ?? RESULT_STATUSES['valid'].style;

        const recTags = Object.entries(RECORD_TYPES).map(([val, label]) => {
            const active = (r.record_types ?? []).includes(val);
            return `<button type="button"
                onclick="toggleRecordTag(${heatIdx},${li},'${val}',this)"
                style="
                    font-size:10px; padding:1px 5px; border-radius:8px; cursor:pointer;
                    border:0.5px solid ${active ? '#EF9F27' : '#dee2e6'};
                    background:${active ? '#FAEEDA' : 'transparent'};
                    color:${active ? '#854F0B' : '#6c757d'};
                ">${label}</button>`;
        }).join('');

        const rankDisplay = (!r.swim_time || status === RESULT_STATUSES['dns'].val || status === RESULT_STATUSES['dq'].val || status === RESULT_STATUSES['dnf'].val)
            ? `<span style="color:#adb5bd; font-size:11px">—</span>`
            : (r.rank_heat ? `<span style="
                        display:inline-flex; align-items:center; justify-content:center;
                        width:24px; height:24px; border-radius:50%;
                        background:#E6F1FB; color:#0C447C;
                        font-size:11px; font-weight:600">
                        ${r.rank_heat}
                    </span>`
                : `<span style="color:#adb5bd; font-size:11px">—</span>`);

        const athleteDisplay = lane.athlete
            ? `<div style="font-size:12px;font-weight:500;color:#212529">${lane.athlete}</div>
               <div style="font-size:10px;color:#adb5bd">${lane.club ?? ''}</div>`
            : `<span style="color:#adb5bd">—</span>`;

        return `<tr style="border-bottom:0.5px solid #f0f0f0" id="lane-row-${heatIdx}-${li}">
            <td style="padding:6px 8px;text-align:center">
                <span style="display:inline-flex;align-items:center;justify-content:center;
                    width:24px;height:24px;border-radius:50%;background:#f1f3f5;
                    font-size:11px;font-weight:500">${lane.lane_number}</span>
            </td>
            <td style="padding:6px 8px">${athleteDisplay}</td>
            <td style="padding:6px 8px;font-family:monospace;font-size:11px;color:#888">
                ${lane.entry_time ?? 'NT'}
            </td>
            <td style="padding:6px 8px">
                <select onchange="updateStatus(${heatIdx},${li},this)"
                    style="width:100%;font-size:11px;padding:3px 4px;
                        border:0.5px solid #dee2e6;border-radius:4px;${selectStyle}">
                    ${statusOpts}
                </select>
            </td>
            <td style="padding:6px 8px">
                <input type="text" ${isDis}
                    class="swim_time_input"
                    value="${r.swim_time ?? ''}"
                    placeholder="00:00.00"
                    maxlength="9"
                    onchange="updateField(${heatIdx},${li},'swim_time',this.value.trim());recalcAndRender(${heatIdx})"
                    style="width:100%;font-size:11px;padding:3px 5px;
                        border:0.5px solid #dee2e6;border-radius:4px;
                        background:${isDis ? '#f8f9fa' : '#fff'};
                        font-family:monospace;color:#212529;opacity:${isDis ? '.4' : '1'}">
            </td>
            <td style="padding:6px 8px;text-align:center">${rankDisplay}</td>
            <td style="padding:6px 8px">
                <div style="display:flex;flex-wrap:wrap;gap:2px">${recTags}</div>
            </td>
        </tr>`;
    }

    /* ── Field Update ──────────────────────────────────────── */
    window.updateField = function(heatIdx, laneIdx, field, value) {
        const lane = _heatsInRound[heatIdx].lanes[laneIdx];
        lane[field] = value;
        updateStatBar(heatIdx);
    };

    window.updateStatus = function(heatIdx, laneIdx, sel) {
        const lane = _heatsInRound[heatIdx].lanes[laneIdx];
        lane.status = sel.value;
        if (sel.value !== RESULT_STATUSES['valid'].val) {
            lane.swim_time     = '';
        }
        renderDrawerHeat(heatIdx);
    };

    // window.updateRank = function(heatIdx, laneIdx, value) {
    //     const lane = _heatsInRound[heatIdx].lanes[laneIdx];
    //     lane.rank_heat = parseInt(value) || null;
    // };

    window.toggleRecordTag = function(heatIdx, laneIdx, tag, btn) {
        const lane = _heatsInRound[heatIdx].lanes[laneIdx];
        if (!lane.record_types) lane.record_types = [];
        const idx = lane.record_types.indexOf(tag);
        if (idx === -1) {
            lane.record_types.push(tag);
            btn.style.background   = '#FAEEDA';
            btn.style.color        = '#854F0B';
            btn.style.borderColor  = '#EF9F27';
        } else {
            lane.record_types.splice(idx, 1);
            btn.style.background   = 'transparent';
            btn.style.color        = '#6c757d';
            btn.style.borderColor  = '#dee2e6';
        }
    };

    function timeToMs(timeStr) {
        if (!timeStr) return Infinity;
        const parts = timeStr.split(':');
        if (parts.length === 2) {
            const minutes = parseInt(parts[0]);
            const [secs, ms] = parts[1].split('.');
            return (minutes * 60 * 100) + (parseInt(secs) * 100) + parseInt(ms ?? 0);
        } else {
            const [secs, ms] = parts[0].split('.');
            return (parseInt(secs) * 100) + parseInt(ms ?? 0);
        }
    }

    /* ── Hitung Rank Otomatis ──────────────────────────────── */
    function calcRanks(lanes) {
        lanes.forEach(l => l.rank_heat = null);

        const valid = lanes.filter(l =>
            l.status === RESULT_STATUSES['valid'].val && l.swim_time
        );
        valid.sort((a, b) => timeToMs(a.swim_time) - timeToMs(b.swim_time));
        let rank = 1;
        valid.forEach((l, i) => {
            if (i > 0 && timeToMs(l.swim_time) === timeToMs(valid[i-1].swim_time)) {
                l.rank_heat = valid[i-1].rank_heat;
            } else {
                l.rank_heat = rank;
            }
            rank++;
        });
    }

    window.recalcAndRender = function(heatIdx) {
        calcRanks(_heatsInRound[heatIdx].lanes);
        renderDrawerHeat(heatIdx);
    };

    /* ── Stat Bar Update ───────────────────────────────────── */
    function updateStatBar(heatIdx) {
        const heat   = _heatsInRound[heatIdx];
        const filled = heat.lanes.filter(l => l.swim_time || l.status).length;
        document.getElementById('drawerStatBar').innerHTML = `
            <span>Lintasan terisi: <strong style="color:#212529">${heat.lanes.length}</strong></span>
            <span>Hasil diinput: <strong style="color:#212529">${filled}</strong> / ${heat.lanes.length}</span>
            <span>${statusLabel(filled, heat.lanes.length)}</span>
        `;
    }

    /* ── Save (stub — isi action nanti) ────────────────────── */
    window.saveHeatResult = function() {
        const eventId = document.getElementById('heat_competition_event_id')?.selectedOptions[0]?.value ?? '';
        const heat    = _heatsInRound[_activeHeatIdx];
        const payload = heat.lanes.map(l => ({
                lane_id:       l.lane_id,
                swim_time:     l.swim_time     ?? null,
                status:        l.status        ?? RESULT_STATUSES['valid'].val,
                rank_heat:     l.rank_heat     ?? null,
                record_types:  l.record_types  ?? [],
            }));
        fetch(HEAT_CONFIG.saveResultUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' },
            body: JSON.stringify(payload),
        })
        .then(async r => {
            const data = await r.json();
            if (!r.ok) throw new Error(data.message || 'Terjadi kesalahan pada server');
            return data;
        })
        .then(data => {
            if (data.status){
                Toast.fire({
                    icon:'success',
                    title:data.message || 'Sukses'
                });
                const note = document.getElementById('drawerFooterNote');
                note.textContent = 'Hasil disimpan!';
                note.style.color = '#0F6E56';
                setTimeout(() => {
                    note.textContent = 'Rank dihitung otomatis dari waktu finish. Bisa di-override manual.';
                    note.style.color = '';
                }, 2500);
                reloadHeatTab(eventId);

            }else{
                Toast.fire({
                    icon:'error',
                    title:data.message || 'Gagal'
                });
            }
        })
        .catch(error => {
            Toast.fire({
                icon:'error',
                title:error.message || 'Gagal generate seri. Silakan coba lagi.'
            });
        });
    };

    // promote athletes
    window.promoteAthletes = async function(round) {
        const eventId = document.getElementById('heat_competition_event_id')?.selectedOptions[0]?.value ?? '';
        const payload = {
            competition_event_id: eventId,
            round_type: round
        }
        const result = await Swal.fire({
            title: "Yakin generate seri ronde ini sekarang ?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, lanjutkan!",
            cancelButtonText: "Batal",
            reverseButtons:true
        });

        if(result.isConfirmed){
            fetch(HEAT_CONFIG.promoteAtletUrl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' },
                body: JSON.stringify(payload),
            })
            .then(async r => {
                const data = await r.json();
                if (!r.ok) throw new Error(data.message || 'Terjadi kesalahan pada server');
                return data;
            })
            .then(data => {
                if (data.status){
                    reloadHeatTab(eventId);
                    Toast.fire({
                        icon:'success',
                        title:data.message || 'Sukses'
                    });
                }else{
                    Toast.fire({
                        icon:'error',
                        title:data.message || 'Gagal'
                    });
                }
            })
            .catch(error => {
                Toast.fire({
                    icon:'error',
                    title:error.message || 'Gagal promosi atet. Silakan coba lagi.'
                });
            });
        }

    };

    $(document).off('input', '.swim_time_input');
    $(document).on('input', '.swim_time_input', function(){
        let digits = this.value.replace(/\D/g, '');
        digits = digits.slice(0, 6);

        let formatted = '';
        if (digits.length <= 2) {
            formatted = digits;
        } else if (digits.length <= 4) {
            formatted = digits.slice(0, 2) + ':' + digits.slice(2);
        } else {
            formatted = digits.slice(0, 2) + ':' + digits.slice(2, 4) + '.' + digits.slice(4);
        }

        this.value = formatted;
    });

})();
</script>
