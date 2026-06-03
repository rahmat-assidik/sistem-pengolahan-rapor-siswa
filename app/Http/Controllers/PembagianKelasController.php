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
        $semesterAktif = Semester::with('tahunAjaran')->where('is_aktif', true)->first();

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

        // Load kelas assignments for ACTIVE semester only
        foreach ($siswaData as $siswa) {
            $siswa->kelasAktif = null;
            if ($semesterAktif) {
                $siswa->kelasAktif = RiwayatKelasSiswa::with(['kelas', 'semester.tahunAjaran'])
                    ->where('nis', $siswa->nis)
                    ->where('semester_id', $semesterAktif->id)
                    ->first();
            }
        }

        // Ambil daftar angkatan unik
        $angkatanList = Siswa::whereNotNull('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan', 'angkatan')->toArray();

        // Ambil semua kelas
        $kelasData = Kelas::orderBy('nama_kelas')->get();

        return view('pages.pembagian_kelas', compact('siswaData', 'angkatanList', 'kelasData', 'semesterAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|exists:siswa,nis',
            'kode_kelas' => 'required|exists:kelas,kode_kelas',
        ]);

        $semesterAktif = Semester::where('is_aktif', true)->first();
        if (!$semesterAktif) {
            return redirect()->back()->with('error', 'Tidak ada semester aktif. Silakan setel semester aktif terlebih dahulu.');
        }

        // Check if already exists in active semester
        $exists = RiwayatKelasSiswa::where('nis', $request->nis)
            ->where('semester_id', $semesterAktif->id)
            ->first();

        if ($exists) {
            return redirect()->back()->with('error', 'Siswa sudah ditempatkan di sebuah kelas pada semester aktif.');
        }

        RiwayatKelasSiswa::create([
            'nis' => $request->nis,
            'kode_kelas' => $request->kode_kelas,
            'semester_id' => $semesterAktif->id,
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
        ]);

        // Check if updating to the same class
        if ($kelasSiswa->kode_kelas == $request->kode_kelas) {
            return redirect()->back()->with('info', 'Siswa sudah berada di kelas tersebut.');
        }

        $kelasSiswa->update([
            'kode_kelas' => $request->kode_kelas,
        ]);

        return redirect()->back()->with('success', 'Penempatan kelas berhasil diperbarui.');
    }
}
