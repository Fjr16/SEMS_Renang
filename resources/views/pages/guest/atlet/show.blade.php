@extends('layouts.main')

@section('content')

<style>
    .ath-detail-hero {
        background: linear-gradient(90deg, rgba(13,110,253,.18), rgba(13,110,253,.05));
        height: 80px;
    }
    .ath-detail-avatar {
        width: 68px;
        height: 68px;
        border-radius: 14px;
        border: 3px solid #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,.10);
        object-fit: cover;
        background: #e9ecef;
        margin-top: -34px;
        flex-shrink: 0;
    }
    .ath-detail-avatar-placeholder {
        width: 68px;
        height: 68px;
        border-radius: 14px;
        border: 3px solid #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,.10);
        background: rgba(13,110,253,.10);
        margin-top: -34px;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 600;
        color: #0d6efd;
    }
    .badge-soft {
        background: rgba(13,110,253,.10);
        color: #0d6efd;
        border: 1px solid rgba(13,110,253,.25);
        font-weight: 600;
    }
    .ath-stat {
        background: #f6f8fb;
        border-radius: .75rem;
        padding: .65rem .9rem;
    }
    .ath-stat .val {
        font-size: 1.3rem;
        font-weight: 700;
        color: #111827;
    }
    .ath-stat .lbl {
        font-size: .78rem;
        color: #6c757d;
        margin-top: 1px;
    }
    .kvs {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: .5rem;
        font-size: .9rem;
        color: #111827;
    }
    .kv {
        padding: .5rem .6rem;
        border: 1px dashed rgba(0,0,0,.10);
        border-radius: .75rem;
        background: rgba(0,0,0,.015);
    }
    .kv small {
        color: #6c757d;
        display: block;
        font-size: .78rem;
        margin-bottom: 2px;
    }
    .event-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: .65rem 0;
        border-bottom: 1px dashed rgba(0,0,0,.08);
    }
    .event-row:last-child { border-bottom: none; }
    .time-pill {
        background: rgba(13,110,253,.10);
        color: #0d6efd;
        border: 1px solid rgba(13,110,253,.2);
        border-radius: 999px;
        padding: 2px 12px;
        font-size: .82rem;
        font-weight: 600;
        white-space: nowrap;
    }
    .pt-row {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: .65rem 0;
        border-bottom: 1px dashed rgba(0,0,0,.08);
    }
    .pt-row:last-child { border-bottom: none; }
    .pt-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(13,110,253,.08);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 1.1rem;
        color: #0d6efd;
    }
    .nav-tabs .nav-link {
        border-radius: .65rem .65rem 0 0;
        font-size: .88rem;
        font-weight: 500;
        color: #6c757d;
    }
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        font-weight: 600;
    }
    .pr-badge {
        font-size: .68rem;
        background: rgba(25,135,84,.12);
        color: #146c43;
        border: 1px solid rgba(25,135,84,.25);
        border-radius: 999px;
        padding: 1px 7px;
        margin-left: 5px;
        vertical-align: middle;
    }
    .empty-state {
        border: 1px dashed rgba(0,0,0,.15);
        background: rgba(255,255,255,.8);
        border-radius: 1rem;
    }
    .sec-label {
        font-size: .75rem;
        letter-spacing: .06em;
        text-transform: uppercase;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: .6rem;
    }
</style>

{{-- Breadcrumb / Back --}}
<div class="mb-3">
    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary" style="border-radius:999px;">
        <i class="bi bi-arrow-left me-1"></i>Kembali
    </a>
</div>

