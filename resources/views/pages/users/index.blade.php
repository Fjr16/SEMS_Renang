@extends('layouts.main')

@section('content')
  <style>
    .soft-card{
      border: 1px solid rgba(0,0,0,.06);
      border-radius: 1rem;
      box-shadow: 0 10px 24px rgba(16,24,40,.05);
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
    .role-pill, .perm-pill{
      display:inline-flex;
      align-items:center;
      gap:.35rem;
      border-radius:999px;
      padding:.22rem .55rem;
      font-size:.82rem;
      border:1px solid rgba(0,0,0,.08);
      background: rgba(13,110,253,.06);
      color:#0d6efd;
      margin: 0 .25rem .25rem 0;
      white-space: nowrap;
    }
    .perm-pill{
      background: rgba(25,135,84,.08);
      color:#198754;
    }
    .box-scroll{
      max-height: 52vh;
      overflow:auto;
      border:1px solid rgba(0,0,0,.08);
      border-radius: .9rem;
      padding: .75rem;
      background: rgba(0,0,0,.012);
    }
    .item-check{
      border: 1px solid rgba(0,0,0,.06);
      border-radius: .75rem;
      padding: .55rem .65rem;
      background: #fff;
    }
    .item-check:hover{ background: rgba(13,110,253,.04); }
    .dt-actions .btn{ border-radius: .65rem; }
  </style>

  {{-- Header Page --}}
  <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4">
    <div>
      <h2 class="fw-bold mb-1">Manajemen User</h2>
      <p class="text-muted mb-0">Kelola akun pengguna, roles, dan permissions</p>
      <div class="mt-2 d-flex gap-2 flex-wrap">
        <span class="chip"><i class="bi bi-person-gear me-1"></i>User</span>
        <span class="chip"><i class="bi bi-shield-lock me-1"></i>Spatie Permission</span>
      </div>
    </div>
    <div class="mt-3 mt-md-0">
      <button data-bs-toggle="modal" onclick="openUserCreate()" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Tambah User
      </button>
    </div>
  </div>

  {{-- Card Table --}}
  <div class="card soft-card border-0">
    <div class="card-body">
      <div class="table-responsive">
        <table id="usersTable" class="table table-hover align-middle mb-0">
          <thead class="table-light">
            <tr>
              <th>Aksi</th>
              <th>Nama</th>
              <th>Email</th>
              <th>Roles</th>
              <th>Status</th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>

  {{-- ===================== MODAL CREATE/EDIT USER ===================== --}}
  <div class="modal fade" id="modalUser" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <form id="formUser">
          @csrf
          <input type="hidden" name="id" id="user_id">
          <div class="modal-header">
            <h5 class="modal-title" id="userTitle">Tambah User</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Nama Lengkap</label>
              <input type="text" class="form-control" name="name" id="user_name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" id="user_email" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Password <span class="text-muted small">(kosongkan jika tidak diubah)</span></label>
              <input type="password" class="form-control" name="password" id="user_password">
            </div>
            <div class="mb-3">
              <label class="form-label">Status</label>
              <select class="form-select" name="status" id="user_status" required>
                <option value="active" selected>Aktif</option>
                <option value="inactive">Nonaktif</option>
              </select>
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

  {{-- ===================== MODAL ASSIGN ROLES ===================== --}}
  <div class="modal fade" id="modalUserRoles" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <form id="formUserRoles">
          @csrf
          <input type="hidden" id="ur_user_id" name="user_id">
          <div class="modal-header">
            <h5 class="modal-title">
              Atur Roles untuk: <span class="text-primary" id="ur_user_name">-</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="d-flex flex-column flex-md-row gap-2 align-items-md-center justify-content-between mb-2">
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" id="urSelectAll">
                  <i class="bi bi-check2-square me-1"></i>Select All
                </button>
                <button type="button" class="btn btn-outline-secondary" id="urClearAll">
                  <i class="bi bi-x-square me-1"></i>Clear
                </button>
              </div>
              <div class="input-group" style="max-width: 320px;">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="urSearch" placeholder="Cari role…">
              </div>
            </div>

            <div class="box-scroll" id="urList">
              {{-- diisi via JS --}}
            </div>
            <div class="text-secondary small mt-2">
              Tips: untuk user biasa, assign role saja sudah cukup. Direct permission hanya untuk override.
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i>Simpan Roles
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  {{-- ===================== MODAL ASSIGN DIRECT PERMISSIONS (optional) ===================== --}}
  <div class="modal fade" id="modalUserPerms" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content">
        <form id="formUserPerms">
          @csrf
          <input type="hidden" id="up_user_id" name="user_id">
          <div class="modal-header">
            <h5 class="modal-title">
              Direct Permissions untuk: <span class="text-primary" id="up_user_name">-</span>
            </h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">
            <div class="alert alert-light border">
              <div class="d-flex gap-2">
                <i class="bi bi-info-circle"></i>
                <div class="small text-secondary">
                  Direct permission hanya untuk kasus khusus (override). Umumnya cukup role.
                </div>
              </div>
            </div>

            <div class="d-flex flex-column flex-md-row gap-2 align-items-md-center justify-content-between mb-2">
              <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-secondary" id="upSelectAll">
                  <i class="bi bi-check2-square me-1"></i>Select All
                </button>
                <button type="button" class="btn btn-outline-secondary" id="upClearAll">
                  <i class="bi bi-x-square me-1"></i>Clear
                </button>
              </div>
              <div class="input-group" style="max-width: 320px;">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="upSearch" placeholder="Cari permission…">
              </div>
            </div>

            <div class="box-scroll" id="upList">
              {{-- diisi via JS --}}
            </div>
          </div>

          <div class="modal-footer">
            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Tutup</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i>Simpan Permissions
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@push('scripts')
<script>
  function csrf() {
    return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') ?? "{{ csrf_token() }}";
  }
  function showSpinner(show){
    const el = document.getElementById('loadingSpinner');
    if(!el) return;
    el.classList.toggle('d-none', !show);
  }
  function escapeHtml(s){
    return String(s ?? '').replace(/[&<>"']/g, (m) => ({
      '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'
    }[m]));
  }

  const modalUser      = new bootstrap.Modal(document.getElementById('modalUser'));
  const modalUserRoles = new bootstrap.Modal(document.getElementById('modalUserRoles'));
  const modalUserPerms = new bootstrap.Modal(document.getElementById('modalUserPerms'));

  // ===================== DataTable Users =====================
  const usersTable = $('#usersTable').DataTable({
    processing:true,
    serverSide:true,
    ajax:"{{ route('users.get') }}",
    columns:[
      {data:'action',name:'action', className:'text-center dt-actions', orderable:false, searchable:false},
      {data:'name',name:'name'},
      {data:'email',name:'email'},
      {data:'roles_badges',name:'roles_badges', orderable:false, searchable:false},
      {data:'status_badge',name:'status_badge', orderable:false, searchable:false},
    ],
    order:[[1,'asc']],
  });

  window.openUserCreate = function(){
    document.getElementById('userTitle').textContent = 'Tambah User';
    document.getElementById('user_id').value = '';
    document.getElementById('user_name').value = '';
    document.getElementById('user_email').value = '';
    document.getElementById('user_password').value = '';
    document.getElementById('user_status').value = 'active';
    modalUser.show();
  }

  $(document).on('click', '#btnUserEdit', function(e){
    e.preventDefault();
    e.stopPropagation();
    showSpinner(true);

    const rowData = usersTable.row($(this).closest('tr')).data();

    document.getElementById('userTitle').textContent = 'Edit User';
    document.getElementById('user_id').value = rowData.id;
    document.getElementById('user_name').value = rowData.name;
    document.getElementById('user_email').value = rowData.email;
    document.getElementById('user_password').value = '';
    document.getElementById('user_status').value = rowData.status ? 'active' : 'inactive';
    modalUser.show();

    showSpinner(false);
  });

  // Submit Create/Edit User
  document.getElementById('formUser').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const id = document.getElementById('user_id').value;
    const url = "{{ route('users.store') }}";
    const fd = new FormData(e.target);

    showSpinner(true);
    try{
        const res = await fetch(url, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': csrf()},
            body: fd
        });
        const json = await res.json();
        if(!res.ok || json.status === false) throw new Error(json.message || 'Gagal simpan user');

        modalUser.hide();

        usersTable.ajax.reload(null,false);

        Toast.fire({
            icon:'success',
            title:json.message ||'Sukses Simpan Data'
        });
    }catch(err){
        Toast.fire({
            icon:'error',
            title:err.message ||'Gagal mendapatkan data'
        });
    }finally{
      showSpinner(false);
    }
  });

  // ===================== Assign Roles to User =====================
  window.openUserRoles = async function(userId){
    showSpinner(true);
    try{
      const res = await fetch("{{ url('/master/users') }}/" + userId + "/roles", {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
      });
      if(!res.ok) throw new Error('Gagal ambil roles user');
      const data = await res.json();
      // expected: { user:{id,name}, roles:[{id,name}], assigned:[id...] }

      document.getElementById('ur_user_id').value = data.user.id;
      document.getElementById('ur_user_name').textContent = data.user.name;

      const assigned = new Set(data.assigned || []);
      document.getElementById('urList').innerHTML = (data.roles || []).map(r => {
        const checked = assigned.has(r.id) ? 'checked' : '';
        return `
          <label class="item-check d-flex align-items-center justify-content-between gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
              <input class="form-check-input ur-check" type="checkbox" name="roles[]" value="${r.id}" ${checked}>
              <div class="fw-semibold">${escapeHtml(r.name)}</div>
            </div>
          </label>
        `;
      }).join('');

      modalUserRoles.show();
    }catch(err){
      alert(err.message);
    }finally{
      showSpinner(false);
    }
  }

  document.getElementById('urSearch').addEventListener('input', (e)=>{
    const q = e.target.value.toLowerCase().trim();
    document.querySelectorAll('#urList .item-check').forEach(item=>{
        if (item.innerText.toLowerCase().includes(q)) {
            $(item).removeClass('d-none');
        }else{
            $(item).addClass('d-none');
        }
    });
  });

  document.getElementById('urSelectAll').addEventListener('click', ()=>{
    document.querySelectorAll('#urList .ur-check').forEach(ch => ch.checked = true);
  });
  document.getElementById('urClearAll').addEventListener('click', ()=>{
    document.querySelectorAll('#urList .ur-check').forEach(ch => ch.checked = false);
  });

  document.getElementById('formUserRoles').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const userId = document.getElementById('ur_user_id').value;
    const url = "{{ url('/master/users') }}/" + userId + "/roles/sync";
    const fd = new FormData(e.target);

    showSpinner(true);
    try{
      const res = await fetch(url, {
        method: 'POST',
        headers: {'X-CSRF-TOKEN': csrf()},
        body: fd
      });
      const json = await res.json();
      if(!res.ok || json.status === false) throw new Error(json.message || 'Gagal sync roles');

      modalUserRoles.hide();
      usersTable.ajax.reload(null,false);
      Toast.fire({
          icon:'success',
          title:json.message ||'Berhasil simpan data'
      });
    }catch(err){
        Toast.fire({
          icon:'error',
          title:err.message ||'Berhasil simpan data'
      });
    }finally{
      showSpinner(false);
    }
  });

  // ===================== Assign Direct Permissions (optional) =====================
  window.openUserPerms = async function(userId){
    showSpinner(true);
    try{
      const res = await fetch("{{ url('/master/users') }}/" + userId + "/permissions", {
        headers: {'X-Requested-With': 'XMLHttpRequest'}
      });
      if(!res.ok) throw new Error('Gagal ambil permissions user');
      const data = await res.json();
      // expected: { user:{id,name}, permissions:[{id,name,guard_name}], assigned:[id...] }

      document.getElementById('up_user_id').value = data.user.id;
      document.getElementById('up_user_name').textContent = data.user.name;

      const assigned = new Set(data.assigned || []);
      document.getElementById('upList').innerHTML = (data.permissions || []).map(p => {
        const checked = assigned.has(p.id) ? 'checked' : '';
        return `
          <label class="item-check d-flex align-items-center justify-content-between gap-2 mb-2">
            <div class="d-flex align-items-center gap-2">
              <input class="form-check-input up-check" type="checkbox" name="permissions[]" value="${p.id}" ${checked}>
              <div>
                <div class="fw-semibold">${escapeHtml(p.name)}</div>
                <div class="text-secondary small">guard: ${escapeHtml(p.guard_name ?? 'web')}</div>
              </div>
            </div>
          </label>
        `;
      }).join('');

      modalUserPerms.show();
    }catch(err){
      alert(err.message);
    }finally{
      showSpinner(false);
    }
  }

  document.getElementById('upSearch').addEventListener('input', (e)=>{
    const q = e.target.value.toLowerCase().trim();
    document.querySelectorAll('#upList .item-check').forEach(item=>{
        if (item.innerText.toLowerCase().includes(q)) {
            $(item).removeClass('d-none');
        }else{
            $(item).addClass('d-none');
        }
    });
  });
  document.getElementById('upSelectAll').addEventListener('click', ()=>{
    document.querySelectorAll('#upList .up-check').forEach(ch => ch.checked = true);
  });
  document.getElementById('upClearAll').addEventListener('click', ()=>{
    document.querySelectorAll('#upList .up-check').forEach(ch => ch.checked = false);
  });

  document.getElementById('formUserPerms').addEventListener('submit', async (e)=>{
    e.preventDefault();
    const userId = document.getElementById('up_user_id').value;
    const url = "{{ url('/master/users') }}/" + userId + "/permissions/sync";
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

      modalUserPerms.hide();
      usersTable.ajax.reload(null,false);
      Toast.fire({
          icon:'success',
          title:json.message ||'Berhasil simpan data'
      });
    }catch(err){
        Toast.fire({
            icon:'error',
            title:err.message ||'Gagal mendapatkan data'
        });
    }finally{
      showSpinner(false);
    }
  });
</script>
@endpush
