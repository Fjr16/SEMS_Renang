@extends('layouts.main')

@section('content')
  <!-- Header Page -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">Master Organisasi</h2>
      <p class="text-muted mb-0">Kelola master data organisasi / penyelenggara kompetisi dan acara</p>
    </div>
    <div class="mt-3 mt-md-0">
      <button data-bs-toggle="modal" data-bs-target="#modalOrganisasi" class="btn btn-primary" onclick="$('#modalTitle').text('Tambah Kelompok Umur'); $('#age_group_id').val('');">
        <i class="bi bi-plus-circle me-1"></i> Tambah Organisasi
      </button>
    </div>
  </div>

  <!-- Card Content -->
  <div class="card shadow-sm border-0">
    <div class="card-body">
        <table id="organizationTable" class="table table-striped align-middle">
            <thead class="table-light">
            <tr>
                <th>Aksi</th>
                <th>Nama</th>
                <th>Dibuat Pada</th>
            </tr>
            </thead>
        </table>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modalOrganisasi" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="formOrganisasi">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Tambah Organisasi</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row">
                <input type="hidden" name="organization_id" id="organization_id">
                <div class="mb-3">
                    <label class="form-label" for="name">Nama Organisasi</label>
                    <input type="text" class="form-control" name="name"  id="name" required>
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
@endsection

@push('scripts')
<script>
    var table;
    $(document).ready(function(){
        table = $('#organizationTable').DataTable({
            processing:true,
            serverSide:true,
            columnDefs: [
                { targets: 0, className: 'dt-actions',  }, // kolom Aksi
            ],
            ajax:"{{ route('organizations.data') }}",
            columns:[
                {data:'action', name:'action', orderable:false, searchable:false},
                {data:'name', name:'name', orderable:true, searchable:true},
                {data:'created_at', name:'created_at', defaultContent:'--/--/--', orderable:true, searchable:false},
            ],
            order:[[2,'desc']]
        });
    });

    function edit(element){
        // isi data ke form
        $('#organization_id').val(element.dataset.id);
        $('#name').val(element.dataset.name);

        $('#modalTitle').text('Edit Organisasi');
        $('#modalOrganisasi').modal('show');
    }

    function destroy(id){
        Swal.fire({
            title:'Konfirmasi Hapus',
            text:'Apakah anda yakin ingin menghapus data Organisasi ini?',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Ya, Hapus',
            cancelButtonText:'Batal'
        }).then(async (result) => {
            if(result.isConfirmed){
                showSpinner();
                try {
                    const res = await fetch("{{ route('organizations.destroy','id:') }}".replace('id:', id), {
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

    $('#modalOrganisasi').on('hidden.bs.modal', function(){
        const form = document.getElementById('formOrganisasi');
        form.reset();
    });

    $('#formOrganisasi').on('submit', async function(e){
        e.preventDefault();
        showSpinner();

        let formData = new FormData(this);
        try {
            const res = await fetch("{{ route('organizations.store') }}", {
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
            $('#modalOrganisasi').modal('hide');
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
