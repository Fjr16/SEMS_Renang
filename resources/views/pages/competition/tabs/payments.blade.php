<!-- Tab Payments -->
<div class="card shadow-sm border-0 mt-3">
    <div class="card-body">
      <h5 class="fw-bold mb-3">💳 Pembayaran Klub</h5>
      <div class="table-responsive">
        <table id="paymentsTable" class="table table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Klub</th>
              <th>Jumlah</th>
              <th>Status</th>
              <th>Tanggal Bayar</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Shark Swim Club</td>
              <td>Rp 1.500.000</td>
              <td><span class="badge bg-success">Lunas</span></td>
              <td>2025-09-25</td>
              <td>
                <button class="btn btn-sm btn-info"><i class="bi bi-eye"></i></button>
                <button class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Dolphin Club</td>
              <td>Rp 1.200.000</td>
              <td><span class="badge bg-danger">Belum Bayar</span></td>
              <td>-</td>
              <td>
                <button class="btn btn-sm btn-info"><i class="bi bi-eye"></i></button>
                <button class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Tombol tambah pembayaran -->
      <div class="mt-3 text-end">
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modalPayment">
          <i class="bi bi-plus-circle me-1"></i> Tambah Pembayaran
        </button>
      </div>
    </div>
</div>

<!-- Modal Tambah Pembayaran -->
<div class="modal fade" id="modalPayment" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Pembayaran</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Klub</label>
            <select class="form-select">
              <option>Shark Swim Club</option>
              <option>Dolphin Club</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Jumlah</label>
            <input type="number" class="form-control" placeholder="Rp 0">
          </div>
          <div class="mb-3">
            <label>Status</label>
            <select class="form-select">
              <option value="paid">Lunas</option>
              <option value="unpaid">Belum Bayar</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Tanggal Bayar</label>
            <input type="date" class="form-control">
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
