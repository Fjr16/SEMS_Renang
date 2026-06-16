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
          <button data-bs-toggle="modal" data-bs-target="#modalUser" class="btn btn-primary btn-sm btn-pill">
            <i class="bi bi-pencil me-1"></i>Edit Profil
          </buttin>
          <button data-bs-toggle="modal" data-bs-target="#modal-ubah-pw" class="btn btn-outline-secondary btn-sm btn-pill">
            <i class="bi bi-lock me-1"></i>Ubah Password
          </button>
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

  {{-- modal ubah password --}}
  <div class="modal fade" id="modal-ubah-pw" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="form-ubah-pw">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Ubah Password</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row">
                <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
                <div class="mb-3">
                    <label class="form-label" for="name">Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="password"  id="password" placeholder="Masukkan password" autocomplete="current-password" required>
                        <button type="button" class="btn btn-outline-secondary" id="btnTogglePass">
                            <i class="bi bi-eye" id="iconEye"></i>
                        </button>
                        <div class="invalid-feedback"></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="mb-3">
                    <label class="form-label" for="name">Konfirmasi Password</label>
                    <div class="input-group">
                        <input type="password" class="form-control" name="password_confirm" placeholder="Konfirmasi password disini" autocomplete="current-password"  id="password_confirm" required>
                        <button type="button" class="btn btn-outline-secondary" id="btnTogglePassConfirm">
                            <i class="bi bi-eye" id="iconEyeConfirm"></i>
                        </button>
                        <div class="invalid-feedback">
                            <small class="fst-italic">* Password tidak sama</small>
                        </div>
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>
  {{-- modal edit profile --}}
  <div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="formUser">
          <input type="hidden" name="user_id" id="user_id" value="{{ $user->id }}">
          <div class="modal-header">
            <h5 class="modal-title" id="userTitle">Edit Profile</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" name="user_name" id="user_name" value="{{ $user->name ?? '' }}" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" id="user_email" value="{{ $user->email }}" required>
            </div>
            @if($hasOrg)
                <div class="mb-3">
                  <label class="form-label">Organisasi</label>
                  <input type="text" class="form-control" value="{{ $user->organization->name ?? '' }}" disabled>
                </div>
            @endif

            @if($hasClub)
            <div class="mb-3">
                <label class="form-label">Klub</label>
                <input type="text" class="form-control" value="{{ $user->club->club_name ?? '' }}" disabled>
            </div>
            @endif
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

@endsection

@push('scripts')
    <script>
        $('#btnTogglePass').on('click', function(){
            const input   = $('#password');
            const isHidden = input.attr('type') === 'password';

            input.attr('type', isHidden ? 'text' : 'password');
            $('#iconEye')
                .toggleClass('bi-eye',        !isHidden)
                .toggleClass('bi-eye-slash',   isHidden);
        });
        $('#btnTogglePassConfirm').on('click', function(){
            const input   = $('#password_confirm');
            const isHidden = input.attr('type') === 'password';

            input.attr('type', isHidden ? 'text' : 'password');
            $('#iconEyeConfirm')
                .toggleClass('bi-eye',        !isHidden)
                .toggleClass('bi-eye-slash',   isHidden);
        });

        $('#password').on('input', function(){
            let pw = $(this).val();
            let errors = [];

            if(pw.length < 8){
                errors.push ("Minimal 8 karakter");
            }
            if(!/[a-zA-Z]/.test(pw)){
                errors.push ("Harus mengandung huruf");
            }
            if(!/[A-Z]/.test(pw)){
                errors.push ("Harus mengandung huruf besar");
            }
            if(!/[0-9]/.test(pw)){
                errors.push ("Harus mengandung angka");
            }
            if(!/[^a-zA-Z0-9]/.test(pw)){
                errors.push ("Harus mengandung simbol (!@#$% dll)");
            }

            if(errors.length > 0){
                $(this).addClass('is-invalid');
                $(this).siblings('.invalid-feedback')
                .html(errors.map(e => `<small class="d-block fst-italic"> * ${e}</small>`).join(''));
            }else{
                $(this).removeClass('is-invalid');
                $(this).siblings('.invalid-feedback').html('');
            }
        });
        $('#password_confirm').on('input', function(){
            let newPw = $('#password').val();
            let confirmPw = $(this).val();

            if(newPw !== confirmPw){
                $(this).addClass('is-invalid');
            }else{
                $(this).removeClass('is-invalid');
            }
        });

        $('#form-ubah-pw').on('submit', async function (e) {
            e.preventDefault();

            const url = "{{ route('user.update-password') }}";

            try {
                const res  = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept':       'application/json',
                    },
                    body: new FormData(this),
                });
                const data = await res.json();

                if (!res.ok || !data.status) {
                    if (data.errors) {
                        const msg = Object.values(data.errors).flat().join('\n');
                        Toast.fire({ icon: 'error', title: msg || 'Terjadi kesalahan' });
                    } else {
                        Toast.fire({ icon: 'error', title: data.message || 'Terjadi kesalahan' });
                    }
                    return;
                }

                Toast.fire({ icon: 'success', title: data.message || 'Password berhasil diubah' });
                $('#modal-ubah-pw').modal('hide');
            } catch (error) {
                console.error(error);
                Toast.fire({ icon: 'error', title: 'Gagal update password.' });
            }
        });

        $('#formUser').on('submit', async function (e) {
            e.preventDefault();

            const url = "{{ route('user.update-profile') }}";

            try {
                const res  = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept':       'application/json',
                    },
                    body: new FormData(this),
                });
                const data = await res.json();

                if (!res.ok || !data.status) {
                    if (data.errors) {
                        const msg = Object.values(data.errors).flat().join('\n');
                        Toast.fire({ icon: 'error', title: msg || 'Terjadi kesalahan' });
                    } else {
                        Toast.fire({ icon: 'error', title: data.message || 'Terjadi kesalahan' });
                    }
                    return;
                }

                Toast.fire({ icon: 'success', title: data.message || 'profile berhasil diubah' });
                $('#modalUser').modal('hide');
                setTimeout(() => {
                    window.location.reload();
                }, 100);
            } catch (error) {
                console.error(error);
                Toast.fire({ icon: 'error', title: 'Gagal update profile.' });
            }
        });
    </script>
@endpush
