@extends('layouts.main')

@section('content')
<div class="container mt-4">
  <!-- Header -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">Competition: Summer Swim Cup 2025</h2>
      <p class="text-muted mb-0">Tanggal: 15 - 17 Juli 2025 | Lokasi: Jakarta Aquatic Stadium</p>
    </div>
    <div class="mt-3 mt-md-0">
      <a href="{{ route('competition') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
      </a>
    </div>
  </div>

  <!-- Tabs -->
  <ul class="nav nav-tabs" id="competitionTabs" role="tablist">
    <li class="nav-item" role="presentation">
      <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">Overview</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="events-tab" data-bs-toggle="tab" data-bs-target="#events" type="button" role="tab">Events</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="entries-tab" data-bs-toggle="tab" data-bs-target="#entries" type="button" role="tab">Entries</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="heats-tab" data-bs-toggle="tab" data-bs-target="#heats" type="button" role="tab">Heats & Lanes</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="results-tab" data-bs-toggle="tab" data-bs-target="#results" type="button" role="tab">Results</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="points-tab" data-bs-toggle="tab" data-bs-target="#points" type="button" role="tab">Team Points</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="officials-tab" data-bs-toggle="tab" data-bs-target="#officials" type="button" role="tab">Officials</button>
    </li>
    <li class="nav-item" role="presentation">
      <button class="nav-link" id="payments-tab" data-bs-toggle="tab" data-bs-target="#payments" type="button" role="tab">Payments</button>
    </li>
  </ul>

  <!-- Tab Content -->
  <div class="tab-content p-4 border border-top-0 rounded-bottom shadow-sm bg-white" id="competitionTabsContent">

    <!-- Overview -->
    <div class="tab-pane fade show active" id="overview" role="tabpanel">
      <h5 class="fw-bold">Informasi Kompetisi</h5>
      <p><strong>Nama:</strong> Summer Swim Cup 2025</p>
      <p><strong>Tanggal:</strong> 15 - 17 Juli 2025</p>
      <p><strong>Lokasi:</strong> Jakarta Aquatic Stadium</p>
      <p><strong>Keterangan:</strong> Kompetisi tingkat nasional untuk kategori junior & senior</p>
    </div>

    <!-- Events -->
    <div class="tab-pane fade" id="events" role="tabpanel">
      @include('pages.competition.tabs.events')
    </div>

    <!-- Entries -->
    <div class="tab-pane fade" id="entries" role="tabpanel">
      @include('pages.competition.tabs.entries')
    </div>

    <!-- Heats -->
    <div class="tab-pane fade" id="heats" role="tabpanel">
      @include('pages.competition.tabs.heats')
    </div>

    <!-- Results -->
    <div class="tab-pane fade" id="results" role="tabpanel">
      @include('pages.competition.tabs.results')
    </div>

    <!-- Team Points -->
    <div class="tab-pane fade" id="points" role="tabpanel">
      @include('pages.competition.tabs.points')
    </div>

    <!-- Officials -->
    <div class="tab-pane fade" id="officials" role="tabpanel">
      @include('pages.competition.tabs.officials')
    </div>

    <!-- Payments -->
    <div class="tab-pane fade" id="payments" role="tabpanel">
      @include('pages.competition.tabs.payments')
    </div>

  </div>
</div>
@endsection
