@extends('layouts.main')

@section('content')
  <!-- Header Page -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">Manajemen Event</h2>
      <p class="text-muted mb-0">Kelola daftar event dalam kompetisi</p>
    </div>
    <div class="mt-3 mt-md-0">
      <button data-bs-toggle="modal" data-bs-target="#modalEvent" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Event
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
              <th>Nama Event</th>
              <th>Stroke</th>
              <th>Distance</th>
              <th>Gender</th>
              <th>Kategori Usia</th>
              <th>Lanes</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <!-- Contoh data statis -->
            <tr>
              <td>1</td>
              <td>100m Freestyle</td>
              <td>Freestyle</td>
              <td>100m</td>
              <td>Male</td>
              <td>Junior</td>
              <td>8</td>
              <td>
                <div class="btn-group">
                  <a href="{{ route('events.entries') }}" class="btn btn-sm btn-info"><i class="bi bi-eye"></i></a>
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
              <td>Female</td>
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

  <!-- Modal Create/Edit -->
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
                <option value="Freestyle">Freestyle</option>
                <option value="Butterfly">Butterfly</option>
                <option value="Backstroke">Backstroke</option>
                <option value="Breaststroke">Breaststroke</option>
              </select>
            </div>
            <div class="mb-3">
              <label>Distance (meter)</label>
              <input type="number" class="form-control" required>
            </div>
            <div class="mb-3">
              <label>Gender</label>
              <select class="form-select" required>
                <option value="Male">Laki-laki</option>
                <option value="Female">Perempuan</option>
              </select>
            </div>
            <div class="mb-3">
              <label>Kategori Usia</label>
              <input type="text" class="form-control" placeholder="misal: Junior / Senior" required>
            </div>
            <div class="mb-3">
              <label>Lanes</label>
              <input type="number" class="form-control" min="1" max="10" required>
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