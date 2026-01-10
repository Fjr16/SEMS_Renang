<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

// Spatie
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserController extends Controller
{
    public function index()
    {
        return view('pages.users.index');
    }

    public function getDataTables()
    {
        $query = User::query()
            ->with('roles')
            ->orderBy('name', 'asc');

        return DataTables::of($query)
            ->addColumn('roles_badges', function (User $row) {
                if ($row->roles->count() === 0) {
                    return '<span class="text-muted small">-</span>';
                }

                return $row->roles->map(function ($r) {
                    return '<span class="role-pill"><i class="bi bi-person-badge"></i>' . e($r->name) . '</span>';
                })->implode(' ');
            })
            ->addColumn('status_badge', function (User $row) {
                $status = $row->status ?? true;

                if (!$status) {
                    return '<span class="badge bg-secondary">Nonaktif</span>';
                }
                return '<span class="badge bg-success">Aktif</span>';
            })
            ->addColumn('action', function (User $row) {
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary" title="Edit" id="btnUserEdit">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-outline-secondary" title="Roles" onclick="openUserRoles(' . $row->id . ')">
                            <i class="bi bi-person-badge"></i>
                        </button>
                        <button class="btn btn-outline-success" title="Direct Permissions" onclick="openUserPerms(' . $row->id . ')">
                            <i class="bi bi-key"></i>
                        </button>
                    </div>
                ';
            })
            ->rawColumns(['action', 'roles_badges', 'status_badge'])
            ->make(true);
    }

    /**
     * Create / Update user (modal)
     * POST users.store / users.update (pakai 1 endpoint juga boleh)
     */
    public function store(Request $req)
    {
        $userId = $req->input('id');

        $validators = Validator::make($req->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($userId ?: 'NULL') . ',id',
            'password' => $userId ? 'nullable|min:6' : 'required|min:6',
            'status' => 'required|in:active,inactive',
            'club_id' => 'nullable',
        ]);

        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(), 0, 150),
            ], 422);
        }

        try {
            $user = $userId ? User::findOrFail($userId) : new User();

            $user->name = $req->name;
            $user->email = $req->email;
            $stts = $req->status === 'active' ? true : false;

            // password: hash hanya jika diisi / create
            if (!$userId || $req->filled('password')) {
                $user->password = Hash::make($req->password);
            }

            // sesuaikan field kamu
            if ($req->has('club_id')) $user->club_id = $req->club_id;
            $user->status = $stts;
            if (!$stts) {
                $user->remember_token = null;
            }

            $user->save();

            return response()->json([
                'status' => true,
                'message' => $userId ? 'Berhasil memperbarui user' : 'Berhasil menambahkan user',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $item = User::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Sukses hapus data',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150),
            ], 500);
        }
    }

    public function profile($id){
        try {
            $item = User::findOrFail(decrypt($id));

            return view('pages.users.show', [
                'item' => $item
            ]);
        } catch (\Throwable $th) {
            return back()->with('error', 'Pengguna tidak ditemukan');
        }
    }

    // ==========================
    // Spatie: Roles mapping user
    // ==========================

    /**
     * GET /users/{id}/roles
     * return: roles list + assigned role ids
     */
    public function userRoles($id)
    {
        $user = User::findOrFail($id);

        $roles = Role::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name', 'guard_name']);

        $assigned = $user->roles->pluck('id')->values();

        return response()->json([
            'user' => ['id' => $user->id, 'name' => $user->name],
            'roles' => $roles,
            'assigned' => $assigned,
        ]);
    }

    /**
     * POST /users/{id}/roles/sync
     * body: roles[] (ids)
     */
    public function syncUserRoles(Request $req, $id)
    {
        $user = User::findOrFail($id);

        $roleIds = $req->input('roles', []);
        $roles = Role::whereIn('id', $roleIds)->get();

        $user->syncRoles($roles);

        return response()->json([
            'status' => true,
            'message' => 'Roles berhasil disimpan',
        ]);
    }

    // ==================================
    // Spatie: Direct permissions (optional)
    // ==================================

    /**
     * GET /users/{id}/permissions
     * return: permissions list + assigned permission ids
     */
    public function userPermissions($id)
    {
        $user = User::findOrFail($id);

        $permissions = Permission::query()
            ->where('guard_name', 'web')
            ->orderBy('name')
            ->get(['id', 'name', 'guard_name']);

        $assigned = $user->permissions->pluck('id')->values();

        return response()->json([
            'user' => ['id' => $user->id, 'name' => $user->name],
            'permissions' => $permissions,
            'assigned' => $assigned,
        ]);
    }

    /**
     * POST /users/{id}/permissions/sync
     * body: permissions[] (ids)
     */
    public function syncUserPermissions(Request $req, $id)
    {
        $user = User::findOrFail($id);

        $permIds = $req->input('permissions', []);
        $perms = Permission::whereIn('id', $permIds)->get();

        $user->syncPermissions($perms);

        return response()->json([
            'status' => true,
            'message' => 'Permissions berhasil disimpan',
        ]);
    }
}
