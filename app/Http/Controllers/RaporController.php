<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\WaliKelas;
use App\Models\RiwayatKelasSiswa;
use Illuminate\Http\Request;

class RaporController extends Controller
{
    public function showRapor(Request $request)
    {
        $semesterAktif = Semester::where('is_aktif', true)->first();
        
        // Ambil semua semester, urutkan berdasarkan Tanggal Mulai Tahun Ajaran terbaru
        $allSemesters = Semester::join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->select('semester.*')
            ->orderBy('tahun_ajaran.tanggal_mulai', 'desc') // Menggunakan tanggal agar urutan pasti akurat
            ->orderBy('semester.semester', 'desc')      // Genap (2) di atas Ganjil (1)
            ->with('tahunAjaran')
            ->get();
        
        // Gunakan semester dari request jika ada, jika tidak gunakan semester aktif
        $selectedSemesterId = $request->get('semester_id', $semesterAktif?->id);
        $selectedSemester = Semester::find($selectedSemesterId);

        $query = Siswa::with(['kelasSiswa' => function ($q) use ($selectedSemesterId) {
            if ($selectedSemesterId) {
                $q->where('semester_id', $selectedSemesterId)->with(['kelas', 'nilai.komponenNilai']);
            }
        }])
        ->where('status', 'Aktif');

        if ($request->filled('search')) {
            $query->where('nama_siswa', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kelas_id')) {
            $kelas = Kelas::find($request->kelas_id);
            $query->whereHas('kelasSiswa', function ($q) use ($kelas, $selectedSemesterId) {
                $q->where('kode_kelas', $kelas?->kode_kelas);
                if ($selectedSemesterId) {
                    $q->where('semester_id', $selectedSemesterId);
                }
            });
        }

        // Restriksi Akses: Wali Kelas bisa melihat kelas binaannya + rekam jejak siswa binaannya sekarang
        $user = auth()->user();
        if (!$user->isAdmin()) {
            if ($user->isWaliKelas()) {
                $semesterAktif = Semester::where('is_aktif', true)->first();

                // 1. NIS Siswa yang SAAT INI (Semester Aktif) dibina oleh guru ini
                $activeSiswaIds = RiwayatKelasSiswa::whereIn('kode_kelas', function($q) use ($user, $semesterAktif) {
                    $q->select('kelas.kode_kelas')
                      ->from('kelas')
                      ->join('wali_kelas', 'kelas.id', '=', 'wali_kelas.kelas_id')
                      ->where('wali_kelas.guru_id', $user->guru_id)
                      ->where('wali_kelas.semester_id', $semesterAktif?->id);
                })->pluck('nis')->toArray();

                // 2. ID Kelas yang dipimpin guru ini PADA SEMESTER YANG DIPILIH
                $managedKelasIds = WaliKelas::where('guru_id', $user->guru_id)
                    ->where('semester_id', $selectedSemesterId)
                    ->pluck('kelas_id')
                    ->toArray();
                
                $query->where(function($q) use ($activeSiswaIds, $managedKelasIds, $selectedSemesterId) {
                    // Akses Kelas: Lihat semua siswa di kelas yang dipimpin pada semester terpilih
                    $q->whereHas('kelasSiswa', function ($sq) use ($managedKelasIds, $selectedSemesterId) {
                        $sq->whereIn('kode_kelas', function($subQuery) use ($managedKelasIds) {
                            $subQuery->select('kode_kelas')->from('kelas')->whereIn('id', $managedKelasIds);
                        })
                        ->where('semester_id', $selectedSemesterId);
                    })
                    // Akses Rekam Jejak: Lihat histori siswa binaan sekarang di semester mana pun
                    ->orWhereIn('nis', $activeSiswaIds);
                });
            } else {
                $query->whereRaw('1 = 0');
            }
        }

        $siswaData = $query->orderBy('nama_siswa')->paginate(20)->withQueryString();

        // Hitung rata-rata dan status kelulusan per siswa (Aggregasi Vertikal)
        $siswaData->getCollection()->transform(function ($siswa) {
            $ks = $siswa->kelasSiswa->first();
            if (!$ks || $ks->nilai->isEmpty()) {
                $siswa->rata_rata = null;
                $siswa->status_lulus = '-';
                return $siswa;
            }

            // Kelompokkan nilai per mata pelajaran (pengampu) untuk dihitung rata-ratanya
            $nilaiPerMapel = $ks->nilai->groupBy('pengampu_id')->map(function ($group) {
                // 1. Hitung Pengetahuan (P)
                $t_vals = $group->filter(fn($n) => $n->komponenNilai?->tipe === 'p_tugas')->pluck('skor');
                $u_vals = $group->filter(fn($n) => $n->komponenNilai?->tipe === 'p_uh')->pluck('skor');
                
                $t_avg = $t_vals->isNotEmpty() ? $t_vals->avg() : null;
                $u_avg = $u_vals->isNotEmpty() ? $u_vals->avg() : null;
                
                $nh_vals = array_filter([$t_avg, $u_avg], fn($v) => !is_null($v));
                $nh = count($nh_vals) > 0 ? array_sum($nh_vals) / count($nh_vals) : null;
                
                $uts = $group->where('jenis_nilai', 'p_uts')->first()?->skor;
                $uas = $group->where('jenis_nilai', 'p_uas')->first()?->skor;
                
                // Rumus Dinamis (Sesuai Dashboard)
                $total_p = (2 * ($nh ?? 0)) + ($uts ?? 0) + ($uas ?? 0);
                $pembagi = ( ($nh !== null ? 2 : 0) + ($uts !== null ? 1 : 0) + ($uas !== null ? 1 : 0) ) ?: 1;
                $p_avg = $total_p / $pembagi;
                
                // 2. Hitung Keterampilan (K)
                $k_vals = $group->whereIn('jenis_nilai', ['k_praktik', 'k_proyek', 'k_portofolio'])->pluck('skor');
                $k_avg = $k_vals->isNotEmpty() ? $k_vals->avg() : null;
                
                // 3. Rata-rata Mapel = (P + K) / 2 jika K ada, jika tidak hanya P
                if ($k_avg !== null) {
                    return ($p_avg + $k_avg) / 2;
                }
                return $p_avg;
            })->filter(fn($v) => $v !== null);

            $siswa->rata_rata = $nilaiPerMapel->isNotEmpty() ? round($nilaiPerMapel->avg(), 1) : null;

            if ($siswa->rata_rata === null) {
                $siswa->status_lulus = '-';
            } elseif ($siswa->rata_rata >= 75) {
                $siswa->status_lulus = 'Lulus';
            } elseif ($siswa->rata_rata >= 65) {
                $siswa->status_lulus = 'Kondisional';
            } else {
                $siswa->status_lulus = 'Tidak Lulus';
            }

            return $siswa;
        });

        $kelasListQuery = Kelas::orderBy('nama_kelas');
        if (!$user->isAdmin() && $user->isWaliKelas()) {
            $managedKelasIds = WaliKelas::where('guru_id', $user->guru_id)->pluck('kelas_id')->toArray();
            $kelasListQuery->whereIn('id', $managedKelasIds);
        }
        $kelasList = $kelasListQuery->get();

        // Siapkan opsi semester untuk filter
        $semesterOptions = $allSemesters->mapWithKeys(function($s) {
            return [$s->id => $s->tahunAjaran->nama . ' - ' . $s->semester];
        })->toArray();

        return view('pages.data_rapor', compact('siswaData', 'kelasList', 'semesterOptions', 'selectedSemester'));
    }

    public function saveCatatan(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,nis',
            'catatan' => 'nullable|string'
        ]);

