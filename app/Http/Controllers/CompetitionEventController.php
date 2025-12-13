<?php

namespace App\Http\Controllers;

use App\Enums\EventSystem;
use App\Enums\EventType;
use App\Enums\Gender;
use App\Enums\Stroke;
use App\Models\Competition;
use App\Models\CompetitionEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CompetitionEventController extends Controller
{
    public function data(Competition $competition){
        $data = CompetitionEvent::query()
        ->with(['ageGroup','competitionSession'])
        ->where('competition_id', $competition->id);

        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('action', function($row) use ($competition){
            $urlDlt = route('competition.tab.events.destroy', ['competition' => $competition, 'id' => $row->id]);
            $edit = '<button class="btn btn-warning btn-sm" data-table="eventsTable" data-modal="modalEvent" data-form="eventForm" onclick="editEvent(this)"><i class="bi bi-pencil"></i></button>';
            $dlt = '<button class="btn btn-danger btn-sm" data-url="'.$urlDlt.'" data-table="eventsTable" onclick="destroyGlobal(this)"><i class="bi bi-trash"></i></button>';
            return '<div class="btn-group">
                        '.
                        $edit .
                        $dlt
                        .'
                    </div>';
        })
        ->addColumn('strokeAttr',function($row){
            if ($row->stroke) {
                $enumStroke = Stroke::from($row->stroke);
                $classList = $enumStroke->class();
                $label = $enumStroke->label();
                return $enumStroke
                ? '<span class="badge '. $classList .'">'. $label .'</span>'
                : '<span class="badge bg-danger text-white">Tidak Dikenali</span>';
            }else{
                return null;
            }
        })
        ->addColumn('genderAttr', function($row){
            if (!$row->gender || $row->gender == 'mixed') {
                return $row->gender
                ? '<span class="badge bg-secondary text-white">'.$row->gender.'</span>'
                : '<span class="badge bg-danger text-white">Tidak Dikenali</span>';
            }else{
                $enumGender = Gender::from($row->gender);
                $classList = $enumGender->class();
                $label = $enumGender->label();
                return $enumGender
                ? '<span class="badge '. $classList .'">'. $label .'</span>'
                : '<span class="badge bg-danger text-white">Tidak Dikenali</span>';
            }
        })
        ->addColumn('eventTypeAttr', function($row){
            if ($row->event_type) {
                $enumEtype = EventType::from($row->event_type);
                $classList = $enumEtype->class();
                $label = $enumEtype->label();
                return $enumEtype
                ? '<span class="badge '. $classList .'">'. $label .'</span>'
                : '<span class="badge bg-danger text-white">Tidak Dikenali</span>';
            }else{
                return null;
            }
        })
        ->addColumn('eventSystemAttr', function($row){
            if ($row->event_system) {
                $enumEsystem = EventSystem::from($row->event_system);
                $classList = $enumEsystem->class();
                $label = $enumEsystem->label();
                return $enumEsystem
                ? '<span class="badge '. $classList .'">'. $label .'</span>'
                : '<span class="badge bg-danger text-white">Tidak Dikenali</span>';
            }else{
                return null;
            }
        })
        ->addColumn('kelompok_umur', function($row){
            return $row->ageGroup?->label ?? '-';
        })
        // ->addColumn('minDOB',function($row){
        //     if(!$row->min_dob){
        //         return '-';
        //     }
        //     return Carbon::parse($row->min_dob)->translatedFormat('d F Y');
        // })
        // ->addColumn('maxDOB',function($row){
        //     if(!$row->max_dob){
        //         return '-';
        //     }
        //     return Carbon::parse($row->max_dob)->translatedFormat('d F Y');
        // })
        ->addColumn('jarak',function($row){
            return $row->distance . ' m';
        })
        ->addColumn('biaya_pendaftaran',function($row){
            return 'Rp ' . ($row->registration_fee ? number_format($row->registration_fee,0,',','.') : '0');
        })
        ->addColumn('session_date', function($row){
            return $row->competitionSession?->date ?? '-';
        })
        ->addColumn('session_name', function($row){
            $nm = $row->competitionSession?->name ?? '-';
            $rentang = ' [' . ($row->competitionSession?->start_time ?? '') . ' - ' . ($row->competitionSession?->end_time ?? '') . ']';
            return $nm . $rentang;
        })
        ->rawColumns(['action', 'strokeAttr', 'genderAttr', 'eventTypeAttr', 'eventSystemAttr'])
        ->make(true);
    }
    public function store(Request $r){
        $validators = Validator::make($r->all(), [
            'competition_event_id' => 'nullable|integer|exists:competition_events,id',
            'competition_id' => 'required|integer|exists:competitions,id',
            'competition_session_id' => 'required|integer|exists:competition_sessions,id',
            'age_group_id' => 'required|exists:age_groups,id',
            'distance' => 'required|integer',
            'stroke' => 'required|string|max:50',
            'gender' => 'required|string|max:10',
            'event_type' => 'required|string|max:20',
            'event_system' => 'required|string|max:20',
            'remarks' => 'nullable',
            'registration_fee' => 'required|numeric|min:0',
        ]);

        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(),0,100),
            ]);
        }

        $item = $r->input('competition_event_id') ? CompetitionEvent::find($r->input('competition_event_id')) : new CompetitionEvent;
        $item->competition_id = $r->competition_id;
        $item->competition_session_id = $r->competition_session_id;
        $item->age_group_id = $r->age_group_id;
        $item->distance = $r->distance;
        $item->stroke = $r->stroke;
        $item->gender = $r->gender;
        $item->event_type = $r->event_type;
        $item->event_system = $r->event_system;
        $item->remarks = $r->remarks;
        $item->registration_fee = $r->registration_fee;

        try {
            DB::beginTransaction();
            $item->save();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $r->input('competition_event_id') ? 'Sukses Update Data' : 'Sukses Simpan Data',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(),0,100) ?? 'Gagal Simpan Data',
            ]);
        }
    }
    public function destroy(Competition $competition, $id){
        try {
            $item = CompetitionEvent::findOrFail($id);
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
