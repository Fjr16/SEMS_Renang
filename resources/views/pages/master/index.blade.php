@extends('layouts.main')

@push('css')
    <style>
        .setting-card{
            border: 1px solid rgba(0,0,0,.06);
            transition: transform .15s ease, box-shadow .15s ease, border-color .15s ease;
            cursor: pointer;
        }
        .setting-card:hover{
            transform: translateY(-2px);
            box-shadow: 0 .75rem 1.5rem rgba(0,0,0,.08);
            border-color: rgba(13,110,253,.25);
        }
        .setting-icon{
            width: 44px; height: 44px;
            display: inline-flex; align-items: center; justify-content: center;
            border-radius: 12px;
            background: rgba(13,110,253,.08);
            color: #0d6efd;
        }
        .setting-card .chev{
            opacity: .65;
            transition: transform .15s ease, opacity .15s ease;
        }
        .setting-card:hover .chev{
            transform: translateX(2px);
            opacity: 1;
        }
    </style>
@endpush
@section('content')
    {{-- Master Setting Header --}}
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
        <div>
            <h2 class="fw-bold mb-1">Master Setting</h2>
            <p class="text-muted mb-0">Kelola data master yang digunakan pada sistem</p>
        </div>
    </div>
    @php
    $cards = [
        [
        'title' => 'Lokasi & Kolam',
        'desc'  => 'Kelola venue dan pool renang',
        'icon'  => 'bi-geo-alt',
        'route' => route('master.venue.pools.index'), // ganti
        ],
        [
        'title' => 'Atlet',
        'desc'  => 'Kelola data atlet terdaftar',
        'icon'  => 'bi-person-badge',
        'route' => route('atlet.index'), // ganti
        ],
        [
        'title' => 'Klub',
        'desc'  => 'Kelola data klub & tim',
        'icon'  => 'bi-people',
        'route' => route('club.index'), // ganti
        ],
        [
        'title' => 'Kompetisi',
        'desc'  => 'Daftar & kelola kompetisi',
        'icon'  => 'bi-trophy',
        'route' => route('competition.index'), // ganti
        ],
    ];
    @endphp

    <div class="row g-3">
    @foreach($cards as $c)
        <div class="col-12 col-md-6 col-xl-4">
        <a href="{{ $c['route'] }}" class="text-decoration-none text-reset">
            <div class="card setting-card h-100 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-start justify-content-between gap-3">
                <div class="d-flex align-items-start gap-3">
                    <div class="setting-icon">
                    <i class="bi {{ $c['icon'] }} fs-5"></i>
                    </div>
                    <div>
                    <div class="fw-semibold fs-5 mb-1">{{ $c['title'] }}</div>
                    <div class="text-muted small">{{ $c['desc'] }}</div>
                    </div>
                </div>
                <i class="bi bi-chevron-right chev mt-1"></i>
                </div>

                <hr class="my-3">

                <div class="d-flex align-items-center justify-content-between">
                <span class="badge text-bg-light border">Buka Pengaturan</span>
                <span class="text-primary small fw-semibold">Klik untuk masuk</span>
                </div>
            </div>
            </div>
        </a>
        </div>
    @endforeach
    </div>
@endsection
