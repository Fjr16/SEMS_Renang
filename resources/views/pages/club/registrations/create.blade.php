@extends('layouts.main')

@section('content')
<style>
    :root{
        --surface:#fff;
        --muted:#6c757d;
        --ring: rgba(13,110,253,.14);
    }
    .page-hero{
        background:
        radial-gradient(1200px 400px at 10% 0%, rgba(13,110,253,.20), transparent),
        radial-gradient(800px 400px at 100% 0%, rgba(25,135,84,.14), transparent),
        linear-gradient(180deg, #ffffff, #f6f8fb);
        border: 1px solid rgba(0,0,0,.06);
        border-radius: 1rem;
        box-shadow: 0 12px 32px rgba(16,24,40,.06);
        overflow:hidden;
    }
    .chip{
        border: 1px solid rgba(0,0,0,.10);
        border-radius: 999px;
        padding: .35rem .7rem;
        font-size: .85rem;
        background:#fff;
        color:#111827;
        white-space: nowrap;
    }

    .btn-pill{ border-radius: 999px; font-weight: 800; }

    .list-scroll{ max-height: 55vh; overflow:auto; padding-right:.25rem; }
    .tiny-chip{
        border:1px solid rgba(0,0,0,.10);
        border-radius:999px;
        padding:.2rem .55rem;
        font-size:.78rem;
        background:#fff;
        color:#111827;
        white-space:nowrap;
    }

    .btn-soft{
        border-radius:999px;
        border:1px solid rgba(0,0,0,.12);
        background: rgba(0,0,0,.02);
        font-weight:800;
    }
    .btn-soft:hover{ background: rgba(0,0,0,.04); }

    .progress-soft{
        height: 10px;
        border-radius: 999px;
        background: rgba(0,0,0,.06);
        overflow:hidden;
    }
    .progress-soft > div{
        height:100%;
        width:0%;
        border-radius:999px;
        background: linear-gradient(90deg, rgba(13,110,253,.95), rgba(25,135,84,.85));
        transition: width .2s ease;
    }

    .entry-wrap{ --bdr: rgba(0,0,0,.08); --muted:#6c757d; --ring: rgba(13,110,253,.16); }
    .panel{
      background:#fff; border:1px solid var(--bdr); border-radius: 1rem;
      box-shadow: 0 10px 24px rgba(16,24,40,.05);
    }
    .panel-h{
      padding:.9rem 1rem; border-bottom:1px solid rgba(0,0,0,.06);
      display:flex; align-items:center; justify-content:space-between; gap:.75rem;
    }
    .panel-b{ padding:1rem; }

    .pill{
      border-radius:999px !important;
      border:1px solid rgba(0,0,0,.10) !important;
      background:#fff;
    }
    .pill:focus{
      border-color: rgba(13,110,253,.35) !important;
      box-shadow: 0 0 0 .25rem var(--ring) !important;
    }

    .ev-card{
      border:1px solid rgba(0,0,0,.07);
      border-radius: 1rem;
      padding:.85rem;
      background: linear-gradient(180deg, rgba(0,0,0,.012), rgba(0,0,0,.006));
      display:flex; align-items:flex-start; justify-content:space-between;
      gap: .85rem;
      transition: .15s ease;
    }
    .ev-card:hover{ transform: translateY(-1px); box-shadow: 0 10px 18px rgba(16,24,40,.06); }
    .ev-title{ font-weight:900; line-height:1.2; }
    .ev-meta{ color: var(--muted); font-size:.86rem; }

    .review{
      border:1px solid rgba(0,0,0,.08);
      border-radius: 1.15rem;
      padding: 1rem;
      background:
        radial-gradient(900px 380px at 30% 0%, rgba(13,110,253,.10), transparent),
        radial-gradient(900px 380px at 100% 0%, rgba(25,135,84,.08), transparent),
        linear-gradient(180deg, #fff, #fbfcff);
      box-shadow: 0 16px 34px rgba(16,24,40,.08);
    }
    .kv{
      border: 1px solid rgba(0,0,0,.08);
      background:#fff;
      border-radius:.9rem;
      padding:.65rem .75rem;
    }
    .kv small{ display:block; color:var(--muted); font-size:.78rem; }
    .kv .val{ font-weight:900; font-size: 1.05rem; }

    .muted{ color: var(--muted); }
    .badge-dot{
      display:inline-flex; align-items:center; gap:.35rem;
      padding:.22rem .55rem; border-radius:999px;
      border:1px solid rgba(0,0,0,.12); background:#fff; font-size:.78rem;
    }
    .dot{
      width:8px; height:8px; border-radius:50%;
      background: rgba(108,117,125,.9);
    }
    .dot.ok{ background: rgba(25,135,84,.95); }
    .dot.warn{ background: rgba(255,193,7,.95); }
</style>

<div class="page-hero p-3 p-md-4 mb-3">
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
    <div>
      <div class="d-flex gap-2 flex-wrap mb-2">
        <span class="chip"><i class="bi bi-person-badge me-1"></i>Club Manager</span>
        <span class="chip"><i class="bi bi-clipboard-check me-1"></i>{{ App\Enums\CompetitionStatus::from($comp->status)->label() }}</span>
      </div>
      <h1 class="h4 fw-bold mb-1">{{ '['. ($comp?->code ?? '-') .'] '.($comp?->name ?? 'Kompetisi') }}</h1>
      <div class="text-secondary small">
        <span>Registrasi: {{ ($comp->registration_start ? Carbon\Carbon::parse($comp->registration_start)->translatedFormat('d F Y') : '-/-/-') }}  -  {{ ($comp->registration_end ? Carbon\Carbon::parse($comp->registration_end)->translatedFormat('d F Y') : '-/-/-') }}</span> <br>
        <span>Kompetisi: {{ ($comp->start_date ? Carbon\Carbon::parse($comp->start_date)->translatedFormat('d F Y') : '-/-/-') }}  -  {{ ($comp->end_date ? Carbon\Carbon::parse($comp->end_date)->translatedFormat('d F Y') : '-/-/-') }}</span>
      </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('manager.club.registration') }}" class="btn btn-outline-secondary btn-pill">
        <i class="bi bi-arrow-left me-1"></i>Kembali
      </a>
    </div>
  </div>
</div>

<form id="regForm" method="POST" action="{{ route('manager.club.registration.store') }}">
  @csrf
  @php
    $athJS = ($athletes ?? collect())->map(function($a){
      return [
        'id' => $a->id,
        'name' => $a->name ?? '-',
        'code' => $a->code ?? '-',
        'meta' => (($a->gender ? App\Enums\Gender::from($a->gender)->label() : 'Tidak Dikenali') . ' • ' . ($a->bod ? Carbon\Carbon::parse($a->bod)->translatedFormat('d F Y') : '-/-/-')),
        'status' => ($a->status ? ($a->status == 'active' ? 'Aktif' : ($a->status == 'inactive' ? 'Tidak Aktif' : 'Status Tidak Dikenali')) : 'Status Tidak Diketahui'),
        'bod'    => $a->bod ? Carbon\Carbon::parse($a->bod)->format('Y-m-d') : null,
      ];
    })->values();

    $ofcJS = ($officials ?? collect())->map(function($o){
      return [
        'id' => $o->id,
        'name' => $o->name ?? '-',
        'meta' => (($o->license ?? '-') . ' • ' . ($o->gender ? App\Enums\Gender::from($o->gender)->label() : 'Tidak Dikenali')),
        'role' => $o->role ?? '-',
      ];
    })->values();

    $evJS = ($events ?? collect())->map(function($ev) use ($comp){
        $label = $ev->event_number ?? '';
        $eventGender = $ev->gender ? ($ev->gender != 'mixed' ? (App\Enums\Gender::from($ev->gender)->label()) : 'Campuran') : '-';
        $tipeEvent = $ev->event_type ? App\Enums\EventType::from($ev->event_type)->label() : '-';
        $tipeLabel   = $tipeEvent == 'Estafet' ? $tipeEvent . ' (maks. Atlet : '. ($ev->max_relay_athletes ?? '-') .')' : $tipeEvent;
        $eventKu = $ev->ageGroup ? $ev->ageGroup->label : '';
        $minDob = $ev->ageGroup ? Carbon\Carbon::parse($comp->start_date)->subYears($ev->ageGroup->max_age)->translatedFormat('d F Y') : '-';
        $maxDob = $ev->ageGroup ? Carbon\Carbon::parse($comp->start_date)->subYears($ev->ageGroup->min_age)->translatedFormat('d F Y') : '-';
        $stroke = $ev->stroke ? App\Enums\Stroke::from($ev->stroke)->label() : '###';
        $meta  = (($ev->distance ?? '-') . ' M ' . ($stroke) . ' • ' . ($eventGender) . ' • '. ($eventKu) . ' - ' . ($tipeEvent == 'Estafet' ? $tipeEvent . ' (maks. Atlet : '. ($ev->max_relay_athletes ?? '-') .')' : $tipeEvent));
        return [
            'id' => $ev->id,
            'label' => '[Event ' . trim($label) .'] ' . trim($meta),
            'ageGroup' => $eventKu,
            'min_max_dob' => $eventKu == 'UMUM' ? '-' : $minDob .' / '. $maxDob,
            'tipeEvent' => trim($tipeLabel),
            'isRelay' => $tipeEvent == 'Estafet',
            'max_relay'   => $ev->max_relay_athletes ?? null,
            'min_dob_raw' => $eventKu == 'UMUM' ? null
                                : ($ev->ageGroup
                                ? Carbon\Carbon::parse($comp->start_date)->subYears($ev->ageGroup->max_age)->format('Y-m-d')
                                : null),
            'max_dob_raw' => $eventKu == 'UMUM' ? null
                                : ($ev->ageGroup
                                ? Carbon\Carbon::parse($comp->start_date)->subYears($ev->ageGroup->min_age)->format('Y-m-d')
                                : null),
        ];
    })->values();
  @endphp

  <div class="entry-wrap">
    <div class="row g-3">
      {{-- LEFT --}}
      <div class="col-12 col-lg-7">
        <div class="panel mb-3">
          <div class="panel-h">
            <div>
              <div class="fw-bold">Pilih Event & Assign Atlet</div>
              <div class="small muted">Setiap event punya daftar atlet sendiri.</div>
            </div>
          </div>
          <div class="panel-b">
            <div class="input-group mb-3">
              <span class="input-group-text bg-white pill" style="border-right:0;">
                <i class="bi bi-search"></i>
              </span>
              <input id="eventSearch" type="text" class="form-control pill" placeholder="Cari event…" style="border-left:0;">
            </div>
            <div class="small muted mb-2">
              Alur: centang event → klik <b>Assign</b> → pilih atlet untuk event.
            </div>

            <div class="list-scroll" id="eventCards">
                @forelse($events as $ev)
                    @php
                    $label = 'Event ' . ($ev->event_number ?? '');
                    // $meta  = ('['.($ev->code ?? '-').']'. .($ev->distance ?? '-') . ' M ' . ($ev->stroke ?? '-') . ' • ' . (($ev->event_type ?? '-')));
                    $eventGender = $ev->gender ? ($ev->gender != 'mixed' ? (App\Enums\Gender::from($ev->gender)->label()) : 'Campuran') : '-';
                    $tipeEvent = $ev->event_type ? App\Enums\EventType::from($ev->event_type)->label() : '-';
                    $eventKu = $ev->ageGroup ? $ev->ageGroup->label : '';
                    $stroke = $ev->stroke ? App\Enums\Stroke::from($ev->stroke)->label() : '###';
                    $meta  = (($ev->distance ?? '-') . ' M ' . ($stroke ?? '-') . ' • ' . ($eventGender) . ' • '. ($eventKu) . ' - ' . ($tipeEvent == 'Estafet' ? $tipeEvent . ' (maks. Atlet : '. ($ev->max_relay_athletes ?? '-') .')' : $tipeEvent));
                    @endphp

                    <div class="ev-card mb-2" data-evcard data-evtext="{{ strtolower($label.' '.$meta) }}">
                        <div class="d-flex gap-3 align-items-start">
                            <div class="form-check form-switch mt-1">
                            <input class="form-check-input ev-toggle" type="checkbox" id="ev_on_{{ $ev->id }}" data-evid="{{ $ev->id }}">
                            <label class="form-check-label small fw-semibold" for="ev_on_{{ $ev->id }}">Ikut</label>
                            </div>

                            <div style="min-width:0;">
                            <div class="ev-title text-truncate">{{ $label }}</div>
                            <div class="ev-meta">{{ $meta }}</div>

                            <div class="d-flex flex-wrap gap-2 mt-2">
                                <span class="tiny-chip">
                                Atlet: <b id="cntAth_{{ $ev->id }}">0</b>
                                </span>
                                <span class="badge-dot" id="status_{{ $ev->id }}">
                                <span class="dot warn"></span><span class="small">Belum di-assign</span>
                                </span>
                            </div>

                            {{-- container hidden inputs untuk event ini --}}
                            <div class="d-none" id="hidden_{{ $ev->id }}"></div>
                            </div>
                        </div>

                        <div class="d-flex flex-column align-items-end gap-2 flex-shrink-0" style="min-width: fit-content;">
                            <button type="button"
                                    class="btn btn-sm btn-primary btn-pill btn-assign w-100"
                                    data-evid="{{ $ev->id }}"
                                    disabled>
                                <i class="bi bi-person-plus"></i>
                                <span class="ms-1 d-none d-sm-inline">Assign</span>
                                {{-- Assign --}}
                            </button>

                            <button type="button"
                                    class="btn btn-sm btn-soft btn-pill btn-clear w-100"
                                    data-evid="{{ $ev->id }}"
                                    disabled>
                                <i class="bi bi-x-circle"></i>
                                <span class="ms-1 d-none d-sm-inline">Clear</span>
                                {{-- Clear --}}
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="soft-card p-4 text-center">
                        <div class="mb-2"><i class="bi bi-clock-history fs-2 text-secondary"></i></div>
                        <div class="fw-semibold">Tidak Ada Event</div>
                        <div class="text-secondary">Menunggu penyelenggara atau admin menginputkan data event</div>
                    </div>
                @endforelse
            </div>

          </div>
        </div>
        {{-- CARD OFFICIAL KOMPETISI --}}
        <div class="panel" id="cardCompOfficials">
            <div class="panel-h">
                <div>
                    <div class="fw-bold"><i class="bi bi-person-badge me-1"></i>Official Kompetisi</div>
                    <div class="small muted">Official berlaku untuk seluruh event dalam kompetisi yang sama</div>
                </div>
                <span class="tiny-chip">Dipilih: <b id="cntCompOfc">0</b></span>
            </div>
            <div class="panel-b">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-white pill" style="border-right:0;">
                        <i class="bi bi-search"></i>
                    </span>
                    <input id="ofcSearch" type="text" class="form-control pill"
                        placeholder="Cari official…" style="border-left:0;">
                </div>

                <div class="d-flex gap-2 mb-3">
                    <button type="button" class="btn btn-sm btn-soft btn-pill" id="ofcSelectAll">
                        <i class="bi bi-check2-square me-1"></i>Pilih Semua
                    </button>
                    <button type="button" class="btn btn-sm btn-soft btn-pill" id="ofcClearAll">
                        <i class="bi bi-x-circle me-1"></i>Hapus Pilihan
                    </button>
                </div>

                <div class="list-scroll" id="compOfcList"></div>

                {{-- hidden inputs official --}}
                <div class="d-none" id="hidden_comp_officials"></div>
            </div>
        </div>
      </div>

      {{-- RIGHT: REVIEW --}}
      <div class="col-12 col-lg-5">
        <div class="review position-sticky" style="top: 88px;">
          <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
            <div>
              <div class="fw-bold" style="font-size:1.05rem;">Review</div>
              <div class="small muted">Validasi dasar sebelum submit.</div>
            </div>
            <span class="tiny-chip"><i class="bi bi-pencil-square me-1"></i>Draft</span>
          </div>

          <div class="progress-soft my-3" aria-hidden="true">
            <div id="progressFill"></div>
          </div>

          <div class="row g-2">
            <div class="col-6">
              <div class="kv">
                <small>Event dipilih</small>
                <div class="val" id="rvEvents">0</div>
              </div>
            </div>
            <div class="col-6">
              <div class="kv">
                <small>Total assign atlet</small>
                <div class="val" id="rvAthAssign">0</div>
              </div>
            </div>
            <div class="col-6">
              <div class="kv">
                <small>Total assign official</small>
                <div class="val" id="rvOfcAssign">0</div>
              </div>
            </div>
            <div class="col-6">
              <div class="kv">
                <small>Event belum lengkap</small>
                <div class="val" id="rvIncomplete">0</div>
              </div>
            </div>
          </div>

          <div class="alert alert-light border mt-3 mb-3" style="border-radius:1rem;">
            <div class="d-flex gap-2">
              <i class="bi bi-shield-check text-primary"></i>
              <div class="small muted mb-0">
                Minimal: event dipilih dan tiap event punya <b>≥1 atlet</b>.
                Official kompetisi bersifat opsional.
              </div>
            </div>
          </div>

          <div class="d-block">
              <button type="submit" class="btn btn-primary w-100 btn-pill mb-2" id="btnSubmitReg" disabled>
                <i class="bi bi-send-check me-1"></i>Submit Entry
              </button>
              <button type="button" class="btn btn-soft w-100 btn-pill" id="btnResetAll"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</button>
          </div>

          <div class="mt-3">
            <div class="fw-semibold mb-2">Ringkasan Event</div>
            <div class="small muted" id="rvList">Belum ada event dipilih.</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- MODAL ASSIGN (Atlet + Official per event) --}}
  <div class="modal fade" id="modalAssign" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-xl modal-dialog-centered">
      <div class="modal-content" style="border-radius:1.25rem; overflow:hidden;">
        <div class="modal-header">
          <div>
            <h5 class="modal-title mb-0">Assign Atlet</h5>
            <div class="small text-secondary" id="modalEvTitle">Event: -</div>
            <div class="ev-meta" id="modalMinMaxDob">Min. / Max. DOB : -</div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
            <div class="d-flex justify-content-between mb-2">
                <button type="button" class="btn btn-sm btn-soft btn-pill" id="mAthAll">
                    <i class="bi bi-check2-square me-1"></i>Pilih Semua
                </button>
                <button type="button" class="btn btn-sm btn-soft btn-pill" id="mAthClear">
                    <i class="bi bi-x-circle me-1"></i>Hapus Pilihan
                </button>
            </div>
            <div class="border rounded-4 p-2" style="max-height:60vh; overflow:auto;" id="mAthList"></div>
        </div>

        <div class="modal-footer">
            <div class="me-auto small text-secondary">
                Dipilih: <b id="mAthCount">0</b> atlet
            </div>
            <button type="button" class="btn btn-soft btn-pill" data-bs-dismiss="modal">Tutup</button>
            <button type="button" class="btn btn-primary btn-pill" id="mSave">
                <i class="bi bi-save2 me-1"></i>Simpan Assign
            </button>
        </div>
      </div>
    </div>
  </div>

