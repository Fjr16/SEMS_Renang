<script>
    // Inject ke window agar accessible dari parent dan setelah re-render
    window.HEAT_CONFIG = {
        poolLanes:   {{ $totalLanes }},
        totalAtlet:  {{ $totalEntries }},
        eventId:     {{ $event->id }},
        generateUrl: "{{ route('competition.heats.generate', $competition) }}",
        resetUrl: "{{ route('competition.heats.resetByEvent', $competition) }}",
        generateByRound: "{{ route('competition.heats.generateByRound', $competition) }}",
        reloadUrl:   "{{ route('competition.heats.partial', $competition) }}",
    };
</script>

<div id="heatMainContent">
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
                        <option value="{{ $e->id }}" {{ $e->id === $event->id ? 'selected' : '' }}>
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
                    <div style="font-size:14px; font-weight:500">{{ $totalEntries }} Entri</div>
                </div>
                <div>
                    <div style="font-size:10px; text-transform:uppercase; letter-spacing:.04em; color:#185FA5">Kapasitas Kolam</div>
                    <div style="font-size:14px; font-weight:500">{{ $totalLanes }} Lintasan</div>
                    <div style="font-size:10px; color:#185FA5">dari venue</div>
                </div>
                <div>
                    <div style="font-size:10px; text-transform:uppercase; letter-spacing:.04em; color:#185FA5">Estimasi Seri</div>
                    <div style="font-size:14px; font-weight:500" id="estimasiSeri">—</div>
                </div>
                <div class="ms-auto">
                    <button class="btn btn-sm btn-outline-secondary" style="font-size:11px"
                        onclick="resetHeatConfig()">Reset & atur ulang</button>
                </div>
            </div>

            @if ($heatsByRound->isEmpty())
                {{-- State: Belum ada heat → tampilkan form konfigurasi --}}
                {{-- @include('pages.competition.tabs.heats-config') --}}
                {{-- Step 1: Pilih struktur ronde --}}
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
                {{-- State: Sudah ada heat → tampilkan hasil --}}
                {{-- @include('pages.competition.tabs.heats-result') --}}
                {{-- Tab Ronde --}}
                <div class="d-flex border-bottom" style="background:#fff">
                    @foreach ($heatsByRound as $roundType => $heats)
                        <button class="btn btn-link btn-sm text-decoration-none fw-semibold px-3 py-2 tab-round-btn"
                            data-round="{{ $roundType }}"
                            style="font-size:13px; border-bottom:2px solid transparent; border-radius:0; color:#6c757d"
                            onclick="switchRoundTab('{{ $roundType }}', this)">
                            {{ App\Enums\RoundTypeEnum::tryFrom($roundType)->label() }}
                            <span class="badge rounded-pill ms-1"
                                style="font-size:10px; background:#E6F1FB; color:#0C447C">
                                {{ $heats->count() }} seri
                            </span>
                        </button>
                    @endforeach
                </div>

                {{-- Panel per Ronde --}}
                @foreach ($heatsByRound as $roundType => $roundHeats)
                    @php $isFirst = $loop->first; @endphp
                    <div class="round-panel" data-round="{{ $roundType }}"
                        style="display:{{ $isFirst ? 'block' : 'none' }}">

                        {{-- Toolbar --}}
                        <div class="d-flex align-items-center gap-2 px-3 py-2 flex-wrap"
                            style="background:#f8f9fa; border-bottom:1px solid #dee2e6; font-size:12px">
                            <span class="text-muted">
                                {{ $roundHeats->sum(fn($h) => $h->heatLanes->count()) }} entri / atlet ·
                                {{-- {{ $roundHeats->first()?->used_lanes ?? $totalLanes }} lane digunakan · --}}
                                {{ $totalLanes }} lane digunakan ·
                                {{ $roundHeats->count() }} seri
                            </span>
                            <div class="ms-auto d-flex gap-2">
                                @if (!$loop->first)
                                    <button class="btn btn-sm btn-outline-success" style="font-size:11px"
                                        onclick="promoteAthletes('{{ $roundType }}')">
                                        <i class="bi bi-arrow-repeat me-1"></i> Promosi Atlet
                                    </button>
                                @endif
                                <button class="btn btn-sm btn-primary" style="font-size:11px"
                                    onclick="regenerateRound('{{ $roundType }}')">
                                    <i class="bi bi-grid me-1"></i> Generate Ulang
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
                                    @foreach ($roundHeats as $heat)
                                        <button class="btn btn-link btn-sm text-decoration-none px-3 py-2 tab-heat-btn flex-shrink-0"
                                            data-round="{{ $roundType }}"
                                            data-heat="{{ $heat->heat_number }}"
                                            style="font-size:12px; border-bottom:2px solid transparent; border-radius:0; color:#6c757d; white-space:nowrap"
                                            onclick="switchHeatTab('{{ $roundType }}', {{ $heat->heat_number }}, this)">
                                            Seri {{ $heat->heat_number }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>
                            <button class="nav-btn btn btn-link btn-sm text-muted p-1"
                                onclick="slideHeatTab('{{ $roundType }}', 1)">
                                <i class="bi bi-chevron-right" style="font-size:11px"></i>
                            </button>
                        </div>

                        {{-- Panel per Heat --}}
                        @foreach ($roundHeats as $heat)
                            <div class="heat-panel table-responsive"
                                data-round="{{ $roundType }}"
                                data-heat="{{ $heat->heat_number }}"
                                style="display:{{ $loop->first ? 'block' : 'none' }}">
                                <table class="table table-hover mb-0" style="font-size:13px">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Peringkat</th>
                                            <th>Lintasan</th>
                                            <th>Atlet</th>
                                            <th>Tim / Klub</th>
                                            {{-- <th>Seed Time</th> --}}
                                            <th>Waktu Tercepat</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- @forelse ($heat->heatLanes->sortBy('lane_number') as $lane) --}}
                                        @forelse ($heat->heatLanes as $lane)
                                            <tr class="text-center">
                                                <td>{{ $lane->lane_order ?? '-' }}</td>
                                                <td><strong>{{ $lane->lane_number }}</strong></td>
                                                <td>{{ $lane->entry?->athlete?->name ?? '-' }}</td>
                                                <td class="text-muted">{{ $lane->entry?->athlete?->club?->club_name ?? '-' }}</td>
                                                <td style="font-family:monospace; font-size:12px">
                                                    {{ $lane->entry->seed_time ?? 'NT' }}
                                                </td>
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

                    </div>
                @endforeach
            @endif

        </div>
    </div>
</div>
