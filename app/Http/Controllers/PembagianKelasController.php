<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\RiwayatKelasSiswa;
use Illuminate\Http\Request;

class PembagianKelasController extends Controller
{
    public function showPembagianKelas(Request $request)
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

        // Load kelas assignments
        foreach ($siswaData as $siswa) {
            $siswa->kelasSiswa = RiwayatKelasSiswa::with(['kelas', 'semester.tahunAjaran'])
                ->where('nis', $siswa->nis)
                ->get();
        }

        // Ambil daftar angkatan unik
        $angkatanList = Siswa::whereNotNull('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan', 'angkatan')->toArray();

        // Ambil semua kelas
        $kelasData = Kelas::orderBy('nama_kelas')->get();

        // Ambil semua semester aktif
        $semesterData = Semester::with('tahunAjaran')->where('is_aktif', true)->get();

        return view('pages.pembagian_kelas', compact('siswaData', 'angkatanList', 'kelasData', 'semesterData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|exists:siswa,nis',
            'kode_kelas' => 'required|exists:kelas,kode_kelas',
            'semester_id' => 'required|exists:semester,id',
        ]);

        // Check if already exists
        $exists = RiwayatKelasSiswa::where('nis', $request->nis)
            ->where('kode_kelas', $request->kode_kelas)
            ->where('semester_id', $request->semester_id)
            ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'Siswa sudah ditambahkan ke kelas ini untuk semester tersebut.');
        }

        RiwayatKelasSiswa::create([
            'nis' => $request->nis,
            'kode_kelas' => $request->kode_kelas,
            'semester_id' => $request->semester_id,
            'status' => 'Aktif',
        ]);

        return redirect()->back()->with('success', 'Siswa berhasil ditambahkan ke kelas.');
    }

    public function destroy($id)
    {
        $kelasSiswa = RiwayatKelasSiswa::findOrFail($id);
        $kelasSiswa->delete();

        return redirect()->back()->with('success', 'Penempatan kelas berhasil dihapus.');
    }

    public function update(Request $request, $id)
    {
        $kelasSiswa = RiwayatKelasSiswa::findOrFail($id);

        $request->validate([
            'kode_kelas' => 'required|exists:kelas,kode_kelas',
            'semester_id' => 'required|exists:semester,id',
        ]);

        // Check if new combination already exists
        $exists = RiwayatKelasSiswa::where('nis', $kelasSiswa->nis)
            ->where('kode_kelas', $request->kode_kelas)
            ->where('semester_id', $request->semester_id)
            ->where('id', '!=', $id)
            ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'Kombinasi siswa, kelas, dan semester ini sudah ada.');
        }

        $kelasSiswa->update([
            'kode_kelas' => $request->kode_kelas,
            'semester_id' => $request->semester_id,
        ]);

        return redirect()->back()->with('success', 'Penempatan kelas berhasil diperbarui.');
    }
}
