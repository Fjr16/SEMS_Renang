@extends('layouts.main')

@section('content')
<div class="container mt-4">
  <!-- Header -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <div class="d-flex gap-2">
            <h2 class="fw-bold mb-1">
                Kompetisi: {{ $competition->name ?? '-' }}
            </h2>
            {{-- <span class="badge {{ $competition->status ? $enumStts::from($competition->status)->class() : 'bg-danger text-white' }}">
                {{ $competition->status ? $enumStts::from($competition->status)->label() : '-' }}
            </span> --}}
        </div>
      <p class="text-muted mb-1">Tanggal: {{ Carbon\Carbon::parse($competition->start_date)->translatedFormat('d F Y') . ' - ' . Carbon\Carbon::parse($competition->end_date)->translatedFormat('d F Y') }}</p>
      <p class="text-muted mb-0">Arena: {{ '['.($competition->venue?->code ?? '-').'] ' . ($competition->venue?->name ?? '-') }}</p>
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
            {{ $competition->organization?->name ?? '-' }}
        </p>
        <p class="mb-1">
            <strong>Arena:</strong>
            {{ '['.($competition->venue?->code ?? '-').'] ' . ($competition->venue?->name ?? '-') }}
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
    <div class="tab-pane fade" id="events" role="tabpanel" data-table="eventsTable">
      @include('pages.competition.tabs.events')
    </div>

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
  </div>
</div>
@endsection

@push('scripts')
    <script>
        const EVENTS_PARTIAL_URL = "{{ route('competition.tab.events.partial', $competition) }}";

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
                    if(paneSelected.id === 'events'){
                        // Reload hanya konten tab events
                        paneSelected.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';

                        fetch(EVENTS_PARTIAL_URL)
                            .then(r => r.text())
                            .then(html => {
                                paneSelected.innerHTML = html;
                                paneSelected.dataset.loaded = '1';
                                initEventTabScripts();
                            })
                            .catch(() => {
                                paneSelected.innerHTML = '<div class="py-4 text-danger text-center">Gagal memuat konten.</div>';
                            });
                    }else{
                        $('#'+paneSelected.dataset.table).DataTable().ajax.reload(null,false);
                    }
                }else{
                    if (paneSelected.id === 'sessions') {
                        getDataSessions();
                        paneSelected.dataset.loaded = '1';
                    }else if(paneSelected.id === 'events'){
                        paneSelected.dataset.loaded = '1';
                        initEventTabScripts();
                    }
                }
            } catch (e) {
              console.log(e.message);
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
            {data:"action", name:"action", className:"text-center dt-actions", searchable:false, orderable:false},
            {data:"name", name:"name", className:"text-center", searchable:true, orderable:true},
            {data:"session_date", name:"session_date", className:"text-center", searchable:true, orderable:true},
            {data:"desc_pool", name:"desc_pool", className:"text-center", searchable:true, orderable:true},
            {data:"session_order", name:"session_order", className:"text-center", searchable:true, orderable:true},
          ],
          order:[[3,'asc']]
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

                if (!res.ok) throw new Error('Terjadi Kesalahan Pada server');

                const result = await res.json();
                hideSpinner();

                if (result.status) {
                    if (tableId) $('#'+tableId).DataTable().ajax.reload(null,false);
                    if (modalId) $('#'+modalId).modal('hide');
                    form.reset();
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

        async function destroyGlobal(element) {
            const { isConfirmed } = await Swal.fire({
                title: "Konfirmasi Hapus",
                text: "Apakah anda yakin menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                cancelButtonText:'Batal',
                confirmButtonText: "Ya, lanjutkan!"
            });

            if(!isConfirmed) return;

            const tableId = '#'+element.dataset.table;
            const url = element.dataset.url;
            try {
                const res = await fetch(url, {
                    method:'DELETE',
                    headers: {
                        'X-CSRF-TOKEN' : '{{ csrf_token() }}',
                    }
                });
                if (!res.ok) {
                    throw new Error("Terjadi Kesalahan Server");
                }
                const result = await res.json();

                if (result.status) {
                    Toast.fire({
                        icon:'success',
                        title:result.message || 'Berhasil hapus data'
                    });
                }else{
                    Toast.fire({
                        icon:'error',
                        title:result.message ?? 'Gagal Hapus Data'
                    });
                }
                $(tableId).DataTable().ajax.reload();
            } catch (error) {
                console.log(error.message);
                Toast.fire({
                    icon:'error',
                    title:error.message || 'Terjadi kesalahan server'
                });
                $(tableId).DataTable().ajax.reload();
            }
        }
    </script>
@endpush