{{-- HERO CARD --}}
<div class="card border-0 shadow-sm mb-4" style="border-radius:1rem; overflow:hidden;">
    <div class="ath-detail-hero"></div>
    <div class="px-3 pb-3">
        <div class="d-flex align-items-end gap-3">

            {{-- Avatar / Initials --}}
            @if($athlete->foto)
                <img class="ath-detail-avatar"
                     src="{{ Storage::url($athlete->foto) }}"
                     alt="{{ $athlete->name }}">
            @else
                @php
                    $initials = collect(explode(' ', $athlete->name))
                        ->take(2)
                        ->map(fn($w) => strtoupper($w[0]))
                        ->join('');
                @endphp
                <div class="ath-detail-avatar-placeholder">{{ $initials }}</div>
            @endif

            <div class="flex-grow-1 pb-1">
                <div class="d-flex align-items-center flex-wrap gap-2">
                    <h5 class="fw-bold mb-0">{{ $athlete->name ?? '-' }}</h5>

                    <span class="badge {{ $athlete->gender ? App\Enums\Gender::tryFrom($athlete->gender)->class() : 'bg-danger text-white' }}">{{ $athlete->gender ? App\Enums\Gender::tryFrom($athlete->gender)->label() : '-' }}</span>

                    @if(($athlete->status ?? '') === 'active')
                        <span class="badge text-bg-success" style="font-size:.72rem;">
                            <i class="bi bi-circle-fill me-1" style="font-size:.45rem;vertical-align:1px;"></i>Aktif
                        </span>
                    @else
                        <span class="badge text-bg-secondary" style="font-size:.72rem;">Non-aktif</span>
                    @endif
                </div>

                <div class="mt-1 d-flex flex-wrap align-items-center gap-2">
                    <span class="badge text-bg-light border">[{{ $athlete->code ?? '-' }}]</span>
                    <span class="text-secondary small">No. Reg: {{ $athlete->registration_number ?? '-' }}</span>
                </div>
            </div>
        </div>

        {{-- Stat strip --}}
        <div class="row g-2 mt-3">
            <div class="col-4">
                <div class="ath-stat text-center">
                    <div class="val">{{ $totalEvents ?? 0 }}</div>
                    <div class="lbl">Total event</div>
                </div>
            </div>
            <div class="col-4">
                <div class="ath-stat text-center">
                    <div class="val">{{ $totalPodium ?? 0 }}</div>
                    <div class="lbl">Podium</div>
                </div>
            </div>
            <div class="col-4">
                <div class="ath-stat text-center">
                    <div class="val">{{ $totalPR ?? 0 }}</div>
                    <div class="lbl">PR dicatat</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- TABS --}}
<ul class="nav nav-tabs mb-3" id="athTab" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active"
                id="tab-info-btn"
                data-bs-toggle="tab"
                data-bs-target="#tab-info"
                type="button" role="tab">
            <i class="bi bi-person-vcard me-1"></i>Informasi
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="tab-event-btn"
                data-bs-toggle="tab"
                data-bs-target="#tab-event"
                type="button" role="tab">
            <i class="bi bi-trophy me-1"></i>Event history
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link"
                id="tab-pt-btn"
                data-bs-toggle="tab"
                data-bs-target="#tab-pt"
                type="button" role="tab">
            <i class="bi bi-clock-history me-1"></i>Personal time
        </button>
    </li>
</ul>

