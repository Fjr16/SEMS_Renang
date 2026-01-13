@if($data->count() === 0)
    <div class="soft-card p-4 text-center">
    <div class="mb-2"><i class="bi bi-info-circle fs-2 text-secondary"></i></div>
    <div class="fw-semibold">Belum ada kompetisi yang open.</div>
    <div class="text-secondary">Silakan cek kembali nanti atau hubungi panitia.</div>
    </div>
@else
    <div class="row g-3">
    @foreach($data as $c)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="soft-card p-3 h-100">
                <div class="fw-bold">[{{ $c->organizer ?? '-' }}] {{ $c->name ?? '-' }}</div>
                <div class="text-secondary small mt-1">{{ $c->location ?? '-' }}</div>

                <div class="row g-2 mt-2">
                    <div class="col-6"><div class="mini-kv"><small>Buka Registrasi</small>{{ $c->registration_start ?? '-' }}</div></div>
                    <div class="col-6"><div class="mini-kv"><small>Tutup Registrasi</small>{{ $c->registration_end ?? '-' }}</div></div>
                    <div class="col-6"><div class="mini-kv"><small>Mulai Kompetisi</small>{{ $c->start_date ?? '-' }}</div></div>
                    <div class="col-6"><div class="mini-kv"><small>Akhir Kompetisi</small>{{ $c->end_date ?? '-' }}</div></div>
                </div>

                <div class="d-flex gap-2 mt-3">
                    <a href="#" class="btn btn-primary btn-pill w-100">
                        <i class="bi bi-clipboard-check me-1"></i>Daftar
                    </a>
                    <a href="#" class="btn btn-outline-secondary btn-pill w-100">
                        <i class="bi bi-eye me-1"></i>Detail
                    </a>
                </div>
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
