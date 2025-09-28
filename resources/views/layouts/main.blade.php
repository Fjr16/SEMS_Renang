<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sistem Manajemen Kompetisi Renang</title>
  @include('layouts.partials.style')
</head>
<body class="mt-auto d-flex flex-column min-vh-100">
    <!-- Tambahkan di body (global spinner) -->
    <div id="loadingSpinner" class="d-none position-fixed top-0 start-0 w-100 h-100 bg-dark bg-opacity-50 d-flex justify-content-center align-items-center" style="z-index: 2000;">
        <div class="spinner-border text-light" role="status" style="width: 3rem; height: 3rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>

    <!-- Navbar -->
    @include('layouts.partials.navbar')
  

  <!-- Konten -->
  <div class="container py-4">
    @yield('content')
  </div>

    <!-- Footer -->
    @include('layouts.partials.footer')

    @include('layouts.partials.script')
</body>
</html>
