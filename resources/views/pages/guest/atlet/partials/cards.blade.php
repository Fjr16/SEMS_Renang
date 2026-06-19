@foreach($athletes as $a)
    @php
    $gender = strtoupper($a->gender ?? '');
    $photo  = $a->foto ?? null;
    $code   = $a->code ?? '-';
    $name   = $a->name ?? '-';
    $no_reg = $a->id ?? '-';
    $clubCurrent = '[' . ($a->club?->club_code ?? '-') . '] - ' . ($a->club?->club_name ?? '-');
    $bod    = $a->bod ?? null;

    try {
        $bodLabel = $bod ? \Carbon\Carbon::parse($bod)->translatedFormat('d F Y') : '-';
    } catch (\Throwable $e) {
        $bodLabel = $bod ?? '-';
    }
    @endphp

    <div class="col-12 col-sm-6 col-lg-4 athlete-item">
    <div class="ath-card h-100">
        <div class="ath-cover"></div>

        <div class="px-3 pb-3">
        <div class="d-flex align-items-end gap-2">
            @if($photo)
            <img class="avatar" src="{{ Storage::url($photo) }}" alt="{{ $name }}">
            @else
            <div class="avatar d-flex align-items-center justify-content-center">
                <i class="bi bi-person text-secondary fs-3"></i>
            </div>
            @endif

            <div class="flex-grow-1 pb-1">
            <div class="d-flex align-items-center justify-content-between gap-2">
                <div class="fw-semibold text-truncate" title="{{ $name }}">{{ $name }}</div>

                @if($gender === 'MALE')
                <span class="badge badge-soft">MALE</span>
                @elseif($gender === 'FEMALE')
                <span class="badge text-bg-warning">FEMALE</span>
                @endif
            </div>

            <div class="text-secondary small">
                <span class="badge text-bg-light border">[{{ $code }}]</span>
                <span class="ms-1 d-inline-block" style="max-width: 210px" title="{{ $no_reg }}">
                No. Reg : {{ $no_reg }}
                </span>
            </div>
            </div>
        </div>

        <div class="kvs mt-3">
            <div class="kv"><small>BOD</small>{{ $bodLabel }}</div>
            <div class="kv"><small>Klub</small>{{ $clubCurrent }}</div>
            {{-- <div class="kv"><small>Kota</small>{{ $a->kota ?? '-' }}</div>
            <div class="kv"><small>Provinsi</small>{{ $a->provinsi ?? '-' }}</div> --}}
        </div>

        <div class="mt-2">
            <a href="{{ route('guest.atlet.show', $a->id) }}" class="btn btn-primary w-100">
                <i class="bi bi-person-vcard me-1"></i>Detail
            </a>
        </div>
        </div>
    </div>
    </div>
@endforeach


@if($athletes->nextPageUrl())
  <div class="js-next-page" data-next="{{ $athletes->nextPageUrl() }}"></div>
@endif
