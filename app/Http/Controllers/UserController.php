<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    public function index(){
        return view('pages.users.index');
    }

    public function getDataTables(){
        $query = User::query()
                ->orderBy('name', 'asc');

        return DataTables::of($query)
        ->make(true);
    }
    public function store(Request $req){
        $validators = Validator::make($req->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|unique:users,email',
            'password' => 'required',
            'club_id' => 'nullable',
            'club_role_cateogory_id' => 'nullable',
            'is_admin' => 'required',
            'user_id' => 'sometimes|nullable|exists:users,id'
        ]);

        if($validators->fails()){
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(), 0,150),
            ]);
        }

        try {
            $item = $req->user_id ? User::find($req->user_id) : new User;
            $item->name = $req->name;
            $item->email = $req->email;
            $item->password = $req->password;
            $item->club_id = $req->club_id;
            $item->club_role_category_id = $req->club_role_category_id;
            $item->is_admin = $req->is_admin;
            $item->save();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil ' . $req->user ? 'memperbarui' : 'menambahkan' . 'user',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0,150),
            ]);
        }
    }
    public function show($id){
        $item = User::find(decrypt($id));
        return view('pages.users.show', compact('item'));
    }
    public function destroy($id){
        try {
            $item = User::findOrFail(decrypt($id));
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Sukses hapus data',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(),0,150),
            ]);
        }
    }
}
