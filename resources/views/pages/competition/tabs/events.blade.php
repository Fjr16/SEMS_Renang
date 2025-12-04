<!-- Tab Events -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h5 class="fw-bold mb-1">Daftar Events</h5>
    <p class="text-muted mb-0">Kelola event yang ada dalam kompetisi ini</p>
  </div>
  <div class="mt-3 mt-md-0">
    <button data-bs-toggle="modal" data-bs-target="#modalEvent" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Tambah Event
    </button>
  </div>
</div>

<!-- Card Table -->
<div class="card shadow-sm border-0">
  <div class="card-body">
        <table id="eventsTable" class="table table-striped align-middle">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Nomor Event</th>
                <th>Stroke</th>
                <th>Distance</th>
                <th>Gender</th>
                <th>Kelompok Umur</th>
                <th>Tipe Event</th>
                <th>Sistem Event</th>
                <th>Minimal DOB</th>
                <th>Maksimal DOB</th>
                <th>Biaya Pendaftaran</th>
                <th>Catatan</th>
                {{-- <th>Lanes</th> --}}
                <th>Aksi</th>
            </tr>
            </thead>
        </table>
  </div>
</div>

<!-- Modal Create/Edit Event -->
<div class="modal fade" id="modalEvent" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="eventForm" data-url="{{ route('competition.tab.events.store', $competition) }}" onsubmit="storeAndUpdateGlobal(event,this,'eventsTable','modalEvent')">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Nomor Event</label>
            <input type="text" class="form-control" id="event_number" name="event_number" required>
          </div>
          <div class="mb-3">
            <label>Stroke</label>
            <select class="form-control" id="stroke" name="stroke" required>
              @foreach ($enumStroke as $stroke)
                <option value="{{ $stroke->value }}" @selected(old('stroke') === $stroke->value)>{{ $stroke->label() }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label>Jarak</label>
            <div class="input-group">
              <input type="number" class="form-control" id="distance" name="distance" required>
              <span class="input-group-text">m</span>
            </div>
          </div>
          <div class="mb-3">
            <label>Gender</label>
            <select class="form-select" required>
              <option value="male">Putra</option>
              <option value="female">Putri</option>
              <option value="mixed">Campuran</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Kategori Umur</label>
            <select class="form-select" required>
              <option value="u12">U-12</option>
              <option value="u15">U-15</option>
              <option value="u18">U-18</option>
              <option value="senior">Senior</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Jumlah Lanes</label>
            <input type="number" class="form-control" min="4" max="10" value="8" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

{{-- @push('scripts')
    <script>

    </script>
@endpush --}}
