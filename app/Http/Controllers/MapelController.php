<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapelController extends Controller
{
    public function showMapel(Request $request)
    {
        $query = Mapel::query();

        if ($request->filled('search')) {
            $query->where('nama_mapel', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_mapel', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->kelompok);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $mapelData = $query->orderBy('nama_mapel')->paginate(20)->withQueryString();

        return view('pages.data_mapel', compact('mapelData'));
    }
// Delate method 
    public function destroy($id)
{
    $mapel = Mapel::findOrFail($id);

    \Illuminate\Support\Facades\DB::transaction(function () use ($mapel) {

        // Hapus semua data pengampu terkait mapel
        $mapel->pengampu()->delete();

        // Hapus data mapel
        $mapel->delete();
    });

    return redirect()->back()
        ->with('success', 'Data mata pelajaran berhasil dihapus.');
}
public function store(Request $request)
{
    $request->validate([
        'kode_mapel' => 'required',
        'nama_mapel' => 'required',
        'kelompok' => 'required',
        'status' => 'required',
    ]);

    Mapel::create([
        'kode_mapel' => $request->kode_mapel,
        'nama_mapel' => $request->nama_mapel,
        'kelompok' => $request->kelompok,
        'status' => $request->status,
    ]);

    return redirect()->back()
        ->with('success', 'Data mata pelajaran berhasil ditambahkan.');
}
}
