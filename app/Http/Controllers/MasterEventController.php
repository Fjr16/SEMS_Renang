<?php

namespace App\Http\Controllers;

use App\Enums\EventType;
use App\Enums\Gender;
use App\Enums\Stroke;
use App\Models\AgeGroup;
use App\Models\MasterEvent;
use App\Traits\HasApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class MasterEventController extends Controller
{
    use HasApiResponse;

    public function data()
    {
        if (!auth()->user()->can('Master Setting.Event-List')) {
            return DataTables::of([])->make(true);
        }

        $data = MasterEvent::query()->with('ageGroup');

        return DataTables::of($data)
            ->addColumn('action', function ($row) {
                $edit = '<button class="btn btn-warning btn-sm"
                    data-id="' . $row->id . '"
                    data-distance="' . $row->distance . '"
                    data-stroke="' . $row->stroke . '"
                    data-gender="' . $row->gender . '"
                    data-age_group_id="' . $row->age_group_id . '"
                    data-event-type="' . $row->event_type . '"
                    data-equipment="' . ($row->equipment ?? '') . '"
                    data-max-relay="' . ($row->max_relay_athletes ?? '') . '"
                    onclick="edit(this)"><i class="bi bi-pencil"></i></button>';
                $dlt = '<button class="btn btn-danger btn-sm" onclick="destroy(' . $row->id . ')"><i class="bi bi-trash"></i></button>';

                if (!auth()->user()->hasAnyPermission(['Master Setting.Event-Ubah', 'Master Setting.Event-Hapus'])) return '';

                return '<div class="btn-group">
                            ' .
                    (auth()->user()->can('Master Setting.Event-Ubah') ? $edit : '')
                    .
                    (auth()->user()->can('Master Setting.Event-Hapus') ? $dlt : '')
                    . '
                        </div>';
            })
            ->editColumn('stroke', function ($row) {
                return Stroke::tryFrom($row->stroke)?->label() ?? $row->stroke;
            })
            ->editColumn('gender', function ($row) {
                if ($row->gender === 'mixed') return '<span class="badge bg-secondary text-white">Campuran</span>';
                $enum = Gender::tryFrom($row->gender);
                return $enum ? '<span class="badge ' . $enum->class() . '">' . $enum->label() . '</span>' : $row->gender;
            })
            ->editColumn('event_type', function ($row) {
                $enum = EventType::tryFrom($row->event_type);
                return $enum ? '<span class="badge ' . $enum->class() . '">' . $enum->label() . '</span>' : $row->event_type;
            })
            ->editColumn('age_group_id', function ($row) {
                return $row->ageGroup?->label ?? '-';
            })
            ->editColumn('equipment', function ($row) {
                return $row->equipment ? ucfirst($row->equipment) : '-';
            })
            ->editColumn('max_relay_athletes', function ($row) {
                return $row->max_relay_athletes ?? '-';
            })
            ->rawColumns(['action', 'gender', 'event_type'])
            ->make(true);
    }

    public function index()
    {
        $this->authorize('Master Setting.Event-List');
        $enumStroke = Stroke::cases();
        $enumGender = Gender::cases();
        $enumEType = EventType::cases();
        $ageGroups = AgeGroup::all();
        return view('pages.master_event.index', compact('enumStroke', 'enumGender', 'enumEType', 'ageGroups'));
    }

    public function store(Request $r)
    {
        if (Gate::none(['Master Setting.Event-Tambah', 'Master Setting.Event-Ubah'])) {
            return $this->unauthorized('Anda tidak memiliki akses');
        }

        $validators = Validator::make($r->all(), [
            'distance'            => 'required|integer|min:1',
            'stroke'              => 'required|string|max:50',
            'gender'              => 'required|string|max:10',
            'age_group_id'        => 'required|integer|exists:age_groups,id',
            'event_type'          => 'required|string|max:20',
            'equipment'           => 'nullable|string|max:20|in:papan,fins',
            'max_relay_athletes'  => 'nullable|integer|min:2|max:10',
            'master_event_id'     => 'nullable|integer|exists:master_events,id',
        ]);

        if ($validators->fails()) {
            return response()->json([
                'status' => false,
                'message' => substr($validators->errors()->first(), 0, 100),
            ]);
        }

        $exists = MasterEvent::where('distance', $r->distance)
            ->where('stroke', $r->stroke)
            ->where('gender', $r->gender)
            ->where('age_group_id', $r->age_group_id)
            ->where('event_type', $r->event_type)
            ->where('equipment', $r->equipment)
            ->when($r->input('master_event_id'), fn($q) => $q->where('id', '!=', $r->input('master_event_id')))
            ->exists();

        if ($exists) {
            return response()->json([
                'status' => false,
                'message' => 'Event dengan kombinasi ini sudah ada',
            ]);
        }

        try {
            $item = $r->input('master_event_id') ? MasterEvent::find($r->input('master_event_id')) : new MasterEvent;
            $item->distance = $r->distance;
            $item->stroke = $r->stroke;
            $item->gender = $r->gender;
            $item->age_group_id = $r->age_group_id;
            $item->event_type = $r->event_type;
            $item->equipment = $r->equipment ?: null;
            $item->max_relay_athletes = $r->event_type === EventType::estafet->value
                ? ($r->max_relay_athletes ?: null)
                : null;

            $strokeLabel = Stroke::tryFrom($r->stroke)?->label() ?? $r->stroke;
            $genderLabel = $r->gender === 'mixed' ? 'Campuran' : (Gender::tryFrom($r->gender)?->label() ?? $r->gender);
            $ageLabel = AgeGroup::find($r->age_group_id)?->label ?? '-';
            $equipLabel = $r->equipment ? ' ' . ucfirst($r->equipment) : '';

            if ($r->event_type === EventType::estafet->value && $item->max_relay_athletes) {
                $item->label = $item->max_relay_athletes . 'x' . $r->distance . 'm ' . $strokeLabel . $equipLabel . ' Estafet ' . $genderLabel . ' ' . $ageLabel;
            } else {
                $item->label = $r->distance . 'm ' . $strokeLabel . $equipLabel . ' ' . $genderLabel . ' ' . $ageLabel;
            }

            $item->save();

            return response()->json([
                'status' => true,
                'message' => $r->input('master_event_id') ? 'Sukses Update Data' : 'Sukses Simpan Data',
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 100) ?? 'Gagal Simpan Data',
            ]);
        }
    }

    public function destroy($id)
    {
        if (!auth()->user()->can('Master Setting.Event-Hapus')) {
            return $this->unauthorized('Anda tidak memiliki akses');
        }

        try {
            $item = MasterEvent::findOrFail($id);
            $item->delete();
            return response()->json([
                'status' => true,
                'message' => 'Sukses hapus data'
            ]);
        } catch (\Throwable $th) {
            return response()->json([
                'status' => false,
                'message' => substr($th->getMessage(), 0, 100) ?: 'Gagal Hapus data'
            ]);
        }
    }
}
