<?php

namespace App\Imports;

use App\Models\Athlete;
use Maatwebsite\Excel\Concerns\ToModel;

class AthleteImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */

    public $club_id;

    public function __construct($clubId)
    {
        $this->club_id = $clubId;
    }

    public function model(array $row)
    {
        return new Athlete([
            'club_id' => $this->club_id,
            'code' => $row[0],
            'foto' => $row[0],
            'name' => $row[0],
            'bod' => $row[0],
            'gender' => $row[0],
            'registration_number' => $row[0],
            'status' => $row[0],
            'kota' => $row[0],
            'provinsi' => $row[0],
        ]);
    }
}
