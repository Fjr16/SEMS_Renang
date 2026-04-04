<!-- Tab Entries -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h5 class="fw-bold mb-1">Daftar Entries</h5>
    <p class="text-muted mb-0">Kelola peserta yang mendaftar ke event kompetisi ini</p>
  </div>
  <div class="mt-3 mt-md-0">
    <button data-bs-toggle="modal" data-bs-target="#modalEntry" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Tambah Entry
    </button>
  </div>
</div>

<!-- Card Table -->
<div class="card shadow-sm border-0">
  <div class="card-body">
    <div class="table-responsive">
      <table id="entriesTable" class="table table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Atlet</th>
            <th>Event</th>
            <th>Seed Time</th>
            <th>Status</th>
            <th>Heat</th>
            <th>Lane</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <!-- Contoh data statis -->
          <tr>
            <td>1</td>
            <td>Budi Santoso</td>
            <td>100m Freestyle</td>
            <td>00:58.23</td>
            <td><span class="badge bg-success">Confirmed</span></td>
            <td>1</td>
            <td>4</td>
            <td>
              <div class="btn-group">
                <a href="#" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                <a href="#" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </div>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>Siti Aminah</td>
            <td>200m Butterfly</td>
            <td>02:13.11</td>
            <td><span class="badge bg-warning">Pending</span></td>
            <td>2</td>
            <td>5</td>
            <td>
              <div class="btn-group">
                <a href="#" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                <a href="#" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Create/Edit Entry -->
<div class="modal fade" id="modalEntry" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Entry</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Atlet</label>
            <select class="form-select" required>
              <option>Budi Santoso</option>
              <option>Siti Aminah</option>
              <option>Andi Wijaya</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Event</label>
            <select class="form-select" required>
              <option>100m Freestyle</option>
              <option>200m Butterfly</option>
              <option>400m Medley</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Seed Time</label>
            <input type="text" class="form-control" placeholder="00:59.88" required>
          </div>
          <div class="mb-3">
            <label>Status</label>
            <select class="form-select" required>
              <option value="pending">Pending</option>
              <option value="confirmed">Confirmed</option>
              <option value="withdrawn">Withdrawn</option>
            </select>
          </div>
          <div class="row">
            <div class="col-md-6 mb-3">
              <label>Heat</label>
              <input type="number" class="form-control" min="1">
            </div>
            <div class="col-md-6 mb-3">
              <label>Lane</label>
              <input type="number" class="form-control" min="1" max="10">
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
