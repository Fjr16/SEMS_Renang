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
        <span class="chip"><i class="bi bi-clipboard-check me-1"></i>Registrasi</span>
      </div>
      <h1 class="h4 fw-bold mb-1">Registrasi: {{ $comp->name ?? 'Kompetisi' }}</h1>
      <div class="text-secondary small">
        {{-- <span class="badge text-bg-light border">{{ $comp->code ?? 'COMP' }}</span> --}}
        <span>Registrasi: {{ $comp->registration_start ?? '-' }}  /  {{ $comp->registration_end ?? '-' }}</span> <br>
        <span>Kompetisi: {{ $comp->start_date ?? '-' }}  /  {{ $comp->end_date ?? '-' }}</span>
      </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('manager.club.registration') }}" class="btn btn-outline-secondary btn-pill">
        <i class="bi bi-arrow-left me-1"></i>Kembali
      </a>
    </div>
  </div>
</div>

<form id="regForm" method="POST" action="#">
  @csrf

  @php
    // Data minimal utk render cepat di JS (aman, hanya display)
    $athJS = ($athletes ?? collect())->map(function($a){
      return [
        'id' => $a->id,
        'name' => $a->name ?? '-',
        'code' => $a->code ?? '-',
        'meta' => (($a->gender ?? '-') . ' • ' . ($a->bod ?? '-')),
      ];
    })->values();

    $ofcJS = ($officials ?? collect())->map(function($o){
      return [
        'id' => $o->id,
        'name' => $o->name ?? '-',
        'role' => $o->position ?? 'Official',
        'license' => $o->license ?? '-',
      ];
    })->values();

    $evJS = ($events ?? collect())->map(function($ev){
    //   $label = ($ev->event_number ?? '') . ' - ' . ($ev->name ?? 'Event');
      $label = $ev->event_number ?? '';
      $meta  = (($ev->gender ?? '-') . ' • ' . ($ev->ageGroup->label ?? '-') . ' • ' . (($ev->distance ?? '') . ($ev->stroke ?? '')));
      return [
        'id' => $ev->id,
        'label' => trim($label),
        'meta' => trim($meta),
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
              <div class="fw-bold">Pilih Event & Assign Peserta</div>
              <div class="small muted">Setiap event punya daftar atlet & official sendiri.</div>
            </div>
            {{-- <a href="#" class="btn btn-sm btn-soft">
              <i class="bi bi-info-circle me-1"></i>Petunjuk
            </a> --}}
          </div>
          <div class="panel-b">
            <div class="input-group mb-3">
              <span class="input-group-text bg-white pill" style="border-right:0;">
                <i class="bi bi-search"></i>
              </span>
              <input id="eventSearch" type="text" class="form-control pill" placeholder="Cari event…"
                     style="border-left:0;">
            </div>

            <div class="list-scroll" id="eventCards">
              {{-- Render event cards --}}
              @foreach($events as $ev)
                @php
                  $label = 'Event ' . ($ev->event_number ?? '');
                  $meta  = (($ev->distance ?? '-') . ' M ' . ($ev->stroke ?? '-') . ' • ' . (($ev->event_type ?? '-')));
                @endphp

                <div class="ev-card mb-2" data-evcard data-evtext="{{ strtolower($label.' '.$meta) }}">
                  <div class="d-flex gap-3 align-items-start">
                    <div class="form-check form-switch mt-1">
                      <input class="form-check-input ev-toggle" type="checkbox" id="ev_on_{{ $ev->id }}" data-evid="{{ $ev->id }}">
                      <label class="form-check-label small fw-semibold" for="ev_on_{{ $ev->id }}">Ikut</label>
                    </div>

                    <div style="min-width:0;">
                      <div class="ev-title text-truncate">{{ $label }}</div>
                      <div class="ev-meta text-truncate">{{ $meta }}</div>

                      <div class="d-flex flex-wrap gap-2 mt-2">
                        <span class="tiny-chip">
                          Atlet: <b id="cntAth_{{ $ev->id }}">0</b>
                        </span>
                        <span class="tiny-chip">
                          Official: <b id="cntOfc_{{ $ev->id }}">0</b>
                        </span>
                        <span class="badge-dot" id="status_{{ $ev->id }}">
                          <span class="dot warn"></span><span class="small">Belum di-assign</span>
                        </span>
                      </div>

                      {{-- container hidden inputs untuk event ini --}}
                      <div class="d-none" id="hidden_{{ $ev->id }}"></div>
                    </div>
                  </div>

                  <div class="d-flex flex-column align-items-end gap-2">
                    {{-- <span class="tiny-chip">#{{ $ev->id }}</span> --}}

                    <button type="button"
                            class="btn btn-sm btn-primary btn-pill btn-assign"
                            data-evid="{{ $ev->id }}"
                            disabled>
                      <i class="bi bi-person-plus me-1"></i>Assign
                    </button>

                    <button type="button"
                            class="btn btn-sm btn-soft btn-pill btn-clear"
                            data-evid="{{ $ev->id }}"
                            disabled>
                      <i class="bi bi-x-circle me-1"></i>Clear
                    </button>
                  </div>
                </div>
              @endforeach
            </div>

            <div class="small muted mt-2">
              Alur: centang event → klik <b>Assign</b> → pilih atlet & official untuk event itu.
            </div>
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
                (Official per event opsional sesuai aturan panitia.)
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100 btn-pill" id="btnSubmitReg" disabled>
            <i class="bi bi-send-check me-1"></i>Submit Entry
          </button>

          <div class="d-flex gap-2 mt-2">
            <a href="#" class="btn btn-soft w-100 btn-pill"><i class="bi bi-eye me-1"></i>Preview</a>
            <a href="#" class="btn btn-soft w-100 btn-pill" id="btnResetAll"><i class="bi bi-arrow-counterclockwise me-1"></i>Reset</a>
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
            <h5 class="modal-title mb-0">Assign Peserta</h5>
            <div class="small text-secondary" id="modalEvTitle">Event: -</div>
          </div>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <ul class="nav nav-pills mb-3" id="assignTabs" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active btn-pill" id="tabAth" data-bs-toggle="pill" data-bs-target="#paneAth" type="button" role="tab">
                <i class="bi bi-person me-1"></i>Atlet
                <span class="ms-1 tiny-chip">Dipilih: <b id="mAthCount">0</b></span>
              </button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link btn-pill" id="tabOfc" data-bs-toggle="pill" data-bs-target="#paneOfc" type="button" role="tab">
                <i class="bi bi-person-badge me-1"></i>Official
                <span class="ms-1 tiny-chip">Dipilih: <b id="mOfcCount">0</b></span>
              </button>
            </li>
          </ul>

          <div class="tab-content">
            {{-- ATH --}}
            <div class="tab-pane fade show active" id="paneAth" role="tabpanel">
              <div class="d-flex gap-2 flex-wrap align-items-center justify-content-between mb-2">
                <div class="input-group" style="max-width:420px;">
                  <span class="input-group-text bg-white pill" style="border-right:0;"><i class="bi bi-search"></i></span>
                  <input type="text" id="mSearchAth" class="form-control pill" placeholder="Cari atlet…" style="border-left:0;">
                </div>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-sm btn-soft btn-pill" id="mAthAll"><i class="bi bi-check2-square me-1"></i>Select All</button>
                  <button type="button" class="btn btn-sm btn-soft btn-pill" id="mAthClear"><i class="bi bi-x-circle me-1"></i>Clear</button>
                </div>
              </div>
              <div class="border rounded-4 p-2" style="max-height:52vh; overflow:auto;" id="mAthList"></div>
            </div>

            {{-- OFC --}}
            <div class="tab-pane fade" id="paneOfc" role="tabpanel">
              <div class="d-flex gap-2 flex-wrap align-items-center justify-content-between mb-2">
                <div class="input-group" style="max-width:420px;">
                  <span class="input-group-text bg-white pill" style="border-right:0;"><i class="bi bi-search"></i></span>
                  <input type="text" id="mSearchOfc" class="form-control pill" placeholder="Cari official…" style="border-left:0;">
                </div>
                <div class="d-flex gap-2">
                  <button type="button" class="btn btn-sm btn-soft btn-pill" id="mOfcAll"><i class="bi bi-check2-square me-1"></i>Select All</button>
                  <button type="button" class="btn btn-sm btn-soft btn-pill" id="mOfcClear"><i class="bi bi-x-circle me-1"></i>Clear</button>
                </div>
              </div>
              <div class="border rounded-4 p-2" style="max-height:52vh; overflow:auto;" id="mOfcList"></div>
            </div>
          </div>
        </div>

        <div class="modal-footer">
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
  const EVENTS = @json($evJS);
  const ATHLETES = @json($athJS);
  const OFFICIALS = @json($ofcJS);

  // state: per event assignment
  // { [eventId]: { on: bool, athletes: Set(ids), officials: Set(ids) } }
  const S = {};
  EVENTS.forEach(e => S[e.id] = { on:false, athletes:new Set(), officials:new Set() });

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

  function byId(id){ return document.getElementById(id); }

  function setStatusChip(evId){
    const st = byId('status_'+evId);
    if(!st) return;

    const on = S[evId].on;
    const a = S[evId].athletes.size;
    const o = S[evId].officials.size;

    // rules minimal: on => harus ada >=1 atlet
    const ok = on && a > 0;
    const warn = on && a === 0;

    st.innerHTML = '';
    const dot = document.createElement('span');
    dot.className = 'dot ' + (ok ? 'ok' : (warn ? 'warn' : ''));
    const tx = document.createElement('span');
    tx.className = 'small';
    tx.textContent = ok ? 'Siap' : (warn ? 'Butuh atlet' : 'Belum di-assign');

    st.appendChild(dot);
    st.appendChild(tx);
  }

  function refreshEventCard(evId){
    byId('cntAth_'+evId).textContent = S[evId].athletes.size;
    byId('cntOfc_'+evId).textContent = S[evId].officials.size;
    setStatusChip(evId);

    const btnAssign = document.querySelector('.btn-assign[data-evid="'+evId+'"]');
    const btnClear  = document.querySelector('.btn-clear[data-evid="'+evId+'"]');
    if(btnAssign) btnAssign.disabled = !S[evId].on;
    if(btnClear) btnClear.disabled = !S[evId].on;
  }

  function renderHiddenInputs(evId){
    const box = byId('hidden_'+evId);
    if(!box) return;

    // wipe
    box.innerHTML = '';

    if(!S[evId].on) return;

    // mark event on (optional, kalau backend butuh daftar event)
    const on = document.createElement('input');
    on.type = 'hidden';
    on.name = `entries[${evId}][on]`;
    on.value = '1';
    box.appendChild(on);

    // athletes
    S[evId].athletes.forEach(id=>{
      const i = document.createElement('input');
      i.type = 'hidden';
      i.name = `entries[${evId}][athletes][]`;
      i.value = String(id);
      box.appendChild(i);
    });

    // officials
    S[evId].officials.forEach(id=>{
      const i = document.createElement('input');
      i.type = 'hidden';
      i.name = `entries[${evId}][officials][]`;
      i.value = String(id);
      box.appendChild(i);
    });
  }

  function refreshReview(){
    let evOn = 0;
    let totalAthAssign = 0;
    let totalOfcAssign = 0;
    let incomplete = 0;

    const lines = [];

    EVENTS.forEach(e=>{
      const st = S[e.id];
      if(!st.on) return;

      evOn++;
      totalAthAssign += st.athletes.size;
      totalOfcAssign += st.officials.size;

      if(st.athletes.size === 0) incomplete++;

      lines.push(`• ${e.label} (Atlet: ${st.athletes.size}, Official: ${st.officials.size})`);
    });

    rvEvents.textContent = evOn;
    rvAthAssign.textContent = totalAthAssign;
    rvOfcAssign.textContent = totalOfcAssign;
    rvIncomplete.textContent = incomplete;

    rvList.textContent = lines.length ? lines.join('\n') : 'Belum ada event dipilih.';
    rvList.style.whiteSpace = 'pre-line';

    // progress: event on (40), any athlete assignment (60)
    let p = 0;
    if(evOn > 0) p += 40;
    if(evOn > 0 && incomplete === 0) p += 60;
    progressFill.style.width = Math.min(100, p) + '%';

    // submit rule: minimal ada event dipilih dan semua event yang ON punya >=1 atlet
    btnSubmit.disabled = !(evOn > 0 && incomplete === 0);
  }

  // search event cards
  const eventSearch = byId('eventSearch');
  if(eventSearch){
    eventSearch.addEventListener('input', (e)=>{
      const q = (e.target.value || '').toLowerCase().trim();
      document.querySelectorAll('[data-evcard]').forEach(card=>{
        const t = (card.getAttribute('data-evtext') || '');
        card.style.display = t.includes(q) ? '' : 'none';
      });
    });
  }

  // toggle event ON/OFF
  document.addEventListener('change', (e)=>{
    if(!e.target.classList.contains('ev-toggle')) return;
    const evId = parseInt(e.target.dataset.evid, 10);
    const on = !!e.target.checked;

    S[evId].on = on;

    if(!on){
      // kalau OFF, reset assignment (supaya gak nyangkut)
      S[evId].athletes.clear();
      S[evId].officials.clear();
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
    S[evId].officials.clear();

    renderHiddenInputs(evId);
    refreshEventCard(evId);
    refreshReview();
  });

  // modal elements
  const modalEvTitle = byId('modalEvTitle');
  const mAthList = byId('mAthList');
  const mOfcList = byId('mOfcList');
  const mAthCount = byId('mAthCount');
  const mOfcCount = byId('mOfcCount');
  const mSearchAth = byId('mSearchAth');
  const mSearchOfc = byId('mSearchOfc');

  function renderModalLists(evId){
    // build athlete list
    mAthList.innerHTML = '';
    ATHLETES.forEach(a=>{
      const id = a.id;
      const checked = S[evId].athletes.has(id);

      const row = document.createElement('label');
      row.className = 'd-flex align-items-start justify-content-between gap-2 p-2 mb-2 bg-white border rounded-4';
      row.style.cursor = 'pointer';

      row.setAttribute('data-text', (a.name+' '+a.code+' '+a.meta).toLowerCase());

      row.innerHTML = `
        <div class="d-flex gap-2 align-items-start" style="min-width:0;">
          <input class="form-check-input m-ath mt-1" type="checkbox" value="${id}" ${checked ? 'checked' : ''}>
          <div style="min-width:0;">
            <div class="fw-semibold text-truncate">${escapeHtml(a.name)}</div>
            <div class="small text-secondary text-truncate">[${escapeHtml(a.code)}] • ${escapeHtml(a.meta)}</div>
          </div>
        </div>
      `;
      mAthList.appendChild(row);
    });

    // build official list
    mOfcList.innerHTML = '';
    OFFICIALS.forEach(o=>{
      const id = o.id;
      const checked = S[evId].officials.has(id);

      const row = document.createElement('label');
      row.className = 'd-flex align-items-start justify-content-between gap-2 p-2 mb-2 bg-white border rounded-4';
      row.style.cursor = 'pointer';

      row.setAttribute('data-text', (o.name+' '+o.role).toLowerCase());

      row.innerHTML = `
        <div class="d-flex gap-2 align-items-start" style="min-width:0;">
          <input class="form-check-input m-ofc mt-1" type="checkbox" value="${id}" ${checked ? 'checked' : ''}>
          <div style="min-width:0;">
            <div class="fw-semibold text-truncate">${escapeHtml(o.name)}</div>
            <div class="small text-secondary text-truncate">${escapeHtml(o.role)}</div>
          </div>
        </div>
      `;
      mOfcList.appendChild(row);
    });

    updateModalCounts();
    // reset search
    if(mSearchAth) mSearchAth.value = '';
    if(mSearchOfc) mSearchOfc.value = '';
  }

  function escapeHtml(s){
    return String(s ?? '').replace(/[&<>"']/g, (m)=>({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[m]));
  }

  function updateModalCounts(){
    if(!currentEventId) return;
    mAthCount.textContent = S[currentEventId].athletes.size;
    mOfcCount.textContent = S[currentEventId].officials.size;
  }

  // open modal
  document.addEventListener('click', (e)=>{
    const btn = e.target.closest('.btn-assign');
    if(!btn) return;

    const evId = parseInt(btn.dataset.evid, 10);
    currentEventId = evId;

    const ev = EVENTS.find(x => x.id === evId);
    modalEvTitle.textContent = 'Event: ' + (ev ? ev.label : ('#'+evId));

    renderModalLists(evId);
    modal?.show();
  });

  // modal select all / clear (only visible rows)
  function setModalAll(type, checked){
    const list = type === 'ath' ? mAthList : mOfcList;
    const cls  = type === 'ath' ? '.m-ath' : '.m-ofc';
    list.querySelectorAll('label').forEach(lbl=>{
      if(lbl.style.display === 'none') return;
      const cb = lbl.querySelector(cls);
      if(cb) cb.checked = checked;
    });
  }

  byId('mAthAll')?.addEventListener('click', ()=>{ setModalAll('ath', true); });
  byId('mAthClear')?.addEventListener('click', ()=>{ setModalAll('ath', false); });
  byId('mOfcAll')?.addEventListener('click', ()=>{ setModalAll('ofc', true); });
  byId('mOfcClear')?.addEventListener('click', ()=>{ setModalAll('ofc', false); });

  // modal search filter
  function bindModalSearch(input, list){
    if(!input || !list) return;
    input.addEventListener('input', (e)=>{
      const q = (e.target.value || '').toLowerCase().trim();
      list.querySelectorAll('label').forEach(lbl=>{
        const t = (lbl.getAttribute('data-text') || '');
        lbl.style.display = t.includes(q) ? '' : 'none';
      });
    });
  }
  bindModalSearch(mSearchAth, mAthList);
  bindModalSearch(mSearchOfc, mOfcList);

  // save modal -> commit to state + hidden inputs
  byId('mSave')?.addEventListener('click', ()=>{
    if(!currentEventId) return;

    const evId = currentEventId;

    // commit athletes
    const athSet = new Set();
    mAthList.querySelectorAll('.m-ath:checked').forEach(cb=> athSet.add(parseInt(cb.value,10)));
    S[evId].athletes = athSet;

    // commit officials
    const ofcSet = new Set();
    mOfcList.querySelectorAll('.m-ofc:checked').forEach(cb=> ofcSet.add(parseInt(cb.value,10)));
    S[evId].officials = ofcSet;

    renderHiddenInputs(evId);
    refreshEventCard(evId);
    refreshReview();

    modal?.hide();
  });

  // reset all
  byId('btnResetAll')?.addEventListener('click', (e)=>{
    e.preventDefault();
    EVENTS.forEach(ev=>{
      S[ev.id].on = false;
      S[ev.id].athletes.clear();
      S[ev.id].officials.clear();

      // uncheck toggle
      const t = document.querySelector('.ev-toggle[data-evid="'+ev.id+'"]');
      if(t) t.checked = false;

      // clear hidden
      const box = byId('hidden_'+ev.id);
      if(box) box.innerHTML = '';

      refreshEventCard(ev.id);
    });
    refreshReview();
  });

  // init
  EVENTS.forEach(ev=> refreshEventCard(ev.id));
  refreshReview();
})();
</script>
@endpush

@endsection
