<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionEvent;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CompetitionEventController extends Controller
{
    public function data(Competition $competition){
        $data = CompetitionEvent::query()
        ->where('competition_id', $competition->id);

        return DataTables::of($data)
        ->addColumn('action', function($row){
            $edit = '<button class="btn btn-warning btn-sm" onclick="edit(this)"><i class="bi bi-pencil"></i></button>';
            $dlt = '<button class="btn btn-danger btn-sm" data-id="'.$row->id.'" onclick="destroy(this)"><i class="bi bi-trash"></i></button>';
            return '<div class="btn-group">
                        '.
                        $edit .
                        $dlt
                        .'
                    </div>';
        })
        ->addColumn('session_date',function($row){
            if(!$row->date){
                return '-';
            }
            return Carbon::parse($row->date)->translatedFormat('d F Y');
        })
        ->rawColumns(['action'])
        ->make(true);
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
