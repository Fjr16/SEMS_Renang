<?php

namespace App\Http\Controllers;

use App\Enums\Gender;
use App\Enums\TeamType;
use App\Models\Athlete;
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
        ->editColumn('status', function($row){
            return $row->status === 'active' ? '<span class="badge bg-success">Aktif</span>' : '<span class="badge bg-secondary">Tidak Aktif</span>';
        })
        ->rawColumns(['action','genderAttr','foto', 'status'])
        ->make(true);
    }

    public function index(){
        $genders = Gender::cases();
        $clubCategories = TeamType::cases();
        return view('pages.atlet.index', compact('genders', 'clubCategories'));
    }
    public function store(Request $r){
        $validators = Validator::make($r->all(), [
            'club_id' => 'required|integer|exists:clubs,id',
            'registration_number' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'bod' => 'required|date|before_or_equal:today',
            'gender' => ['required', new Enum(Gender::class)],
            'status' => ['nullable', 'in:active,inactive'],
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
        $item->registration_number = $r->registration_number;
        $item->status = $r->status ? ($r->status === 'active' ? 'active' : 'inactive') : 'inactive';
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

    // guest controller
    public function indexGuest(){
        $q = request('q');
        $gender = request('gender');
        $province = request('province');

        $query = Athlete::query()
        ->with('club')
        ->where('status', 'active')
        ->when($q, function($qq) use ($q){
            $qq->where(function($subQ) use ($q){
                $subQ->where('name', 'LIKE', '%'.$q.'%')
                    ->orWhere('code', 'LIKE', '%'.$q.'%')
                    ->orWhere('registration_number', 'LIKE', '%'.$q.'%')
                    ->orWhereHas('club', function($clubQ) use ($q){
                        $clubQ->where('club_name', 'LIKE', '%'.$q.'%')
                            ->orWhere('club_code', 'LIKE', '%'.$q.'%');
                    });
            });
        })
        ->when($gender, function($qq) use ($gender){
            $qq->where('gender', strtolower($gender));
        })
        ->orderBy('name', 'asc');
        $athletes = $query->paginate(21)->withQueryString();

        if(request()->ajax()){
            return view('pages.guest.atlet.partials.cards', compact('athletes'))->render();
        }

        $accessType = 'Guest';
        return view('pages.guest.atlet.index',compact('athletes', 'accessType'));
    }
    public function showGuest(){
        return 'berhasil';
        // $data = Athlete::query()->with('club')->get();
        // return view('pages.guest.atlet.index',compact('data'));
    }
}
