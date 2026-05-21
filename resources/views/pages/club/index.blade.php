@extends('layouts.main')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Manajemen Klub</h2>
            <p class="text-muted mb-0">Kelola data Klub yang terdaftar dalam sistem</p>
        </div>
        @can('Master Setting.Klub-Tambah')
        <div class="mt-3 mt-md-0">
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
    </script>
@endpush
