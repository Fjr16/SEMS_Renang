@extends('layouts.main')

@section('content')
  <!-- Header Page -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">Manajemen User</h2>
      <p class="text-muted mb-0">Kelola akun pengguna dan perannya di sistem</p>
    </div>
    <div class="mt-3 mt-md-0">
      <button data-bs-toggle="modal" data-bs-target="#modalUser" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah User
      </button>
    </div>
  </div>

  <!-- Card Content -->
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table id="dataTable" class="table table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Role</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <!-- Contoh data statis -->
            <tr>
              <td>1</td>
              <td>Admin Utama</td>
              <td>admin@swimcomp.com</td>
              <td><span class="badge bg-primary">Admin</span></td>
              <td><span class="badge bg-success">Aktif</span></td>
              <td>
                <div class="btn-group">
                  <a href="{{ route('users.detail') }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
                  <a href="#" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                  <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Petugas Lomba</td>
              <td>official@swimcomp.com</td>
              <td><span class="badge bg-secondary">Official</span></td>
              <td><span class="badge bg-success">Aktif</span></td>
              <td>
                <div class="btn-group">
                  <a href="{{ route('users.detail') }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
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

  <!-- Modal Create/Edit User -->
  <div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form>
          <div class="modal-header">
            <h5 class="modal-title">Tambah User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label>Nama Lengkap</label>
              <input type="text" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Email</label>
              <input type="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Password</label>
              <input type="password" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Role</label>
              <select class="form-select">
                <option value="admin">Admin</option>
                <option value="official">Official</option>
                <option value="club_manager">Club Manager</option>
              </select>
            </div>
            <div class="mb-3">
              <label>Status</label>
              <select class="form-select">
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
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
@endsection