<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Models\Athlete;
use App\Models\ClubRoleCategory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Yajra\DataTables\Facades\DataTables;

class AthleteController extends Controller
{
    public function data(){
        $data = Athlete::query()->with('club');

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
        ->addColumn('codeName', function($row){
            return '['. $row->code .'] ' . $row->name;
        })
        ->addColumn('clubDesc',function($row){
            return '[' . $row->club->club_code .'] ' . $row->club->club_name;
        })
        ->editColumn('bod', function($row){
            return $row->bod ? Carbon::parse($row->bod)->format('d F Y') : null;
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
        ->editColumn('foto', function($row){
            if(!$row->foto) return '-';
            $url = Storage::url($row->foto);
            return '<a href="'.$url.'" target="_blank">
                        <img src="'. $url .'" alt="logo-klub" class="img-fluid">
                    </a>';
        })
        ->rawColumns(['action','genderAttr','foto'])
        ->make(true);
    }

    public function index(){
        $genders = Gender::cases();
        $clubCategories = ClubRoleCategory::all();
        return view('pages.atlet.index', compact('genders', 'clubCategories'));
    }
    public function store(Request $r){
        $validators = Validator::make($r->all(), [
            'club_id' => 'required|integer|exists:clubs,id',
            'name' => 'required|string|max:255',
            'bod' => 'required|date|before_or_equal:today',
            'gender' => ['required', new Enum(Gender::class)],
            'school_name' => 'nullable|string|max:255',
            'city_name' => 'nullable|string|max:255',
            'province_name' => 'nullable|string|max:255',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'athlete_id' => 'nullable|integer|exists:athletes,id',
        ]);

        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(),0,100),
            ]);
        }

        $item = $r->input('athlete_id') ? Athlete::find($r->input('athlete_id')) : new Athlete;
        $item->club_id = $r->club_id;
        $item->name = $r->name;
        $item->bod = $r->bod;
        $item->gender = $r->gender;
        $item->school_name = $r->school_name;
        // $item->club_name = $r->club_name;
        $item->city_name = $r->city_name;
        $item->province_name = $r->province_name;
        if($r->file('foto')){
            $item->foto = $r->file('foto')->store('club/athlete', 'public');
        }

        try {
            DB::beginTransaction();
            $item->save();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $r->input('athlete_id') ? 'Sukses Update Data' : 'Sukses Simpan Data',
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
            $item = Athlete::findOrFail($id);
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
