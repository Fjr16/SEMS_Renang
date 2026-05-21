{{-- @extends('errors::minimal')

@section('title', __('Forbidden'))
@section('code', '403')
@section('message', __($exception->getMessage() ?: 'Forbidden')) --}}


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Akses Ditolak</title>
    @include('layouts.partials.style')
    @include('layouts.partials.script')
</head>
<body>
    @include('layouts.partials.navbar')

    {{-- Content --}}
    <div class="container py-4">
        <div class="d-flex flex-column align-items-center justify-content-center text-center" style="min-height: 80vh; padding: 2rem;">

            <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center mb-4"
                style="width:72px; height:72px;">
                <i class="bi bi-lock text-primary" style="font-size: 2rem;"></i>
            </div>

            <h1 class="fw-semibold text-primary mb-1" style="font-size: 5rem; line-height:1;">403</h1>
            <h5 class="fw-semibold mb-2">Akses Ditolak</h5>
            <p class="text-muted mb-4" style="max-width: 380px;">
                Anda tidak memiliki izin untuk mengakses halaman ini.
                Hubungi administrator jika Anda merasa ini adalah kesalahan.
            </p>

            <div class="d-flex gap-2">
                <a href="javascript:history.back()" class="btn btn-primary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <a href="{{ url('/') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-house me-1"></i> Beranda
                </a>
            </div>
        </div>
    </div>

    @include('layouts.partials.footer')
</body>
</html>
