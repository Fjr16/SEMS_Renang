@extends('layouts.main')

@section('content')
<style>
  :root{ --ring: rgba(13,110,253,.14); }
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
  .tab-pill .nav-link{
    border-radius: 999px;
    padding: .45rem .9rem;
    font-weight: 800;
    color: #334155;
    border: 1px solid rgba(0,0,0,.08);
    background: rgba(255,255,255,.7);
  }
  .tab-pill .nav-link.active{
    background: rgba(13,110,253,.12);
    color: #0d6efd;
    border-color: rgba(13,110,253,.25);
  }

  .btn-pill{ border-radius: 999px; font-weight: 800; }
  .status-pill{
    border-radius: 999px;
    padding:.25rem .6rem;
    font-size:.8rem;
    font-weight:800;
    border:1px solid rgba(0,0,0,.10);
    background:#fff;
  }
  .status-draft{ color:#6c757d; background: rgba(108,117,125,.08); border-color: rgba(108,117,125,.18); }
  .status-pending{ color:#b58100; background: rgba(255,193,7,.18); border-color: rgba(255,193,7,.30); }
  .status-approved{ color:#198754; background: rgba(25,135,84,.14); border-color: rgba(25,135,84,.25); }
  .status-rejected{ color:#dc3545; background: rgba(220,53,69,.12); border-color: rgba(220,53,69,.22); }

  .mini-kv{
    border: 1px dashed rgba(0,0,0,.14);
    border-radius: .85rem;
    padding:.55rem .65rem;
    background: rgba(0,0,0,.015);
  }
  .mini-kv small{ display:block; color:#6c757d; font-size:.78rem; }

  /* style untuk searchbar */
.soft-card{
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 1rem;
    box-shadow: 0 10px 24px rgba(16,24,40,.05);
    background:#fff;
  }
  .filter-card{
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 1rem;
    background: rgba(255,255,255,.85);
    box-shadow: 0 10px 24px rgba(16,24,40,.05);
    backdrop-filter: blur(6px);
  }

  .searchbar{
    border-radius: 999px;
    border: 1px solid rgba(0,0,0,.10);
    background: #fff;
    box-shadow: 0 10px 20px rgba(16,24,40,.04);
    min-height: 46px;
  }
  .searchbar:focus-within{
    outline: 3px solid rgba(13,110,253,.14);
    border-color: rgba(13,110,253,.35);
  }
  .searchbar .form-control{
    border:0; box-shadow:none;
    padding-top: .15rem; padding-bottom: .15rem;
  }

  .filter-group{
    display:flex;
    gap:.5rem;
    flex-wrap: wrap;
    align-items: center;
    justify-content: flex-end;
  }

  .select-pill{
    border-radius: 999px;
    border: 1px solid rgba(0,0,0,.10);
    min-height: 46px;
    padding-left: .9rem;
    padding-right: 2.2rem; /* space caret */
    background-color:#fff;
  }
  .btn-pill{
    border-radius: 999px;
    font-weight: 800;
    min-height: 46px;
    display:inline-flex;
    align-items:center;
    gap:.4rem;
    padding: .55rem .95rem;
  }

  .btn-ghost{
    border-radius: 999px;
    min-height: 46px;
    padding: .55rem .9rem;
  }

  .hint{
    color:#6c757d;
    font-size:.85rem;
  }

  /* rapihin tampilan di layar kecil */
  @media (max-width: 575.98px){
    .filter-group{ justify-content: stretch; }
    .filter-group > *{ flex: 1 1 auto; }
    .btn-pill, .btn-ghost, .select-pill{ width:100%; }
  }
</style>

@php
  // $competitionsOpen, $entries (riwayat), $counts (draft,pending,approved,rejected)
  $entries = $entries ?? collect();
  $counts = $counts ?? ['draft'=>0,'pending'=>0,'approved'=>0,'rejected'=>0];
@endphp

<div class="page-hero p-3 p-md-4 mb-3">
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
    <div>
      <div class="d-flex gap-2 flex-wrap mb-2">
        <span class="chip"><i class="bi bi-person-badge me-1"></i>Team Manager</span>
        <span class="chip"><i class="bi bi-clipboard-check me-1"></i>Pendaftaran Kompetisi</span>
      </div>
      <h1 class="h4 fw-bold mb-1">Registrasi Kompetisi</h1>
      <div class="text-secondary">Daftarkan team, atlet, & official + pantau status pendaftaran.</div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('manager.club.dashboard') }}" class="btn btn-outline-secondary btn-pill">
        <i class="bi bi-arrow-left me-1"></i>Kembali
      </a>
    </div>
  </div>

  {{-- Summary status --}}
  <div class="row g-2 mt-3">
    <div class="col-6 col-md-3"><div class="mini-kv"><small>Draft</small>{{ $counts['draft'] ?? 0 }}</div></div>
    <div class="col-6 col-md-3"><div class="mini-kv"><small>Pending</small>{{ $counts['pending'] ?? 0 }}</div></div>
    <div class="col-6 col-md-3"><div class="mini-kv"><small>Approved</small>{{ $counts['approved'] ?? 0 }}</div></div>
    <div class="col-6 col-md-3"><div class="mini-kv"><small>Rejected</small>{{ $counts['rejected'] ?? 0 }}</div></div>
  </div>

  {{-- Tabs --}}
  <ul class="nav tab-pill mt-3 gap-2" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabOpen" type="button">
        <i class="bi bi-door-open me-1"></i>Kompetisi Open
      </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabHistory" type="button">
        <i class="bi bi-clock-history me-1"></i>Riwayat & Proses
      </button>
    </li>
  </ul>
</div>

<div class="tab-content">
  {{-- =============== TAB OPEN =============== --}}
  <div class="tab-pane fade show active" id="tabOpen">
    <div class="soft-card filter-card p-3 mb-3">
        <form method="GET" class="row g-2">
            {{-- Search --}}
            <div class="col-12 col-lg">
                <div class="searchbar px-3 py-2">
                    <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-search text-secondary"></i>
                    <input
                        type="text"
                        name="q"
                        value="{{ request('q') }}"
                        class="form-control p-0"
                        placeholder="Cari kompetisi (nama / penyelenggara / lokasi)…"
                    >
                    @if(request('q'))
                        <a href="{{ url()->current() }}" class="text-secondary text-decoration-none" title="Hapus kata kunci">
                        <i class="bi bi-x-circle"></i>
                        </a>
                    @endif
                    </div>
                </div>
                <div class="hint mt-2">
                    Tip: coba kata kunci seperti <span class="badge text-bg-light border">Padang</span> atau nama penyelenggara.
                </div>
            </div>

            {{-- Filters + Buttons --}}
            <div class="col-12 col-lg-auto">
                <div class="filter-group">
                    <select name="status" class="form-select select-pill" style="min-width: 210px;">
                        @foreach ($compClass::cases() as $stts)
                            <option value="{{ $stts->value }}" @selected(request('status') === $stts->value)>{{ $stts->label() }}</option>
                        @endforeach
                    </select>

                    <button class="btn btn-primary btn-pill" type="submit">
                    <i class="bi bi-funnel"></i> Filter
                    </button>

                    <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-ghost" type="button">
                    <i class="bi bi-arrow-counterclockwise"></i>
                        <span class="d-none d-md-inline">Reset</span>
                        <span class="d-inline d-md-none">Reset</span>
                    </a>
                </div>
            </div>
        </form>
    </div>

    @include('pages.club.registrations.partials.cards')
  </div>

  {{-- =============== TAB HISTORY =============== --}}
  <div class="tab-pane fade" id="tabHistory">
    <div class="soft-card p-3 mb-3">
      <div class="d-flex flex-column flex-lg-row gap-2 align-items-lg-center justify-content-between">
        <div class="fw-bold">Riwayat Pendaftaran</div>

        <div class="d-flex gap-2 flex-wrap">
          <select id="historyStatus" class="form-select" style="max-width: 210px">
            <option value="">Semua Status</option>
            <option value="draft">Draft</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
          </select>

          <div class="searchbar px-3 py-2" style="min-width: 260px;">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-search text-secondary"></i>
              <input id="historySearch" type="text" class="form-control p-0" placeholder="Cari kompetisi/kode…">
            </div>
          </div>
        </div>
      </div>
    </div>

    @if($entries->count() === 0)
      <div class="soft-card p-4 text-center">
        <div class="mb-2"><i class="bi bi-clock-history fs-2 text-secondary"></i></div>
        <div class="fw-semibold">Belum ada riwayat pendaftaran.</div>
        <div class="text-secondary">Jika kamu sudah daftar, statusnya akan muncul di sini.</div>
      </div>
    @else
      <div class="row g-3" id="historyGrid">
        @foreach($entries as $e)
          @php
            $st = $e->status ?? 'draft';
            $pill = match($st){
              'pending','submitted' => 'status-pill status-pending',
              'approved' => 'status-pill status-approved',
              'rejected' => 'status-pill status-rejected',
              default => 'status-pill status-draft'
            };
            $stLabel = strtoupper($st === 'submitted' ? 'PENDING' : $st);
          @endphp

          <div class="col-12 col-lg-6 history-item" data-status="{{ $st }}">
            <div class="soft-card p-3">
              <div class="d-flex align-items-start justify-content-between gap-2">
                <div>
                  <div class="fw-bold">{{ $e->competition_name ?? 'Kompetisi' }}</div>
                  <div class="text-secondary small">
                    <span class="badge text-bg-light border">Entry #{{ $e->code ?? ($e->id ?? '-') }}</span>
                    <span class="ms-2">{{ $e->created_at_label ?? '-' }}</span>
                  </div>
                </div>
                <span class="{{ $pill }}">{{ $stLabel }}</span>
              </div>

              <div class="row g-2 mt-2">
                <div class="col-4"><div class="mini-kv"><small>Event</small>{{ $e->events_count ?? 0 }}</div></div>
                <div class="col-4"><div class="mini-kv"><small>Atlet</small>{{ $e->athletes_count ?? 0 }}</div></div>
                <div class="col-4"><div class="mini-kv"><small>Official</small>{{ $e->officials_count ?? 0 }}</div></div>
              </div>

              <div class="d-flex gap-2 flex-wrap mt-3">
                {{-- <a href="{{ route('cm.entries.show', $e->id ?? 0) ?? '#' }}" class="btn btn-outline-secondary btn-pill btn-sm"> --}}
                <a href="#" class="btn btn-outline-secondary btn-pill btn-sm">
                  <i class="bi bi-eye me-1"></i>Detail
                </a>

                @if(in_array($st, ['draft']))
                  {{-- <a href="{{ route('cm.registrations.edit', $e->id ?? 0) ?? '#' }}" class="btn btn-primary btn-pill btn-sm"> --}}
                  <a href="#" class="btn btn-primary btn-pill btn-sm">
                    <i class="bi bi-pencil-square me-1"></i>Lanjutkan
                  </a>
                @endif

                @if(in_array($st, ['approved']))
                  {{-- <a href="{{ route('cm.entries.export', $e->id ?? 0) ?? '#' }}" class="btn btn-outline-primary btn-pill btn-sm"> --}}
                  <a href="#" class="btn btn-outline-primary btn-pill btn-sm">
                    <i class="bi bi-download me-1"></i>Export
                  </a>
                @endif

                @if(in_array($st, ['draft','submitted','pending']))
                  <button type="button" class="btn btn-outline-danger btn-pill btn-sm"
                          onclick="alert('Nanti sambungkan ke endpoint cancel')">
                    <i class="bi bi-x-circle me-1"></i>Batalkan
                  </button>
                @endif
              </div>

              @if(!empty($e->last_note))
                <div class="text-secondary small mt-2">
                  <i class="bi bi-chat-left-text me-1"></i>{{ $e->last_note }}
                </div>
              @endif
            </div>
          </div>
        @endforeach
      </div>

      @if(method_exists($entries,'links'))
        <div class="d-flex justify-content-center mt-4">
          {{ $entries->withQueryString()->links() }}
        </div>
      @endif
    @endif
  </div>
</div>

@push('scripts')
<script>
(function(){
  const statusSel = document.getElementById('historyStatus');
  const searchInp = document.getElementById('historySearch');
  const items = () => document.querySelectorAll('#historyGrid .history-item');

  function applyFilter(){
    const st = (statusSel?.value || '').toLowerCase().trim();
    const q  = (searchInp?.value || '').toLowerCase().trim();

    items().forEach(el=>{
      const okStatus = !st || (el.dataset.status || '').toLowerCase() === st
        || (st === 'pending' && ['pending','submitted'].includes((el.dataset.status || '').toLowerCase()));

      const okText = !q || el.innerText.toLowerCase().includes(q);

      el.style.display = (okStatus && okText) ? '' : 'none';
    });
  }

  statusSel?.addEventListener('change', applyFilter);
  searchInp?.addEventListener('input', applyFilter);

  // Fix DataTables/tab issue? (jaga-jaga kalau nanti tab ini ada table)
  document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(btn=>{
    btn.addEventListener('shown.bs.tab', ()=> window.dispatchEvent(new Event('resize')));
  });
})();
</script>
@endpush

@endsection
