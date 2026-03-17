{{-- resources/views/pages/competition/tabs/_event_row.blade.php --}}
{{-- Dipakai oleh Controller untuk render HTML via response()->json() --}}

@php
    if(!$event->gender || $event->gender == 'mixed'){
        $genderClass = 'badge bg-secondary text-white';
        $genderLabel = ($event->gender == 'mixed' ? 'Campuran' : $event->gender);
    }else{
        $genderClass = 'badge ' . \App\Enums\Gender::from($event->gender)->class();
        $genderLabel = \App\Enums\Gender::from($event->gender)->label();
    }
    if($event->stroke){
        $strokeLabel = App\Enums\Stroke::from($event->stroke)->label();
    }else{
        $strokeLabel = '-';
    }

        if($event->event_type){
        $eTypeClass = 'badge ' .\App\Enums\EventType::from($event->event_type)->class();
        $eTypeLabel = \App\Enums\EventType::from($event->event_type)->label();
    }else{
        $eTypeClass = '';
        $eTypeLabel = '-';
    }
@endphp

<tr class="event-row border-bottom"
    style="border-color:#f8fafc!important"
    data-id="{{ $event->id }}"
    data-nomor="{{ $event->event_number }}"
    data-gaya="{{ \App\Enums\Stroke::from($event->stroke)->label() }}"
    data-kelamin="{{ $event->gender }}"
    data-tipe="{{ $event->event_type }}"
    data-session="{{ strtolower($sesi->name ?? '') }}">
    <td class="px-4 text-muted" style="font-family:monospace;font-size:12px">{{ $index + 1 }}</td>
    <td class="px-4">
        <span style="font-family:monospace;font-weight:700;color:#4f46e5;font-size:15px">
            {{ $event->event_number }}
        </span>
    </td>
    <td class="px-4 fw-medium text-dark">{{ $strokeLabel }}</td>
    <td class="px-4" style="font-family:monospace;color:#6b7280">{{ $event->distance }} m</td>
    <td class="px-4"><span class="{{ $genderClass }}">{{ $genderLabel }}</span></td>
    <td class="px-4 text-muted" style="font-size:12px">{{ $event->ageGroup?->label ?? '-' }}</td>
    <td class="px-4"><span class="{{ $eTypeClass }}">{{ $eTypeLabel }}</span></td>
    <td class="px-4 text-center" style="font-family:monospace;color:#6b7280;font-weight:600">
        {{ $event->max_relay_athletes ?? '-' }}
    </td>
    <td class="px-4 text-end" style="font-family:monospace;font-weight:600;color:#059669;white-space:nowrap">
        Rp {{ $event->registration_fee ? number_format($event->registration_fee, 0, ',', '.') : '-' }}
    </td>
    <td class="px-4 text-center">
        <div class="d-flex justify-content-center gap-1">
            <button class="btn btn-warning btn-sm" onclick="editEvent({{ $event->id }})">
                <i class="bi bi-pencil" style="font-size:13px"></i>
            </button>
            <button class="btn btn-danger btn-sm"
                onclick="confirmDeleteEvent({{ $event->id }}, '{{ $event->event_number }}')">
                <i class="bi bi-trash" style="font-size:13px"></i>
            </button>
        </div>
    </td>
</tr>
