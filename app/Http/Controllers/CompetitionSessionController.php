<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use Illuminate\Http\Request;

class CompetitionSessionController extends Controller
{
     public function data(){
        $data = Competition::query();

        return DataTables::of($data)
        ->addColumn('action', function($row){
            $edit = '<button class="btn btn-warning btn-sm" onclick="edit(this)">Edit</button>';
            $dlt = '<button class="btn btn-danger btn-sm" data-id="'.$row->id.'" onclick="destroy(this)">Hapus</button>';
            return $edit .' '. $dlt;
        })
        ->addColumn('comp_date',function($row){
            if(!$row->start_date || !$row->end_date){
                return '-';
            }
            return ((Carbon::parse($row->start_date)->format('d-m-Y') ?? '') .' - ' . (Carbon::parse($row->end_date)->format('d-m-y') ?? ''));
        })
        ->addColumn('registration_date',function($row){
            if (!$row->registration_start || !$row->registration_end) {
                return '-';
            }
            return ((Carbon::parse($row->registration_start)->format('d-m-Y') ?? '') .' - ' . (Carbon::parse($row->registration_end)->format('d-m-Y') ?? ''));
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
            return $row?->created_at->format('d-m-Y');
        })
        ->rawColumns(['action','statusAttr'])
        ->make(true);
    }
    public function index(Competition $competition){
        return view('pages.competition.tabs.sessions', compact('competition'));
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
            'competition_id' => 'nullable|integer|exists:officials,id',
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
}
