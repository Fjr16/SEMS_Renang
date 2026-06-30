<style>
  .nav-auth{
    display:flex;
    align-items:center;
    gap:.5rem;
  }

  .nav-auth .btn{
    border-radius: 999px;
    padding: .42rem .85rem;
    font-weight: 700;
    line-height: 1;
    display:inline-flex;
    align-items:center;
    gap:.45rem;
    box-shadow: 0 10px 22px rgba(0,0,0,.10);
    transition: transform .12s ease, box-shadow .12s ease, background-color .12s ease, border-color .12s ease;
  }
  .nav-auth .btn:hover{
    transform: translateY(-1px);
    box-shadow: 0 14px 30px rgba(0,0,0,.14);
  }

  .btn-nav-login{
    background: rgba(255,255,255,.96);
    border: 1px solid rgba(255,255,255,.55);
    color: #0d6efd;
  }
  .btn-nav-login:hover{
    background: #fff;
    border-color: rgba(255,255,255,.85);
    color: #0b5ed7;
  }

  .btn-nav-register{
    background: rgba(255,255,255,.10);
    border: 1px solid rgba(255,255,255,.35);
    color: #fff;
    backdrop-filter: blur(8px);
  }
  .btn-nav-register:hover{
    background: rgba(255,255,255,.18);
    border-color: rgba(255,255,255,.55);
    color: #fff;
  }

  .navbar-profile-link {
    display:flex;
    align-items:center;
    gap:.4rem;
    padding: .4rem .6rem;
    border-radius: 8px;
    transition: background .15s ease;
  }
  .navbar-profile-link:hover {
    background: rgba(255,255,255,.1);
  }

  /* mobile auth area: di luar collapse, rata kanan */
  .navbar-auth-mobile {
    display:flex;
    align-items:center;
    gap:.5rem;
    margin-left:auto;
  }

  @media (max-width: 576px){
    .nav-auth .btn span{ display:none; }
    .nav-auth .btn{ padding:.45rem .65rem; }
  }
</style>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('dashboard') }}">
            🏊 <span class="ms-2">SwimComp</span>
        </a>

        <!-- Auth MOBILE: di luar collapse, hanya tampil < lg -->
        <div class="navbar-auth-mobile d-lg-none">
            @auth
            <div class="dropdown">
                <a class="navbar-profile-link text-white text-decoration-none dropdown-toggle"
                   href="#" id="navbarProfileMobile" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-person-circle" style="font-size:20px;"></i>
                    <span class="d-none d-sm-inline fw-semi-bold">{{ Auth::user()->name }}</span>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" aria-labelledby="navbarProfileMobile">
                    <li><a class="dropdown-item" href="{{ route('user.profile', encrypt(auth()->user()->id)) }}"><i class="bi bi-person-circle me-2"></i>Profil Saya</a></li>
                    <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-2"></i>Logout
                            </button>
                        </form>
                    </li>
                </ul>
            </div>
            @else
            <div class="nav-auth">
                <a href="{{ route('login') }}" class="btn btn-nav-login btn-sm">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Login</span>
                </a>
            </div>
            @endauth
        </div>

        <button class="navbar-toggler ms-2 d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="d-flex flex-column flex-lg-row w-100 align-items-lg-center justify-content-between">
                <ul class="navbar-nav mx-lg-auto order-2 order-lg-1 my-3 my-lg-0 gap-lg-2">
                    <li class="nav-item"><a class="nav-link {{ Route::is('guest.competition*') ? 'active fw-semibold' : '' }}" href="{{ route('guest.competition.index') }}">Kompetisi</a></li>
                    <li class="nav-item"><a class="nav-link {{ Route::is('guest.atlet*') ? 'active fw-semibold' : '' }} " href="{{ route('guest.atlet.index') }}">Atlet</a></li>
                    @can('Tim Saya.Dashboard')
                    <li class="nav-item"><a class="nav-link {{ Route::is('manager.club*') ? 'active fw-semibold' : '' }} " href="{{ route('manager.club.dashboard') }}">Tim Saya</a></li>
                    @endcan
                    @canany([
                        'Master Setting.Klub-List',
                        'Master Setting.Atlet-List',
                        'Master Setting.Official-List',
                        'Master Setting.Kompetisi-List',
                        'Master Setting.Lokasi & Kolam-List',
                        'Master Setting.Kelompok Umur-List',
                        'Master Setting.Event-List',
                        'Master Setting.Organisasi-List',
                        'Master Setting.User Hak Akses-List',
                    ])
                    <li class="nav-item"><a class="nav-link {{ request()->is('master*') ? 'active fw-semibold' : '' }}" href="{{ route('master.setting.index') }}">Master Setting</a></li>
                    @endcanany
                </ul>

                <!-- Auth DESKTOP: di dalam collapse, hanya tampil >= lg -->
                <div class="d-none d-lg-flex align-items-center order-1 order-lg-2 ms-lg-3 gap-3">
                    @auth
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle"
                           href="#" id="navbarProfile" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-circle me-1" style="font-size:18px;"></i>
                            <span class="fw-semi-bold">{{ Auth::user()->name }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" aria-labelledby="navbarProfile">
                            <li><a class="dropdown-item" href="{{ route('user.profile', encrypt(auth()->user()->id)) }}"><i class="bi bi-person-circle me-2"></i>Profil Saya</a></li>
                            <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @else
                    <div class="nav-auth">
                        <a href="{{ route('login') }}" class="btn btn-nav-login btn-sm">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Login</span>
                        </a>
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</nav>
