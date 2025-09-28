@extends('layouts.main')

@section('content')
  <!-- Header Page -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">🔥 Manajemen Heats</h2>
      <p class="text-muted mb-0">Kelola sesi heat untuk setiap event kompetisi.</p>
    </div>
    <div class="mt-3 mt-md-0">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalHeat" id="btnAddHeat">
        <i class="bi bi-plus-circle me-1"></i> Tambah Heat
      </button>
    </div>
  </div>

  <!-- Card Content -->
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table id="heatsTable" class="table table-striped table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Event</th>
              <th>Heat Number</th>
              <th>Jumlah Lane Terisi</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            {{-- Contoh data statis --}}
            <tr>
              <td>1</td>
              <td>50m Freestyle U14 M</td>
              <td>1</td>
              <td>8</td>
              <td>
                <div class="btn-group">
                  <button class="btn btn-sm btn-info btn-view" data-id="1"><i class="bi bi-eye"></i></button>
                  <button class="btn btn-sm btn-warning btn-edit"
                          data-id="1"
                          data-event="50m Freestyle U14 M"
                          data-heat="1"
                          title="Edit"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-danger btn-delete" data-id="1" data-bs-toggle="modal" data-bs-target="#modalDelete"><i class="bi bi-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>100m Butterfly U16 F</td>
              <td>2</td>
              <td>6</td>
              <td>
                <div class="btn-group">
                  <button class="btn btn-sm btn-info btn-view" data-id="2"><i class="bi bi-eye"></i></button>
                  <button class="btn btn-sm btn-warning btn-edit"
                          data-id="2"
                          data-event="100m Butterfly U16 F"
                          data-heat="2"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-danger btn-delete" data-id="2" data-bs-toggle="modal" data-bs-target="#modalDelete"><i class="bi bi-trash"></i></button>
                </div>
              </td>
            </tr>
            {{-- end loop --}}
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal: Create/Edit Heat -->
  <div class="modal fade" id="modalHeat" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="formHeat">
          <input type="hidden" name="id" id="heatId" value="">
          <div class="modal-header">
            <h5 class="modal-title" id="modalHeatTitle">Tambah Heat</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Event</label>
              <select class="form-select" id="heatEvent" name="event_id" required>
                <option value="">-- Pilih Event --</option>
                {{-- @foreach($events as $event) --}}
                <option value="1">50m Freestyle U14 M</option>
                <option value="2">100m Butterfly U16 F</option>
                {{-- @endforeach --}}
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Heat Number</label>
              <input type="number" id="heatNumber" name="heat_number" min="1" class="form-control" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: Delete -->
  <div class="modal fade" id="modalDelete" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body text-center">
          <p class="mb-3">Yakin ingin menghapus heat ini?</p>
          <div class="d-flex justify-content-center gap-2">
            <button class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button class="btn btn-danger btn-sm" id="confirmDelete">Hapus</button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // DataTable
    $('#heatsTable').DataTable({
      responsive: true,
      pageLength: 10,
      language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
    });

    // Tambah Heat
    document.getElementById('btnAddHeat').addEventListener('click', () => {
      document.getElementById('modalHeatTitle').innerText = 'Tambah Heat';
      document.getElementById('formHeat').reset();
      document.getElementById('heatId').value = '';
    });

    // Edit Heat
    document.querySelectorAll('.btn-edit').forEach(btn => {
      btn.addEventListener('click', function () {
        const id = this.dataset.id;
        const event = this.dataset.event;
        const heat = this.dataset.heat;

        document.getElementById('modalHeatTitle').innerText = 'Edit Heat';
        document.getElementById('heatId').value = id;
        document.getElementById('heatEvent').value = event; // sesuaikan value dengan id event
        document.getElementById('heatNumber').value = heat;

        const modal = new bootstrap.Modal(document.getElementById('modalHeat'));
        modal.show();
      });
    });

    // Submit form
    document.getElementById('formHeat').addEventListener('submit', function (e) {
      e.preventDefault();
      showSpinner();
      setTimeout(() => {
        hideSpinner();
        bootstrap.Modal.getInstance(document.getElementById('modalHeat')).hide();
        location.reload();
      }, 600);
    });

    // Hapus Heat
    let deleteId = null;
    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', () => deleteId = btn.dataset.id);
    });
    document.getElementById('confirmDelete').addEventListener('click', function () {
      if (!deleteId) return;
      showSpinner();
      setTimeout(() => {
        hideSpinner();
        bootstrap.Modal.getInstance(document.getElementById('modalDelete')).hide();
        location.reload();
      }, 500);
    });
  });
</script>
@endsection
