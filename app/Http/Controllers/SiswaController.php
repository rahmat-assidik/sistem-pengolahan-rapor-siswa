<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function showDataSiswa(Request $request)
    {
        $query = Siswa::query();

        // Filter Search (Nama atau NIS)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Angkatan
        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $siswaData = $query->orderBy('nama_siswa')->paginate(20)->withQueryString();

        // Ambil daftar angkatan unik
        $angkatanList = Siswa::whereNotNull('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan', 'angkatan')->toArray();

        return view('pages.data_siswa', compact('siswaData', 'angkatanList'));
    }

<<<<<<< Updated upstream
    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswa,nis',
            'nama_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'angkatan' => 'required|integer',
            'status' => 'required|in:Aktif,Nonaktif,Alumni,Mutasi',
        ]);

        Siswa::create($request->only(['nis', 'nama_siswa', 'jenis_kelamin', 'angkatan', 'status']));
=======
public function store(Request $request)
{
    $request->validate([
        'nis' => 'required|unique:siswa,nis',
        'nama_siswa' => 'required|string|max:255',
        'nama_orang_tua' => 'nullable|string|max:255',
        'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
        'angkatan' => 'required|integer',
        'status' => 'required|in:Aktif,Tidak Aktif',
    ]);

    Siswa::create([
        'nis' => $request->nis,
        'nama_siswa' => $request->nama_siswa,
        'nama_orang_tua' => $request->nama_orang_tua,
        'jenis_kelamin' => $request->jenis_kelamin,
        'angkatan' => $request->angkatan,
        'status' => $request->status,
    ]);
>>>>>>> Stashed changes

    return redirect()->route('data_siswa')
        ->with('success', 'Data siswa berhasil ditambahkan.');
}

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);

        $request->validate([
            'nis' => 'required|unique:siswa,nis,' . $id . ',nis',
            'nama_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'angkatan' => 'required|integer',
            'status' => 'required|in:Aktif,Nonaktif,Alumni,Mutasi',
        ]);

        $siswa->update($request->only(['nis', 'nama_siswa', 'jenis_kelamin', 'angkatan', 'status']));

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $siswa = Siswa::findOrFail($id);

        \Illuminate\Support\Facades\DB::transaction(function() use ($id, $siswa) {
            // 1. Ambil semua ID penempatan kelas siswa ini
            $kelasSiswaIds = \App\Models\RiwayatKelasSiswa::where('nis', $id)->pluck('id');

            // 2. Hapus semua nilai yang terhubung dengan penempatan kelas tersebut
            \App\Models\Nilai::whereIn('kelas_siswa_id', $kelasSiswaIds)->delete();

            // 3. Hapus data penempatan kelas
            \App\Models\RiwayatKelasSiswa::whereIn('id', $kelasSiswaIds)->delete();

            // 4. Hapus data utama siswa
            $siswa->delete();
        });

        return redirect()->back()->with('success', 'Data siswa dan seluruh riwayat nilai berhasil dihapus.');
    }
}
