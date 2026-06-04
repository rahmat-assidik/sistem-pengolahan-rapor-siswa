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

        $kelasData = $query->withCount(['kelasSiswa' => function ($q) use ($targetSemesterId) {
            if ($targetSemesterId) {
                $q->where('semester_id', $targetSemesterId);
            }
        }])->orderBy('nama_kelas')->paginate(20)->withQueryString();

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
            'tingkat'    => 'required|string|in:X,XI,XII',
        ]);

        DB::transaction(function () use ($validated) {
            Kelas::create([
                'kode_kelas' => $validated['kode_kelas'],
                'nama_kelas' => $validated['nama_kelas'],
                'tingkat'    => $validated['tingkat'],
            ]);
        });

        return redirect()->back()->with('success', 'Data kelas baru berhasil disimpan.');
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'kode_kelas' => 'required|string|unique:kelas,kode_kelas,' . $id,
            'nama_kelas' => 'required|string',
            'tingkat'    => 'required|string|in:X,XI,XII',
        ]);

        DB::transaction(function () use ($validated, $id) {
            $kelas = Kelas::findOrFail($id);

            $kelas->update([
                'kode_kelas' => $validated['kode_kelas'],
                'nama_kelas' => $validated['nama_kelas'],
                'tingkat'    => $validated['tingkat'],
            ]);
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