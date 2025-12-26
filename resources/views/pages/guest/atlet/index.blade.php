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

    {{-- Filter bar (GET query) --}}
    <form method="GET" action="" class="mt-3">
      <div class="d-flex flex-column flex-lg-row gap-2">
        {{-- Search --}}
        <div class="searchbar px-3 py-2 flex-grow-1">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-search text-secondary"></i>
            <input
              type="text"
              name="q"
              value="{{ request('q') }}"
              class="form-control p-0"
              placeholder="Cari atlet / kode / klub / kota / provinsi…"
            >
          </div>
        </div>

        <div class="d-flex gap-2 flex-wrap">
          <select name="gender" class="form-select" style="max-width: 180px">
            <option value="">Semua Gender</option>
            <option value="MALE" @selected(request('gender')==='MALE')>Male</option>
            <option value="FEMALE" @selected(request('gender')==='FEMALE')>Female</option>
          </select>

          <select name="province" class="form-select" style="max-width: 220px">
            <option value="">Semua Provinsi</option>
            {{-- jika kamu punya list provinsi dari backend, loop saja --}}
            @php
              $provList = $provList ?? collect([
                (object)['value'=>'padang','label'=>'Padang'],
                (object)['value'=>'sumbar','label'=>'Sumbar'],
              ]);
            @endphp
            @foreach($provList as $p)
              <option value="{{ $p->value }}" @selected(request('province')===$p->value)>{{ $p->label }}</option>
            @endforeach
          </select>

          <button class="btn btn-primary">
            <i class="bi bi-funnel me-1"></i>Filter
          </button>

          <a href="{{ url()->current() }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
          </a>
        </div>
      </div>

      <div class="mt-2 text-secondary small">
        Tip: gunakan kata kunci seperti <span class="badge text-bg-light border">ATH-01</span> atau nama klub/kota.
      </div>
    </form>
  </div>

  {{-- ====== GRID LIST (Mobile-first cards) ====== --}}
  @php
    $items = $data ?? collect();
  @endphp

  @if($items->count() === 0)
    <div class="empty-state p-4 text-center">
      <div class="mb-2">
        <i class="bi bi-info-circle fs-2 text-secondary"></i>
      </div>
      <div class="fw-semibold">Tidak ada atlet yang cocok.</div>
      <div class="text-secondary">Coba ubah kata kunci atau reset filter.</div>
    </div>
  @else
    <div class="row g-3">
      @foreach($items as $a)
        @php
          $gender = $a->gender ?? null;
          $photo  = $a->foto ?? null;
          $code   = $a->code ?? '-';
          $name   = $a->name ?? '-';
          $clubCurrent = $a->club?->name ?? '-';
          $bod    = $a->bod ?? null;
          $school = $a->school_name ?? '-';
          $city   = $a->city_name ?? '-';
          $prov   = $a->province_name ?? '-';

          // BOD format aman
          try {
            $bodLabel = $bod ? \Carbon\Carbon::parse($bod)->translatedFormat('d F Y') : '-';
          } catch (\Throwable $e) {
            $bodLabel = $bod ?? '-';
          }
        @endphp

        <div class="col-12 col-sm-6 col-lg-4">
          <div class="ath-card h-100">
            <div class="ath-cover"></div>

            <div class="px-3 pb-3">
              <div class="d-flex align-items-end gap-2">
                @if($photo)
                  <img class="avatar" src="{{ Storage::url($photo) }}" alt="{{ $name }}">
                @else
                  <div class="avatar d-flex align-items-center justify-content-center">
                    <i class="bi bi-person text-secondary fs-3"></i>
                  </div>
                @endif

                <div class="flex-grow-1 pb-1">
                  <div class="d-flex align-items-center justify-content-between gap-2">
                    <div class="fw-semibold text-truncate" title="{{ $name }}">{{ $name }}</div>

                    @if($gender === 'MALE')
                      <span class="badge badge-soft">MALE</span>
                    @elseif($gender === 'FEMALE')
                      <span class="badge text-bg-warning">FEMALE</span>
                    @endif
                  </div>

                  <div class="text-secondary small">
                    <span class="badge text-bg-light border">[{{ $code }}]</span>
                    <span class="ms-1 text-truncate d-inline-block" style="max-width: 210px" title="{{ $clubCurrent }}">
                      {{ $clubCurrent }}
                    </span>
                  </div>
                </div>
              </div>

              <div class="kvs mt-3">
                <div class="kv"><small>BOD</small>{{ $bodLabel }}</div>
                <div class="kv"><small>Sekolah</small>{{ $school }}</div>
                <div class="kv"><small>Kota</small>{{ $city }}</div>
                <div class="kv"><small>Provinsi</small>{{ $prov }}</div>
              </div>

              <div class="d-flex gap-2 mt-3">
                {{-- Sesuaikan route detail kalau sudah ada --}}
                <a href="{{ route('guest.atlet.show', $a->id ?? 0) ?? '#' }}" class="btn btn-outline-secondary w-100">
                  <i class="bi bi-person-vcard me-1"></i>Detail
                </a>
                <a href="" class="btn btn-primary w-100">
                  <i class="bi bi-trophy me-1"></i>Event  History
                </a>
              </div>
              <div class="mt-2">
                <a href="" class="btn btn-primary w-100">
                  <i class="bi bi-clock me-1"></i>Personal Time
                </a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>

    {{-- Pagination (kalau $athletes adalah paginator) --}}
    @if(method_exists($items, 'links'))
      <div class="d-flex justify-content-center mt-4">
        {{ $items->withQueryString()->links() }}
      </div>
    @endif
  @endif

@endsection
