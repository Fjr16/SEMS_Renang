<?php

namespace App\Http\Controllers;

use App\Enums\CompetitionTeamEntryStatus;
use App\Enums\CompetitionTeamPaymentStatus;
use App\Enums\CompetitionTeamStatus;
use App\Models\Club;
use App\Models\Competition;
use App\Models\CompetitionEntry;
use App\Models\CompetitionTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;

class CompetitionTabEntriesController extends Controller
{
    public function partialReload(Competition $competition, Request $req){
        $stts = $req->query('status');
        $club_id = $req->query('club_id');
        $club = null;
        if ($club_id) {
            $club = Club::find($club_id);
        }
        $competitionTeams = CompetitionTeam::query()
        ->where('competition_id', $competition->id)
        ->when($stts, function($q) use ($stts){
            $q->where('status', $stts);
        })
        ->when($club_id, function($q) use ($club_id){
            $q->where('team_id', $club_id);
        })
        ->with([
            'team',
            'competitionTeamOfficials:id,competition_team_id,official_id,role_override',
            'competitionEntries' => function($q){
                $q->select(
                    'id','competition_team_id',
                    'athlete_id','competition_event_id',
                    'is_relay','entry_time',
                    'seed_time','status'
                )
                ->with([
                    'athlete',
                    'competitionEvent'
                ]);
            }
        ])
        ->get();

        return view('pages.competition.tabs.entries', compact(
            'competition',
            'club',
            'stts',
            'competitionTeams'
        ));
    }

    public function verification(Request $req){
        $validators = Validator::make($req->all(), [
            'competition_team_id' => 'required|exists:competition_teams,id',
            'notes' => 'nullable',
            'payment_status' => ['nullable', new Enum(CompetitionTeamPaymentStatus::class)],
            'status' => ['required', new Enum(CompetitionTeamStatus::class)]
        ], [
            'competition_team_id.required' => 'Data registrasi entry tidak ditemukan',
            'competition_team_id.exists' => 'Data registrasi entry tidak ditemukan',
            'status.required' => 'Status tidak valid'
        ]);

        if($validators->fails()){
            return response()->json([
                'status' => false,
                'message' => $validators->errors()->first()
            ]);
        }

        try {
            DB::transaction(function() use ($req){
                $item = CompetitionTeam::find($req->competition_team_id);
                if($req->status === CompetitionTeamStatus::Rejected->value){
                    $item->update([
                        'notes' => $req->notes,
                        'status' => $req->status
                    ]);

                    $item->competitionEntries()->update([
                        'status' => CompetitionTeamEntryStatus::Pending->value
                    ]);
                }
                if($req->status === CompetitionTeamStatus::Active->value){
                    $item->update([
                        'status' => $req->status,
                        'payment_status' => $req->payment_status,
                        'notes' => null
                    ]);

                    $item->competitionEntries()->update([
                        'status' => CompetitionTeamEntryStatus::Active->value
                    ]);
                }
                if($req->status === CompetitionTeamStatus::Withdrawn->value){
                    $item->update([
                        'status' => $req->status,
                        'notes' => null
                    ]);

                    $item->competitionEntries()->update([
                        'status' => CompetitionTeamEntryStatus::Withdrawn->value
                    ]);
                }
                if($req->status === CompetitionTeamStatus::Disqualified->value){
                    $item->update([
                        'status' => $req->status,
                        'notes' => null
                    ]);

                    $item->competitionEntries()->update([
                        'status' => CompetitionTeamEntryStatus::Disqualified->value
                    ]);
                }
            });

            return response()->json([
                'status' => true,
                'message' => 'Entry berhasil ' . ($req->status ? (CompetitionTeamEntryStatus::tryFrom($req->status)?->label() ?? 'diperbarui') : 'diperbarui'),
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Terjadi Kesalahan, Proses verifikasi gagal !!',
                'errors' => $th->getMessage()
            ]);
        }

    }

    public function updateSeedTime(Request $req){
        $validators = Validator::make($req->all(), [
            'competition_entry_id' => 'required|exists:competition_entries,id',
            'seed_time' => ['nullable', 'regex:/^\d{2}:\d{2}\.\d{2}$/']
        ],[
            'competition_entry_id.required' => 'Entry harus diisi',
            'competition_entry_id.exists' => 'Entry tidak ditemukan',
            'seed_time.regex' => 'Format seed time tidak valid',
        ]);

        if($validators->fails()){
            return response()->json([
                'status' => false,
                'message' => $validators->errors()->first()
            ]);
        }

        try {
            $item = CompetitionEntry::find($req->competition_entry_id);
            $item->seed_time = $req->seed_time;
            $item->save();

            return response()->json([
                'status' => true,
                'message' => 'Berhasil Simpan seed time'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 150)
            ]);
        }

    }

    public function destroyEntry($competition, $id){
        try {
            $item = CompetitionEntry::findOrFail($id);
            $item->delete();

            return response()->json([
                'status' => true,
                'message' => 'Entry berhasil dihapus'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal ! ' . substr($th->getMessage(),0,150)
            ]);
        }
    }
    public function updateStatusEntry(Request $req){
        $validators = Validator::make($req->all(), [
            'competition_entry_id' => 'required|exists:competition_entries,id',
            'status' => ['required', new Enum(CompetitionTeamEntryStatus::class)]
        ], [
            'competition_entry_id.required' => 'Data entry tidak valid',
            'competition_entry_id.exists' => 'Data entry tidak ditemukan',
            'status.required' => 'Status tidak valid'
        ]);

        if($validators->fails()){
            return response()->json([
                'status' => false,
                'message' => $validators->errors()->first()
            ]);
        }
        try {
            $item = CompetitionEntry::findOrFail($req->competition_entry_id);
            $item->status = $req->status;
            $item->save();

            return response()->json([
                'status' => true,
                'message' => 'Entry berhasil ' . CompetitionTeamEntryStatus::from($req->status)->label()
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => 'Gagal ! ' . substr($th->getMessage(),0,150)
            ]);
        }
    }
}
