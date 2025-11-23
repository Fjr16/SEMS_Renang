<?php

namespace App\Http\Controllers;

use App\Models\Athlete;
use App\Models\Club;
use Illuminate\Http\Request;

class OtherController extends Controller
{
    public function getClubByCategory(Request $r){
        $categoryId = $r->input('category_id');
        $keyword = $r->input('q', '');
        $page = $r->input('page', 1);
        $perPage = 10;

        $query = Club::query()
        ->when($categoryId, function($q) use ($categoryId){
            $q->where('club_role_category_id', $categoryId);
        })
        ->when($keyword != '', function($q) use ($keyword){
            $q->where(function($qq) use ($keyword){
                $qq->where('club_name', 'like', "%{$keyword}%")
                   ->orWhere('club_code', 'like', "%{$keyword}%");
            });
        })
        ->orderBy('club_name', 'asc');

        $paginated = $query->simplePaginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $paginated->items(),
            'pagination' => [
                'more' => $paginated->hasMorePages(),
            ],
        ]);
    }
    public function findAtletById($id){
        try {
            $item = Athlete::findOrFail($id);
            $categoryId = null;
            $club = null;
            if($item && $item->club_id){
                $categoryId = $item->club->club_role_category_id;
                $club = $item->club;
            }
            return response()->json([
                'status' => true,
                'data' => [
                    'athlete' => $item,
                    'category_id' => $categoryId ?? null,
                    'club' => $club ?? null
                ]
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'data' => [],
                'message' => substr($th->getMessage(), 0,100) || 'Data tidak ditemukan'
            ]);
        }
    }
}
