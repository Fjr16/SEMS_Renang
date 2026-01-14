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
    .stepper{
        display:flex;
        gap:.5rem;
        flex-wrap:wrap;
    }
    .step{
        border:1px solid rgba(0,0,0,.10);
        border-radius:999px;
        padding:.35rem .7rem;
        background:#fff;
        color:#111827;
        font-size:.85rem;
        display:flex; align-items:center; gap:.4rem;
    }

    .btn-pill{ border-radius: 999px; font-weight: 800; }

    .entry-shell{
        --bdr: rgba(0,0,0,.08);
        --muted: var(--muted);
        --soft: rgba(0,0,0,.02);
        --ring: var(--ring);
    }
    .entry-card{
        background:#fff;
        border:1px solid var(--bdr);
        border-radius: 1rem;
        box-shadow: 0 10px 24px rgba(16,24,40,.05);
    }
    .entry-card .card-head{
        display:flex; align-items:center; justify-content:space-between;
        gap:.75rem;
        padding: .9rem 1rem;
        border-bottom:1px solid rgba(0,0,0,.06);
    }
    .entry-card .card-bodyy{ padding: 1rem; }

    .step-badge{
        display:inline-flex; align-items:center; gap:.5rem;
        padding:.35rem .6rem;
        border-radius:999px;
        background: rgba(13,110,253,.08);
        border:1px solid rgba(13,110,253,.18);
        color:#0d6efd;
        font-weight:800;
        font-size:.85rem;
        white-space:nowrap;
    }
    .mini-help{ color: var(--muted); font-size:.875rem; }

    .pill-input{
        border-radius:999px !important;
        border:1px solid rgba(0,0,0,.10) !important;
        background:#fff;
    }
    .pill-input:focus{
        border-color: rgba(13,110,253,.35) !important;
        box-shadow: 0 0 0 .25rem var(--ring) !important;
    }

    .list-box{
        border:1px solid rgba(0,0,0,.08);
        border-radius: 1rem;
        background: linear-gradient(180deg, rgba(0,0,0,.012), rgba(0,0,0,.006));
        overflow:hidden;
    }
    .list-toolbar{
        display:flex; align-items:center; justify-content:space-between;
        gap:.75rem;
        padding: .75rem .85rem;
        border-bottom:1px solid rgba(0,0,0,.06);
        background:#fff;
    }
    .list-scroll{
        max-height: 48vh;
        overflow:auto;
        padding: .75rem;
    }

    .pick-item{
        border:1px solid rgba(0,0,0,.06);
        border-radius: .9rem;
        padding: .65rem .75rem;
        background:#fff;
        display:flex;
        gap:.75rem;
        align-items:flex-start;
        justify-content:space-between;
        transition: .15s ease;
    }
    .pick-item:hover{ transform: translateY(-1px); box-shadow: 0 10px 18px rgba(16,24,40,.06); }
    .pick-item.is-checked{
        border-color: rgba(13,110,253,.25);
        box-shadow: 0 0 0 .2rem rgba(13,110,253,.08);
        background: rgba(13,110,253,.03);
    }

    .pick-left{ display:flex; gap:.65rem; align-items:flex-start; min-width: 0; }
    .pick-title{ font-weight:800; line-height:1.2; }
    .pick-meta{ color: var(--muted); font-size:.85rem; }
    .pick-right{ display:flex; align-items:center; gap:.5rem; }

    .tiny-chip{
        border:1px solid rgba(0,0,0,.10);
        border-radius:999px;
        padding:.2rem .55rem;
        font-size:.78rem;
        background:#fff;
        color:#111827;
        white-space:nowrap;
    }

    .review-box{
        border:1px solid rgba(0,0,0,.08);
        border-radius: 1.15rem;
        padding: 1rem;
        background:
        radial-gradient(1000px 400px at 30% 0%, rgba(13,110,253,.10), transparent),
        radial-gradient(1000px 400px at 100% 0%, rgba(25,135,84,.08), transparent),
        linear-gradient(180deg, #fff, #fbfcff);
        box-shadow: 0 16px 34px rgba(16,24,40,.08);
    }
    .review-kv{
        border: 1px solid rgba(0,0,0,.08);
        background:#fff;
        border-radius: .9rem;
        padding:.65rem .75rem;
    }
    .review-kv small{ display:block; color: var(--muted); font-size:.78rem; }
    .review-kv .val{ font-weight:900; font-size: 1.05rem; }

    .selected-chips{
        display:flex; flex-wrap:wrap; gap:.35rem;
        padding-top:.5rem;
    }
    .selected-chips .chipx{
        display:inline-flex; align-items:center; gap:.35rem;
        border:1px dashed rgba(0,0,0,.18);
        background: rgba(0,0,0,.02);
        border-radius: 999px;
        padding:.22rem .55rem;
        font-size:.78rem;
        color:#111827;
        max-width:100%;
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
</style>

@php
  // Controller idealnya mengirim:
  // $competition, $teams, $events, $athletes, $officials
  $teams = $teams ?? collect();
  $events = $events ?? collect();
  $athletes = $athletes ?? collect();
  $officials = $officials ?? collect();
@endphp

<div class="page-hero p-3 p-md-4 mb-3">
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
    <div>
      <div class="d-flex gap-2 flex-wrap mb-2">
        <span class="chip"><i class="bi bi-person-badge me-1"></i>Club Manager</span>
        <span class="chip"><i class="bi bi-clipboard-check me-1"></i>Registrasi</span>
      </div>
      <h1 class="h4 fw-bold mb-1">Registrasi: {{ $comp->name ?? 'Kompetisi' }}</h1>
      <div class="text-secondary small">
        <span class="badge text-bg-light border">{{ $comp->code ?? 'COMP' }}</span>
        <span class="ms-2">Deadline: {{ $comp->reg_deadline_label ?? '-' }}</span>
      </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('manager.club.registration') }}" class="btn btn-outline-secondary btn-pill">
        <i class="bi bi-arrow-left me-1"></i>Kembali
      </a>
    </div>
  </div>

  <div class="stepper mt-3">
    <div class="step active"><i class="bi bi-1-circle"></i>Team</div>
    <div class="step"><i class="bi bi-2-circle"></i>Event</div>
    <div class="step"><i class="bi bi-3-circle"></i>Peserta</div>
    <div class="step"><i class="bi bi-4-circle"></i>Review</div>
  </div>
