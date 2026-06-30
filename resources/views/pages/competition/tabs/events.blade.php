@push('styles')
<style>
    /* Session toggle chevron */
    .session-chevron.rotated { transform: rotate(180deg); }
    .session-toggle-btn:hover { background:#f1f5f9 !important; }

    /* Table rows */
    .event-row:hover { background:#f8fafc !important; }

    /* Select2 custom override for event modal */
    #modalEvent .select2-container--classic .select2-selection--single {
        height: 38px;
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        background: #fff;
    }
    #modalEvent .select2-container--classic .select2-selection--single .select2-selection__rendered {
        line-height: 36px;
        padding-left: 12px;
        color: #212529;
        font-size: 14px;
    }
    #modalEvent .select2-container--classic .select2-selection--single .select2-selection__arrow {
        height: 36px;
    }
    #modalEvent .select2-container--classic.select2-container--open .select2-selection--single {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13,110,253,.15);
    }
    #modalEvent .select2-container--classic .select2-results__option {
        padding: 8px 12px;
        font-size: 13px;
    }
    #modalEvent .select2-container--classic .select2-results__option--highlighted {
        background: #4f46e5;
    }
    #modalEvent .select2-container--classic .select2-results__group {
        padding: 8px 12px;
        font-weight: 700;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .05em;
        color: #4f46e5;
        background: #f0f0ff;
    }
    .select2-results__option .event-option {
        display: flex;
        align-items: center;
        gap: 8px;
        line-height: 1.3;
    }
    .select2-results__option .event-option .ev-distance {
        font-weight: 700;
        font-family: monospace;
        color: #4f46e5;
        min-width: 52px;
    }
    .select2-results__option .event-option .ev-stroke {
        font-weight: 600;
        min-width: 90px;
    }
    .select2-results__option .event-option .ev-gender {
        font-size: 11px;
        padding: 1px 6px;
        border-radius: 4px;
        font-weight: 600;
    }
    .select2-results__option .event-option .ev-ku {
        font-size: 11px;
        color: #6b7280;
    }
    .select2-results__option[aria-disabled="true"] {
        display: none;
    }
</style>
@endpush

<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Daftar Events</h5>
        <p class="text-muted mb-0">Kelola event yang ada dalam kompetisi ini</p>
    </div>
    <div class="mt-3 mt-md-0">
        <button data-bs-toggle="modal" data-bs-target="#modalEvent" onclick="openCreateEvent()"
            class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Event
        </button>
    </div>
</div>

<!-- ── Stat Cards ── -->
@php
    $totalEvents     = $competition->events->count();
    $totalSesi       = $competition->sessions->count();
    $totalEstafet    = $competition->events->where('event_type', \App\Enums\EventType::estafet->value)->count();
    $totalPerorangan = $totalEvents - $totalEstafet;