<div class="tab-content" id="athTabContent">

    {{-- ── TAB: INFORMASI ── --}}
    <div class="tab-pane fade show active" id="tab-info" role="tabpanel">
        <div class="card border-0 shadow-sm p-3" style="border-radius:1rem;">
            <p class="sec-label">Data pribadi</p>
            <div class="kvs">
                <div class="kv">
                    <small>Tanggal lahir</small>
                    @php
                        try {
                            echo $athlete->bod
                                ? \Carbon\Carbon::parse($athlete->bod)->translatedFormat('d F Y')
                                : '-';
                        } catch (\Throwable $e) {
                            echo $athlete->bod ?? '-';
                        }
                    @endphp
                    @php
                        try {
                            echo $athlete->bod
                                ? '( ' . \Carbon\Carbon::parse($athlete->bod)->age . ' tahun )'
                                : '-';
                        } catch (\Throwable $e) {
                            echo '-';
                        }
                    @endphp
                </div>
                <div class="kv">
                    <small>Gender</small>
                    {{ ucfirst($athlete->gender ? App\Enums\Gender::tryFrom($athlete->gender)->label()  : '-') }}
                </div>
                <div class="kv">
                    <small>Kota</small>
                    {{ ucfirst($athlete->kota ?? '-') }}
                </div>
                <div class="kv">
                    <small>Provinsi</small>
                    {{ ucfirst($athlete->provinsi ?? '-') }}
                </div>
                <div class="kv">
                    <small>Klub</small>
                    [{{ $athlete->club?->club_code ?? '-' }}] — {{ $athlete->club?->club_name ?? '-' }}
                </div>
                <div class="kv">
                    <small>Terdaftar Pada</small>
                    @php
                        try {
                            echo $athlete->created_at
                                ? \Carbon\Carbon::parse($athlete->bod)->translatedFormat('d F Y')
                                : '-';
                        } catch (\Throwable $e) {
                            echo '-';
                        }
                    @endphp
                </div>
            </div>
        </div>
    </div>

    {{-- ── TAB: EVENT HISTORY ── --}}
    <div class="tab-pane fade" id="tab-event" role="tabpanel">
        <div class="card border-0 shadow-sm p-3" style="border-radius:1rem;">
            <p class="sec-label">Riwayat event diikuti</p>

            @forelse($eventHistories ?? [] as $ev)
                @php
                    $isPR = $ev->is_personal_record ?? false;
                    $rank = $ev->rank ?? null;
                @endphp
                <div class="event-row">
                    <div>
                        <div class="fw-semibold" style="font-size:.9rem;">
                            {{ $ev->event?->name ?? '-' }}
                            @if($isPR)<span class="pr-badge">PR</span>@endif
                        </div>
                        <div class="text-secondary" style="font-size:.78rem;">
                            {{ $ev->competition?->name ?? '-' }}
                            @if($ev->competition?->date)
                                &middot;
                                {{ \Carbon\Carbon::parse($ev->competition->date)->translatedFormat('d M Y') }}
                            @endif
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-shrink-0">
                        @if($rank)
                            <span class="badge text-bg-success" style="font-size:.72rem;">#{{ $rank }}</span>
                        @endif
                        <span class="time-pill">{{ $ev->time ?? '-' }}</span>
                    </div>
                </div>
            @empty
                <div class="empty-state p-4 text-center">
                    <i class="bi bi-calendar-x fs-2 text-secondary mb-2 d-block"></i>
                    <div class="fw-semibold">Belum ada riwayat event.</div>
                    <div class="text-secondary small">Atlet ini belum mengikuti event apapun.</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- ── TAB: PERSONAL TIME ── --}}
    <div class="tab-pane fade" id="tab-pt" role="tabpanel">
        <div class="card border-0 shadow-sm p-3" style="border-radius:1rem;">
            <p class="sec-label">Waktu terbaik pribadi</p>

            @forelse($personalTimes ?? [] as $pt)
                <div class="pt-row">
                    <div class="pt-icon">
                        <i class="bi bi-water"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="fw-semibold" style="font-size:.88rem;">
                            {{ $pt->event?->name ?? '-' }}
                        </div>
                        <div class="text-secondary" style="font-size:.78rem;">
                            {{ $pt->pool_type ?? 'Kolam 50m' }}
                        </div>
                    </div>
                    <div class="text-end flex-shrink-0">
                        <div class="fw-bold" style="font-size:.95rem; color:#111827;">
                            {{ $pt->time ?? '-' }}
                        </div>
                        <div class="text-secondary" style="font-size:.75rem;">
                            @if($pt->achieved_at)
                                {{ \Carbon\Carbon::parse($pt->achieved_at)->translatedFormat('d M Y') }}
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state p-4 text-center">
                    <i class="bi bi-stopwatch fs-2 text-secondary mb-2 d-block"></i>
                    <div class="fw-semibold">Belum ada catatan waktu.</div>
                    <div class="text-secondary small">Personal time atlet ini belum tersedia.</div>
                </div>
            @endforelse
        </div>
    </div>

</div>

@endsection
