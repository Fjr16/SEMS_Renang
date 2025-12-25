<?php

namespace App\Http\Controllers;

use App\Helpers\CodeGenerator;
use App\Models\Pool;
use App\Models\Venue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class VenuesAndPoolController extends Controller
{
    public function index()
    {
        return view('pages.venue_pool.index');
    }
    public function venueData(){
        $venues = Venue::query()
        ->orderBy('created_at','desc');

        return DataTables::of($venues)
        ->make(true);
    }
    public function poolData(){
        $venue_id = request()->get('venue_id');
        if(!$venue_id){
            return DataTables::of(collect([]))->make(true);
        }
        $pools = Pool::query()
        ->where('venue_id', $venue_id)
        ->orderBy('created_at','desc');

        return DataTables::of($pools)
        ->addColumn('action',function($row){
            $edit = '<button class="btn btn-sm btn-primary btn-edit-pool" onclick="editPool(this)"><i class="bi bi-pencil-square"></i></button>';
            $delete = '<button class="btn btn-sm btn-danger btn-delete-pool" data-id="'.$row->id.'" onclick="destroyPool(this)"><i class="bi bi-trash"></i></button>';
            return $edit.' '.$delete;
        })
        ->make(true);
    }
    public function storeVenue(Request $req){
        $validators = Validator::make($req->all(), [
            'name' => 'required|string',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'province' => 'nullable|string|max:100',
            'country' => 'required|string|max:100',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
            'venue_id' => 'sometimes|nullable|exists:venues,id',
        ]);
        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'messages' => substr($validators->errors()->first(), 0, 150),
            ]);
        }

        try {
            $getCode = CodeGenerator::makeVenueBaseCode($req->name, $req->city);
            $isExists = Venue::where('code', $getCode)
                ->when($req->venue_id, function($query) use ($req){
                    return $query->where('id', '!=', $req->venue_id);
                })
            ->exists();
            if ($isExists) {
                return response()->json([
                    'status' => false,
                    'messages' => 'Lokasi atau venue dengan nama dan kota yang sama telah ada.',
                ]);
            }
            $item = $req->venue_id ? Venue::findOrFail($req->venue_id) : new Venue;
            $item->code = $getCode;
            $item->name = $req->name;
            $item->address = $req->address;
            $item->city = $req->city;
            $item->province = $req->province;
            $item->country = $req->country;
            $item->notes = $req->notes ?? null;
            $item->is_active = $req->is_active ?? true;
            $item->save();

            return response()->json([
                'status' => true,
                'messages' => 'Sukses menambah / memperbarui data venue.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'messages' => substr($th->getMessage(), 0, 150),
            ]);
        }

    }
    public function destroyVenue($id){
        try {
            $item = Venue::findOrFail($id);
            $item->delete();
            return response()->json([
                'status' => true,
                'messages' => 'Sukses menghapus data venue beserta kolam terkait.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'messages' => substr($th->getMessage(), 0, 150),
            ]);
        }
    }
    public function storePool(Request $req){
        $validators = Validator::make($req->all(), [
            'venue_id' => 'required|exists:venues,id',
            'name' => 'required|string',
            'pool_role' => 'required|string|max:100',
            'course_type' => 'required|in:SCM,LCM,SCY',
            'length_meter' => 'required|integer|min:1',
            'total_lanes' => 'required|integer|max:12|min:1',
            'is_available' => 'boolean',
            'notes' => 'nullable|string',
            'pool_id' => 'sometimes|nullable|exists:pools,id',
        ]);

        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'messages' => substr($validators->errors()->first(), 0, 150),
            ]);
        }

        try {
            $venue = Venue::findOrFail($req->venue_id);
            $baseCode = CodeGenerator::makePoolBaseCode(
                $venue->code,
                $req->pool_role,
                $req->course_type,
                $req->total_lanes
            );

            // lock key granular per venue + baseCode
            $lockKey = "pool_code:{$venue->id}:" . strtoupper($baseCode);

            $result = DB::transaction(function () use ($req, $venue, $baseCode, $lockKey) {

                // tunggu max 5 detik untuk dapat lock
                $row = DB::selectOne("SELECT GET_LOCK(?, 5) AS l", [$lockKey]);
                if ((int)($row->l ?? 0) !== 1) {
                    return [
                        'ok' => false,
                        'status_code' => 409,
                        'payload' => [
                            'status' => false,
                            'messages' => 'Sedang ada proses pembuatan kode kolam, coba beberapa saat lagi.',
                        ],
                    ];
                }
    
                try {
                    // cek eksistensi candidate code dalam venue (exclude pool_id saat update)
                    $existsFn = function (string $code) use ($req, $venue) {
                        return Pool::where('venue_id', $venue->id)
                            ->where('code', $code)
                            ->when($req->pool_id, fn($q) => $q->where('id', '!=', $req->pool_id))
                            ->exists();
                    };
    
                    $finalCode = CodeGenerator::uniqueCode($baseCode, $existsFn);
    
                    $item = $req->pool_id ? Pool::findOrFail($req->pool_id) : new Pool();
                    $item->code         = $finalCode;
                    $item->venue_id     = $req->venue_id;
                    $item->name         = $req->name;
                    $item->pool_role    = $req->pool_role;
                    $item->course_type  = $req->course_type;
                    $item->length_meter = (int) $req->length_meter;
                    $item->total_lanes  = (int) $req->total_lanes;
                    $item->is_available = $req->has('is_available') ? (bool) $req->is_available : true;
                    $item->notes        = $req->notes ?? null;
                    $item->save();
    
                    return [
                        'ok' => true,
                        'status_code' => $req->pool_id ? 200 : 201,
                        'payload' => [
                            'status' => true,
                            'messages' => $req->pool_id ? 'Berhasil update kolam.' : 'Berhasil tambah kolam.',
                            'data' => $item->fresh(),
                        ],
                    ];
                } finally {
                    // pastikan lock dilepas
                    DB::selectOne("SELECT RELEASE_LOCK(?)", [$lockKey]);
                }
            }, 3);

            return response()->json($result['payload']);

        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'messages' => substr($th->getMessage(), 0, 150),
            ]);
        }
    }
    public function destroyPool($id){
        try {
            $item = Pool::findOrFail($id);
            $item->delete();
            return response()->json([
                'status' => true,
                'messages' => 'Sukses menghapus data kolam.',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'messages' => substr($th->getMessage(), 0, 150),
            ]);
        }
    }
}
