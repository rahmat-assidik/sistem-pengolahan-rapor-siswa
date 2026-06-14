<?php

namespace App\Http\Controllers;

use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\RiwayatKelasSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PembagianKelasController extends Controller
{
    public function showPembagianKelas(Request $request)
    {
        $semesterAktif = Semester::with('tahunAjaran')->where('is_aktif', true)->first();

        $query = Kelas::query();

        if ($request->filled('search')) {
            $query->where('nama_kelas', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_kelas', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        $kelasData = $query->withCount(['kelasSiswa' => function($q) use ($semesterAktif) {
            if ($semesterAktif) {
                $q->where('semester_id', $semesterAktif->id);
            }
        }])->orderBy('nama_kelas')->paginate(20)->withQueryString();

        return view('pages.pembagian_kelas', compact('kelasData', 'semesterAktif'));
    }

    public function showSetWaliKelas(Request $request)
    {
        $semesterAktif = Semester::with('tahunAjaran')->where('is_aktif', true)->first();
        
        $kelasData = Kelas::with(['waliKelas' => function($q) use ($semesterAktif) {
            if ($semesterAktif) {
                $q->where('semester_id', $semesterAktif->id);
            }
        }, 'waliKelas.guru'])->orderBy('nama_kelas')->get();
        
        $guruList = \App\Models\Guru::orderBy('nama_guru')->get();

        return view('pages.set_wali_kelas', compact('kelasData', 'semesterAktif', 'guruList'));
    }

    public function updateWaliKelas(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
            'guru_id' => 'required|exists:guru,nip',
        ]);

        $semesterAktif = Semester::where('is_aktif', true)->first();
        if (!$semesterAktif) {
            return redirect()->back()->with('error', 'Tidak ada semester aktif.');
        }

        \App\Models\WaliKelas::updateOrCreate(
            ['kelas_id' => $request->kelas_id, 'semester_id' => $semesterAktif->id],
            ['guru_id' => $request->guru_id]
        );

        return redirect()->back()->with('success', 'Wali kelas berhasil diperbarui.');
    }


    public function manageStudents(Request $request, $kode_kelas)
    {
        $kelas = Kelas::where('kode_kelas', $kode_kelas)->firstOrFail();
        $semesterAktif = Semester::with('tahunAjaran')->where('is_aktif', true)->first();

        if (!$semesterAktif) {
            return redirect()->route('pembagian_kelas')->with('error', 'Tidak ada semester aktif.');
        }

        // Siswa yang sudah ada di kelas ini
        $siswaDiKelas = RiwayatKelasSiswa::with('siswa')
            ->where('kode_kelas', $kode_kelas)
            ->where('semester_id', $semesterAktif->id)
            ->get();

        // Siswa yang belum memiliki kelas di semester aktif
        $queryBelumAdaKelas = Siswa::whereDoesntHave('kelasSiswa', function($q) use ($semesterAktif) {
            $q->where('semester_id', $semesterAktif->id);
        });

        // Filter unassigned students
        if ($request->filled('search')) {
            $queryBelumAdaKelas->where(function($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('angkatan')) {
            $queryBelumAdaKelas->where('angkatan', $request->angkatan);
        }

        $siswaTersedia = $queryBelumAdaKelas->orderBy('nama_siswa')->paginate(20)->withQueryString();
        $angkatanList = Siswa::whereNotNull('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan', 'angkatan')->toArray();

        return view('pages.kelola_siswa_kelas', compact('kelas', 'siswaDiKelas', 'siswaTersedia', 'semesterAktif', 'angkatanList'));
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

    public function bulkStore(Request $request)
    {
        $request->validate([
            'selected_nis' => 'required|array',
            'selected_nis.*' => 'exists:siswa,nis',
            'kode_kelas' => 'required|exists:kelas,kode_kelas',
        ]);

        $semesterAktif = Semester::where('is_aktif', true)->first();
        if (!$semesterAktif) {
            return redirect()->back()->with('error', 'Tidak ada semester aktif.');
        }

        $successCount = 0;

        DB::beginTransaction();
        try {
            foreach ($request->selected_nis as $nis) {
                $existing = RiwayatKelasSiswa::where('nis', $nis)
                    ->where('semester_id', $semesterAktif->id)
                    ->first();

                if (!$existing) {
                    RiwayatKelasSiswa::create([
                        'nis' => $nis,
                        'kode_kelas' => $request->kode_kelas,
                        'semester_id' => $semesterAktif->id,
                        'status' => 'Aktif',
                    ]);
                } else {
                    $existing->update([
                        'kode_kelas' => $request->kode_kelas,
                    ]);
                }
                $successCount++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses pembagian kelas: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', "Berhasil memproses $successCount siswa ke kelas.");
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

    public function moveAll(Request $request)
    {
        $request->validate([
            'from_kode_kelas' => 'required|exists:kelas,kode_kelas',
            'to_kode_kelas' => 'required|exists:kelas,kode_kelas|different:from_kode_kelas',
        ]);

        $semesterAktif = Semester::where('is_aktif', true)->first();
        if (!$semesterAktif) {
            return redirect()->back()->with('error', 'Tidak ada semester aktif.');
        }

        $affected = RiwayatKelasSiswa::where('kode_kelas', $request->from_kode_kelas)
            ->where('semester_id', $semesterAktif->id)
            ->update(['kode_kelas' => $request->to_kode_kelas]);

        return redirect()->back()->with('success', "Berhasil memindahkan $affected siswa dari kelas " . $request->from_kode_kelas . " ke kelas " . $request->to_kode_kelas);
    }
}
