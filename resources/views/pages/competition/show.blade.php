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
    <div class="tab-pane fade" id="events" role="tabpanel" data-table="eventsTable"></div>
    <!-- Entries -->
    <div class="tab-pane fade" id="entries" role="tabpanel"></div>

    <!-- Heats -->
    {{-- <div class="tab-pane fade" id="heats" role="tabpanel" data-url="{{ route('competition.tab.heats', $competition) }}"></div> --}}
    {{-- <div class="tab-pane fade" id="heats" role="tabpanel">
      @include('pages.competition.tabs.heats')
    </div> --}}
  </div>
</div>
@endsection

@push('scripts')
    <script>
        const EVENTS_PARTIAL_URL = "{{ route('competition.tab.events.partial', $competition) }}";
        const ENTRIES_PARTIAL_URL = "{{ route('competition.tab.entries.partial', $competition) }}";

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
                if(paneSelected.id === 'events'){
                    // Reload hanya konten tab events
                    paneSelected.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';

                    fetch(EVENTS_PARTIAL_URL)
                        .then(r => r.text())
                        .then(html => {
                            paneSelected.innerHTML = html;
                            initEventTabScripts();
                        })
                        .catch(err => {
                            console.error('Fetch error:', err.message);
                            paneSelected.innerHTML = '<div class="py-4 text-danger text-center">Gagal memuat konten.</div>';
                        });
                }else if(paneSelected.id === 'entries'){
                    // Reload hanya konten tab entries
                    paneSelected.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
                    fetchPartialEntriesTab();
                }else if(paneSelected.id === 'sessions'){
                    getDataSessions();
                }else{
                    $('#'+paneSelected.dataset.table).DataTable().ajax.reload(null,false);
                }
            } catch (e) {
                console.log(e.message);
                paneSelected.innerHTML = '<div class="py-4 text-danger text-center">Gagal memuat konten.</div>';
            }
        });
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

    {{-- scripts untuk tab sessions --}}
    <script>
        function getDataSessions(){
            if($.fn.DataTable.isDataTable('#sessionsTable') ) {
                $('#sessionsTable').DataTable().ajax.reload(null, false);
            }else{
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
        }
        async function editSession(element) {
            const tableId = '#'+element.dataset.table;
            const modalId = '#'+element.dataset.modal;

            const form = document.getElementById(element.dataset.form);
            form.reset();

            const tr = $(element).closest('tr');
            const data = $(tableId).DataTable().row(tr).data();

            $('#competition_session_id').val(data.id);
            $('#name').val(data.name);
            $('#session_date').flatpickr().setDate(data.session_date);
            $('#pool_id').val(data.pool_id);
            $('#session_order').val(data.session_order);

            $(modalId).modal('show');
        }

        function resetSessionForm(){
            let form = document.getElementById('sessionForm');
            form.reset();

            document.getElementById('competition_session_id').value = '';
        }
    </script>

    {{-- scripts untuk tab events --}}
    <script>
        const EVENTS_URL_STORE   = "{{ route('competition.tab.events.store', $competition) }}";
        const EVENTS_URL_EDIT    = "{{ route('competition.tab.events.edit', [$competition, ':event']) }}".replace(':event', '');
        const EVENTS_URL_UPDATE  = "{{ route('competition.tab.events.update', [$competition, ':event']) }}".replace(':event', '');
        const EVENTS_URL_DESTROY = "{{ route('competition.tab.events.destroy', [$competition, ':event']) }}".replace(':event', '');
        const ESTAFET_VALUE  = "{{ \App\Enums\EventType::estafet->value }}";

        // ── Open create modal ────────────────────────────────────────────────
        function openCreateEvent() {
            document.getElementById('eventForm').reset();
            document.getElementById('competition_event_id').value = '';
            document.getElementById('modalEventTitle').textContent = 'Tambah Event';
            document.getElementById('max_relay_athletes').disabled = true;

            document.getElementById('event_type').dispatchEvent(new Event('change'));
        }
        // ── Toggle Sesi (fix) ──────────────────────────────────────────────────────
        function toggleSessionGroup(btn) {
            const body    = btn.nextElementSibling;
            const chevron = btn.querySelector('.session-chevron');
            const isOpen  = body.style.display !== 'none';
            body.style.display = isOpen ? 'none' : 'block';
            chevron.classList.toggle('rotated', isOpen);
        }
        // ── Helper: update stat cards (fix) ────────────────────────────────────────
        function refreshStatCards() {
            const rows    = document.querySelectorAll('#sessionContainer tr.event-row[data-id]');
            const total   = rows.length;
            let   estafet = 0;
            rows.forEach(r => { if (r.dataset.tipe === ESTAFET_VALUE) estafet++; });

            const statEls = document.querySelectorAll('.stat-value');
            if (statEls[0]) statEls[0].textContent = total;
            if (statEls[2]) statEls[2].textContent = estafet;
            if (statEls[3]) statEls[3].textContent = total - estafet;
        }
        // ── Helper: update per-session count badge (fix) ───────────────────────────
        function refreshSessionCount(sessionId) {
            const card = document.querySelector(`.session-group-card[data-session-id="${sessionId}"]`);
            if (!card) return;
            const count = card.querySelectorAll('tr.event-row[data-id]').length;
            const badge = card.querySelector('.session-visible-count');
            if (badge) badge.textContent = count;

            // Tampilkan/sembunyikan empty state
            const empty = card.querySelector('.session-empty-row');
            if (empty) empty.style.display = count === 0 ? '' : 'none';
        }
        // ── Edit Event (existing, unchanged) ────────────────────────────────
        async function editEvent(eventId) {
            document.getElementById('modalEventTitle').textContent = 'Edit Event';
            try {
                const res  = await fetch(`${EVENTS_URL_EDIT}${eventId}`, {
                    method:'GET',
                    headers: { 'Accept': 'application/json'},
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    Toast.fire({
                        icon:'error',
                        title:data.message || 'Data tidak ditemukan'
                    });
                    return;
                }

                const ev = data.event;
                const modal = document.getElementById('modalEvent');

                modal.querySelector('#eventForm').reset();
                modal.querySelector('#competition_event_id').value   = ev.id;
                modal.querySelector('#competition_session_id').value = ev.competition_session_id;
                modal.querySelector('#stroke').value                 = ev.stroke;
                modal.querySelector('#distance').value               = ev.distance;
                modal.querySelector('#gender').value                 = ev.gender;
                modal.querySelector('#age_group_id').value           = ev.age_group_id;
                modal.querySelector('#event_type').value             = ev.event_type;
                modal.querySelector('#registration_fee').value       = toNum(ev.registration_fee);

                const maxEl = modal.querySelector('#max_relay_athletes');
                maxEl.disabled = ev.event_type !== ESTAFET_VALUE;
                maxEl.value    = ev.max_relay_athletes ?? '';

                new bootstrap.Modal(modal).show();

            } catch (err) {
                console.error(err);
                Toast.fire({
                    icon:'error',
                    title:'Gagal mengambil data event'
                });
            }
        }
        // ── Delete ───────────────────────────────────────────────────────────
        let _deleteTargetId = null;
        function confirmDeleteEvent(eventId, eventNo) {
            _deleteTargetId = eventId;
            document.getElementById('deleteEventNo').textContent = eventNo;
            new bootstrap.Modal(document.getElementById('modalDeleteEvent')).show();
        }

        async function executeDeleteEvent() {
            if (!_deleteTargetId) return;

            const btn = document.getElementById('deleteEventConfirmBtn');
            btn.disabled    = true;
            btn.textContent = 'Menghapus...';

            try {
                const res  = await fetch(`${EVENTS_URL_DESTROY}${_deleteTargetId}`, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' },
                });
                const data = await res.json();

                if (!res.ok || !data.success) {
                    Toast.fire({
                        icon:'error',
                        title:data.message || 'Gagal menghapus'
                    });
                    return;
                }

                // Hapus semua elemen DOM dengan data-id ini (desktop tr + mobile div)
                document.querySelectorAll(`.event-row[data-id="${_deleteTargetId}"]`)
                    .forEach(el => el.remove());

                refreshSessionCount(data.session_id);
                refreshStatCards();

                bootstrap.Modal.getInstance(document.getElementById('modalDeleteEvent'))?.hide();
                Toast.fire({
                    icon:'success',
                    title:data.message || 'Terjadi Kesalahan'
                });
                _deleteTargetId = null;

            } catch (err) {
                console.error(err);
                Toast.fire({
                    icon:'error',
                    title:'Gagal menghapus event'
                });
            } finally {
                btn.disabled    = false;
                btn.textContent = 'Hapus';
            }
        }

        // ── Filter & Search ──────────────────────────────────────────────────
        function applyEventFilter() {
            const q      = document.getElementById('eventSearchInput').value.toLowerCase().trim();
            const gender = document.getElementById('filterGender').value;
            const tipe   = document.getElementById('filterTipe').value;
            let   total  = 0;

            document.querySelectorAll('#sessionContainer .event-row').forEach(row => {
                const match =
                    (
                        !q || row.dataset.nomor.toLowerCase().includes(q)
                        || row.dataset.gaya.toLowerCase().includes(q)
                        || row.dataset.session.toLowerCase().includes(q)
                    ) &&
                    (!gender || row.dataset.kelamin === gender) &&
                    (!tipe || row.dataset.tipe === tipe);

                row.style.display = match ? '' : 'none';
                if (match) total++;
            });

            // Update per-session count badge
            document.querySelectorAll('.session-group-card').forEach(card => {
                const visible = card.querySelectorAll('.event-row[data-id]:not([style*="display: none"])').length;
                const badge   = card.querySelector('.session-visible-count');
                if (badge) badge.textContent = visible;
            });

            document.getElementById('eventEmptyFilter').classList.toggle('d-none', total > 0);
        }

        function resetEventFilter() {
            document.getElementById('eventSearchInput').value = '';
            document.getElementById('filterGender').value = '';
            document.getElementById('filterTipe').value = '';
            applyEventFilter();
        }

        function initEventTabScripts() {
            // ── Relay toggle (existing) (fix) ──────────────────────────────────────────
            document.getElementById('event_type').addEventListener('change', function () {
                const maxEl = document.getElementById('max_relay_athletes');
                maxEl.disabled = this.value !== ESTAFET_VALUE;
                if (this.value !== ESTAFET_VALUE) maxEl.value = '';
            });

            // ── SUBMIT FORM (Create & Update) ────────────────────────────────────
            document.getElementById('eventForm').addEventListener('submit', async function (e) {
                e.preventDefault();

                const eventId   = document.getElementById('competition_event_id').value;
                const isEdit    = !!eventId;
                const url = isEdit ? `${EVENTS_URL_UPDATE}${eventId}` : EVENTS_URL_STORE
                const formData  = new FormData(this);
                if (isEdit) formData.append('_method', 'PUT');

                const submitBtn = document.getElementById('eventSubmitBtn');
                submitBtn.disabled    = true;
                submitBtn.textContent = 'Menyimpan...';

                try {
                    const res  = await fetch(url, {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}", 'Accept': 'application/json' },
                        body: formData,
                    });
                    const data = await res.json();

                    if (!res.ok || !data.success) {
                        // Tampilkan error validasi jika ada
                        if (data.errors) {
                            const msg = Object.values(data.errors).flat().join('\n');
                            Toast.fire({
                                icon:'error',
                                title:msg || 'Terjadi kesalahan'
                            });
                        } else {
                            Toast.fire({
                                icon:'error',
                                title:data.message || 'Terjadi kesalahan'
                            });
                        }
                        return;
                    }

                    const sessionId = data.session_id;
                    const oldSessionId  = document.querySelector(`tr.event-row[data-id="${eventId}"]`)
                                    ?.closest('.session-group-card')
                                    ?.dataset.sessionId;
                    const card = document.querySelector(`.session-group-card[data-session-id="${sessionId}"]`);

                    if (card) {
                        const wrapper     = document.createElement('table');
                        wrapper.innerHTML = `<tbody>${data.row_html}</tbody>`;
                        const newTr       = wrapper.querySelector('tr.event-row');
                        const tbody       = card.querySelector('tbody.event-rows-desktop'); // ← fix selector

                        if (isEdit) {
                            const sesiPindah = oldSessionId && oldSessionId !== String(sessionId);
                            if (sesiPindah) {
                                // Hapus row dari sesi LAMA
                                const oldCard  = document.querySelector(`.session-group-card[data-session-id="${oldSessionId}"]`);
                                const oldTbody = oldCard?.querySelector('tbody.event-rows-desktop');
                                oldTbody?.querySelector(`tr.event-row[data-id="${eventId}"]`)?.remove();
                                refreshSessionCount(oldSessionId);

                                // Tambahkan ke sesi BARU
                                tbody?.querySelector('.session-empty-row')?.remove();
                                tbody?.appendChild(newTr);
                            } else {
                                // Sesi sama, replace saja
                                tbody?.querySelector(`tr.event-row[data-id="${eventId}"]`)?.replaceWith(newTr);
                            }
                        } else {
                            tbody?.querySelector('.session-empty-row')?.remove(); // hapus empty state
                            tbody?.appendChild(newTr);
                        }

                        refreshSessionCount(sessionId);
                    }

                    refreshStatCards();
                    // Tutup modal & reset form
                    bootstrap.Modal.getInstance(document.getElementById('modalEvent'))?.hide();
                    this.reset();
                    document.getElementById('competition_event_id').value = '';
                    document.getElementById('modalEventTitle').textContent = 'Tambah Event';

                    Toast.fire({
                        icon:'success',
                        title:data.message || 'success'
                    });

                } catch (err) {
                    console.error(err);
                    Toast.fire({
                        icon:'error',
                        title:'Gagal menyimpan event. Periksa koneksi.'
                    });
                } finally {
                    submitBtn.disabled    = false;
                    submitBtn.textContent = 'Simpan';
                }
            });

            document.getElementById('eventSearchInput').addEventListener('input', applyEventFilter);
            document.getElementById('filterGender').addEventListener('change', applyEventFilter);
            document.getElementById('filterTipe').addEventListener('change', applyEventFilter);
        }
    </script>

    {{-- scripts tab entries --}}
    <script>
        let currentEntryId = null;
        /* ── Group toggle collapse ── */
        function toggleGroup(teamId) {
            const body   = document.getElementById('team-body-' + teamId);
            const chev   = document.getElementById('chevron-' + teamId);
            const isOpen = body.style.display !== 'none';
            body.style.display = isOpen ? 'none' : '';
            chev.style.transform = isOpen ? '' : 'rotate(90deg)';
        }
        function toggleRelayMembers(entryId) {
            const row = document.getElementById('relay-members-' + entryId);
            row.style.display = row.style.display === 'none' ? '' : 'none';
        }

        async function confirmEntry(competitionTeamId, payment_stts, stts){
            const rejectedEnumValue = "{{ App\Enums\CompetitionTeamStatus::Rejected->value }}";
            const activeEnumValue = "{{ App\Enums\CompetitionTeamStatus::Active->value }}";
            const paidValue = "{{ App\Enums\CompetitionTeamPaymentStatus::Paid->value }}";
            const unpaidValue = "{{ App\Enums\CompetitionTeamPaymentStatus::Unpaid->value }}";
            const paidLabel = "{{ App\Enums\CompetitionTeamPaymentStatus::Paid->label() }}";
            const unpaidLabel = "{{ App\Enums\CompetitionTeamPaymentStatus::Unpaid->label() }}";

            let isConfirmed = false;
            let message = null;
            let paymentStatus = null;
            if(stts === rejectedEnumValue){
                const result = await Swal.fire({
                    input: "textarea",
                    inputLabel: "Pesan / Catatan",
                    inputPlaceholder: "Jelaskan alasan penolakan disini...",
                    inputAttributes: { "aria-label": "Jelaskan alasan penolakan disini" },
                    showCancelButton: true,
                    confirmButtonText:'Kirim'
                });
                isConfirmed = result.isConfirmed;
                message = result.value;
            }else if(stts === activeEnumValue){
                const inputOptions = new Promise((resolve) => {
                    resolve({
                        [unpaidValue]:unpaidLabel,
                        [paidValue]: paidLabel
                    });
                });
                const result = await Swal.fire({
                    title: "Yakin Menerima Entry ?",
                    icon: "warning",
                    input: "radio",
                    inputOptions,
                    inputValue: payment_stts,
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, lanjutkan!",
                    cancelButtonText: "Batal"
                });
                isConfirmed = result.isConfirmed;
                paymentStatus = result.value;
            }else{
                const result = await Swal.fire({
                    title: "Yakin Ingin Membatalkan atau Diskualifikasi Entry ?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, lanjutkan!",
                    cancelButtonText: "Batal"
                });
                isConfirmed = result.isConfirmed;
            }
            if(isConfirmed){
                showSpinner();
                try {
                    const res = await fetch("{{ route('competition.tab.entries.verification', $competition ) }}", {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                        body: JSON.stringify({
                            competition_team_id : competitionTeamId,
                            notes : message ?? null,
                            status : stts,
                            payment_status: paymentStatus
                        })
                    });
                    if(!res.ok) throw new Error('Terjadi kesalahan pada server, coba lagi beberapa saat');

                    const result = await res.json();
                    hideSpinner();

                    if(result.errors) console.log(result.errors);
                    if(!result.status) throw new Error(result.message || 'Proses verifikasi entry gagal');
                    fetchPartialEntriesTab();
                    Toast.fire({
                        icon:'success',
                        title:result.message || 'Proses verifikasi entry berhasil'
                    });
                } catch (error) {
                    hideSpinner();
                    Toast.fire({
                        icon:'error',
                        title:error.message ||'Proses verifikasi entry gagal'
                    });
                }
            }
        }

        /* ── Delete entry ── */
        async function deleteEntry(entryId) {
            const result = await Swal.fire({
                title: "Yakin ingin menghapus entry ?",
                text: "Entry tidak bisa dikembalikan jika telah dihapus",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, lanjutkan!",
                cancelButtonText: "Batal"
            });
            if(result.isConfirmed){
                showSpinner();
                const url = "{{ route('competition.tab.entries.deleteEntry', ['competition' => $competition, 'id' => 'entry_id'] ) }}".replace('entry_id', entryId);
                try {
                    const res = await fetch(url, {
                        method: 'DELETE',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                    });
                    if(!res.ok) throw new Error('Terjadi kesalahan pada server, coba lagi beberapa saat');

                    const result = await res.json();
                    hideSpinner();

                    if(!result.status) throw new Error(result.message || 'Proses hapus entry gagal');
                    fetchPartialEntriesTab();
                    Toast.fire({
                        icon:'success',
                        title:result.message || 'Proses hapus entry berhasil'
                    });
                } catch (error) {
                    hideSpinner();
                    Toast.fire({
                        icon:'error',
                        title:error.message ||'Proses hapus entry gagal'
                    });
                }
            };
        }
        /* ── update status entry ── */
        async function updateStatusEntry(entryId, stts) {
            const result = await Swal.fire({
                title: "Yakin ingin mengubah status entry ?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya, lanjutkan!",
                cancelButtonText: "Batal"
            });
            if(result.isConfirmed){
                showSpinner();
                const url = "{{ route('competition.tab.entries.updateStatusEntry', $competition ) }}";
                try {
                    const res = await fetch(url, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
                        body:JSON.stringify({
                            competition_entry_id:entryId,
                            status:stts
                        })
                    });
                    if(!res.ok) throw new Error('Terjadi kesalahan pada server, coba lagi beberapa saat');

                    const result = await res.json();
                    hideSpinner();

                    if(!result.status) throw new Error(result.message || 'Proses update status entry gagal');
                    fetchPartialEntriesTab();
                    Toast.fire({
                        icon:'success',
                        title:result.message || 'Proses update status entry berhasil'
                    });
                } catch (error) {
                    hideSpinner();
                    Toast.fire({
                        icon:'error',
                        title:error.message ||'Proses update status entry gagal'
                    });
                }
            };
        }

        // switch mini tab
        function switchTab(teamId, tab, btnEl) {
            // Reset semua tab button dalam group ini
            document.querySelectorAll(`#team-group-${teamId} .tab-btn`).forEach(btn => {
                btn.style.borderBottom = '2px solid transparent';
                btn.style.color = '#6c757d';
            });
            // Aktifkan tab yang dipilih
            btnEl.style.borderBottom = '2px solid #2563EB';
            btnEl.style.color = '#2563EB';

            // Tampilkan/sembunyikan konten
            document.getElementById(`tab-entry-${teamId}`).style.display    = tab === 'entry'    ? '' : 'none';
            document.getElementById(`tab-official-${teamId}`).style.display = tab === 'official' ? '' : 'none';
        }

        function initEntryTabScripts(){
            $(document).off('change', '#f-status, #club_id');
            $(document).off('input', '.seed_time_input');
            $(document).off('keydown', '.seed_time_input');

            const clubSelect = $('#club_id').select2({
                width:'100%',
                placeholder:'Filter Klub',
                allowClear:true,
                minimumInputLength:0,
                ajax:{
                    url:"{{ route('getClubByCategory') }}",
                    dataType:'json',
                    delay:250,
                    data:function(params){
                        return {
                            q:params.term || '',
                            page:params.page || 1,
                        };
                    },
                    processResults:function(res,params){
                        params.page = params.page || 1;

                        return {
                            results:(res.data || []).map(row => ({
                                id:row.id,
                                text:`[${row.club_code ?? ''}] ${row.club_name ?? ''}`
                            })),
                            pagination:{
                                more:res.pagination?.more || false
                            }
                        };
                    },
                    cache:true,
                },
                templateResult:function(item){
                    if (item.loading) return item.text;
                    return item.text;
                },
                templateSelection:function(item){
                    return item.text || item.id;
                }
            });

            $(document).on('input', '.seed_time_input', function(){
                let digits = this.value.replace(/\D/g, '');

                digits = digits.slice(0, 6);

                let formatted = '';

                if (digits.length <= 2) {
                    formatted = digits;
                } else if (digits.length <= 4) {
                    formatted = digits.slice(0, 2) + ':' + digits.slice(2);
                } else {
                    formatted = digits.slice(0, 2) + ':' + digits.slice(2, 4) + '.' + digits.slice(4);
                }

                this.value = formatted;
            });
            $(document).on('keydown', '.seed_time_input', async function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();

                    const value = $(this).val();
                    const entryId = $(this).data('entry-id');
                    console.log(value,entryId);

                    showSpinner();
                    try {
                        const res = await fetch("{{ route('competition.tab.entries.updateSeedTime', $competition) }}", {
                            method:'POST',
                            headers:{
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN' : "{{ csrf_token() }}"
                            },
                            body:JSON.stringify({
                                competition_entry_id:entryId,
                                seed_time:value
                            })
                        });
                        if(!res.ok) throw new Error("Terjadi Kesalahan pada server, coba lagi beberapa saat !!");

                        const result = await res.json();
                        hideSpinner();

                        if(!result.status) throw new Error(result.message || 'Proses simpan seed time gagal');

                        Toast.fire({
                            icon:'success',
                            title:result.message || 'Proses simpan seed time berhasil'
                        });
                    } catch (error) {
                        hideSpinner();
                        Toast.fire({
                            icon:'error',
                            title:error.message || 'Proses simpan seed time gagal'
                        });
                    }

                }
            });

            // filter
            $(document).on('change', '#f-status, #club_id', () => fetchPartialEntriesTab());
        }

        function fetchPartialEntriesTab(){
            const tab = document.getElementById('entries');
            const status = document.getElementById('f-status')?.value ?? '';
            const clubId = document.getElementById('club_id')?.value ?? '';

            const url = new URL(ENTRIES_PARTIAL_URL);
            if (status) url.searchParams.set('status', status);
            if (clubId)  url.searchParams.set('club_id', clubId);

            tab.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';

            fetch(url)
                .then(r => r.text())
                .then(html => {
                    tab.innerHTML = html;
                    initEntryTabScripts();
                })
                .catch(err => {
                    console.error('Fetch error:', err.message);
                    tab.innerHTML = '<div class="py-4 text-danger text-center">Gagal memuat konten.</div>';
                });
        }
    </script>
@endpush
