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
    @can('Master Setting.Atlet-Tambah')
    {{-- <div class="mt-3 mt-md-0">
      <button data-bs-toggle="modal" data-bs-target="#modalAthlete" class="btn btn-primary" onclick="$('#modalTitle').text('Tambah Atlet'); $('#athlete_id').val('');">
        <i class="bi bi-plus-circle me-1"></i> Tambah Atlet
      </button>
    </div> --}}

    <div class="mt-3 mt-md-0 d-flex gap-2">
        <button data-bs-toggle="modal" data-bs-target="#modalImport" class="btn btn-success">
            <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Excel
        </button>
        <button data-bs-toggle="modal" data-bs-target="#modalAthlete" class="btn btn-primary"
                onclick="$('#modalTitle').text('Tambah Atlet'); $('#athlete_id').val('');">
            <i class="bi bi-plus-circle me-1"></i> Tambah Atlet
        </button>
    </div>
    @endcan
  </div>

  <!-- Card Content -->
  <div class="card shadow-sm border-0">
    <div class="card-body">
        <table id="atletTable" class="table table-striped align-middle">
            <thead class="table-light">
            <tr>
                @canany(['Master Setting.Atlet-Ubah', 'Master Setting.Atlet-Hapus'])
                <th>Aksi</th>
                @endcanany
                <th>Foto</th>
                <th>Atlet</th>
                <th>No Registrasi</th>
                <th>Klub Sekarang</th>
                <th>BOD</th>
                <th>Jenis Kelamin</th>
                <th>Status</th>
            </tr>
            </thead>
        </table>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modalAthlete" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="formAthlete" enctype="multipart/form-data">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Tambah Atlet</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
                <div class="row">
                    <div class="col-6">
                        <input type="hidden" name="athlete_id" id="athlete_id">
                        <label class="form-label" for="team_type">Kategori Klub</label>
                        <select name="team_type" id="team_type" class="form-control form-control-md">
                            <option value=""></option>
                            @foreach ($clubCategories as $cat)
                                <option value="{{ $cat->value }}" @selected(old('team_type') == $cat->value)>{{ $cat->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label" for="club_id">Klub</label>
                        <select name="club_id" id="club_id" class="form-control" style="width: 100%" disabled></select>
                    </div>
                </div>
            </div>
            <div class="mb-0">
                <div class="row">
                    <div class="col-sm-8 col-md-7 col-6">
                        <div class="mb-3">
                            <label class="form-label" for="name">Nama Lengkap</label>
                            <input type="text" class="form-control" name="name"  id="name" required>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-6 col-md-6 col-12 mb-3 mb-sm-0">
                                <label class="form-label" for="gender">Gender</label>
                                <select class="form-control" name="gender" id="gender">
                                    @foreach ($genders as $gd)
                                        <option value="{{ $gd->value }}" @selected(old('gender') == $gd->value)>{{ $gd->label() }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-6 col-12">
                                <label class="form-label" for="bod">Tanggal Lahir</label>
                                <input type="text" class="form-control tanggal" name="bod" id="bod" required placeholder="Pilih tanggal lahir">
                            </div>
                        </div>
                        {{-- <div class="mb-3">
                            <label class="form-label" for="registration_number">Nomor Registrasi</label>
                            <input type="text" name="registration_number" id="registration_number" class="form-control">
                        </div> --}}
                        <div class="mb-3">
                            <label class="form-label" for="kota">Kota</label>
                            <input type="text" name="kota" id="kota" class="form-control" maxlength="50">
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
                <div class="mb-3">
                    <label class="form-label" for="provinsi">Provinsi</label>
                    <input type="text" name="provinsi" id="provinsi" class="form-control" maxlength="50">
                </div>
                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="status" name="status" value="active" checked>
                        <label class="form-check-label" for="status">Status Aktif</label>
                    </div>
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- modal import --}}
    <div class="modal fade" id="modalImport" tabindex="-1">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form id="formImport" enctype="multipart/form-data">
                    @csrf
                    @method('POST')
                    <div class="modal-header">
                        <h5 class="modal-title">Import Data Atlet</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">

                        {{-- Download Template --}}
                        <div class="alert d-flex align-items-center gap-3 mb-4"
                            style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px">
                            <div class="flex-shrink-0 d-flex align-items-center justify-content-center rounded-3"
                                style="width:40px;height:40px;background:#dcfce7">
                                <i class="bi bi-file-earmark-excel" style="color:#16a34a;font-size:18px"></i>
                            </div>
                            <div class="flex-grow-1">
                                <p class="fw-semibold mb-0" style="font-size:13px;color:#15803d">Template Excel</p>
                                <p class="mb-0 text-muted" style="font-size:12px">
                                    Download template lalu isi data atlet sesuai format
                                </p>
                            </div>
                            <a href="{{ route('atlet.template') }}" class="btn btn-sm btn-success flex-shrink-0">
                                <i class="bi bi-download me-1"></i> Download
                            </a>
                        </div>

                        {{-- Upload Area --}}
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Upload File</label>

                            {{-- Drop Zone --}}
                            <div id="importDropZone"
                                onclick="document.getElementById('importFile').click()"
                                style="border:2px dashed #ced4da;border-radius:10px;padding:32px 16px;
                                        text-align:center;cursor:pointer;transition:all .2s;background:#f8fafc">
                                <i class="bi bi-cloud-arrow-up text-muted" style="font-size:2rem"></i>
                                <p class="mb-1 fw-semibold text-muted mt-2" style="font-size:14px">
                                    Klik atau drag & drop file di sini
                                </p>
                                <p class="mb-0 text-muted" style="font-size:12px">
                                    Format: .xlsx, .xls, .csv — Maks. 5MB
                                </p>
                            </div>

                            {{-- File Info (muncul setelah pilih file) --}}
                            <div id="importFileInfo" class="d-none mt-3">
                                <div class="d-flex align-items-center gap-3 p-3 rounded-3"
                                    style="background:#f1f5f9;border:1px solid #e2e8f0">
                                    <i class="bi bi-file-earmark-spreadsheet text-success" style="font-size:1.5rem"></i>
                                    <div class="flex-grow-1 min-w-0">
                                        <p class="fw-semibold mb-0 text-truncate" id="importFileName" style="font-size:13px"></p>
                                        <p class="text-muted mb-0" id="importFileSize" style="font-size:12px"></p>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-light" onclick="resetImportFile()">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>

                            <input type="file" id="importFile" name="file"
                                accept=".xlsx,.xls,.csv" class="d-none"
                                onchange="handleImportFile(event)">
                        </div>

                        {{-- Catatan --}}
                        <div class="text-muted" style="font-size:12px">
                            <i class="bi bi-info-circle me-1"></i>
                            Pastikan format file sesuai template. Data yang sudah ada tidak akan diduplikasi.
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-success" id="btnImportSubmit" disabled>
                            <i class="bi bi-file-earmark-arrow-up me-1"></i> Import
                        </button>
                    </div>
                    <div id="importMessage"></div>
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
            ajax:"{{ route('atlet.data') }}",
            columns:[
                @canany(['Master Setting.Atlet-Ubah', 'Master Setting.Atlet-Hapus'])
                {data:'action', name:'action', className:'text-center dt-actions', orderable:false, searchable:false},
                @endcanany
                {data:'foto', name:'foto', className:'text-center dt-fotos', orderable:false, searchable:false},
                {data:'codeName', name:'name', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'id', name:'id', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'clubDesc', name:'clubDesc', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'bod', name:'bod', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'genderAttr', name:'gender', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'status', name:'status', className:'text-center', orderable:true, searchable:true},
            ],
            order:[[3,'asc']]
        });

        $('#team_type').select2({
            width:'100%',
            placeholder:'Pilih Kategori Klub',
            allowClear:true,
            dropdownParent: $('#modalAthlete') // id modal kamu
        });

        const clubSelect = $('#club_id').select2({
            width:'100%',
            placeholder:'Pilih Klub',
            allowClear:true,
            dropdownParent: $('#modalAthlete'),
            minimumInputLength:0,
            ajax:{
                url:"{{ route('getClubByCategory') }}",
                dataType:'json',
                delay:250,
                data:function(params){
                    return {
                        q:params.term || '',
                        page:params.page || 1,
                        team_type:$('#team_type').val()
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

        $('#team_type').on('change', function () {
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

    async function edit(id_atlet){
        try {
            const res = await fetch(`{{ url('findAtletById') }}/${id_atlet}`, {
                method:'GET'
            });
            if(!res.ok) throw new Error('Terjadi kesalahan pada server');

            const result = await res.json();
            if(!result.status) throw new Error(result.message || 'Gagal mendapatkan data');
            const atlet = result.data.athlete;
            const club = result.data.club;
            // isi data ke form
            $('#team_type').val(club.team_type ?? '').trigger('change');
            // preload klub
            if(club){
                const option = new Option(`[${club.club_code}] ${club.club_name}`, club.id, true, true);
                $('#club_id').append(option).trigger('change');
                $('#club_id').prop('disabled', false);
            }
            $('#name').val(atlet.name);
            $('#gender').val(atlet.gender);
            $('#bod')[0]._flatpickr.setDate(atlet.bod);
            // $('#registration_number').val(atlet.registration_number);
            $('#status').prop('checked', atlet.status === 'active');
            $('#athlete_id').val(id_atlet);
            $('#kota').val(atlet.kota);
            $('#provinsi').val(atlet.provinsi);
            // preview foto
            if(atlet.foto){
                const img = document.getElementById('fotoPreview');
                const placeholder = document.getElementById('galleryPlaceholder');
                img.src = '/storage/'+atlet.foto;
                img.classList.remove('d-none');
                if(placeholder) placeholder.classList.add('d-none');
            }
            $('#modalTitle').text('Edit Atlet');
            $('#modalAthlete').modal('show');
        } catch (error) {
            Toast.fire({
                icon:'error',
                title:error.message ||'Gagal mendapatkan data'
            });
        }
    }

    function destroy(id_atlet){
        Swal.fire({
            title:'Konfirmasi Hapus',
            text:'Apakah anda yakin ingin menghapus data atlet ini?',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Ya, Hapus',
            cancelButtonText:'Batal'
        }).then(async (result) => {
            if(result.isConfirmed){
                showSpinner();
                try {
                    const res = await fetch("{{ route('atlet.destroy','id:') }}".replace('id:', id_atlet), {
                        method:'DELETE',
                        headers:{
                            'X-CSRF-TOKEN':"{{ csrf_token() }}",
                            'Accept': 'application/json',
                        }
                    });

                    const result = await res.json();
                    if(!res.ok) throw new Error(result.message || 'Terjadi kesalahan pada server');

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

    $('#modalAthlete').on('hidden.bs.modal', function(){
        // reset form saat modal ditutup
        const form = document.getElementById('formAthlete');
        form.reset();

        // reset preview gambar
        const img = document.getElementById('fotoPreview');
        const placeholder = document.getElementById('galleryPlaceholder');

        img.src = '';
        img.classList.add('d-none');
        if(placeholder) placeholder.classList.remove('d-none');

        // reset select2 klub
        $('#team_type').val(null).trigger('change');
        $('#club_id').val(null).trigger('change').prop('disabled', true);
    });

    $('#formAthlete').on('submit', async function(e){
        e.preventDefault();
        showSpinner();

        let formData = new FormData(this);
        try {
            const res = await fetch("{{ route('atlet.store') }}", {
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':"{{ csrf_token() }}",
                    'Accept': 'application/json',
                },
                body:formData
            });

            const result = await res.json();

            if(!res.ok) throw new Error(result.message || 'Terjadi kesalahan pada server');

            hideSpinner();
            if(!result.status) throw new Error(result.message || 'Gagal menyimpan data');

            Toast.fire({
                icon:'success',
                title:result.message || 'Sukses menyimpan data'
            });
            table.ajax.reload(null, false);
            $('#modalAthlete').modal('hide');
        } catch (error) {
            hideSpinner();
            Toast.fire({
                icon:'error',
                title:error.message ||'Gagal menyimpan data'
            });
            table.ajax.reload(null, false);
        }
    });

    // import
    function handleImportFile(event) {
        const file = event.target.files[0];

        if (!file) {
            resetImportFile();
            return;
        }

        document.getElementById('importFileInfo').classList.remove('d-none');
        document.getElementById('importFileName').textContent = file.name;
        document.getElementById('importFileSize').textContent =
            (file.size / 1024 / 1024).toFixed(2) + ' MB';

        document.getElementById('btnImportSubmit').disabled = false;
    }
    function resetImportFile() {
        document.getElementById('importFile').value = '';

        document.getElementById('importFileInfo').classList.add('d-none');
        document.getElementById('importFileName').textContent = '';
        document.getElementById('importFileSize').textContent = '';

        document.getElementById('btnImportSubmit').disabled = true;
    }

    const dropZone = document.getElementById('importDropZone');
    const fileInput = document.getElementById('importFile');

    dropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.style.borderColor = '#16a34a';
        dropZone.style.background = '#ecfdf5';
    });

    dropZone.addEventListener('dragleave', function() {
        dropZone.style.borderColor = '#ced4da';
        dropZone.style.background = '#f8fafc';
    });

    dropZone.addEventListener('drop', function(e) {
        e.preventDefault();

        dropZone.style.borderColor = '#ced4da';
        dropZone.style.background = '#f8fafc';

        const files = e.dataTransfer.files;

        if(files.length > 0){
            fileInput.files = files;
            handleImportFile({ target: fileInput });
        }
    });

    $('#formImport').on('submit', function(e){

        e.preventDefault();

        const btn = $('#btnImportSubmit');
        const formData = new FormData(this);

        btn.prop('disabled', true);
        btn.html(`
            <span class="spinner-border spinner-border-sm me-1"></span>
            Importing...
        `);

        $('#importMessage').html('');

        $.ajax({
            url: "{{ route('atlet.import') }}",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,

            success: function(response){

                $('#importMessage').html(`
                    <div class="alert alert-success">
                        ${response.message}
                    </div>
                `);

                setTimeout(() => {

                    $('#modalImport').modal('hide');

                    resetImportFile();

                    if(typeof table !== 'undefined'){
                        table.ajax.reload();
                    }

                }, 1500);
            },

            error: function(xhr){

                let html = '';

                if(xhr.status === 422){

                    const errors = xhr.responseJSON.errors;

                    html += '<div class="alert alert-danger"><ul class="mb-0">';

                    Object.keys(errors).forEach(key => {
                        html += `<li>${errors[key][0]}</li>`;
                    });

                    html += '</ul></div>';

                }else{

                    html = `
                        <div class="alert alert-danger">
                            ${xhr.responseJSON?.message ??
                            'Terjadi kesalahan saat import data'}
                        </div>
                    `;
                }

                $('#importMessage').html(html);
            },

            complete: function(){

                btn.prop('disabled', false);

                btn.html(`
                    <i class="bi bi-file-earmark-arrow-up me-1"></i>
                    Import
                `);
            }
        });

    });
</script>
@endpush
