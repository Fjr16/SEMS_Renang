@extends('layouts.main')

@section('content')
  <!-- Header Page -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">Detail Event: 100m Freestyle</h2>
      <p class="text-muted mb-0">Lihat data heats dan entries untuk event ini</p>
    </div>
    <div class="mt-3 mt-md-0">
      <button data-bs-toggle="modal" data-bs-target="#modalEntry" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah Entry
      </button>
    </div>
  </div>

  <!-- Info Event -->
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <dl class="row mb-0">
        <dt class="col-sm-3">Nama Event</dt>
        <dd class="col-sm-9">100m Freestyle</dd>

        <dt class="col-sm-3">Stroke</dt>
        <dd class="col-sm-9">Freestyle</dd>

        <dt class="col-sm-3">Gender</dt>
        <dd class="col-sm-9">Putra</dd>

        <dt class="col-sm-3">Kategori Umur</dt>
        <dd class="col-sm-9">17-18 Tahun</dd>

        <dt class="col-sm-3">Lanes</dt>
        <dd class="col-sm-9">8</dd>
      </dl>
    </div>
  </div>

  <!-- Entries Table -->
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h5 class="fw-bold mb-3">Daftar Entries</h5>
      <div class="table-responsive">
        <table id="dataTable" class="table table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Atlet</th>
              <th>Klub</th>
              <th>Seed Time</th>
              <th>Heat</th>
              <th>Lane</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Budi Santoso</td>
              <td>Shark Club</td>
              <td>00:58.21</td>
              <td>1</td>
              <td>4</td>
              <td><span class="badge bg-success">Aktif</span></td>
              <td>
                <div class="btn-group">
                  <a href="#" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                  <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
                </div>
              </td>
            </tr>
            <tr>
              <td>2</td>
              <td>Siti Aminah</td>
              <td>Dolphin Club</td>
              <td>01:02.15</td>
              <td>1</td>
              <td>5</td>
              <td><span class="badge bg-warning">Pending</span></td>
              <td>
                <div class="btn-group">
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

  <!-- Modal Entry -->
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
              <select class="form-select">
                <option>Pilih Atlet</option>
                <option>Budi Santoso</option>
                <option>Siti Aminah</option>
              </select>
            </div>
            <div class="mb-3">
              <label>Seed Time</label>
              <input type="text" class="form-control" placeholder="00:59.00">
            </div>
            <div class="mb-3">
              <label>Status</label>
              <select class="form-select">
                <option>Aktif</option>
                <option>Pending</option>
                <option>Didiskualifikasi</option>
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