</form>

@push('scripts')
<script>
(function(){
    // datasets
    const EXISTING = @json($existingJS);
    const EVENTS = @json($evJS);
    const ATHLETES = @json($athJS);
    const OFFICIALS = @json($ofcJS);

    const ROLE_OPTIONS = ['Manajer Tim','Pelatih Kepala','Asisten Pelatih','Fisioterapis','Dokter Tim','Official Lainnya'];

    const S = {};
    EVENTS.forEach(e => S[e.id] = {
        on:false,
        athletes:new Set(),
        entryTimes: new Map(),
        teamEntryTime: '',
        relayOrders: new Map(),
        relayErrors: new Map(),
    });

    // State official level kompetisi (bukan per event)
    const COMP_OFFICIALS = {
        officials: new Set(),
        officialRoles: new Map(),
    };

    const modalEl = document.getElementById('modalAssign');
    const modal = modalEl ? new bootstrap.Modal(modalEl) : null;
    let currentEventId = null;

    // review nodes
    const rvEvents = document.getElementById('rvEvents');
    const rvAthAssign = document.getElementById('rvAthAssign');
    const rvOfcAssign = document.getElementById('rvOfcAssign');
    const rvIncomplete = document.getElementById('rvIncomplete');
    const rvList = document.getElementById('rvList');
    const progressFill = document.getElementById('progressFill');
    const btnSubmit = document.getElementById('btnSubmitReg');

    // modal elements
    const modalEvTitle = byId('modalEvTitle');
    const modalMinMaxDob = byId('modalMinMaxDob');
    const mAthList = byId('mAthList');
    const mAthCount = byId('mAthCount');

    // helpers
    function escapeHtml(s){
        return String(s ?? '').replace(/[&<>"']/g, (m)=>({
        '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
        }[m]));
    }

    function byId(id){ return document.getElementById(id); }

    // validasi
    function validateAthlete(a, ev) {
        const reasons = [];
        if (a.status !== 'Aktif') {
            reasons.push('Status atlet ' + a.status.toLowerCase());
        }
        if (ev.min_dob_raw && ev.max_dob_raw && a.bod) {
            if(a.bod < ev.min_dob_raw || a.bod > ev.max_dob_raw){
                reasons.push('Tidak sesuai kelompok umur');
            }
        }
        return reasons;
    }

    function isRelayFull(evId) {
        const ev = EVENTS.find(x => x.id === evId);
        if (!ev || !ev.max_relay) return false;
        return S[evId].athletes.size >= ev.max_relay;
    }

    function validateRelayOrders(evId) {
        const ev = EVENTS.find(x => x.id === evId);
        if (!ev || !ev.max_relay) return { valid: true, errors: new Map() };

        const errors = new Map(); // athleteId -> pesan error
        const orderCount = new Map(); // urutan -> [athleteId]

        S[evId].athletes.forEach(id => {
            const ord = S[evId].relayOrders.get(id);
            if (!ord) {
                errors.set(id, 'Urutan belum dipilih');
            } else {
                if (!orderCount.has(ord)) orderCount.set(ord, []);
                orderCount.get(ord).push(id);
            }
        });

        // cek duplikat
        orderCount.forEach((ids, ord) => {
            if (ids.length > 1) {
                ids.forEach(id => errors.set(id, `Urutan ${ord} duplikat`));
            }
        });

        return { valid: errors.size === 0, errors };
    }

    // State -> UI
    function renderCompOfcCard() {
        const list = byId('compOfcList');
        if (!list) return;
        list.innerHTML = '';

        if (OFFICIALS.length === 0) {
            list.innerHTML = `
                <div class="p-4 text-center">
                    <div class="mb-2"><i class="bi bi-person-x fs-2 text-secondary"></i></div>
                    <div class="fw-semibold">Tidak Ada Official</div>
                    <div class="text-secondary small">Belum ada official terdaftar di klub ini.</div>
                </div>`;
            return;
        }

        OFFICIALS.forEach(o => {
            const id = o.id;
            const checked = COMP_OFFICIALS.officials.has(id);
            const savedRole = COMP_OFFICIALS.officialRoles.get(id) || o.role;
            const initials = o.name.trim().split(' ').slice(0, 2).map(w => w[0].toUpperCase()).join('');
            const roleOptionsHtml = ROLE_OPTIONS.map(r =>
                `<option value="${r}" ${r === savedRole ? 'selected' : ''}>${r}</option>`
            ).join('');

            const wrapper = document.createElement('div');
            wrapper.className = 'mb-2';
            wrapper.setAttribute('data-ofc-text', (o.name + ' ' + o.role).toLowerCase());

            wrapper.innerHTML = `
                <div class="ofc-row d-flex align-items-center gap-2 p-2 bg-white border rounded-4
                    ${checked ? 'border-primary' : ''}"
                    style="cursor:pointer; transition: border-color .15s;">
                    <input class="form-check-input comp-ofc-cb flex-shrink-0 mt-0"
                        type="checkbox" value="${id}" ${checked ? 'checked' : ''}
                        style="width:1.1rem; height:1.1rem;">
                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center
                                rounded-circle bg-primary bg-opacity-10 text-primary fw-semibold"
                        style="width:36px; height:36px; font-size:12px;">${initials}</div>
                    <div style="min-width:0; flex:1;">
                        <div class="fw-semibold text-truncate" style="font-size:.9rem;">${escapeHtml(o.name)}</div>
                        <div class="small text-secondary text-truncate">${escapeHtml(o.meta)}</div>
                    </div>
                    <span class="badge rounded-pill text-bg-light border flex-shrink-0" style="font-size:.75rem;">
                        ${escapeHtml(o.role)}
                    </span>
                </div>

                <div class="ofc-role-panel px-2 pt-2 pb-1" style="display:${checked ? 'block' : 'none'};">
                    <div class="rounded-3 px-3 py-2" style="background:#f8f9ff; border:1px solid #dde3f5;">
                        <label class="form-label small text-secondary mb-1" style="font-size:.75rem;">
                            <i class="bi bi-shield-check me-1"></i>Posisi di kompetisi ini
                        </label>
                        <select class="form-select form-select-sm ofc-role-select rounded-3" data-ofc-id="${id}">
                            ${roleOptionsHtml}
                        </select>
                    </div>
                </div>
            `;

            const row    = wrapper.querySelector('.ofc-row');
            const cb     = wrapper.querySelector('.comp-ofc-cb');
            const panel  = wrapper.querySelector('.ofc-role-panel');
            const select = wrapper.querySelector('.ofc-role-select');

            function applyChecked(isChecked) {
                if (isChecked) {
                    COMP_OFFICIALS.officials.add(id);
                    if (!COMP_OFFICIALS.officialRoles.has(id)) {
                        COMP_OFFICIALS.officialRoles.set(id, savedRole);
                    }
                    panel.style.display = 'block';
                    row.classList.add('border-primary');
                } else {
                    COMP_OFFICIALS.officials.delete(id);
                    COMP_OFFICIALS.officialRoles.delete(id);
                    panel.style.display = 'none';
                    row.classList.remove('border-primary');
                }
                byId('cntCompOfc').textContent = COMP_OFFICIALS.officials.size;
                renderCompOfficialInputs();
                refreshReview();
            }

            row.addEventListener('click', e => {
                if (e.target === cb || e.target === select) return;
                cb.checked = !cb.checked;
                applyChecked(cb.checked);
            });
            cb.addEventListener('change', () => applyChecked(cb.checked));
            select.addEventListener('change', () => {
                COMP_OFFICIALS.officialRoles.set(id, select.value);
                renderCompOfficialInputs();
                refreshReview();
            });
            select.addEventListener('click', e => e.stopPropagation());

            list.appendChild(wrapper);
        });
    }
    function setStatusChip(evId){
        const st = byId('status_'+evId);
        if(!st) return;

        const { on, athletes } = S[evId];

        // rules minimal: on => harus ada >=1 atlet
        const ok   = on && athletes.size > 0;
        const warn = on && athletes.size === 0;

        st.innerHTML = `
            <span class="dot ${ok ? 'ok' : warn ? 'warn' : ''}"></span>
            <span class="small">${ok ? 'Siap' : warn ? 'Butuh atlet' : 'Belum di-assign'}</span>
        `;
    }
    function refreshEventCard(evId){
        byId('cntAth_'+evId).textContent = S[evId].athletes.size;
        setStatusChip(evId);

        const btnAssign = document.querySelector('.btn-assign[data-evid="'+evId+'"]');
        const btnClear  = document.querySelector('.btn-clear[data-evid="'+evId+'"]');
        if(btnAssign) btnAssign.disabled = !S[evId].on;
        if(btnClear) btnClear.disabled = !S[evId].on;
    }
    function renderHiddenInputs(evId){
        const box = byId('hidden_'+evId);
        if(!box) return;
        box.innerHTML = '';
        if(!S[evId].on) return;

        // simpan hasil validasi urutan ke state
        const { errors } = validateRelayOrders(evId);
        S[evId].relayErrors = errors;

        const mk = (name, value) => {
            const i = document.createElement('input');
            i.type = 'hidden'; i.name = name; i.value = value;
            box.appendChild(i);
        };

        mk(`entries[${evId}][on]`, '1');
        S[evId].athletes.forEach(id => {
            mk(`entries[${evId}][athletes][]`, id);
            const et = S[evId].entryTimes.get(id);
            if(et) mk(`entries[${evId}][entry_times][${id}]`, et);
            const ord = S[evId].relayOrders.get(id);
            if(ord) mk(`entries[${evId}][relay_orders][${id}]`, ord);
        });

        // entry time tim untuk relay
        const ev = EVENTS.find(x => x.id === evId);
        if (ev?.isRelay && S[evId].teamEntryTime) {
            mk(`entries[${evId}][team_entry_time]`, S[evId].teamEntryTime);
        }
    }
    function renderCompOfficialInputs() {
        let box = byId('hidden_comp_officials');
        if (!box) return;
        box.innerHTML = '';
        const mk = (name, value) => {
            const i = document.createElement('input');
            i.type = 'hidden'; i.name = name; i.value = value;
            box.appendChild(i);
        };
        COMP_OFFICIALS.officials.forEach(id => {
            mk(`comp_officials[]`, id);
            const role = COMP_OFFICIALS.officialRoles.get(id);
            if (role) mk(`comp_official_roles[${id}]`, role);
        });
    }
    function refreshReview(){
        let evOn = 0;
        let totalAthAssign = 0;
        let incomplete = 0;
        const lines = [];

        EVENTS.forEach(e=>{
            const st = S[e.id];
            if(!st.on) return;
            evOn++;
            totalAthAssign += st.athletes.size;
            if (st.athletes.size === 0) {
                incomplete++;
            } else if (st.relayErrors && st.relayErrors.size > 0) {
                incomplete++;
            }
            lines.push(`• ${e.label} (Atlet: ${st.athletes.size})`);
        });

        rvEvents.textContent = evOn;
        rvAthAssign.textContent = totalAthAssign;
        rvOfcAssign.textContent  = COMP_OFFICIALS.officials.size;
        rvIncomplete.textContent = incomplete;

        let summary = lines.length ? lines.join('\n') : 'Belum ada event dipilih.';
        if (COMP_OFFICIALS.officials.size > 0) {
            const ofcNames = [...COMP_OFFICIALS.officials].map(id => {
                const o    = OFFICIALS.find(x => x.id === id);
                const role = COMP_OFFICIALS.officialRoles.get(id) || o?.role || '-';
                return `  - ${o ? o.name : id} (${role})`;
            }).join('\n');
            summary += `\n\nOfficial Kompetisi:\n${ofcNames}`;
        }

        rvList.textContent = summary;
        rvList.style.whiteSpace = 'pre-line';

        let p = 0;
        if(evOn > 0) p += 40;
        if(evOn > 0 && incomplete === 0) p += 60;
        progressFill.style.width = Math.min(100, p) + '%';
        btnSubmit.disabled = !(evOn > 0 && incomplete === 0);
    }
    function updateModalCounts(){
        if(!currentEventId) return;
        mAthCount.textContent = S[currentEventId].athletes.size;
    }

    // jika data entry sebelumnya telah ada maka load data menggunaka fungsi ini
    function initStateFromExisting() {
        if (!EXISTING) return;

        // Restore officials
        EXISTING.officials.forEach(o => {
            COMP_OFFICIALS.officials.add(o.id);
            COMP_OFFICIALS.officialRoles.set(o.id, o.role);
        });

        // Restore entries per event
        EXISTING.competitionEntries.forEach(entry => {
            const evId = entry.event_id;
            if (!S[evId]) return;

            // Aktifkan toggle event
            S[evId].on = true;
            const toggle = document.querySelector('.ev-toggle[data-evid="' + evId + '"]');
            if (toggle) toggle.checked = true;

            if (entry.is_relay) {
                // Restore relay
                S[evId].teamEntryTime = entry.team_entry_time || '';
                entry.athletes.forEach(a => {
                    S[evId].athletes.add(a.id);
                    S[evId].relayOrders.set(a.id, a.leg_order);
                });
            } else {
                // Restore perorangan
                entry.athletes.forEach(a => {
                    S[evId].athletes.add(a.id);
                    if (a.entry_time) S[evId].entryTimes.set(a.id, a.entry_time);
                });
            }

            renderHiddenInputs(evId);
            refreshEventCard(evId);
        });

        refreshReview();
        renderCompOfcCard();
    }

    // Render Modal List
    function renderModalLists(evId){
        const ev = EVENTS.find(x => x.id === evId);
        const isRelay = ev.isRelay;

        if (isRelay && !byId('teamEntryTimeInput')) {
            const savedTeamEntry = S[evId].teamEntryTime || '';
            mAthList.insertAdjacentHTML('beforebegin', `
                <div id="teamEntryTimeWrapper" class="rounded-3 px-3 py-2 mb-3"
                    style="background:#f8f9ff; border:1px solid #dde3f5;">
                    <label class="form-label small text-secondary mb-1" style="font-size:.75rem;">
                        <i class="bi bi-stopwatch me-1"></i>Entry Time Tim
                        <span class="text-muted">(mm:ss.cc)</span>
                    </label>
                    <input type="text" id="teamEntryTimeInput"
                        class="form-control form-control-sm rounded-3"
                        placeholder="00:00.00" maxlength="8"
                        value="${escapeHtml(savedTeamEntry)}">
                </div>
            `);

            byId('teamEntryTimeInput')?.addEventListener('input', e => {
                let raw = e.target.value.replace(/\D/g,'').slice(0,6);
                let formatted = raw;
                if(raw.length > 4) formatted = raw.slice(0,2)+':'+raw.slice(2,4)+'.'+raw.slice(4);
                else if(raw.length > 2) formatted = raw.slice(0,2)+':'+raw.slice(2);
                e.target.value = formatted;
                S[evId].teamEntryTime = formatted;
            });
        }

        mAthList.innerHTML = '';
        ATHLETES.forEach(a=>{
            const id = a.id;
            const checked = S[evId].athletes.has(id);
            const reasons  = validateAthlete(a, ev);
            const invalid  = reasons.length > 0;
            const relayFull = !checked && isRelayFull(evId);
            const disabled  = invalid || relayFull;
            const initials = a.name.trim().split(' ').slice(0, 2).map(w => w[0].toUpperCase()).join('');
            const badgeColor = a.status === 'Aktif' ? 'bg-primary'
                            : a.status === 'Tidak Aktif' ? 'bg-danger'
                            : 'bg-warning';

            const reasonMsg = invalid
                ? `<div class="mt-1 d-flex align-items-center gap-1" style="font-size:.75rem;color:#dc3545;"><i class="bi bi-exclamation-circle"></i> ${escapeHtml(reasons.join(', '))}</div>`
                : relayFull
                ? `<div class="mt-1 d-flex align-items-center gap-1" style="font-size:.75rem;color:#dc3545;"><i class="bi bi-exclamation-circle"></i> Kuota estafet penuh (maks. ${ev.max_relay})</div>`
                : '';

            const savedEntryTime  = S[evId].entryTimes.get(id)   || '';
            const savedRelayOrder = S[evId].relayOrders.get(id)  || '';

            const maxRelay = isRelay ? (ev.max_relay ?? 4) : null;
            const orderOptions = maxRelay > 0
                                ? Array.from({length: maxRelay}, (_,i) => i+1)
                                    .map(n => `<option value="${n}" ${savedRelayOrder == n ? 'selected':''}>${n}</option>`)
                                    .join('')
                                : '';

             const entryPanel = `
                <div class="ath-entry-panel px-2 pt-2 pb-1" style="display:${checked ? 'block' : 'none'};">
                    <div class="rounded-3 px-3 py-2" style="background:#f8f9ff; border:1px solid #dde3f5;">
                        <div class="row g-2 align-items-end">
                            ${!isRelay ? `
                            <div class="col-12">
                                <label class="form-label small text-secondary mb-1" style="font-size:.75rem;">
                                    <i class="bi bi-stopwatch me-1"></i>Entry Time <span class="text-muted">(mm:ss.cc)</span>
                                </label>
                                <input type="text"
                                    class="form-control form-control-sm rounded-3 ath-entry-time"
                                    placeholder="00:00.00"
                                    maxlength="8"
                                    value="${escapeHtml(savedEntryTime)}"
                                    data-ath-id="${id}">
                            </div>` : ''}
                            ${isRelay ? `
                            <div class="col-12">
                                <label class="form-label small text-secondary mb-1" style="font-size:.75rem;">
                                    <i class="bi bi-list-ol me-1"></i>Urutan
                                </label>
                                <select class="form-select form-select-sm rounded-3 ath-relay-order" data-ath-id="${id}">
                                    <option value="" selected>Pilih</option>
                                    ${orderOptions}
                                </select>
                                <div class="relay-order-error" style="font-size:.75rem; color:#dc3545; margin-top:.2rem;"></div>
                            </div>` : ''}
                        </div>
                    </div>
                </div>
            `;

            const wrapper = document.createElement('div');
            wrapper.className = 'mb-2';
            wrapper.setAttribute('data-text', (a.name + ' ' + a.code + ' ' + a.meta).toLowerCase());

            wrapper.innerHTML = `
                <div class="ath-row d-flex align-items-center gap-2 p-2 border rounded-4
                    ${checked ? 'border-primary' : ''}
                    ${disabled ? 'opacity-75' : ''}
                    ${disabled ? 'bg-light' : 'bg-white'}"
                    style="cursor:${disabled ? 'not-allowed' : 'pointer'}; transition: border-color .15s;">
                    <input class="form-check-input m-ath flex-shrink-0 mt-0"
                        type="checkbox" value="${id}"
                        ${checked ? 'checked' : ''}
                        ${disabled ? 'disabled' : ''}
                        style="width:1.1rem; height:1.1rem;">
                    <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-circle fw-semibold
                                ${disabled ? 'bg-secondary bg-opacity-10 text-secondary' : 'bg-primary bg-opacity-10 text-primary'}"
                        style="width:36px; height:36px; font-size:12px;">
                        ${initials}
                    </div>
                    <div style="min-width:0; flex:1;">
                        <div class="fw-semibold text-truncate" style="font-size:.9rem;">${escapeHtml(a.name)}</div>
                        <div class="small text-secondary text-truncate">[${escapeHtml(a.code)}] • ${escapeHtml(a.meta)}</div>
                        ${reasonMsg}
                    </div>
                    <span class="badge ${badgeColor} flex-shrink-0">${escapeHtml(a.status)}</span>
                </div>
                ${!disabled ? entryPanel : ''}
            `;

            if(!disabled){
                const row = wrapper.querySelector('.ath-row');
                const cb  = wrapper.querySelector('.m-ath');
                const panel = wrapper.querySelector('.ath-entry-panel');
                const etInput   = wrapper.querySelector('.ath-entry-time');
                const ordSelect = wrapper.querySelector('.ath-relay-order');

                 // Format otomatis entry time: ketik angka → auto format mm:ss.cc
                if(etInput){
                    etInput.addEventListener('input', () => {
                        let raw = etInput.value.replace(/\D/g,'').slice(0,6); // maks 6 digit angka
                        let formatted = raw;
                        if(raw.length > 4) formatted = raw.slice(0,2) + ':' + raw.slice(2,4) + '.' + raw.slice(4);
                        else if(raw.length > 2) formatted = raw.slice(0,2) + ':' + raw.slice(2);
                        etInput.value = formatted;
                        S[evId].entryTimes.set(id, formatted);
                    });
                    etInput.addEventListener('click', e => e.stopPropagation());
                }
                if(ordSelect){
                    ordSelect.addEventListener('change', () => {
                        S[evId].relayOrders.set(id, ordSelect.value);
                    });
                    ordSelect.addEventListener('click', e => e.stopPropagation());
                }

                function applyChecked(isChecked) {
                    if (isChecked) {
                        S[evId].athletes.add(id);
                        row.classList.add('border-primary');
                        if(panel) panel.style.display = 'block';
                    } else {
                        S[evId].athletes.delete(id);
                        S[evId].entryTimes.delete(id);
                        S[evId].relayOrders.delete(id);
                        row.classList.remove('border-primary');
                        if(panel) panel.style.display = 'none';
                    }
                    updateModalCounts();
                    if (ev.max_relay) renderModalLists(evId);
                }

                row.addEventListener('click', e => {
                    if (e.target === cb || e.target === etInput || e.target === ordSelect) return;
                    cb.checked = !cb.checked;
                    applyChecked(cb.checked);
                });

                cb.addEventListener('change', () => applyChecked(cb.checked));
            }

            mAthList.appendChild(wrapper);
        });

        updateModalCounts();
    }

    // Event Listener
    // search event cards
    byId('eventSearch')?.addEventListener('input', (e)=>{
        const q = (e.target.value || '').toLowerCase().trim();
        document.querySelectorAll('[data-evcard]').forEach(card=>{
            card.style.display = card.getAttribute('data-evtext').includes(q) ? '' : 'none';
        });
    });
    // search official
    byId('ofcSearch')?.addEventListener('input', e => {
        const q = (e.target.value || '').toLowerCase().trim();
        document.querySelectorAll('[data-ofc-text]').forEach(card => {
            card.style.display = card.getAttribute('data-ofc-text').includes(q) ? '' : 'none';
        });
    });
    // select all official
    byId('ofcSelectAll')?.addEventListener('click', () => {
        document.querySelectorAll('.comp-ofc-cb').forEach(cb => {
            if (cb.checked) return;
            cb.checked = true;
            cb.dispatchEvent(new Event('change'));
        });
    });
    // clear all official
    byId('ofcClearAll')?.addEventListener('click', () => {
        document.querySelectorAll('.comp-ofc-cb').forEach(cb => {
            if (!cb.checked) return;
            cb.checked = false;
            cb.dispatchEvent(new Event('change'));
        });
    });
    // toggle event ON/OFF
    document.addEventListener('change', (e)=>{
        if(!e.target.classList.contains('ev-toggle')) return;
        const evId = parseInt(e.target.dataset.evid, 10);
        S[evId].on = e.target.checked;
        if(!S[evId].on){
            S[evId].athletes.clear();
            S[evId].entryTimes.clear();
            S[evId].teamEntryTime = '';
            S[evId].relayOrders.clear();
            S[evId].relayErrors = new Map();
        }

        renderHiddenInputs(evId);
        refreshEventCard(evId);
        refreshReview();
    });
    // clear event assignment
    document.addEventListener('click', (e)=>{
        const btn = e.target.closest('.btn-clear');
        if(!btn) return;
        const evId = parseInt(btn.dataset.evid, 10);
        S[evId].athletes.clear();
        S[evId].entryTimes.clear();
        S[evId].teamEntryTime = '';
        S[evId].relayOrders.clear();
        S[evId].relayErrors = new Map();
        renderHiddenInputs(evId);
        refreshEventCard(evId);
        refreshReview();
    });
    // open modal
    document.addEventListener('click', (e)=>{
        const btn = e.target.closest('.btn-assign');
        if(!btn) return;
        byId('teamEntryTimeWrapper')?.remove();
        const evId = parseInt(btn.dataset.evid, 10);
        currentEventId = evId;
        const ev = EVENTS.find(x => x.id === evId);
        modalEvTitle.textContent = (ev ? ev.label: '-');
        modalMinMaxDob.textContent = (ev ? 'Min. / Max. DOB: ' + ev.min_max_dob : '-');
        renderModalLists(evId);
        modal?.show();
    });
    // modal select all / clear (only visible rows)
    function setModalAll(checked){
        mAthList.querySelectorAll('.m-ath').forEach(cb => {
            if (cb.disabled) return;
            if (checked && isRelayFull(currentEventId) && !cb.checked) return;
            if (cb.checked === checked) return;
            cb.checked = checked;
            cb.dispatchEvent(new Event('change'));
        });
    }

    byId('mAthAll')?.addEventListener('click', ()=>{ setModalAll(true); });
    byId('mAthClear')?.addEventListener('click', ()=>{ setModalAll(false); });

    // Simpan assign
    byId('mSave')?.addEventListener('click', ()=>{
        if(!currentEventId) return;
        const ev = EVENTS.find(x => x.id === currentEventId);

        // validasi urutan estafet sebelum simpan
        if(ev && ev.max_relay) {
            const { valid, errors } = validateRelayOrders(currentEventId);

            // tampilkan error di UI
            document.querySelectorAll('.ath-relay-order').forEach(sel => {
                const athId = parseInt(sel.dataset.athId, 10);
                const errMsg = sel.closest('.mb-2')?.querySelector('.relay-order-error');
                if(errors.has(athId)){
                    sel.classList.add('is-invalid');
                    if(errMsg) errMsg.textContent = errors.get(athId);
                } else {
                    sel.classList.remove('is-invalid');
                    if(errMsg) errMsg.textContent = '';
                }
            });

            if(!valid){
                // flash toast peringatan
                Toast.fire({
                    icon:'error',
                    title:'Periksa urutan estafet: ada yang kosong atau duplikat.'
                });
                return; // jangan tutup modal
            }
        }

        renderHiddenInputs(currentEventId);
        refreshEventCard(currentEventId);
        refreshReview();
        modal?.hide();
    });
    // reset all
    byId('btnResetAll')?.addEventListener('click', (e)=>{
        e.preventDefault();
        COMP_OFFICIALS.officials.clear();
        COMP_OFFICIALS.officialRoles.clear();
        renderCompOfficialInputs();
        renderCompOfcCard();                          // ← re-render card agar semua uncheck
        byId('cntCompOfc').textContent = 0;           // ← reset counter

        EVENTS.forEach(ev=>{
            S[ev.id].on = false;
            S[ev.id].athletes.clear();
            S[ev.id].entryTimes.clear();
            S[ev.id].teamEntryTime = '';
            S[ev.id].relayOrders.clear();
            S[ev.id].relayErrors = new Map();
            const t = document.querySelector('.ev-toggle[data-evid="'+ev.id+'"]');
            if(t) t.checked = false;
            const box = byId('hidden_'+ev.id);
            if(box) box.innerHTML = '';
            refreshEventCard(ev.id);
        });
        refreshReview();
    });
    // init
    EVENTS.forEach(ev=> refreshEventCard(ev.id));
    renderCompOfcCard();
    initStateFromExisting();
    refreshReview();

    byId('btnSubmitReg')?.addEventListener('click', async e => {
        e.preventDefault();

        // Build payload langsung dari S
        const entries = {};
        EVENTS.forEach(ev => {
            const st = S[ev.id];
            if(!st.on) return;
            entries[ev.id] = {
                athletes: [...st.athletes],
                team_entry_time: st.teamEntryTime,
                entry_times:  Object.fromEntries(st.entryTimes),
                relay_orders: Object.fromEntries(st.relayOrders),
            };
        });

        const payload = {
            competition_id: {{ $comp->id }},
            team_id: {{ auth()->user()->club->id ?? 'null' }},
            entries,
            comp_officials: [...COMP_OFFICIALS.officials].map(id => ({
                id,
                role: COMP_OFFICIALS.officialRoles.get(id) || null,
            })),
            _token: '{{ csrf_token() }}',
        };

        try {
            // loading state
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Menyimpan...';

            const res  = await fetch('{{ route("manager.club.registration.store") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify(payload),
            });

            const data = await res.json();

            if(res.ok){
                await Toast.fire({
                    icon:'success',
                    title:'Entry berhasil disimpan!',
                });
                window.location.reload();
            } else {
                const errors = data.errors;
                let errorMsg = data.message || 'Terjadi kesalahan, coba lagi.';
                if (errors) {
                    if (Array.isArray(errors)) {
                        errorMsg = errors.join('\n');
                    } else if (typeof errors === 'object') {
                        errorMsg = Object.values(errors).flat().join('\n');
                    }
                }

                Swal.fire({
                    icon: 'error',
                    title: data.message || 'Data tidak valid',
                    html: errors
                        ? '<ul class="text-start small mb-0">'
                            + (Array.isArray(errors)
                                ? errors
                                : Object.values(errors).flat()
                            ).map(e => `<li>${e}</li>`).join('')
                            + '</ul>'
                        : errorMsg,
                });
            }
        } catch(err) {
            console.log(err);
            Toast.fire({
                icon:'error',
                title:'Gagal terhubung ke server. periksa koneksi',
            });
        } finally {
            btnSubmit.disabled = false;
            btnSubmit.innerHTML = '<i class="bi bi-send-check me-1"></i>Submit Entry';
        }
    });
})();
</script>
@endpush

@endsection
