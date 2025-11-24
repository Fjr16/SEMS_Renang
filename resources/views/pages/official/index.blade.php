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
      <h2 class="fw-bold mb-1">Manajemen Official</h2>
      <p class="text-muted mb-0">Kelola data official yang terdaftar dalam sistem</p>
    </div>
    <div class="mt-3 mt-md-0">
      <button data-bs-toggle="modal" data-bs-target="#modalOfficial" class="btn btn-primary" onclick="$('#modalTitle').text('Tambah Official'); $('#official_id').val('');">
        <i class="bi bi-plus-circle me-1"></i> Tambah Official
      </button>
    </div>
  </div>

  <!-- Card Content -->
  <div class="card shadow-sm border-0">
    <div class="card-body">
        <table id="officialTable" class="table table-striped align-middle">
            <thead class="table-light">
            <tr>
                <th>Aksi</th>
                <th>Foto</th>
                <th>Official</th>
                <th>Jenis Kelamin</th>
                <th>License</th>
                <th>Klub</th>
                <th>Kota</th>
                <th>Provinsi</th>
            </tr>
            </thead>
        </table>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modalOfficial" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="formOfficial" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Tambah Atlet</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
              <div class="mb-3">
                <div class="row">
                    <div class="col-6">
                        <input type="hidden" name="official_id" id="official_id">
                        <label class="form-label" for="club_role_category_id">Kategori Klub</label>
                        <select name="club_role_category_id" id="club_role_category_id" class="form-control form-control-md">
                            <option value=""></option>
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
                    <div class="col-sm-8 col-md-7 col-6">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name"  id="name" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-6 col-12 mb-3 mb-sm-0">
                                <label class="form-label" for="gender">Gender</label>
                                <select class="form-control" name="gender" id="gender">
                                    @foreach ($genders as $gd)
                                        <option value="{{ $gd->value }}" @selected(old('gender') == $gd->value)>{{ $gd->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-12">
                                <label class="form-label" for="license">License</label>
                                <select class="form-control" name="license" id="license">
                                    @foreach ($licenses as $license)
                                        <option value="{{ $license->value }}" @selected(old('license') == $license->value)>{{ $license->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label" for="current_city">Nama Kota</label>
                            <input type="text" name="current_city" id="current_city" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-sm-4 col-md-5 col-6">
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
                <label class="form-label" for="current_province">Nama Provinsi</label>
                <input type="text" class="form-control" name="current_province" id="current_province" required>
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
        table = $('#officialTable').DataTable({
            processing:true,
            serverSide:true,
            columnDefs: [
                { targets: 0, className: 'dt-actions',  }, // kolom Aksi
                { targets: 1, className: 'dt-fotos',  } // kolom foto
            ],
            ajax:"{{ route('official.data') }}",
            columns:[
                {data:'action', name:'action', className:'text-center', orderable:false, searchable:false},
                {data:'foto', name:'foto', className:'text-center', orderable:false, searchable:false},
                {data:'name', name:'name', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'genderAttr', name:'gender', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'licenseAttr', name:'licenseAttr', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'clubDesc', name:'clubDesc', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'current_city', name:'current_city', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'current_province', name:'current_province', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
            ],
            order:[[2,'asc']]
        });

        $('#club_role_category_id').select2({
            width:'100%',
            placeholder:'Pilih Kategori Klub',
            allowClear:true,
            dropdownParent: $('#modalOfficial') // id modal kamu
        });

        const clubSelect = $('#club_id').select2({
            width:'100%',
            placeholder:'Pilih Klub',
            allowClear:true,
            dropdownParent: $('#modalOfficial'),
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
                processResults:function(res,params){
                    params.page = params.page || 1;

                    return {
                        results:(res.data || []).map(row => ({
                            id:row.id,
                            text:`[${row.club_code ?? ''}] ${row.club_name ?? ''}`
                        })),
                        pagination:{
                            more:res.pagination?.more || false
                        }
                    };
                },
                cache:true,
            },
            templateResult:function(item){
                if (item.loading) return item.text;
                return item.text;
            },
            templateSelection:function(item){
                return item.text || item.id;
            }
        });

        $('#club_role_category_id').on('change', function () {
            const hasCat = !!$(this).val();
            clubSelect.val(null).trigger('change');

            if (hasCat) {
                $('#club_id').prop('disabled', false);
                // $('#club_id').select2('open'); // boleh dihapus kalau tidak mau auto-open
            } else {
                $('#club_id').prop('disabled', true);
            }
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

    async function edit(id_official){
        try {
            const res = await fetch(`{{ url('findOfficialById') }}/${id_official}`, {
                method:'GET'
            });
            if(!res.ok) throw new Error('Terjadi kesalahan pada server');

            const result = await res.json();
            if(!result.status) throw new Error(result.message || 'Gagal mendapatkan data');
            const official = result.data.official;
            const categoryId = result.data.category_id;
            const club = result.data.club;
            // isi data ke form
            $('#club_role_category_id').val(categoryId).trigger('change');
            // preload klub
            if(club){
                const option = new Option(`[${club.club_code}] ${club.club_name}`, club.id, true, true);
                $('#club_id').append(option).trigger('change');
                $('#club_id').prop('disabled', false);
            }
            $('#name').val(official.name);
            $('#gender').val(official.gender);
            $('#current_city').val(official.current_city);
            $('#current_province').val(official.current_province);
            $('#license').val(official.license);
            $('#official_id').val(id_official);
            // preview foto
            if(official.foto){
                const img = document.getElementById('fotoPreview');
                const placeholder = document.getElementById('galleryPlaceholder');
                img.src = '/storage/'+official.foto;
                img.classList.remove('d-none');
                if(placeholder) placeholder.classList.add('d-none');
            }
            $('#modalTitle').text('Edit Official');
            $('#modalOfficial').modal('show');
        } catch (error) {
            Toast.fire({
                icon:'error',
                title:error.message ||'Gagal mendapatkan data'
            });
        }
    }

    function destroy(id_official){
        Swal.fire({
            title:'Konfirmasi Hapus',
            text:'Apakah anda yakin ingin menghapus data official ini?',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Ya, Hapus',
            cancelButtonText:'Batal'
        }).then(async (result) => {
            if(result.isConfirmed){
                showSpinner();
                try {
                    const res = await fetch("{{ route('official.destroy','id:') }}".replace('id:', id_official), {
                        method:'DELETE',
                        headers:{
                            'X-CSRF-TOKEN':"{{ csrf_token() }}",
                        }
                    });
                    if(!res.ok) throw new Error('Terjadi kesalahan pada server');

                    const result = await res.json();

                    hideSpinner();
                    if(!result.status) throw new Error(result.message || 'Gagal menghapus data');

                    Toast.fire({
                        icon:'success',
                        title:result.message || 'Sukses menghapus data'
                    });
                    table.ajax.reload(null, false);
                } catch (error) {
                    hideSpinner();
                    Toast.fire({
                        icon:'error',
                        title:error.message ||'Gagal menghapus data'
                    });
                }
            }
        });
    }

    $('#modalOfficial').on('hidden.bs.modal', function(){
        // reset form saat modal ditutup
        const form = document.getElementById('formOfficial');
        form.reset();

        // reset preview gambar
        const img = document.getElementById('fotoPreview');
        const placeholder = document.getElementById('galleryPlaceholder');

        img.src = '';
        img.classList.add('d-none');
        if(placeholder) placeholder.classList.remove('d-none');

        // reset select2 klub
        $('#club_role_category_id').val(null).trigger('change');
        $('#club_id').val(null).trigger('change').prop('disabled', true);
    });

    $('#formOfficial').on('submit', async function(e){
        e.preventDefault();
        showSpinner();

        let formData = new FormData(this);
        try {
            const res = await fetch("{{ route('official.store') }}", {
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':"{{ csrf_token() }}",
                },
                body:formData
            });
            if(!res.ok) throw new Error('Terjadi kesalahan pada server');

            const result = await res.json();

            hideSpinner();
            if(!result.status) throw new Error(result.message || 'Gagal menyimpan data');

            Toast.fire({
                icon:'success',
                title:result.message || 'Sukses menyimpan data'
            });
            table.ajax.reload(null, false);
            $('#modalOfficial').modal('hide');
        } catch (error) {
            hideSpinner();
            Toast.fire({
                icon:'error',
                title:error.message ||'Gagal menyimpan data'
            });
            table.ajax.reload(null, false);
        }
    });
</script>
@endpush
