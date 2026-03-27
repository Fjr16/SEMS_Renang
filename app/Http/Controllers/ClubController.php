<?php

namespace App\Http\Controllers;

use App\Enums\TeamType;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Yajra\DataTables\Facades\DataTables;

class ClubController extends Controller
{
    // CRUD Master Data
    public function data(){
        $data = Club::select(
            'id',
            'club_name',
            'club_code',
            'club_city',
            'club_province',
            'club_lead',
            'lead_phone',
            'team_type',
            'club_logo',
        );

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
        ->addColumn('tipe_klub', function($row){
            return TeamType::tryFrom($row->team_type)?->label();
        })
        ->editColumn('club_logo', function($row){
            if(!$row->club_logo) {
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
            }
            $url = Storage::url($row->club_logo);
            return '<a href="'.$url.'" target="_blank">
                        <img src="'. $url .'" alt="logo-klub" class="img-fluid">
                    </a>';
        })
        ->rawColumns(['action','club_logo'])
        ->make(true);
    }

    public function index(){
        $data = TeamType::cases();
        return view('pages.club.index', compact('data'));
    }
    public function store(Request $r){
        try {
            $validators = Validator::make($r->all(), [
                'team_type' => ['required', new Enum(TeamType::class)],
                'club_code' => 'required',
                'club_name' => 'required',
                'club_lead' => 'nullable',
                'lead_phone' => 'nullable',
                'club_city' => 'nullable',
                'club_province' => 'nullable',
                'club_logo' => 'nullable',
            ]);

            if ($validators->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => substr($validators->errors()->first(),0,100),
                ]);
            }

            $item = $r->input('club_id') ? Club::find($r->input('club_id')) : new Club;
            if($r->file('club_logo')){
                if ($item->club_logo) Storage::disk('public')->delete($item->club_logo);
                $item->club_logo = $r->file('club_logo')->store('/club/logo', 'public');
            }
            $item->team_type = $r->team_type;
            $item->club_code = $r->club_code;
            $item->club_name = $r->club_name;
            $item->club_lead = $r->club_lead;
            $item->lead_phone = $r->lead_phone;
            $item->club_city = $r->club_city;
            $item->club_province = $r->club_province;
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
            $item = Club::find($id);
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
    // END CRUD Master Data
}
