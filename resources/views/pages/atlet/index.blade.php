@extends('layouts.main')

@section('content')
  <!-- Header Page -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">Manajemen Atlet</h2>
      <p class="text-muted mb-0">Kelola data atlet yang terdaftar dalam sistem</p>
    </div>
    <div class="mt-3 mt-md-0">
      <button data-bs-toggle="modal" data-bs-target="#modalAthlete" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Atlet
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
              <th>Umur</th>
              <th>Klub</th>
              <th>Kategori</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <!-- Contoh data statis, nanti pakai loop blade -->
            <tr>
              <td>1</td>
              <td>Budi Santoso</td>
              <td>16</td>
              <td>Shark Swim Club</td>
              <td>Junior</td>
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
              <td>18</td>
              <td>Dolphin Club</td>
              <td>Senior</td>
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

  <!-- Modal -->
  <div class="modal fade" id="modalAthlete" tabindex="-1">
    <div class="modal-dialog">
      <div class="modal-content">
        <form>
          <div class="modal-header">
            <h5 class="modal-title">Tambah Atlet</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label>Nama Lengkap</label>
              <input type="text" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Tanggal Lahir</label>
              <input type="date" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Gender</label>
              <select class="form-select">
                <option>L</option>
                <option>P</option>
              </select>
            </div>
            <div class="mb-3">
              <label>Klub</label>
              <select class="form-select">
                <option>Club A</option>
                <option>Club B</option>
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