</div>

{{-- <form id="regForm" method="POST" action="{{ route('club.registration.store', $comp->id ?? 0) }}"> --}}
<form id="regForm" method="POST" action="#">
  @csrf

  <div class="entry-shell">
    <div class="row g-3">

      {{-- LEFT --}}
      <div class="col-12 col-lg-7">

        {{-- STEP 1: TEAM --}}
        <div class="entry-card mb-3">
          <div class="card-head">
            <div class="d-flex align-items-center gap-2">
              <span class="step-badge"><i class="bi bi-1-circle"></i> Team</span>
              <div class="d-none d-md-block mini-help">Pilih team yang akan mendaftarkan entries.</div>
            </div>
            <a href="#" class="btn btn-sm btn-soft">
              <i class="bi bi-people me-1"></i>Kelola Team
            </a>
          </div>
          <div class="card-bodyy">
            <label class="form-label fw-semibold mb-1">Team</label>
            <select class="form-select pill-input" name="team_id" id="team_id" required>
              <option value="">-- Pilih Team --</option>
              @foreach($teams as $t)
                <option value="{{ $t->id }}">{{ $t->name }}</option>
              @endforeach
            </select>

            <div class="mini-help mt-2">
              Tips: kalau kamu punya lebih dari 1 team, pastikan team yang dipilih sesuai atlet yang akan didaftarkan.
            </div>
          </div>
        </div>

        {{-- STEP 2: EVENT --}}
        <div class="entry-card mb-3">
          <div class="card-head">
            <div class="d-flex align-items-center gap-2">
              <span class="step-badge"><i class="bi bi-2-circle"></i> Event</span>
              <div class="mini-help d-none d-md-block">Centang event yang ingin diikuti.</div>
            </div>

            <div class="d-flex align-items-center gap-2">
              <button type="button" class="btn btn-sm btn-soft" id="evSelectAll">
                <i class="bi bi-check2-square me-1"></i>Select All
              </button>
              <button type="button" class="btn btn-sm btn-soft" id="evClear">
                <i class="bi bi-x-circle me-1"></i>Clear
              </button>
            </div>
          </div>

          <div class="card-bodyy pt-3">
            <div class="input-group mb-3">
              <span class="input-group-text bg-white pill-input" style="border-right:0;">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control pill-input" id="eventSearch" placeholder="Cari event…"
                     style="border-left:0;">
            </div>

            <div class="list-box">
              <div class="list-toolbar">
                <div class="mini-help">
                  <i class="bi bi-info-circle me-1"></i>
                  Pilih minimal 1 event.
                </div>
                <span class="tiny-chip">
                  Dipilih: <b id="evCount">0</b>
                </span>
              </div>

              <div class="list-scroll" id="eventList">
                @foreach($events as $ev)
                  @php
                    $label = $ev->label ?? ($ev->event_no.' - '.$ev->name);
                    $meta  = ($ev->gender ?? '-') . ' • ' . ($ev->age_group ?? '-') . ' • ' . ($ev->distance ?? '') . ($ev->stroke ?? '');
                  @endphp

                  <label class="pick-item mb-2" data-type="event">
                    <div class="pick-left">
                      <input class="form-check-input ev-check mt-1" type="checkbox" name="event_ids[]" value="{{ $ev->id }}">
                      <div class="min-w-0" style="min-width:0;">
                        <div class="pick-title text-truncate">{{ $label }}</div>
                        <div class="pick-meta text-truncate">{{ $meta }}</div>
                      </div>
                    </div>
                    <div class="pick-right">
                      <span class="tiny-chip">#{{ $ev->id }}</span>
                      <i class="bi bi-chevron-right text-secondary"></i>
                    </div>
                  </label>
                @endforeach
              </div>
            </div>

            <div class="mini-help mt-2">
              Setelah memilih event, lanjut pilih atlet & official.
            </div>
          </div>
        </div>

        {{-- STEP 3: ATHLETE --}}
        <div class="entry-card mb-3">
          <div class="card-head">
            <div class="d-flex align-items-center gap-2">
              <span class="step-badge"><i class="bi bi-3-circle"></i> Atlet</span>
              <div class="mini-help d-none d-md-block">Centang atlet yang akan didaftarkan.</div>
            </div>

            <div class="d-flex align-items-center gap-2">
              <button type="button" class="btn btn-sm btn-soft" id="athSelectAll">
                <i class="bi bi-check2-square me-1"></i>Select All
              </button>
              <button type="button" class="btn btn-sm btn-soft" id="athClear">
                <i class="bi bi-x-circle me-1"></i>Clear
              </button>
            </div>
          </div>

          <div class="card-bodyy pt-3">
            <div class="input-group mb-3">
              <span class="input-group-text bg-white pill-input" style="border-right:0;">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control pill-input" id="athSearch" placeholder="Cari atlet…"
                     style="border-left:0;">
            </div>

            <div class="list-box">
              <div class="list-toolbar">
                <div class="mini-help">
                  <i class="bi bi-info-circle me-1"></i>
                  Pilih minimal 1 atlet.
                </div>
                <span class="tiny-chip">
                  Dipilih: <b id="athCount">0</b>
                </span>
              </div>

              <div class="list-scroll" id="athList">
                @foreach($athletes as $a)
                  @php
                    $name = $a->name ?? '-';
                    $code = $a->code ?? '-';
                    $meta = ($a->gender ?? '-') . ' • ' . ($a->bod_label ?? ($a->bod ?? '-'));
                  @endphp

                  <label class="pick-item mb-2" data-type="athlete">
                    <div class="pick-left">
                      <input class="form-check-input ath-check mt-1" type="checkbox" name="athlete_ids[]" value="{{ $a->id }}">
                      <div style="min-width:0;">
                        <div class="pick-title text-truncate">{{ $name }}</div>
                        <div class="pick-meta text-truncate">[{{ $code }}] • {{ $meta }}</div>
                      </div>
                    </div>
                    <div class="pick-right">
                      <span class="tiny-chip">#{{ $a->id }}</span>
                      <i class="bi bi-chevron-right text-secondary"></i>
                    </div>
                  </label>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        {{-- STEP 4: OFFICIAL --}}
        <div class="entry-card">
          <div class="card-head">
            <div class="d-flex align-items-center gap-2">
              <span class="step-badge"><i class="bi bi-4-circle"></i> Official</span>
              <div class="mini-help d-none d-md-block">Optional, sesuai kebutuhan panitia.</div>
            </div>

            <div class="d-flex align-items-center gap-2">
              <button type="button" class="btn btn-sm btn-soft" id="ofcSelectAll">
                <i class="bi bi-check2-square me-1"></i>Select All
              </button>
              <button type="button" class="btn btn-sm btn-soft" id="ofcClear">
                <i class="bi bi-x-circle me-1"></i>Clear
              </button>
            </div>
          </div>

          <div class="card-bodyy pt-3">
            <div class="input-group mb-3">
              <span class="input-group-text bg-white pill-input" style="border-right:0;">
                <i class="bi bi-search"></i>
              </span>
              <input type="text" class="form-control pill-input" id="ofcSearch" placeholder="Cari official…"
                     style="border-left:0;">
            </div>

            <div class="list-box">
              <div class="list-toolbar">
                <div class="mini-help">
                  <i class="bi bi-info-circle me-1"></i>
                  Official tidak wajib (kecuali aturan lomba mengharuskan).
                </div>
                <span class="tiny-chip">
                  Dipilih: <b id="ofcCount">0</b>
                </span>
              </div>

              <div class="list-scroll" id="ofcList">
                @foreach($officials as $o)
                  @php
                    $name = $o->name ?? '-';
                    $role = $o->role_name ?? ($o->position ?? 'Official');
                  @endphp

                  <label class="pick-item mb-2" data-type="official">
                    <div class="pick-left">
                      <input class="form-check-input ofc-check mt-1" type="checkbox" name="official_ids[]" value="{{ $o->id }}">
                      <div style="min-width:0;">
                        <div class="pick-title text-truncate">{{ $name }}</div>
                        <div class="pick-meta text-truncate">{{ $role }}</div>
                      </div>
                    </div>
                    <div class="pick-right">
                      <span class="tiny-chip">#{{ $o->id }}</span>
                      <i class="bi bi-chevron-right text-secondary"></i>
                    </div>
                  </label>
                @endforeach
              </div>
            </div>

            <div class="mini-help mt-2">
              Jika official diwajibkan, kamu bisa set validasi minimal jumlah official di backend.
            </div>
          </div>
        </div>

      </div>

      {{-- RIGHT: REVIEW --}}
      <div class="col-12 col-lg-5">
        <div class="review-box position-sticky" style="top: 88px;">
          <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
            <div>
              <div class="fw-bold" style="font-size:1.05rem;">Review</div>
              <div class="mini-help">Ringkasan pilihan sebelum submit.</div>
            </div>
            <span class="tiny-chip">
              <i class="bi bi-pencil-square me-1"></i>Draft
            </span>
          </div>

          <div class="progress-soft my-3" aria-hidden="true">
            <div id="progressFill"></div>
          </div>

          <div class="row g-2">
            <div class="col-6">
              <div class="review-kv">
                <small>Event dipilih</small>
                <div class="val" id="rvEvents">0</div>
              </div>
            </div>
            <div class="col-6">
              <div class="review-kv">
                <small>Atlet dipilih</small>
                <div class="val" id="rvAthletes">0</div>
              </div>
            </div>
            <div class="col-6">
              <div class="review-kv">
                <small>Official dipilih</small>
                <div class="val" id="rvOfficials">0</div>
              </div>
            </div>
            <div class="col-6">
              <div class="review-kv">
                <small>Team</small>
                <div class="val text-truncate" id="rvTeam">-</div>
              </div>
            </div>
          </div>

          <div class="selected-chips" id="selectedChips"></div>

          <div class="alert alert-light border mt-3 mb-3" role="alert" style="border-radius:1rem;">
            <div class="d-flex gap-2">
              <i class="bi bi-shield-check text-primary"></i>
              <div class="mini-help mb-0">
                Minimal: <b>Team</b> + <b>1 Event</b> + <b>1 Atlet</b>.
                Setelah submit, status bisa <b>Pending</b> / <b>Approved</b>.
              </div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-100" id="btnSubmitReg" disabled
                  style="border-radius:999px; font-weight:900;">
            <i class="bi bi-send-check me-1"></i>Submit Entry
          </button>

          <div class="d-flex gap-2 mt-2">
            <a href="#" class="btn btn-soft w-100">
              <i class="bi bi-eye me-1"></i>Preview
            </a>
            <a href="#" class="btn btn-soft w-100">
              <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
            </a>
          </div>
        </div>
      </div>

    </div>
  </div>
