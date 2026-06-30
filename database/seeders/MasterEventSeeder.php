<?php

namespace Database\Seeders;

use App\Enums\EventType;
use App\Enums\Gender;
use App\Enums\Stroke;
use App\Models\AgeGroup;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MasterEventSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('master_events')->truncate();

        $strokes = [
            'freestyle' => [
                'individual' => [
                    ['distance' => 1500, 'equipment' => null],
                    ['distance' => 800,  'equipment' => null],
                    ['distance' => 400,  'equipment' => null],
                    ['distance' => 200,  'equipment' => null],
                    ['distance' => 100,  'equipment' => null],
                    ['distance' => 50,   'equipment' => null],
                    ['distance' => 25,   'equipment' => null],
                    ['distance' => 50,   'equipment' => 'papan'],
                    ['distance' => 25,   'equipment' => 'papan'],
                    ['distance' => 50,   'equipment' => 'fins'],
                    ['distance' => 25,   'equipment' => 'fins'],
                ],
                'relay' => [
                    ['distance' => 200, 'athletes' => 4],
                    ['distance' => 100, 'athletes' => 4],
                    ['distance' => 50,  'athletes' => 4],
                ],
            ],
            'breast stroke' => [
                'individual' => [
                    ['distance' => 400, 'equipment' => null],
                    ['distance' => 200, 'equipment' => null],
                    ['distance' => 100, 'equipment' => null],
                    ['distance' => 50,  'equipment' => null],
                    ['distance' => 25,  'equipment' => null],
                    ['distance' => 50,  'equipment' => 'papan'],
                    ['distance' => 25,  'equipment' => 'papan'],
                ],
                'relay' => [],
            ],
            'butterfly stroke' => [
                'individual' => [
                    ['distance' => 400, 'equipment' => null],
                    ['distance' => 200, 'equipment' => null],
                    ['distance' => 100, 'equipment' => null],
                    ['distance' => 50,  'equipment' => null],
                    ['distance' => 25,  'equipment' => null],
                    ['distance' => 50,  'equipment' => 'papan'],
                    ['distance' => 25,  'equipment' => 'papan'],
                    ['distance' => 50,  'equipment' => 'fins'],
                    ['distance' => 25,  'equipment' => 'fins'],
                ],
                'relay' => [],
            ],
            'back stroke' => [
                'individual' => [
                    ['distance' => 400, 'equipment' => null],
                    ['distance' => 200, 'equipment' => null],
                    ['distance' => 100, 'equipment' => null],
                    ['distance' => 50,  'equipment' => null],
                    ['distance' => 25,  'equipment' => null],
                    ['distance' => 50,  'equipment' => 'papan'],
                    ['distance' => 25,  'equipment' => 'papan'],
                    ['distance' => 50,  'equipment' => 'fins'],
                    ['distance' => 25,  'equipment' => 'fins'],
                ],
                'relay' => [],
            ],
            'medley stroke' => [
                'individual' => [],
                'relay' => [
                    ['distance' => 200, 'athletes' => 4],
                    ['distance' => 100, 'athletes' => 4],
                    ['distance' => 50,  'athletes' => 4],
                ],
            ],
        ];

        $genders = ['male', 'female'];
        $relayGenders = ['male', 'female', 'mixed'];
        $ageGroups = AgeGroup::all();

        $rows = [];

        foreach ($ageGroups as $ag) {
            foreach ($strokes as $strokeValue => $events) {
                $strokeLabel = Stroke::from($strokeValue)->label();

                // Individual events
                foreach ($events['individual'] as $ev) {
                    foreach ($genders as $g) {
                        $genderLabel = Gender::from($g)->label();
                        $equipLabel = $ev['equipment'] ? ' ' . ucfirst($ev['equipment']) : '';
                        $label = "{$ev['distance']}m {$strokeLabel}{$equipLabel} {$genderLabel} {$ag->label}";

                        $rows[] = [
                            'distance'            => $ev['distance'],
                            'stroke'              => $strokeValue,
                            'gender'              => $g,
                            'age_group_id'        => $ag->id,
                            'event_type'          => EventType::individual->value,
                            'max_relay_athletes'  => null,
                            'equipment'           => $ev['equipment'],
                            'label'               => $label,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ];
                    }
                }

                // Relay events
                foreach ($events['relay'] as $ev) {
                    foreach ($relayGenders as $g) {
                        $genderLabel = $g === 'mixed' ? 'Campuran' : Gender::from($g)->label();
                        $label = "{$ev['athletes']}x{$ev['distance']}m {$strokeLabel} Estafet {$genderLabel} {$ag->label}";

                        $rows[] = [
                            'distance'            => $ev['distance'],
                            'stroke'              => $strokeValue,
                            'gender'              => $g,
                            'age_group_id'        => $ag->id,
                            'event_type'          => EventType::estafet->value,
                            'max_relay_athletes'  => $ev['athletes'],
                            'equipment'           => null,
                            'label'               => $label,
                            'created_at'          => now(),
                            'updated_at'          => now(),
                        ];
                    }
                }
            }
        }

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('master_events')->insert($chunk);
        }
    }
}
