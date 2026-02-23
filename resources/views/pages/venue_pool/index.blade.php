@extends('layouts.main')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h2 class="fw-bold mb-1">Lokasi (Venue) & Kolam (Pool)</h2>
    <p class="text-muted mb-0">Kelola venue dan pool dalam satu halaman (klik venue untuk melihat pool)</p>
  </div>
  <div class="mt-3 mt-md-0 d-flex gap-2">
    <button class="btn btn-outline-primary"
      data-bs-toggle="modal" data-bs-target="#modalVenue"
      onclick="openCreateVenue()">
      <i class="bi bi-geo-alt me-1"></i> Tambah Venue
    </button>

    <button class="btn btn-primary"
      onclick="openCreatePool()"
      id="btnAddPool" disabled>
      <i class="bi bi-plus-circle me-1"></i> Tambah Pool
    </button>
  </div>
</div>

<div class="row g-3">
  {{-- KIRI: VENUE LIST --}}
  <div class="col-lg-4">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-2">
          <div>
            <div class="fw-semibold">Daftar Venue</div>
            <div class="text-muted small">Klik 1 venue untuk melihat pool</div>
          </div>
          <span class="badge text-bg-light border" id="venueCount">0</span>
        </div>

        <table id="venueTable" class="table table-striped table-sm w-100">
          <thead>
            <tr>
              <th>Venue</th>
              <th>Status</th>
              <th class="text-center">Aksi</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  {{-- KANAN: DETAIL VENUE + POOL TABLE --}}
  <div class="col-lg-8">
    <div class="card shadow-sm border-0">
      <div class="card-body">
        <div class="d-flex flex-column flex-md-row justify-content-between gap-2">
          <div>
            <div class="fw-bold" id="selectedVenueName">Pilih venue terlebih dahulu</div>
            <div class="text-muted small" id="selectedVenueMeta">—</div>
          </div>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary btn-sm" id="btnEditVenue" onclick="openEditVenue()" disabled>
              <i class="bi bi-pencil-square me-1"></i> Edit Venue
            </button>
            <button class="btn btn-outline-danger btn-sm" id="btnDeleteVenue" onclick="destroyVenue()" disabled>
              <i class="bi bi-trash me-1"></i> Hapus Venue
            </button>
          </div>
        </div>

        <hr class="my-3">

        <div class="d-flex align-items-center justify-content-between mb-2">
          <div class="fw-semibold">Pools di Venue terpilih</div>
          <span class="badge text-bg-light border" id="poolCount">0</span>
        </div>

        <table id="poolTable" class="table table-striped w-100">
          <thead>
            <tr>
              <th class="text-center">Aksi</th>
              <th>Nama Pool</th>
              <th class="text-center">Course</th>
              <th class="text-center">Panjang</th>
              <th class="text-center">Lanes</th>
              <th class="text-center">Kedalaman</th>
              <th class="text-center">Status</th>
            </tr>
          </thead>
        </table>

        <div class="alert alert-info mt-3 mb-0 small" id="hintSelectVenue">
          <i class="bi bi-info-circle me-1"></i>
          Klik salah satu venue di kiri untuk menampilkan daftar pool.
        </div>
      </div>
    </div>
  </div>
</div>

{{-- MODAL VENUE --}}
<div class="modal fade" id="modalVenue" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="form-venue">
        <div class="modal-header">
          <h5 class="modal-title" id="venueModalTitle">Tambah Venue</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="venue_id" id="venue_id">
          <div class="row g-3">
            {{-- <div class="col-md-4">
              <label class="form-label" for="venue_code">Kode Venue</label>
              <input type="text" class="form-control" id="venue_code" name="code" placeholder="VNU-001">
              <div class="text-muted small mt-1">Boleh otomatis/generate juga</div>
            </div> --}}
            <div class="col-md-12">
              <label class="form-label" for="venue_name">Nama Venue</label>
              <input type="text" class="form-control" id="venue_name" name="name" placeholder="Nama Lokasi">
            </div>

            <div class="col-md-12">
              <label class="form-label" for="venue_address">Alamat</label>
              <input type="text" class="form-control" id="venue_address" name="address" placeholder="Alamat singkat venue">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="venue_city">Kota</label>
              <input type="text" class="form-control" id="venue_city" name="city" placeholder="Kota tempat venue berada">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="venue_province">Provinsi</label>
              <input type="text" class="form-control" id="venue_province" name="province" placeholder="Provinsi tempat venue berada">
            </div>
            <div class="col-md-8">
              <label class="form-label" for="venue_country">Negara</label>
              <input type="text" class="form-control" id="venue_country" name="country" placeholder="Negara tempat venue berada">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="venue_is_active">Status</label>
                <select class="form-control" id="venue_is_active" name="is_active">
                  <option value="1">Aktif</option>
                  <option value="0">Nonaktif</option>
                </select>
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

