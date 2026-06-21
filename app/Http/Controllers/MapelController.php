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

// store data mapel
    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required|unique:mapel,kode_mapel',
            'nama_mapel' => 'required|string|max:255|unique:mapel,nama_mapel',
            'kelompok'   => 'required|in:Wajib,Peminatan,Muatan Lokal',
            'status'     => 'required|in:Aktif,Tidak Aktif',
        ], [
            'kode_mapel.required' => 'Kode mapel wajib diisi.',
            'kode_mapel.unique'   => 'Kode mapel ini sudah digunakan, silakan pakai kode lain.',
            'nama_mapel.required' => 'Nama mapel wajib diisi.',
            'nama_mapel.max'      => 'Nama mapel maksimal 255 karakter.',
            'nama_mapel.unique'   => 'Nama mata pelajaran ini sudah digunakan, silakan pakai nama lain.',
            'kelompok.required'   => 'Kelompok wajib dipilih.',
            'kelompok.in'         => 'Kelompok harus salah satu dari: Wajib, Peminatan, atau Muatan Lokal.',
            'status.required'     => 'Status wajib dipilih.',
            'status.in'           => 'Status harus salah satu dari: Aktif atau Tidak Aktif.',
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
            'nama_mapel' => 'required|string|max:30|unique:mapel,nama_mapel,' . $kode_mapel . ',kode_mapel',
            'kelompok'   => 'required|in:Wajib,Peminatan,Muatan Lokal',
            'status'     => 'required|in:Aktif,Tidak Aktif',
        ], [
            'kode_mapel.required' => 'Kode mapel wajib diisi.',
            'nama_mapel.required' => 'Nama mapel wajib diisi.',
            'nama_mapel.max'      => 'Nama mapel maksimal 30 karakter.',
            'kelompok.required'   => 'Kelompok wajib dipilih.',
            'kelompok.in'         => 'Kelompok harus salah satu dari: Wajib, Peminatan, atau Muatan Lokal.',
            'status.required'     => 'Status wajib dipilih.',
            'status.in'           => 'Status harus salah satu dari: Aktif atau Tidak Aktif.',
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

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv'
        ]);

        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\MapelImport, $request->file('file'));

        return redirect()->back()->with('success', 'Data mata pelajaran berhasil diimport.');
    }
}