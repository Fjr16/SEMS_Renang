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
@endphp

<div class="page-hero p-3 p-md-4 mb-3">
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
    <div>
        @if (Route::is('manager.club.registration'))
        <div class="d-flex gap-2 flex-wrap mb-2">
          <span class="chip"><i class="bi bi-person-badge me-1"></i>Team Manager</span>
          <span class="chip"><i class="bi bi-clipboard-check me-1"></i>Pendaftaran Kompetisi</span>
        </div>
        @endif
      <h1 class="h4 fw-bold mb-1">Daftar Kompetisi</h1>
      @if (Route::is('manager.club.registration'))
      <div class="text-secondary">Daftarkan team, atlet, & official + pantau status pendaftaran.</div>
      @endif
      @if (Route::is('guest.competition.index'))
      <div class="text-secondary">Tampilan publik untuk melihat kompetisi yang terdaftar</div>
      @endif
    </div>

    @if (Route::is('manager.club.registration'))
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('manager.club.dashboard') }}" class="btn btn-outline-secondary btn-pill">
        <i class="bi bi-arrow-left me-1"></i>Kembali
      </a>
    </div>
    @endif
    @if (Route::is('guest.competition.index'))
    <div class="d-flex gap-2 flex-wrap">
        <span class="chip">
            <i class="bi bi-people me-1"></i>
            {{ isset($data) ? (method_exists($data, 'total') ? $data->total() : $data->count()) : 0 }}
            Kompetisi
        </span>
        <span class="chip"><i class="bi bi-shield-check me-1"></i>{{ $accessType ?? 'Guest' }}</span>
    </div>
    @endif
  </div>

  @if (Route::is('manager.club.registration'))
    {{-- Summary status --}}
    <div class="row g-2 mt-3">
        @foreach (App\Enums\CompetitionTeamStatus::cases() as $registStts)
            <div class="col-6 col-md">
                <div class="mini-kv {{ $registStts->class() }} text-white rounded-3 p-2 text-center">
                    <div class="fs-4 fw-bold">{{ $counts[$registStts->value] ?? 0 }}</div>
                    <small class="text-white opacity-75">{{ $registStts->label() }}</small>
                </div>
            </div>
        @endforeach
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
  @endif
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
                        placeholder="Cari kompetisi (nama kompetisi / penyelenggara / nama venue)…"
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
                        <option value="">Semua</option>
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

    <div class="row g-3" id="competitionGrid">
        @include('pages.club.registrations.partials.cards')
    </div>

    {{-- Sentinel + loader --}}
    <div class="py-4 text-center" id="loadMoreWrap">
        <div class="d-none" id="loadMoreSpinner">
            <div class="spinner-border" role="status"></div>
            <div class="text-secondary small mt-2">Memuat data...</div>
        </div>

        {{-- sentinel: kalau terlihat -> load next page --}}
        <div id="sentinel" style="height: 1px;"></div>
    </div>
    <div class="empty-state p-4 text-center d-none" id="empty_state">
      <div class="mb-2">
        <i class="bi bi-info-circle fs-2 text-secondary"></i>
      </div>
      <div class="fw-semibold">Tidak ada kompetisi yang cocok.</div>
      <div class="text-secondary">Coba ubah kata kunci atau reset filter.</div>
    </div>
  </div>

  @if (Route::is('manager.club.registration'))
  {{-- =============== TAB HISTORY =============== --}}
  <div class="tab-pane fade" id="tabHistory">
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
                        placeholder="Cari kompetisi (nama kompetisi / penyelenggara / nama venue)…"
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
                    <select name="historyStatus" id="historyStatus" class="form-select select-pill" style="min-width: 210px;">
                        <option value="">Semua</option>
                        @foreach ($compClass::cases() as $stts)
                            <option value="{{ $stts->value }}" @selected(request('historyStatus') === $stts->value)>{{ $stts->label() }}</option>
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
                $events_count = $e->competitionEntries
                                ->whereIn('status', [\App\Enums\CompetitionTeamEntryStatus::Pending->value, \App\Enums\CompetitionTeamEntryStatus::Active->value])
                                ->groupBy('competition_event_id')->count();
                $individualAtlets = $e->competitionEntries->where('is_relay', false)
                                    ->whereIn('status', [\App\Enums\CompetitionTeamEntryStatus::Pending->value, \App\Enums\CompetitionTeamEntryStatus::Active->value])
                                    ->pluck('athlete_id')->unique();
                $relayAtlets = $e->competitionEntries->where('is_relay', true)
                            ->flatMap(function($entry){
                                return $entry->competitionEntryRelayMembers
                                        ->where('status', 'active')
                                        ->pluck('athlete_id');
                            })->unique();

                $athletes_count = $individualAtlets->merge($relayAtlets)->unique()->count();
                $st = $e->status instanceof \App\Enums\CompetitionTeamStatus
                    ? $e->status
                    : \App\Enums\CompetitionTeamStatus::tryFrom($e->status ?? 'pending');
                $paySt = $e->payment_status instanceof \App\Enums\CompetitionTeamPaymentStatus
                    ? $e->payment_status
                    : \App\Enums\CompetitionTeamPaymentStatus::tryFrom($e->payment_status ?? 'unpaid');
            @endphp

          <div class="col-12 col-lg-6 history-item" data-status="{{ $st?->value }}">
            <div class="soft-card p-3">
                <div class="d-flex align-items-start justify-content-between gap-2">
                    <div>
                        <div class="fw-bold">{{ '[' . ($e->competition?->code ?? '-') . '] ' . ($e->competition?->name ?? '-') }}</div>
                        <div class="text-secondary small mt-1 d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge text-bg-light border">Entry #{{ str_pad($e->id,2,'0', STR_PAD_LEFT) }}</span>
                            <span>
                                <i class="bi bi-calendar3 me-1"></i>
                                {{ \Carbon\Carbon::parse($e->created_at)->translatedFormat('d F Y') }}
                            </span>
                        </div>
                    </div>
                    <span class="badge {{ $st?->class() ?? 'bg-secondary' }} text-white text-nowrap">
                        <i class="{{ $st?->icon() ?? 'bi bi-circle' }} me-1"></i>
                        {{ $st?->label() ?? 'Tidak Diketahui' }}
                    </span>
                </div>

                <div class="row g-2 mt-2">
                    <div class="col-4">
                        <div class="mini-kv text-center">
                            <small><i class="bi bi-flag me-1"></i>Event</small>
                            {{-- {{ $e->competitionEntries->groupBy('competition_event_id')->count() ?? 0 }} --}}
                            {{ $events_count }}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mini-kv text-center">
                            <small><i class="bi bi-person-arms-up me-1"></i>Atlet</small>
                            {{ $athletes_count }}
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mini-kv text-center">
                            <small><i class="bi bi-person-badge me-1"></i>Official</small>
                            {{ $e->competitionTeamOfficials->count() ?? 0 }}
                        </div>
                    </div>
                </div>

              {{-- Payment Info --}}
                <div class="mt-3 p-2 rounded-3 border d-flex align-items-center justify-content-between flex-wrap gap-2"
                    style="background: var(--bs-light)">
                    <div class="d-flex align-items-center gap-2">
                        <small class="text-secondary">Status Pembayaran</small>
                        <span class="badge {{ $paySt?->class() ?? 'bg-secondary' }}">
                            <i class="{{ $paySt?->icon() ?? 'bi bi-circle' }} me-1"></i>{{ $paySt?->label() ?? 'Tidak diketahui' }}
                        </span>
                    </div>
                    <div class="text-end">
                        <div class="fw-bold text-dark">
                            Rp {{ number_format($e->total_fee ?? 0, 0, ',', '.') }}
                        </div>
                        <small class="text-secondary">Total Biaya</small>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-2 flex-wrap mt-3">
                    <a href="#" class="btn btn-outline-secondary btn-pill btn-sm">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>

                    @if($st?->value === App\Enums\CompetitionTeamStatus::Pending->value || $st?->value === App\Enums\CompetitionTeamStatus::Rejected->value)
                        <a href="{{ route('manager.club.registration.create', ['competition' => $e->competition]) }}" class="btn btn-outline-primary btn-pill btn-sm">
                            <i class="bi bi-pencil-square me-1"></i>Edit Entry
                        </a>
                        {{-- <button class="btn btn-outline-danger btn-pill btn-sm">
                            <i class="bi bi-x-circle me-1"></i>Batalkan Pendaftaran
                        </button> --}}
                        @if($st?->value === App\Enums\CompetitionTeamStatus::Rejected->value)
                            {{-- tombol info --}}
                            <button
                                class="btn btn-outline-info btn-pill btn-sm"
                                title="Alasan penolakan"
                                data-bs-toggle="modal"
                                data-bs-target="#modalReason"
                                data-reason="{{ $e->notes ?? '' }}"
                            >
                                <i class="bi bi-info-circle"></i>
                                Info
                            </button>
                        @endif
                    @endif

                    @if($st?->value === App\Enums\CompetitionTeamStatus::Active->value)
                        <a href="#" class="btn btn-outline-success btn-pill btn-sm">
                            <i class="bi bi-download me-1"></i>Export Start List
                        </a>
                        @if(($e->payment_status ?? 'unpaid') !== App\Enums\CompetitionTeamPaymentStatus::Paid->value)
                            <span class="text-secondary small fst-italic align-self-center">
                                <i class="bi bi-info-circle me-1"></i>
                                Segera lakukan pembayaran agar tim anda terdaftar pada kompetisi
                            </span>
                        @endif
                    @endif

                    @if(in_array($st?->value, [App\Enums\CompetitionTeamStatus::Withdrawn->value, App\Enums\CompetitionTeamStatus::Disqualified->value]))
                        <span class="text-secondary small fst-italic align-self-center">
                            <i class="bi bi-info-circle me-1"></i>
                            {{ $st?->value === App\Enums\CompetitionTeamStatus::Withdrawn->value ? 'Tim dinyatakan telah mengundurkan diri oleh panitia' : 'Tim didiskualifikasi oleh panitia' }}
                        </span>
                    @endif
                </div>
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
  @endif
