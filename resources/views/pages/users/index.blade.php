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
    <div class="page-head flex-grow-1">
        <div class="d-flex align-items-start justify-content-between gap-2 flex-wrap">
            <div>
                <h2 class="fw-bold mb-1">Manajemen User</h2>
                <p class="text-muted mb-0">Kelola akun pengguna, roles, dan permissions</p>
            </div>
        </div>
        <ul class="nav tab-pill mt-3" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tabUsers" type="button" role="tab">
                    <i class="bi bi-person-badge me-1"></i>Users
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tabRoles" type="button" role="tab">
                    <i class="bi bi-person-badge me-1"></i>Roles
                </button>
            </li>
        </ul>
    </div>
  </div>

  <div class="tab-content">
    <div class="tab-pane fade show active" id="tabUsers">
        {{-- Card Table --}}
        <div class="card soft-card border-0">
            <div class="card-header text-end">
                <button type="button" id="btnAddUser" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-1"></i> Tambah User
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="usersTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Aksi</th>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Organisasi</th>
                                <th>Klub</th>
                                <th>Roles</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="tab-pane fade" id="tabRoles">
        <div class="card soft-card border-0">
            <div class="card-header text-end">
                <button class="btn btn-primary" id="btnAddRole">
                    <i class="bi bi-plus-circle me-1"></i>Tambah Role
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="rolesTable" class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                            <th style="width:120px">Aksi</th>
                            <th>Role</th>
                            <th>Permissions</th>
                            <th>Dibuat</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
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
              <label class="form-label">Nama Lengkap *</label>
              <input type="text" class="form-control" name="name" id="user_name" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Email *</label>
              <input type="email" class="form-control" name="email" id="user_email" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Organisasi</label>
              <select name="organization_id" id="organization_id" class="form-control">
                <option value="" selected>Tidak Dalam Organisasi</option>
                @foreach ($organizations as $name => $id)
                    <option value="{{ $id }}" @selected(old('organization_id') == $id)>{{ $name ?? '-' }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Klub</label>
              <select name="club_id" id="club_id" class="form-control">
                <option value="" selected>Tidak Dalam Klub</option>
                @foreach ($clubs as $name => $id)
                    <option value="{{ $id }}" @selected(old('club_id') == $id)>{{ $name ?? '-' }}</option>
                @endforeach
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Password <span class="text-muted small" id="user_pw_note">*</span></label>
              <input type="password" class="form-control" name="password" id="user_password">
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

  {{-- modal create role --}}
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
  {{-- modal assign permission to role --}}
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

              {{-- <div class="input-group" style="max-width: 320px;">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" id="permSearch" placeholder="Cari permission…">
              </div> --}}
            </div>

            <div class="perm-box" id="permList">
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
    const modalRole   = new bootstrap.Modal(document.getElementById('modalRole'));
    const modalAssign = new bootstrap.Modal(document.getElementById('modalAssign'));

    // ===================== Users =====================
    const usersTable = $('#usersTable').DataTable({
        processing:true,
        serverSide:true,
        ajax:"{{ route('users.get') }}",
        columns:[
            {data:'action',name:'action', className:'text-center dt-actions', orderable:false, searchable:false},
            {data:'name',name:'name'},
            {data:'email',name:'email'},
            {data:'organization.name',name:'organization.name'},
            {data:'club.club_name',name:'club.club_name', 'defaultContent':'-'},
            {data:'roles_badges',name:'roles_badges', orderable:false, searchable:false},
            {data:'status_badge',name:'status_badge', searchable:false},
        ],
        order:[[1,'asc']],
    });
    $(document).on('click', '#btnAddUser', function(e){
        e.preventDefault();
        e.stopPropagation();
        document.getElementById('userTitle').textContent = 'Tambah User';
        document.getElementById('user_id').value = '';
        document.getElementById('user_name').value = '';
        document.getElementById('user_email').value = '';
        $('#organization_id').val('').trigger('change');
        $('#club_id').val('').trigger('change');
        document.getElementById('user_password').value = '';
        $('#user_pw_note').html('*');
        modalUser.show();
    });
    $(document).on('click', '#btnUserEdit', function(e){
        e.preventDefault();
        e.stopPropagation();
        showSpinner(true);

        const rowData = usersTable.row($(this).closest('tr')).data();

        document.getElementById('userTitle').textContent = 'Edit User';
        document.getElementById('user_id').value = rowData.id;
        document.getElementById('user_name').value = rowData.name;
        document.getElementById('user_email').value = rowData.email;
        $('#organization_id').val(rowData.organization?.id).trigger('change');
        $('#club_id').val(rowData.club?.id).trigger('change');
        document.getElementById('user_password').value = '';
        $('#user_pw_note').html('(kosongkan jika tidak diubah)');
        modalUser.show();

        showSpinner(false);
    });
    $('#organization_id').select2({
        width:"100%",
        dropdownParent: $('#modalUser')
    });
    $('#club_id').select2({
        width:"100%",
        dropdownParent: $('#modalUser')
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
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
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
    // delete User
    async function deleteUser(userId) {
        const confirm = await Swal.fire({
            title: 'Hapus Akun?',
            text: 'Akun pengguna akan dinonaktifkan.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal',
            reverseButtons:true
        });

        if (!confirm.isConfirmed) return;

        showSpinner(true);
        try {
            const url = "{{ route('users.destroy', 'id') }}".replace('id', userId);
            const  res = await fetch(url, {
                method:'DELETE',
                headers:{
                    'X-CSRF-TOKEN' : "{{ csrf_token() }}"
                }
            });

            if(!res.ok) throw new Error('Terjadi kesalahan sistem, hubungi admin');
            const data = await res.json();
            if(data.status){
                Toast.fire({
                    icon:'success',
                    title:data.message ||'berhasil menghapus atau menonaktifkan akun'
                });
            }else{
                Toast.fire({
                    icon:'error',
                    title:data.message ||'Gagal menghapus atau menonaktifkan akun'
                });
            }
            usersTable.ajax.reload();
        } catch (error) {
            console.log(error.message);
            Toast.fire({
                icon:'error',
                title:error?.message ||'Proses gagal, hubungi admin aplikasi'
            });
        } finally {
            showSpinner(false);
        }
    }
    // restore User
    async function restoreUser(userId) {
        const confirm = await Swal.fire({
            title: 'Aktifkan Akun?',
            text: 'Akun pengguna akan di aktifkan kembali.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Ya, Lanjutkan!',
            cancelButtonText: 'Batal',
            reverseButtons:true
        });

        if (!confirm.isConfirmed) return;

        showSpinner(true);
        try {
            const url = "{{ route('users.restore', 'id') }}".replace('id', userId);
            const  res = await fetch(url, {
                method:'POST',
                headers:{
                    'X-CSRF-TOKEN' : "{{ csrf_token() }}"
                }
            });

            if(!res.ok) throw new Error('Terjadi kesalahan sistem, hubungi admin');
            const data = await res.json();
            if(data.status){
                Toast.fire({
                    icon:'success',
                    title:data.message ||'Akun berhasil diaktifkan'
                });
            }else{
                Toast.fire({
                    icon:'error',
                    title:data.message ||'Gagal mengaktifkan akun'
                });
            }
            usersTable.ajax.reload();
        } catch (error) {
            console.log(error.message);
            Toast.fire({
                icon:'error',
                title:error?.message ||'Proses gagal, hubungi admin aplikasi'
            });
        } finally {
            showSpinner(false);
        }
    }
    // Assign Roles to User
    async function openUserRoles(userId){
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
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
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

    // ===================== Roles =====================
    document.getElementById('btnAddRole').addEventListener('click', () => {
        document.getElementById('roleTitle').textContent = 'Tambah Role';
        document.getElementById('role_id').value = '';
        document.getElementById('role_name').value = '';
        modalRole.show();
    });
    const rolesTable = $('#rolesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('roles.get') }}",
        columns: [
            {data:'action', name:'action', className:'text-center dt-actions', orderable:false, searchable:false},
            {data:'name', name:'name'},
            {data:'permissions_count', name:'permissions_count', className:'text-center', searchable:false},
            {data:'created_at', name:'created_at', className:'text-center', searchable:false},
        ],
        order: [[1,'asc']],
    });

    document.getElementById('formRole').addEventListener('submit', async (e) => {
        e.preventDefault();
        const id = document.getElementById('role_id').value;
        const url = "{{ route('roles.store') }}";

        const fd = new FormData(e.target);

        showSpinner(true);
        try{
        const res = await fetch(url, {
            method: 'POST',
            headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
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
        // return `
        //     <label class="d-flex align-items-center justify-content-between gap-2 mb-2">
        //     <div class="d-flex align-items-center gap-2">
        //         <input class="form-check-input perm-check" type="checkbox" name="permissions[]" value="${p.id}" ${checked}>
        //         <div>
        //         <div class="fw-semibold">${escapeHtml(p.name)}</div>
        //         <div class="text-secondary small">guard: ${escapeHtml(p.guard_name ?? 'web')}</div>
        //         </div>
        //     </div>
        //     <span class="badge text-bg-light border">${p.id}</span>
        //     </label>
        // `;
        return `
            <label class="d-flex align-items-center mb-2">
            <div class="d-flex align-items-center gap-2">
                <input class="form-check-input perm-check" type="checkbox" name="permissions[]" value="${p.id}" ${checked}>
                <div class="fw-semibold">${escapeHtml(p.name)}</div>
            </div>
            </label>
        `;
        }).join('');
    }
    document.getElementById('btnSelectAll').addEventListener('click', () => {
        document.querySelectorAll('#permList .perm-check').forEach(ch => ch.checked = true);
    });
    document.getElementById('btnClearAll').addEventListener('click', () => {
        document.querySelectorAll('#permList .perm-check').forEach(ch => ch.checked = false);
    });
    document.getElementById('formAssign').addEventListener('submit', async (e) => {
        e.preventDefault();
        const roleId = document.getElementById('assign_role_id').value;
        const url = "{{ url('/master/roles') }}/" + roleId + "/permissions/sync";

        const fd = new FormData(e.target);

        showSpinner(true);
        try{
            const res = await fetch(url, {
                method: 'POST',
                headers: {'X-CSRF-TOKEN': "{{ csrf_token() }}"},
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
</script>
@endpush
