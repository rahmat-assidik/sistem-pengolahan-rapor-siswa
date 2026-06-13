<?php

namespace App\Http\Controllers;

use App\Models\BobotNilai;
use Illuminate\Http\Request;

class BobotNilaiController extends Controller
{
    public function index()
    {
        $setting = BobotNilai::current();
        return view('pages.settings', compact('setting'));
    }

    public function update(Request $request)
    {
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

        $setting = BobotNilai::current();
        $setting->update($request->all());

        return redirect()->back()->with('success', 'Bobot nilai berhasil diperbarui.');
    }
}
