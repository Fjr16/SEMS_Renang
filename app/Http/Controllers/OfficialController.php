<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Enums\License;
use App\Enums\TeamType;
use App\Models\Official;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Yajra\DataTables\Facades\DataTables;

class OfficialController extends Controller
{
    public function data(){
        $data = Official::query()->with('club');

        return DataTables::of($data)
        ->addColumn('action', function($row){
            $edit = '<button class="btn btn-warning btn-sm" onclick="edit('.$row->id.')"><i class="bi bi-pencil"></i></button>';
            $dlt = '<button class="btn btn-danger btn-sm" onclick="destroy('.$row->id.')"><i class="bi bi-trash"></i></button>';
            return '<div class="btn-group">
                        '.
                        $edit .
                        $dlt
                        .'
                    </div>';
        })
        ->addColumn('clubDesc',function($row){
            return '[' . $row->club->club_code .'] ' . $row->club->club_name;
        })
        ->addColumn('genderAttr', function($row){
            if ($row->gender) {
                $enumGender = Gender::from($row->gender);
                $classList = $enumGender->class();
                $label = $enumGender->label();
                return $enumGender
                ? '<span class="badge '. $classList .'">'. $label .'</span>'
                : '<span class="badge bg-danger text-white">Tidak Dikenali</span>';
            }else{
                return null;
            }
        })
        ->addColumn('licenseAttr', function($row){
            if ($row->license) {
                $enumLicense = License::from($row->license);
                $classList = $enumLicense->class();
                $label = $enumLicense->label();
                return $enumLicense
                ? '<span class="badge '. $classList .'">'. $label .'</span>'
                : '<span class="badge bg-danger text-white">Tidak Dikenali</span>';
            }else{
                return null;
            }
        })
        ->editColumn('foto', function($row){
            if(!$row->foto){
                return '<div style="width:40px;height:40px;background:#f5f5f5;border:1px dashed #ccc;
                            border-radius:6px;display:flex;align-items:center;justify-content:center;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" fill="#ccc" viewBox="0 0 16 16">
                                <path d="M6.002 5.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0z"/>
                                <path d="M2.002 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h12a2 2 0 0 0
                                        2-2V3a2 2 0 0 0-2-2h-12zm12 1a1 1 0 0 1 1 1v6.5l-3.777-1.947a.5.5
                                        0 0 0-.577.093l-3.71 3.71-2.66-1.772a.5.5 0 0 0-.63.062L1.002
                                        12V3a1 1 0 0 1 1-1h12z"/>
                            </svg>
                        </div>';
            };
            $url = Storage::url($row->foto);
            return '<a href="'.$url.'" target="_blank">
                        <img src="'. $url .'" alt="logo-klub" class="img-fluid">
                    </a>';
        })
        ->rawColumns(['action','genderAttr', 'licenseAttr', 'foto'])
        ->make(true);
    }
    public function index(){
        $genders = Gender::cases();
        $licenses = License::cases();
        $clubCategories = TeamType::cases();
        return view('pages.official.index', compact('genders', 'clubCategories', 'licenses'));
    }
    public function store(Request $r){
        $validators = Validator::make($r->all(), [
            'club_id' => 'required|integer|exists:clubs,id',
            'role' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'gender' => ['required', new Enum(Gender::class)],
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'official_id' => 'nullable|integer|exists:officials,id',
        ]);

        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(),0,100),
            ]);
        }

        $item = $r->input('official_id') ? Official::find($r->input('official_id')) : new Official;
        $item->club_id = $r->club_id;
        $item->role = $r->role;
        $item->name = $r->name;
        $item->gender = $r->gender;
        $item->license = $r->license;
        if($r->file('foto')){
            $item->foto = $r->file('foto')->store('club/official', 'public');
        }

        try {
            DB::beginTransaction();
            $item->save();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $r->input('official_id') ? 'Sukses Update Data' : 'Sukses Simpan Data',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(),0,100) ?? 'Gagal Simpan Data',
            ]);
        }
    }
    public function destroy($id){
        try {
            $item = Official::findOrFail($id);
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
