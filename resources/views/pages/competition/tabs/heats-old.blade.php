<style>
    /* ── Team group card ── */
    .team-group { border: 1px solid #dee2e6; border-radius: 10px; margin-bottom: 10px; overflow: hidden; }
    .team-group-header {
        background: #e3efff6d;
        padding: 16px 16px;
        cursor: pointer;
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 8px;
        user-select: none;
    }
    .team-group-body { border-top: 1px solid #dee2e6; }
    .team-group-body table th { font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: .04em; padding: 7px 14px; border-bottom: 1px solid #dee2e6; }
    .team-group-body table td { padding: 9px 14px; font-size: 13px; vertical-align: middle; }

        /* ── Official row ── */
</style>
<!-- Tab Heats -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-2">
  <div>
    <h5 class="fw-bold mb-1">Manajemen Seri Perlombaan</h5>
    <p class="text-muted mb-0">Kelola seri perlombaan untuk tiap event</p>
  </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body px-2">
        <div id="view-group">
            <div class="team-group" id="team-group-{{ $event->id }}">
                <div class="team-group-header">
                    <div class="d-flex align-items-center gap-2 w-100">
                        <select name="competition_event_id" id="heat_competition_event_id" class="form-control flex-grow-1" style="font-size:13px; min-width:0">
                            @foreach ($selectEvents as $item)
                                <option value="{{ $item->id }}" @selected($event->id === $item->id)>{{ $item->getLabel() }}</option>
                            @endforeach
                        </select>

                        <div class="d-flex align-items-center gap-2" onclick="generateHeats($event->id)">
                            <button class="btn btn-sm btn-primary d-flex align-items-center gap-1"
                                data-bs-toggle="modal" data-bs-target="#modalHeat"
                                style="font-size:12px; white-space:nowrap; border-radius:6px">
                                <i class="bi bi-grid"></i>Auto Generate Seri
                            </button>
                        </div>
                    </div>
                </div>
                @if($event->heats->isNotEmpty())
                <div class="team-group-body" id="team-body-1">
                    <div class="d-flex border-bottom px-3" style="background:#e3efff6d">
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
                @else
                    <div class="text-center text-muted p-5" style="font-size:14px"><span style="font-size: 18px; font-weight:bold;">Belum ada seri dalam event / acara ini</span></br> Sebelum menggunakan auto generate seri pastikan pendaftaran kompetisi telah ditutup dan pendaftaran telah selesai di review, agar tidak ada entry atau pendaftaran yang terlewat</div>
                @endif
            </div>
        </div>
    </div>
</div>
