<?php

namespace App\Http\Controllers;

use App\Models\TandaTangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TandaTanganController extends Controller
{
    public function showSettings()
    {
        $settings = TandaTangan::pluck('value', 'key')->toArray();
        return view('pages.tanda_tangan_settings', compact('settings'));
    }

    public function updateSignature(Request $request)
    {
        $request->validate([
            'kepala_sekolah_nama' => 'nullable|string|max:255',
            'kepala_sekolah_ttd' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->filled('kepala_sekolah_nama')) {
            TandaTangan::updateOrCreate(
                ['key' => 'kepala_sekolah_nama'],
                ['value' => $request->kepala_sekolah_nama]
            );
        }

        if ($request->hasFile('kepala_sekolah_ttd')) {
            $path = $request->file('kepala_sekolah_ttd')->store('signatures', 'public');
            
            TandaTangan::updateOrCreate(
                ['key' => 'kepala_sekolah_ttd_path'],
                ['value' => $path]
            );
        }

        return redirect()->back()->with('success', 'Pengaturan Tanda Tangan berhasil diperbarui.');
    }
}
