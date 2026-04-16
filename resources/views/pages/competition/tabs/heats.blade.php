<style>
    /* ── Team group card ── */
    .team-group { border: 1px solid #dee2e6; border-radius: 10px; margin-bottom: 10px; overflow: hidden; }
    .team-group-header {
        background: #f8f9fa;
        padding: 11px 16px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
        user-select: none;
    }
    .team-group-header:hover { background: #f1f3f5; }
    .team-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .team-group-body { border-top: 1px solid #dee2e6; }
    .team-group-body table th { font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: .04em; padding: 7px 14px; border-bottom: 1px solid #dee2e6; }
    .team-group-body table td { padding: 9px 14px; font-size: 13px; vertical-align: middle; }

        /* ── Official row ── */
    .official-row { display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: #f8f9fa; border-radius: 8px; font-size: 13px; margin-bottom: 6px; }
    .official-avatar { width: 28px; height: 28px; border-radius: 50%; background: #DBEAFE; color: #1D4ED8; font-size: 10px; font-weight: 600; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
</style>
<!-- Tab Heats -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h5 class="fw-bold mb-1">Manajemen Seri Perlombaan</h5>
    <p class="text-muted mb-0">Kelola seri perlombaan untuk tiap event</p>
  </div>
  <div class="mt-3 mt-md-0">
    <button data-bs-toggle="modal" data-bs-target="#modalHeat" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Tambah Heat
    </button>
  </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <div id="view-group">
            {{-- @forelse($event->heats as $heat)
                @php
                    $team     = $compTeam['team'];
                    $teamEntries = $compTeam['competitionEntries'];
                @endphp --}}

                <div class="team-group" id="team-group-{{ $event->id }}">
                    <div class="team-group-header" onclick="toggleGroup('{{ $event->id }}')">
                        <div class="d-flex align-items-center gap-2 w-100">
                            {{-- <span id="chevron-1" style="font-size:11px;color:#6c757d;transition:transform .2s;display:inline-block"><i class="bi bi-caret-right-square-fill"></i></span> --}}
                            <span class="fw-semibold flex-grow-1" style="font-size:14px">{{ $event->getLabel() }}</span>
                            <span class="text-muted me-1" style="font-size:12px"> entry</span>
                            {{-- <div class="d-flex gap-1">
                                @foreach($teamEntries->groupBy('status') as $status => $statusEntries)
                                    <span class="badge rounded-pill badge-{{ $status }}" style="font-size:10px">
                                        {{ $statusEntries->count() }} {{ App\Enums\CompetitionTeamEntryStatus::tryFrom($status)->label() ?? 'Tidak Dikenali' }}
                                    </span>
                                @endforeach
                            </div> --}}
                        </div>
                        {{-- <div class="d-flex align-items-center gap-3 w-100" onclick="event.stopPropagation()">
                            <span class="badge rounded-pill badge-{{ $compTeam?->status }}" style="font-size:10px">
                                {{ App\Enums\CompetitionTeamStatus::tryFrom($compTeam->status)->label() ?? 'Tidak Dikenali' }}
                            </span>
                             @php
                            $payIcon    = match($compTeam->payment_status) {
                                    'paid'    => ['💳', '#065F46', '#D1FAE5', 'Lunas'],
                                    default   => ['❌', '#991B1B', '#FEE2E2', 'Belum Bayar'],
                                };
                            @endphp
                            <span style="font-size:11px;background:{{ $payIcon[2] }};color:{{ $payIcon[1] }};padding:2px 8px;border-radius:20px;font-weight:500">
                                {{ $payIcon[0] }} {{ $payIcon[3] }}
                            </span>
                            <span class="text-muted" style="font-size:12px">
                                <span style="font-size:10px">Total Biaya</span>
                                <span class="fw-semibold" style="color:#111">Rp {{ number_format($compTeam->total_fee ?? 0, 0, ',', '.') }}</span>
                            </span>
                            <div class="flex-grow-1"></div>
                            <div class="d-flex gap-1" onclick="event.stopPropagation()">
                                @if($compTeam->status === App\Enums\CompetitionTeamStatus::Pending->value && $compTeam->competition->status === App\Enums\CompetitionStatus::register->value)
                                    <button class="btn btn-sm" style="background:#D1FAE5;color:#065F46;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                        onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Active->value }}')">✓ Terima</button>
                                    <button class="btn btn-sm" style="background:#FEE2E2;color:#991B1B;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                        onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Rejected->value }}')">✕ Tolak</button>
                                @elseif($compTeam->status === App\Enums\CompetitionTeamStatus::Pending->value && $compTeam->competition->status === App\Enums\CompetitionStatus::running->value)
                                    <button class="btn btn-sm" style="background:#FEE2E2;color:#991B1B;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                        onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Rejected->value }}')">✕ Tolak</button>
                                @elseif ($compTeam->status === App\Enums\CompetitionTeamStatus::Active->value && $compTeam->competition->status === App\Enums\CompetitionStatus::register->value)
                                    <button class="btn btn-sm" style="background:#FEE2E2;color:#991B1B;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                        onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Rejected->value }}')">✕ Tolak</button>
                                @elseif ($compTeam->status === App\Enums\CompetitionTeamStatus::Active->value && $compTeam->competition->status === App\Enums\CompetitionStatus::running->value)
                                <button class="btn btn-sm" style="background:#FEF3C7;color:#92400E;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                    onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Withdrawn->value }}')">
                                    ⚑ Undur diri
                                </button>
                                <button class="btn btn-sm" style="background:#FEE2E2;color:#991B1B;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                    onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Disqualified->value }}')">
                                    ⊘ Diskualifikasi
                                </button>
                                @elseif ($compTeam->status === App\Enums\CompetitionTeamStatus::Rejected->value && $compTeam->competition->status === App\Enums\CompetitionStatus::register->value)
                                <button class="btn btn-sm" style="background:#D1FAE5;color:#065F46;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                        onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Active->value }}')">✓ Terima</button>
                                @endif
                            </div>
                        </div> --}}
                    </div>
                    <div class="team-group-body" id="team-body-1">
                        <div class="d-flex border-bottom px-3" style="background:#fafafa">
                            <button class="btn btn-link btn-sm text-decoration-none fw-semibold px-3 py-2 tab-btn active"
                                style="font-size:12px;border-bottom:2px solid #2563EB;border-radius:0;color:#2563EB"
                                onclick="switchTab(1, 'entry', this)">
                                Entry Atlet
                                {{-- <span class="badge rounded-pill ms-1" style="background:#e9ecef;color:#495057;font-size:10px">
                                    {{ $teamEntries->count() }}
                                </span> --}}
                            </button>
                            <button class="btn btn-link btn-sm text-decoration-none fw-semibold px-3 py-2 tab-btn"
                                style="font-size:12px;border-bottom:2px solid transparent;border-radius:0;color:#6c757d"
                                onclick="switchTab(1, 'official', this)">
                                Official
                                {{-- <span class="badge rounded-pill ms-1" style="background:#e9ecef;color:#495057;font-size:10px">
                                    {{ $compTeam['competitionTeamOfficials']->count() }}
                                </span> --}}
                            </button>
                        </div>

                        <div id="tab-entry-1" class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Atlet</th>
                                        <th>Event</th>
                                        <th>Maks. Atlet</th>
                                        <th>Entry Time</th>
                                        <th>Seed Time</th>
                                        <th>Status</th>
                                        <th>Biaya Pendaftaran</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- @foreach($teamEntries as $i => $entry)
                                    @php
                                        $label = 'Event ' . ($entry->competitionEvent->event_number ?? '-');
                                        $eventGender = $entry->competitionEvent->gender ? ($entry->competitionEvent->gender != 'mixed' ? (App\Enums\Gender::from($entry->competitionEvent->gender)->label()) : 'Campuran') : '-';
                                        $tipeEvent = $entry->competitionEvent->event_type ? App\Enums\EventType::from($entry->competitionEvent->event_type)->label() : '-';
                                        $eventKu = $entry->competitionEvent->ageGroup ? $entry->competitionEvent->ageGroup->label : '';
                                        $meta  = (($entry->competitionEvent->distance ?? '-') . ' M ' . ($entry->competitionEvent->stroke ?? '-') . ' • ' . ($eventGender) . ' • '. ($eventKu));
                                    @endphp

                                    @if($entry->is_relay)
                                        <tr id="entry-row-g-{{ $entry->id }}" onclick="toggleRelayMembers({{ $entry->id }})">
                                            <td class="text-muted">{{ $i + 1 }}</td>
                                            <td>
                                                <span class="fw-medium">
                                                    {{ $entry->is_relay ? $team->club_name : ($entry?->athlete?->name ?? '-') }}
                                                </span>
                                                @if($entry->is_relay)
                                                    <span class="relay-tag">Estafet</span>
                                                @endif
                                            </td>
                                            <td>{{ $meta }}</td>
                                            <td><code>{{ $entry->competitionEvent->max_relay_athletes ?? '—' }}</code></td>
                                            <td><code>{{ $entry->entry_time ?? '—' }}</code></td>
                                            <td style="width: max-content;">
                                                <input
                                                    type="text"
                                                    class="seed_time_input form-control form-control-sm rounded-3 w-auto"
                                                    placeholder="00:00.00"
                                                    maxlength="8"
                                                    value="{{ $entry->seed_time ?? '' }}"
                                                    style="min-width: 75px; max-width: 90px;"
                                                    data-entry-id = "{{ $entry->id ?? '' }}"
                                                    @disabled($compTeam->competition->status !== App\Enums\CompetitionStatus::register->value)
                                                >
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill badge-{{ $entry->status }}" id="badge-g-{{ $entry->id }}">
                                                    {{ App\Enums\CompetitionTeamEntryStatus::tryFrom($entry->status)->label() ?? 'Tidak Dikenali' }}
                                                </span>
                                            </td>
                                            <td><code>Rp {{ number_format($entry->competitionEvent->registration_fee, 0, ',', '.') }}</code></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                    @if (
                                                        $compTeam->competition->status === App\Enums\CompetitionStatus::register->value
                                                    )
                                                    <button class="btn btn-danger btn-action" title="Hapus"
                                                        onclick="deleteEntry({{ $entry->id }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                        </svg>
                                                    </button>
                                                    @elseif(
                                                    $compTeam->competition->status === App\Enums\CompetitionStatus::running->value
                                                    && $compTeam->status === App\Enums\CompetitionTeamStatus::Pending->value
                                                    )
                                                    <button class="btn btn-danger btn-action" title="Hapus"
                                                        onclick="deleteEntry({{ $entry->id }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                        </svg>
                                                    </button>
                                                    @elseif(
                                                    $compTeam->competition->status === App\Enums\CompetitionStatus::running->value
                                                    && $compTeam->status === App\Enums\CompetitionTeamStatus::Active->value
                                                    )
                                                    @if ($entry->status === App\Enums\CompetitionTeamEntryStatus::Active->value )
                                                        <button class="btn btn-danger btn-action" title="Diskualifikasi"
                                                            onclick="updateStatusEntry('{{ $entry->id }}', '{{ App\Enums\CompetitionTeamEntryStatus::Disqualified->value }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                            </svg>
                                                        </button>
                                                        <button class="btn btn-warning btn-action" title="Undur Diri"
                                                            onclick="updateStatusEntry('{{ $entry->id }}', '{{ App\Enums\CompetitionTeamEntryStatus::Withdrawn->value }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                                                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                                            </svg>
                                                        </button>
                                                    @elseif ($entry->status === App\Enums\CompetitionTeamEntryStatus::Disqualified->value )
                                                        <button class="btn btn-success btn-action" title="Aktifkan"
                                                            onclick="updateStatusEntry('{{ $entry->id }}', '{{ App\Enums\CompetitionTeamEntryStatus::Active->value }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                                            </svg>
                                                        </button>
                                                        <button class="btn btn-warning btn-action" title="Undur Diri"
                                                            onclick="updateStatusEntry('{{ $entry->id }}', '{{ App\Enums\CompetitionTeamEntryStatus::Withdrawn->value }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                                                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                                            </svg>
                                                        </button>
                                                    @elseif ($entry->status === App\Enums\CompetitionTeamEntryStatus::Withdrawn->value )
                                                        <button class="btn btn-success btn-action" title="Aktifkan"
                                                            onclick="updateStatusEntry('{{ $entry->id }}', '{{ App\Enums\CompetitionTeamEntryStatus::Active->value }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                                            </svg>
                                                        </button>
                                                        <button class="btn btn-danger btn-action" title="Diskualifikasi"
                                                            onclick="updateStatusEntry('{{ $entry->id }}', '{{ App\Enums\CompetitionTeamEntryStatus::Disqualified->value }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                            </svg>
                                                        </button>
                                                    @endif
                                                    @elseif(
                                                    $compTeam->competition->status === App\Enums\CompetitionStatus::running->value
                                                    && $compTeam->status !== App\Enums\CompetitionTeamStatus::Active->value
                                                    && $compTeam->status !== App\Enums\CompetitionTeamStatus::Pending->value
                                                    )
                                                    <span class="badge bg-secondary">
                                                        {{ 'Tim Telah ' . App\Enums\CompetitionTeamStatus::from($compTeam->status)->label() ?? '-' }}
                                                    </span>
                                                    @elseif( $compTeam->competition->status === App\Enums\CompetitionStatus::closed->value)
                                                    <span class="badge bg-secondary">
                                                        {{ 'Kompetisi Telah ' . App\Enums\CompetitionStatus::from($compTeam->competition->status)->label() ?? '-' }}
                                                    </span>
                                                    @else
                                                    <span class="badge bg-secondary">
                                                        'Status Tidak Diketahui'
                                                    </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        <tr id="relay-members-{{ $entry->id }}" style="display:none;background:#f8fafc">
                                            <td colspan="9" class="px-4 py-2">
                                                <div class="d-flex align-items-center gap-2 mb-2">
                                                    <span style="font-size:11px;font-weight:600;color:#374151">Anggota Estafet</span>
                                                    <span class="badge rounded-pill" style="background:#e9ecef;color:#495057;font-size:10px">
                                                        {{ $entry->competitionEntryRelayMembers->where('status','active')->count() }} atlet
                                                    </span>
                                                </div>
                                                <table class="table table-sm mb-0" style="font-size:12px">
                                                    <thead>
                                                        <tr style="background:#f1f5f9">
                                                            <th style="width:60px">Urutan</th>
                                                            <th>Nama Atlet</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @forelse($entry->competitionEntryRelayMembers->sortBy('leg_order') as $member)
                                                        <tr>
                                                            <td>
                                                                <span style="background:#2563EB;color:#fff;border-radius:50%;width:22px;height:22px;display:inline-flex;align-items:center;justify-content:center;font-size:11px;font-weight:600">
                                                                    {{ $member->leg_order ?? '?' }}
                                                                </span>
                                                            </td>
                                                            <td class="fw-medium">{{ $member->athlete->name ?? '-' }}</td>
                                                            <td>
                                                                <span class="badge rounded-pill badge-{{ $member->status }}" style="font-size:10px">
                                                                    {{ $member->status === 'active' ? 'Aktif' : '?' }}
                                                                </span>
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td colspan="3" class="text-center text-muted py-2">Belum ada anggota relay</td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                    @else
                                        <tr id="entry-row-g-{{ $entry->id }}">
                                            <td class="text-muted">{{ $i + 1 }}</td>
                                            <td>
                                                <span class="fw-medium">
                                                    {{ $entry->is_relay ? $team->club_name : ($entry?->athlete?->name ?? '-') }}
                                                </span>
                                                @if($entry->is_relay)
                                                    <span class="relay-tag">Estafet</span>
                                                @endif
                                            </td>
                                            <td>{{ $meta }}</td>
                                            <td><code>{{ $entry->competitionEvent->max_relay_athletes ?? '—' }}</code></td>
                                            <td><code>{{ $entry->entry_time ?? '—' }}</code></td>
                                            <td>
                                                <input
                                                    type="text"
                                                    class="seed_time_input form-control form-control-sm rounded-3 w-auto"
                                                    placeholder="00:00.00"
                                                    maxlength="8"
                                                    value="{{ $entry->seed_time ?? '' }}"
                                                    style="min-width: 75px;max-width:90px;"
                                                    data-entry-id = "{{ $entry->id ?? '' }}"
                                                    @disabled($compTeam->competition->status !== App\Enums\CompetitionStatus::register->value)
                                                >
                                            </td>
                                            <td>
                                                <span class="badge rounded-pill badge-{{ $entry->status }}" id="badge-g-{{ $entry->id }}">
                                                    {{ App\Enums\CompetitionTeamEntryStatus::tryFrom($entry->status)->label() ?? 'Tidak Dikenali' }}
                                                </span>
                                            </td>
                                            <td><code>Rp {{ number_format($entry->competitionEvent->registration_fee, 0, ',', '.') }}</code></td>
                                            <td>
                                                <div class="d-flex gap-1">
                                                   @if (
                                                        $compTeam->competition->status === App\Enums\CompetitionStatus::register->value
                                                    )
                                                    <button class="btn btn-danger btn-action" title="Hapus"
                                                        onclick="deleteEntry({{ $entry->id }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                        </svg>
                                                    </button>
                                                    @elseif(
                                                    $compTeam->competition->status === App\Enums\CompetitionStatus::running->value
                                                    && $compTeam->status === App\Enums\CompetitionTeamStatus::Pending->value
                                                    )
                                                    <button class="btn btn-danger btn-action" title="Hapus"
                                                        onclick="deleteEntry({{ $entry->id }})">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                            <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                            <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                        </svg>
                                                    </button>
                                                    @elseif(
                                                    $compTeam->competition->status === App\Enums\CompetitionStatus::running->value
                                                    && $compTeam->status === App\Enums\CompetitionTeamStatus::Active->value
                                                    )
                                                    @if ($entry->status === App\Enums\CompetitionTeamEntryStatus::Active->value )
                                                        <button class="btn btn-danger btn-action" title="Diskualifikasi"
                                                            onclick="updateStatusEntry('{{ $entry->id }}', '{{ App\Enums\CompetitionTeamEntryStatus::Disqualified->value }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                            </svg>
                                                        </button>
                                                        <button class="btn btn-warning btn-action" title="Undur Diri"
                                                            onclick="updateStatusEntry('{{ $entry->id }}', '{{ App\Enums\CompetitionTeamEntryStatus::Withdrawn->value }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                                                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                                            </svg>
                                                        </button>
                                                    @elseif ($entry->status === App\Enums\CompetitionTeamEntryStatus::Disqualified->value )
                                                        <button class="btn btn-success btn-action" title="Aktifkan"
                                                            onclick="updateStatusEntry('{{ $entry->id }}', '{{ App\Enums\CompetitionTeamEntryStatus::Active->value }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                                            </svg>
                                                        </button>
                                                        <button class="btn btn-warning btn-action" title="Undur Diri"
                                                            onclick="updateStatusEntry('{{ $entry->id }}', '{{ App\Enums\CompetitionTeamEntryStatus::Withdrawn->value }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                                <path fill-rule="evenodd" d="M10 12.5a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-9a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v2a.5.5 0 0 0 1 0v-2A1.5 1.5 0 0 0 9.5 2h-8A1.5 1.5 0 0 0 0 3.5v9A1.5 1.5 0 0 0 1.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-2a.5.5 0 0 0-1 0z"/>
                                                                <path fill-rule="evenodd" d="M15.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 0 0-.708.708L14.293 7.5H5.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708z"/>
                                                            </svg>
                                                        </button>
                                                    @elseif ($entry->status === App\Enums\CompetitionTeamEntryStatus::Withdrawn->value )
                                                        <button class="btn btn-success btn-action" title="Aktifkan"
                                                            onclick="updateStatusEntry('{{ $entry->id }}', '{{ App\Enums\CompetitionTeamEntryStatus::Active->value }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05"/>
                                                            </svg>
                                                        </button>
                                                        <button class="btn btn-danger btn-action" title="Diskualifikasi"
                                                            onclick="updateStatusEntry('{{ $entry->id }}', '{{ App\Enums\CompetitionTeamEntryStatus::Disqualified->value }}')">
                                                            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                                                                <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708"/>
                                                            </svg>
                                                        </button>
                                                    @endif

                                                    @elseif(
                                                    $compTeam->competition->status === App\Enums\CompetitionStatus::running->value
                                                    && $compTeam->status !== App\Enums\CompetitionTeamStatus::Active->value
                                                    && $compTeam->status !== App\Enums\CompetitionTeamStatus::Pending->value
                                                    )
                                                    <span class="badge bg-secondary">
                                                        {{ 'Tim Telah ' . App\Enums\CompetitionTeamStatus::from($compTeam->status)->label() ?? '-' }}
                                                    </span>
                                                    @elseif( $compTeam->competition->status === App\Enums\CompetitionStatus::closed->value)
                                                    <span class="badge bg-secondary">
                                                        {{ 'Kompetisi Telah ' . App\Enums\CompetitionStatus::from($compTeam->competition->status)->label() ?? '-' }}
                                                    </span>
                                                    @else
                                                    <span class="badge bg-secondary">
                                                        'Status Tidak Diketahui'
                                                    </span>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endif
                                    @endforeach --}}
                                </tbody>
                            </table>
                        </div>

                        {{-- Tab: Official --}}
                        <div id="tab-official-1" style="display:none">
                            <div class="p-3">
                                {{-- @forelse($compTeam['competitionTeamOfficials'] as $ofc)
                                <div class="official-row" id="official-row-{{ $ofc->id }}">
                                    <div class="official-avatar">
                                        {{ strtoupper(substr($ofc->official->name, 0, 1)) }}{{ strtoupper(substr(strstr($ofc->official->name, ' '), 1, 1)) }}
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-medium" style="font-size:13px">{{ $ofc->official->name }}</div>
                                        <div class="text-muted" style="font-size:11px">
                                            {{ $ofc->role_override ?? '-' }}
                                            @if($ofc->official->license)
                                                · <code style="font-size:10px">{{ $ofc->official->license }}</code>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @empty --}}
                                    <div class="text-center text-muted py-4" style="font-size:13px">
                                        Tidak ada official terdaftar untuk tim ini.
                                    </div>
                                {{-- @endforelse --}}
                            </div>
                        </div>

                    </div>
                </div>
            {{-- @empty
                <div class="text-center text-muted py-5" style="font-size:13px">Belum ada entry untuk kompetisi ini.</div>
            @endforelse --}}
        </div>
    </div>
</div>

<!-- Card Table -->
{{-- <div class="card shadow-sm border-0">
  <div class="card-body">
    <div class="table-responsive">
      <table id="heatsTable" class="table table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Event</th>
            <th>Heat</th>
            <th>Jumlah Entries</th>
            <th>Input results</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <!-- Contoh data statis -->
          <tr>
            <td>1</td>
            <td>100m Freestyle</td>
            <td>Heat 1</td>
            <td>8</td>
            <td>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalResultsHeat1">
                    <i class="bi bi-flag-checkered"></i> Input Results
                </button>
            </td>
            <td>
              <div class="btn-group">
                <button data-bs-toggle="modal" data-bs-target="#modalHeatDetail" class="btn btn-sm btn-info">
                  <i class="bi bi-eye"></i>
                </button>
                <button data-bs-toggle="modal" data-bs-target="#modalHeat" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </div>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>100m Freestyle</td>
            <td>Heat 2</td>
            <td>6</td>
            <td>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalResultsHeat1">
                    <i class="bi bi-flag-checkered"></i> Input Results
                </button>
            </td>
            <td>
              <div class="btn-group">
                <button data-bs-toggle="modal" data-bs-target="#modalHeatDetail" class="btn btn-sm btn-info">
                  <i class="bi bi-eye"></i>
                </button>
                <button data-bs-toggle="modal" data-bs-target="#modalHeat" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div> --}}

<!-- Modal Create/Edit Heat -->
<div class="modal fade" id="modalHeat" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Heat</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Event</label>
            <select class="form-select" required>
              <option>100m Freestyle</option>
              <option>200m Butterfly</option>
              <option>400m Medley</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Heat Number</label>
            <input type="number" class="form-control" min="1" required>
          </div>
          <div class="mb-3">
            <label>Jumlah Lintasan (Lane)</label>
            <input type="number" class="form-control" min="1" max="10" value="8" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detail Heat (lihat lane assignments) -->
<div class="modal fade" id="modalHeatDetail" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Heat 1 - 100m Freestyle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Lane</th>
                <th>Nama Peserta</th>
                <th>Klub</th>
                <th>Seed Time</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Andi Wijaya</td>
                <td>Dolphin Club</td>
                <td>58.21</td>
              </tr>
              <tr>
                <td>2</td>
                <td>Budi Santoso</td>
                <td>Aqua Swim</td>
                <td>59.10</td>
              </tr>
              <tr>
                <td>3</td>
                <td>Citra Lestari</td>
                <td>Shark Team</td>
                <td>01:00.50</td>
              </tr>
              <!-- dst... -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        {{-- <button class="btn btn-primary">Edit Assignments</button> --}}
        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalLaneAssignments1">
            <i class="bi bi-diagram-3"></i> Lane Assignments
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Lane Assignments -->
<div class="modal fade" id="modalLaneAssignments1" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Lane Assignments - Heat 1</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Lane</th>
                  <th>Atlet</th>
                  <th>Seed Time</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @for ($i = 1; $i <= 8; $i++)
                  <tr>
                    <td>{{ $i }}</td>
                    <td>
                      <select class="form-select form-select-sm">
                        <option value="">-- Kosong --</option>
                        <option value="1">Budi Santoso</option>
                        <option value="2">Siti Aminah</option>
                      </select>
                    </td>
                    <td>
                      <input type="text" class="form-control form-control-sm" placeholder="00:55.32">
                    </td>
                    <td>
                      <select class="form-select form-select-sm">
                        <option>Terdaftar</option>
                        <option>DNS</option>
                        <option>DSQ</option>
                      </select>
                    </td>
                  </tr>
                @endfor
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Input Results -->
<div class="modal fade" id="modalResultsHeat1" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Input Results - Heat 1</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Lane</th>
                  <th>Nama Atlet</th>
                  <th>Waktu (detik)</th>
                  <th>Status</th>
                  <th>Ranking</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Budi Santoso</td>
                  <td><input type="text" class="form-control" placeholder="00:55.32"></td>
                  <td>
                    <select class="form-select">
                      <option value="finished">Finished</option>
                      <option value="dsq">Disqualified</option>
                      <option value="dns">Did Not Start</option>
                    </select>
                  </td>
                  <td><input type="number" class="form-control" placeholder="1"></td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Siti Aminah</td>
                  <td><input type="text" class="form-control" placeholder="00:56.10"></td>
                  <td>
                    <select class="form-select">
                      <option value="finished">Finished</option>
                      <option value="dsq">Disqualified</option>
                      <option value="dns">Did Not Start</option>
                    </select>
                  </td>
                  <td><input type="number" class="form-control" placeholder="2"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan Results</button>
        </div>
      </form>
    </div>
  </div>
</div>
