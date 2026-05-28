<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Semester;
use App\Models\RiwayatKelasSiswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RekapNilaiController extends Controller
{
    public function showRekapNilai(Request $request)
    {
        $semesterAktif = Semester::where('is_aktif', true)->first();

        // Menggunakan Query Builder dari RiwayatKelasSiswa agar semua siswa muncul meskipun belum ada nilai
        $query = RiwayatKelasSiswa::query()
            ->select(
                'riwayat_kelas_siswa.id as kelas_siswa_id',
                'pengampu.id as pengampu_id',
                'riwayat_kelas_siswa.nis as siswa_id',
                'pengampu.kkm',
                'mapel.nama_mapel',
                'kelas.nama_kelas'
            )
            ->join('kelas', 'riwayat_kelas_siswa.kode_kelas', '=', 'kelas.kode_kelas')
            ->join('pengampu', function($join) {
                $join->on('kelas.id', '=', 'pengampu.kelas_id')
                     ->on('riwayat_kelas_siswa.semester_id', '=', 'pengampu.semester_id');
            })
            ->join('mapel', 'pengampu.mapel_id', '=', 'mapel.kode_mapel')
            ->leftJoin('nilai', function($join) {
                $join->on('riwayat_kelas_siswa.id', '=', 'nilai.kelas_siswa_id')
                     ->on('pengampu.id', '=', 'nilai.pengampu_id');
            })
            ->leftJoin('komponen_nilai', 'nilai.komponen_nilai_id', '=', 'komponen_nilai.id')
            // Pivot vertikal ke horizontal - Mendukung Komponen Dinamis
            ->selectRaw("ROUND(AVG(CASE WHEN komponen_nilai.tipe = 'p_tugas' THEN nilai.skor END), 1) as tugas")
            ->selectRaw("ROUND(AVG(CASE WHEN komponen_nilai.tipe = 'p_uh' THEN nilai.skor END), 1) as uh")
            ->selectRaw("MAX(CASE WHEN nilai.jenis_nilai = 'p_uts' THEN nilai.skor END) as uts")
            ->selectRaw("MAX(CASE WHEN nilai.jenis_nilai = 'p_uas' THEN nilai.skor END) as uas")
            // Perhitungan Rata-rata Pengetahuan: ((2 * NH) + UTS + UAS) / 4
            ->selectRaw("
                ROUND(
                    (
                        (
                            (COALESCE(AVG(CASE WHEN komponen_nilai.tipe = 'p_tugas' THEN nilai.skor END), 0) + 
                             COALESCE(AVG(CASE WHEN komponen_nilai.tipe = 'p_uh' THEN nilai.skor END), 0)) 
                             / (CASE WHEN EXISTS (SELECT 1 FROM komponen_nilai kn2 WHERE kn2.pengampu_id = pengampu.id AND kn2.tipe IN ('p_tugas', 'p_uh')) THEN 2 ELSE 1 END)
                             * 2
                        ) +
                        COALESCE(MAX(CASE WHEN nilai.jenis_nilai = 'p_uts' THEN nilai.skor END), 0) +
                        COALESCE(MAX(CASE WHEN nilai.jenis_nilai = 'p_uas' THEN nilai.skor END), 0)
                    ) / 4, 1
                ) as rata_pengetahuan
            ")
            // Cek Kelengkapan (3 Aspek): Pengetahuan, Keterampilan, dan Sikap
            ->selectRaw("
                CASE WHEN 
                    -- Pengetahuan Lengkap
                    MAX(CASE WHEN komponen_nilai.tipe = 'p_tugas' THEN nilai.skor END) IS NOT NULL AND
                    MAX(CASE WHEN komponen_nilai.tipe = 'p_uh' THEN nilai.skor END) IS NOT NULL AND
                    MAX(CASE WHEN nilai.jenis_nilai = 'p_uts' THEN nilai.skor END) IS NOT NULL AND
                    MAX(CASE WHEN nilai.jenis_nilai = 'p_uas' THEN nilai.skor END) IS NOT NULL AND
                    -- Keterampilan Minimal 1
                    (MAX(CASE WHEN nilai.jenis_nilai = 'k_praktik' THEN nilai.skor END) IS NOT NULL OR
                     MAX(CASE WHEN nilai.jenis_nilai = 'k_proyek' THEN nilai.skor END) IS NOT NULL OR
                     MAX(CASE WHEN nilai.jenis_nilai = 'k_portofolio' THEN nilai.skor END) IS NOT NULL) AND
                    -- Sikap Lengkap
                    MAX(CASE WHEN nilai.jenis_nilai = 's_spiritual' THEN nilai.skor END) IS NOT NULL AND
                    MAX(CASE WHEN nilai.jenis_nilai = 's_sosial' THEN nilai.skor END) IS NOT NULL
                THEN 1 ELSE 0 END as is_lengkap
            ")
            ->groupBy('riwayat_kelas_siswa.id', 'pengampu.id', 'riwayat_kelas_siswa.nis', 'pengampu.kkm', 'mapel.nama_mapel', 'kelas.nama_kelas');

        // Filter berdasarkan Semester Aktif
        if ($semesterAktif) {
            $query->where('riwayat_kelas_siswa.semester_id', $semesterAktif->id);
        }

        // Filter Pencarian & Dropdown
        if ($request->search) {
            $query->whereHas('siswa', function ($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->kelas_id) {
            $query->where('pengampu.kelas_id', $request->kelas_id);
        }

        if ($request->mapel_id) {
            $query->where('pengampu.mapel_id', $request->mapel_id);
        }

        $nilaiData = $query->with(['siswa'])
            ->paginate(20)
            ->withQueryString();

        $kelasList = Kelas::orderBy('nama_kelas')->get();
        $mapelList = Mapel::where('status', 'Aktif')->orderBy('nama_mapel')->get();

        return view('pages.rekap_nilai', compact('nilaiData', 'kelasList', 'mapelList'));
    }
}