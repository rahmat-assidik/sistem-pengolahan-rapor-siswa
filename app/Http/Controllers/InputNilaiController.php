<?php

namespace App\Http\Controllers;

use App\Models\Pengampu;
use App\Models\Nilai;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\RiwayatKelasSiswa;
use App\Models\Semester;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InputNilaiController extends Controller
{
    public function showInputNilai(Request $request)
    {
        $semesterAktif = Semester::where('is_aktif', true)->first();
        $user = auth()->user();

        $pengampuList = Pengampu::with(['mapel', 'kelas'])
            ->when($semesterAktif, fn($q) => $q->where('semester_id', $semesterAktif->id))
            ->where('guru_id', $user->guru_id)
            ->where('status', 'Aktif')
            ->get();

        $mapelList = $pengampuList->pluck('mapel')->unique('id')->values();
        $kelasList = $pengampuList->pluck('kelas')->unique('id')->values();

        $mapelId = $request->get('mapel_id');
        $kelasId = $request->get('kelas_id');

        if ($mapelId && $kelasId) {
            $selectedPengampu = $pengampuList->where('mapel_id', $mapelId)
                                           ->where('kelas_id', $kelasId)
                                           ->first();
        } else {
            $selectedPengampuId = $request->get('pengampu_id', $pengampuList->first()?->id);
            $selectedPengampu = $pengampuList->firstWhere('id', $selectedPengampuId);
        }

        if ($request->filled('pengampu_id') && !$selectedPengampu) {
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
            'siswaJsonData'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'pengampu_id' => 'required|exists:pengampu,id',
            'nilai' => 'required|array',
        ]);

        $pengampu = Pengampu::findOrFail($request->pengampu_id);
        $semesterId = $pengampu->semester_id;
        $dataNilai = $request->input('nilai');

        DB::transaction(function() use ($pengampu, $semesterId, $dataNilai) {
            foreach ($dataNilai as $siswaId => $fields) {
                // Cari context riwayat kelas siswa
                $ks = RiwayatKelasSiswa::where('nis', $siswaId)
                    ->where('kode_kelas', $pengampu->kelas->kode_kelas)
                    ->where('semester_id', $semesterId)
                    ->first();

                if (!$ks) continue;

                $tugas = isset($fields['tugas']) && $fields['tugas'] !== '' ? $fields['tugas'] : null;
                $ulangan = isset($fields['ulangan']) && $fields['ulangan'] !== '' ? $fields['ulangan'] : null;
                $uts = isset($fields['uts']) && $fields['uts'] !== '' ? $fields['uts'] : null;
                $uas = isset($fields['uas']) && $fields['uas'] !== '' ? $fields['uas'] : null;
                $nilai_akhir = isset($fields['nilai_akhir']) && $fields['nilai_akhir'] !== '' ? $fields['nilai_akhir'] : null;

                // Logika hapus jika semua kosong (opsional) atau update
                if ($tugas === null && $ulangan === null && $uts === null && $uas === null && $nilai_akhir === null) {
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
