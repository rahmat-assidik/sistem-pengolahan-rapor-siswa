<?php

namespace App\Http\Controllers;

use App\Models\Pengampu;
use App\Models\Nilai;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\RiwayatKelasSiswa;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\BobotNilai;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InputNilaiController extends Controller
{
    public function showInputNilai(Request $request)
    {
        $semesterAktif = Semester::where('is_aktif', true)->first();
        $user = auth()->user();
        $settings = BobotNilai::current();

        $pengampuList = Pengampu::with(['mapel', 'kelas'])
            ->when($semesterAktif, fn($q) => $q->where('semester_id', $semesterAktif->id))
            ->where('guru_id', $user->guru_id)
            ->where('status', 'Aktif')
            ->get();

        $mapelList = $pengampuList->pluck('mapel')->unique('kode_mapel')->values();
        
        $mapelId = $request->get('mapel_id', $mapelList->first()?->kode_mapel);
        
        // Filter kelas list based on selected mapel
        $kelasList = $pengampuList->where('mapel_id', $mapelId)->pluck('kelas')->unique('id')->values();
        
        $kelasId = $request->get('kelas_id');
        // Memastikan kelas_id valid untuk mapel yang dipilih, jika tidak valid ambil yang pertama
        if (!$kelasList->contains('id', $kelasId)) {
            $kelasId = $kelasList->first()?->id;
        }

        $selectedPengampu = $pengampuList->where('mapel_id', $mapelId)
                                       ->where('kelas_id', $kelasId)
                                       ->first();

        if ($request->filled('mapel_id') && $request->filled('kelas_id') && !$selectedPengampu) {
             return redirect()->route('input_nilai')->with('error', 'Anda tidak memiliki otoritas untuk mengakses data ini.');
        }

        $siswaList = new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20);
        $siswaJsonData = collect([]);

        if ($selectedPengampu && $semesterAktif) {
            // Ambil KelasSiswa (Bridge antara Siswa, Kelas, dan Semester)
            $siswaList = Siswa::whereHas('kelasSiswa', function($q) use ($selectedPengampu, $semesterAktif) {
                    $q->where('kode_kelas', $selectedPengampu->kelas->kode_kelas)
                      ->where('semester_id', $semesterAktif->id);
                })
                ->orderBy('nama_siswa')
                ->paginate(20)
                ->withQueryString();

            $siswaIds = collect($siswaList->items())->pluck('nis');
            
            $kelasSiswaMap = RiwayatKelasSiswa::whereIn('nis', $siswaIds)
                ->where('kode_kelas', $selectedPengampu->kelas->kode_kelas)
                ->where('semester_id', $semesterAktif->id)
                ->get()
                ->keyBy('nis');

            $kelasSiswaIds = $kelasSiswaMap->pluck('id');

            // Ambil Nilai yang fix
            $nilaiMap = Nilai::where('pengampu_id', $selectedPengampu->id)
                ->whereIn('kelas_siswa_id', $kelasSiswaIds)
                ->get()
                ->keyBy('kelas_siswa_id');

            $siswaJsonData = collect($siswaList->items())->map(function ($s) use ($kelasSiswaMap, $nilaiMap) {
                $ks = $kelasSiswaMap->get($s->nis);
                $nSiswa = $nilaiMap->get($ks->id);
                
                return [
                    'id' => $s->id,
                    'nis' => $s->nis,
                    'nama' => $s->nama_siswa,
                    'tugas' => $nSiswa?->tugas,
                    'ulangan' => $nSiswa?->ulangan,
                    'uts' => $nSiswa?->uts,
                    'uas' => $nSiswa?->uas,
                    'nilai_akhir' => $nSiswa?->nilai_akhir,
                    'predikat' => Nilai::hitungPredikat($nSiswa?->nilai_akhir),
                ];
            })->values();
        }

        return view('pages.input_nilai', compact(
            'pengampuList',
            'mapelList',
            'kelasList',
            'selectedPengampu',
            'siswaList',
            'siswaJsonData',
            'settings'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengampu_id' => 'required|exists:pengampu,id',
            'kkm'         => 'required|integer|min:0|max:100',
            'nilai'       => 'required|array',
        ]);

        $pengampu = Pengampu::findOrFail($request->pengampu_id);
        $semesterId = $pengampu->semester_id;
        $dataNilai = $request->input('nilai');

        $settings = BobotNilai::forPengampu($pengampu->id) ?? BobotNilai::current();

        DB::transaction(function() use ($pengampu, $semesterId, $dataNilai, $request, $settings) {
            // Update KKM pengampu
            $pengampu->update(['kkm' => $request->kkm]);

            foreach ($dataNilai as $siswaId => $fields) {
                // Cari context riwayat kelas siswa
                $ks = RiwayatKelasSiswa::where('nis', $siswaId)
                    ->where('kode_kelas', $pengampu->kelas->kode_kelas)
                    ->where('semester_id', $semesterId)
                    ->first();

                if (!$ks) continue;

                $tugas = isset($fields['tugas']) && $fields['tugas'] !== '' ? (float)$fields['tugas'] : null;
                $ulangan = isset($fields['ulangan']) && $fields['ulangan'] !== '' ? (float)$fields['ulangan'] : null;
                $uts = isset($fields['uts']) && $fields['uts'] !== '' ? (float)$fields['uts'] : null;
                $uas = isset($fields['uas']) && $fields['uas'] !== '' ? (float)$fields['uas'] : null;
                
                // Hitung Nilai Akhir berdasarkan bobot
                $nilai_akhir = null;
                if ($tugas !== null || $ulangan !== null || $uts !== null || $uas !== null) {
                    $valTugas = $tugas ?? 0;
                    $valUlangan = $ulangan ?? 0;
                    $valUts = $uts ?? 0;
                    $valUas = $uas ?? 0;

                    $total = ($valTugas * $settings->bobot_tugas) + 
                             ($valUlangan * $settings->bobot_ulangan) + 
                             ($valUts * $settings->bobot_uts) + 
                             ($valUas * $settings->bobot_uas);
                    
                    $nilai_akhir = round($total / 100, 2);
                }

                // Logika hapus jika semua kosong (opsional) atau update
                if ($tugas === null && $ulangan === null && $uts === null && $uas === null) {
                    Nilai::where('kelas_siswa_id', $ks->id)
                         ->where('pengampu_id', $pengampu->id)
                         ->delete();
                } else {
                    Nilai::updateOrCreate(
                        [
                            'kelas_siswa_id' => $ks->id, 
                            'pengampu_id' => $pengampu->id
                        ],
                        [
                            'tugas' => $tugas,
                            'ulangan' => $ulangan,
                            'uts' => $uts,
                            'uas' => $uas,
                            'nilai_akhir' => $nilai_akhir,
                        ]
                    );
                }
            }
        });

        return redirect()->back()->with([
            'success' => 'Data nilai berhasil disimpan.'
        ]);
    }
}
