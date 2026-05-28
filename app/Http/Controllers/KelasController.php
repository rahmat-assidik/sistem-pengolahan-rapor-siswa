<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Guru;
use App\Models\Semester;
use App\Models\WaliKelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function showKelas(Request $request)
    {
        $semesterAktif = Semester::where('is_aktif', true)->first();

        $targetSemesterId = null;
        if ($request->filled('tahun_ajaran_id') || $request->filled('semester')) {
            $semQuery = Semester::query();
            if ($request->filled('tahun_ajaran_id')) $semQuery->where('tahun_ajaran_id', $request->tahun_ajaran_id);
            if ($request->filled('semester')) $semQuery->where('semester', $request->semester);
            $targetSemester = $semQuery->first();
            $targetSemesterId = $targetSemester?->id;
        } else {
            $targetSemesterId = $semesterAktif?->id;
        }

        $query = Kelas::query();

        if ($request->filled('search')) {
            $query->where('nama_kelas', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('tingkat')) {
            $query->where('tingkat', $request->tingkat);
        }

        // Load wali kelas - jika ada semester aktif filter by semester, jika tidak load semua
        $kelasData = $query->with(['waliKelas' => function ($q) use ($targetSemesterId) {
            if ($targetSemesterId) {
                $q->where('semester_id', $targetSemesterId)->with('guru');
            } else {
                $q->with('guru')->latest();
            }
        }])->withCount(['kelasSiswa' => function ($q) use ($targetSemesterId) {
            if ($targetSemesterId) {
                $q->where('semester_id', $targetSemesterId);
            }
        }])->orderBy('nama_kelas')->paginate(20)->withQueryString();

        $kelasData->getCollection()->transform(function ($kelas) {
            $kelas->wali    = $kelas->waliKelas->first()?->guru;
            $kelas->wali_id = $kelas->waliKelas->first()?->guru_id;
            return $kelas;
        });

        $displaySemester = $targetSemesterId ? Semester::with('tahunAjaran')->find($targetSemesterId) : $semesterAktif;
        $guruList        = Guru::where('status', 'Aktif')->orderBy('nama_guru')->get();
        $tahunAjaranList = \App\Models\TahunAjaran::orderBy('nama', 'desc')->get();

        return view('pages.data_kelas', compact('kelasData', 'guruList', 'tahunAjaranList', 'semesterAktif', 'displaySemester'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_kelas' => 'required|string|unique:kelas,kode_kelas',
            'nama_kelas' => 'required|string',
            'tingkat'    => 'required|string',
            'wali_id'    => 'nullable|exists:guru,nip',
        ]);

        $semesterAktif = Semester::where('is_aktif', true)->first();

        DB::transaction(function () use ($validated, $request, $semesterAktif) {
            $kelas = Kelas::create([
                'kode_kelas' => $validated['kode_kelas'],
                'nama_kelas' => $validated['nama_kelas'],
                'tingkat'    => $validated['tingkat'],
            ]);

            if ($request->filled('wali_id') && $semesterAktif) {
                WaliKelas::create([
                    'guru_id'     => $request->wali_id,
                    'kelas_id'    => $kelas->id,
                    'semester_id' => $semesterAktif->id,
                ]);
            }
        });

        return redirect()->back()->with('success', 'Data kelas baru berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kode_kelas' => 'required|string|unique:kelas,kode_kelas,' . $id,
            'nama_kelas' => 'required|string',
            'tingkat'    => 'required|string',
            'wali_id'    => 'nullable|exists:guru,nip',
        ]);

        $semesterAktif = Semester::where('is_aktif', true)->first();

        DB::transaction(function () use ($validated, $request, $id, $semesterAktif) {
            $kelas = Kelas::findOrFail($id);

            $kelas->update([
                'kode_kelas' => $validated['kode_kelas'],
                'nama_kelas' => $validated['nama_kelas'],
                'tingkat'    => $validated['tingkat'],
            ]);

            if ($semesterAktif) {
                WaliKelas::where('kelas_id', $kelas->id)
                         ->where('semester_id', $semesterAktif->id)
                         ->delete();

                if ($request->filled('wali_id')) {
                    WaliKelas::create([
                        'guru_id'     => $request->wali_id,
                        'kelas_id'    => $kelas->id,
                        'semester_id' => $semesterAktif->id,
                    ]);
                }
            }
        });

        return redirect()->back()->with('success', 'Data kelas berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $kelas = Kelas::findOrFail($id);

        DB::transaction(function () use ($kelas) {
            $kelasSiswaIds = $kelas->kelasSiswa()->pluck('id');
            \App\Models\Nilai::whereIn('kelas_siswa_id', $kelasSiswaIds)->delete();
            $kelas->kelasSiswa()->delete();
            $kelas->waliKelas()->delete();
            $kelas->pengampu()->delete();
            $kelas->delete();
        });

        return redirect()->back()->with('success', 'Data kelas berhasil dihapus.');
    }
}