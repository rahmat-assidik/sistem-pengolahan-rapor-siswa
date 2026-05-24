<?php

namespace App\Http\Controllers;

use App\Models\Pengampu;
use App\Models\Nilai;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\RiwayatKelasSiswa;
use App\Models\Semester;
use App\Models\Siswa;
use App\Models\KomponenNilai;
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
        $komponenList = collect([]);

        if ($selectedPengampu && $semesterAktif) {
            // Ambil Komponen Dinamis
            $komponenList = KomponenNilai::where('pengampu_id', $selectedPengampu->id)->get();

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

            // Ambil Nilai Vertikal dan kelompokkan
            $nilaiGrouped = Nilai::where('pengampu_id', $selectedPengampu->id)
                ->whereIn('kelas_siswa_id', $kelasSiswaIds)
                ->get()
                ->groupBy('kelas_siswa_id');

            $siswaJsonData = collect($siswaList->items())->map(function ($s) use ($kelasSiswaMap, $nilaiGrouped, $komponenList) {
                $ks = $kelasSiswaMap->get($s->nis);
                $nSiswa = $nilaiGrouped->get($ks->id) ?? collect([]);
                
                // Helper to get score by component ID
                $getSkorKomp = fn($comp_id) => $nSiswa->firstWhere('komponen_nilai_id', $comp_id)?->skor;
                // Helper to get score by fixed type (UTS/UAS/Sikap)
                $getSkorFixed = fn($type) => $nSiswa->firstWhere('jenis_nilai', $type)->whereNull('komponen_nilai_id')->first()?->skor;

                // Hitung Rata-rata Pengetahuan dengan pembobotan dinamis
                $t_vals = [];
                $uh_vals = [];
                
                foreach($komponenList as $komp) {
                    $val = $getSkorKomp($komp->id);
                    if ($val !== null) {
                        if ($komp->tipe === 'p_tugas') $t_vals[] = $val;
                        if ($komp->tipe === 'p_uh') $uh_vals[] = $val;
                    }
                }
                
                $t_avg = count($t_vals) > 0 ? array_sum($t_vals) / count($t_vals) : null;
                $uh_avg = count($uh_vals) > 0 ? array_sum($uh_vals) / count($uh_vals) : null;

                $nh_components = array_filter([$t_avg, $uh_avg], fn($v) => $v !== null);
                $p_uts = $nSiswa->where('jenis_nilai', 'p_uts')->first()?->skor;
                $p_uas = $nSiswa->where('jenis_nilai', 'p_uas')->first()?->skor;

                if (count($nh_components) > 0 || $p_uts !== null || $p_uas !== null) {
                    $nh = count($nh_components) > 0 ? array_sum($nh_components) / count($nh_components) : 0;
                    $p_avg = round(((2 * $nh) + ($p_uts ?? 0) + ($p_uas ?? 0)) / 4, 1);
                } else {
                    $p_avg = null;
                }

                $k_vals = array_filter([
                    $nSiswa->where('jenis_nilai', 'k_praktik')->first()?->skor,
                    $nSiswa->where('jenis_nilai', 'k_proyek')->first()?->skor,
                    $nSiswa->where('jenis_nilai', 'k_portofolio')->first()?->skor
                ], fn($v) => $v !== null);
                
                $k_avg = count($k_vals) > 0 ? round(array_sum($k_vals) / count($k_vals), 2) : null;

                // Konversi Skor Sikap kembali ke Huruf (4=A, 3=B, 2=C, 1=D)
                $numToChar = [4 => 'A', 3 => 'B', 2 => 'C', 1 => 'D', 0 => 'B'];
                
                // Construct score array for dynamic components
                $scores = [];
                foreach($komponenList as $komp) {
                    $scores['comp_' . $komp->id] = $getSkorKomp($komp->id);
                }

                return array_merge([
                    'id' => $s->id,
                    'nis' => $s->nis,
                    'nama' => $s->nama_siswa,
                    'p_uts' => $p_uts,
                    'p_uas' => $p_uas,
                    'p_avg' => $p_avg,
                    'p_pred' => Nilai::hitungPredikat($p_avg),
                    'k_praktik' => $nSiswa->where('jenis_nilai', 'k_praktik')->first()?->skor,
                    'k_proyek' => $nSiswa->where('jenis_nilai', 'k_proyek')->first()?->skor,
                    'k_portofolio' => $nSiswa->where('jenis_nilai', 'k_portofolio')->first()?->skor,
                    'k_avg' => $k_avg,
                    'k_pred' => Nilai::hitungPredikat($k_avg),
                    's_spiritual' => ($recS = $nSiswa->where('jenis_nilai', 's_spiritual')->first()) ? ($numToChar[(int)$recS->skor] ?? '') : '',
                    's_sosial' => ($recSo = $nSiswa->where('jenis_nilai', 's_sosial')->first()) ? ($numToChar[(int)$recSo->skor] ?? '') : '',
                ], $scores);
            })->values();
        }

        return view('pages.input_nilai', compact(
            'pengampuList',
            'mapelList',
            'kelasList',
            'selectedPengampu',
            'siswaList',
            'siswaJsonData',
            'komponenList'
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

                foreach ($fields as $key => $value) {
                    // Tentukan kriteria pencarian (Unique Key)
                    $searchCriteria = [
                        'kelas_siswa_id' => $ks->id, 
                        'pengampu_id' => $pengampu->id,
                    ];

                    if (str_starts_with($key, 'comp_')) {
                        $searchCriteria['komponen_nilai_id'] = str_replace('comp_', '', $key);
                        $searchCriteria['jenis_nilai'] = 'dynamic';
                    } else {
                        $searchCriteria['jenis_nilai'] = $key;
                        $searchCriteria['komponen_nilai_id'] = null;
                        
                        // Handle Sikap conversion
                        if (in_array($key, ['s_spiritual', 's_sosial']) && $value !== null && $value !== '') {
                            $charToNum = ['A' => 4, 'B' => 3, 'C' => 2, 'D' => 1];
                            $value = $charToNum[$value] ?? 3;
                        }
                    }

                    // Logika: Jika value kosong/null, hapus data. Jika ada isi, simpan/update.
                    if ($value === null || $value === '') {
                        Nilai::where($searchCriteria)->delete();
                    } else {
                        Nilai::updateOrCreate($searchCriteria, ['skor' => $value]);
                    }
                }
            }
        });

        return redirect()->back()->with([
            'success' => 'Data nilai berhasil disimpan.',
            'active_tab' => $request->active_tab
        ]);
    }

    public function storeKomponen(Request $request)
    {
        $request->validate([
            'pengampu_id' => 'required|exists:pengampu,id',
            'nama_komponen' => 'required|string|max:100',
            'tipe' => 'required|in:p_tugas,p_uh'
        ]);

        KomponenNilai::create($request->all());

        return redirect()->back()->with('success', 'Komponen nilai berhasil ditambahkan.');
    }

    public function destroyKomponen($id)
    {
        $komponen = KomponenNilai::findOrFail($id);
        
        // Hapus semua nilai yang tertempel pada komponen ini
        Nilai::where('komponen_nilai_id', $id)->delete();
        
        $komponen->delete();

        return redirect()->back()->with('success', 'Komponen nilai dan seluruh datanya berhasil dihapus.');
    }
}
