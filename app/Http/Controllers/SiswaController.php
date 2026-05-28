<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;

class SiswaController extends Controller
{
    public function showDataSiswa(Request $request)
    {
        $semesterAktif = Semester::where('is_aktif', true)->first();

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

        // Filter Periode Akademik (Kelas, Tahun Ajaran, Semester)
        $query->whereHas('kelasSiswa', function($q) use ($request, $semesterAktif) {
            // Filter Kelas
            if ($request->filled('kelas_id')) {
                $q->where('kelas_id', $request->kelas_id);
            }

            // Filter Tahun Ajaran
            if ($request->filled('tahun_ajaran_id')) {
                $q->whereHas('semester', function($sq) use ($request) {
                    $sq->where('tahun_ajaran_id', $request->tahun_ajaran_id);
                });
            }

            // Filter Semester (Ganjil/Genap)
            if ($request->filled('semester')) {
                $q->whereHas('semester', function($sq) use ($request) {
                    $sq->where('semester', $request->semester);
                });
            }

            // Default: Jika tidak ada filter periode sama sekali, tampilkan yang aktif saat ini
            if (!$request->filled('tahun_ajaran_id') && !$request->filled('semester') && !$request->filled('kelas_id') && $semesterAktif) {
                $q->where('semester_id', $semesterAktif->id);
            }
        });

        $siswaData = $query->with(['kelasSiswa' => function ($q) use ($request, $semesterAktif) {
            // Filter yang tampil di tabel harus sama dengan filter pencarian
            if ($request->filled('kelas_id')) {
                $q->where('kelas_id', $request->kelas_id);
            }

            if ($request->filled('tahun_ajaran_id')) {
                $q->whereHas('semester', function($sq) use ($request) {
                    $sq->where('tahun_ajaran_id', $request->tahun_ajaran_id);
                });
            }

            if ($request->filled('semester')) {
                $q->whereHas('semester', function($sq) use ($request) {
                    $sq->where('semester', $request->semester);
                });
            }

            // Default jika tidak ada filter
            if (!$request->filled('tahun_ajaran_id') && !$request->filled('semester') && !$request->filled('kelas_id') && $semesterAktif) {
                $q->where('semester_id', $semesterAktif->id);
            }

            $q->with(['kelas', 'semester.tahunAjaran']);
        }])->orderBy('nama_siswa')->paginate(20)->withQueryString();

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $tahunAjaranList = TahunAjaran::orderBy('nama', 'desc')->get();
        
        // Ambil daftar angkatan unik
        $angkatanList = Siswa::whereNotNull('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan', 'angkatan')->toArray();

        return view('pages.data_siswa', compact('siswaData', 'kelasList', 'tahunAjaranList', 'angkatanList', 'semesterAktif'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|unique:siswa,nis',
            'nama_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'angkatan' => 'required|integer',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        $semesterAktif = Semester::where('is_aktif', true)->first();

        if (!$semesterAktif) {
            return redirect()->back()->with('error', 'Tidak ada semester aktif. Harap aktifkan semester terlebih dahulu di menu Akademik.');
        }

        \Illuminate\Support\Facades\DB::transaction(function() use ($request, $semesterAktif) {
            $siswa = Siswa::create($request->only(['nis', 'nama_siswa', 'jenis_kelamin', 'angkatan', 'status']));

            // Hubungkan ke kelas di semester aktif
            $kelas = Kelas::findOrFail($request->kelas_id);
            \App\Models\RiwayatKelasSiswa::create([
                'nis' => $siswa->nis,
                'kode_kelas' => $kelas->kode_kelas,
                'semester_id' => $semesterAktif->id
            ]);
        });

        return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $siswa = Siswa::findOrFail($id);
        $semesterAktif = Semester::where('is_aktif', true)->first();
        
        $request->validate([
            'nis' => 'required|unique:siswa,nis,' . $id,
            'nama_siswa' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'angkatan' => 'required|integer',
            'status' => 'required|in:Aktif,Tidak Aktif',
            'kelas_id' => 'nullable|exists:kelas,id'
        ]);

        \Illuminate\Support\Facades\DB::transaction(function() use ($siswa, $request, $semesterAktif) {
            $siswa->update($request->only(['nis', 'nama_siswa', 'jenis_kelamin', 'angkatan', 'status']));

            // Update penempatan kelas jika ada semester aktif
            if ($request->filled('kelas_id') && $semesterAktif) {
                $kelas = Kelas::findOrFail($request->kelas_id);
                \App\Models\RiwayatKelasSiswa::updateOrCreate(
                    ['nis' => $siswa->nis, 'semester_id' => $semesterAktif->id],
                    ['kode_kelas' => $kelas->kode_kelas]
                );
            }
        });

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
