<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class OrganizationController extends Controller
{
     public function data(){
        Carbon::setLocale('ID');
        $data = Organization::query();

        return DataTables::of($data)
        ->addColumn('action', function($row){
            $edit = '<button class="btn btn-warning btn-sm" data-id="'.$row->id.'" data-name="'.$row->name.'" onclick="edit(this)"><i class="bi bi-pencil"></i></button>';
            $dlt = '<button class="btn btn-danger btn-sm" onclick="destroy('.$row->id.')"><i class="bi bi-trash"></i></button>';
            return '<div class="btn-group">
                        '.
                        $edit .
                        $dlt
                        .'
                    </div>';
        })
        ->editColumn('created_at', function($item){
            return $item->created_at->translatedFormat('d F Y');
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function index(){
        return view('pages.organization.index');
    }
    public function store(Request $r){
        $validators = Validator::make($r->all(), [
            'name' => 'required|string|max:255',
            'organization_id' => 'nullable|exists:organizations,id',
        ]);

        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(),0,100),
            ]);
        }

        try {
            $item = $r->input('organization_id') ? Organization::find($r->input('organization_id')) : new Organization;
            $item->name = $r->name;
            $item->save();

            return response()->json([
                'status' => true,
                'message' => $r->input('organization_id') ? 'Sukses Update Data' : 'Sukses Simpan Data',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(),0,100) ?? 'Gagal Simpan Data',
            ]);
        }
    }
    public function destroy($id){
        try {
            $item = Organization::findOrFail($id);
            $item->delete();
            return response()->json([
                'status' => true,
                'message' => 'Sukses hapus data'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(),0,100) || 'Gagal Hapus data'
            ]);
        }
    }
}
