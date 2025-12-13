<?php

namespace App\Http\Controllers;

use App\Enums\CompetitionStatus;
use App\Enums\EventSystem;
use App\Enums\EventType;
use App\Enums\Gender;
use App\Enums\Stroke;
use App\Models\AgeGroup;
use App\Models\Competition;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Yajra\DataTables\Facades\DataTables;

class CompetitionController extends Controller
{
    public function data(){
        $data = Competition::query();

        return DataTables::of($data)
        ->addColumn('action', function($row){
            $routeShow = route('competition.show', $row);
            $edit = '<button class="btn btn-warning btn-sm" onclick="edit(this)"><i class="bi bi-pencil"></i></button>';
            $dlt = '<button class="btn btn-danger btn-sm" data-id="'.$row->id.'" onclick="destroy(this)"><i class="bi bi-trash"></i></button>';
            $show = '<a href="'.$routeShow.'" class="btn btn-info btn-sm text-white"><i class="bi bi-eye"></i></a>';

            return '<div class="btn-group">
                        '.
                        $show .
                        $edit .
                        $dlt
                        .'
                    </div>';
        })
        ->addColumn('comp_date',function($row){
            if(!$row->start_date || !$row->end_date){
                return '-';
            }
            return ((Carbon::parse($row->start_date)->format('d/m/Y') ?? '') .' - ' . (Carbon::parse($row->end_date)->format('d/m/y') ?? ''));
        })
        ->addColumn('registration_date',function($row){
            if (!$row->registration_start || !$row->registration_end) {
                return '-';
            }
            return ((Carbon::parse($row->registration_start)->format('d/m/Y') ?? '') .' - ' . (Carbon::parse($row->registration_end)->format('d/m/Y') ?? ''));
        })
        ->addColumn('statusAttr', function($row){
            if ($row->status) {
                $enumStts = CompetitionStatus::from($row->status);
                $classList = $enumStts->class();
                $label = $enumStts->label();
                return $enumStts
                ? '<span class="badge '. $classList .'">'. $label .'</span>'
                : '<span class="badge bg-danger text-white">Tidak Dikenali</span>';
            }else{
                return null;
            }
        })
        ->editColumn('created_at', function($row){
            return $row?->created_at->format('d/m/Y');
        })
        ->rawColumns(['action','statusAttr'])
        ->make(true);
    }
    public function index(){
        $data = CompetitionStatus::cases();
        return view('pages.competition.index', compact('data'));
    }
    public function store(Request $r){
        $validators = Validator::make($r->all(), [
            'name' => 'required|string|max:255',
            'organizer' => 'required|string|max:255',
            'start_date' => 'required|date|before_or_equal:end_date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'location' => 'required|string|max:255',
            'registration_start' => 'required|date|before_or_equal:registration_end',
            'registration_end' => 'required|date|after_or_equal:registration_start',
            'status' => ['required', new Enum(CompetitionStatus::class)],
            'competition_id' => 'nullable|integer|exists:competitions,id',
        ],[
            'start_date.before_or_equal' => 'Tanggal Mulai Kompetisi harus kecil dari tanggal selesai kompetisi',
            'end_date.after_or_equal' => 'Tanggal Selesai Kompetisi harus besar dari tanggal mulai kompetisi',
            'registration_start.before_or_equal' => 'Tanggal buka registrasi harus kecil dari tanggal tutup registrasi',
            'registration_end.after_or_equal' => 'Tanggal tutup registrasi harus besar dari tanggal buka registrasi',
        ]);

        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(),0,100),
            ]);
        }

        $item = $r->input('competition_id') ? Competition::find($r->input('competition_id')) : new Competition;
        $item->name = $r->name;
        $item->organizer = $r->organizer;
        $item->start_date = $r->start_date;
        $item->end_date = $r->end_date;
        $item->location = $r->location;
        $item->registration_start = $r->registration_start;
        $item->registration_end = $r->registration_end;
        $item->status = $r->status;

        try {
            DB::beginTransaction();
            $item->save();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $r->input('competition_id') ? 'Sukses Update Data' : 'Sukses Simpan Data',
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
            $item = Competition::findOrFail($id);
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
    // manajemen competition || show detail kompetisi
    public function show(Competition $competition){
        Carbon::setLocale('id');
        $enumStts = CompetitionStatus::class;
        $enumStroke = Stroke::cases();
        $enumGender = Gender::cases();
        $enumEType = EventType::cases();
        $enumESystem = EventSystem::cases();
        $ageGroups = AgeGroup::all();
        $counts = [
            'sessions'  => $competition->sessions()->count(),
            'events'    => $competition->events()->count(),
            // 'entries'   => $competition->entries()->count(),
            // 'heats'     => $competition->heats()->count(),
            // 'results'   => $competition->results()->count(),
            // 'points'    => $competition->teamPoints()->count(),
            // 'officials' => $competition->officials()->count(),
            // 'payments'  => $competition->payments()->count(),
        ];
        return view('pages.competition.show', compact('competition', 'counts', 'enumStts', 'enumStroke', 'enumGender', 'enumEType', 'enumESystem', 'ageGroups'));
    }
}
