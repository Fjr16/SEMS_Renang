<!-- Tab Events -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h5 class="fw-bold mb-1">Daftar Events</h5>
    <p class="text-muted mb-0">Kelola event yang ada dalam kompetisi ini</p>
  </div>
  <div class="mt-3 mt-md-0">
    <button data-bs-toggle="modal" data-bs-target="#modalEvent" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Tambah Event
    </button>
  </div>
</div>

<!-- Card Table -->
<div class="card shadow-sm border-0">
  <div class="card-body">
        <table id="eventsTable" class="table table-striped align-middle">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Tanggal</th>
                <th>Sesi</th>
                <th>Nomor Event</th>
                <th>Gaya Perlombaan</th>
                <th>Jarak</th>
                <th>Jenis Kelamin</th>
                <th>Kelompok Umur</th>
                <th>Tipe Perlombaan</th>
                <th>Sistem Perlombaan</th>
                {{-- <th>Minimal DOB</th> --}}
                {{-- <th>Maksimal DOB</th> --}}
                <th>Biaya Pendaftaran</th>
                <th>Catatan</th>
                {{-- <th>Lanes</th> --}}
                <th>Aksi</th>
            </tr>
            </thead>
        </table>
  </div>
</div>

<!-- Modal Create/Edit Event -->
<div class="modal fade" id="modalEvent" tabindex="-1">
  <div class="modal-dialog modal-xl">
    <div class="modal-content">
      <form id="eventForm" data-url="{{ route('competition.tab.events.store', $competition) }}" onsubmit="storeAndUpdateGlobal(event,this,'eventsTable','modalEvent')">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Event</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <div class="row g-3">
                <input type="hidden" id="competition_event_id" name="competition_event_id">
                <div class="col-md-4 col-12">
                    <label>Kompetisi</label>
                    <input type="text" class="form-control" id="competition_name" value="{{ $competition->name ?? '' }}" disabled>
                    <input type="hidden" value="{{ $competition->id ?? '' }}" name="competition_id" id="competition_id">
                </div>
                <div class="col-md-8 col-12">
                    <label>Sesi Perlombaan</label>
                    <select name="competition_session_id" id="competition_session_id" class="form-control">
                        @foreach ($competition->sessions as $sesi)
                            {{-- <option value="{{ $sesi->id }}" @selected(old('competition_session_id') == $sesi->id)>{{ '[' . ($sesi->date ?? '-/-') .'] '. ($sesi->name ?? '-') . ' [' . ($sesi->start_time ?? '-') . ' - ' . ($sesi->end_time ?? '') . ']' }}</option> --}}
                            <option value="{{ $sesi->id }}" @selected(old('competition_session_id') == $sesi->id)>{{ ($sesi->date ?? '-/-') . ' [' . ($sesi->start_time ?? '-') . ' - ' . ($sesi->end_time ?? '') . ']' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-12">
                    <label>Gaya Perlombaan</label>
                    <select class="form-control" id="stroke" name="stroke" required>
                        @foreach ($enumStroke as $stroke)
                            <option value="{{ $stroke->value }}">{{ $stroke->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-12">
                    <label>Jarak</label>
                    <div class="input-group">
                        <input type="number" class="form-control" id="distance" name="distance" required>
                        <span class="input-group-text">m</span>
                    </div>
                </div>

                <div class="col-md-4 col-12">
                    <label>Jenis Kelamin</label>
                    <select class="form-control" id="gender" name="gender" required>
                        @foreach ($enumGender as $gender)
                            <option value="{{ $gender->value }}">{{ $gender->label() }}</option>
                        @endforeach
                        <option value="mixed">MIXED</option>
                    </select>
                </div>

                <div class="col-md-2 col-12">
                    <label>Kelompok Umur</label>
                    <select class="form-control" id="age_group_id" name="age_group_id" required>
                        @foreach ($ageGroups as $ku)
                            <option value="{{ $ku->id }}">{{ $ku->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-12">
                    <label>Tipe Perlombaan</label>
                    <select class="form-control" id="event_type" name="event_type" required>
                        @foreach ($enumEType as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-12">
                    <label>Sistem Perlombaan</label>
                    <select class="form-control" id="event_system" name="event_system" required>
                        @foreach ($enumESystem as $system)
                            <option value="{{ $system->value }}">{{ $system->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-12">
                    <label>Biaya Pendaftaran</label>
                    <div class="input-group">
                        <span class="input-group-text">Rp</span>
                        <input type="text" class="form-control" id="registration_fee" name="registration_fee" required>
                    </div>
                </div>

                <div class="col-12">
                    <label>Catatan</label>
                    <textarea class="form-control" name="remarks" id="remarks" rows="2"></textarea>
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

@push('scripts')
    <script>
        async function editEvent(element) {
            const tableId = '#'+element.dataset.table;
            const modalId = '#'+element.dataset.modal;

            const form = document.getElementById(element.dataset.form);
            form.reset();

            let tr = $(element).closest('tr');
            if (tr.hasClass('child')) {
                tr = tr.prev(); // parent row
            }
            const data = $(tableId).DataTable().row(tr).data();

            if(!data) {
                Toast.fire({
                    icon:'error',
                    title:'Data Tidak ditemukan, atau hubungi admin'
                });
            }

            $('#competition_event_id').val(data.id);
            $('#competition_session_id').val(data.session_id);
            $('#event_number').val(data.event_number);
            $('#stroke').val(data.stroke);
            $('#distance').val(data.distance);
            $('#gender').val(data.gender);
            $('#age_group_id').val(data.age_group_id);
            $('#event_type').val(data.event_type);
            $('#event_system').val(data.event_system);
            $('#registration_fee').val(toNum(data.registration_fee));
            $('#remarks').val(data.remarks);

            $(modalId).modal('show');
        }
    </script>
@endpush
