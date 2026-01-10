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
  .soft-card{
    background: var(--surface);
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 1rem;
    box-shadow: 0 10px 24px rgba(16,24,40,.05);
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
  .step.active{
    border-color: rgba(13,110,253,.35);
    background: rgba(13,110,253,.10);
    color:#0d6efd;
    font-weight:800;
  }
  .searchbar{
    border-radius: 999px;
    border: 1px solid rgba(0,0,0,.10);
    background: #fff;
  }
  .searchbar:focus-within{
    outline: 3px solid var(--ring);
    border-color: rgba(13,110,253,.35);
  }
  .searchbar .form-control{ border:0; box-shadow:none; }
  .box-scroll{
    border:1px solid rgba(0,0,0,.08);
    border-radius: .85rem;
    padding: .75rem;
    background: rgba(0,0,0,.012);
    max-height: 55vh;
    overflow:auto;
  }
  .item-check{
    border: 1px solid rgba(0,0,0,.06);
    border-radius: .75rem;
    padding: .55rem .65rem;
    background: #fff;
  }
  .item-check:hover{ background: rgba(13,110,253,.04); }
  .btn-pill{ border-radius: 999px; font-weight: 800; }
  .kv{
    border: 1px dashed rgba(0,0,0,.14);
    border-radius: .85rem;
    padding:.55rem .65rem;
    background: rgba(0,0,0,.015);
  }
  .kv small{ display:block; color:var(--muted); font-size:.78rem; }
</style>

@php
  // Controller idealnya mengirim:
  // $competition, $teams, $events, $athletes, $officials
  $comp = $competition ?? (object)[];
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
      <a href="{{ route('club.registration') }}" class="btn btn-outline-secondary btn-pill">
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

<form id="regForm" method="POST" action="{{ route('club.registration.store', $comp->id ?? 0) }}">
  @csrf

  <div class="row g-3">
    {{-- LEFT: selection --}}
    <div class="col-12 col-lg-7">
      {{-- Step 1: Team --}}
      <div class="soft-card p-3 mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
          <div>
            <div class="fw-bold">1) Pilih Team</div>
            <div class="text-secondary small">Jika kamu punya lebih dari 1 team.</div>
          </div>
        </div>

        <select class="form-select" name="team_id" id="team_id" required>
          <option value="">-- Pilih Team --</option>
          @foreach($teams as $t)
            <option value="{{ $t->id }}">{{ $t->name }}</option>
          @endforeach
        </select>
      </div>

      {{-- Step 2: Event --}}
      <div class="soft-card p-3 mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
          <div>
            <div class="fw-bold">2) Pilih Event yang diikuti</div>
            <div class="text-secondary small">Centang event yang ingin diikuti.</div>
          </div>

          <div class="input-group searchbar" style="max-width: 320px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" id="eventSearch" placeholder="Cari event…">
          </div>
        </div>

        <div class="box-scroll" id="eventList">
          @foreach($events as $ev)
            @php
              $label = $ev->label ?? ($ev->event_no.' - '.$ev->name);
              $meta  = ($ev->gender ?? '-') . ' • ' . ($ev->age_group ?? '-') . ' • ' . ($ev->distance ?? '') . ($ev->stroke ?? '');
            @endphp
            <label class="item-check d-flex align-items-center justify-content-between gap-2 mb-2">
              <div class="d-flex align-items-start gap-2">
                <input class="form-check-input ev-check" type="checkbox" name="event_ids[]" value="{{ $ev->id }}">
                <div>
                  <div class="fw-semibold">{{ $label }}</div>
                  <div class="text-secondary small">{{ $meta }}</div>
                </div>
              </div>
              <span class="badge text-bg-light border">{{ $ev->id }}</span>
            </label>
          @endforeach
        </div>

        <div class="text-secondary small mt-2">
          Setelah pilih event, lanjut pilih atlet & official.
        </div>
      </div>

      {{-- Step 3: Athlete --}}
      <div class="soft-card p-3 mb-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
          <div>
            <div class="fw-bold">3) Pilih Atlet</div>
            <div class="text-secondary small">Atlet yang tersedia untuk team kamu.</div>
          </div>

          <div class="input-group searchbar" style="max-width: 320px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" id="athSearch" placeholder="Cari atlet…">
          </div>
        </div>

        <div class="box-scroll" id="athList">
          @foreach($athletes as $a)
            @php
              $name = $a->name ?? '-';
              $code = $a->code ?? '-';
              $meta = ($a->gender ?? '-') . ' • ' . ($a->bod_label ?? ($a->bod ?? '-'));
            @endphp
            <label class="item-check d-flex align-items-center justify-content-between gap-2 mb-2">
              <div class="d-flex align-items-center gap-2">
                <input class="form-check-input ath-check" type="checkbox" name="athlete_ids[]" value="{{ $a->id }}">
                <div>
                  <div class="fw-semibold">{{ $name }}</div>
                  <div class="text-secondary small">[{{ $code }}] • {{ $meta }}</div>
                </div>
              </div>
              <span class="badge text-bg-light border">{{ $a->id }}</span>
            </label>
          @endforeach
        </div>
      </div>

      {{-- Step 4: Officials --}}
      <div class="soft-card p-3">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
          <div>
            <div class="fw-bold">4) Pilih Official</div>
            <div class="text-secondary small">Official yang ikut mendampingi team.</div>
          </div>

          <div class="input-group searchbar" style="max-width: 320px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" id="ofcSearch" placeholder="Cari official…">
          </div>
        </div>

        <div class="box-scroll" id="ofcList">
          @foreach($officials as $o)
            @php
              $name = $o->name ?? '-';
              $role = $o->role_name ?? ($o->position ?? 'Official');
            @endphp
            <label class="item-check d-flex align-items-center justify-content-between gap-2 mb-2">
              <div class="d-flex align-items-center gap-2">
                <input class="form-check-input ofc-check" type="checkbox" name="official_ids[]" value="{{ $o->id }}">
                <div>
                  <div class="fw-semibold">{{ $name }}</div>
                  <div class="text-secondary small">{{ $role }}</div>
                </div>
              </div>
              <span class="badge text-bg-light border">{{ $o->id }}</span>
            </label>
          @endforeach
        </div>
      </div>
    </div>

    {{-- RIGHT: Review --}}
    <div class="col-12 col-lg-5">
      <div class="soft-card p-3 position-sticky" style="top: 88px;">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="fw-bold">Review</div>
          <span class="badge text-bg-light border">Draft</span>
        </div>

        <div class="row g-2">
          <div class="col-6"><div class="kv"><small>Event dipilih</small><span id="rvEvents">0</span></div></div>
          <div class="col-6"><div class="kv"><small>Atlet dipilih</small><span id="rvAthletes">0</span></div></div>
          <div class="col-6"><div class="kv"><small>Official dipilih</small><span id="rvOfficials">0</span></div></div>
          <div class="col-6"><div class="kv"><small>Team</small><span id="rvTeam">-</span></div></div>
        </div>

        <div class="text-secondary small mt-2">
          Pastikan event sudah dipilih sebelum submit.
        </div>

        <div class="d-flex gap-2 mt-3">
          <button type="submit" class="btn btn-primary btn-pill w-100" id="btnSubmitReg" disabled>
            <i class="bi bi-send-check me-1"></i>Submit Entry
          </button>
        </div>

        <div class="text-secondary small mt-2">
          Setelah submit, status bisa <b>Pending</b> / <b>Approved</b> sesuai aturan panitia.
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

  const btnSubmit = document.getElementById('btnSubmitReg');

  function countChecked(selector){
    return document.querySelectorAll(selector + ':checked').length;
  }

  function refreshReview(){
    const ev = countChecked('.ev-check');
    const ath = countChecked('.ath-check');
    const ofc = countChecked('.ofc-check');

    rvEvents.textContent = ev;
    rvAthletes.textContent = ath;
    rvOfficials.textContent = ofc;

    const teamText = teamSel?.selectedOptions?.[0]?.textContent ?? '-';
    rvTeam.textContent = teamText;

    // minimal: harus pilih team + minimal 1 event + minimal 1 atlet
    btnSubmit.disabled = !(teamSel.value && ev > 0 && ath > 0);
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

  bindSearch('eventSearch', '#eventList', '.item-check');
  bindSearch('athSearch', '#athList', '.item-check');
  bindSearch('ofcSearch', '#ofcList', '.item-check');

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

  refreshReview();
})();
</script>
@endpush

@endsection
