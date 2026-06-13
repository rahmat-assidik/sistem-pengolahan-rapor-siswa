<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Nilai;
use App\Models\Pengampu;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function showDashboard()
    {
        $semesterAktif = \App\Models\Semester::with('tahunAjaran')->where('is_aktif', true)->first();
        $user = auth()->user();
        
        $totalSiswa = Siswa::where('status', 'Aktif')->count();
        $totalGuru = Guru::where('status', 'Aktif')->count();
        $totalKelas = Kelas::count();
        $totalMapel = Mapel::where('status', 'Aktif')->count();

        // Data untuk chart distribusi nilai per kelas
        $distribusiPerKelas = [];
        $kelasList = Kelas::orderBy('nama_kelas')->get();

        foreach ($kelasList as $kelas) {
            $counts = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0];
            $queryNilaiKelas = Nilai::join('riwayat_kelas_siswa', 'nilai.kelas_siswa_id', '=', 'riwayat_kelas_siswa.id')
                ->where('riwayat_kelas_siswa.semester_id', $semesterAktif?->id)
                ->where('riwayat_kelas_siswa.kode_kelas', $kelas->kode_kelas)
                ->whereNotNull('nilai.nilai_akhir');

            if ($user->role === 'guru') {
                $queryNilaiKelas->whereIn('nilai.pengampu_id', function($q) use ($user) {
                    $q->select('id')->from('pengampu')->where('guru_id', $user->guru_id);
                });
            }

            $nilaiKelas = $queryNilaiKelas->select('nilai.nilai_akhir')->get();

            foreach ($nilaiKelas as $n) {
                $v = $n->nilai_akhir;
                if ($v >= 90) $counts['A']++;
                elseif ($v >= 80) $counts['B']++;
                elseif ($v >= 70) $counts['C']++;
                else $counts['D']++;
            }

            if ($nilaiKelas->count() > 0) {
                $distribusiPerKelas[$kelas->nama_kelas] = $counts;
            }
        }

        // Statistik Progres Input (berdasarkan penugasan pengampu)
        $pengampuQuery = Pengampu::query();
        if ($user->role === 'guru') {
            $pengampuQuery->where('guru_id', $user->guru_id);
        }
        
        $pengampuList = $pengampuQuery->get();
        $totalSiswaDiampu = 0;
        foreach ($pengampuList as $p) {
            $totalSiswaDiampu += \App\Models\RiwayatKelasSiswa::where('kode_kelas', $p->kelas->kode_kelas)
                                ->where('semester_id', $semesterAktif?->id)
                                ->count();
        }

        $totalNilaiTerisi = Nilai::whereIn('pengampu_id', $pengampuList->pluck('id'))
            ->whereNotNull('nilai_akhir')
            ->count();

        // Data Tambahan untuk Guru
        $mapelDiampu = $user->role === 'guru' ? $pengampuList->pluck('mapel_id')->unique()->count() : 0;
        $kelasDiampu = $user->role === 'guru' ? $pengampuList->pluck('kelas_id')->unique()->count() : 0;

        return view('pages.dashboard', compact(
            'totalSiswa', 'totalGuru', 'totalKelas', 'totalMapel',
            'distribusiPerKelas', 'totalSiswaDiampu', 'totalNilaiTerisi',
            'semesterAktif', 'mapelDiampu', 'kelasDiampu'
        ));
    }
}
