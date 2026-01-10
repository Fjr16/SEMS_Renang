@extends('layouts.main')

@section('content')
  <!-- Header Page -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">Detail User</h2>
      <p class="text-muted mb-0">Informasi lengkap pengguna</p>
    </div>
    <div class="mt-3 mt-md-0">
      <a href="{{ route('dashboard') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali
      </a>
    </div>
  </div>

  <!-- Card User Info -->
  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <h5 class="fw-bold">Informasi Akun</h5>
      <div class="row mt-3">
        <div class="col-md-6">
          <p><strong>Nama:</strong> Admin Utama</p>
          <p><strong>Email:</strong> admin@swimcomp.com</p>
          <p><strong>Role:</strong> <span class="badge bg-primary">Admin</span></p>
        </div>
        <div class="col-md-6">
          <p><strong>Status:</strong> <span class="badge bg-success">Aktif</span></p>
          <p><strong>Dibuat pada:</strong> 20 Sep 2025</p>
          <p><strong>Terakhir login:</strong> 28 Sep 2025, 14:32</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Card Activity / Competition Involvement -->
  <div class="card shadow-sm border-0">
    <div class="card-body">
      <h5 class="fw-bold">Aktivitas / Kompetisi</h5>
      <div class="table-responsive mt-3">
        <table class="table table-striped align-middle">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>Kompetisi</th>
              <th>Peran</th>
              <th>Status</th>
              <th>Tanggal</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>1</td>
              <td>Kejuaraan Renang Nasional 2025</td>
              <td><span class="badge bg-secondary">Official</span></td>
              <td><span class="badge bg-success">Aktif</span></td>
              <td>Agustus 2025</td>
            </tr>
            <tr>
              <td>2</td>
              <td>Turnamen Antar Klub 2024</td>
              <td><span class="badge bg-primary">Admin</span></td>
              <td><span class="badge bg-danger">Selesai</span></td>
              <td>Oktober 2024</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
@endsection
