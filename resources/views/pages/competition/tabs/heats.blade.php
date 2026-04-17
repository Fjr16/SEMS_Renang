<style>
    /* ── Team group card ── */
    .team-group { border: 1px solid #dee2e6; border-radius: 10px; margin-bottom: 10px; overflow: hidden; }
    .team-group-header {
        background: #f8f9fa;
        padding: 11px 16px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
        user-select: none;
    }
    .team-group-header:hover { background: #f1f3f5; }
    .team-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .team-group-body { border-top: 1px solid #dee2e6; }
    .team-group-body table th { font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: .04em; padding: 7px 14px; border-bottom: 1px solid #dee2e6; }
    .team-group-body table td { padding: 9px 14px; font-size: 13px; vertical-align: middle; }

        /* ── Official row ── */
    .official-row { display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: #f8f9fa; border-radius: 8px; font-size: 13px; margin-bottom: 6px; }
    .official-avatar { width: 28px; height: 28px; border-radius: 50%; background: #DBEAFE; color: #1D4ED8; font-size: 10px; font-weight: 600; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
</style>
<!-- Tab Heats -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-2">
  <div>
    <h5 class="fw-bold mb-1">Manajemen Seri Perlombaan</h5>
    <p class="text-muted mb-0">Kelola seri perlombaan untuk tiap event</p>
  </div>
  <div class="mt-3 mt-md-0">
    <button data-bs-toggle="modal" data-bs-target="#modalHeat" class="btn btn-outline-primary">
      <i class="bi bi-magic me-1"></i> Generate Seri
    </button>
  </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body px-2">
        <div id="view-group">
            @if(!empty($event->heats))
                <div class="team-group" id="team-group-{{ $event->id }}">
                    <div class="team-group-header" onclick="toggleGroup('{{ $event->id }}')">
                        <div class="d-flex align-items-center gap-2 w-100">
                            {{-- <span id="chevron-1" style="font-size:11px;color:#6c757d;transition:transform .2s;display:inline-block"><i class="bi bi-caret-right-square-fill"></i></span> --}}
                            <span class="fw-semibold flex-grow-1" style="font-size:14px">{{ $event->getLabel() }}</span>
                            <span class="text-muted me-1" style="font-size:12px"> entry</span>
                            {{-- <div class="d-flex gap-1">
                                @foreach($teamEntries->groupBy('status') as $status => $statusEntries)
                                    <span class="badge rounded-pill badge-{{ $status }}" style="font-size:10px">
                                        {{ $statusEntries->count() }} {{ App\Enums\CompetitionTeamEntryStatus::tryFrom($status)->label() ?? 'Tidak Dikenali' }}
                                    </span>
                                @endforeach
                            </div> --}}
                        </div>
                        {{-- <div class="d-flex align-items-center gap-3 w-100" onclick="event.stopPropagation()">
                            <span class="badge rounded-pill badge-{{ $compTeam?->status }}" style="font-size:10px">
                                {{ App\Enums\CompetitionTeamStatus::tryFrom($compTeam->status)->label() ?? 'Tidak Dikenali' }}
                            </span>
                             @php
                            $payIcon    = match($compTeam->payment_status) {
                                    'paid'    => ['💳', '#065F46', '#D1FAE5', 'Lunas'],
                                    default   => ['❌', '#991B1B', '#FEE2E2', 'Belum Bayar'],
                                };
                            @endphp
                            <span style="font-size:11px;background:{{ $payIcon[2] }};color:{{ $payIcon[1] }};padding:2px 8px;border-radius:20px;font-weight:500">
                                {{ $payIcon[0] }} {{ $payIcon[3] }}
                            </span>
                            <span class="text-muted" style="font-size:12px">
                                <span style="font-size:10px">Total Biaya</span>
                                <span class="fw-semibold" style="color:#111">Rp {{ number_format($compTeam->total_fee ?? 0, 0, ',', '.') }}</span>
                            </span>
                            <div class="flex-grow-1"></div>
                            <div class="d-flex gap-1" onclick="event.stopPropagation()">
                                @if($compTeam->status === App\Enums\CompetitionTeamStatus::Pending->value && $compTeam->competition->status === App\Enums\CompetitionStatus::register->value)
                                    <button class="btn btn-sm" style="background:#D1FAE5;color:#065F46;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                        onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Active->value }}')">✓ Terima</button>
                                    <button class="btn btn-sm" style="background:#FEE2E2;color:#991B1B;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                        onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Rejected->value }}')">✕ Tolak</button>
                                @elseif($compTeam->status === App\Enums\CompetitionTeamStatus::Pending->value && $compTeam->competition->status === App\Enums\CompetitionStatus::running->value)
                                    <button class="btn btn-sm" style="background:#FEE2E2;color:#991B1B;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                        onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Rejected->value }}')">✕ Tolak</button>
                                @elseif ($compTeam->status === App\Enums\CompetitionTeamStatus::Active->value && $compTeam->competition->status === App\Enums\CompetitionStatus::register->value)
                                    <button class="btn btn-sm" style="background:#FEE2E2;color:#991B1B;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                        onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Rejected->value }}')">✕ Tolak</button>
                                @elseif ($compTeam->status === App\Enums\CompetitionTeamStatus::Active->value && $compTeam->competition->status === App\Enums\CompetitionStatus::running->value)
                                <button class="btn btn-sm" style="background:#FEF3C7;color:#92400E;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                    onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Withdrawn->value }}')">
                                    ⚑ Undur diri
                                </button>
                                <button class="btn btn-sm" style="background:#FEE2E2;color:#991B1B;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                    onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Disqualified->value }}')">
                                    ⊘ Diskualifikasi
                                </button>
                                @elseif ($compTeam->status === App\Enums\CompetitionTeamStatus::Rejected->value && $compTeam->competition->status === App\Enums\CompetitionStatus::register->value)
                                <button class="btn btn-sm" style="background:#D1FAE5;color:#065F46;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                        onclick="confirmEntry({{ $compTeam?->id }}, '{{ $compTeam?->payment_status }}', '{{ App\Enums\CompetitionTeamStatus::Active->value }}')">✓ Terima</button>
                                @endif
                            </div>
                        </div> --}}
                    </div>
                    <div class="team-group-body" id="team-body-1">
                        <div class="d-flex border-bottom px-3" style="background:#fafafa">
                            @foreach ($event->heats as $heat)
                                <button class="btn btn-link btn-sm text-decoration-none fw-semibold px-3 py-2 tab-heat-btn"
                                    data-heat="{{ $heat->heat_number }}"
                                    style="font-size:12px;border-bottom:2px solid transparent;border-radius:0;color:#2563EB;">
                                    {{ 'Seri ' . $heat->heat_number }}
                                </button>
                            @endforeach
                        </div>

                        @foreach ($event->heats as $heat)
                        <div class="heat-panel table-responsive" data-heat="{{ $heat->heat_number }}" style="display:none">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr class="text-center">
                                        <th>Peringkat</th>
                                        <th>Lintasan</th>
                                        <th>Atlet</th>
                                        <th>Tim / Klub</th>
                                        <th>Waktu Tercepat</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse ($heat->heatLanes as $lane)
                                        <tr class="text-center">
                                            <td>{{ $lane->lane_order ?? '-' }}</td>
                                            <td>{{ $lane->lane_number ?? '-' }}</td>
                                            <td>{{ $lane->entry->athlete?->name ?? '-' }}</td>
                                            <td>{{ $lane->entry->athlete?->club?->club_name ?? '-' }}</td>
                                            <td>{{ $lane->entry?->seed_time ?? 'NT' }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">Belum ada data.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="text-center text-muted py-5" style="font-size:13px">Belum ada seri dalam kompetisi ini.</div>
            @endif
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
