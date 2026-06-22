<!-- Tab Events -->
<style>
    .session-type-card:hover:not(.disabled-card) {
        border-color: #86b7fe !important;
        background: #f0f6ff !important;
    }
    .disabled-card {
        cursor: not-allowed !important;
    }
    .cursor-pointer {
        cursor: pointer;
    }
</style>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h5 class="fw-bold mb-1">Daftar Sesi</h5>
    <p class="text-muted mb-0">Kelola sesi yang ada dalam kompetisi ini</p>
  </div>
  <div class="mt-3 mt-md-0">
    <button data-bs-toggle="modal" data-bs-target="#modalSessions" onclick="resetSessionForm()" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Tambah Sesi
    </button>
  </div>
</div>

<!-- Card Table -->
<div class="card shadow-sm border-0">
  <div class="card-body">
      <table id="sessionsTable" class="table table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>Aksi</th>
            <th>Nama Sesi</th>
            <th>Tanggal Sesi</th>
            <th>Kolam</th>
            <th>Urutan</th>
          </tr>
        </thead>
      </table>
  </div>
</div>

<!-- Modal Create/Edit Event -->
<div class="modal fade" id="modalSessions" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="sessionForm" data-url="{{ route('competition.tab.sessions.store', $competition) }}" onsubmit="return storeAndUpdateGlobal(event,this,'sessionsTable','modalSessions')">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Sesi</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" name="competition_session_id" id="competition_session_id">
            <input type="hidden" name="competition_id" value="{{ $competition->id }}">
            <input type="hidden" name="name" id="name">
            <input type="hidden" name="session_order" id="session_order">
          <div class="row mb-3">
            <div class="col-6">
                <label>Lokasi / Tempat</label>
                <input type="text" class="form-control" id="venue_name" value="{{ $competition->venue->name ?? '' }}" disabled>
            </div>
            <div class="col-6">
              <label>Kolam</label>
              <select class="form-select form-select-md" name="pool_id" id="pool_id" required>
                <option value="">Pilih Kolam</option>
                @foreach ($pools as $pool)
                    <option value="{{ $pool->id }}" @selected(old('pool_id') == $pool->id)>{{ '[' . ($pool->code ?? '-') . '] ' . ($pool->name ?? '-') }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="mb-3">
            <label>Tanggal Sesi</label>
            <input type="text" class="form-control mark-date" placeholder="Pilih Tanggal" name="session_date" id="session_date" required>
          </div>
          <div class="mb-3" id="sessionTypeGroup">
            <label class="d-block mb-2">Tipe Sesi</label>
            <div class="row g-2">
              <div class="col-6">
                <div class="card session-type-card text-center cursor-pointer" data-value="Sesi Pagi" onclick="selectSessionType(this)" style="border:2px solid #dee2e6; border-radius:10px; transition:all .15s;">
                  <div class="card-body py-3">
                    <i class="bi bi-sunrise text-warning" style="font-size:1.5rem;"></i>
                    <div class="fw-semibold mt-1 small">Sesi Pagi</div>
                  </div>
                </div>
              </div>
              <div class="col-6">
                <div class="card session-type-card text-center cursor-pointer" data-value="Sesi Siang" onclick="selectSessionType(this)" style="border:2px solid #dee2e6; border-radius:10px; transition:all .15s;">
                  <div class="card-body py-3">
                    <i class="bi bi-sun text-orange" style="font-size:1.5rem; color:#f59f00;"></i>
                    <div class="fw-semibold mt-1 small">Sesi Siang</div>
                  </div>
                </div>
              </div>
            </div>
            <div id="sessionTypeFeedback" class="text-danger small mt-1 d-none"></div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary" id="sessionSubmitBtn">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>
