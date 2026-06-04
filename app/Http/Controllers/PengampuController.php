<?php

namespace App\Http\Controllers;

use App\Models\Pengampu;
use App\Models\Guru;
use App\Models\Mapel;
use App\Models\Kelas;
use App\Models\Semester;
use Illuminate\Http\Request;

class PengampuController extends Controller
{
    public function showPengampu(Request $request)
    {
        $semesterAktif = Semester::where('is_aktif', true)->first();

        $query = Pengampu::query()->with(['guru', 'mapel', 'kelas', 'semester.tahunAjaran']);

        if ($request->filled('tahun_ajaran_id') || $request->filled('semester')) {
            $query->whereHas('semester', function($q) use ($request) {
                if ($request->filled('tahun_ajaran_id')) $q->where('tahun_ajaran_id', $request->tahun_ajaran_id);
                if ($request->filled('semester')) $q->where('semester', $request->semester);
            });
        } else if ($semesterAktif) {
            $query->where('semester_id', $semesterAktif->id);
        }

        if ($request->filled('mapel_id')) {
            $query->where('mapel_id', $request->mapel_id);
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $pengampus = $query->orderBy('id')->paginate(20)->withQueryString();

        $gurus = Guru::where('status', 'Aktif')->orderBy('nama_guru')->get();
        $mapels = Mapel::orderBy('nama_mapel')->get();
        $kelas = Kelas::orderBy('nama_kelas')->get();
        $tahunAjaranList = \App\Models\TahunAjaran::orderBy('nama', 'desc')->get();
        $semesters = Semester::with('tahunAjaran')->orderByDesc('id')->get();

        return view('pages.pengampu', compact('pengampus', 'gurus', 'mapels', 'kelas', 'tahunAjaranList', 'semesterAktif', 'semesters'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id'     => 'required|exists:guru,nip',
            'mapel_id'    => 'required|exists:mapel,kode_mapel',
            'kelas_id'    => 'required|exists:kelas,id',
            'semester_id' => 'required|exists:semester,id',
            'kkm'         => 'nullable|integer|min:0|max:100',
        ]);

        $exists = Pengampu::where('guru_id', $request->guru_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('semester_id', $request->semester_id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data pengampu dengan kombinasi guru, mapel, kelas, dan semester ini sudah ada.');
        }

        Pengampu::create([
            'guru_id'     => $request->guru_id,
            'mapel_id'    => $request->mapel_id,
            'kelas_id'    => $request->kelas_id,
            'semester_id' => $request->semester_id,
            'kkm'         => $request->kkm ?? 75,
        ]);

        return redirect()->back()->with('success', 'Pengampu berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'guru_id'     => 'required|exists:guru,nip',
            'mapel_id'    => 'required|exists:mapel,kode_mapel',
            'kelas_id'    => 'required|exists:kelas,id',
            'semester_id' => 'required|exists:semester,id',
            'kkm'         => 'nullable|integer|min:0|max:100',
        ]);

        $pengampu = Pengampu::findOrFail($id);

        $exists = Pengampu::where('guru_id', $request->guru_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('semester_id', $request->semester_id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data pengampu dengan kombinasi guru, mapel, kelas, dan semester ini sudah ada.');
        }

        $pengampu->update([
            'guru_id'     => $request->guru_id,
            'mapel_id'    => $request->mapel_id,
            'kelas_id'    => $request->kelas_id,
            'semester_id' => $request->semester_id,
            'kkm'         => $request->kkm ?? 75,
        ]);

        return redirect()->back()->with('success', 'Data pengampu berhasil diupdate.');
    }

    public function destroy($id)
    {
        $pengampu = Pengampu::findOrFail($id);
        $pengampu->delete();

        return redirect()->back()->with('success', 'Data pengampu berhasil dihapus.');
    }
}