

<style>

    /* ── Summary stat cards ── */
    .stat-card { background: #f8f9fa; border-radius: 8px; padding: 10px 16px; cursor: pointer; border: 1.5px solid transparent; transition: border-color .15s; }
    .stat-card:hover { border-color: #dee2e6; }
    .stat-card.active { border-color: #2563EB; }
    .stat-card .stat-num { font-size: 20px; font-weight: 600; display: block; margin-bottom: 1px; }
    .stat-card .stat-label { font-size: 12px; color: #6c757d; }

    /* ── View toggle ── */
    .view-toggle .btn { font-size: 12px; padding: 4px 14px; }

    /* ── Status badges ── */
    .badge-pending    { background: #FEF3C7; color: #92400E; }
    .badge-active     { background: #D1FAE5; color: #065F46; }
    .badge-rejected   { background: #FEE2E2; color: #991B1B; }
    .badge-disqualified { background: #FCE7F3; color: #9D174D; }
    .badge-withdrawn  { background: #F3F4F6; color: #374151; }

    /* ── Team group card ── */
    .team-group { border: 1px solid #dee2e6; border-radius: 10px; margin-bottom: 10px; overflow: hidden; }
    .team-group-header { background: #f8f9fa; padding: 11px 16px; cursor: pointer; display: flex; align-items: center; gap: 10px; user-select: none; }
    .team-group-header:hover { background: #f1f3f5; }
    .team-dot { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }
    .team-group-body { border-top: 1px solid #dee2e6; }
    .team-group-body table th { font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: .04em; padding: 7px 14px; border-bottom: 1px solid #dee2e6; }
    .team-group-body table td { padding: 9px 14px; font-size: 13px; vertical-align: middle; }

    /* ── Relay tag ── */
    .relay-tag { font-size: 10px; background: #EDE9FE; color: #5B21B6; padding: 1px 6px; border-radius: 4px; margin-left: 5px; border: 1px solid #DDD6FE; }

    /* ── Bulk action bar ── */
    #bulk-action-bar { background: #DBEAFE; border-radius: 8px; padding: 8px 14px; display: none; align-items: center; gap: 10px; font-size: 13px; color: #1D4ED8; margin-bottom: 12px; }
    #bulk-action-bar.show { display: flex; }

    /* ── Relay member row ── */
    .relay-member-row { display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: #f8f9fa; border-radius: 8px; font-size: 13px; margin-bottom: 6px; }
    .relay-leg-badge { width: 22px; height: 22px; border-radius: 50%; background: #EDE9FE; color: #5B21B6; font-size: 10px; font-weight: 700; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

    /* ── Official row ── */
    .official-row { display: flex; align-items: center; gap: 10px; padding: 8px 12px; background: #f8f9fa; border-radius: 8px; font-size: 13px; margin-bottom: 6px; }
    .official-avatar { width: 28px; height: 28px; border-radius: 50%; background: #DBEAFE; color: #1D4ED8; font-size: 10px; font-weight: 600; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }

    /* ── Section label ── */
    .section-label { font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: .04em; font-weight: 600; margin-bottom: 8px; }

    /* ── Action buttons ── */
    .btn-action { width: 28px; height: 28px; padding: 0; display: inline-flex; align-items: center; justify-content: center; font-size: 13px; border-radius: 5px; }

    /* flat table font size */
    #view-flat table td, #view-flat table th { font-size: 13px; vertical-align: middle; }
    #view-flat table th { font-size: 11px; color: #6c757d; text-transform: uppercase; letter-spacing: .04em; }
</style>

@php
    $entryCounts = [
        'all' => '',
        'pending' => '',
        'active' => '',
        'rejected' => '',
        'disqualified' => '',
    ];
    $entriesByTeam = collect();
    $entries = collect();
@endphp
{{-- Main card --}}
<div class="card border-top-0 rounded-top-0 shadow-sm">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h6 class="fw-semibold mb-1">Daftar Entries</h6>
                <p class="text-muted mb-0" style="font-size:12px">Kelola dan review peserta yang mendaftar ke event kompetisi ini</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <div class="btn-group view-toggle" role="group" id="viewToggle">
                    <button type="button" class="btn btn-outline-primary active" id="btn-view-group" onclick="setView('group')">Per Tim</button>
                    <button type="button" class="btn btn-outline-primary" id="btn-view-flat" onclick="setView('flat')">Semua Entry</button>
                </div>
                <a href="" class="btn btn-primary btn-sm">
                    + Tambah Entry
                </a>
            </div>
        </div>

        {{-- Summary stats --}}
        <div class="d-flex gap-2 flex-wrap mb-3" id="summary-bar">
            <div class="stat-card" onclick="quickFilter('')">
                <span class="stat-num" id="s-all">{{ $entryCounts['all'] }}</span>
                <span class="stat-label">Total</span>
            </div>
            <div class="stat-card" onclick="quickFilter('pending')">
                <span class="stat-num text-warning" id="s-pending">{{ $entryCounts['pending'] }}</span>
                <span class="stat-label">Pending</span>
            </div>
            <div class="stat-card" onclick="quickFilter('active')">
                <span class="stat-num text-success" id="s-active">{{ $entryCounts['active'] }}</span>
                <span class="stat-label">Active</span>
            </div>
            <div class="stat-card" onclick="quickFilter('rejected')">
                <span class="stat-num text-danger" id="s-rejected">{{ $entryCounts['rejected'] }}</span>
                <span class="stat-label">Rejected</span>
            </div>
            <div class="stat-card" onclick="quickFilter('disqualified')">
                <span class="stat-num" style="color:#9D174D" id="s-dq">{{ $entryCounts['disqualified'] }}</span>
                <span class="stat-label">Disqualified</span>
            </div>
        </div>

        {{-- Filters --}}
        <div class="d-flex gap-2 flex-wrap align-items-center mb-3">
            <span class="text-muted" style="font-size:12px">Filter:</span>
            <select class="form-select form-select-sm" style="width:100%" id="f-status" onchange="applyFilter()">
                <option value="">Semua Status</option>
                <option value="pending">Pending</option>
                <option value="active">Active</option>
                <option value="rejected">Rejected</option>
                <option value="disqualified">Disqualified</option>
                <option value="withdrawn">Withdrawn</option>
            </select>
            <select class="form-select form-select-sm" style="width:auto" id="event_id" onchange="applyFilter()">
                <option value="">Semua Event</option>
            </select>
            <select class="form-select form-select-sm" style="width:auto" id="club_id" name="club_id" onchange="applyFilter()">
                <option value="">Semua Tim</option>
            </select>
        </div>

        {{-- Bulk action bar (flat view) --}}
        <div id="bulk-action-bar">
            <span id="bulk-count">0 entry dipilih</span>
            <button class="btn btn-sm" style="background:#D1FAE5;color:#065F46;border:none" onclick="bulkAction('active')">✓ Terima Semua</button>
            <button class="btn btn-sm" style="background:#FEE2E2;color:#991B1B;border:none" onclick="bulkAction('rejected')">✕ Tolak Semua</button>
            <button class="btn btn-sm" style="background:#FCE7F3;color:#9D174D;border:none" onclick="bulkAction('disqualified')">Disqualified</button>
            <button class="btn btn-sm" style="background:#F3F4F6;color:#374151;border:none" onclick="bulkAction('withdrawn')">Withdrawn</button>
        </div>

        {{-- ═══════════════════════════════ GROUP VIEW ═══════════════════════════════ --}}
        <div id="view-group">
            @forelse($entriesByTeam as $teamId => $teamData)
                @php
                    $team     = $teamData['team'];
                    $teamEntries = $teamData['entries'];
                    $entryIds = $teamEntries->pluck('id')->implode(',');
                    $dotColors = ['Aqua Club Jakarta' => '#2563EB', 'Dolphin Bandung' => '#16A34A', 'Swim Pro Surabaya' => '#D97706'];
                    $dotColor  = $dotColors[$team->name] ?? '#6c757d';
                @endphp
                <div class="team-group" id="team-group-{{ $teamId }}">
                    <div class="team-group-header" onclick="toggleGroup({{ $teamId }})">
                        <span id="chevron-{{ $teamId }}" style="font-size:11px;color:#6c757d;transition:transform .2s;display:inline-block">▶</span>
                        <div class="team-dot" style="background:{{ $dotColor }}"></div>
                        <span class="fw-semibold flex-grow-1" style="font-size:14px">{{ $team->name }}</span>
                        <span class="text-muted me-2" style="font-size:12px">{{ $teamEntries->count() }} entry</span>
                        <div class="d-flex gap-1 me-2">
                            @foreach($teamEntries->groupBy('status') as $status => $statusEntries)
                                <span class="badge rounded-pill badge-{{ $status }}" style="font-size:10px">
                                    {{ $statusEntries->count() }} {{ ucfirst($status) }}
                                </span>
                            @endforeach
                        </div>
                        <div class="d-flex gap-1" onclick="event.stopPropagation()">
                            <button class="btn btn-sm" style="background:#D1FAE5;color:#065F46;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                onclick="bulkTeam('{{ $entryIds }}', 'active')">✓ Terima Semua</button>
                            <button class="btn btn-sm" style="background:#FEE2E2;color:#991B1B;font-size:11px;border:none;border-radius:20px;padding:3px 10px"
                                onclick="bulkTeam('{{ $entryIds }}', 'rejected')">✕ Tolak Semua</button>
                        </div>
                    </div>
                    <div class="team-group-body" id="team-body-{{ $teamId }}">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Atlet</th>
                                    <th>Event</th>
                                    <th>Seed Time</th>
                                    <th>Status</th>
                                    <th>Heat</th>
                                    <th>Lane</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($teamEntries as $i => $entry)
                                <tr id="entry-row-g-{{ $entry->id }}">
                                    <td class="text-muted">{{ $i + 1 }}</td>
                                    <td>
                                        <span class="fw-medium">
                                            {{ $entry->is_relay ? 'Relay ' . $team->name : optional($entry->athlete)->name }}
                                        </span>
                                        @if($entry->is_relay)
                                            <span class="relay-tag">Relay</span>
                                        @endif
                                    </td>
                                    <td>{{ optional($entry->event)->name }}</td>
                                    <td><code>{{ $entry->seed_time ?? '—' }}</code></td>
                                    <td>
                                        <span class="badge rounded-pill badge-{{ $entry->status }}" id="badge-g-{{ $entry->id }}">
                                            {{ ucfirst($entry->status) }}
                                        </span>
                                    </td>
                                    <td class="text-muted">{{ $entry->heat_number ?? '—' }}</td>
                                    <td class="text-muted">{{ $entry->lane_number ?? '—' }}</td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button class="btn btn-primary btn-action"
                                                title="Review"
                                                onclick="openModal({{ $entry->id }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                                    <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                                </svg>
                                            </button>
                                            {{-- <a href="{{ route('admin.competitions.entries.edit', [$competition, $entry]) }}" --}}
                                            <a href=""
                                                class="btn btn-warning btn-action" title="Edit">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                                </svg>
                                            </a>
                                            <button class="btn btn-danger btn-action" title="Hapus"
                                                onclick="confirmDelete({{ $entry->id }})">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                                    <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                                    <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-5" style="font-size:13px">Belum ada entry untuk kompetisi ini.</div>
            @endforelse
        </div>

        {{-- ═══════════════════════════════ FLAT VIEW ═══════════════════════════════ --}}
        <div id="view-flat" style="display:none">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="cb-all" onchange="toggleAll(this)"></th>
                        <th>#</th>
                        <th>Atlet</th>
                        <th>Tim</th>
                        <th>Event</th>
                        <th>Seed Time</th>
                        <th>Status</th>
                        <th>Heat</th>
                        <th>Lane</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody id="flat-body">
                    @foreach($entries as $i => $entry)
                    <tr id="entry-row-f-{{ $entry->id }}" data-status="{{ $entry->status }}" data-event="{{ $entry->event_id }}" data-team="{{ $entry->competition_team->team_id ?? '' }}">
                        <td><input type="checkbox" class="entry-cb" value="{{ $entry->id }}" onchange="toggleEntry(this, {{ $entry->id }})"></td>
                        <td class="text-muted">{{ $i + 1 }}</td>
                        <td>
                            <span class="fw-medium">
                                {{ $entry->is_relay ? 'Relay ' . optional($entry->competitionTeam->team)->name : optional($entry->athlete)->name }}
                            </span>
                            @if($entry->is_relay)
                                <span class="relay-tag">Relay</span>
                            @endif
                        </td>
                        <td class="text-muted" style="font-size:12px">{{ optional($entry->competitionTeam->team)->name }}</td>
                        <td>{{ optional($entry->event)->name }}</td>
                        <td><code>{{ $entry->seed_time ?? '—' }}</code></td>
                        <td>
                            <span class="badge rounded-pill badge-{{ $entry->status }}" id="badge-f-{{ $entry->id }}">
                                {{ ucfirst($entry->status) }}
                            </span>
                        </td>
                        <td class="text-muted">{{ $entry->heat_number ?? '—' }}</td>
                        <td class="text-muted">{{ $entry->lane_number ?? '—' }}</td>
                        <td>
                            <div class="d-flex gap-1">
                                <button class="btn btn-primary btn-action" title="Review" onclick="openModal({{ $entry->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M10.5 8a2.5 2.5 0 1 1-5 0 2.5 2.5 0 0 1 5 0"/>
                                        <path d="M0 8s3-5.5 8-5.5S16 8 16 8s-3 5.5-8 5.5S0 8 0 8m8 3.5a3.5 3.5 0 1 0 0-7 3.5 3.5 0 0 0 0 7"/>
                                    </svg>
                                </button>
                                {{-- <a href="{{ route('admin.competitions.entries.edit', [$competition, $entry]) }}" class="btn btn-warning btn-action" title="Edit"> --}}
                                <a href="" class="btn btn-warning btn-action" title="Edit">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.499.499 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                    </svg>
                                </a>
                                <button class="btn btn-danger btn-action" title="Hapus" onclick="confirmDelete({{ $entry->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5m3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0z"/>
                                        <path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4zM2.5 3h11V2h-11z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            {{-- Pagination --}}
            <div class="d-flex justify-content-end mt-2">
                {{-- {{ $entries->withQueryString()->links() }} --}}
            </div>
        </div>

    </div>
</div>

{{-- ═══════════════════════════════ MODAL REVIEW ═══════════════════════════════ --}}
<div class="modal fade" id="modalReview" tabindex="-1" aria-labelledby="modalReviewLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title fw-semibold mb-0" id="modalReviewLabel">Detail & Review Entry</h5>
                    <p class="text-muted mb-0" style="font-size:12px" id="modal-subtitle"></p>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">

                {{-- Info grid --}}
                <div class="row g-3 mb-3">
                    <div class="col-6">
                        <label class="section-label">Atlet / Tim</label>
                        <div class="fw-medium" id="m-atlet">—</div>
                    </div>
                    <div class="col-6">
                        <label class="section-label">Klub</label>
                        <div class="fw-medium" id="m-team">—</div>
                    </div>
                    <div class="col-6">
                        <label class="section-label">Event</label>
                        <div class="fw-medium" id="m-event">—</div>
                    </div>
                    <div class="col-6">
                        <label class="section-label">Seed Time</label>
                        <div class="fw-medium" id="m-seed" style="font-family:monospace">—</div>
                    </div>
                    <div class="col-3">
                        <label class="section-label">Heat</label>
                        <div class="fw-medium" id="m-heat">—</div>
                    </div>
                    <div class="col-3">
                        <label class="section-label">Lane</label>
                        <div class="fw-medium" id="m-lane">—</div>
                    </div>
                    <div class="col-6">
                        <label class="section-label">Jenis Entry</label>
                        <div class="fw-medium" id="m-type">—</div>
                    </div>
                </div>

                <hr>

                {{-- Relay members (shown only for relay) --}}
                <div id="relay-section" style="display:none" class="mb-3">
                    <div class="section-label">Anggota Relay</div>
                    <div id="relay-members-list"></div>
                    <hr>
                </div>

                {{-- Officials --}}
                <div id="officials-section" class="mb-3">
                    <div class="section-label">Official Tim</div>
                    <div id="officials-list"></div>
                    <hr>
                </div>

                {{-- Current status --}}
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="text-muted" style="font-size:13px">Status saat ini:</span>
                    <span id="m-status-badge"></span>
                </div>

                {{-- Review note --}}
                <div class="mb-2">
                    <label class="form-label" style="font-size:12px;color:#6c757d">Catatan Review (opsional)</label>
                    <textarea class="form-control" id="review-note" rows="3"
                        placeholder="Tambahkan catatan untuk keputusan review ini..."></textarea>
                </div>
            </div>
            <div class="modal-footer flex-wrap gap-2 justify-content-end">
                <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-sm" style="background:#F3F4F6;color:#374151;border:1px solid #dee2e6"
                    onclick="doAction('withdrawn')">Withdrawn</button>
                <button type="button" class="btn btn-sm" style="background:#9D174D;color:#fff;border:none"
                    onclick="doAction('disqualified')">Disqualified</button>
                <button type="button" class="btn btn-danger btn-sm" onclick="doAction('rejected')">Tolak</button>
                <button type="button" class="btn btn-success btn-sm" onclick="doAction('active')">Terima</button>
            </div>
        </div>
    </div>
</div>

{{-- Delete form (hidden) --}}
<form id="delete-form" method="POST" style="display:none">
    @csrf
    @method('DELETE')
</form>


{{-- @push('scripts')
<script>
const REVIEW_URL  = "{{ route('admin.competitions.entries.review', [$competition, '__id__']) }}";
const BULK_URL    = "{{ route('admin.competitions.entries.bulk-review', $competition) }}";
const DELETE_URL  = "{{ route('admin.competitions.entries.destroy', [$competition, '__id__']) }}";
const CSRF        = "{{ csrf_token() }}";

let currentEntryId = null;
let selectedIds    = new Set();
let currentView    = 'group';

/* ── Helpers ── */
const badgeHtml = (status) => {
    const map = {
        pending:      ['badge-pending',      'Pending'],
        active:       ['badge-active',       'Active'],
        rejected:     ['badge-rejected',     'Rejected'],
        disqualified: ['badge-disqualified', 'Disqualified'],
        withdrawn:    ['badge-withdrawn',    'Withdrawn'],
    };
    const [cls, label] = map[status] || ['bg-secondary', status];
    return `<span class="badge rounded-pill ${cls}">${label}</span>`;
};



/* ── Group toggle collapse ── */
function toggleGroup(teamId) {
    const body   = document.getElementById('team-body-' + teamId);
    const chev   = document.getElementById('chevron-' + teamId);
    const isOpen = body.style.display !== 'none';
    body.style.display = isOpen ? 'none' : '';
    chev.style.transform = isOpen ? '' : 'rotate(90deg)';
}


/* ── Checkbox handling ── */
function toggleEntry(cb, id) {
    if (cb.checked) selectedIds.add(id); else selectedIds.delete(id);
    updateBulkBar();
}
function toggleAll(masterCb) {
    document.querySelectorAll('.entry-cb').forEach(cb => {
        if (cb.closest('tr').style.display !== 'none') {
            cb.checked = masterCb.checked;
            toggleEntry(cb, parseInt(cb.value));
        }
    });
}


/* ── Bulk action (flat) ── */
function bulkAction(status) {
    if (!selectedIds.size) return;
    sendBulk([...selectedIds], status);
}

/* ── Bulk action (per team) ── */
function bulkTeam(idStr, status) {
    const ids = idStr.split(',').map(Number);
    sendBulk(ids, status);
}

function sendBulk(ids, status) {
    fetch(BULK_URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ ids, status })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            ids.forEach(id => updateBadges(id, status));
            updateSummary(data.counts);
            selectedIds.clear();
            updateBulkBar();
            document.getElementById('cb-all') && (document.getElementById('cb-all').checked = false);
        }
    })
    .catch(() => alert('Terjadi kesalahan. Silakan coba lagi.'));
}

/* ── Open review modal ── */
function openModal(entryId) {
    currentEntryId = entryId;
    const url = REVIEW_URL.replace('__id__', entryId);
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.json())
        .then(data => {
            populateModal(data);
            new bootstrap.Modal(document.getElementById('modalReview')).show();
        });
}

function populateModal(data) {
    document.getElementById('modalReviewLabel').textContent =
        data.is_relay ? 'Review Entry Relay' : 'Review Entry Individual';
    document.getElementById('modal-subtitle').textContent = data.team + ' · ' + data.event;
    document.getElementById('m-atlet').textContent = data.atlet;
    document.getElementById('m-team').textContent  = data.team;
    document.getElementById('m-event').textContent = data.event;
    document.getElementById('m-seed').textContent  = data.seed_time || '—';
    document.getElementById('m-heat').textContent  = data.heat || 'Belum ditetapkan';
    document.getElementById('m-lane').textContent  = data.lane || 'Belum ditetapkan';
    document.getElementById('m-type').textContent  = data.is_relay ? 'Relay' : 'Individual';
    document.getElementById('m-status-badge').innerHTML = badgeHtml(data.status);
    document.getElementById('review-note').value   = '';

    // Relay members
    const relaySection = document.getElementById('relay-section');
    if (data.is_relay && data.relay_members && data.relay_members.length) {
        relaySection.style.display = '';
        document.getElementById('relay-members-list').innerHTML = data.relay_members.map(m => `
            <div class="relay-member-row">
                <div class="relay-leg-badge">${m.leg}</div>
                <span class="fw-medium flex-grow-1">${m.name}</span>
                <span class="text-muted" style="font-size:12px">${m.stroke || ''}</span>
            </div>`).join('');
    } else {
        relaySection.style.display = 'none';
    }

    // Officials
    const officialsList = document.getElementById('officials-list');
    if (data.officials && data.officials.length) {
        officialsList.innerHTML = data.officials.map(o => {
            const initials = o.name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
            return `<div class="official-row">
                <div class="official-avatar">${initials}</div>
                <div>
                    <div class="fw-medium" style="font-size:13px">${o.name}</div>
                    <div class="text-muted" style="font-size:11px">${o.role || ''}</div>
                </div>
            </div>`;
        }).join('');
        document.getElementById('officials-section').style.display = '';
    } else {
        document.getElementById('officials-section').style.display = 'none';
    }
}

/* ── Submit review ── */
function doAction(status) {
    if (!currentEntryId) return;
    const note = document.getElementById('review-note').value;
    const url  = REVIEW_URL.replace('__id__', currentEntryId);
    fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ status, note })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('modalReview')).hide();
            updateBadges(currentEntryId, status);
            updateSummary(data.counts);
        }
    })
    .catch(() => alert('Terjadi kesalahan. Silakan coba lagi.'));
}

/* ── Update badges in table without reload ── */
function updateBadges(id, status) {
    ['g', 'f'].forEach(prefix => {
        const badge = document.getElementById(`badge-${prefix}-${id}`);
        if (badge) badge.outerHTML = badgeHtml(status).replace('badge rounded-pill', 'badge rounded-pill')
            .replace('>', ` id="badge-${prefix}-${id}">`);
        const row = document.getElementById(`entry-row-${prefix}-${id}`);
        if (row) row.dataset.status = status;
    });
}

/* ── Update summary counts ── */
function updateSummary(counts) {
    if (!counts) return;
    document.getElementById('s-all').textContent        = counts.all        || 0;
    document.getElementById('s-pending').textContent    = counts.pending     || 0;
    document.getElementById('s-active').textContent     = counts.active      || 0;
    document.getElementById('s-rejected').textContent   = counts.rejected    || 0;
    document.getElementById('s-dq').textContent         = counts.disqualified|| 0;
    document.getElementById('tab-entry-count').textContent = counts.all     || 0;
}

/* ── Delete confirm ── */
function confirmDelete(entryId) {
    if (!confirm('Yakin ingin menghapus entry ini?')) return;
    const form = document.getElementById('delete-form');
    form.action = DELETE_URL.replace('__id__', entryId);
    form.submit();
}
</script>
@endpush --}}

@push('scripts')
    <script>
        const eventSelect = $('#event_id').select2({
            width:'100%',
            placeholder:'Filter Event',
            allowClear:true,
            minimumInputLength:0,
            ajax:{
                url:"{{ route('getAllEvent') }}",
                dataType:'json',
                delay:250,
                data:function(params){
                    return {
                        q:params.term || '',
                        page:params.page || 1,
                        competition_id : "{{ $competition->id }}"
                    };
                },
                processResults:function(res,params){
                    params.page = params.page || 1;

                    return {
                        results:(res.data || []).map(row => ({
                            id:row.id,
                            text:`[Event ${row.event_number ?? ''}] ${row.distance ?? '-'} M ${row.stroke ?? '-'} • ${row.gender} • ${row.event_type}`
                        })),
                        pagination:{
                            more:res.pagination?.more || false
                        }
                    };
                },
                cache:true,
            },
            templateResult:function(item){
                if (item.loading) return item.text;
                return item.text;
            },
            templateSelection:function(item){
                return item.text || item.id;
            }
        });
        const clubSelect = $('#club_id').select2({
            width:'100%',
            placeholder:'Filter Klub',
            allowClear:true,
            minimumInputLength:0,
            ajax:{
                url:"{{ route('getClubByCategory') }}",
                dataType:'json',
                delay:250,
                data:function(params){
                    return {
                        q:params.term || '',
                        page:params.page || 1,
                    };
                },
                processResults:function(res,params){
                    params.page = params.page || 1;

                    return {
                        results:(res.data || []).map(row => ({
                            id:row.id,
                            text:`[${row.club_code ?? ''}] ${row.club_name ?? ''}`
                        })),
                        pagination:{
                            more:res.pagination?.more || false
                        }
                    };
                },
                cache:true,
            },
            templateResult:function(item){
                if (item.loading) return item.text;
                return item.text;
            },
            templateSelection:function(item){
                return item.text || item.id;
            }
        });

        let currentEntryId = null;
        let selectedIds    = new Set();
        let currentView    = 'group';

        /* ── View toggle ── */
        function setView(v) {
            currentView = v;
            selectedIds.clear();
            updateBulkBar();
            document.getElementById('view-group').style.display = v === 'group' ? '' : 'none';
            document.getElementById('view-flat').style.display  = v === 'flat'  ? '' : 'none';
            document.getElementById('btn-view-group').classList.toggle('active', v === 'group');
            document.getElementById('btn-view-flat').classList.toggle('active', v === 'flat');
        }

        function updateBulkBar() {
            const bar = document.getElementById('bulk-action-bar');
            document.getElementById('bulk-count').textContent = selectedIds.size + ' entry dipilih';
            bar.classList.toggle('show', currentView === 'flat' && selectedIds.size > 0);
        }

        /* ── Quick filter via stat card ── */
        function quickFilter(status) {
            document.getElementById('f-status').value = status;
            applyFilter();
        }
        /* ── Filter (flat view — hide/show rows) ── */
        function applyFilter() {
            const fs = document.getElementById('f-status').value;
            const fe = document.getElementById('event_id').value;
            const ft = document.getElementById('club_id').value;
            document.querySelectorAll('#flat-body tr').forEach(tr => {
                const matchStatus = !fs || tr.dataset.status === fs;
                const matchEvent  = !fe || tr.dataset.event  === fe;
                const matchTeam   = !ft || tr.dataset.team   === ft;
                tr.style.display  = (matchStatus && matchEvent && matchTeam) ? '' : 'none';
            });
            selectedIds.clear();
            updateBulkBar();
        }
    </script>
@endpush
