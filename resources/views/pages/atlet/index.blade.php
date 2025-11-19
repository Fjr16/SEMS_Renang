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
        <table id="atletTable" class="table table-striped align-middle">
          <thead class="table-light">
            <tr>
                <th>Aksi</th>
                <th>Foto</th>
                <th>Atlet</th>
                <th>Klub Sekarang</th>
                <th>BOD</th>
                <th>Jenis Kelamin</th>
                <th>Sekolah</th>
                <th>Klub</th>
                <th>Kota</th>
                <th>Provinsi</th>
            </tr>
          </thead>
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

@push('scripts')
<script>
    var table;
    $(document).ready(function(){
        table = $('#atletTable').DataTable({
            processing:true,
            serverSide:true,
            language:{
                processing:'loading...',
                search:'Cari:',
                infoEmpty:'Tidak ada data',
                zeroRecords:'Tidak ada data yang cocok',
                paginate:{
                    first:'Awal',
                    last:'Akhir',
                    next:'Berikutnya',
                    previous:'Sebelumnya'
                },
            },
            ajax:"{{ route('atlet.data') }}",
            columns:[
                {data:'action', name:'action', className:'text-center', orderable:false, searchable:false},
                {data:'foto', name:'foto', className:'text-center', orderable:false, searchable:false},
                {data:'codeName', name:'name', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'clubDesc', name:'clubDesc', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'bod', name:'bod', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'genderAttr', name:'gender', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'school_name', name:'school_name', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'club_name', name:'club_name', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'city_name', name:'city_name', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'province_name', name:'province_name', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
            ],
            order:[[2,'asc']]
        });
    });
</script>
@endpush
