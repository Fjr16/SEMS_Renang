<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Enums\License;
use App\Models\ClubRoleCategory;
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
            $edit = '<button class="btn btn-warning btn-sm" onclick="edit('.$row->id.')">Edit</button>';
            $dlt = '<button class="btn btn-danger btn-sm" onclick="destroy('.$row->id.')">Hapus</button>';
            return $edit .' '. $dlt;
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
            if(!$row->foto) return '-';
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
        $clubCategories = ClubRoleCategory::all();
        return view('pages.official.index', compact('genders', 'clubCategories', 'licenses'));
    }
    public function store(Request $r){
        $validators = Validator::make($r->all(), [
            'club_id' => 'required|integer|exists:clubs,id',
            'name' => 'required|string|max:255',
            'gender' => ['required', new Enum(Gender::class)],
            // 'current_club' => 'nullable|string|max:255',
            'current_city' => 'nullable|string|max:255',
            'current_province' => 'nullable|string|max:255',
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
        $item->name = $r->name;
        $item->gender = $r->gender;
        $item->license = $r->license;
        $item->current_city = $r->current_city;
        $item->current_province = $r->current_province;
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
