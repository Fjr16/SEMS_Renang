<?php

namespace App\Http\Controllers;

use App\Enums\EventType;
use App\Enums\Gender;
use App\Enums\Stroke;
use App\Models\AgeGroup;
use App\Models\Competition;
use App\Models\CompetitionEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CompetitionEventController extends Controller
{
    public function partialReload(Competition $competition){
        $enumStroke = Stroke::cases();
        $enumGender = Gender::cases();
        $enumEType  = EventType::cases();
        $ageGroups  = AgeGroup::all();

        return view('pages.competition.tabs.events', compact(
            'competition', 'enumStroke', 'enumGender', 'enumEType', 'ageGroups'
        ));
    }
    public function store(Request $request, Competition $competition){
        $validated = $request->validate([
            'competition_session_id' => 'required|exists:competition_sessions,id',
            'stroke'                 => 'required|string|max:50',
            'distance'               => 'required|numeric|min:1',
            'gender'                 => 'required|string|max:10',
            'age_group_id'           => 'required|exists:age_groups,id',
            'event_type'             => 'required|string|max:20',
            'max_relay_athletes'     => [
                Rule::requiredIf(fn() => $request->event_type === EventType::estafet->value),
                'nullable', 'integer', 'max:4',
            ],
            'registration_fee'       => 'required|numeric|min:0',
        ]);

        $event = $competition->events()->create($validated);
        $event->load('ageGroup');

        $sessionEvents = $competition->events()
            ->where('competition_session_id', $validated['competition_session_id'])
            ->orderBy('id')->get();
        $index = $sessionEvents->search(fn($e) => $e->id === $event->id);

        return response()->json([
            'success'    => true,
            'message'    => 'Event berhasil ditambahkan.',
            'session_id' => $event->competition_session_id,
            'row_html'   => view('pages.competition.tabs._event_row', [
                'event'       => $event,
                'sesi'        => $event->session,
                'index'       => $index,
                'competition' => $competition,
            ])->render(),
        ]);
    }
    public function edit(Competition $competition, CompetitionEvent $event)    {
        if($event->competitionSession->competition_id !== $competition->id){
            return response()->json([
                'success' => false,
                'message' => 'Hanya penyelenggara kompetisi ' . $competition?->name . ' yang dapat edit event ini.',
            ]);
        }

        return response()->json([
            'success' => true,
            'event'   => $event,
        ]);
    }
    public function update(Request $request, Competition $competition, CompetitionEvent $event){
        $validated = $request->validate([
            'competition_session_id' => 'required|exists:competition_sessions,id',
            'stroke'                 => 'required|string|max:50',
            'distance'               => 'required|numeric|min:1',
            'gender'                 => 'required|string|max:10',
            'age_group_id'           => 'required|exists:age_groups,id',
            'event_type'             => 'required|string|max:20',
            'max_relay_athletes'     => [
                Rule::requiredIf(fn() => $request->event_type === EventType::estafet->value),
                'nullable', 'integer', 'max:4',
            ],
            'registration_fee'       => 'required|numeric|min:0',
        ], [
            'max_relay_athletes.required' => 'Maks. jumlah atlet wajib diisi untuk tipe estafet'
        ]);

        if($request->event_type === EventType::individual->value) $validated['max_relay_athletes'] = null;
        $event->update($validated);
        $event->load('ageGroup');

        $sessionEvents = $competition->events()
            ->where('competition_session_id', $event->competition_session_id)
            ->orderBy('id')->get();
        $index = $sessionEvents->search(fn($e) => $e->id === $event->id);

        return response()->json([
            'success'    => true,
            'message'    => 'Event berhasil diperbarui.',
            'session_id' => $event->competition_session_id,
            'row_html'   => view('pages.competition.tabs._event_row', [
                'event'       => $event,
                'sesi'        => $event->session,
                'index'       => $index,
                'competition' => $competition,
            ])->render(),
        ]);
    }
    public function destroy(Competition $competition, CompetitionEvent $event){
        if($event->competitionSession->competition_id !== $competition->id){
            return response()->json([
                'success'    => false,
                'message'    => 'Hanya penyelenggara kompetisi ' . $competition?->name . ' yang dapat menghapus event ini.',
            ]);
        }

        $sessionId = $event->competition_session_id;
        $event->delete();

        return response()->json([
            'success'    => true,
            'message'    => 'Event berhasil dihapus.',
            'session_id' => $sessionId,
        ]);
    }
}