</div>

{{-- modal menampilkan alasan reject entry --}}
<div class="modal fade" id="modalReason" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alasan Penolakan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p id="reasonText">-</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function(){
        const grid = document.getElementById('competitionGrid');
        const sentinel = document.getElementById('sentinel');
        const spinner = document.getElementById('loadMoreSpinner');
        const emptyData = document.getElementById('empty_state');

        let nextPageUrl = @json($data->nextPageUrl());
        let countItems = @json($data->count());
        let loading = false;

        // kalau memang tidak ada halaman berikutnya dari awal
        if(countItems === 0){
            emptyData.classList.remove('d-none');
        }

        async function loadNext(){
            if (!nextPageUrl || loading) return;
            loading = true;
            spinner.classList.remove('d-none');

            try{
                // penting: server detect ajax -> return partial cards
                const res = await fetch(nextPageUrl, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                if(!res.ok) throw new Error('Gagal memuat data');
                const html = await res.text();

                if (!html.trim()) { // jaga-jaga kalau server benar2 kosong
                    nextPageUrl = null;
                    observer.disconnect();
                    return;
                }

                // append cards
                const tmp = document.createElement('div');
                tmp.innerHTML = html;

                // ambil next url dari marker sebelum dipindah ke grid
                const marker = tmp.querySelector('.js-next-page');
                nextPageUrl = marker ? marker.dataset.next : null;
                if (marker) marker.remove();

                while(tmp.firstChild){
                    grid.appendChild(tmp.firstChild);
                }

            // stop bila sudah melewati last page: cek kalau response kosong
            if (!nextPageUrl) {
                observer.disconnect();
            }
            }catch(err){
                console.error(err);
            }finally{
                spinner.classList.add('d-none');
                loading = false;
            }
        }

        // Observer: saat sentinel terlihat -> load
        const observer = new IntersectionObserver((entries) => {
            if(entries[0].isIntersecting) loadNext();
        }, { rootMargin: '400px' });

        if (sentinel) observer.observe(sentinel);

        // open modal info alasan penolakan
        const modal = document.getElementById('modalReason');

        modal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const reason = button.getAttribute('data-reason');

            document.getElementById('reasonText').textContent = reason || '-';
        });
    })();

    if("{{ Route::is('manager.club.registration') }}"){
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
    }
</script>
@endpush

@endsection
