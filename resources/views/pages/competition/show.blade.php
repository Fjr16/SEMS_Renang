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
    <div class="tab-pane fade" id="sessions" role="tabpanel" data-table="sessionsTable">
      @include('pages.competition.tabs.sessions')
    </div>

    <!-- Events -->
    <div class="tab-pane fade" id="events" role="tabpanel" data-table="eventsTable"></div>
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

            if (!paneSelected){
              Toast.fire({
                icon:'error',
                title:'Gagal Memuat Data'
              });
              return;
            };

            try {
                if(paneSelected.dataset.loaded === '1') {
                  $('#'+paneSelected.dataset.table).DataTable().ajax.reload(null,false);
                }else{
                  if (paneSelected.id === 'sessions') {
                    getDataSessions();
                    paneSelected.dataset.loaded = '1';
                  }
                }
            } catch (e) {
                paneSelected.innerHTML = '<div class="py-4 text-danger text-center">Gagal memuat konten.</div>';
            }
        });
    </script>

    <script>
      //get data for datatable
      function getDataSessions(){
        $('#sessionsTable').DataTable({
          processing:true,
          serverSide:true,
          ajax:"{{ route('competition.tab.sessions.data', $competition) }}",
          columns:[
            {data:"DT_RowIndex", name:"DT_RowIndex", searchable:false, orderable:false},
            {data:"name", name:"name", className:"text-center", searchable:true, orderable:true},
            {data:"session_date", name:"date", className:"text-center", searchable:true, orderable:true},
            {data:"start_time", name:"start_time", className:"text-center", searchable:true, orderable:true},
            {data:"end_time", name:"end_time", className:"text-center", searchable:true, orderable:true},
            {data:"action", name:"action", className:"text-center dt-actions", searchable:false, orderable:false},
          ],
          order:[[2,'asc']]
        });
      }
      function getDataEvents(){
        $('#eventsTable').DataTable({
          processing:true,
          serverSide:true,
          ajax:"{{ route('competition.tab.events.data', $competition) }}",
          columns:[
            {data:"DT_RowIndex", name:"DT_RowIndex", searchable:false, orderable:false},
            {data:"session_name", name:"session_name", className:"text-center", searchable:true, orderable:true},
            {data:"event_number", name:"event_number", className:"text-center", searchable:true, orderable:true},
            {data:"stroke", name:"stroke", className:"text-center", searchable:true, orderable:true},
            {data:"distance", name:"distance", className:"text-center", searchable:true, orderable:true},
            {data:"genderAttr", name:"genderAttr", className:"text-center", searchable:true, orderable:true},
            {data:"kelompok_umur", name:"kelompok_umur", className:"text-center", searchable:true, orderable:true},
            {data:"event_type", name:"event_type", className:"text-center", searchable:true, orderable:true},
            {data:"event_system", name:"event_system", className:"text-center", searchable:true, orderable:true},
            {data:"remarks", name:"remarks", className:"text-center", searchable:true, orderable:true},
            {data:"min_dob", name:"min_dob", className:"text-center", searchable:true, orderable:true},
            {data:"max_dob", name:"max_dob", className:"text-center", searchable:true, orderable:true},
            {data:"registration_fee", name:"registration_fee", className:"text-center", searchable:true, orderable:true},
            {data:"action", name:"action", className:"text-center dt-actions", searchable:false, orderable:false},
          ],
          order:[[2,'asc']]
        });
      }
    </script>

    <script>
        async function storeAndUpdateGlobal(e, form, tableId = null, modalId = null){
            e.preventDefault();
            const url = form.dataset.url;
            const data = new FormData(form);

            showSpinner();
            try {
                const res = await fetch(url, {
                    method:'POST',
                    headers:{
                        'X-CSRF-TOKEN' : "{{ csrf_token() }}",
                    },
                    body:data,
                });

                if (res.ok) throw new Error('Terjadi Kesalahan Pada server');

                const result = await res.json();
                hideSpinner();

                if (result.status) {
                    if (tableId) $('#'+tableId).ajax.reload(null,false);
                    if (modalId) $('#'+modalId).hide();
                    form.reset;
                    Toast.fire({
                        icon:'success',
                        title:result.message || 'Success'
                    });
                }else{
                    console.log(result.message);
                    Toast.fire({
                        icon:'error',
                        title: result.message || 'Error'
                    });
                }
            } catch (error) {
                hideSpinner();
                console.log(error.message);
                Toast.fire({
                    icon:'error',
                    title:error.message || 'Terjadi Kesalahan'
                });
            }
        }
    </script>
@endpush
