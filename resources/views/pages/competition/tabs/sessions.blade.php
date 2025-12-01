<!-- Tab Events -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h5 class="fw-bold mb-1">Daftar Sesi</h5>
    <p class="text-muted mb-0">Kelola sesi yang ada dalam kompetisi ini</p>
  </div>
  <div class="mt-3 mt-md-0">
    <button data-bs-toggle="modal" data-bs-target="#modalSessions" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Tambah Sesi
    </button>
  </div>
</div>

<!-- Card Table -->
<div class="card shadow-sm border-0">
  <div class="card-body">
      <table id="sessionsTable" class="table table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>Nama Sesi</th>
            <th>Tanggal Sesi</th>
            <th>Jam Mulai</th>
            <th>Jam Selesai</th>
            <th>Aksi</th>
          </tr>
        </thead>
      </table>
  </div>
</div>

<!-- Modal Create/Edit Event -->
<div class="modal fade" id="modalSessions" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="sessionForm" data-url="{{ route('competition.tab.sessions.store', $competition) }}" onsubmit="return storeAndUpdateGlobal(event,this,'sessionsTable','modalSessions')">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Sesi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="competition_session_id" id="competition_session_id">
          <div class="mb-3">
            <label>Kompetisi</label>
            <input type="text" class="form-control" id="competition_name" value="{{ $competition->name ?? '' }}" disabled>
            <input type="hidden" value="{{ $competition->id ?? '' }}" name="competition_id" id="competition_id">
          </div>
          <div class="mb-3">
            <label>Nama Sesi</label>
            <input type="text" class="form-control" name="name" id="name" required>
          </div>
          <div class="mb-3">
            <label>Tanggal Sesi</label>
            <input type="text" class="form-control mark-date" placeholder="Pilih Tanggal" name="date" id="date" required>
          </div>
          <div class="row mb-3">
            <div class="col-6">
              <label>Jam Mulai</label>
              <input class="form-control waktu-picker" placeholder="Pilih Jam" name="start_time" id="start_time" required>
            </div>
            <div class="col-6">
              <label>Jam Selesai</label>
              <input class="form-control waktu-picker" placeholder="Pilih Jam" name="end_time" id="end_time" required>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('scripts')
    <script>
        async function editSession(element) {
            const tableId = '#'+element.dataset.table;
            const modalId = '#'+element.dataset.modal;

            const form = document.getElementById(element.dataset.form);
            form.reset();

            const tr = $(element).closest('tr');
            const data = $(tableId).DataTable().row(tr).data();

            $('#competition_session_id').val(data.id);
            $('#name').val(data.name);
            $('#date').flatpickr().setDate(data.date);
            $('#start_time')[0]._flatpickr.setDate(data.start_time, true, 'H:i');
            $('#end_time')[0]._flatpickr.setDate(data.end_time, true, 'H:i');

            $(modalId).modal('show');
        }
    </script>
@endpush
