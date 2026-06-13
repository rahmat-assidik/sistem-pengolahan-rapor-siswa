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

        if ($semesterAktif) {
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

        return view('pages.pengampu', compact('pengampus', 'gurus', 'mapels', 'kelas', 'semesterAktif'));
    }

    public function store(Request $request)
    {
        $semesterAktif = Semester::where('is_aktif', true)->first();

        if (!$semesterAktif) {
            return redirect()->back()->with('error', 'Tidak ada semester aktif yang diset.');
        }

        $request->validate([
            'guru_id'     => 'required|exists:guru,nip',
            'mapel_id'    => 'required|exists:mapel,kode_mapel',
            'kelas_id'    => 'required|exists:kelas,id',
        ]);

        $exists = Pengampu::where('guru_id', $request->guru_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('semester_id', $semesterAktif->id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data pengampu dengan kombinasi guru, mapel, dan kelas untuk semester ini sudah ada.');
        }

        Pengampu::create([
            'guru_id'     => $request->guru_id,
            'mapel_id'    => $request->mapel_id,
            'kelas_id'    => $request->kelas_id,
            'semester_id' => $semesterAktif->id,
        ]);

        return redirect()->back()->with('success', 'Pengampu berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $semesterAktif = Semester::where('is_aktif', true)->first();

        if (!$semesterAktif) {
            return redirect()->back()->with('error', 'Tidak ada semester aktif yang diset.');
        }

        $request->validate([
            'guru_id'     => 'required|exists:guru,nip',
            'mapel_id'    => 'required|exists:mapel,kode_mapel',
            'kelas_id'    => 'required|exists:kelas,id',
        ]);

        $pengampu = Pengampu::findOrFail($id);

        $exists = Pengampu::where('guru_id', $request->guru_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('kelas_id', $request->kelas_id)
            ->where('semester_id', $semesterAktif->id)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Data pengampu dengan kombinasi guru, mapel, dan kelas untuk semester ini sudah ada.');
        }

        $pengampu->update([
            'guru_id'     => $request->guru_id,
            'mapel_id'    => $request->mapel_id,
            'kelas_id'    => $request->kelas_id,
            'semester_id' => $semesterAktif->id,
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