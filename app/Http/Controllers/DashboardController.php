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
        $semesterAktif = \App\Models\Semester::where('is_aktif', true)->first();
        
        $totalSiswa = Siswa::where('status', 'Aktif')->count();
        $totalGuru = Guru::where('status', 'Aktif')->count();
        $totalKelas = Kelas::count();
        $totalMapel = Mapel::where('status', 'Aktif')->count();

        // Data untuk chart distribusi nilai (berdasarkan Nilai Akhir)
        $distribusi = ['A' => 0, 'B' => 0, 'C' => 0, 'D' => 0, 'E' => 0];
        
        // Query Nilai hanya untuk Semester Aktif
        $nilaiAggregat = Nilai::join('riwayat_kelas_siswa', 'nilai.kelas_siswa_id', '=', 'riwayat_kelas_siswa.id')
            ->select('nilai.nilai_akhir')
            ->where('riwayat_kelas_siswa.semester_id', $semesterAktif?->id)
            ->whereNotNull('nilai.nilai_akhir')
            ->get();

        foreach ($nilaiAggregat as $n) {
            $p_avg = $n->nilai_akhir;
            
            if ($p_avg >= 90) $distribusi['A']++;
            elseif ($p_avg >= 80) $distribusi['B']++;
            elseif ($p_avg >= 70) $distribusi['C']++;
            elseif ($p_avg > 0) $distribusi['D']++;
        }

        // Kelengkapan nilai (Strict Check: Harus ada tugas, ulangan, uts, uas, nilai_akhir)
        $totalNilaiSlots = Pengampu::all()->sum(function ($p) {
            return $p->kelas->kelasSiswa()->count();
        });

        // Kita ambil semua pasangan (siswa, pengampu) yang punya nilai lengkap
        $totalNilaiTerisi = Nilai::whereNotNull('tugas')
            ->whereNotNull('ulangan')
            ->whereNotNull('uts')
            ->whereNotNull('uas')
            ->whereNotNull('nilai_akhir')
            ->count();

        return view('pages.dashboard', compact(
            'totalSiswa', 'totalGuru', 'totalKelas', 'totalMapel',
            'distribusi', 'totalNilaiSlots', 'totalNilaiTerisi'
        ));
    }
}
