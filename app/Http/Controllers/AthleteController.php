<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Models\Athlete;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AthleteController extends Controller
{
    public function data(){
        $data = Athlete::query()->with('club');

        return DataTables::of($data)
        ->addColumn('action', function($row){
            $edit = '<button class="btn btn-warning btn-sm" onclick="edit(this)">Edit</button>';
            $dlt = '<button class="btn btn-danger btn-sm" data-id="'.$row->id.'" onclick="destroy(this)">Hapus</button>';
            return $edit .' '. $dlt;
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
        return view('pages.atlet.index');
    }
    public function store(Request $r){
        try {
            $validators = Validator::make($r->all(), [
                'club_id' => 'required',
                'code' => 'required',
                'name' => 'required',
                'bod' => 'required',
                'gender' => 'required',
                'school_name' => 'required',
                'club_name' => 'nullable',
                'city_name' => 'nullable',
                'province_name' => 'nullable',
            ]);

            if ($validators->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => substr($validators->errors()->first(),0,100),
                ]);
            }

            $item = $r->input('athlete_id') ? Athlete::find($r->input('athlete_id')) : new Athlete;
            if($r->file('foto')){
                if ($item->foto) Storage::disk('public')->delete($item->foto);
                $item->foto = $r->file('foto')->store('/club/athlete', 'public');
            }
            $item->club_id = $r->club_id;
            $item->code = $r->code;
            $item->name = $r->name;
            $item->bod = $r->bod;
            $item->gender = $r->gender;
            $item->school_name = $r->school_name;
            $item->club_name = $r->club_name;
            $item->city_name = $r->city_name;
            $item->province_name = $r->province_name;
            $item->save();

            return response()->json([
                'status' => true,
                'message' => 'Sukses simpan data',
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
            $item = Athlete::find($id);
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
