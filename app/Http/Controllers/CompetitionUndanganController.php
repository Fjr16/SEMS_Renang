<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Traits\HasApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CompetitionUndanganController extends Controller
{
    use HasApiResponse;

    public function partialReload(Competition $competition)
    {
        return view('pages.competition.tabs.undangan', compact('competition'));
    }

    public function store(Competition $competition, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|file|mimetypes:application/pdf|max:5120',
        ], [
            'file.required' => 'File PDF wajib diunggah',
            'file.mimes'    => 'File harus berformat PDF',
            'file.max'      => 'Ukuran file maksimal 10MB',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => $validator->errors()->first(),
            ]);
        }

        try {
            DB::beginTransaction();

            if ($competition->undangan_path && Storage::disk('public')->exists($competition->undangan_path)) {
                Storage::disk('public')->delete($competition->undangan_path);
            }

            $file = $request->file('file');
            $path = $file->store('competition/undangan', 'public');

            $competition->update(['undangan_path' => $path]);

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => 'Berhasil mengunggah file undangan',
            ]);
        } catch (\Throwable $th) {
            DB::rollBack();
            return response()->json([
                'status'  => false,
                'message' => substr($th->getMessage(), 0, 100) ?? 'Gagal mengunggah file',
            ]);
        }
    }
}
