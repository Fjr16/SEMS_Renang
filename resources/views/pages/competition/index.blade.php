@extends('layouts.main')

<style>
    .split-action {
        position: relative;
        display: inline-flex;
        align-items: stretch;
        font-family: inherit;
    }

    /* MAIN BUTTON */
    .split-main {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid #5ab8ff;
        border-right: none;
        border-radius: 8px 0 0 8px;
        background: #e7f5ff;
        color: #1971c2;
        cursor: pointer;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .split-main:hover {
        background: #d0ebff;
        color: #1864ab;
    }

    /* CARET BUTTON */
    .split-caret {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 32px;
        border: 1px solid #dee2e6;
        border-left: 1px solid #e9ecef;
        border-radius: 0 8px 8px 0;
        background: #a7a4a4;
        color: #fafafa;
        cursor: pointer;
        transition: all 0.15s ease;
        font-size: 12px;
    }

    .split-caret:hover {
        background: #f1f3f5;
        color: #212529;
    }

    /* ACTIVE STATE (biar berasa klik) */
    .split-main:active,
    .split-caret:active {
        background: #e9ecef;
        transform: scale(0.97);
    }

    /* FOCUS (aksesibilitas + modern glow) */
    .split-main:focus,
    .split-caret:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(13,110,253,0.15);
    }

    /* ICON ROTATION */
    .split-caret i {
        transition: transform 0.2s ease;
    }
    .split-caret.open i {
        transform: rotate(180deg);
    }

    /* DROPDOWN */
    .split-dropdown {
        display: none;
        position: absolute;
        top: calc(100% + 6px);
        left: 0;
        min-width: 160px;
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 10px;
        box-shadow: 0 8px 20px rgba(0,0,0,0.08);
        z-index: 1050;
        overflow: hidden;
        animation: fadeIn 0.15s ease;
    }

    .split-dropdown.show {
        display: block;
    }

    /* ITEM */
    .split-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 9px 14px;
        font-size: 13px;
        color: #212529;
        cursor: pointer;
        transition: all 0.12s ease;
    }

    .split-item:hover {
        background: #f1f3f5;
    }

    /* DANGER ITEM */
    .split-item.danger {
        color: #dc3545;
    }
    .split-item.danger:hover {
        background: #fff1f2;
    }

    /* DIVIDER */
    .split-divider {
        height: 1px;
        background: #f1f3f5;
        margin: 4px 0;
    }

    /* ANIMATION */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
        <div>
            <h2 class="fw-bold mb-1">Kompetisi & Event</h2>
            <p class="text-muted mb-0">Kelola data Kompetisi dan Event yang terdaftar dalam sistem</p>
        </div>
        <div class="mt-3 mt-md-0">
            <button data-bs-toggle="modal" data-bs-target="#modalCompetition" class="btn btn-primary" onclick="$('#modalTitle').text('Tambah Kompetisi'); $('#competition_id').val(''); document.getElementById('form-submit').reset();">
                <i class="bi bi-plus-circle me-1"></i> Tambah Kompetisi
            </button>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <table id="competitionTable" class="table table-striped">
                <thead>
                    <tr class="text-nowrap">
                        <th>Aksi</th>
                        <th>Nama Kompetisi</th>
                        <th>Penyelenggara</th>
                        <th>Waktu Kompetisi</th>
                        <th>Arena</th>
                        <th>Waktu Pendaftaran</th>
                        <th>Status</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalCompetition" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
            <form id="form-submit">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Tambah Kompetisi</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <input type="hidden" name="competition_id" id="competition_id">
                        <div class="row">
                            <div class="col-md-8">
                                <label class="form-label" for="name">Nama Kompetisi</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="Nama Kompetisi">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label" for="organization_id">Penyelenggara</label>
                                <select name="organization_id" class="form-control" id="organization_id" style="width: 100%"></select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="sanction_number">No. Legalisasi</label>
                        <input type="text" class="form-control" name="sanction_number" id="sanction_number">
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="venue_id">Lokasi Kompetisi / Arena / Venue</label>
                        <select name="venue_id" class="form-control" id="venue_id" style="width: 100%"></select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="">Tanggal Kompetisi</label>
                        <div class="row">
                            <div class="col-md-6">
                                <input type="text" class="form-control mark-date" id="start_date" name="start_date" placeholder="Tanggal Mulai Kompetisi">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control mark-date" id="end_date" name="end_date" placeholder="Tanggal Selesai Kompetisi">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="row">
                            <label class="form-label">Tanggal Registrasi</label>
                            <div class="col-md-6">
                                <input type="text" class="form-control mark-date" id="registration_start" name="registration_start" placeholder="Tanggal Mulai Registrasi">
                            </div>
                            <div class="col-md-6">
                                <input type="text" class="form-control mark-date" id="registration_end" name="registration_end" placeholder="Tanggal Tutup Registrasi">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="status">Status Kompetisi</label>
                        <select class="form-control" name="status" id="status">
                            @foreach ($data as $stts)
                                <option value="{{ $stts->value }}" @selected(old('status') == $stts->value)>{{ $stts->label() }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="description">Deskripsi</label>
                        <input type="text" class="form-control" name="description" id="description">
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-primary">Simpan</button></div>
            </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
    <script>
        var table;

        $('#venue_id').select2({
            width:'100%',
            placeholder:'Cari Venue',
            allowClear:true,
            minimumInputLength:0,
            dropdownParent: $('#modalCompetition'),
            ajax:{
                url:"{{ route('getVenue') }}",
                dataType:'json',
                delay:250,
                data:function(params){
                    return {
                        q:params.term || '',
                        page:params.page || 1
                    };
                },
                processResults:function(res,params){
                    params.page = params.page || 1;
                    return {
                        results:(res.data || []).map(row => ({
                            id:row.id,
                            text:`[${row.code ?? ''}] ${row.name ?? ''}`
                        })),
                        pagination:{
                            more:res.pagination?.more || false
                        }
                    };
                },
                cache:true,
            },
             templateResult:function(item){
                return item.text;
            },
            templateSelection:function(item){
                return item.text || item.id;
            }
        });
        $('#organization_id').select2({
            width:'100%',
            placeholder:'Cari Penyelenggara',
            allowClear:true,
            minimumInputLength:0,
            dropdownParent:$('#modalCompetition'),
            ajax:{
                url:"{{ route('getOrganization') }}",
                dataType:'json',
                delay:250,
                data:function(params){
                    return {
                        q:params.term || '',
                        page:params.page || 1
                    };
                },
                processResults:function(res,params){
                    params.page = params.page || 1;
                    return {
                        results:(res.data || []).map(row => ({
                            id:row.id,
                            text:row.name
                        })),
                        pagination:{
                            more:res.pagination?.more || false
                        }
                    };
                },
                cache:true,
            },
            templateResult:function(item){
                return item.text;
            },
            templateSelection:function(item){
                return item.text || item.id;
            }
        });

        $(document).ready(function(){
            table = $('#competitionTable').DataTable({
                processing:true,
                serverSide:true,
                ajax:"{{ route('competition.data') }}",
                columnDefs:[
                    {target:0, className:'dt-actions'}
                ],
                columns:[
                    {data:'action', name:'action', className:'text-center', orderable:false, searchable:false},
                    {data:'comp_desc', name:'name', orderable:true, searchable:true},
                    {data:'organizer', name:'organization.name', orderable:true, searchable:true},
                    {data:'comp_date', name:'start_date', orderable:true, searchable:true},
                    {data:'venue_desc', name:'venue.name', orderable:true, searchable:true},
                    {data:'registration_date', name:'registration_start', orderable:true, searchable:true},
                    {data:'statusAttr', name:'status', className:'text-center', orderable:true, searchable:true},
                ],
                order:[[6,'asc']],
            });
        });

        const $globalDropdown = $('<div class="split-dropdown" id="globalSplitDropdown"></div>').appendTo('body');
        let $currentCaret = null;

        function toggleSplit(caretBtn) {
            const $caret = $(caretBtn);
            const $originalDropdown = $caret.closest('.split-action').find('.split-dropdown');

            // Kalau klik caret yang sama → tutup
            if ($currentCaret && $currentCaret.is($caret) && $globalDropdown.hasClass('show')) {
                closeAllSplitDropdowns();
                return;
            }

            closeAllSplitDropdowns();

            // Salin konten dropdown ke global dropdown
            $globalDropdown.html($originalDropdown.html());

            // Posisikan
            const rect = caretBtn.getBoundingClientRect();
            $globalDropdown.css({
                position: 'fixed',
                top: rect.bottom + 6,
                left: rect.left,
                zIndex: 9999,
            }).addClass('show');

            $caret.addClass('open');
            $currentCaret = $caret;
        }

        function closeAllSplitDropdowns() {
            $globalDropdown.removeClass('show').html('');
            $('.split-caret').removeClass('open');
            $currentCaret = null;
        }

        $(document).on('click', '.split-caret', function(e) {
            e.stopPropagation();
        });

        $(document).on('click', function(e) {
            if (!$(e.target).closest('.split-action').length && !$(e.target).closest('#globalSplitDropdown').length) {
                closeAllSplitDropdowns();
            }
        });

        $(document).on('draw.dt', '#competitionTable', function() {
            closeAllSplitDropdowns();
        });

        $(window).on('scroll resize', function() {
            closeAllSplitDropdowns();
        });

        $('#form-submit').on('submit', async function(e){
            e.preventDefault();
            showSpinner();

            let formData = new FormData(this);

            try {
                const res = await fetch("{{ route('competition.store') }}", {
                    method:'POST',
                    headers: {
                        'X-CSRF-TOKEN' : "{{ csrf_token() }}"
                    },
                    body:formData,
                });

                if(!res.ok) {
                    throw new Error("Terjadi kesalahan pada server");
                }

                const result = await res.json();

                hideSpinner();
                if (result.status) {
                    table.ajax.reload();
                    $('#modalCompetition').modal('hide');
                    this.reset();
                    Toast.fire({
                        icon: 'success',
                        title: result.message || 'Success'
                    });
                }else{
                    console.log(result.message);
                    Toast.fire({
                        icon: 'error',
                        title: result.message || 'Error'
                    });
                }

            } catch (error) {
                hideSpinner();
                console.log(error.message);
                Toast.fire({
                    icon: 'error',
                    title: error.message || 'Terjadi Kesalahan'
                });
            }
        });

        function edit(element){
            closeAllSplitDropdowns();
            $('#modalTitle').text('Edit Kompetisi');
            $('#form-submit')[0].reset();

            const compId = $(element).data('id');
            const data = table.rows().data().toArray().find(r => r.id == compId);

            $('#competition_id').val(data.id);
            $('#name').val(data.name);
            if (data.organization_id) {
                const option = new Option(
                    data.organization.name,
                    data.organization_id,
                    true,
                    true
                );
                $('#organization_id').append(option).trigger('change');
            }
            $('#start_date').flatpickr().setDate(data.start_date);
            $('#end_date').flatpickr().setDate(data.end_date);
            if (data.venue_id) {
                const option = new Option(
                    `[${data.venue.code ?? ''}] ${data.venue.name ?? ''}`,
                    data.venue_id,
                    true,
                    true
                );
                $('#venue_id').append(option).trigger('change');
            }
            $('#registration_start').flatpickr().setDate(data.registration_start);
            $('#registration_end').flatpickr().setDate(data.registration_end);
            $('#status').val(data.status);
            $('#sanction_number').val(data.sanction_number);
            $('#description').val(data.description);
            $('#modalCompetition').modal('show');
        }

        async function destroy(element){
            closeAllSplitDropdowns();
            const { isConfirmed } = await Swal.fire({
                title: "Konfirmasi Hapus",
                text: "Apakah anda yakin ingin menghapus data ini?",
                icon: "warning",
                showCancelButton: true,
                cancelButtonText:'Batal',
                confirmButtonText: "Ya, Hapus!"
            });

            if(!isConfirmed) return;

            try {
                const compId = element.dataset.id;
                const url = "{{ route('competition.destroy', ':id') }}".replace(':id', compId);
                const res = await fetch(url, {
                    method:'DELETE',
                    headers: {
                        'X-CSRF-TOKEN' : '{{ csrf_token() }}',
                    }
                });
                if (!res.ok) {
                    throw new Error("Terjadi Kesalahan Server");
                }
                const result = await res.json();
                table.ajax.reload();
                if (result.status) {
                    Toast.fire({
                        icon:'success',
                        title:result.message || 'Berhasil hapus data'
                    })
                }else{
                    Toast.fire({
                        icon:'error',
                        title:result.message ?? 'Gagal Hapus Data'
                    })
                }
            } catch (error) {
                table.ajax.reload();
                console.log(error.message);
                Toast.fire({
                    icon:'error',
                    title:error.message || 'Terjadi kesalahan server'
                })
            }
        }
    </script>
@endpush