</form>

@push('scripts')
<script>
(function(){
  const teamSel = document.getElementById('team_id');

  const rvTeam = document.getElementById('rvTeam');
  const rvEvents = document.getElementById('rvEvents');
  const rvAthletes = document.getElementById('rvAthletes');
  const rvOfficials = document.getElementById('rvOfficials');

  const evCount = document.getElementById('evCount');
  const athCount = document.getElementById('athCount');
  const ofcCount = document.getElementById('ofcCount');

  const progressFill = document.getElementById('progressFill');
  const selectedChips = document.getElementById('selectedChips');

  const btnSubmit = document.getElementById('btnSubmitReg');

  function qsa(sel){ return Array.from(document.querySelectorAll(sel)); }

  function countChecked(selector){
    return document.querySelectorAll(selector + ':checked').length;
  }

  function togglePickedClass(){
    qsa('label.pick-item').forEach(lbl=>{
      const cb = lbl.querySelector('input[type="checkbox"]');
      if(!cb) return;
      lbl.classList.toggle('is-checked', cb.checked);
    });
  }

  function buildSelectedChips(){
    if(!selectedChips) return;

    const teamText = teamSel?.selectedOptions?.[0]?.textContent?.trim();
    const ev = countChecked('.ev-check');
    const ath = countChecked('.ath-check');
    const ofc = countChecked('.ofc-check');

    selectedChips.innerHTML = '';

    if(teamSel?.value){
      const el = document.createElement('div');
      el.className = 'chipx';
      el.innerHTML = `<i class="bi bi-people"></i><span class="text-truncate">${teamText}</span>`;
      selectedChips.appendChild(el);
    }

    if(ev){
      const el = document.createElement('div');
      el.className = 'chipx';
      el.innerHTML = `<i class="bi bi-flag"></i><span>${ev} event</span>`;
      selectedChips.appendChild(el);
    }

    if(ath){
      const el = document.createElement('div');
      el.className = 'chipx';
      el.innerHTML = `<i class="bi bi-person"></i><span>${ath} atlet</span>`;
      selectedChips.appendChild(el);
    }

    if(ofc){
      const el = document.createElement('div');
      el.className = 'chipx';
      el.innerHTML = `<i class="bi bi-person-badge"></i><span>${ofc} official</span>`;
      selectedChips.appendChild(el);
    }
  }

  function refreshReview(){
    const ev = countChecked('.ev-check');
    const ath = countChecked('.ath-check');
    const ofc = countChecked('.ofc-check');

    rvEvents.textContent = ev;
    rvAthletes.textContent = ath;
    rvOfficials.textContent = ofc;

    if(evCount) evCount.textContent = ev;
    if(athCount) athCount.textContent = ath;
    if(ofcCount) ofcCount.textContent = ofc;

    const teamText = teamSel?.selectedOptions?.[0]?.textContent ?? '-';
    rvTeam.textContent = teamText;

    // progress: team (25) + event (35) + athlete (35) + official (5)
    let p = 0;
    if(teamSel.value) p += 25;
    if(ev > 0) p += 35;
    if(ath > 0) p += 35;
    if(ofc > 0) p += 5;
    if(progressFill) progressFill.style.width = Math.min(100, p) + '%';

    // minimal: harus pilih team + minimal 1 event + minimal 1 atlet
    btnSubmit.disabled = !(teamSel.value && ev > 0 && ath > 0);

    togglePickedClass();
    buildSelectedChips();
  }

  // Search filter
  function bindSearch(inputId, listSelector, itemSelector){
    const input = document.getElementById(inputId);
    if(!input) return;
    input.addEventListener('input', (e)=>{
      const q = (e.target.value || '').toLowerCase().trim();
      document.querySelectorAll(listSelector + ' ' + itemSelector).forEach(item=>{
        item.style.display = item.innerText.toLowerCase().includes(q) ? '' : 'none';
      });
    });
  }

  bindSearch('eventSearch', '#eventList', 'label.pick-item');
  bindSearch('athSearch', '#athList', 'label.pick-item');
  bindSearch('ofcSearch', '#ofcList', 'label.pick-item');

  // Select all / clear helpers (only visible items)
  function setAll(selector, checked){
    qsa(selector).forEach(cb=>{
      const parent = cb.closest('label.pick-item');
      if(parent && parent.style.display === 'none') return;
      cb.checked = checked;
    });
    refreshReview();
  }

  document.getElementById('evSelectAll')?.addEventListener('click', ()=> setAll('.ev-check', true));
  document.getElementById('evClear')?.addEventListener('click', ()=> setAll('.ev-check', false));

  document.getElementById('athSelectAll')?.addEventListener('click', ()=> setAll('.ath-check', true));
  document.getElementById('athClear')?.addEventListener('click', ()=> setAll('.ath-check', false));

  document.getElementById('ofcSelectAll')?.addEventListener('click', ()=> setAll('.ofc-check', true));
  document.getElementById('ofcClear')?.addEventListener('click', ()=> setAll('.ofc-check', false));

  // listeners
  document.addEventListener('change', (e)=>{
    if(
      e.target.matches('.ev-check') ||
      e.target.matches('.ath-check') ||
      e.target.matches('.ofc-check') ||
      e.target.matches('#team_id')
    ){
      refreshReview();
    }
  });

  // (opsional) tombol reset base: hanya reset form
  document.querySelector('a.btn.btn-soft[href="#"] i.bi-arrow-counterclockwise')?.closest('a')
    ?.addEventListener('click', (e)=>{
      e.preventDefault();
      document.getElementById('regForm')?.reset();
      refreshReview();
    });

  refreshReview();
})();
</script>
@endpush

@endsection
