<?php

namespace App\Http\Controllers;

use App\Models\BobotNilai;
use Illuminate\Http\Request;

class BobotNilaiController extends Controller
{
    public function index()
    {
        // Load with pengampu, and nested relations for display efficiency
        $specificSettings = BobotNilai::whereNotNull('pengampu_id')
            ->with(['pengampu.guru', 'pengampu.mapel', 'pengampu.kelas'])
            ->get();

        return view('pages.settings', compact('specificSettings'));
    }


    public function indexGuru()
    {
        $guruId = auth()->user()->guru_id;
        $pengampus = \App\Models\Pengampu::where('guru_id', $guruId)
            ->with(['mapel', 'kelas'])
            ->get();

        $bobots = \App\Models\BobotNilai::whereIn('pengampu_id', $pengampus->pluck('id'))->get()->keyBy('pengampu_id');

        return view('pages.bobot_nilai_guru', compact('pengampus', 'bobots'));
    }

    public function updateGuru(Request $request, $pengampuId)
    {
        $pengampu = \App\Models\Pengampu::findOrFail($pengampuId);

        // Authorization: Ensure the teacher manages this pengampu
        if ($pengampu->guru_id !== auth()->user()->guru_id) {
            return redirect()->back()->with('error', 'Anda tidak berwenang mengubah bobot nilai mata pelajaran ini.');
        }

        $request->validate([
            'bobot_tugas'   => 'required|integer|min:0|max:100',
            'bobot_ulangan' => 'required|integer|min:0|max:100',
            'bobot_uts'     => 'required|integer|min:0|max:100',
            'bobot_uas'     => 'required|integer|min:0|max:100',
        ]);

        $total = $request->bobot_tugas + $request->bobot_ulangan + $request->bobot_uts + $request->bobot_uas;

        if ($total != 100) {
            return redirect()->back()->with('error', "Total bobot harus berjumlah 100%. Saat ini: {$total}%");
        }

        \App\Models\BobotNilai::updateOrCreate(
            ['pengampu_id' => $pengampuId],
            $request->only(['bobot_tugas', 'bobot_ulangan', 'bobot_uts', 'bobot_uas'])
        );

        return redirect()->back()->with('success', 'Bobot nilai berhasil diperbarui.');
    }
}
