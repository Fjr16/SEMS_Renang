<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Yajra\DataTables\DataTables;

class RolesPermissionsController extends Controller
{
    public function rolesData()
    {
        Carbon::setLocale('id');
        $query = Role::query()
            ->select('roles.name', 'roles.id', 'roles.created_at')
            ->withCount('permissions');

        return DataTables::of($query)
            ->addColumn('action', function (Role $role) {
                return '
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-primary"
                                title="Assign Permission"
                                onclick="openAssign(' . $role->id . ')">
                            <i class="bi bi-key"></i>
                        </button>
                    </div>
                ';
            })
            ->editColumn('permissions_count', fn($r) =>
                '<span class="badge bg-info">' . $r->permissions_count . '</span>'
            )
            ->editColumn('created_at', fn($r) =>
                $r->created_at?->translatedformat('d F Y') ?? '-'
            )
            ->orderColumn('permissions_count', function ($query, $order) {
                $query->orderByRaw("CAST(permissions_count AS UNSIGNED) $order");
            })
            ->rawColumns(['action','permissions_count'])
            ->make(true);
    }

    public function storeRole(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id'         => 'nullable|exists:roles,id',
            'name'       => 'required|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $role = $request->id
            ? Role::findOrFail($request->id)
            : new Role();

        $role->name       = $request->name;
        $role->guard_name = 'web';
        $role->save();

        return response()->json([
            'status'  => true,
            'message' => 'Role berhasil disimpan',
        ]);
    }

    public function rolePermissions($id)
    {
        $role = Role::findOrFail($id);

        return response()->json([
            'role' => [
                'id'   => $role->id,
                'name' => $role->name,
            ],
            'permissions' => Permission::orderBy('name')
                ->get(['id','name','guard_name']),
            'assigned' => $role->permissions()->pluck('id')->values(),
        ]);
    }

    public function syncRolePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $permIds = $request->input('permissions', []);
        $perms   = Permission::whereIn('id', $permIds)->get();

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        $role->syncPermissions($perms);

        return response()->json([
            'status'  => true,
            'message' => 'Mapping permission berhasil disimpan',
        ]);
    }
}