@endphp
<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'Total Events',  'value' => $totalEvents,     'color' => '#4f46e5', 'icon' => 'bi-trophy'],
        ['label' => 'Total Sesi',    'value' => $totalSesi,       'color' => '#7c3aed', 'icon' => 'bi-calendar3'],
        ['label' => 'Estafet',       'value' => $totalEstafet,    'color' => '#d97706', 'icon' => 'bi-people-fill'],
        ['label' => 'Perorangan',    'value' => $totalPerorangan, 'color' => '#059669', 'icon' => 'bi-person-fill'],
    ] as $stat)
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius:14px">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:40px;height:40px;background:{{ $stat['color'] }}18">
                    <i class="bi {{ $stat['icon'] }}" style="color:{{ $stat['color'] }};font-size:18px"></i>
                </div>
                <div>
                    <div class="fw-bold stat-value" style="font-size:1.3rem;color:{{ $stat['color'] }};font-family:monospace;line-height:1">
                        {{ $stat['value'] }}
                    </div>
                    <div class="text-muted" style="font-size:12px;font-weight:500">{{ $stat['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- ── Filter Bar ── -->
<div class="card border-0 shadow-sm mb-4" style="border-radius:14px">
    <div class="card-body py-3">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-lg-5">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="eventSearchInput" class="form-control border-start-0 ps-0"
                           placeholder="Cari nomor, gaya, sesi...">
                </div>
            </div>
            <div class="col-6 col-lg-2">
                <select id="filterGender" class="form-select form-select-sm">
                    <option value="">Semua Kelamin</option>
                    @foreach($enumGender as $g)
                        <option value="{{ $g->value }}">{{ $g->label() }}</option>
                    @endforeach
                    <option value="mixed">Campuran</option>
                </select>
            </div>
            <div class="col-6 col-lg-2">
                <select id="filterTipe" class="form-select form-select-sm">
                    <option value="">Semua Tipe</option>
                    @foreach($enumEType as $t)
                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-lg-2 ms-auto">
                <button onclick="resetEventFilter()" class="btn btn-sm btn-dark w-100 w-sm-auto">
                    <i class="bi bi-x-circle me-1"></i>Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Sessions Grouped ── -->
<div id="sessionContainer" class="d-flex flex-column gap-3">
    @forelse($competition->sessions as $sesi)
    @php $sesiEvents = $competition->events->where('competition_session_id', $sesi->id); @endphp

    <div class="card border-0 shadow-sm session-group-card" data-session-id="{{ $sesi->id }}" data-session="{{ strtolower($sesi->name) }}" style="border-radius:16px;overflow:hidden">
        <!-- Session Header -->
        <div class="session-toggle-btn d-flex align-items-center justify-content-between px-4 py-3"
             onclick="toggleSessionGroup(this)" role="button"
             style="background:#f8fafc;border-bottom:1px solid #f1f5f9;cursor:pointer;user-select:none">
            <div class="d-flex align-items-center gap-3">
                <div style="width:4px;height:24px;background:#4f46e5;border-radius:2px;flex-shrink:0"></div>
                <div>
                    <p class="fw-semibold text-dark mb-0" style="font-size:14px">{{ $sesi->name }}</p>
                    <p class="text-muted mb-0" style="font-size:12px">
                        {{ \Carbon\Carbon::parse($sesi->session_date)->translatedFormat('d F Y') }}
                        &middot;
                        <span class="session-visible-count">{{ $sesiEvents->count() }}</span> event
                    </p>
                </div>
            </div>
            <i class="bi bi-chevron-down text-muted session-chevron" style="transition:transform 0.2s"></i>
        </div>

        <!-- Session Body -->
        <div class="session-group-body">

            <!-- DESKTOP TABLE (≥768px) -->
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0" style="font-size:13px">
                    <thead style="background:#f8fafc">
                        <tr>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">#</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">No. Event</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Gaya</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Jarak</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Kelamin</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Kelompok</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Tipe</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold text-center" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Alat</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold text-end" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Biaya</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold text-center" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="event-rows-desktop">
                        @forelse($sesiEvents as $i => $event)
                            @include('pages.competition.tabs._event_row', [
                                'event'       => $event,
                                'sesi'        => $sesi,
                                'index'       => $i,
                                'competition' => $competition,
                            ])
                        @empty
                            <tr class="session-empty-row">
                                <td colspan="10" class="text-center text-muted py-4" style="font-size:13px">
                                    Belum ada event di sesi ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @empty
    <div class="text-center py-5">
        <i class="bi bi-calendar-x text-muted" style="font-size:3rem"></i>
        <p class="text-muted mt-3 mb-1 fw-semibold">Belum ada sesi</p>
        <p class="text-muted" style="font-size:13px">Tambahkan sesi terlebih dahulu sebelum membuat event</p>
    </div>
    @endforelse
</div>

<!-- Empty Filter State -->
<div id="eventEmptyFilter" class="text-center py-5 d-none">
    <i class="bi bi-search text-muted" style="font-size:2.5rem"></i>
    <p class="fw-semibold text-muted mt-3 mb-1">Tidak ada event yang cocok</p>
    <p class="text-muted" style="font-size:13px">Coba ubah kata kunci atau filter</p>
</div>

<div class="modal fade" id="modalEvent" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form id="eventForm">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEventTitle">Tambah Event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row g-3">
                <input type="hidden" id="competition_event_id" name="competition_event_id">
                <div class="col-md-6 col-12">
                    <label class="form-label">Kompetisi</label>
                    <input type="text" class="form-control" id="competition_name"
                           value="{{ $competition->name ?? '' }}" disabled>
                    <input type="hidden" value="{{ $competition->id ?? '' }}" name="competition_id" id="competition_id">
                </div>
                <div class="col-md-6 col-12">
                    <label class="form-label">Sesi Perlombaan</label>
                    <select name="competition_session_id" id="competition_session_id" class="form-select">
                        @foreach ($competition->sessions as $sesi)
                            <option value="{{ $sesi->id }}" @selected(old('competition_session_id') == $sesi->id)>
                                {{ ($sesi->session_date ?? '-/-') . ' [' . ($sesi->name ?? '-') . ']' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12">
                    <label class="form-label">Pilih Event</label>
                    <select name="master_event_id" id="master_event_id" class="form-select" required>
                        <option value="">-- Pilih Event --</option>
                        @php
                            $grouped = $masterEvents->groupBy('event_type');
                        @endphp
                        @foreach($grouped as $type => $events)
                            <optgroup label="{{ \App\Enums\EventType::from($type)->label() }}">
                                @foreach($events as $me)
                                    @php
                                        $gLabel = $me->gender === 'mixed' ? 'Campuran' : (\App\Enums\Gender::tryFrom($me->gender)?->label() ?? $me->gender);
                                        $sLabel = \App\Enums\Stroke::tryFrom($me->stroke)?->label() ?? $me->stroke;
                                        $bgGender = $me->gender === 'mixed' ? '#6c757d' : ($me->gender === 'male' ? '#0d6efd' : '#d63384');
                                        $kuLabel = $me->ageGroup?->label ?? '-';
                                        $isRelay = $me->event_type === \App\Enums\EventType::estafet->value;
                                        $equipLabel = $me->equipment ? ucfirst($me->equipment) : '';
                                        if ($isRelay && $me->max_relay_athletes) {
                                            $distDisplay = $me->max_relay_athletes . 'x' . $me->distance . 'm';
                                        } else {
                                            $distDisplay = $me->distance . 'm';
                                        }
                                        $searchText = $distDisplay . ' ' . $sLabel . ' ' . ($equipLabel ? $equipLabel . ' ' : '') . ($isRelay ? 'Estafet ' : '') . $gLabel . ' ' . $kuLabel;
                                    @endphp
                                    <option value="{{ $me->id }}"
                                        data-event-type="{{ $me->event_type }}"
                                        data-stroke="{{ $sLabel }}"
                                        data-distance="{{ $me->distance }}"
                                        data-dist-display="{{ $distDisplay }}"
                                        data-gender="{{ $gLabel }}"
                                        data-gender-color="{{ $bgGender }}"
                                        data-ku="{{ $kuLabel }}"
                                        data-equipment="{{ $equipLabel }}">{{ $searchText }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6 col-12">
                    <label class="form-label">Biaya Pendaftaran</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control rupiah"
                               id="registration_fee" name="registration_fee" required>
                    </div>
                </div>
                <div class="col-md-6 col-12">
                    <label class="form-label">Limit Waktu</label>
                    <input type="text" name="limit_waktu" class="form-control" id="limit_waktu"
                           placeholder="Kosongkan untuk NO LIMIT">
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" id="eventSubmitBtn">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="modalDeleteEvent" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="d-flex align-items-center justify-content-center mb-3 mx-auto rounded-circle"
                     style="width:52px;height:52px;background:#fef2f2">
                    <i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;font-size:22px"></i>
                </div>
                <h5 class="fw-bold mb-1">Hapus Event?</h5>
                <p class="text-muted mb-4" style="font-size:13px">
                    Event nomor <strong id="deleteEventNo" class="text-dark"></strong>
                    akan dihapus permanen.
                </p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light fw-semibold flex-fill rounded-3"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger fw-semibold flex-fill rounded-3"
                            id="deleteEventConfirmBtn" onclick="executeDeleteEvent()">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>
