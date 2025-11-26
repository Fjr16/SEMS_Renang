<!-- Tab Events -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h5 class="fw-bold mb-1">Daftar Sesi</h5>
    <p class="text-muted mb-0">Kelola sesi yang ada dalam kompetisi ini</p>
  </div>
  <div class="mt-3 mt-md-0">
    <button data-bs-toggle="modal" data-bs-target="#modalEvent" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Tambah Sesi
    </button>
  </div>
</div>

<!-- Card Table -->
<div class="card shadow-sm border-0">
  <div class="card-body">
    <div class="table-responsive">
      <table id="eventsTable" class="table table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Nama Event</th>
            <th>Stroke</th>
            <th>Distance</th>
            <th>Gender</th>
            <th>Kategori Umur</th>
            <th>Lanes</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>1</td>
            <td>100m Freestyle</td>
            <td>Freestyle</td>
            <td>100m</td>
            <td>Putra</td>
            <td>U-18</td>
            <td>8</td>
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
            <td>200m Butterfly</td>
            <td>Butterfly</td>
            <td>200m</td>
            <td>Putri</td>
            <td>Senior</td>
            <td>6</td>
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

<!-- Modal Create/Edit Event -->
<div class="modal fade" id="modalEvent" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Nama Event</label>
            <input type="text" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Stroke</label>
            <select class="form-select" required>
              <option value="freestyle">Freestyle</option>
              <option value="backstroke">Backstroke</option>
              <option value="breaststroke">Breaststroke</option>
              <option value="butterfly">Butterfly</option>
              <option value="medley">Medley</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Distance</label>
            <select class="form-select" required>
              <option value="50">50m</option>
              <option value="100">100m</option>
              <option value="200">200m</option>
              <option value="400">400m</option>
              <option value="800">800m</option>
              <option value="1500">1500m</option>
            </select>
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
