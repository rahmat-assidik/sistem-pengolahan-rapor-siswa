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

    public function importFromSemester(Request $request)
    {
        $request->validate([
            'source_semester_id' => 'required|exists:semester,id',
        ]);

        $semesterAktif = Semester::where('is_aktif', true)->first();
        if (!$semesterAktif) {
            return redirect()->back()->with('error', 'Tidak ada semester aktif.');
        }

        if ($request->source_semester_id == $semesterAktif->id) {
            return redirect()->back()->with('error', 'Tidak dapat mengimpor dari semester yang sama.');
        }

        $pengampusSebelumnya = Pengampu::where('semester_id', $request->source_semester_id)->get();

        if ($pengampusSebelumnya->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data pengampu di semester yang dipilih.');
        }

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            foreach ($pengampusSebelumnya as $p) {
                $exists = Pengampu::where('guru_id', $p->guru_id)
                    ->where('mapel_id', $p->mapel_id)
                    ->where('kelas_id', $p->kelas_id)
                    ->where('semester_id', $semesterAktif->id)
                    ->exists();

                if (!$exists) {
                    Pengampu::create([
                        'guru_id'     => $p->guru_id,
                        'mapel_id'    => $p->mapel_id,
                        'kelas_id'    => $p->kelas_id,
                        'kkm'         => $p->kkm,
                        'status'      => $p->status,
                        'semester_id' => $semesterAktif->id,
                    ]);
                }
            }
            \Illuminate\Support\Facades\DB::commit();
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengimpor data: ' . $e->getMessage());
        }

        return redirect()->back()->with('success', 'Data pengampu berhasil diimpor.');
    }

    public function destroy($id)
    {
        $pengampu = Pengampu::findOrFail($id);
        $pengampu->delete();

        return redirect()->back()->with('success', 'Data pengampu berhasil dihapus.');
    }
}