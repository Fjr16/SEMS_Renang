@extends('layouts.main')

@section('content')
<div class="container mt-4">
  <!-- Header -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">Competition: {{ $competition->name ?? '-' }}</h2>
      <p class="text-muted mb-1">Tanggal: {{ Carbon\Carbon::parse($competition->start_date)->translatedFormat('d F Y') . ' - ' . Carbon\Carbon::parse($competition->end_date)->translatedFormat('d F Y') }}</p>
      <p class="text-muted mb-0">Lokasi: {{ $competition->location ?? '-' }}</p>
    </div>
    <div class="mt-3 mt-md-0">
      <a href="{{ route('competition.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
      </a>
    </div>
  </div>

  <!-- Tabs -->
  <ul class="nav nav-tabs" id="competitionTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab" aria-controls="overview" aria-selected="true">Overview</button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="sessions-tab" data-bs-toggle="tab" data-bs-target="#sessions" type="button" role="tab" aria-controls="overview" aria-selected="false">
            Sessions
            <span class="badge bg-secondary">{{ $counts['sessions'] ?? 0 }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab" aria-controls="overview" aria-selected="false">
            Events
            <span class="badge bg-secondary">{{ $counts['events'] ?? 0 }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="entries-tab" data-bs-toggle="tab" data-bs-target="#entries" type="button" role="tab" aria-controls="overview" aria-selected="false">
            Entries
            <span class="badge bg-secondary">{{ $counts['entries'] ?? 0 }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="heats-tab" data-bs-toggle="tab" data-bs-target="#heats" type="button" role="tab" aria-controls="overview" aria-selected="false">
            Heats & Lanes
            <span class="badge bg-secondary">{{ $counts['heats'] ?? 0 }}</span>
        </button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="results-tab" data-bs-toggle="tab" data-bs-target="#results" type="button" role="tab" aria-controls="overview" aria-selected="false">Results</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="points-tab" data-bs-toggle="tab" data-bs-target="#points" type="button" role="tab" aria-controls="overview" aria-selected="false">Team Points</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="officials-tab" data-bs-toggle="tab" data-bs-target="#officials" type="button" role="tab" aria-controls="overview" aria-selected="false">Officials</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab" aria-controls="overview" aria-selected="false">Payments</button>
    </li>
  </ul>

  <!-- Tab Content -->
  <div class="tab-content p-4 border border-top-0 rounded-bottom shadow-sm bg-white" id="competitionTabsContent">

    <!-- Overview -->
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
      <h5 class="fw-bold mb-3">Informasi Kompetisi</h5>
        <p class="mb-1">
            <strong>Dibuat Pada:</strong>
            {{ $competition->created_at->translatedFormat('d F Y') }}
        </p>
        <p class="mb-1">
            <strong>Nama Kompetisi:</strong>
            {{ $competition->name ?? '' }}
        </p>
        <p class="mb-1">
            <strong>Registrasi Mulai:</strong>
            {{ Carbon\Carbon::parse($competition->registration_start)->translatedFormat('d F Y') . ' - ' . Carbon\Carbon::parse($competition->registration_end)->translatedFormat('d F Y') }}
        </p>
        <p class="mb-1">
            <strong>Tanggal Pelaksanaan:</strong>
            {{ Carbon\Carbon::parse($competition->start_date)->translatedFormat('d F Y') . ' - ' . Carbon\Carbon::parse($competition->end_date)->translatedFormat('d F Y') }}
        </p>
        <p class="mb-1">
            <strong>Penyelenggara:</strong>
            {{ $competition->organizer ?? '-' }}
        </p>
        <p class="mb-1">
            <strong>Lokasi:</strong>
            {{ $competition->location ?? '-' }}
        </p>
        <p class="mb-1">
            <strong>Status:</strong>
            <span class="badge {{ $competition->status ? $enumStts::from($competition->status)->class() : 'bg-danger text-white' }}">
                {{ $competition->status ? $enumStts::from($competition->status)->label() : '-' }}
            </span>
        </p>
    </div>

    <!-- Sessions -->
    <div class="tab-pane fade" id="sessions" role="tabpanel" data-url="{{ route('competition.tab.sessions', $competition) }}"></div>
    {{-- <div class="tab-pane fade" id="sessions" role="tabpanel">
      @include('pages.competition.tabs.sessions')
    </div> --}}

    <!-- Events -->
    <div class="tab-pane fade" id="events" role="tabpanel" data-url="{{ route('competition.tab.events', $competition) }}"></div>
    {{-- <div class="tab-pane fade" id="events" role="tabpanel">
      @include('pages.competition.tabs.events')
    </div> --}}

    <!-- Entries -->
    <div class="tab-pane fade" id="entries" role="tabpanel" data-url="{{ route('competition.tab.entries', $competition) }}"></div>
    {{-- <div class="tab-pane fade" id="entries" role="tabpanel">
      @include('pages.competition.tabs.entries')
    </div> --}}

    <!-- Heats -->
    <div class="tab-pane fade" id="heats" role="tabpanel" data-url="{{ route('competition.tab.heats', $competition) }}"></div>
    {{-- <div class="tab-pane fade" id="heats" role="tabpanel">
      @include('pages.competition.tabs.heats')
    </div> --}}

    <!-- Results -->
    <div class="tab-pane fade" id="results" role="tabpanel" data-url="{{ route('competition.tab.results', $competition) }}"></div>
    {{-- <div class="tab-pane fade" id="results" role="tabpanel">
      @include('pages.competition.tabs.results')
    </div> --}}

    <!-- Team Points -->
    <div class="tab-pane fade" id="points" role="tabpanel" data-url="{{ route('competition.tab.points', $competition) }}"></div>
    {{-- <div class="tab-pane fade" id="points" role="tabpanel">
      @include('pages.competition.tabs.points')
    </div> --}}

    <!-- Officials -->
    <div class="tab-pane fade" id="officials" role="tabpanel" data-url="{{ route('competition.tab.officials', $competition) }}"></div>
    {{-- <div class="tab-pane fade" id="officials" role="tabpanel">
      @include('pages.competition.tabs.officials')
    </div> --}}

    <!-- Payments -->
    <div class="tab-pane fade" id="payments" role="tabpanel" data-url="{{ route('competition.tab.payments', $competition) }}"></div>
    {{-- <div class="tab-pane fade" id="payments" role="tabpanel">
      @include('pages.competition.tabs.payments')
    </div> --}}

  </div>
</div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('shown.bs.tab', async function(ev) {
            const paneSelector = ev.target.getAttribute('data-bs-target');
            if (paneSelector === '#overview') return;
            const paneSelected = document.querySelector(paneSelector);

            if (!paneSelected || paneSelected.dataset.loaded === '1') return;

            const url = paneSelected.dataset.url;
            if (!url){
                Toast.fire({
                    icon:'error',
                    title:'Data source Not Found'
                });
                return;
            }

            showSpinner();

             try {
                const res = await fetch(url);
                paneSelected.innerHTML = await res.text();
                paneSelected.dataset.loaded = '1';

                // contoh: inisialisasi DataTables/JS khusus tab di sini
                // if (pane.id === 'entries') initEntriesTable();
            } catch (e) {
                paneSelected.innerHTML = '<div class="py-4 text-danger text-center">Gagal memuat konten.</div>';
            }
            hideSpinner();
        });
    </script>
@endpush