{{-- MODAL POOL --}}
<div class="modal fade" id="modalPool" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="form-pool">
        <div class="modal-header">
          <h5 class="modal-title" id="poolModalTitle">Tambah Pool</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="pool_id" id="pool_id">
          <input type="hidden" name="venue_id" id="pool_venue_id">

          <div class="alert alert-light border small">
            <i class="bi bi-geo-alt me-1"></i>
            Venue terpilih: <b id="poolVenueLabel">—</b>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label" for="pool_name">Nama Pool</label>
              <input type="text" class="form-control" id="pool_name" name="name" placeholder="Kolam Utama">
            </div>
            <div class="col-md-6">
              <label class="form-label" for="pool_role">Pool Role</label>
              <input type="text" class="form-control" id="pool_role" name="pool_role" placeholder="Pemanasan / Lomba / Latihan">
            </div>

            <div class="col-md-4">
              <label class="form-label" for="course_type">Course Type</label>
              <select class="form-control" id="course_type" name="course_type">
                <option value="SCM">SCM</option>
                <option value="LCM">LCM</option>
                <option value="SCY">SCY</option>
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label" for="length_meter">Panjang (meter)</label>
              <input type="number" class="form-control" id="length_meter" name="length_meter" min="1" placeholder="25 / 50">
            </div>
            <div class="col-md-4">
              <label class="form-label" for="total_lanes">Total Lanes</label>
              <input type="number" class="form-control" id="total_lanes" name="total_lanes" min="1" max="12" placeholder="8">
            </div>
            <div class="col-md-4">
                <label class="form-label" for="depth">Kedalaman (meter)</label>
                <input type="number" class="form-control" id="depth" name="depth" min="1" placeholder="2">
            </div>

            <div class="col-md-4">
              <label class="form-label" for="status">Status</label>
              <select class="form-control" id="status" name="status">
                <option value="active">Aktif</option>
                <option value="inactive">Nonaktif</option>
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
  let venueTable, poolTable;

  // state venue terpilih
  let selectedVenue = null; // {id, code, name, address, is_active, ...}

  $(document).ready(function () {
    // VENUE DATATABLE
    venueTable = $('#venueTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: "{{ route('master.venue.data') }}",
      columns: [
        {
          data: null,
          name: 'name',
          render: (row) => {
            const code = row.code ?? '-';
            const address = row.address ?? '';
            return `
              <div class="fw-semibold">${row.name}</div>
              <div class="text-muted small">${code}${address ? ' • ' + address : ''}</div>
            `;
          }
        },
        {
          data: 'is_active',
          name: 'is_active',
          className: 'text-center',
          render: (d) => d ? '<span class="badge text-bg-success">Aktif</span>' : '<span class="badge text-bg-secondary">Nonaktif</span>'
        },
        {
          data: null,
          orderable: false,
          searchable: false,
          className: 'text-center',
          render: (row) => `
            <button class="btn btn-sm btn-outline-primary" onclick="selectVenueByRow(this)">
              <i class="bi bi-arrow-right-circle"></i>
            </button>
            <button class="btn btn-sm btn-outline-secondary ms-1" onclick="openEditVenueFromRow(this)">
              <i class="bi bi-pencil-square"></i>
            </button>
          `
        }
      ],
      drawCallback: function(settings){
        const info = venueTable.page.info();
        $('#venueCount').text(info.recordsDisplay ?? 0);
      }
    });

    // POOL DATATABLE (awal disabled / kosong)
    poolTable = $('#poolTable').DataTable({
      processing: true,
      serverSide: true,
      ajax: {
        url: "{{ route('master.pool.data') }}",
        data: function (d) {
          d.venue_id = selectedVenue?.id ?? null;
        }
      },
      columns: [
        { data: 'action', name: 'action', className:'text-center', orderable:false, searchable:false },
        { data: 'name', name: 'name' },
        { data: 'course_type', name: 'course_type', className:'text-center' },
        { data: 'length_meter', name: 'length_meter', className:'text-center' },
        { data: 'total_lanes', name: 'total_lanes', className:'text-center' },
        { data: 'depth', name: 'depth', className:'text-center' },
        {
          data: 'badge_status',
          name: 'badge_status',
          className:'text-center',
        },
      ],
      drawCallback: function(){
        const info = poolTable.page.info();
        $('#poolCount').text(info.recordsDisplay ?? 0);
      }
    });

    // SUBMIT VENUE
    $('#form-venue').on('submit', async function(e){
      e.preventDefault();
      showSpinner();

      const formData = new FormData(this);

      try {
        const res = await fetch("{{ route('master.venue.store') }}", {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
          body: formData
        });

        if(!res.ok) throw new Error("Terjadi kesalahan pada server");
        const result = await res.json();

        hideSpinner();

        if(result.status){
          $('#modalVenue').modal('hide');
          this.reset();
          venueTable.ajax.reload(null, false);

          // kalau sedang edit venue yang lagi dipilih, refresh panel kanan juga
          if(selectedVenue?.id && (Number($('#venue_id').val()) === selectedVenue.id)){
            // pakai data baru (kalau API kamu ngirim data venue)
            // minimal: reload venue list, user bisa klik lagi
            resetSelectedVenue(true);
          }

          Toast.fire({ icon:'success', title: result.message || 'Berhasil simpan venue' });
        } else {
          Toast.fire({ icon:'error', title: result.message || 'Gagal simpan venue' });
        }
      } catch (err){
        hideSpinner();
        Toast.fire({ icon:'error', title: err.message || 'Terjadi kesalahan' });
      }
    });

    // SUBMIT POOL
    $('#form-pool').on('submit', async function(e){
      e.preventDefault();
      if(!selectedVenue?.id){
        Toast.fire({ icon:'warning', title:'Pilih venue dulu sebelum menambah pool' });
        return;
      }

      showSpinner();
      const formData = new FormData(this);

      try {
        const res = await fetch("{{ route('master.venue.pool.store') }}", {
          method: 'POST',
          headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },
          body: formData
        });

        if(!res.ok) throw new Error("Terjadi kesalahan pada server");
        const result = await res.json();

        hideSpinner();

        if(result.status){
          $('#modalPool').modal('hide');
          this.reset();
          poolTable.ajax.reload(null, false);
          Toast.fire({ icon:'success', title: result.message || 'Berhasil simpan pool' });
        } else {
          Toast.fire({ icon:'error', title: result.message || 'Gagal simpan pool' });
        }
      } catch (err){
        hideSpinner();
        Toast.fire({ icon:'error', title: err.message || 'Terjadi kesalahan' });
      }
    });

  });


  // ============= VENUE SELECT / PANEL UPDATE =============
  function selectVenueByRow(btn){
    const tr = $(btn).closest('tr');
    const row = venueTable.row(tr).data();
    if(!row) return;

    selectedVenue = row;

    $('#selectedVenueName').text(row.name);
    $('#selectedVenueMeta').html(`
      <span class="badge text-bg-light border">${row.code ?? '-'}</span>
      <span class="text-muted ms-2">${row.address ?? ''}</span>
    `);

    $('#btnAddPool').prop('disabled', false);
    $('#btnEditVenue').prop('disabled', false);
    $('#btnDeleteVenue').prop('disabled', false);

    $('#hintSelectVenue').addClass('d-none');

    // reload pools by venue_id
    poolTable.ajax.reload();
  }

  function resetSelectedVenue(clearPool = true){
    selectedVenue = null;
    $('#selectedVenueName').text('Pilih venue terlebih dahulu');
    $('#selectedVenueMeta').text('—');

    $('#btnAddPool').prop('disabled', true);
    $('#btnEditVenue').prop('disabled', true);
    $('#btnDeleteVenue').prop('disabled', true);

    $('#hintSelectVenue').removeClass('d-none');
    if(clearPool) poolTable.ajax.reload();
  }


  // ============= OPEN MODALS =============
  function openCreateVenue(){
    $('#venueModalTitle').text('Tambah Venue');
    $('#form-venue')[0].reset();
    $('#venue_id').val('');
    $('#modalVenue').modal('show');
  }

  function openEditVenue(){
    if(!selectedVenue) return;
    $('#venueModalTitle').text('Edit Venue');
    $('#form-venue')[0].reset();

    $('#venue_id').val(selectedVenue.id);
    $('#venue_code').val(selectedVenue.code ?? '');
    $('#venue_name').val(selectedVenue.name ?? '');
    $('#venue_address').val(selectedVenue.address ?? '');
    $('#venue_city').val(selectedVenue.city ?? '');
    $('#venue_province').val(selectedVenue.province ?? '');
    $('#venue_country').val(selectedVenue.country ?? '');
    $('#venue_is_active').val(selectedVenue.is_active ? 1 : 0);

    $('#modalVenue').modal('show');
  }

  function openEditVenueFromRow(btn){
    const tr = $(btn).closest('tr');
    const row = venueTable.row(tr).data();
    if(!row) return;

    selectedVenue = row; // sekalian set agar konsisten
    openEditVenue();
  }

  function openCreatePool(){
    if(!selectedVenue?.id){
      Toast.fire({ icon:'warning', title:'Pilih venue dulu' });
      return;
    }

    $('#poolModalTitle').text('Tambah Pool');
    $('#form-pool')[0].reset();
    $('#pool_id').val('');
    $('#pool_venue_id').val(selectedVenue.id);
    $('#poolVenueLabel').text(`${selectedVenue.name} (${selectedVenue.code ?? '-'})`);

    $('#modalPool').modal('show');
  }

  // ============= POOL EDIT (kalau action pool memanggil ini) =============
  function editPool(element){
    const tr = $(element).closest('tr');
    const data = poolTable.row(tr).data();
    if(!data) return;

    $('#poolModalTitle').text('Edit Pool');
    $('#form-pool')[0].reset();

    $('#pool_id').val(data.id);
    $('#pool_venue_id').val(selectedVenue.id);
    $('#poolVenueLabel').text(`${selectedVenue.name} (${selectedVenue.code ?? '-'})`);

    $('#pool_name').val(data.name);
    $('#pool_role').val(data.pool_role);
    $('#course_type').val(data.course_type);
    $('#length_meter').val(data.length_meter);
    $('#total_lanes').val(data.total_lanes);
    $('#depth').val(data.depth);
    $('#status').val(data.status);
    $('#modalPool').modal('show');
  }

  // ============= DESTROY VENUE/POOL =============
  async function destroyVenue(){
    if(!selectedVenue?.id) return;

    const { isConfirmed } = await Swal.fire({
      title: "Hapus Venue?",
      text: "Semua pool terkait juga akan dihapus permanen.",
      icon: "warning",
      showCancelButton: true,
      cancelButtonText:'Batal',
      confirmButtonText: "Ya, Hapus!"
    });

    if(!isConfirmed) return;

    try{
      const url = "{{ route('master.venue.destroy', ':id') }}".replace(':id', selectedVenue.id);
      const res = await fetch(url, {
        method:'DELETE',
        headers:{ 'X-CSRF-TOKEN': "{{ csrf_token() }}" }
      });
      if(!res.ok) throw new Error("Terjadi Kesalahan Server");
      const result = await res.json();

      if(result.status){
        Toast.fire({ icon:'success', title: result.message || 'Venue terhapus' });
        resetSelectedVenue(true);
        venueTable.ajax.reload(null, false);
      } else {
        Toast.fire({ icon:'error', title: result.message || 'Gagal hapus venue' });
      }
    }catch(err){
      Toast.fire({ icon:'error', title: err.message || 'Terjadi kesalahan' });
    }
  }

  async function destroyPool(element){
    const { isConfirmed } = await Swal.fire({
      title: "Hapus Pool?",
      text: "Data pool akan dihapus permanen.",
      icon: "warning",
      showCancelButton: true,
      cancelButtonText:'Batal',
      confirmButtonText: "Ya, Hapus!"
    });
    if(!isConfirmed) return;

    try{
      const id = element.dataset.id;
      const url = "{{ route('master.venue.pool.destroy', ':id') }}".replace(':id', id);

      const res = await fetch(url, {
        method:'DELETE',
        headers:{ 'X-CSRF-TOKEN': "{{ csrf_token() }}" }
      });
      if(!res.ok) throw new Error("Terjadi Kesalahan Server");
      const result = await res.json();

      poolTable.ajax.reload(null, false);

      if(result.status){
        Toast.fire({ icon:'success', title: result.message || 'Pool terhapus' });
      } else {
        Toast.fire({ icon:'error', title: result.message || 'Gagal hapus pool' });
      }
    }catch(err){
      Toast.fire({ icon:'error', title: err.message || 'Terjadi kesalahan' });
    }
  }
</script>
@endpush
