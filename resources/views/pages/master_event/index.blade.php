@extends('layouts.main')

@section('content')
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">Master Event</h2>
      <p class="text-muted mb-0">Kelola master data event / nomor lomba</p>
    </div>
    @can('Master Setting.Event-Tambah')
    <div class="mt-3 mt-md-0">
      <button data-bs-toggle="modal" data-bs-target="#modalMasterEvent" class="btn btn-primary" onclick="openCreate()">
        <i class="bi bi-plus-circle me-1"></i> Tambah Event
      </button>
    </div>
    @endcan
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body">
        <table id="masterEventTable" class="table table-striped align-middle" style="width:100%">
            <thead class="table-light">
            <tr>
                @canany(['Master Setting.Event-Ubah', 'Master Setting.Event-Hapus'])
                <th>Aksi</th>
                @endcanany
                <th>Label</th>
                <th>Gaya</th>
                <th>Jarak</th>
                <th>Kelamin</th>
                <th>Kelompok Umur</th>
                <th>Tipe</th>
                <th>Alat</th>
                <th>Maks Atlet</th>
            </tr>
            </thead>
        </table>
    </div>
  </div>

  <div class="modal fade" id="modalMasterEvent" tabindex="-1">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <form id="formMasterEvent">
          <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Tambah Master Event</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="row g-3">
                <input type="hidden" name="master_event_id" id="master_event_id">
                <div class="col-md-6 col-12">
                    <label class="form-label">Tipe Perlombaan</label>
                    <select class="form-select" id="event_type" name="event_type" required onchange="toggleRelayField()">
                        @foreach ($enumEType as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-12">
                    <label class="form-label">Gaya Perlombaan</label>
                    <select class="form-select" id="stroke" name="stroke" required>
                        @foreach ($enumStroke as $stroke)
                            <option value="{{ $stroke->value }}">{{ $stroke->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">Jarak (meter)</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="distance" name="distance" required min="1">
                        <span class="input-group-text">m</span>
                    </div>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">Jenis Kelamin</label>
                    <select class="form-select" id="gender" name="gender" required>
                        @foreach ($enumGender as $gender)
                            <option value="{{ $gender->value }}">{{ $gender->label() }}</option>
                        @endforeach
                        <option value="mixed">Campuran</option>
                    </select>
                </div>
                <div class="col-md-4 col-12">
                    <label class="form-label">Kelompok Umur</label>
                    <select class="form-select" id="age_group_id" name="age_group_id" required>
                        @foreach ($ageGroups as $ku)
                            <option value="{{ $ku->id }}">{{ $ku->label }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6 col-12">
                    <label class="form-label">Alat (Equipment)</label>
                    <select class="form-select" id="equipment" name="equipment">
                        <option value="">Reguler (Tanpa Alat)</option>
                        <option value="papan">Papan</option>
                        <option value="fins">Fins</option>
                    </select>
                </div>
                <div class="col-md-6 col-12" id="relayFieldWrapper" style="display:none">
                    <label class="form-label">Jumlah Atlet Estafet</label>
                    <input type="number" class="form-control" id="max_relay_athletes" name="max_relay_athletes" min="2" max="10" placeholder="Contoh: 4">
                </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
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
        table = $('#masterEventTable').DataTable({
            processing:true,
            serverSide:true,
            ajax:"{{ route('master.event.data') }}",
            columns:[
                @canany(['Master Setting.Event-Ubah', 'Master Setting.Event-Hapus'])
                {data:'action', name:'action', className:'text-center', orderable:false, searchable:false},
                @endcanany
                {data:'label', name:'label', orderable:true, searchable:true},
                {data:'stroke', name:'stroke', className:'text-center', orderable:true, searchable:true},
                {data:'distance', name:'distance', className:'text-center', orderable:true, searchable:true},
                {data:'gender', name:'gender', className:'text-center', orderable:true, searchable:false},
                {data:'age_group_id', name:'age_group_id', className:'text-center', orderable:true, searchable:true},
                {data:'event_type', name:'event_type', className:'text-center', orderable:true, searchable:false},
                {data:'equipment', name:'equipment', className:'text-center', orderable:true, searchable:true, defaultContent:'-'},
                {data:'max_relay_athletes', name:'max_relay_athletes', className:'text-center', orderable:true, searchable:false, defaultContent:'-'},
            ],
            order:[[1,'asc']]
        });
    });

    function toggleRelayField(){
        var isRelay = document.getElementById('event_type').value === '{{ \App\Enums\EventType::estafet->value }}';
        document.getElementById('relayFieldWrapper').style.display = isRelay ? '' : 'none';
        if(!isRelay) document.getElementById('max_relay_athletes').value = '';
    }

    function openCreate(){
        $('#formMasterEvent')[0].reset();
        $('#master_event_id').val('');
        $('#modalTitle').text('Tambah Master Event');
        toggleRelayField();
    }

    function edit(element){
        $('#master_event_id').val(element.dataset.id);
        $('#distance').val(element.dataset.distance);
        $('#stroke').val(element.dataset.stroke);
        $('#gender').val(element.dataset.gender);
        $('#age_group_id').val(element.dataset.age_group_id);
        $('#event_type').val(element.dataset.eventType);
        $('#equipment').val(element.dataset.equipment || '');
        $('#modalTitle').text('Edit Master Event');
        toggleRelayField();
        $('#max_relay_athletes').val(element.dataset.maxRelay || '');
        $('#modalMasterEvent').modal('show');
    }

    function destroy(id){
        Swal.fire({
            title:'Konfirmasi Hapus',
            text:'Apakah anda yakin ingin menghapus master event ini?',
            icon:'warning',
            showCancelButton:true,
            confirmButtonText:'Ya, Hapus',
            cancelButtonText:'Batal'
        }).then(async (result) => {
            if(result.isConfirmed){
                showSpinner();
                try {
                    const res = await fetch("{{ route('master.event.destroy','id:') }}".replace('id:', id), {
                        method:'DELETE',
                        headers:{
                            'X-CSRF-TOKEN':"{{ csrf_token() }}",
                            'Accept': 'application/json',
                        }
                    });

                    const data = await res.json();
                    if(!res.ok) throw new Error(data.message || 'Terjadi kesalahan pada server');

                    hideSpinner();
                    if(!data.status) throw new Error(data.message || 'Gagal menghapus data');

                    Toast.fire({
                        icon:'success',
                        title:data.message || 'Sukses menghapus data'
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

    $('#modalMasterEvent').on('hidden.bs.modal', function(){
        document.getElementById('formMasterEvent').reset();
    });

    $('#formMasterEvent').on('submit', async function(e){
        e.preventDefault();
        showSpinner();

        let formData = new FormData(this);
        // Kosongkan max_relay_athletes jika bukan relay
        if(document.getElementById('relayFieldWrapper').style.display === 'none'){
            formData.delete('max_relay_athletes');
        }
        try {
            const res = await fetch("{{ route('master.event.store') }}", {
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN':"{{ csrf_token() }}",
                    'Accept': 'application/json',
                },
                body:formData
            });

            const data = await res.json();
            if(!res.ok) throw new Error(data.message || 'Terjadi kesalahan pada server');

            hideSpinner();
            if(!data.status) throw new Error(data.message || 'Gagal menyimpan data');

            Toast.fire({
                icon:'success',
                title:data.message || 'Sukses menyimpan data'
            });
            table.ajax.reload(null, false);
            $('#modalMasterEvent').modal('hide');
        } catch (error) {
            hideSpinner();
            Toast.fire({
                icon:'error',
                title:error.message ||'Gagal menyimpan data'
            });
        }
    });
</script>
@endpush
