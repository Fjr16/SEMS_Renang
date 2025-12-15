@extends('layouts.main')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Manajemen Klub</h2>
            <p class="text-muted mb-0">Kelola data Klub yang terdaftar dalam sistem</p>
        </div>
        <div class="mt-3 mt-md-0">
            <button data-bs-toggle="modal" data-bs-target="#modalClub" class="btn btn-primary" onclick="$('#modalTitle').text('Tambah Klub'); $('#club_id').val(''); document.getElementById('form-submit').reset();">
                <i class="bi bi-plus-circle me-1"></i> Tambah Klub
            </button>
        </div>
    </div>
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table id="clubTable" class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Aksi</th>
                        <th>Logo</th>
                        <th>Kategori</th>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Alamat</th>
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
                        <label class="form-label" for="club_category_id">Kategori</label>
                        <select class="form-control" name="club_category_id" id="club_category_id">
                            @foreach ($data as $category)
                                <option value="{{ $category->id }}" @selected(old('club_category_id') == $category->id)>{{ '[' . $category->code .']' . ' ' . $category->name }}</option>
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
                        <label class="form-label" for="club_province">Provinsi</label>
                        <input type="text" class="form-control" id="club_province" name="club_province">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="club_address">Alamat</label>
                        <textarea name="club_address" id="club_address" class="form-control" rows="1"></textarea>
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
                    {data:'action', name:'action', className:'text-center', orderable:false, searchable:false},
                    {data:'club_logo', name:'club_logo',defaultContent:'-', className:'text-center', orderable:false, searchable:false},
                    {data:'club_role_category_id', name:'category.name',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                    {data:'club_code', name:'club_code',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                    {data:'club_name', name:'club_name',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                    {data:'club_address', name:'club_address',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                    {data:'club_province', name:'club_province',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                    {data:'club_lead', name:'club_lead',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                    {data:'lead_phone', name:'lead_phone',defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                ],
                order: [[2, 'desc']],

                // initComplete: function(settings, json) {
                //     // dipanggil SEKALI setelah table pertama kali selesai inisialisasi
                //     // cocok untuk: pasang event di search, pindahin filter, dll
                //     let api = this.api();
                //     // contoh: custom search
                //     $('#custom-search').on('keyup', function () {
                //         api.search(this.value).draw();
                //     });
                // },

                // drawCallback: function(settings) {
                //     // dipanggil SETIAP kali table redraw
                //     // cocok untuk: re-init tooltip, hitung summary, toggle tombol, dll
                //     let api = this.api();

                //     // contoh: hitung total kolom halaman ini
                //     let total = api.column(3, {page:'current'}).data().reduce(function(a,b){
                //         return (parseFloat(a)||0) + (parseFloat(b)||0);
                //     }, 0);

                //     $('#totalPage').text(total);
                // },

                // rowCallback: function(row, data, index) {
                //     // jalan setiap row dibentuk
                //     if (data.status === 'cancelled') {
                //         $(row).addClass('table-danger');
                //     }
                // }
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
                        'X-CSRF-TOKEN' : "{{ csrf_token() }}"
                    },
                    body:formData,
                });

                if(!res.ok) {
                    throw new Error("Terjadi kesalahan pada server");
                }

                const result = await res.json();

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
            $('#club_category_id').val(data.category_id);
            $('#club_code').val(data.club_code);
            $('#club_name').val(data.club_name);
            $('#club_lead').val(data.club_lead);
            $('#lead_phone').val(data.lead_phone);
            $('#club_province').val(data.club_province);
            $('#club_address').val(data.club_address);
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
                    }
                });
                if (!res.ok) {
                    throw new Error("Terjadi Kesalahan Server");
                }
                const result = await res.json();
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
