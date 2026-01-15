@extends('layouts.main')

@section('content')
<style>
  :root{
    --surface:#ffffff;
    --muted:#6c757d;
    --ring: rgba(13,110,253,.14);
  }
  .cm-hero{
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
  .icon-badge{
    width: 44px; height: 44px;
    border-radius: 14px;
    display:flex; align-items:center; justify-content:center;
    background: rgba(13,110,253,.10);
    border: 1px solid rgba(13,110,253,.18);
    color:#0d6efd;
    box-shadow: 0 10px 20px rgba(16,24,40,.06);
    flex: 0 0 auto;
  }
  .btn-pill{
    border-radius: 999px;
    font-weight: 700;
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
  .kvs{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap:.5rem;
  }
  .kv{
    border: 1px dashed rgba(0,0,0,.14);
    border-radius: .85rem;
    padding:.55rem .65rem;
    background: rgba(0,0,0,.015);
  }
  .kv small{ display:block; color:var(--muted); font-size:.78rem; }
  .list-mini{
    display:flex;
    gap:.5rem;
    flex-wrap: wrap;
  }
  .mini-link{
    border: 1px solid rgba(0,0,0,.10);
    border-radius: 999px;
    padding:.32rem .65rem;
    background:#fff;
    color:#111827;
    text-decoration:none;
    font-size:.85rem;
  }
  .mini-link:hover{ border-color: rgba(13,110,253,.35); color:#0d6efd; }
  .table-slim td, .table-slim th{ padding:.55rem .6rem; }
</style>


{{-- =================== HERO =================== --}}
<div class="cm-hero p-3 p-md-4 mb-4">
  <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
    <div>
      <div class="d-flex align-items-center gap-2 flex-wrap">
        <span class="chip"><i class="bi bi-person-badge me-1"></i>Club Manager</span>
        <span class="chip"><i class="bi bi-shield-check me-1"></i>Akses Team</span>
      </div>

      <h1 class="h4 fw-bold mb-1 mt-2">Dashboard Team</h1>
      <div class="text-secondary">
        <span class="fw-semibold">{{ $item?->clubRoleCategory?->name ?? '-' }}</span>
        <span class="mx-2">•</span>
        <span>{{ $item?->club_name ?? '-' }}</span>
      </div>
    </div>

    <div class="d-flex gap-2 flex-wrap">
      {{-- <a href="{{ route('cm.team.profile') ?? '#' }}" class="btn btn-outline-secondary btn-pill"> --}}
      <a href="#" class="btn btn-outline-secondary btn-pill">
        <i class="bi bi-building me-1"></i>Profil Team
      </a>
      {{-- <a href="{{ route('cm.registration') ?? '#' }}" class="btn btn-primary btn-pill"> --}}
      <a href="{{ route('manager.club.registration') }}" class="btn btn-primary btn-pill">
        <i class="bi bi-clipboard-check me-1"></i>Daftar Kompetisi
      </a>
    </div>
  </div>
</div>

{{-- =================== QUICK ACTIONS =================== --}}
<div class="row g-3 mb-3">
  {{-- RU Team --}}
  <div class="col-12 col-lg-4">
    <div class="soft-card p-3 h-100">
      <div class="d-flex align-items-start gap-3">
        <div class="icon-badge"><i class="bi bi-people fs-5"></i></div>
        <div class="flex-grow-1">
          <div class="fw-bold">Ringkasan</div>
          <div class="text-secondary small mb-2">
            Jumlah data official dan atlet
          </div>

          <div class="kvs mb-3">
            <div class="kv"><small>Atlet</small>{{ $item?->athletes?->count() ?? 0 }}</div>
            <div class="kv"><small>Official</small>{{ $item?->officials?->count() ?? 0 }}</div>
          </div>

          {{-- <div class="list-mini"> --}}
            {{-- <a class="mini-link" href="{{ route('cm.team.ru') ?? '#' }}"><i class="bi bi-diagram-3 me-1"></i>Lihat RU</a>
            <a class="mini-link" href="{{ route('cm.team.documents') ?? '#' }}"><i class="bi bi-folder2-open me-1"></i>Dokumen</a> --}}
            {{-- <a class="mini-link" href="#"><i class="bi bi-diagram-3 me-1"></i>Lihat RU</a> --}}
            {{-- <a class="mini-link" href="#"><i class="bi bi-folder2-open me-1"></i>Dokumen</a> --}}
          {{-- </div> --}}
        </div>
      </div>
    </div>
  </div>

  {{-- CRUD Atlet --}}
  <div class="col-12 col-lg-4">
    <div class="soft-card p-3 h-100">
      <div class="d-flex align-items-start gap-3">
        <div class="icon-badge"><i class="bi bi-person-lines-fill fs-5"></i></div>
        <div class="flex-grow-1">
          <div class="fw-bold">Kelola Atlet</div>
          <div class="text-secondary small mb-3">
            Manajemen data atlet pada tim terkait
          </div>

          <div class="d-flex gap-2 flex-wrap">
            <a href="{{ route('manager.club.atlet', ['club' => $item]) }}" class="btn btn-outline-secondary btn-sm btn-pill">
              <i class="bi bi-list-ul me-1"></i>Daftar Atlet
            </a>
            {{-- <a href="{{ route('cm.athletes.create') ?? '#' }}" class="btn btn-primary btn-sm btn-pill"> --}}
            <a href="#" class="btn btn-primary btn-sm btn-pill">
              <i class="bi bi-plus-circle me-1"></i>Tambah
            </a>
          </div>

          {{-- <div class="list-mini mt-3"> --}}
            {{-- <a class="mini-link" href="{{ route('cm.athletes.import') ?? '#' }}"><i class="bi bi-upload me-1"></i>Import</a>
            <a class="mini-link" href="{{ route('cm.athletes.export') ?? '#' }}"><i class="bi bi-download me-1"></i>Export</a> --}}
            {{-- <a class="mini-link" href="#"><i class="bi bi-upload me-1"></i>Import</a> --}}
            {{-- <a class="mini-link" href="#"><i class="bi bi-download me-1"></i>Export</a> --}}
          {{-- </div> --}}
        </div>
      </div>
    </div>
  </div>

  {{-- CRUD Official --}}
  <div class="col-12 col-lg-4">
    <div class="soft-card p-3 h-100">
      <div class="d-flex align-items-start gap-3">
        <div class="icon-badge"><i class="bi bi-person-workspace fs-5"></i></div>
        <div class="flex-grow-1">
          <div class="fw-bold">Kelola Official</div>
          <div class="text-secondary small mb-3">
            Data pelatih, manajer, pendamping, dan official team.
          </div>

          <div class="d-flex gap-2 flex-wrap">
            {{-- <a href="{{ route('cm.officials.index') ?? '#' }}" class="btn btn-outline-secondary btn-sm btn-pill"> --}}
            <a href="#" class="btn btn-outline-secondary btn-sm btn-pill">
              <i class="bi bi-list-ul me-1"></i>Daftar Official
            </a>
            {{-- <a href="{{ route('cm.officials.create') ?? '#' }}" class="btn btn-primary btn-sm btn-pill"> --}}
            <a href="#" class="btn btn-primary btn-sm btn-pill">
              <i class="bi bi-plus-circle me-1"></i>Tambah
            </a>
          </div>

          {{-- <div class="list-mini mt-3"> --}}
            {{-- <a class="mini-link" href="{{ route('cm.officials.import') ?? '#' }}"><i class="bi bi-upload me-1"></i>Import</a>
            <a class="mini-link" href="{{ route('cm.officials.export') ?? '#' }}"><i class="bi bi-download me-1"></i>Export</a> --}}
            {{-- <a class="mini-link" href="#"><i class="bi bi-upload me-1"></i>Import</a> --}}
            {{-- <a class="mini-link" href="#"><i class="bi bi-download me-1"></i>Export</a> --}}
          {{-- </div> --}}
        </div>
      </div>
    </div>
  </div>
</div>

{{-- =================== REGISTRATION HUB =================== --}}
<div class="row g-3">
  <div class="col-12 col-lg-7">
    <div class="soft-card p-3">
      <div class="d-flex align-items-center justify-content-between gap-2 flex-wrap mb-2">
        <div>
          <div class="fw-bold">Pendaftaran Kompetisi / Event</div>
          <div class="text-secondary small">Daftarkan team, atlet, dan official ke kompetisi tertentu.</div>
        </div>
        <a class="btn btn-primary btn-sm btn-pill" href="{{ route('manager.club.registration') }}">
          <i class="bi bi-clipboard-check me-1"></i>Daftar sekarang
        </a>
      </div>

      <div class="row g-2 mt-1">
        <div class="col-12 col-md-6">
          <div class="kv">
            <small>Step 1</small>
            Pilih kompetisi → lihat rules & deadline
          </div>
        </div>
        <div class="col-12 col-md-6">
          <div class="kv">
            <small>Step 2</small>
            Daftarkan team → pilih event → pilih atlet/official
          </div>
        </div>
        <div class="col-12 col-md-6">
          <div class="kv">
            <small>Step 3</small>
            Submit entry → tunggu approval (jika ada)
          </div>
        </div>
        <div class="col-12 col-md-6">
          <div class="kv">
            <small>Step 4</small>
            Cetak dokumen / export entry (opsional)
          </div>
        </div>
      </div>

      <hr class="my-3">

      <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
        <div class="text-secondary small">
          Pastikan data atlet (DOB, gender, club/team) lengkap sebelum daftar.
        </div>
        <div class="d-flex gap-2 flex-wrap">
          {{-- <a class="btn btn-outline-secondary btn-sm btn-pill" href="{{ route('cm.entries.index') ?? '#' }}"> --}}
          <a class="btn btn-outline-secondary btn-sm btn-pill" href="#">
            <i class="bi bi-list-check me-1"></i>Riwayat Pendaftaran
          </a>
          {{-- <a class="btn btn-outline-secondary btn-sm btn-pill" href="{{ route('cm.entries.export') ?? '#' }}"> --}}
          <a class="btn btn-outline-secondary btn-sm btn-pill" href="#">
            <i class="bi bi-printer me-1"></i>Export / Print
          </a>
        </div>
      </div>
    </div>
  </div>

  {{-- Right column: open competitions + recent entries --}}
  <div class="col-12 col-lg-5">
    <div class="soft-card p-3 mb-3">
      <div class="d-flex align-items-center justify-content-between mb-2">
        <div class="fw-bold">Kompetisi Tersedia</div>
        {{-- <a href="{{ route('cm.competitions.index') ?? '#' }}" class="text-decoration-none small"> --}}
        <a href="#" class="text-decoration-none small">
          Lihat semua <i class="bi bi-arrow-right"></i>
        </a>
      </div>

      <div class="text-secondary small mb-2">
        Berikut kompetisi yang sedang buka pendaftaran.
      </div>

      <div class="vstack gap-2">
        @forelse(($openCompetitions ?? []) as $c)
          {{-- <a href="{{ route('cm.registration', $c->id ?? null) ?? '#' }}" class="text-decoration-none"> --}}
          <a href="#" class="text-decoration-none">
            <div class="perm-item d-flex align-items-center justify-content-between">
              <div>
                <div class="fw-semibold">{{ $c->name ?? '-' }}</div>
                <div class="text-secondary small">
                  Deadline: {{ $c->deadline_label ?? '-' }}
                </div>
              </div>
              <span class="badge text-bg-light border">{{ $c->code ?? 'OPEN' }}</span>
            </div>
          </a>
        @empty
          <div class="perm-item text-secondary">
            Belum ada kompetisi yang open saat ini.
          </div>
        @endforelse
      </div>
    </div>

    <div class="soft-card p-3">
      <div class="fw-bold mb-2">Aktivitas Terakhir</div>

      <div class="table-responsive">
        <table class="table table-slim align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Tanggal</th>
              <th>Aksi</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            @forelse(($recentEntries ?? []) as $r)
              <tr>
                <td class="text-secondary small">{{ $r->date_label ?? '-' }}</td>
                <td class="small">
                  <div class="fw-semibold">{{ $r->title ?? 'Entry' }}</div>
                  <div class="text-secondary">{{ $r->subtitle ?? '' }}</div>
                </td>
                <td>
                  <span class="badge {{ ($r->status ?? '') === 'approved' ? 'bg-success' : 'bg-warning text-dark' }}">
                    {{ strtoupper($r->status ?? 'pending') }}
                  </span>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="3" class="text-center text-secondary small">Belum ada aktivitas.</td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>
@endsection
