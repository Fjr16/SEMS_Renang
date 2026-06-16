<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Organization;
use App\Models\User;
use App\Traits\HasApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

// Spatie
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use HasApiResponse;

    public function index(){
        $organizations = Organization::pluck('id', 'name');
        $clubs = Club::pluck('id', 'club_name');
        return view('pages.users.index', compact('organizations', 'clubs'));
    }

    public function getDataTables(){
        $query = User::query()
            ->select('users.id', 'users.name', 'users.email','users.deleted_at', 'users.club_id', 'users.organization_id')
            ->withTrashed()
            ->with(['roles', 'club', 'organization']);

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
                $status = $row->deleted_at == null;

                if (!$status) {
                    return '<span class="badge bg-secondary">Nonaktif</span>';
                }
                return '<span class="badge bg-success">Aktif</span>';
            })
            ->orderColumn('status_badge', function ($query, $order) {
                $query->orderByRaw("users.deleted_at IS NULL $order");
            })
            ->addColumn('action', function (User $row) {
                $btn = '
                    <button class="btn btn-outline-primary" title="Edit" id="btnUserEdit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-outline-secondary" title="Roles" onclick="openUserRoles(' . $row->id . ')">
                        <i class="bi bi-person-badge"></i>
                    </button>
                ';
                $btnHapus = '<button class="btn btn-outline-danger" title="Hapus / Non Aktif" onclick="deleteUser(' . $row->id . ')">
                                <i class="bi bi-x"></i>
                            </button>';
                $btnRestore = '<button class="btn btn-outline-warning" title="Aktikan Kembali" onclick="restoreUser(' . $row->id . ')">
                                <i class="bi bi-recycle"></i>
                            </button>';

                $mergeBtn = $row->deleted_at == null
                            ? $btn . $btnHapus
                            : $btn . $btnRestore;
                return '
                    <div class="btn-group btn-group-sm">'
                        . $mergeBtn .
                    '</div>';
            })
            ->rawColumns(['action', 'roles_badges', 'status_badge'])
            ->make(true);
    }

    public function store(Request $req){
        $userId = $req->input('id');

        $validators = Validator::make($req->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($userId ?: 'NULL') . ',id',
            'password' => $userId ? 'nullable|min:6' : 'required|min:6',
            'club_id' => 'nullable',
            'organization_id' => 'nullable',
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

            // password: hash hanya jika diisi / create
            if (!$userId || $req->filled('password')) {
                $user->password = Hash::make($req->password);
            }

            // sesuaikan field kamu
            if ($req->has('club_id')) $user->club_id = $req->club_id;
            if ($req->has('organization_id')) $user->organization_id = $req->organization_id;

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

    public function destroy($id){
        try {
            $item = User::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Akun berhasil di hapus / di nonaktifkan',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150),
            ], 500);
        }
    }
    public function restore($id){
        try {
            User::withTrashed()->findOrFail($id)->restore();

            return response()->json([
                'status' => true,
                'message' => 'Sukses mengaktifkan akun',
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

    public function updatePassword(Request $req){
        $validators = Validator::make($req->all(), [
            'user_id'          => 'required|exists:users,id',
            'password'         => [
                'required',
                'string',
                'min:8',
                'regex:/[a-zA-Z]/',
                'regex:/[0-9]/',
                'regex:/[A-Z]/',
                'regex:/[^a-zA-Z0-9]/',
            ],
            'password_confirm' => 'required|same:password',
        ], [
            'user_id.required'          => 'Akun tidak ditemukan',
            'user_id.exists'            => 'Akun tidak ditemukan',
            'password.required'         => 'Password harus diisi',
            'password.min'              => 'Password minimal 8 karakter',
            'password.regex'            => 'Password harus mengandung huruf, angka, huruf besar, dan simbol',
            'password_confirm.required' => 'Konfirmasi password harus diisi',
            'password_confirm.same'     => 'Konfirmasi password tidak cocok',
        ]);

        if($validators->fails()){
            return $this->validationError($validators->errors(), 'Validasi gagal');
        }

        if ((int) $req->user_id !== auth()->user()->id) {
            return $this->error('Anda tidak memiliki akses untuk mengubah password ini');
        }

        try {
            $item = User::find($req->user_id);
            $item->password = Hash::make($req->password);
            $item->save();

            return $this->success($item,'berhasil ubah password');
        } catch (\Throwable $th) {
            return $this->error(substr($th->getMessage,0,150));
        }
    }

    public function updateProfile(Request $req){
        $userId = $req->input('user_id');

        $validators = Validator::make($req->all(), [
            'user_id' => 'required|exists:users,id',
            'user_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . ($userId ?: 'NULL') . ',id',
        ]);

        if ($validators->fails()) {
            return $this->validationError($validators->errors(), 'Validasi gagal');
        }

        try {
            $user = User::findOrFail($userId);
            $user->name = $req->user_name;
            $user->email = $req->email;
            $user->save();

            return $this->success($user,'berhasil perbarui profil');
        } catch (\Throwable $th) {
            return $this->error(substr($th->getMessage,0,150));
        }
    }

    public function userRoles($id){
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

    public function syncUserRoles(Request $req, $id){
        try {
            $user = User::findOrFail($id);

            $roleIds = $req->input('roles', []);
            $roles = Role::whereIn('id', $roleIds)->get();

            $user->syncRoles($roles);

            return response()->json([
                'status' => true,
                'message' => 'Roles berhasil disimpan',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(),0,150),
            ]);
        }
    }
}
