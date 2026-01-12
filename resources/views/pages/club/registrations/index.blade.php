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
  .searchbar{
    border-radius: 999px;
    border: 1px solid rgba(0,0,0,.10);
    background: #fff;
    box-shadow: 0 10px 20px rgba(16,24,40,.04);
  }
  .searchbar:focus-within{
    outline: 3px solid var(--ring);
    border-color: rgba(13,110,253,.35);
  }
  .searchbar .form-control{ border:0; box-shadow:none; }
  .soft-card{
    background: var(--surface);
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 1rem;
    box-shadow: 0 10px 24px rgba(16,24,40,.05);
  }
  .comp-card{
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 1rem;
    overflow:hidden;
    background:#fff;
    box-shadow: 0 10px 24px rgba(16,24,40,.05);
    transition: transform .12s ease, box-shadow .12s ease;
  }
  .comp-card:hover{
    transform: translateY(-2px);
    box-shadow: 0 14px 30px rgba(16,24,40,.08);
  }
  .comp-cover{
    height: 70px;
    background: linear-gradient(90deg, rgba(13,110,253,.22), rgba(13,110,253,.06));
  }
  .badge-soft{
    background: rgba(13,110,253,.10);
    color: #0d6efd;
    border: 1px solid rgba(13,110,253,.25);
    font-weight: 700;
  }
  .kv{
    border: 1px dashed rgba(0,0,0,.14);
    border-radius: .85rem;
    padding: .55rem .65rem;
    background: rgba(0,0,0,.015);
  }
  .kv small{ display:block; color:var(--muted); font-size:.78rem; }
  .btn-pill{ border-radius: 999px; font-weight: 800; }
</style>

@php
  // Controller idealnya mengirim:
  // $competitionsOpen (collection/paginator), $club, $team
  $items = $competitionsOpen ?? collect();
@endphp

<div class="page-hero p-3 p-md-4 mb-4">
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
    <div>
      <div class="d-flex gap-2 flex-wrap mb-2">
        <span class="chip"><i class="bi bi-person-badge me-1"></i>Club Manager</span>
        <span class="chip"><i class="bi bi-clipboard-check me-1"></i>Registrasi Kompetisi</span>
      </div>
      <h1 class="h4 fw-bold mb-1">Pilih Kompetisi</h1>
      <p class="mb-0 text-secondary">Pilih kompetisi yang sedang membuka pendaftaran untuk mendaftarkan team, atlet, dan official.</p>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('manager.club.dashboard') ?? '#' }}" class="btn btn-outline-secondary btn-pill">
        <i class="bi bi-arrow-left me-1"></i>Kembali
      </a>
    </div>
  </div>

  <form method="GET" class="mt-3">
    <div class="d-flex flex-column flex-lg-row gap-2">
      <div class="searchbar px-3 py-2 flex-grow-1">
        <div class="d-flex align-items-center gap-2">
          <i class="bi bi-search text-secondary"></i>
          <input type="text" name="q" value="{{ request('q') }}" class="form-control p-0"
                 placeholder="Cari kompetisi (nama/kode/lokasi)…">
        </div>
      </div>
      <div class="d-flex gap-2 flex-wrap">
        <select name="status" class="form-select" style="max-width: 210px">
          <option value="">Semua Status</option>
          <option value="open" @selected(request('status')==='open')>Open Registration</option>
          <option value="soon" @selected(request('status')==='soon')>Soon</option>
        </select>
        <button class="btn btn-primary btn-pill">
          <i class="bi bi-funnel me-1"></i>Filter
        </button>
        <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-pill">
          <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
        </a>
      </div>
    </div>
  </form>
</div>

@if($items->count() === 0)
  <div class="soft-card p-4 text-center">
    <div class="mb-2"><i class="bi bi-info-circle fs-2 text-secondary"></i></div>
    <div class="fw-semibold">Belum ada kompetisi yang open.</div>
    <div class="text-secondary">Silakan cek kembali nanti atau hubungi panitia.</div>
  </div>
@else
  <div class="row g-3">
    @foreach($items as $c)
      @php
        $name = $c->name ?? '-';
        $code = $c->code ?? 'COMP';
        $city = $c->city_name ?? '-';
        $venue= $c->venue_name ?? '-';
        $start= $c->start_date_label ?? ($c->start_date ?? '-');
        $end  = $c->end_date_label ?? ($c->end_date ?? '-');
        $deadline = $c->reg_deadline_label ?? ($c->registration_deadline ?? '-');
        $status = $c->registration_status ?? 'open';
      @endphp

      <div class="col-12 col-md-6 col-lg-4">
        <div class="comp-card h-100">
          <div class="comp-cover"></div>
          <div class="p-3">
            <div class="d-flex align-items-start justify-content-between gap-2">
              <div class="fw-bold">{{ $name }}</div>
              <span class="badge badge-soft">{{ strtoupper($status) }}</span>
            </div>
            <div class="text-secondary small mt-1">
              <span class="badge text-bg-light border">[{{ $code }}]</span>
              <span class="ms-1">{{ $city }}</span>
            </div>

            <div class="row g-2 mt-2">
              <div class="col-6"><div class="kv"><small>Venue</small>{{ $venue }}</div></div>
              <div class="col-6"><div class="kv"><small>Deadline</small>{{ $deadline }}</div></div>
              <div class="col-6"><div class="kv"><small>Start</small>{{ $start }}</div></div>
              <div class="col-6"><div class="kv"><small>End</small>{{ $end }}</div></div>
            </div>

            <div class="d-flex gap-2 mt-3">
              <a href="{{ route('cm.registrations.show', $c->id ?? 0) ?? '#' }}" class="btn btn-primary btn-pill w-100">
                <i class="bi bi-clipboard-check me-1"></i>Daftar
              </a>
              <a href="{{ route('guest.competitions.show', $c->id ?? 0) ?? '#' }}" class="btn btn-outline-secondary btn-pill w-100">
                <i class="bi bi-eye me-1"></i>Detail
              </a>
            </div>

            <div class="text-secondary small mt-2">
              Pastikan data atlet lengkap sebelum submit entry.
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  @if(method_exists($items, 'links'))
    <div class="d-flex justify-content-center mt-4">
      {{ $items->withQueryString()->links() }}
    </div>
  @endif
@endif

@endsection
