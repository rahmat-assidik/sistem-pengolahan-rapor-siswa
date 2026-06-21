<?php

namespace App\Imports;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class GuruImport implements OnEachRow, WithHeadingRow
{
    /**
    * @param Row $row
    */
    public function onRow(Row $row)
    {
        $data = $row->toArray();

        $guru = Guru::updateOrCreate(
            ['nip' => $data['nip']],
            [
                'nama_guru'     => $data['nama_guru'],
                'email'         => $data['email'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'no_hp'         => $data['no_hp'],
                'status'        => $data['status'],
            ]
        );

        // Otomatis buat atau update akun guru
        User::updateOrCreate(
            ['guru_id' => $guru->nip],
            [
                'username' => $guru->nip,
                'nama'     => $guru->nama_guru,
                'email'    => $guru->email,
                'password' => Hash::make($guru->nip),
                'role'     => 'guru',
            ]
        );
    }
}
