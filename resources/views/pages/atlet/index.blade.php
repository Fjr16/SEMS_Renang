@extends('layouts.main')

@push('css')
    <style>
        .gallery-box {
            border: 2px dashed #ced4da;
            border-radius: 8px;
            padding: 6px;
            /* width: 100%; */
            width: 150px;
            aspect-ratio: 3 / 4; /* kotak potret */
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: #f8f9fa;
            cursor: pointer;
            transition: border-color 0.2s ease, background-color 0.2s ease;
            overflow: hidden;
        }

        .gallery-box:hover {
            border-color: #0d6efd;
            background-color: #eef4ff;
        }

        .gallery-placeholder {
            padding: 4px;
        }

        .gallery-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 6px;
        }
    </style>
@endpush
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
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form>
          <div class="modal-header">
            <h5 class="modal-title">Tambah Atlet</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <div class="mb-3">
                <div class="row">
                    <div class="col-6">
                        <label class="form-label" for="club_role_category_id">Kategori Klub</label>
                        <select name="club_role_category_id" id="club_role_category_id" class="form-control">
                            <option value="">--- Pilih Kategori ---</option>
                            @foreach ($clubCategories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('club_role_category_id') == $cat->id)>{{ $cat->name ?? '' }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="club_id">Klub</label>
                        <select name="club_id" id="club_id" class="form-control" style="width: 100%" disabled></select>
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <div class="row">
                    <div class="col-8">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="bod">Tanggal Lahir</label>
                            <input type="date" class="form-control" name="bod" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="gender">Gender</label>
                            <select class="form-control" name="gender" id="gender">
                                @foreach ($genders as $gd)
                                    <option value="{{ $gd->value }}" @selected(old('gender') == $gd->value)>{{ $gd->label() }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="mb-2">
                            <label class="form-label" for="foto">Foto</label>
                        </div>

                        <!-- Gallery Box -->
                        <div class="gallery-box" onclick="document.getElementById('foto').click()">
                            <div id="galleryPlaceholder" class="gallery-placeholder text-center">
                                <div class="small text-muted mb-1">Klik untuk pilih foto</div>
                                <div class="small text-muted">JPG / PNG, maks 2MB</div>
                            </div>
                            <img id="fotoPreview" class="gallery-image d-none" alt="Preview Foto">
                        </div>

                        <!-- Input File Asli (disembunyikan) -->
                        <input type="file" name="foto" id="foto" accept="image/*" class="d-none" onchange="previewImg(event)">
                    </div>
                </div>
            </div>
            <div class="mb-3">
                <label>Nama Sekolah</label>
                <input type="text" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Nama Klub</label>
                <input type="text" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Nama Provinsi</label>
                <input type="text" class="form-control" required>
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


    function previewImg(event){
        const input = event.target;
        const file = input.files[0];

        if(!file) return;

        const allowTypes = ['image/jpeg', 'image/png'];
        const maxSize = 2*1024*1024;

        if(!allowTypes.includes(file.type)){
            Toast.fire({
                icon:'error',
                title:'Gambar yang diterima hanya .jpeg/jpg .png'
            });
            input.value = '';
            return;
        }

        if(file.size > maxSize){
            Toast.fire({
                icon:'error',
                title:'Ukuran foto maksimal 2mb'
            });
            input.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(e){
            const img = document.getElementById('fotoPreview');
            const placeholder = document.getElementById('galleryPlaceholder');

            img.src = e.target.result;
            img.classList.remove('d-none');
            if(placeholder) placeholder.classList.add('d-none');
        }

        reader.readAsDataURL(file);
    }

    $('#club_role_category_id').select2({
        width:'100%',
        placeholder:'Pilih Kategori Klub',
        allowClear:true
    });

    const clubSelect = $('club_id').select2({
        width:'100%',
        placeholder:'Pilih Klub',
        allowClear:true,
        minimumInputLength:0,
        ajax:{
            url:"{{ route('getClubByCategory') }}",
            dataType:'json',
            delay:250,
            data:function(params){
                return {
                    q:params.term || '',
                    page:params.page || 1,
                    category_id:$('#club_role_category_id').val()
                };
            },
            processResult:function(data,params){
                params.page = params.page || 1;

                return {
                    results:(data.results || []).map(row => ({
                        id:row.id,
                        text:row.text || `[${row.code ?? ''}] ${row.name ?? ''}`
                    })),
                    pagination:{
                        more:data.pagination?.more || false
                    }
                };
            },
            cache:true,
        },
        // template
    })
</script>
@endpush