        $semesterAktif = Semester::where('is_aktif', true)->first();
        if (!$semesterAktif) {
            return redirect()->back()->with('error', 'Semester aktif tidak ditemukan.');
        }

        $kelasSiswa = RiwayatKelasSiswa::where('nis', $request->siswa_id)
            ->where('semester_id', $semesterAktif->id)
            ->first();

        if (!$kelasSiswa) {
            return redirect()->back()->with('error', 'Data penempatan siswa tidak ditemukan.');
        }

        // Otorisasi Wali Kelas via Tabel WaliKelas
        $user = auth()->user();
        $kelas = Kelas::where('kode_kelas', $kelasSiswa->kode_kelas)->first();
        $isAuthorized = WaliKelas::where('guru_id', $user->guru_id)
            ->where('kelas_id', $kelas?->id)
            ->where('semester_id', $semesterAktif->id)
            ->exists();

        if (!$user->isGuru() || !$isAuthorized) {
            return redirect()->back()->with('error', 'Akses Ditolak: Hanya Wali Kelas yang bersangkutan yang dapat memberikan catatan rapor pada semester ini.');
        }

        $kelasSiswa->update([
            'catatan_wali' => $request->catatan
        ]);

        return redirect()->back()->with('success', 'Catatan wali kelas berhasil diperbarui.');
    }
}
