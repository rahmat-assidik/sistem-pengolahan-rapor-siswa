<?php

namespace App\Imports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SiswaImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Siswa([
            'nis'            => $row['nis'],
            'nama_siswa'     => $row['nama_siswa'],
            'nama_orang_tua' => $row['nama_orang_tua'],
            'jenis_kelamin'  => $row['jenis_kelamin'],
            'angkatan'       => $row['angkatan'],
            'status'         => $row['status'],
        ]);
    }
}
