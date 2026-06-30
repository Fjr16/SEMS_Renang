<?php

namespace App\Models;

use App\Enums\EventType;
use App\Enums\Gender;
use App\Enums\Stroke;
use Illuminate\Database\Eloquent\Model;

class MasterEvent extends Model
{
    protected $fillable = [
        'distance',
        'stroke',
        'gender',
        'age_group_id',
        'event_type',
        'max_relay_athletes',
        'equipment',
        'label',
    ];

    public function ageGroup()
    {
        return $this->belongsTo(AgeGroup::class);
    }

    public function getDisplayLabelAttribute()
    {
        $parts = [];

        if ($this->event_type === EventType::estafet->value && $this->max_relay_athletes) {
            $parts[] = $this->max_relay_athletes . 'x' . $this->distance . 'm';
        } else {
            $parts[] = $this->distance . 'm';
        }

        $parts[] = Stroke::tryFrom($this->stroke)?->label() ?? $this->stroke;

        if ($this->equipment) {
            $parts[] = ucfirst($this->equipment);
        }

        if ($this->event_type === EventType::estafet->value) {
            $parts[] = 'Estafet';
        }

        $genderLabel = $this->gender === 'mixed' ? 'Campuran' : (Gender::tryFrom($this->gender)?->label() ?? $this->gender);
        $parts[] = $genderLabel;

        $parts[] = $this->ageGroup?->label ?? '-';

        return implode(' ', $parts);
    }
}
