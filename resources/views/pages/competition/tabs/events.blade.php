<!-- Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
        <h5 class="fw-bold mb-1">Daftar Events</h5>
        <p class="text-muted mb-0">Kelola event yang ada dalam kompetisi ini</p>
    </div>
    <div class="mt-3 mt-md-0">
        <button data-bs-toggle="modal" data-bs-target="#modalEvent" onclick="openCreateEvent()"
            class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Tambah Event
        </button>
    </div>
</div>

<!-- ── Stat Cards ── -->
@php
    $totalEvents     = $competition->events->count();
    $totalSesi       = $competition->sessions->count();
    $totalEstafet    = $competition->events->where('event_type', \App\Enums\EventType::estafet->value)->count();
    $totalPerorangan = $totalEvents - $totalEstafet;
@endphp
<div class="row g-3 mb-4">
    @foreach([
        ['label' => 'Total Events',  'value' => $totalEvents,     'color' => '#4f46e5', 'icon' => 'bi-trophy'],
        ['label' => 'Total Sesi',    'value' => $totalSesi,       'color' => '#7c3aed', 'icon' => 'bi-calendar3'],
        ['label' => 'Estafet',       'value' => $totalEstafet,    'color' => '#d97706', 'icon' => 'bi-people-fill'],
        ['label' => 'Perorangan',    'value' => $totalPerorangan, 'color' => '#059669', 'icon' => 'bi-person-fill'],
    ] as $stat)
    <div class="col-6 col-md-3">
        <div class="card border-0 shadow-sm h-100" style="border-radius:14px">
            <div class="card-body d-flex align-items-center gap-3 py-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0"
                     style="width:40px;height:40px;background:{{ $stat['color'] }}18">
                    <i class="bi {{ $stat['icon'] }}" style="color:{{ $stat['color'] }};font-size:18px"></i>
                </div>
                <div>
                    <div class="fw-bold stat-value" style="font-size:1.3rem;color:{{ $stat['color'] }};font-family:monospace;line-height:1">
                        {{ $stat['value'] }}
                    </div>
                    <div class="text-muted" style="font-size:12px;font-weight:500">{{ $stat['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- ── Filter Bar ── -->
<div class="card border-0 shadow-sm mb-4" style="border-radius:14px">
    <div class="card-body py-3">
        <div class="row g-2 align-items-center">
            <div class="col-12 col-sm-5 col-lg-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="eventSearchInput" class="form-control border-start-0 ps-0"
                           placeholder="Cari nomor, gaya, sesi...">
                </div>
            </div>
            <div class="col-6 col-sm-3 col-lg-2">
                <select id="filterGender" class="form-select form-select-sm">
                    <option value="">Semua Kelamin</option>
                    @foreach($enumGender as $g)
                        <option value="{{ $g->value }}">{{ $g->label() }}</option>
                    @endforeach
                    <option value="mixed">Campuran</option>
                </select>
            </div>
            <div class="col-6 col-sm-3 col-lg-2">
                <select id="filterTipe" class="form-select form-select-sm">
                    <option value="">Semua Tipe</option>
                    @foreach($enumEType as $t)
                        <option value="{{ $t->value }}">{{ $t->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-sm-auto ms-sm-auto">
                <button onclick="resetEventFilter()" class="btn btn-sm btn-dark w-100 w-sm-auto">
                    <i class="bi bi-x-circle me-1"></i>Reset
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ── Sessions Grouped ── -->
<div id="sessionContainer" class="d-flex flex-column gap-3">
    @forelse($competition->sessions as $sesi)
    @php $sesiEvents = $competition->events->where('competition_session_id', $sesi->id); @endphp

    <div class="card border-0 shadow-sm session-group-card" data-session-id="{{ $sesi->id }}" data-session="{{ strtolower($sesi->name) }}" style="border-radius:16px;overflow:hidden">
        <!-- Session Header -->
        <div class="session-toggle-btn d-flex align-items-center justify-content-between px-4 py-3"
             onclick="toggleSessionGroup(this)" role="button"
             style="background:#f8fafc;border-bottom:1px solid #f1f5f9;cursor:pointer;user-select:none">
            <div class="d-flex align-items-center gap-3">
                <div style="width:4px;height:24px;background:#4f46e5;border-radius:2px;flex-shrink:0"></div>
                <div>
                    <p class="fw-semibold text-dark mb-0" style="font-size:14px">{{ $sesi->name }}</p>
                    <p class="text-muted mb-0" style="font-size:12px">
                        {{ \Carbon\Carbon::parse($sesi->session_date)->translatedFormat('d F Y') }}
                        &middot;
                        <span class="session-visible-count">{{ $sesiEvents->count() }}</span> event
                    </p>
                </div>
            </div>
            <i class="bi bi-chevron-down text-muted session-chevron" style="transition:transform 0.2s"></i>
        </div>

        <!-- Session Body -->
        <div class="session-group-body">

            <!-- DESKTOP TABLE (≥768px) -->
            <div class="table-responsive">
                <table class="table table-borderless align-middle mb-0" style="font-size:13px">
                    <thead style="background:#f8fafc">
                        <tr>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">#</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">No. Event</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Gaya</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Jarak</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Kelamin</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Kelompok</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Tipe</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold text-center" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Maks.</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold text-end" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Biaya</th>
                            <th class="px-4 py-3 text-uppercase fw-semibold text-center" style="font-size:11px;color:#9ca3af;letter-spacing:.05em">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="event-rows-desktop">
                        @forelse($sesiEvents as $i => $event)
                            @include('pages.competition.tabs._event_row', [
                                'event'       => $event,
                                'sesi'        => $sesi,
                                'index'       => $i,
                                'competition' => $competition,
                            ])
                        @empty
                            <tr class="session-empty-row">
                                <td colspan="10" class="text-center text-muted py-4" style="font-size:13px">
                                    Belum ada event di sesi ini
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @empty
    <div class="text-center py-5">
        <i class="bi bi-calendar-x text-muted" style="font-size:3rem"></i>
        <p class="text-muted mt-3 mb-1 fw-semibold">Belum ada sesi</p>
        <p class="text-muted" style="font-size:13px">Tambahkan sesi terlebih dahulu sebelum membuat event</p>
    </div>
    @endforelse
</div>

<!-- Empty Filter State -->
<div id="eventEmptyFilter" class="text-center py-5 d-none">
    <i class="bi bi-search text-muted" style="font-size:2.5rem"></i>
    <p class="fw-semibold text-muted mt-3 mb-1">Tidak ada event yang cocok</p>
    <p class="text-muted" style="font-size:13px">Coba ubah kata kunci atau filter</p>
</div>

<div class="modal fade" id="modalEvent" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form id="eventForm">
        <div class="modal-header">
          <h5 class="modal-title" id="modalEventTitle">Tambah Event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row g-3">
                <input type="hidden" id="competition_event_id" name="competition_event_id">
                <div class="col-md-4 col-12">
                    <label class="form-label">Kompetisi</label>
                    <input type="text" class="form-control" id="competition_name"
                           value="{{ $competition->name ?? '' }}" disabled>
                    <input type="hidden" value="{{ $competition->id ?? '' }}" name="competition_id" id="competition_id">
                </div>
                <div class="col-md-8 col-12">
                    <label class="form-label">Sesi Perlombaan</label>
                    <select name="competition_session_id" id="competition_session_id" class="form-control">
                        @foreach ($competition->sessions as $sesi)
                            <option value="{{ $sesi->id }}" @selected(old('competition_session_id') == $sesi->id)>
                                {{ ($sesi->session_date ?? '-/-') . ' [' . ($sesi->name ?? '-') . ']' }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-12">
                    <label class="form-label">Gaya Perlombaan</label>
                    <select class="form-control" id="stroke" name="stroke" required>
                        @foreach ($enumStroke as $stroke)
                            <option value="{{ $stroke->value }}">{{ $stroke->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">Jarak</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="distance" name="distance" required>
                        <span class="input-group-text">m</span>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">Jenis Kelamin</label>
                    <select class="form-control" id="gender" name="gender" required>
                        @foreach ($enumGender as $gender)
                            <option value="{{ $gender->value }}">{{ $gender->label() }}</option>
                        @endforeach
                        <option value="mixed">Campuran</option>
                    </select>
                </div>

                <div class="col-md-4 col-12">
                    <label class="form-label">Kelompok Umur</label>
                    <select class="form-control" id="age_group_id" name="age_group_id" required>
                        @foreach ($ageGroups as $ku)
                            <option value="{{ $ku->id }}">{{ $ku->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">Tipe Perlombaan</label>
                    <select class="form-control" id="event_type" name="event_type" required>
                        @foreach ($enumEType as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">Maks. Jumlah Atlet</label>
                    <input class="form-control" type="number" min="0" max="4"
                           name="max_relay_athletes" id="max_relay_athletes" disabled>
                </div>

                <div class="col-md-12 col-12">
                    <label class="form-label">Biaya Pendaftaran</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control rupiah"
                               id="registration_fee" name="registration_fee" required>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" id="eventSubmitBtn">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Delete Confirm Modal -->
<div class="modal fade" id="modalDeleteEvent" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-body p-4 text-center">
                <div class="d-flex align-items-center justify-content-center mb-3 mx-auto rounded-circle"
                     style="width:52px;height:52px;background:#fef2f2">
                    <i class="bi bi-exclamation-triangle-fill" style="color:#ef4444;font-size:22px"></i>
                </div>
                <h5 class="fw-bold mb-1">Hapus Event?</h5>
                <p class="text-muted mb-4" style="font-size:13px">
                    Event nomor <strong id="deleteEventNo" class="text-dark"></strong>
                    akan dihapus permanen.
                </p>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-light fw-semibold flex-fill rounded-3"
                            data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-danger fw-semibold flex-fill rounded-3"
                            id="deleteEventConfirmBtn" onclick="executeDeleteEvent()">Hapus</button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    /* Session toggle chevron */
    .session-chevron.rotated { transform: rotate(180deg); }
    .session-toggle-btn:hover { background:#f1f5f9 !important; }

    /* Table rows */
    .event-row:hover { background:#f8fafc !important; }
</style>
@endpush

@push('scripts')
<script>

const EVENTS_URL_STORE   = "{{ route('competition.tab.events.store', $competition) }}";
const EVENTS_URL_EDIT    = "{{ route('competition.tab.events.edit', [$competition, ':event']) }}".replace(':event', '');
const EVENTS_URL_UPDATE  = "{{ route('competition.tab.events.update', [$competition, ':event']) }}".replace(':event', '');
const EVENTS_URL_DESTROY = "{{ route('competition.tab.events.destroy', [$competition, ':event']) }}".replace(':event', '');
const CSRF           = "{{ csrf_token() }}";
const ESTAFET_VALUE  = "{{ \App\Enums\EventType::estafet->value }}";

// ── Helper: Toast notifikasi ─────────────────────────────────────────
function showToast(icon, title) {
    // Pakai SweetAlert2 Toast jika tersedia, fallback ke alert biasa
    if (typeof Toast !== 'undefined') {
        Toast.fire({ icon, title });
    } else if (typeof Swal !== 'undefined') {
        Swal.fire({ icon, title, toast: true, position: 'top-end', showConfirmButton: false, timer: 3000 });
    } else {
        alert(title);
    }
}
// ── Open create modal ────────────────────────────────────────────────
function openCreateEvent() {
    document.getElementById('eventForm').reset();
    document.getElementById('competition_event_id').value = '';
    document.getElementById('modalEventTitle').textContent = 'Tambah Event';
    document.getElementById('max_relay_athletes').disabled = true;
}
// ── Toggle Sesi ──────────────────────────────────────────────────────
function toggleSessionGroup(btn) {
    const body    = btn.nextElementSibling;
    const chevron = btn.querySelector('.session-chevron');
    const isOpen  = body.style.display !== 'none';
    body.style.display = isOpen ? 'none' : 'block';
    chevron.classList.toggle('rotated', isOpen);
}
// ── Relay toggle (existing) ──────────────────────────────────────────
document.getElementById('event_type').addEventListener('change', function () {
    const maxEl = document.getElementById('max_relay_athletes');
    maxEl.disabled = this.value !== ESTAFET_VALUE;
    if (this.value !== ESTAFET_VALUE) maxEl.value = '';
});
// ── Helper: update stat cards ────────────────────────────────────────
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
// ── Helper: update per-session count badge ───────────────────────────
function refreshSessionCount(sessionId) {
    const card = document.querySelector(`.session-group-card[data-session-id="${sessionId}"]`);
    if (!card) return;
    const count = card.querySelectorAll('tr.event-row[data-id]').length;
    const badge = card.querySelector('.session-visible-count');
    if (badge) badge.textContent = count;

    // Tampilkan/sembunyikan empty state
    const emptyDesktop = card.querySelector('.session-empty-row');
    if (emptyDesktop) emptyDesktop.style.display = count === 0 ? '' : 'none';
}
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
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
            body: formData,
        });
        const data = await res.json();

        if (!res.ok || !data.success) {
            // Tampilkan error validasi jika ada
            if (data.errors) {
                const msg = Object.values(data.errors).flat().join('\n');
                showToast('error', msg);
            } else {
                showToast('error', data.message ?? 'Terjadi kesalahan.');
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

        showToast('success', data.message);

    } catch (err) {
        console.error(err);
        showToast('error', 'Gagal menyimpan event. Periksa koneksi.');
    } finally {
        submitBtn.disabled    = false;
        submitBtn.textContent = 'Simpan';
    }
});
// ── Edit Event (existing, unchanged) ────────────────────────────────
async function editEvent(eventId) {
    document.getElementById('modalEventTitle').textContent = 'Edit Event';
    try {
        const res  = await fetch(`${EVENTS_URL_EDIT}${eventId}`, {
            headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': CSRF },
        });
        const data = await res.json();

        if (!res.ok || !data.success) {
            showToast('error', 'Data tidak ditemukan.');
            return;
        }

        const ev = data.event;
        document.getElementById('eventForm').reset();
        document.getElementById('competition_event_id').value   = ev.id;
        document.getElementById('competition_session_id').value = ev.competition_session_id;
        document.getElementById('stroke').value                 = ev.stroke;
        document.getElementById('distance').value               = ev.distance;
        document.getElementById('gender').value                 = ev.gender;
        document.getElementById('age_group_id').value           = ev.age_group_id;
        document.getElementById('event_type').value             = ev.event_type;
        document.getElementById('registration_fee').value       = toNum(ev.registration_fee);

        const maxEl = document.getElementById('max_relay_athletes');
        maxEl.disabled = ev.event_type !== ESTAFET_VALUE;
        maxEl.value    = ev.max_relay_athletes ?? '';

        new bootstrap.Modal(document.getElementById('modalEvent')).show();

    } catch (err) {
        console.error(err);
        showToast('error', 'Gagal mengambil data event.');
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
            headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
        });
        const data = await res.json();

        if (!res.ok || !data.success) {
            showToast('error', data.message ?? 'Gagal menghapus.');
            return;
        }

        // Hapus semua elemen DOM dengan data-id ini (desktop tr + mobile div)
        document.querySelectorAll(`.event-row[data-id="${_deleteTargetId}"]`)
            .forEach(el => el.remove());

        refreshSessionCount(data.session_id);
        refreshStatCards();

        bootstrap.Modal.getInstance(document.getElementById('modalDeleteEvent'))?.hide();
        showToast('success', data.message);
        _deleteTargetId = null;

    } catch (err) {
        console.error(err);
        showToast('error', 'Gagal menghapus event.');
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

document.getElementById('eventSearchInput').addEventListener('input', applyEventFilter);
document.getElementById('filterGender').addEventListener('change', applyEventFilter);
document.getElementById('filterTipe').addEventListener('change', applyEventFilter);

</script>
@endpush
