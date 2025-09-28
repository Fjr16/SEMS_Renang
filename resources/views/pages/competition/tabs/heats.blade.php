<!-- Tab Heats -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
  <div>
    <h5 class="fw-bold mb-1">Manajemen Heats</h5>
    <p class="text-muted mb-0">Kelola heat (seri perlombaan) untuk tiap event</p>
  </div>
  <div class="mt-3 mt-md-0">
    <button data-bs-toggle="modal" data-bs-target="#modalHeat" class="btn btn-primary">
      <i class="bi bi-plus-circle me-1"></i> Tambah Heat
    </button>
  </div>
</div>

<!-- Card Table -->
<div class="card shadow-sm border-0">
  <div class="card-body">
    <div class="table-responsive">
      <table id="heatsTable" class="table table-striped align-middle">
        <thead class="table-light">
          <tr>
            <th>#</th>
            <th>Event</th>
            <th>Heat</th>
            <th>Jumlah Entries</th>
            <th>Input results</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody>
          <!-- Contoh data statis -->
          <tr>
            <td>1</td>
            <td>100m Freestyle</td>
            <td>Heat 1</td>
            <td>8</td>
            <td>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalResultsHeat1">
                    <i class="bi bi-flag-checkered"></i> Input Results
                </button>
            </td>
            <td>
              <div class="btn-group">
                <button data-bs-toggle="modal" data-bs-target="#modalHeatDetail" class="btn btn-sm btn-info">
                  <i class="bi bi-eye"></i>
                </button>
                <button data-bs-toggle="modal" data-bs-target="#modalHeat" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </div>
            </td>
          </tr>
          <tr>
            <td>2</td>
            <td>100m Freestyle</td>
            <td>Heat 2</td>
            <td>6</td>
            <td>
                <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#modalResultsHeat1">
                    <i class="bi bi-flag-checkered"></i> Input Results
                </button>
            </td>
            <td>
              <div class="btn-group">
                <button data-bs-toggle="modal" data-bs-target="#modalHeatDetail" class="btn btn-sm btn-info">
                  <i class="bi bi-eye"></i>
                </button>
                <button data-bs-toggle="modal" data-bs-target="#modalHeat" class="btn btn-sm btn-warning">
                  <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal Create/Edit Heat -->
<div class="modal fade" id="modalHeat" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Tambah Heat</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label>Event</label>
            <select class="form-select" required>
              <option>100m Freestyle</option>
              <option>200m Butterfly</option>
              <option>400m Medley</option>
            </select>
          </div>
          <div class="mb-3">
            <label>Heat Number</label>
            <input type="number" class="form-control" min="1" required>
          </div>
          <div class="mb-3">
            <label>Jumlah Lintasan (Lane)</label>
            <input type="number" class="form-control" min="1" max="10" value="8" required>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Detail Heat (lihat lane assignments) -->
<div class="modal fade" id="modalHeatDetail" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Detail Heat 1 - 100m Freestyle</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Lane</th>
                <th>Nama Peserta</th>
                <th>Klub</th>
                <th>Seed Time</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>1</td>
                <td>Andi Wijaya</td>
                <td>Dolphin Club</td>
                <td>58.21</td>
              </tr>
              <tr>
                <td>2</td>
                <td>Budi Santoso</td>
                <td>Aqua Swim</td>
                <td>59.10</td>
              </tr>
              <tr>
                <td>3</td>
                <td>Citra Lestari</td>
                <td>Shark Team</td>
                <td>01:00.50</td>
              </tr>
              <!-- dst... -->
            </tbody>
          </table>
        </div>
      </div>
      <div class="modal-footer">
        {{-- <button class="btn btn-primary">Edit Assignments</button> --}}
        <button class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#modalLaneAssignments1">
            <i class="bi bi-diagram-3"></i> Lane Assignments
        </button>
      </div>
    </div>
  </div>
</div>

<!-- Modal Lane Assignments -->
<div class="modal fade" id="modalLaneAssignments1" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Lane Assignments - Heat 1</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-sm table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Lane</th>
                  <th>Atlet</th>
                  <th>Seed Time</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                @for ($i = 1; $i <= 8; $i++)
                  <tr>
                    <td>{{ $i }}</td>
                    <td>
                      <select class="form-select form-select-sm">
                        <option value="">-- Kosong --</option>
                        <option value="1">Budi Santoso</option>
                        <option value="2">Siti Aminah</option>
                      </select>
                    </td>
                    <td>
                      <input type="text" class="form-control form-control-sm" placeholder="00:55.32">
                    </td>
                    <td>
                      <select class="form-select form-select-sm">
                        <option>Terdaftar</option>
                        <option>DNS</option>
                        <option>DSQ</option>
                      </select>
                    </td>
                  </tr>
                @endfor
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
          <button type="submit" class="btn btn-primary">Simpan</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Modal Input Results -->
<div class="modal fade" id="modalResultsHeat1" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form>
        <div class="modal-header">
          <h5 class="modal-title">Input Results - Heat 1</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <div class="table-responsive">
            <table class="table table-bordered align-middle">
              <thead class="table-light">
                <tr>
                  <th>Lane</th>
                  <th>Nama Atlet</th>
                  <th>Waktu (detik)</th>
                  <th>Status</th>
                  <th>Ranking</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>1</td>
                  <td>Budi Santoso</td>
                  <td><input type="text" class="form-control" placeholder="00:55.32"></td>
                  <td>
                    <select class="form-select">
                      <option value="finished">Finished</option>
                      <option value="dsq">Disqualified</option>
                      <option value="dns">Did Not Start</option>
                    </select>
                  </td>
                  <td><input type="number" class="form-control" placeholder="1"></td>
                </tr>
                <tr>
                  <td>2</td>
                  <td>Siti Aminah</td>
                  <td><input type="text" class="form-control" placeholder="00:56.10"></td>
                  <td>
                    <select class="form-select">
                      <option value="finished">Finished</option>
                      <option value="dsq">Disqualified</option>
                      <option value="dns">Did Not Start</option>
                    </select>
                  </td>
                  <td><input type="number" class="form-control" placeholder="2"></td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan Results</button>
        </div>
      </form>
    </div>
  </div>
</div>