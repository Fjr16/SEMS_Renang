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
                <div class="col-md-4 col-12">
                    <label>Nomor Event</label>
                    <input type="text" class="form-control" id="event_number" name="event_number" required>
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
                        <option value="mixed">Mixed</option>
                    </select>
                </div>

                <div class="col-md-8 col-12">
                    <label>Kelompok Umur</label>
                    <select class="form-control" id="age_group_id" name="age_group_id" required>
                        @foreach ($ageGroups as $ku)
                            <option value="{{ $ku->id }}">{{ $ku->label }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-12">
                    <label>Tipe Perlombaan</label>
                    <select class="form-control" id="event_type" name="event_type" required>
                        @foreach ($enumEType as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4 col-12">
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

{{-- @push('scripts')
    <script>

    </script>
@endpush --}}
