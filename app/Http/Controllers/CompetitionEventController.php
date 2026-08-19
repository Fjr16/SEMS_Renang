<?php

namespace App\Http\Controllers;

use App\Enums\EventType;
use App\Enums\Gender;
use App\Enums\Stroke;
use App\Models\AgeGroup;
use App\Models\Competition;
use App\Models\CompetitionEvent;
use App\Models\CompetitionSession;
use App\Models\MasterEvent;
use Illuminate\Http\Request;

class CompetitionEventController extends Controller
{
    public function partialReload(Competition $competition){
        $enumStroke = Stroke::cases();
        $enumGender = Gender::cases();
        $enumEType  = EventType::cases();
        $ageGroups  = AgeGroup::all();
        $masterEvents = MasterEvent::with('ageGroup')->orderBy('event_type')->orderBy('stroke')->orderBy('distance')->orderBy('gender')->get();

        return view('pages.competition.tabs.events', compact(
            'competition', 'enumStroke', 'enumGender', 'enumEType', 'ageGroups', 'masterEvents'
        ));
    }
    public function store(Request $request, Competition $competition){
        $validated = $request->validate([
            'competition_session_id' => 'required|exists:competition_sessions,id',
            'master_event_id'        => 'required|exists:master_events,id',
            'registration_fee'       => 'required|numeric|min:0',
            'event_number'           => 'required|digits_between:3,5',
            'limit_waktu'           => 'nullable|regex:/^\d{2}:\d{2}\.\d{2}$/'
        ]);

        $masterEvent = MasterEvent::findOrFail($validated['master_event_id']);

        $session_date = CompetitionSession::where('id', $request->competition_session_id)->value('session_date');
        $sessionIdsOnSameDay = CompetitionSession::where('competition_id', $competition->id)
            ->where('session_date', $session_date)
            ->pluck('id');

        $exists = CompetitionEvent::whereIn('competition_session_id', $sessionIdsOnSameDay)
            ->where('distance', $masterEvent->distance)
            ->where('stroke', $masterEvent->stroke)
            ->where('age_group_id', $masterEvent->age_group_id)
            ->where('gender', $masterEvent->gender)
            ->where('event_type', $masterEvent->event_type)
            ->where('equipment', $masterEvent->equipment)
            ->exists();

        if ($exists) {
            return response()->json([
                'success'    => false,
                'message'    => 'Event dengan kombinasi ini sudah ada di sesi yang sama.'
            ]);
        }

        $eventData = [
            'event_number'           => $validated['event_number'],
            'competition_session_id' => $validated['competition_session_id'],
            'distance'               => $masterEvent->distance,
            'stroke'                 => $masterEvent->stroke,
            'gender'                 => $masterEvent->gender,
            'age_group_id'           => $masterEvent->age_group_id,
            'event_type'             => $masterEvent->event_type,
            'max_relay_athletes'     => $masterEvent->max_relay_athletes,
            'equipment'              => $masterEvent->equipment,
            'registration_fee'       => $validated['registration_fee'],
        ];

        if (!empty($validated['limit_waktu'])) {
            $eventData['limit_waktu'] = $validated['limit_waktu'];
        }

        $event = $competition->events()->create($eventData);
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
                'sesi'        => $event->competitionSession,
                'index'       => $index,
                'competition' => $competition,
            ])->render(),
        ]);
    }
    public function edit(Competition $competition, CompetitionEvent $event){
        if($event->competitionSession->competition_id !== $competition->id){
            return response()->json([
                'success' => false,
                'message' => 'Hanya penyelenggara kompetisi ' . $competition?->name . ' yang dapat edit event ini.',
            ]);
        }

        $masterEvent = MasterEvent::where('distance', $event->distance)
            ->where('stroke', $event->stroke)
            ->where('gender', $event->gender)
            ->where('age_group_id', $event->age_group_id)
            ->where('event_type', $event->event_type)
            ->where('equipment', $event->equipment)
            ->first();

        return response()->json([
            'success'         => true,
            'event'           => $event,
            'event_number'     => $event->event_number,
            'master_event_id' => $masterEvent?->id,
        ]);
    }
    public function update(Request $request, Competition $competition, CompetitionEvent $event){
        $validated = $request->validate([
            'competition_session_id' => 'required|exists:competition_sessions,id',
            'master_event_id'        => 'required|exists:master_events,id',
            'registration_fee'       => 'required|numeric|min:0',
            'event_number'           => 'required|digits_between:3,5',
            'limit_waktu'           => 'nullable|regex:/^\d{2}:\d{2}\.\d{2}$/'
        ]);

        $masterEvent = MasterEvent::findOrFail($validated['master_event_id']);

        $sessionIdsOnSameDay = CompetitionSession::where('competition_id', $competition->id)
            ->where('session_date', $event->competitionSession->session_date)
            ->pluck('id');

        $exists = CompetitionEvent::where('id', '!=', $event->id)
            ->whereIn('competition_session_id', $sessionIdsOnSameDay)
            ->where('distance', $masterEvent->distance)
            ->where('stroke', $masterEvent->stroke)
            ->where('age_group_id', $masterEvent->age_group_id)
            ->where('gender', $masterEvent->gender)
            ->where('event_type', $masterEvent->event_type)
            ->where('equipment', $masterEvent->equipment)
            ->exists();

        if ($exists) {
            return response()->json([
                'success'    => false,
                'message'    => 'Event dengan kombinasi ini sudah ada di sesi yang sama.'
            ]);
        }

        $eventData = [
            'event_number' => $validated['event_number'],
            'competition_session_id' => $validated['competition_session_id'],
            'distance'               => $masterEvent->distance,
            'stroke'                 => $masterEvent->stroke,
            'gender'                 => $masterEvent->gender,
            'age_group_id'           => $masterEvent->age_group_id,
            'event_type'             => $masterEvent->event_type,
            'max_relay_athletes'     => $masterEvent->max_relay_athletes,
            'equipment'              => $masterEvent->equipment,
            'registration_fee'       => $validated['registration_fee'],
        ];

        if (!empty($validated['limit_waktu'])) {
            $eventData['limit_waktu'] = $validated['limit_waktu'];
        } else {
            $eventData['limit_waktu'] = 'NO LIMIT';
        }

        $event->update($eventData);
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
                'sesi'        => $event->competitionSession,
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
