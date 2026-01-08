@extends('layouts.main')

@section('content')
<style>
  .auth-wrap{
    min-height: calc(100vh - 180px); /* kira-kira, menyesuaikan navbar+footer */
    display: flex;
    align-items: center;
  }
  .auth-card{
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 1.25rem;
    overflow: hidden;
    box-shadow: 0 14px 38px rgba(16,24,40,.10);
    background: #fff;
  }
  .auth-hero{
    background:
      radial-gradient(1200px 400px at 10% 0%, rgba(13,110,253,.22), transparent),
      radial-gradient(800px 400px at 100% 0%, rgba(25,135,84,.16), transparent),
      linear-gradient(180deg, #ffffff, #f6f8fb);
    border-bottom: 1px solid rgba(0,0,0,.06);
    padding: 1.25rem 1.25rem 1rem;
  }
  .brand-badge{
    width: 44px; height: 44px;
    border-radius: 14px;
    display:flex; align-items:center; justify-content:center;
    background: rgba(13,110,253,.10);
    border: 1px solid rgba(13,110,253,.18);
    color:#0d6efd;
    box-shadow: 0 10px 20px rgba(16,24,40,.06);
  }
  .form-control, .form-select{
    border-radius: .9rem;
    border-color: rgba(0,0,0,.12);
  }
  .form-control:focus{
    box-shadow: 0 0 0 .2rem rgba(13,110,253,.14);
    border-color: rgba(13,110,253,.35);
  }
  .btn-auth{
    border-radius: .95rem;
    padding: .7rem 1rem;
    font-weight: 700;
  }
  .muted-link{ color:#6c757d; text-decoration:none; }
  .muted-link:hover{ color:#0d6efd; text-decoration:underline; }
</style>

<div class="auth-wrap">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-12 col-sm-10 col-md-7 col-lg-5">
        <div class="auth-card">

          <div class="auth-hero">
            <div class="d-flex align-items-center gap-3">
              <div class="brand-badge">
                <i class="bi bi-water fs-4"></i>
              </div>
              <div>
                <div class="fw-bold fs-5 mb-0">Masuk</div>
                <div class="text-secondary small">Sistem Manajemen Kompetisi Renang</div>
              </div>
            </div>

            @if (session('status'))
              <div class="alert alert-success mt-3 mb-0">
                {{ session('status') }}
              </div>
            @endif

            @if ($errors->any())
              <div class="alert alert-danger mt-3 mb-0">
                <div class="fw-semibold mb-1">Login gagal</div>
                <div class="small">{{ $errors->first() }}</div>
              </div>
            @endif
          </div>

          <div class="p-4">
            <form method="POST" action="{{ route('login') }}">
              @csrf

              <div class="mb-3">
                <label class="form-label small text-secondary">Email</label>
                <div class="input-group">
                  <span class="input-group-text bg-white" style="border-radius:.9rem 0 0 .9rem;">
                    <i class="bi bi-envelope"></i>
                  </span>
                  <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    class="form-control @error('email') is-invalid @enderror"
                    placeholder="contoh: admin@swimcomp.com"
                    autocomplete="email"
                    required
                  >
                </div>
                @error('email')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="mb-2">
                <label class="form-label small text-secondary">Password</label>
                <div class="input-group">
                  <span class="input-group-text bg-white" style="border-radius:.9rem 0 0 .9rem;">
                    <i class="bi bi-lock"></i>
                  </span>
                  <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Masukkan password"
                    autocomplete="current-password"
                    required
                  >
                  <button type="button" class="btn btn-outline-secondary" id="btnTogglePass"
                          style="border-radius:0 .9rem .9rem 0;">
                    <i class="bi bi-eye" id="iconEye"></i>
                  </button>
                </div>
                @error('password')
                  <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
              </div>

              <div class="d-flex align-items-center justify-content-between mt-3">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="remember" id="remember"
                         @checked(old('remember'))>
                  <label class="form-check-label small text-secondary" for="remember">
                    Remember me
                  </label>
                </div>

                {{-- kalau kamu punya route forgot password --}}
                @if (Route::has('password.request'))
                  <a class="muted-link small" href="{{ route('password.request') }}">Lupa password?</a>
                @else
                  <span class="text-secondary small"> </span>
                @endif
              </div>

              <button class="btn btn-primary btn-auth w-100 mt-3" type="submit">
                <i class="bi bi-box-arrow-in-right me-1"></i>Masuk
              </button>

              {{-- Optional: Register --}}
              @if (Route::has('register'))
                <div class="text-center mt-3 small text-secondary">
                  Belum punya akun?
                  <a class="muted-link fw-semibold" href="{{ route('register') }}">Daftar</a>
                </div>
              @endif
            </form>
          </div>

        </div>

        <div class="text-center mt-3 small text-secondary">
          © {{ date('Y') }} SwimComp • Semua hak dilindungi
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
  (function(){
    const pass = document.getElementById('password');
    const btn  = document.getElementById('btnTogglePass');
    const icon = document.getElementById('iconEye');

    if(!pass || !btn) return;

    btn.addEventListener('click', () => {
      const isPwd = pass.getAttribute('type') === 'password';
      pass.setAttribute('type', isPwd ? 'text' : 'password');
      icon.classList.toggle('bi-eye', !isPwd);
      icon.classList.toggle('bi-eye-slash', isPwd);
    });
  })();
</script>
@endpush

@endsection
