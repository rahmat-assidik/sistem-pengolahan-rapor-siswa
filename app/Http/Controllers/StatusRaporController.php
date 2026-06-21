<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\WaliKelas;
use App\Models\RiwayatKelasSiswa;
use Illuminate\Http\Request;

class StatusRaporController extends Controller
{
    /**
     * Menampilkan halaman status rapor untuk wali kelas.
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // Pastikan user adalah wali kelas
        if (!$user->isWaliKelas()) {
            abort(403, 'Akses ditolak. Hanya wali kelas yang dapat mengakses halaman ini.');
        }

        $semesterAktif = Semester::where('is_aktif', true)->first();
        $allSemesters = Semester::join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->select('semester.*')
            ->orderBy('tahun_ajaran.tanggal_mulai', 'desc')
            ->orderBy('semester.semester', 'desc')
            ->with('tahunAjaran')
            ->get();

        $selectedSemesterId = $request->get('semester_id', $semesterAktif?->id);
        $selectedSemester = Semester::find($selectedSemesterId);

        // Ambil kelas yang diampu oleh wali kelas ini pada semester yang dipilih
        $managedKelas = Kelas::whereIn('id',
            WaliKelas::where('guru_id', $user->guru_id)
                ->when($selectedSemesterId, fn($q) => $q->where('semester_id', $selectedSemesterId))
                ->pluck('kelas_id')
        )->orderBy('nama_kelas')->get();

        $selectedKelas = null;
        $siswaData = collect();

        if ($request->filled('kelas_id') && $request->filled('semester_id')) {
            $selectedKelas = Kelas::find($request->kelas_id);

            // Verifikasi bahwa wali kelas benar-benar mengampu kelas ini
            $isAuthorized = WaliKelas::where('guru_id', $user->guru_id)
                ->where('kelas_id', $request->kelas_id)
                ->where('semester_id', $selectedSemesterId)
                ->exists();

            if (!$isAuthorized) {
                abort(403, 'Anda tidak memiliki akses ke kelas ini.');
            }

            $kodeKelas = $selectedKelas?->kode_kelas;

            $query = Siswa::with(['kelasSiswa' => function ($q) use ($selectedSemesterId) {
                $q->where('semester_id', $selectedSemesterId)->with(['kelas', 'nilai']);
            }])
            ->where('status', 'Aktif')
            ->whereHas('kelasSiswa', function ($q) use ($kodeKelas, $selectedSemesterId) {
                $q->where('kode_kelas', $kodeKelas)
                  ->where('semester_id', $selectedSemesterId);
            });

            if ($request->filled('search')) {
                $query->where('nama_siswa', 'like', '%' . $request->search . '%');
            }

            $siswaData = $query->orderBy('nama_siswa')->paginate(20)->withQueryString();

            // Hitung rata-rata nilai untuk setiap siswa
            $siswaData->getCollection()->transform(function ($siswa) {
                $ks = $siswa->kelasSiswa->first();
                if (!$ks || $ks->nilai->isEmpty()) {
                    $siswa->rata_rata = null;
                    $siswa->status_rapor_value = $ks?->status_rapor ?? 'Belum Ditentukan';
                    $siswa->kelas_siswa_id = $ks?->id;
                    return $siswa;
                }
                $nilaiPerMapel = $ks->nilai->map(fn ($n) => $n->nilai_akhir)->filter(fn($v) => $v !== null);
                $siswa->rata_rata = $nilaiPerMapel->isNotEmpty() ? round($nilaiPerMapel->avg(), 1) : null;
                $siswa->status_rapor_value = $ks->status_rapor ?? 'Belum Ditentukan';
                $siswa->kelas_siswa_id = $ks->id;
                return $siswa;
            });
        }

        $semesterOptions = $allSemesters->mapWithKeys(fn($s) => [$s->id => $s->tahunAjaran->nama . ' - ' . $s->semester])->toArray();

        return view('pages.status_rapor', compact(
            'siswaData', 'managedKelas', 'semesterOptions', 'selectedSemester', 'selectedKelas'
        ));
    }

    /**
     * Update status rapor satu siswa.
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'kelas_siswa_id' => 'required|exists:riwayat_kelas_siswa,id',
            'status_rapor' => 'required|in:Tuntas,Tidak Tuntas,Belum Ditentukan',
        ]);

        $kelasSiswa = RiwayatKelasSiswa::findOrFail($request->kelas_siswa_id);

        // Otorisasi: pastikan wali kelas berhak
        $user = auth()->user();
        $kelas = Kelas::where('kode_kelas', $kelasSiswa->kode_kelas)->first();
        $isAuthorized = WaliKelas::where('guru_id', $user->guru_id)
            ->where('kelas_id', $kelas?->id)
            ->where('semester_id', $kelasSiswa->semester_id)
            ->exists();

        if (!$user->isGuru() || !$isAuthorized) {
            return redirect()->back()->with('error', 'Akses ditolak. Anda tidak berhak mengubah status rapor siswa ini.');
        }

        $catatan = null;
        if ($request->status_rapor === 'Tuntas') {
            $catatan = "Berdasarkan pencapaian hasil belajar, peserta didik ditetapkan:\nLulus / <del>Tidak Lulus</del>";
        } elseif ($request->status_rapor === 'Tidak Tuntas') {
            $catatan = "Berdasarkan pencapaian hasil belajar, peserta didik ditetapkan:\n<del>Lulus</del> / Tidak Lulus";
        }

        $kelasSiswa->update([
            'status_rapor' => $request->status_rapor,
            'catatan_wali' => $catatan,
        ]);

        return redirect()->back()->with('success', 'Status rapor dan catatan berhasil diperbarui.');
    }

    /**
     * Bulk update status rapor beberapa siswa sekaligus.
     */
    public function bulkUpdateStatus(Request $request)
    {
        $request->validate([
            'kelas_siswa_ids' => 'required|array|min:1',
            'kelas_siswa_ids.*' => 'exists:riwayat_kelas_siswa,id',
            'status_rapor' => 'required|in:Tuntas,Tidak Tuntas,Belum Ditentukan',
        ]);

        $user = auth()->user();

        foreach ($request->kelas_siswa_ids as $id) {
            $kelasSiswa = RiwayatKelasSiswa::findOrFail($id);
            $kelas = Kelas::where('kode_kelas', $kelasSiswa->kode_kelas)->first();

            $isAuthorized = WaliKelas::where('guru_id', $user->guru_id)
                ->where('kelas_id', $kelas?->id)
                ->where('semester_id', $kelasSiswa->semester_id)
                ->exists();

            if (!$user->isGuru() || !$isAuthorized) {
                return redirect()->back()->with('error', 'Akses ditolak untuk salah satu siswa yang dipilih.');
            }

            $catatan = null;
            if ($request->status_rapor === 'Tuntas') {
                $catatan = "Berdasarkan pencapaian hasil belajar, peserta didik ditetapkan:\nLulus / <del>Tidak Lulus</del>";
            } elseif ($request->status_rapor === 'Tidak Tuntas') {
                $catatan = "Berdasarkan pencapaian hasil belajar, peserta didik ditetapkan:\n<del>Lulus</del> / Tidak Lulus";
            }

            $kelasSiswa->update([
                'status_rapor' => $request->status_rapor,
                'catatan_wali' => $catatan,
            ]);
        }

        $count = count($request->kelas_siswa_ids);
        return redirect()->back()->with('success', "Status rapor dan catatan {$count} siswa berhasil diperbarui.");
    }
}
