<!-- Tab Officials -->
<div class="card shadow-sm border-0 mt-3">
    <div class="card-body">
      <h5 class="fw-bold mb-3">👨‍⚖️ Manajemen Official</h5>
      <div class="table-responsive">
        <table id="officialsTable" class="table table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Nama</th>
              <th>Role</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Andi Pratama</td>
              <td><span class="badge bg-primary">Referee</span></td>
              <td>
                <button class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Siti Handayani</td>
              <td><span class="badge bg-secondary">Starter</span></td>
              <td>
                <button class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Tambah Official -->
      <div class="mt-3 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalOfficial">
          <i class="bi bi-plus-circle me-1"></i> Tambah Official
        </button>
      </div>
    </div>
</div>

<!-- Modal Tambah Official -->
<div class="modal fade" id="modalOfficial" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Official</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Nama User</label>
            <select class="form-select">
              <option>Andi Pratama</option>
              <option>Siti Handayani</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Role</label>
            <select class="form-select">
              <option>Referee</option>
              <option>Starter</option>
              <option>Judge</option>
              <option>Timekeeper</option>
            </select>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
