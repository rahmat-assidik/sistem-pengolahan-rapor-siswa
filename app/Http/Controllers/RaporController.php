<?php

namespace App\Http\Controllers;

use App\Models\Nilai;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Semester;
use App\Models\WaliKelas;
use App\Models\RiwayatKelasSiswa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class RaporController extends Controller
{
    public function showRapor(Request $request)
    {
        $semesterAktif = Semester::where('is_aktif', true)->first();
        $allSemesters = Semester::join('tahun_ajaran', 'semester.tahun_ajaran_id', '=', 'tahun_ajaran.id')
            ->select('semester.*')
            ->orderBy('tahun_ajaran.tanggal_mulai', 'desc')
            ->orderBy('semester.semester', 'desc')
            ->with('tahunAjaran')
            ->get();
        
        $selectedSemesterId = $request->get('semester_id', $semesterAktif?->id);
        $selectedSemester = Semester::find($selectedSemesterId);
        $selectedKelas = Kelas::find($request->kelas_id);
        $user = auth()->user();

        $siswaData = collect(); // Default empty
        if ($request->filled('kelas_id') && $request->filled('semester_id')) {
            $selectedKelasModel = Kelas::find($request->kelas_id);
            $kodeKelas = $selectedKelasModel?->kode_kelas;

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

            // Authorization logic
            if (!$user->isAdmin()) {
                if ($user->isWaliKelas()) {
                    $managedKodeKelas = WaliKelas::join('kelas', 'wali_kelas.kelas_id', '=', 'kelas.id')
                        ->where('wali_kelas.guru_id', $user->guru_id)
                        ->where('wali_kelas.semester_id', $selectedSemesterId)
                        ->pluck('kelas.kode_kelas')
                        ->toArray();
                    
                    if (!in_array($kodeKelas, $managedKodeKelas)) {
                        $query->whereRaw('1 = 0');
                    }
                } else {
                    $query->whereRaw('1 = 0');
                }
            }

            $siswaData = $query->orderBy('nama_siswa')->paginate(20)->withQueryString();

            // Hitung rata-rata dan status kelulusan (Aggregasi)
            $siswaData->getCollection()->transform(function ($siswa) {
                // ... (existing transformation logic)
                $ks = $siswa->kelasSiswa->first();
                if (!$ks || $ks->nilai->isEmpty()) {
                    $siswa->rata_rata = null;
                    $siswa->status_lulus = '-';
                    return $siswa;
                }
                $nilaiPerMapel = $ks->nilai->map(fn ($n) => $n->nilai_akhir)->filter(fn($v) => $v !== null);
                $siswa->rata_rata = $nilaiPerMapel->isNotEmpty() ? round($nilaiPerMapel->avg(), 1) : null;
                $siswa->status_lulus = $siswa->rata_rata >= 75 ? 'Lulus' : ($siswa->rata_rata >= 65 ? 'Kondisional' : 'Tidak Lulus');
                return $siswa;
            });
        }

        $kelasListQuery = Kelas::orderBy('nama_kelas');
        if (!$user->isAdmin() && $user->isWaliKelas()) {
            $managedKelasIds = WaliKelas::where('guru_id', $user->guru_id)->pluck('kelas_id')->toArray();
            $kelasListQuery->whereIn('id', $managedKelasIds);
        }
        $kelasList = $kelasListQuery->get();

        $semesterOptions = $allSemesters->mapWithKeys(fn($s) => [$s->id => $s->tahunAjaran->nama . ' - ' . $s->semester])->toArray();

        return view('pages.data_rapor', compact('siswaData', 'kelasList', 'semesterOptions', 'selectedSemester', 'selectedKelas'));
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

    /**
     * Ambil data lengkap untuk laporan rapor siswa
     */
    private function getRaporData($nis, $semester_id)
    {
        // Ambil data siswa
        $siswa = Siswa::find($nis);
        if (!$siswa) {
            return ['error' => 'Data siswa tidak ditemukan.'];
        }

        // Ambil semester
        $semester = Semester::find($semester_id);
        if (!$semester) {
            return ['error' => 'Data semester tidak ditemukan.'];
        }

        // Ambil penempatan kelas siswa pada semester ini
        $kelasSiswa = RiwayatKelasSiswa::where('nis', $nis)
            ->where('semester_id', $semester_id)
            ->with(['kelas', 'nilai.pengampu.guru', 'nilai.pengampu.mapel'])
            ->first();

        if (!$kelasSiswa) {
            return ['error' => 'Data penempatan siswa pada semester ini tidak ditemukan.'];
        }

        // Ambil data wali kelas
        $kelas = Kelas::where('kode_kelas', $kelasSiswa->kode_kelas)->first();
        $waliKelas = WaliKelas::where('kelas_id', $kelas?->id)
            ->where('semester_id', $semester_id)
            ->with('guru')
            ->first();

        // Siapkan data nilai dengan detail mata pelajaran dan guru
        $nilaiDetails = $kelasSiswa->nilai->map(function ($nilai) {
            return [
                'nama_mapel' => $nilai->pengampu->mapel->nama_mapel ?? '-',
                'nama_guru' => $nilai->pengampu->guru->nama_guru ?? '-',
                'kkm' => $nilai->pengampu->kkm ?? 75,
                'tugas' => $nilai->tugas,
                'ulangan' => $nilai->ulangan,
                'uts' => $nilai->uts,
                'uas' => $nilai->uas,
                'nilai_akhir' => $nilai->nilai_akhir,
                'predikat' => Nilai::hitungPredikat($nilai->nilai_akhir),
            ];
        })->sortBy('nama_mapel')->values();

        // Hitung rata-rata nilai
        $rataRataNilai = $nilaiDetails->isNotEmpty() 
            ? round($nilaiDetails->avg('nilai_akhir'), 2) 
            : 0;

        // Tentukan status kelulusan
        $statusLulus = 'Tidak Lulus';
        if ($rataRataNilai >= 75) {
            $statusLulus = 'Lulus';
        } elseif ($rataRataNilai >= 65) {
            $statusLulus = 'Kondisional';
        }

        // Siapkan data untuk view
        return [
            'siswa' => $siswa,
            'semester' => $semester,
            'kelasSiswa' => $kelasSiswa,
            'waliKelas' => $waliKelas,
            'nilaiDetails' => $nilaiDetails,
            'rataRataNilai' => $rataRataNilai,
            'statusLulus' => $statusLulus,
        ];
    }

    /**
     * Generate dan download dokumen rapor siswa dalam format PDF
     */
    public function generateRapor($nis, $semester_id)
    {
        $data = $this->getRaporData($nis, $semester_id);
        if (isset($data['error'])) {
            return redirect()->back()->with('error', $data['error']);
        }

        // Generate PDF
        $pdf = Pdf::loadView('rapor.rapor-pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        $tahunAjaranSafe = str_replace(['/', '\\'], '-', $data['semester']->tahunAjaran->nama);
        $filename = 'Rapor_' . strtoupper($data['siswa']->nis) . '_' . $tahunAjaranSafe . '_Semester_' . $data['semester']->semester . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Generate dan stream dokumen rapor siswa dalam format PDF untuk preview
     */
    public function previewRapor($nis, $semester_id)
    {
        $data = $this->getRaporData($nis, $semester_id);
        if (isset($data['error'])) {
            return response($data['error'], 404);
        }

        // Generate PDF
        $pdf = Pdf::loadView('rapor.rapor-pdf', $data);
        $pdf->setPaper('a4', 'portrait');
        $tahunAjaranSafe = str_replace(['/', '\\'], '-', $data['semester']->tahunAjaran->nama);
        $filename = 'Rapor_' . strtoupper($data['siswa']->nis) . '_' . $tahunAjaranSafe . '_Semester_' . $data['semester']->semester . '.pdf';

        return $pdf->stream($filename, ["Attachment" => false]);
    }
}
