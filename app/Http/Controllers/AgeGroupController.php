<?php

namespace App\Http\Controllers;

use App\Models\AgeGroup;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AgeGroupController extends Controller
{
     public function data(){
        $data = AgeGroup::query();

        return DataTables::of($data)
        ->addColumn('action', function($row){
            $edit = '<button class="btn btn-warning btn-sm" data-id="'.$row->id.'" data-label="'.$row->label.'" data-min_age="'.$row->min_age.'" data-max_age="'.$row->max_age.'" onclick="edit(this)"><i class="bi bi-pencil"></i></button>';
            $dlt = '<button class="btn btn-danger btn-sm" onclick="destroy('.$row->id.')"><i class="bi bi-trash"></i></button>';
            return '<div class="btn-group">
                        '.
                        $edit .
                        $dlt
                        .'
                    </div>';
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function index(){
        return view('pages.age_group.index');
    }
    public function store(Request $r){
        $validators = Validator::make($r->all(), [
            'label' => 'required|string|max:255',
            'min_age' => 'nullable|numeric|min:0',
            'max_age' => 'nullable|numeric|min:0',
            'age_group_id' => 'nullable|numeric|exists:age_groups,id',
        ]);

        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(),0,100),
            ]);
        }

        try {
            $item = $r->input('age_group_id') ? AgeGroup::find($r->input('age_group_id')) : new AgeGroup;
            $item->label = $r->label;
            $item->min_age = $r->min_age;
            $item->max_age = $r->max_age;
            $item->save();

            return response()->json([
                'status' => true,
                'message' => $r->input('age_group_id') ? 'Sukses Update Data' : 'Sukses Simpan Data',
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
            $item = AgeGroup::findOrFail($id);
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
