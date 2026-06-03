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
                'kelas.nama_kelas',
                'nilai.tugas',
                'nilai.ulangan',
                'nilai.uts',
                'nilai.uas',
                'nilai.nilai_akhir as rata_pengetahuan' // kita panggil rata_pengetahuan agar view tidak perlu banyak ubah jika ada referensi kesana
            )
            ->selectRaw("
                CASE WHEN 
                    nilai.tugas IS NOT NULL AND
                    nilai.ulangan IS NOT NULL AND
                    nilai.uts IS NOT NULL AND
                    nilai.uas IS NOT NULL
                THEN 1 ELSE 0 END as is_lengkap
            ")
            ->join('kelas', 'riwayat_kelas_siswa.kode_kelas', '=', 'kelas.kode_kelas')
            ->join('pengampu', function($join) {
                $join->on('kelas.id', '=', 'pengampu.kelas_id')
                     ->on('riwayat_kelas_siswa.semester_id', '=', 'pengampu.semester_id');
            })
            ->join('mapel', 'pengampu.mapel_id', '=', 'mapel.kode_mapel')
            ->leftJoin('nilai', function($join) {
                $join->on('riwayat_kelas_siswa.id', '=', 'nilai.kelas_siswa_id')
                     ->on('pengampu.id', '=', 'nilai.pengampu_id');
            });

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