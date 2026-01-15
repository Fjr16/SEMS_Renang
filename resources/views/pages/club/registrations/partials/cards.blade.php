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

@if($data->count() === 0)
    <div class="soft-card p-4 text-center">
    <div class="mb-2"><i class="bi bi-info-circle fs-2 text-secondary"></i></div>
    <div class="fw-semibold">Belum ada kompetisi yang open.</div>
    <div class="text-secondary">Silakan cek kembali nanti atau hubungi panitia.</div>
    </div>
@else
    <div class="competition-list">
        @foreach($data as $c)
        <div class="comp-item soft-card p-3">
            <div class="d-flex justify-content-between align-items-start gap-2">
                <div class="min-w-0">
                    <div class="fw-semibold text-truncate">
                        {{ $c->name ?? '-' }}
                    </div>
                    <div class="small text-muted text-truncate">
                        <span class="me-2">[{{ $c->organizer ?? '-' }}]</span>
                        <span>• {{ $c->location ?? '-' }}</span>
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

            {{-- actions --}}
            <div class="d-flex gap-2 mt-3">
            <a href="{{ route('manager.club.registration.create', ['competition' => $c]) }}" class="btn btn-primary btn-sm comp-btn">
                <i class="bi bi-clipboard-check me-1"></i>Daftar
            </a>
            <a href="#" class="btn btn-outline-secondary btn-sm comp-btn">
                <i class="bi bi-eye me-1"></i>Detail
            </a>
            </div>
        </div>
        @endforeach
    </div>

    @if(method_exists($data,'links'))
    <div class="d-flex justify-content-center mt-4">
        {{ $data->withQueryString()->links() }}
    </div>
    @endif
@endif


@if($data->nextPageUrl())
  <div class="js-next-page" data-next="{{ $data->nextPageUrl() }}"></div>
@endif
