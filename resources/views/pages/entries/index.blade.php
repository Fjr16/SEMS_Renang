@extends('layouts.main')

@section('content')
  <!-- Header Page -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">📋 Manajemen Entries</h2>
      <p class="text-muted mb-0">Kelola pendaftaran atlet ke setiap event (seed time, heat, lane).</p>
    </div>
    <div class="mt-3 mt-md-0">
      <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalEntry" id="btnAddEntry">
        <i class="bi bi-plus-circle me-1"></i> Tambah Entry
      </button>
    </div>
  </div>
  
  <!-- Filter -->
  <div class="row mb-3">
    <div class="col-md-4">
      <select class="form-select">
        <option selected>Filter by Competition</option>
        <option>Competition 1</option>
        <option>Competition 2</option>
      </select>
    </div>
    <div class="col-md-4">
      <select class="form-select">
        <option selected>Filter by Event</option>
        <option>Event 1</option>
        <option>Event 2</option>
      </select>
    </div>
  </div>


  <!-- Card Content -->
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table id="entriesTable" class="table table-striped table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Athlete</th>
              <th>Event</th>
              <th>Seed Time</th>
              <th>Status</th>
              <th>Heat</th>
              <th>Lane</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            {{-- Contoh static — ganti dengan loop blade: @foreach($entries as $entry) --}}
            <tr>
              <td>1</td>
              <td>Rudi Santoso</td>
              <td>50m Freestyle U14 M</td>
              <td>00:28.75</td>
              <td><span class="badge bg-success">Confirmed</span></td>
              <td>1</td>
              <td>4</td>
              <td>
                <div class="btn-group">
                  <button class="btn btn-sm btn-info btn-view" data-id="1" title="Lihat"><i class="bi bi-eye"></i></button>
                  <button class="btn btn-sm btn-warning btn-edit" 
                          data-id="1"
                          data-athlete="Rudi Santoso"
                          data-event="50m Freestyle U14 M"
                          data-seed="00:28.75"
                          data-status="confirmed"
                          data-heat="1"
                          data-lane="4"
                          title="Edit"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-danger btn-delete" data-id="1" title="Hapus" data-bs-toggle="modal" data-bs-target="#modalDelete"><i class="bi bi-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Siti Aminah</td>
              <td>100m Freestyle U16 F</td>
              <td>01:12.20</td>
              <td><span class="badge bg-warning text-dark">Pending</span></td>
              <td></td>
              <td></td>
              <td>
                <div class="btn-group">
                  <button class="btn btn-sm btn-info btn-view" data-id="2" title="Lihat"><i class="bi bi-eye"></i></button>
                  <button class="btn btn-sm btn-warning btn-edit" 
                          data-id="2"
                          data-athlete="Siti Aminah"
                          data-event="100m Freestyle U16 F"
                          data-seed="01:12.20"
                          data-status="pending"
                          data-heat=""
                          data-lane=""
                          title="Edit"><i class="bi bi-pencil"></i></button>
                  <button class="btn btn-sm btn-danger btn-delete" data-id="2" title="Hapus" data-bs-toggle="modal" data-bs-target="#modalDelete"><i class="bi bi-trash"></i></button>
                </div>
              </td>
            </tr>
            {{-- end loop --}}
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal: Create / Edit Entry -->
  <div class="modal fade" id="modalEntry" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        {{-- NOTE: Sesuaikan action + method pada saat integrasi backend --}}
        <form id="formEntry">
          <input type="hidden" name="id" id="entryId" value="">
          <div class="modal-header">
            <h5 class="modal-title" id="modalEntryTitle">Tambah Entry</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label">Atlet</label>
                <select id="entryAthlete" name="athlete_id" class="form-select" required>
                  <option value="">-- Pilih Atlet --</option>
                  {{-- @foreach($athletes as $a) --}}
                  <option value="1">Rudi Santoso</option>
                  <option value="2">Siti Aminah</option>
                  {{-- @endforeach --}}
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Event</label>
                <select id="entryEvent" name="event_id" class="form-select" required>
                  <option value="">-- Pilih Event --</option>
                  {{-- @foreach($events as $e) --}}
                  <option value="1">50m Freestyle U14 M</option>
                  <option value="2">100m Freestyle U16 F</option>
                  {{-- @endforeach --}}
                </select>
              </div>

              <div class="col-md-6">
                <label class="form-label">Seed Time (mm:ss.xx)</label>
                <input id="entrySeed" name="seed_time" type="text" class="form-control" placeholder="00:30.25" pattern="^\\d{1,2}:\\d{2}\\.\\d{2}$" title="Format mm:ss.xx (contoh 00:28.75)">
                <div class="form-text">Kosongkan jika tidak ada seed time.</div>
              </div>

              <div class="col-md-6">
                <label class="form-label">Status</label>
                <select id="entryStatus" name="status" class="form-select" required>
                  <option value="entered">Entered</option>
                  <option value="confirmed">Confirmed</option>
                  <option value="withdrawn">Withdrawn</option>
                </select>
              </div>

              <div class="col-md-4">
                <label class="form-label">Heat Number</label>
                <input id="entryHeat" name="heat_number" type="number" min="1" class="form-control">
              </div>

              <div class="col-md-4">
                <label class="form-label">Lane</label>
                <input id="entryLane" name="lane" type="number" min="1" class="form-control">
              </div>

              <div class="col-md-4">
                <label class="form-label">Catatan (opsional)</label>
                <input id="entryNote" name="note" type="text" class="form-control" placeholder="catatan singkat">
              </div>
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary" id="btnSaveEntry">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Modal: Delete Confirmation -->
  <div class="modal fade" id="modalDelete" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-body text-center">
          <p class="mb-3">Yakin ingin menghapus entry ini?</p>
          <div class="d-flex justify-content-center gap-2">
            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
            <button type="button" class="btn btn-danger btn-sm" id="confirmDelete">Hapus</button>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('scripts')
