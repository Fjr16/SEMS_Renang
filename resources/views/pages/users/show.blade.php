@extends('layouts.main')

@section('content')

  <style>
    :root {
      --surface: #ffffff;
      --muted:   #6c757d;
      --ring:    rgba(13,110,253,.12);
    }

    .page-hero {
      background: radial-gradient(1200px 400px at 10% 0%, rgba(13,110,253,.18), transparent),
                  radial-gradient(800px 400px at 100% 0%, rgba(25,135,84,.14), transparent),
                  linear-gradient(180deg, #ffffff, #f6f8fb);
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 1rem;
      box-shadow: 0 10px 30px rgba(16,24,40,.06);
      overflow: hidden;
    }

    .profile-cover {
      height: 84px;
      background: linear-gradient(90deg, rgba(13,110,253,.22), rgba(13,110,253,.07));
    }

    .profile-avatar {
      width: 68px; height: 68px;
      border-radius: 16px;
      border: 3px solid #fff;
      box-shadow: 0 8px 20px rgba(0,0,0,.12);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.4rem;
      font-weight: 700;
      letter-spacing: 1px;
      margin-top: -34px;
      flex-shrink: 0;
      background: rgba(13,110,253,.12);
      color: #0d6efd;
    }

    .profile-avatar.is-org   { background: rgba(25,135,84,.12);  color: #198754; }
    .profile-avatar.is-club  { background: rgba(255,193,7,.18);  color: #a17a00; }
    .profile-avatar.is-admin { background: rgba(13,110,253,.12); color: #0d6efd; }

    .kvs {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
      gap: .6rem;
    }

    .kv {
      padding: .6rem .75rem;
      border: 1px dashed rgba(0,0,0,.10);
      border-radius: .75rem;
      background: rgba(0,0,0,.015);
    }
    .kv small { color: var(--muted); font-size: .75rem; display: block; margin-bottom: 2px; }
    .kv span  { font-size: .9rem; font-weight: 500; color: #111827; }

    .info-card {
      background: var(--surface);
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 1rem;
      box-shadow: 0 4px 16px rgba(16,24,40,.04);
      overflow: hidden;
    }

    .info-card-header {
      padding: .8rem 1.25rem;
      border-bottom: 1px solid rgba(0,0,0,.06);
      display: flex;
      align-items: center;
      gap: .5rem;
      font-weight: 600;
      font-size: .9rem;
    }
    .info-card-header i { font-size: 1rem; color: #0d6efd; }

    .rel-card {
      display: flex;
      align-items: center;
      gap: .85rem;
      padding: 1rem 1.25rem;
    }

    .rel-icon {
      width: 48px; height: 48px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.3rem;
      flex-shrink: 0;
    }
    .rel-icon.org  { background: rgba(25,135,84,.12);  color: #198754; }
    .rel-icon.club { background: rgba(255,193,7,.15);  color: #a17a00; }

    .rel-tag {
      display: inline-flex;
      align-items: center;
      gap: 4px;
      padding: 2px 8px;
      border-radius: 6px;
      font-size: .75rem;
      background: rgba(0,0,0,.04);
      color: var(--muted);
      border: 1px solid rgba(0,0,0,.07);
    }

    .badge-soft         { background: rgba(13,110,253,.10); color: #0d6efd; border: 1px solid rgba(13,110,253,.25); font-weight: 600; }
    .badge-success-soft { background: rgba(25,135,84,.10);  color: #198754; border: 1px solid rgba(25,135,84,.25);  font-weight: 600; }
    .badge-warning-soft { background: rgba(255,193,7,.15);  color: #a17a00; border: 1px solid rgba(255,193,7,.35);  font-weight: 600; }

    .btn-pill { border-radius: 999px !important; padding-left: .9rem; padding-right: .9rem; }

    .empty-rel {
      padding: 2rem 1.25rem;
      text-align: center;
      color: var(--muted);
      font-size: .88rem;
    }
  </style>

  {{-- Breadcrumb --}}
  <nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb mb-0" style="font-size:.85rem">
      <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
      <li class="breadcrumb-item active">Profil Pengguna</li>
    </ol>
  </nav>

  @php
    $user     = auth()->user();
    $initials = strtoupper(implode('', array_map(fn($w) => $w[0], explode(' ', trim($user->name ?? 'U')))));
    $initials = substr($initials, 0, 2);

    $hasOrg  = (bool) $user->organization_id;
    $hasClub = (bool) $user->club_id;

    $avatarClass = match(true) {
        $hasOrg  => 'is-org',
        $hasClub => 'is-club',
        default  => 'is-admin',
    };

    $roleName = ucfirst($user->getRoleNames()->first() ?? 'User');
  @endphp

  {{-- ===== HERO ===== --}}
  <div class="page-hero mb-4">
    <div class="profile-cover"></div>

    <div class="px-3 px-md-4 pb-4">
      <div class="d-flex flex-wrap align-items-start justify-content-between gap-2">

        <div class="d-flex align-items-end gap-3">
          <div class="profile-avatar {{ $avatarClass }}">{{ $initials }}</div>
          <div class="pb-1">
            <div class="d-flex align-items-center gap-2 flex-wrap">
              <span class="fw-bold" style="font-size:1.1rem">{{ $user->name }}</span>

              @if($hasOrg)
                <span class="badge rounded-pill badge-success-soft">
                  <i class="bi bi-building me-1"></i>Penyelenggara
                </span>
              @elseif($hasClub)
                <span class="badge rounded-pill badge-warning-soft">
                  <i class="bi bi-people me-1"></i>Manajer Klub
                </span>
              @else
                <span class="badge rounded-pill badge-soft">
                  <i class="bi bi-shield-check me-1"></i>{{ $roleName }}
                </span>
              @endif

              <span class="badge rounded-pill badge-success-soft">
                <i class="bi bi-circle-fill me-1" style="font-size:.45rem"></i>Aktif
              </span>
            </div>
            <div class="text-secondary small mt-1">
              <i class="bi bi-envelope me-1"></i>{{ $user->email }}
            </div>
          </div>
        </div>

        <div class="d-flex gap-2 flex-wrap" style="padding-top:.5rem">
          {{-- <a href="{{ route('profile.edit') }}" class="btn btn-primary btn-sm btn-pill"> --}}
          <a href="" class="btn btn-primary btn-sm btn-pill">
            <i class="bi bi-pencil me-1"></i>Edit Profil
          </a>
          {{-- <a href="{{ route('profile.password') }}" class="btn btn-outline-secondary btn-sm btn-pill"> --}}
          <a href="" class="btn btn-outline-secondary btn-sm btn-pill">
            <i class="bi bi-lock me-1"></i>Ubah Password
          </a>
          <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm btn-pill">
            <i class="bi bi-arrow-left me-1"></i>Kembali
          </a>
        </div>
      </div>

      {{-- KV --}}
      <div class="kvs mt-4">
        <div class="kv">
          <small><i class="bi bi-person-badge me-1"></i>ID Pengguna</small>
          <span>#{{ $user->id }}</span>
        </div>
        <div class="kv">
          <small><i class="bi bi-key me-1"></i>Role</small>
          <span>{{ $roleName }}</span>
        </div>
        <div class="kv">
          <small><i class="bi bi-calendar3 me-1"></i>Bergabung</small>
          <span>{{ $user->created_at?->translatedFormat('d F Y') ?? '-' }}</span>
        </div>
        <div class="kv">
          <small><i class="bi bi-pencil-square me-1"></i>Terakhir diperbarui</small>
          <span>{{ $user->updated_at?->translatedFormat('d F Y') ?? '-' }}</span>
        </div>
      </div>
    </div>
  </div>

  {{-- ===== RELASI (hanya tampil jika ada org atau klub) ===== --}}
  @if($hasOrg || $hasClub)
    <div class="row g-3">

      {{-- Organisasi --}}
      @if($hasOrg)
      <div class="{{ $hasOrg && $hasClub ? 'col-md-6' : 'col-12' }}">
        <div class="info-card">
          <div class="info-card-header">
            <i class="bi bi-building"></i>
            Organisasi
          </div>

          @if($user->organization)
            <div class="rel-card">
              <div class="rel-icon org">
                <i class="bi bi-building"></i>
              </div>
              <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold text-truncate">{{ $user->organization->name ?? '-' }}</div>
                {{-- @if($user->organization->description ?? null)
                  <div class="text-secondary small mt-1">{{ $user->organization->description }}</div>
                @endif --}}
                {{-- <div class="d-flex gap-2 flex-wrap mt-2">
                  @if($user->organization->created_at ?? null)
                    <span class="rel-tag"><i class="bi bi-hash"></i>{{ $user->organization->created_at->translatedFormat('d F Y') }}</span>
                  @endif
                  @if($user->organization->updated_at ?? null)
                    <span class="rel-tag"><i class="bi bi-geo-alt"></i>{{ $user->organization->updated_at->translatedFormat('d F Y') }}</span>
                  @endif
                </div> --}}
              </div>
            </div>
          @else
            <div class="empty-rel">
              <i class="bi bi-building fs-2 d-block mb-2 opacity-25"></i>
              Data organisasi tidak ditemukan
            </div>
          @endif
        </div>
      </div>
      @endif

      @if($hasClub)
      <div class="{{ $hasOrg && $hasClub ? 'col-md-6' : 'col-12' }}">
        <div class="info-card">
          <div class="info-card-header">
            <i class="bi bi-people" style="color:#a17a00"></i>
            Klub
          </div>

          @if($user->club)
            <div class="rel-card">
              @if($user->club->club_logo)
                <img src="{{ Storage::url($user->club->club_logo) }}"
                     alt="{{ $user->club->club_name }}"
                     style="width:48px;height:48px;border-radius:12px;object-fit:cover;flex-shrink:0;border:1px solid rgba(0,0,0,.08)">
              @else
                <div class="rel-icon club">
                  <i class="bi bi-people"></i>
                </div>
              @endif
              <div class="flex-grow-1 min-w-0">
                <div class="fw-semibold text-truncate">{{ $user->club->club_name ?? '-' }}</div>
                @if($user->club->club_lead ?? null)
                  <div class="text-secondary small mt-1">
                    <i class="bi bi-person me-1"></i>{{ $user->club->club_lead }}
                    @if($user->club->lead_phone ?? null)
                      &nbsp;·&nbsp;<i class="bi bi-telephone me-1"></i>{{ $user->club->lead_phone }}
                    @endif
                  </div>
                @endif
                <div class="d-flex gap-2 flex-wrap mt-2">
                  @if($user->club->club_code ?? null)
                    <span class="rel-tag"><i class="bi bi-hash"></i>{{ $user->club->club_code }}</span>
                  @endif
                  @if($user->club->team_type ?? null)
                    <span class="rel-tag"><i class="bi bi-tag"></i>{{ $user->club->team_type }}</span>
                  @endif
                  @if($user->club->club_city ?? null)
                    <span class="rel-tag"><i class="bi bi-geo-alt"></i>{{ $user->club->club_city }}</span>
                  @endif
                  @if($user->club->club_province ?? null)
                    <span class="rel-tag"><i class="bi bi-map"></i>{{ $user->club->club_province }}</span>
                  @endif
                </div>
              </div>
            </div>
          @else
            <div class="empty-rel">
              <i class="bi bi-people fs-2 d-block mb-2 opacity-25"></i>
              Data klub tidak ditemukan
            </div>
          @endif
        </div>
      </div>
      @endif

    </div>
  @endif

@endsection
