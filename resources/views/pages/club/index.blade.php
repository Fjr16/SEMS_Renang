@extends('layouts.main')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Manajemen Klub</h2>
            <p class="text-muted mb-0">Kelola data Klub yang terdaftar dalam sistem</p>
        </div>
        @can('Master Setting.Klub-Tambah')
        <div class="mt-3 mt-md-0" d-flex gap-2>
            <button data-bs-toggle="modal" data-bs-target="#modalImport" class="btn btn-success">
                <i class="bi bi-file-earmark-arrow-up me-1"></i> Import Excel
            </button>
            <button data-bs-toggle="modal" data-bs-target="#modalClub" class="btn btn-primary" onclick="$('#modalTitle').text('Tambah Klub'); $('#club_id').val(''); document.getElementById('form-submit').reset();">
                <i class="bi bi-plus-circle me-1"></i> Tambah Klub
            </button>
        </div>
        @endcan
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table id="clubTable" class="table table-striped align-middle">
                <thead>
                    <tr>
                        @canany(['Master Setting.Klub-Ubah', 'Master Setting.Klub-Hapus'])
                        <th>Aksi</th>
                        @endcanany
                        <th>Logo</th>
                        <th>Kategori</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Kota</th>
                        <th>Provinsi</th>
                        <th>Penanggung Jawab (PJ)</th>
                        <th>HP PJ</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalClub" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <form id="form-submit">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Klub</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="hidden" name="club_id" id="club_id">
                        <label class="form-label" for="team_type">Kategori</label>
                        <select class="form-control" name="team_type" id="team_type">
                            @foreach ($data as $category)
                                <option value="{{ $category->value }}" @selected(old('team_type') == $category->value)>{{ $category->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label" for="club_code">Kode Klub</label>
                                <input type="text" class="form-control" id="club_code" name="club_code">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="club_name">Nama Klub</label>
                                <input type="text" class="form-control" id="club_name" name="club_name">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label" for="club_lead">Penanggung Jawab (PJ)</label>
                                <input type="text" class="form-control" id="club_lead" name="club_lead">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label" for="lead_phone">HP Penanggung Jawab (PJ)</label>
                                <input type="text" max="12" class="form-control" id="lead_phone" name="lead_phone">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="club_city">Kota</label>
                        <input type="text" name="club_city" id="club_city" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="club_province">Provinsi</label>
                        <input type="text" class="form-control" id="club_province" name="club_province">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="club_logo">Logo</label>
                        <input type="file" class="form-control" name="club_logo" id="club_logo">
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
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
                        <h5 class="modal-title">Import Data Klub</h5>
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
                                    Download template lalu isi data klub sesuai format
                                </p>
                            </div>
                            <a href="{{ route('klub.template') }}" class="btn btn-sm btn-success flex-shrink-0">
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
                                    Format: .xlsx, .xls, .csv
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
            $('#club_id').val('');
            table = $('#clubTable').DataTable({
                processing :true,
                serverSide :true,
                ajax :"{{ route('club.data') }}",
                columnDefs:[
                    {target:0, className:'dt-actions'},
                    {target:1, className:'dt-fotos'}
                ],
                columns:[
                    @canany(['Master Setting.Klub-Ubah', 'Master Setting.Klub-Hapus'])
                    {data:'action', name:'action', className:'text-center', orderable:false, searchable:false},
                    @endcanany
                    {data:'club_logo', name:'club_logo',defaultContent:'-', className:'text-center', orderable:false, searchable:false},
                    {data:'tipe_klub', name:'team_type',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                    {data:'club_code', name:'club_code',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                    {data:'club_name', name:'club_name',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                    {data:'club_city', name:'club_city',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                    {data:'club_province', name:'club_province',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                    {data:'club_lead', name:'club_lead',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                    {data:'lead_phone', name:'lead_phone',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                ],
                order: [[2, 'desc']],
            });
        });

        $('#form-submit').on('submit', async function(e){
            e.preventDefault();
            showSpinner();

            let formData = new FormData(this);

            try {
                const res = await fetch("{{ route('club.store') }}", {
                    method:'POST',
                    headers: {
                        'X-CSRF-TOKEN' : "{{ csrf_token() }}",
                        'Accept' : 'application/json'
                    },
                    body:formData,
                });


                const result = await res.json();
                if(!res.ok) {
                    throw new Error(result.message || "Terjadi kesalahan pada server");
                }

                hideSpinner();
                if (result.status) {
                    table.ajax.reload();
                    $('#modalClub').modal('hide');
                    this.reset();
                    Toast.fire({
                        icon: 'success',
                        title: result.message || 'Success'
                    });
                }else{
                    console.log(result.message);
                    Toast.fire({
                        icon: 'error',
                        title: result.message || 'Error'
                    });
                }

            } catch (error) {
                hideSpinner();
                console.log(error.message);
                Toast.fire({
                    icon: 'error',
                    title: error.message || 'Terjadi Kesalahan'
                });
            }
        });

        function edit(element){
            $('#modalTitle').text('Edit Klub');
            $('#form-submit')[0].reset();
            const tr = $(element).closest('tr');
            const data = table.row(tr).data();

            $('#club_id').val(data.id);
            $('#team_type').val(data.team_type);
            $('#club_code').val(data.club_code);
            $('#club_name').val(data.club_name);
            $('#club_lead').val(data.club_lead);
            $('#lead_phone').val(data.lead_phone);
            $('#club_province').val(data.club_province);
            $('#club_city').val(data.club_city);
            $('#modalClub').modal('show');
        }

        async function destroy(element){
            const { isConfirmed } = await Swal.fire({
                title: "Konfirmasi Hapus",
                text: "Apakah anda yakin ingin menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                cancelButtonText:'Batal',
                confirmButtonText: "Ya, Hapus!"
            });

            if(!isConfirmed) return;

            try {
                const clubId = element.dataset.id;
                const url = "{{ route('club.destroy', ':id') }}".replace(':id', clubId);
                const res = await fetch(url, {
                    method:'DELETE',
                    headers: {
                        'X-CSRF-TOKEN' : '{{ csrf_token() }}',
                        'Accept': 'application/json',
                    }
                });
                const result = await res.json();
                if (!res.ok) {
                    throw new Error(result.message || "Terjadi Kesalahan Server");
                }
                table.ajax.reload();
                if (result.status) {
                    Toast.fire({
                        icon:'success',
                        title:result.message || 'Berhasil hapus data'
                    })
                }else{
                    Toast.fire({
                        icon:'error',
                        title:result.message ?? 'Gagal Hapus Data'
                    })
                }
            } catch (error) {
                table.ajax.reload();
                console.log(error.message);
                Toast.fire({
                    icon:'error',
                    title:error.message || 'Terjadi kesalahan server'
                })
            }
        }

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
                url: "{{ route('klub.import') }}",
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
                    tampilkanErrorImport(xhr);
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

        function tampilkanErrorImport(xhr) {
            if (xhr.status !== 422) {
                $('#importMessage').html(
                    `<div class="alert alert-danger">${xhr.responseJSON?.message || 'Terjadi kesalahan.'}</div>`
                );
                return;
            }

            const res = xhr.responseJSON;
            const errors = res.errors;
            let html = '<div class="alert alert-danger">';

            if (Array.isArray(errors)) {
                // Baris import gagal
                html += `<p class="mb-1">${res.message}</p><ul class="mb-0">`;
                errors.forEach(item => {
                    html += `<li>Baris ${item.baris} atlet:${item.data?.nama ?? '-'} (${item.kolom}): ${item.pesan}</li>`;
                });
                html += '</ul>';

            } else if (errors && typeof errors === 'object') {
                // Validasi field gagal (misal file kosong/format salah)
                html += `<p class="mb-1">${res.message}</p><ul class="mb-0">`;
                Object.keys(errors).forEach(key => {
                    errors[key].forEach(msg => {
                        html += `<li>${msg}</li>`;
                    });
                });
                html += '</ul>';

            } else {
                html += `<p class="mb-0">${res.message}</p>`;
            }

            html += '</div>';
            $('#importMessage').html(html);
        }

        document.getElementById('modalImport').addEventListener('hidden.bs.modal', function (e) {
            document.getElementById('importMessage').innerHTML  = '';
        });
    </script>
@endpush
