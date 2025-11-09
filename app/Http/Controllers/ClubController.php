<?php

namespace App\Http\Controllers;

use App\Models\Club;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ClubController extends Controller
{
    public function data(){
        $data = Club::
        select(
            'clubs.*',
            'category.*'
        )
        ->leftjoin('club_role_categories as category', 'clubs.club_role_category_id', '=', 'category.id');
        return DataTables::of($data)
        ->addColumn('action', function($row){
            $edit = '<button class="btn btn-warning btn-sm" onclick="edit('.$row->id.')">Edit</button>';
            $dlt = '<button class="btn btn-danger btn-sm" onclick="deleter('.$row->id.')">Hapus</button>';
            return $edit . $dlt;
        })
        ->editColumn('club_role_category_id', function($row){
            return 
        })
        ->make(true);
    }

    public function index(){
        return view('pages.club.index');        
    }
    public function store(Request $r){

    }
    public function destroy($id){

    }
}
