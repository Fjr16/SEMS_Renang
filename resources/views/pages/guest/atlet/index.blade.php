@extends('layouts.main')

@section('content')

  {{-- ====== Custom CSS (khusus halaman ini) ====== --}}
  <style>
    :root{
      --bg:#f6f8fb;
      --surface:#ffffff;
      --muted:#6c757d;
      --ring: rgba(13,110,253,.12);
    }

    .page-hero{
      background: radial-gradient(1200px 400px at 10% 0%, rgba(13,110,253,.18), transparent),
                  radial-gradient(800px 400px at 100% 0%, rgba(25,135,84,.14), transparent),
                  linear-gradient(180deg, #ffffff, #f6f8fb);
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 1rem;
      box-shadow: 0 10px 30px rgba(16,24,40,.06);
      overflow: hidden;
    }

    /* ===== Filter Bar (Improved) ===== */
    .filter-bar{
    background: rgba(255,255,255,.72);
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 1rem;
    padding: .75rem;
    backdrop-filter: blur(10px);
    }

    .searchbar{
    border-radius: 999px;
    border: 1px solid rgba(0,0,0,.10);
    background: #fff;
    box-shadow: 0 10px 20px rgba(16,24,40,.04);
    padding: .55rem .75rem;
    }
    .searchbar:focus-within{
    outline: 3px solid var(--ring);
    border-color: rgba(13,110,253,.35);
    }
    .searchbar .form-control{
    border:0 !important;
    box-shadow:none !important;
    padding: 0 !important;
    min-height: 24px;
    }
    .searchbar .btn-clear{
    border: 0;
    background: transparent;
    padding: .15rem .35rem;
    border-radius: 999px;
    color: #6c757d;
    }
    .searchbar .btn-clear:hover{
    background: rgba(0,0,0,.06);
    color: #111827;
    }

    .filter-actions{
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: .5rem;
    }

    @media (min-width: 992px){
    .filter-grid{
        display: grid;
        grid-template-columns: 1.4fr .5fr auto auto;
        gap: .5rem;
        align-items: center;
    }
    .filter-actions{
        display: flex;
        gap: .5rem;
    }
    }

    .form-select, .btn{
    border-radius: .85rem;
    }

    .btn-pill{
    border-radius: 999px !important;
    padding-left: .9rem;
    padding-right: .9rem;
    }

    .chip{
      border: 1px solid rgba(0,0,0,.10);
      border-radius: 999px;
      padding: .35rem .7rem;
      font-size: .85rem;
      background: #fff;
      color: #111827;
      white-space: nowrap;
    }

    .ath-card{
      background: var(--surface);
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 1rem;
      overflow: hidden;
      box-shadow: 0 10px 24px rgba(16,24,40,.05);
      transition: transform .12s ease, box-shadow .12s ease;
    }
    .ath-card:hover{
      transform: translateY(-2px);
      box-shadow: 0 14px 30px rgba(16,24,40,.08);
    }
    .ath-cover{
      height: 72px;
      background: linear-gradient(90deg, rgba(13,110,253,.18), rgba(13,110,253,.06));
    }
    .avatar{
      width: 64px; height: 64px;
      border-radius: 14px;
      border: 3px solid #fff;
      box-shadow: 0 10px 20px rgba(0,0,0,.08);
      object-fit: cover;
      background: #e9ecef;
      margin-top: -32px;
    }

    .kvs{
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: .5rem;
      font-size: .9rem;
      color: #111827;
    }
    .kv{
      padding: .5rem .6rem;
      border: 1px dashed rgba(0,0,0,.10);
      border-radius: .75rem;
      background: rgba(0,0,0,.015);
    }
    .kv small{ color: var(--muted); display:block; font-size:.78rem; }

    .badge-soft{
      background: rgba(13,110,253,.10);
      color: #0d6efd;
      border: 1px solid rgba(13,110,253,.25);
      font-weight: 600;
    }

    .empty-state{
      border: 1px dashed rgba(0,0,0,.15);
      background: rgba(255,255,255,.8);
      border-radius: 1rem;
    }
  </style>

    {{-- ====== HERO + FILTER ====== --}}
    <div class="page-hero p-3 p-md-4 mb-4">
        <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-2">
        <div>
            <h1 class="h4 fw-bold mb-1">Daftar Atlet</h1>
            <p class="mb-0 text-secondary">
            Tampilan publik untuk melihat atlet yang terdaftar.
            </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
            <span class="chip">
            <i class="bi bi-people me-1"></i>
            {{ isset($athletes) ? (method_exists($athletes, 'total') ? $athletes->total() : $athletes->count()) : 0 }}
            Atlet
            </span>
            <span class="chip"><i class="bi bi-shield-check me-1"></i>Guest</span>
        </div>
        </div>

        <form method="GET" action="" class="mt-3">
            <div class="filter-bar">
                <div class="filter-grid">
                    {{-- SEARCH --}}
                    <div class="searchbar">
                        <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-search text-secondary"></i>

                        <input
                            id="q"
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            class="form-control"
                            placeholder="Cari atlet / kode / klub / kota / provinsi / sekolah…"
                            autocomplete="off"
                        >

                        @if(request('q'))
                            <a class="btn-clear" href="{{ url()->current() . '?' . http_build_query(request()->except('q')) }}" title="Hapus pencarian">
                            <i class="bi bi-x-lg"></i>
                            </a>
                        @else
                            <button type="button" class="btn-clear d-none" id="btnClear" title="Hapus">
                            <i class="bi bi-x-lg"></i>
                            </button>
                        @endif
                        </div>
                    </div>

                    {{-- GENDER --}}
                    <select name="gender" class="form-select">
                        <option value="">Semua Gender</option>
                        <option value="MALE" @selected(request('gender')==='MALE')>Male</option>
                        <option value="FEMALE" @selected(request('gender')==='FEMALE')>Female</option>
                    </select>

                    {{-- ACTIONS --}}
                    <div class="filter-actions">
                        <button class="btn btn-primary btn-pill">
                        <i class="bi bi-funnel me-1"></i>Filter
                        </button>

                        <a href="{{ url()->current() }}" class="btn btn-outline-secondary btn-pill">
                        <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                        </a>
                    </div>
                </div>

                <div class="mt-2 text-secondary small">
                Tip: gunakan kata kunci seperti <span class="badge text-bg-light border">ATH-01</span> atau nama klub/kota.
                </div>
            </div>

            {{-- Clear button logic (tanpa jQuery) --}}
            <script>
                (function(){
                const q = document.getElementById('q');
                const btn = document.getElementById('btnClear');
                if(!q || !btn) return;

                const toggle = () => btn.classList.toggle('d-none', !q.value);
                toggle();

                q.addEventListener('input', toggle);
                btn.addEventListener('click', () => {
                    q.value = '';
                    toggle();
                    q.focus();
                });
                })();
            </script>
        </form>

    </div>

    {{-- GRID --}}
    <div class="row g-3" id="athleteGrid">
        @include('pages.guest.atlet.partials.cards', ['athletes' => $athletes])
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
      <div class="fw-semibold">Tidak ada atlet yang cocok.</div>
      <div class="text-secondary">Coba ubah kata kunci atau reset filter.</div>
    </div>
@endsection

@push('scripts')
    <script>
        (function(){
            const grid = document.getElementById('athleteGrid');
            const sentinel = document.getElementById('sentinel');
            const spinner = document.getElementById('loadMoreSpinner');
            const emptyData = document.getElementById('empty_state');

            let nextPageUrl = @json($athletes->nextPageUrl());
            let countItems = @json($athletes->count());
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
        })();
    </script>
@endpush