<script>
  document.addEventListener('DOMContentLoaded', function () {
    // Inisialisasi DataTable
    const table = $('#entriesTable').DataTable({
      responsive: true,
      pageLength: 10,
      lengthMenu: [5,10,25,50],
      language: { url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json' }
    });

    // Buka modal tambah (reset form)
    document.getElementById('btnAddEntry').addEventListener('click', function () {
      document.getElementById('modalEntryTitle').innerText = 'Tambah Entry';
      document.getElementById('formEntry').reset();
      document.getElementById('entryId').value = '';
    });

    // Edit: isi form ketika tombol edit diklik
    document.querySelectorAll('.btn-edit').forEach(btn => {
      btn.addEventListener('click', function () {
        const id = this.dataset.id;
        // ambil data dari data-* attributes (atau fetch dari API jika perlu)
        const athlete = this.dataset.athlete || '';
        const event = this.dataset.event || '';
        const seed = this.dataset.seed || '';
        const status = this.dataset.status || 'entered';
        const heat = this.dataset.heat || '';
        const lane = this.dataset.lane || '';

        document.getElementById('modalEntryTitle').innerText = 'Edit Entry';
        document.getElementById('entryId').value = id;
        // Jika menggunakan dynamic option values, pilih option sesuai value (contoh berikut asumsi option value sama nama)
        // Untuk produksi, gunakan athlete/event id sebagai value
        document.getElementById('entryAthlete').value = document.querySelector(`#entryAthlete option[data-name="${athlete}"]`)?.value || document.getElementById('entryAthlete').value;
        document.getElementById('entryEvent').value = document.querySelector(`#entryEvent option[data-name="${event}"]`)?.value || document.getElementById('entryEvent').value;
        document.getElementById('entrySeed').value = seed;
        document.getElementById('entryStatus').value = status;
        document.getElementById('entryHeat').value = heat;
        document.getElementById('entryLane').value = lane;

        // tampilkan modal
        const modal = new bootstrap.Modal(document.getElementById('modalEntry'));
        modal.show();
      });
    });

    // Simpan form (contoh: submit via AJAX)
    const formEntry = document.getElementById('formEntry');
    formEntry.addEventListener('submit', function (e) {
      e.preventDefault();
      // TODO: kirim ke backend via fetch/axios. Di sini demo saja:
      const formData = new FormData(formEntry);
      showSpinner();
      setTimeout(() => {
        hideSpinner();
        // close modal
        const bs = bootstrap.Modal.getInstance(document.getElementById('modalEntry'));
        if (bs) bs.hide();
        // reload tabel atau men-insert row baru; di demo, kita reload halaman
        location.reload();
      }, 700);
    });

    // Hapus (contoh): menyimpan id yang akan dihapus
    let deletingId = null;
    document.querySelectorAll('.btn-delete').forEach(btn => {
      btn.addEventListener('click', function () {
        deletingId = this.dataset.id;
      });
    });

    document.getElementById('confirmDelete').addEventListener('click', function () {
      if (!deletingId) return;
      // TODO: panggil API delete / submit form
      showSpinner();
      setTimeout(() => {
        hideSpinner();
        // tutup modal
        const delModal = bootstrap.Modal.getInstance(document.getElementById('modalDelete'));
        if (delModal) delModal.hide();
        // pada demo, reload
        location.reload();
      }, 600);
    });
  });
</script>
@endsection
