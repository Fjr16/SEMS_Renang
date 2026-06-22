<div class="undangan-tab-content">
    <style>
        .undangan-drop-zone {
            border: 2px dashed #ced4da;
            border-radius: 10px;
            padding: 40px 16px;
            text-align: center;
            cursor: pointer;
            transition: all .2s;
            background: #f8f9fa;
        }
        .undangan-drop-zone:hover,
        .undangan-drop-zone.dragover {
            border-color: #86b7fe;
            background: #f0f6ff;
        }
        .undangan-drop-zone.dragover {
            border-style: solid;
        }
        .undangan-pdf-viewer {
            border: 1px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            background: #f8f9fa;
        }
        .undangan-pdf-viewer iframe {
            width: 100%;
            height: 65vh;
            border: none;
            display: block;
        }
        .undangan-pdf-toolbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 16px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
        }
    </style>

    @if($competition->undangan_path)
        {{-- Sudah ada file → tampilkan preview --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="fw-bold mb-0">Undangan Kompetisi</h6>
                <small class="text-muted">Preview dokumen undangan yang telah diunggah</small>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ Storage::url($competition->undangan_path) }}"
                   class="btn btn-sm btn-outline-secondary" download target="_blank">
                    <i class="bi bi-download me-1"></i> Download
                </a>
                <button class="btn btn-sm btn-primary" onclick="openUndanganReplace()">
                    <i class="bi bi-arrow-repeat me-1"></i> Ganti File
                </button>
            </div>
        </div>

        <div class="undangan-pdf-viewer">
            <iframe src="{{ Storage::url($competition->undangan_path) }}#toolbar=1&navpanes=0&scrollbar=1"></iframe>
        </div>

        {{-- Form ganti file (hidden) --}}
        <div id="undanganReplaceSection" class="d-none mt-3">
            <div class="card border">
                <div class="card-body">
                    <h6 class="fw-bold mb-3">Ganti File Undangan</h6>
                    <form id="undanganForm" enctype="multipart/form-data">
                        <div id="undanganDropZone" class="undangan-drop-zone"
                             onclick="document.getElementById('undanganFileInput').click()">
                            <div id="dropZonePlaceholder">
                                <i class="bi bi-cloud-arrow-up text-muted" style="font-size:2rem;"></i>
                                <p class="mb-1 fw-semibold text-muted mt-2">Klik atau drag & drop file PDF baru di sini</p>
                                <p class="mb-0 text-muted" style="font-size:12px">Format: PDF, maks 5MB</p>
                            </div>
                            <div id="dropZoneFileInfo" class="d-none">
                                <div class="d-flex align-items-center gap-3 p-2 rounded-3 mx-auto"
                                     style="background:#f1f5f9; border:1px solid #e2e8f0; max-width:400px">
                                    <i class="bi bi-file-earmark-pdf text-danger" style="font-size:1.5rem"></i>
                                    <div class="flex-grow-1 min-w-0 text-start">
                                        <p class="fw-semibold mb-0 text-truncate" id="undanganFileName"></p>
                                        <p class="text-muted mb-0 small" id="undanganFileSize"></p>
                                    </div>
                                    <button type="button" class="btn btn-sm btn-light" onclick="resetUndanganFile(event)">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <input type="file" id="undanganFileInput" name="file" accept=".pdf" class="d-none"
                               onchange="handleUndanganFileSelect(event)">
                        <div class="d-flex justify-content-end gap-2 mt-3">
                            <button type="button" class="btn btn-sm btn-light" onclick="cancelUndanganReplace()">Batal</button>
                            <button type="button" class="btn btn-sm btn-primary" id="undanganSubmitBtn" onclick="submitUndangan()">
                                <i class="bi bi-cloud-arrow-up me-1"></i> Upload & Ganti
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @else
        {{-- Belum ada file → tampilkan upload --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h6 class="fw-bold mb-0">Undangan Kompetisi</h6>
                <small class="text-muted">Unggah dokumen undangan kompetisi (PDF)</small>
            </div>
        </div>

        <div class="text-center py-4">
            <form id="undanganForm" enctype="multipart/form-data">
                <div id="undanganDropZone" class="undangan-drop-zone"
                     onclick="document.getElementById('undanganFileInput').click()">
                    <div id="dropZonePlaceholder">
                        <i class="bi bi-file-earmark-pdf text-muted" style="font-size:3rem;"></i>
                        <p class="fw-semibold text-muted mt-2 mb-1">Klik atau drag & drop file PDF di sini</p>
                        <p class="mb-0 text-muted" style="font-size:12px">Format: PDF, maks 5MB</p>
                    </div>
                    <div id="dropZoneFileInfo" class="d-none">
                        <div class="d-flex align-items-center gap-3 p-3 rounded-3 mx-auto"
                             style="background:#f1f5f9; border:1px solid #e2e8f0; max-width:420px">
                            <i class="bi bi-file-earmark-pdf text-danger" style="font-size:2rem"></i>
                            <div class="flex-grow-1 min-w-0 text-start">
                                <p class="fw-semibold mb-0 text-truncate" id="undanganFileName"></p>
                                <p class="text-muted mb-0 small" id="undanganFileSize"></p>
                            </div>
                            <button type="button" class="btn btn-sm btn-light" onclick="resetUndanganFile(event)">
                                <i class="bi bi-x"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <input type="file" id="undanganFileInput" name="file" accept=".pdf" class="d-none"
                       onchange="handleUndanganFileSelect(event)">

                <div class="d-flex justify-content-center mt-3">
                    <button type="button" class="btn btn-primary" id="undanganSubmitBtn" onclick="submitUndangan()">
                        <i class="bi bi-cloud-arrow-up me-1"></i> Upload Undangan
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
