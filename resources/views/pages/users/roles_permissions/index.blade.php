@extends('layouts.main')

@section('content')
  <style>
    .page-head{
      background: linear-gradient(180deg, rgba(13,110,253,.10), rgba(255,255,255,0));
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 1rem;
      padding: 1rem 1rem;
    }
    .soft-card{
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 1rem;
      box-shadow: 0 10px 24px rgba(16,24,40,.05);
    }
    .tab-pill .nav-link{
      border-radius: 999px;
      padding: .45rem .9rem;
      font-weight: 600;
      color: #334155;
    }
    .tab-pill .nav-link.active{
      background: rgba(13,110,253,.12);
      color: #0d6efd;
    }
    .chip{
      border:1px solid rgba(0,0,0,.10);
      border-radius:999px;
      padding:.28rem .65rem;
      font-size:.85rem;
      background:#fff;
      color:#111827;
      white-space:nowrap;
    }
    .perm-box{
      border:1px solid rgba(0,0,0,.08);
      border-radius: .85rem;
      padding: .75rem;
      background: rgba(0,0,0,.012);
      max-height: 52vh;
      overflow:auto;
    }
    .perm-item{
      border: 1px solid rgba(0,0,0,.06);
      border-radius: .75rem;
      padding: .55rem .65rem;
      background: #fff;
    }
    .perm-item:hover{ background: rgba(13,110,253,.04); }
    .btn-soft{
      border-radius: .9rem;
      font-weight: 600;
    }
    .dt-actions .btn{ border-radius: .65rem; }
  </style>

  {{-- Header --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-3">
    <div class="page-head flex-grow-1">
      <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
        <div>
          <h2 class="fw-bold mb-1">Roles & Permissions</h2>
          <p class="text-muted mb-0">Kelola role, permission, dan mapping-nya</p>
        </div>
        <div class="d-flex gap-2 flex-wrap">
          <span class="chip"><i class="bi bi-shield-lock me-1"></i>Access Control</span>
          <span class="chip"><i class="bi bi-gear me-1"></i>Master Setting</span>
        </div>
      </div>

      {{-- Tabs --}}
      <ul class="nav tab-pill mt-3" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabRoles" type="button" role="tab">
            <i class="bi bi-person-badge me-1"></i>Roles
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabPerms" type="button" role="tab">
            <i class="bi bi-key me-1"></i>Permissions
          </button>
        </li>
      </ul>
    </div>

    <div class="mt-3 mt-md-0 ms-md-3 d-flex gap-2 flex-wrap">
      <button class="btn btn-primary" id="btnAddRole">
        <i class="bi bi-plus-circle me-1"></i>Tambah Role
      </button>
      <button class="btn btn-outline-primary" id="btnAddPerm">
        <i class="bi bi-plus-circle me-1"></i>Tambah Permission
      </button>
    </div>
  </div>

  <div class="tab-content">
    {{-- ===================== TAB ROLES ===================== --}}
    <div class="tab-pane fade show active" id="tabRoles" role="tabpanel">
      <div class="card soft-card border-0">
        <div class="card-body">
          <div class="table-responsive">
            <table id="rolesTable" class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width:120px">Aksi</th>
                  <th>Role</th>
                  <th>Guard</th>
                  <th>Permissions</th>
                  <th>Dibuat</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- ===================== TAB PERMISSIONS ===================== --}}
    <div class="tab-pane fade" id="tabPerms" role="tabpanel">
      <div class="card soft-card border-0">
        <div class="card-body">
          <div class="table-responsive">
            <table id="permsTable" class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th style="width:120px">Aksi</th>
                  <th>Permission</th>
                  <th>Guard</th>
                  <th>Dibuat</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- ===================== MODAL ROLE (CREATE/EDIT) ===================== --}}
  <div class="modal fade" id="modalRole" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="formRole">
          @csrf
          <input type="hidden" name="id" id="role_id">
          <div class="modal-header">
            <h5 class="modal-title" id="roleTitle">Tambah Role</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nama Role</label>
              <input type="text" class="form-control" name="name" id="role_name" required>
              <div class="form-text">Contoh: <code>admin</code>, <code>official</code>, <code>club_manager</code></div>
            </div>
            <div class="mb-3">
              <label class="form-label">Guard</label>
              <input type="text" class="form-control" name="guard_name" id="role_guard" value="web">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- ===================== MODAL PERMISSION (CREATE/EDIT) ===================== --}}
  <div class="modal fade" id="modalPerm" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="formPerm">
          @csrf
          <input type="hidden" name="id" id="perm_id">
          <div class="modal-header">
            <h5 class="modal-title" id="permTitle">Tambah Permission</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nama Permission</label>
              <input type="text" class="form-control" name="name" id="perm_name" required>
              <div class="form-text">Contoh: <code>users.view</code>, <code>competitions.create</code>, <code>results.export</code></div>
            </div>
            <div class="mb-3">
              <label class="form-label">Guard</label>
              <input type="text" class="form-control" name="guard_name" id="perm_guard" value="web">
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
            <button type="submit" class="btn btn-primary">Simpan</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- ===================== MODAL ASSIGN PERMISSIONS TO ROLE ===================== --}}
  <div class="modal fade" id="modalAssign" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <form id="formAssign">
          @csrf
          <input type="hidden" id="assign_role_id" name="role_id">
          <div class="modal-header">
            <h5 class="modal-title">
              Atur Permission untuk Role: <span class="text-primary" id="assignRoleName">-</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="d-flex flex-column flex-md-row gap-2 align-items-md-center justify-content-between mb-2">
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary btn-soft" id="btnSelectAll">
                  <i class="bi bi-check2-square me-1"></i>Select All
                </button>
                <button type="button" class="btn btn-outline-secondary btn-soft" id="btnClearAll">
                  <i class="bi bi-x-square me-1"></i>Clear
                </button>
              </div>

              <div class="input-group" style="max-width: 320px;">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="permSearch" placeholder="Cari permission…">
              </div>
            </div>

            <div class="perm-box" id="permList">
              {{-- diisi via JS --}}
            </div>

            <div class="text-secondary small mt-2">
              Tip: gunakan penamaan konsisten, misal <code>users.view</code>, <code>users.create</code>, <code>users.update</code>, <code>users.delete</code>.
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i>Simpan Mapping
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  // ===================== Helpers =====================
  function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? "{{ csrf_token() }}";
  }

  function showSpinner(show){
    const el = document.getElementById('loadingSpinner');
    if(!el) return;
    el.classList.toggle('d-none', !show);
  }

  // ===================== Init Modals =====================
  const modalRole   = new bootstrap.Modal(document.getElementById('modalRole'));
  const modalPerm   = new bootstrap.Modal(document.getElementById('modalPerm'));
  const modalAssign = new bootstrap.Modal(document.getElementById('modalAssign'));

  document.getElementById('btnAddRole').addEventListener('click', () => {
    document.getElementById('roleTitle').textContent = 'Tambah Role';
    document.getElementById('role_id').value = '';
    document.getElementById('role_name').value = '';
    document.getElementById('role_guard').value = 'web';
    modalRole.show();
  });

  document.getElementById('btnAddPerm').addEventListener('click', () => {
    document.getElementById('permTitle').textContent = 'Tambah Permission';
    document.getElementById('perm_id').value = '';
    document.getElementById('perm_name').value = '';
    document.getElementById('perm_guard').value = 'web';
    modalPerm.show();
  });

  // ===================== DataTables Roles =====================
  const rolesTable = $('#rolesTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('roles.get') }}",
    columns: [
      {data:'action', name:'action', className:'text-center dt-actions', orderable:false, searchable:false},
      {data:'name', name:'name'},
      {data:'guard_name', name:'guard_name', className:'text-center'},
      {data:'permissions_count', name:'permissions_count', className:'text-center'},
      {data:'created_at', name:'created_at', className:'text-center'},
    ],
    order: [[1,'asc']],
  });

  // ===================== DataTables Permissions =====================
  const permsTable = $('#permsTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: "{{ route('permissions.get') }}",
    columns: [
      {data:'action', name:'action', className:'text-center dt-actions', orderable:false, searchable:false},
      {data:'name', name:'name'},
      {data:'guard_name', name:'guard_name', className:'text-center'},
      {data:'created_at', name:'created_at', className:'text-center'},
    ],
    order: [[1,'asc']],
  });

  // ===================== Submit Role =====================
  document.getElementById('formRole').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('role_id').value;
    const url = "{{ route('roles.store') }}";

    const fd = new FormData(e.target);

    showSpinner(true);
    try{
      const res = await fetch(url, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': csrf()},
        body: fd
      });
      const json = await res.json();
      if(!res.ok || json.status === false) throw new Error(json.message || 'Gagal simpan role');

      modalRole.hide();
      rolesTable.ajax.reload(null,false);
       Toast.fire({
          icon:'success',
          text:json.message || 'Berhasil simpan role'
      });
    }catch(err){
        Toast.fire({
           icon:'error',
           text:err.message || 'Gagal simpan role'
       });
    }finally{
      showSpinner(false);
    }
  });

  // ===================== Submit Permission =====================
  document.getElementById('formPerm').addEventListener('submit', async (e) => {
    e.preventDefault();
    const id = document.getElementById('perm_id').value;
    const url = "{{ route('permissions.store') }}";

    const fd = new FormData(e.target);

    showSpinner(true);
    try{
      const res = await fetch(url, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': csrf()},
        body: fd
      });
      const json = await res.json();
      if(!res.ok || json.status === false) throw new Error(json.message || 'Gagal simpan permission');

      modalPerm.hide();
      permsTable.ajax.reload(null,false);
      rolesTable.ajax.reload(null,false); // update count

      Toast.fire({
          icon:'success',
          text:json.message || 'Berhasil simpan permission'
      });
    }catch(err){
        Toast.fire({
            icon:'error',
            text:err.message || 'Gagal simpan permission'
        });
    }finally{
      showSpinner(false);
    }
  });

  // ===================== Assign Permissions Modal =====================
  // Kamu panggil ini dari tombol di kolom action role: openAssign(role_id)
  window.openAssign = async function(roleId){
    showSpinner(true);
    try{
      const res = await fetch("{{ url('/master/roles') }}/" + roleId + "/permissions", {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
      });
      if(!res.ok) throw new Error('Gagal ambil permissions');
      const data = await res.json();

      // data: { role: {...}, permissions: [...], assigned: [id...] }
      document.getElementById('assign_role_id').value = data.role.id;
      document.getElementById('assignRoleName').textContent = data.role.name;

      renderPermissions(data.permissions, new Set(data.assigned));
      modalAssign.show();
    }catch(err){
        Toast.fire({
            icon:'error',
            text:err.message || 'Proses gagal'
        });
    }finally{
      showSpinner(false);
    }
  }

  function renderPermissions(perms, assignedSet){
    const box = document.getElementById('permList');
    box.innerHTML = perms.map(p => {
      const checked = assignedSet.has(p.id) ? 'checked' : '';
      return `
        <label class="perm-item d-flex align-items-center justify-content-between gap-2 mb-2">
          <div class="d-flex align-items-center gap-2">
            <input class="form-check-input perm-check" type="checkbox" name="permissions[]" value="${p.id}" ${checked}>
            <div>
              <div class="fw-semibold">${escapeHtml(p.name)}</div>
              <div class="text-secondary small">guard: ${escapeHtml(p.guard_name ?? 'web')}</div>
            </div>
          </div>
          <span class="badge text-bg-light border">${p.id}</span>
        </label>
      `;
    }).join('');
  }

  function escapeHtml(s){
    return String(s ?? '').replace(/[&<>"']/g, (m) => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[m]));
  }

  // Search within permissions
  document.getElementById('permSearch').addEventListener('input', (e) => {
    const q = e.target.value.toLowerCase().trim();
    document.querySelectorAll('#permList .perm-item').forEach(item => {
      const text = item.innerText.toLowerCase();
      item.style.display = text.includes(q) ? '' : 'none';
    });
  });

  // Select/Clear all
  document.getElementById('btnSelectAll').addEventListener('click', () => {
    document.querySelectorAll('#permList .perm-check').forEach(ch => ch.checked = true);
  });
  document.getElementById('btnClearAll').addEventListener('click', () => {
    document.querySelectorAll('#permList .perm-check').forEach(ch => ch.checked = false);
  });

  // Submit assign
  document.getElementById('formAssign').addEventListener('submit', async (e) => {
    e.preventDefault();
    const roleId = document.getElementById('assign_role_id').value;
    const url = "{{ url('/master/roles') }}/" + roleId + "/permissions/sync";

    const fd = new FormData(e.target);

    showSpinner(true);
    try{
      const res = await fetch(url, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': csrf()},
        body: fd
      });
      const json = await res.json();
      if(!res.ok || json.status === false) throw new Error(json.message || 'Gagal sync permissions');

      modalAssign.hide();
      rolesTable.ajax.reload(null,false);

      Toast.fire({
        icon:'success',
        text:json.message || 'Berhasil simpan data'
      });
    }catch(err){
      Toast.fire({
        icon:'error',
        text:err.message || 'Gagal Simpan Data'
      });
    }finally{
      showSpinner(false);
    }
  });

  async function deletePermission(permission_id){
    try {
        showSpinner(true);
        const res = await fetch("{{ url('/master/permissions') }}/" + permission_id, {
            method: 'DELETE',
            headers: {'X-CSRF-TOKEN': csrf()},
        });
        if(!res.ok) throw new Error('Gagal hapus permissions');
        const data = await res.json();

        permsTable.ajax.reload(null,false);
        Toast.fire({
            icon:'success',
            text:data.message || 'Berhasil hapus permission'
        });
    } catch (err) {
        Toast.fire({
            icon:'error',
            text:err.message || 'Gagal hapus permission'
        });
    } finally {
        showSpinner(false);
    }
  }


    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(btn => {
        btn.addEventListener('shown.bs.tab', () => {
            $($.fn.dataTable.tables(true)).DataTable().columns.adjust().responsive?.recalc?.();
        });
    });
</script>
@endpush
