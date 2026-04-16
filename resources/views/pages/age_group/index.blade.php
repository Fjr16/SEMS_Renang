@extends('layouts.main')

@section('content')
  <!-- Header Page -->
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">Master Kelompok Umur</h2>
      <p class="text-muted mb-0">Kelola master data kelompok umur yang akan digunakan dalam acara / event</p>
    </div>
    <div class="mt-3 mt-md-0">
      <button data-bs-toggle="modal" data-bs-target="#modalKelompokUmur" class="btn btn-primary" onclick="$('#modalTitle').text('Tambah Kelompok Umur'); $('#age_group_id').val('');">
        <i class="bi bi-plus-circle me-1"></i> Tambah KU
      </button>
    </div>
  </div>

  <!-- Card Content -->
  <div class="card shadow-sm border-0">
    <div class="card-body">
        <table id="ageGroupTable" class="table table-striped align-middle">
            <thead class="table-light">
            <tr>
                <th>Aksi</th>
                <th>Label</th>
                <th>Umur Minimal</th>
                <th>Umur Maksimal</th>
            </tr>
            </thead>
        </table>
    </div>
  </div>

  <!-- Modal -->
  <div class="modal fade" id="modalKelompokUmur" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="formKelompokUmur">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Tambah Kelompok Umur</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row">
                <input type="hidden" name="age_group_id" id="age_group_id">
                <div class="mb-3">
                    <label class="form-label" for="name">Nama / Label</label>
                    <input type="text" class="form-control" name="label"  id="label" required>
                </div>
                <div class="row mb-3">
                    <label class="form-label">Rentang Usia</label>
                    <div class="col-sm-6 col-12 mb-3 mb-sm-0">
                        <input type="text" min="0" class="form-control" name="min_age" id="min_age" placeholder="Minimal Usia" oninput="this.value = this.value.replace('/[^0-9]/g', '')">
                    </div>
                    <div class="col-sm-6 col-12">
                        <input type="text" min="0" class="form-control" name="max_age" id="max_age" placeholder="Maksimal Usia" oninput="this.value = this.value.replace('/[^0-9]/g', '')">
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
@endsection

@push('scripts')
<script>
    var table;
    $(document).ready(function(){
        table = $('#ageGroupTable').DataTable({
            processing:true,
            serverSide:true,
            columnDefs: [
                { targets: 0, className: 'dt-actions',  }, // kolom Aksi
                { targets: 1, className: 'dt-fotos',  } // kolom foto
            ],
            ajax:"{{ route('age.group.data') }}",
            columns:[
                {data:'action', name:'action', className:'text-center', orderable:false, searchable:false},
                {data:'label', name:'label', className:'text-center', orderable:true, searchable:true},
                {data:'min_age', name:'min_age', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
                {data:'max_age', name:'max_age', defaultContent:'-', className:'text-center', orderable:true, searchable:true},
            ],
            order:[[2,'asc']]
        });
    });

    function edit(element){
        // isi data ke form
        $('#age_group_id').val(element.dataset.id);
        $('#label').val(element.dataset.label);
        $('#min_age').val(element.dataset.min_age);
        $('#max_age').val(element.dataset.max_age);

        $('#modalTitle').text('Edit Kelompok Umur');
        $('#modalKelompokUmur').modal('show');
    }

    function destroy(id_age_group){
        Swal.fire({
            title:'Konfirmasi Hapus',
            text:'Apakah anda yakin ingin menghapus data kelompok umur ini?',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Ya, Hapus',
            cancelButtonText:'Batal'
        }).then(async (result) => {
            if(result.isConfirmed){
                showSpinner();
                try {
                    const res = await fetch("{{ route('age.group.destroy','id:') }}".replace('id:', id_age_group), {
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

    $('#modalKelompokUmur').on('hidden.bs.modal', function(){
        const form = document.getElementById('formKelompokUmur');
        form.reset();
    });

    $('#formKelompokUmur').on('submit', async function(e){
        e.preventDefault();
        showSpinner();

        let formData = new FormData(this);
        try {
            const res = await fetch("{{ route('age.group.store') }}", {
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
            $('#modalKelompokUmur').modal('hide');
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
