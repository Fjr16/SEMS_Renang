<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\ClubRoleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ClubController extends Controller
{
    // CRUD Master Data
    public function data(){
        $data = Club::leftjoin('club_role_categories as category', 'clubs.club_role_category_id', '=', 'category.id')
        ->select(
            'clubs.*',
            'category.id as category_id',
            'category.code as category_code',
            'category.name as category_name'
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
        ->editColumn('club_role_category_id', function($row){
            return '['. $row->category_code .'] ' . $row->category_name;
        })
        ->editColumn('club_logo', function($row){
            if(!$row->club_logo) return '-';
            $url = Storage::url($row->club_logo);
            return '<a href="'.$url.'" target="_blank">
                        <img src="'. $url .'" alt="logo-klub" class="img-fluid">
                    </a>';
        })
        ->rawColumns(['action','club_logo'])
        ->make(true);
    }

    public function index(){
        $data = ClubRoleCategory::all();
        return view('pages.club.index', compact('data'));
    }
    public function store(Request $r){
        try {
            $validators = Validator::make($r->all(), [
                'club_category_id' => 'required',
                'club_code' => 'required',
                'club_name' => 'required',
                'club_lead' => 'required',
                'lead_phone' => 'required',
                'club_province' => 'required',
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
            $item->club_role_category_id = $r->club_category_id;
            $item->club_code = $r->club_code;
            $item->club_name = $r->club_name;
            $item->club_lead = $r->club_lead;
            $item->lead_phone = $r->lead_phone;
            $item->club_province = $r->club_province;
            $item->club_address = $r->club_address;
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

    // club manage
    public function dashboard(){
        return view('pages.club.dashboard');
    }
    public function indexRegistComp(){
        return view('pages.club.registrations.index');
    }
    public function showRegistComp(){
        return view('pages.club.registrations.show');
    }
    public function storeRegistComp(){
        return 'berhasil';
    }
    // END club manage
}
