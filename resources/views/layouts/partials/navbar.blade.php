<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">🏊 SwimComp</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link {{ Route::is('atlet*') ? 'active' : '' }} " href="{{ route('atlet') }}">Atlet</a></li>
            <li class="nav-item"><a class="nav-link {{ Route::is('club*') ? 'active' : '' }}" href="{{ route('club') }}">Klub</a></li>
            <li class="nav-item"><a class="nav-link {{ Route::is('competition*') ? 'active' : '' }}" href="{{ route('competition') }}">Kompetisi</a></li>
            <li class="nav-item"><a class="nav-link {{ Route::is('startlist*') ? 'active' : '' }}" href="{{ route('startlist') }}">Startlist</a></li>
            <li class="nav-item"><a class="nav-link {{ Route::is('results*') ? 'active' : '' }}" href="{{ route('results') }}">Hasil</a></li>
        </ul>
        
        <!--start switch mode -->
        <!-- Dark Mode Switch -->
        <div class="darkmode-toggle d-flex justify-content-lg-end ms-lg-3 mt-2 mt-lg-0">
            <input type="checkbox" class="btn-check" id="darkModeToggle" autocomplete="off">
            <label class="btn btn-outline-light btn-sm d-flex align-items-center gap-2" for="darkModeToggle">
                <i class="bi bi-brightness-high-fill"></i>
                <i class="bi bi-moon-stars-fill"></i>
            </label>
        </div>
        <!--end switch mode -->
        </div>
    </div>
</nav>