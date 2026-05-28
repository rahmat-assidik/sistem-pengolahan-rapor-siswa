<?php

namespace App\Http\Controllers;

use App\Models\Mapel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapelController extends Controller
{
// menampilkan data mapel dengan fitur pencarian, filter kelompok dan status
    public function index(Request $request)
    {
        $query = Mapel::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_mapel', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_mapel', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('kelompok')) {
            $query->where('kelompok', $request->kelompok);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $mapelData = $query->orderBy('nama_mapel')
            ->paginate(20)
            ->withQueryString();

        return view('pages.data_mapel', compact('mapelData'));
    }

// strore data mapel
    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required',
            'nama_mapel' => 'required',
            'kelompok'   => 'required',
            'status'     => 'required',
        ]);

        Mapel::create([
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel,
            'kelompok'   => $request->kelompok,
            'status'     => $request->status,
        ]);

        return redirect()->back()
            ->with('success', 'Data mata pelajaran berhasil ditambahkan.');
    }

// edit data mapel
    public function edit($kode_mapel)
    {
        $mapel = Mapel::findOrFail($kode_mapel);

        return view('pages.edit_mapel', compact('mapel'));
    }

//    update data mapel
    public function update(Request $request, $kode_mapel)
    {
        $request->validate([
            'kode_mapel' => 'required',
            'nama_mapel' => 'required',
            'kelompok'   => 'required',
            'status'     => 'required',
        ]);

        $mapel = Mapel::findOrFail($kode_mapel);

        $mapel->update([
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel,
            'kelompok'   => $request->kelompok,
            'status'     => $request->status,
        ]);

        return redirect()->route('data_mapel')
            ->with('success', 'Data mata pelajaran berhasil diupdate.');
    }

// delete data mapel beserta pengampunya
    public function destroy($kode_mapel)
    {
        $mapel = Mapel::findOrFail($kode_mapel);

        DB::transaction(function () use ($mapel) {
            $mapel->delete();
        });

        return redirect()->back()
            ->with('success', 'Data mata pelajaran berhasil dihapus.');
    }
}