<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('dashboard') }}">
            🏊 <span class="ms-2">SwimComp</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <div class="d-flex flex-column flex-lg-row w-100 align-items-lg-center justify-content-between">
                <ul class="navbar-nav mx-lg-auto order-2 order-lg-1 my-3 my-lg-0 gap-lg-2">
                    <li class="nav-item"><a class="nav-link {{ Route::is('guest.atlet*') ? 'active fw-semibold' : '' }} " href="{{ route('guest.atlet.index') }}">Atlet</a></li>
                    {{-- <li class="nav-item"><a class="nav-link {{ Route::is('official*') ? 'active fw-semibold' : '' }} " href="{{ route('official.index') }}">Official</a></li> --}}
                    <li class="nav-item"><a class="nav-link {{ Route::is('club*') ? 'active fw-semibold' : '' }}" href="{{ route('club.index') }}">Klub</a></li>
                    <li class="nav-item"><a class="nav-link {{ Route::is('competition*') ? 'active fw-semibold' : '' }}" href="{{ route('competition.index') }}">Kompetisi</a></li>
                    {{-- <li class="nav-item"><a class="nav-link {{ Route::is('startlist*') ? 'active fw-semibold' : '' }}" href="{{ route('startlist') }}">Startlist</a></li>
                    <li class="nav-item"><a class="nav-link {{ Route::is('results*') ? 'active fw-semibold' : '' }}" href="{{ route('results') }}">Hasil</a></li>
                    <li class="nav-item"><a class="nav-link {{ Route::is('entries*') ? 'active fw-semibold' : '' }}" href="{{ route('entries') }}">Entries</a></li>
                    <li class="nav-item"><a class="nav-link {{ Route::is('heats*') ? 'active fw-semibold' : '' }}" href="{{ route('heats') }}">Heats</a></li> --}}
                    {{-- <li class="nav-item"><a class="nav-link {{ Route::is('users*') ? 'active fw-semibold' : '' }}" href="{{ route('users') }}">Manajemen User</a></li> --}}
                    <li class="nav-item"><a class="nav-link {{ request()->is('master*') ? 'active fw-semibold' : '' }}" href="{{ route('master.setting.index') }}">Master Setting</a></li>
                </ul>

                <!-- Kanan: Toggle + Profile -->
                <div class="d-flex align-items-center order-1 order-lg-2 ms-lg-3 gap-3">
                    <!-- Dark Mode Switch -->
                    <div class="darkmode-toggle">
                        <input type="checkbox" class="btn-check" id="darkModeToggle" autocomplete="off">
                        <label class="btn btn-outline-light btn-sm rounded-pill px-3 d-flex align-items-center gap-2" for="darkModeToggle">
                        <i class="bi bi-brightness-high-fill"></i>
                        <i class="bi bi-moon-stars-fill"></i>
                        </label>
                    </div>

                    <!-- Profile Dropdown -->
                    <div class="dropdown">
                        <a class="d-flex align-items-center text-white text-decoration-none dropdown-toggle" href="#" id="navbarProfile"
                        role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle me-1" style="font-size:18px;"></i>
                        <span class="d-none fw-semi-bold d-md-inline">{{ Auth::user()->name ?? 'Pengguna' }}</span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3" aria-labelledby="navbarProfile">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person-circle me-2"></i>Profil Saya</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Pengaturan</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="">
                            @csrf
                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Logout</button>
                            </form>
                        </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>
