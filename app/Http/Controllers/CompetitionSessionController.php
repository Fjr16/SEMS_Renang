<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Models\CompetitionSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CompetitionSessionController extends Controller
{
    public function data(Competition $competition){
        Carbon::setLocale('id');
        $data = CompetitionSession::query()
        ->where('competition_id', $competition->id)
        ->orderBy('session_order', 'asc');

        return DataTables::of($data)
        ->addColumn('action', function($row) use ($competition){
            $urlDlt = route('competition.tab.sessions.destroy', ['competition' => $competition, 'id' => $row->id]);

            $edit = '<button class="btn btn-warning btn-sm" data-modal="modalSessions" data-form="sessionForm" data-table="sessionsTable" onclick="editSession(this)"><i class="bi bi-pencil"></i></button>';
            $dlt = '<button class="btn btn-danger btn-sm" data-url="'.$urlDlt.'" data-table="sessionsTable" onclick="destroyGlobal(this)"><i class="bi bi-trash"></i></button>';
            return '<div class="btn-group">
                        '.
                        $edit .
                        $dlt
                        .'
                    </div>';
        })
        ->editColumn('session_date',function($row){
            if(!$row->session_date){
                return '-';
            }
            return Carbon::parse($row->session_date)->translatedFormat('d F Y');
        })
        ->addColumn('desc_pool', function($row){
            return '[' . $row->pool->code . '] ' . $row->pool->name;
        })
        ->rawColumns(['action'])
        ->make(true);
    }
    public function store(Request $r){
        $validators = Validator::make($r->all(), [
            'competition_id' => 'required|integer|exists:competitions,id',
            'pool_id' => 'required|integer|exists:pools,id',
            'name' => 'required|string|max:255',
            'session_date' => 'required|date',
            'session_order' => 'required|integer',
            'competition_session_id' => 'nullable|integer|exists:competition_sessions,id',
        ]);

        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(),0,100),
            ]);
        }

        $item = $r->input('competition_session_id') ? CompetitionSession::find($r->input('competition_session_id')) : new CompetitionSession();
        $item->competition_id = $r->competition_id;
        $item->pool_id = $r->pool_id;
        $item->name = $r->name;
        $item->session_date = $r->session_date;
        $item->session_order = $r->session_order;

        try {
            DB::beginTransaction();
            $item->save();
            DB::commit();

            return response()->json([
                'status' => true,
                'message' => $r->input('competition_session_id') ? 'Sukses Update Data' : 'Sukses Simpan Data',
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
            $item = CompetitionSession::findOrFail($id);
            $item->competitionEvents()->delete();
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
