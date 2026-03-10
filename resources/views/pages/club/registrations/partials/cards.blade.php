<style>
    /* wrapper list */
    .competition-list{
    display: grid;
    gap: 12px;
    }

    /* tiap item */
    .comp-item{
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 14px;
    background: #fff;
    transition: .15s ease;
    }
    .comp-item:hover{
    transform: translateY(-1px);
    box-shadow: 0 10px 24px rgba(0,0,0,.06);
    }

    /* meta */
    .comp-meta{
    border-top: 1px dashed rgba(0,0,0,.10);
    padding-top: 10px;
    }
    .meta-row{
    display: flex;
    gap: 10px;
    align-items: baseline;
    justify-content: space-between;
    margin-top: 4px;
    }
    .meta-label{
    font-size: .78rem;
    text-transform: uppercase;
    letter-spacing: .02em;
    color: #6c757d;
    font-weight: 700;
    white-space: nowrap;
    }
    .meta-value{
    font-size: .9rem;
    color: #212529;
    text-align: right;
    }

    /* tombol lebih efisien */
    .comp-btn{
    border-radius: 999px;
    padding: .35rem .75rem;
    }

    /* desktop: buat 2 kolom biar padat */
    @media (min-width: 768px){
    .competition-list{
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
    }

    /* desktop besar: 3 kolom */
    @media (min-width: 1200px){
    .competition-list{
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    }
</style>

<div class="competition-list">
    @foreach($data as $c)
    <div class="comp-item soft-card p-3">
        <div class="d-flex justify-content-between align-items-start gap-2">
            <div class="min-w-0">
                <div class="fw-semibold text-truncate">
                    {{ $c->name ?? '-' }}
                </div>
                <div class="small text-muted">
                    <span class="me-2">[{{ $c->organization->name ?? '-' }}]</span>
                    <span>• {{ $c->venue->name ?? '-' }}</span>
                </div>
            </div>

            <span class="badge rounded-pill border {{ $compClass::tryFrom($c?->status ?? '')?->class() ?? '' }} small">
            {{ $compClass::tryFrom($c?->status ?? '')?->label() ?? '-' }}
            </span>
        </div>

        {{-- info ringkas: 2 baris --}}
        <div class="comp-meta mt-2">
        <div class="meta-row">
            <span class="meta-label">Registrasi</span>
            <span class="meta-value">
            {{ $c->registration_start ?? '-' }} — {{ $c->registration_end ?? '-' }}
            </span>
        </div>
        <div class="meta-row">
            <span class="meta-label">Kompetisi</span>
            <span class="meta-value">
            {{ $c->start_date ?? '-' }} — {{ $c->end_date ?? '-' }}
            </span>
        </div>
        </div>

        @php
            $ableToRegister=false;
            $stts = $compClass::tryFrom($c?->status ?? '');
            if ($stts == $compClass::from('REGISTRATION')) {
                $ableToRegister=true;
            }
        @endphp

        {{-- actions --}}
        @if ($ableToRegister)
        <div class="mt-3">
            <a href="{{ route('manager.club.registration.create', ['competition' => $c]) }}" class="btn btn-primary btn-sm comp-btn">
                <i class="bi bi-clipboard-check me-1"></i>Daftar
            </a>
        </div>
        @else
        <div class="mt-3">
            <a class="btn btn-primary btn-sm comp-btn disabled">
                <i class="bi bi-clipboard-check me-1"></i>Daftar
            </a>
        </div>
        @endif
    </div>
    @endforeach
</div>


@if($data->nextPageUrl())
  <div class="js-next-page" data-next="{{ $data->nextPageUrl() }}"></div>
@endif
