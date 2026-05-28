<?php

namespace App\Http\Controllers;

use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;  // ← ini yang kurang

class TahunAjaranController extends Controller
{
    /**
     * Menampilkan semua data tahun ajaran.
     */
    public function index()
    {
        $tahunAjaran = TahunAjaran::latest()->get();

        return view('tahun_ajaran.index', compact('tahunAjaran'));
    }

    /**
     * Menyimpan data tahun ajaran.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|unique:tahun_ajaran,nama',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_aktif' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request) {

            if ($request->is_aktif) {
                TahunAjaran::query()->update(['is_aktif' => false]);
            }

            TahunAjaran::create([
                'nama'            => $request->nama,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_aktif'        => $request->is_aktif ?? false,
            ]);
        });

        return redirect()->back()->with('success', 'Tahun ajaran berhasil ditambahkan.');
    }

    /**
     * Update data tahun ajaran.
     */
    public function update(Request $request, $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        $request->validate([
            'nama'            => 'required|unique:tahun_ajaran,nama,' . $id,
            'tanggal_mulai'   => 'required|date',
            'tanggal_selesai' => 'required|date|after:tanggal_mulai',
            'is_aktif'        => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($request, $tahunAjaran) {

            if ($request->is_aktif) {
                TahunAjaran::query()->update(['is_aktif' => false]);
            }

            $tahunAjaran->update([
                'nama'            => $request->nama,
                'tanggal_mulai'   => $request->tanggal_mulai,
                'tanggal_selesai' => $request->tanggal_selesai,
                'is_aktif'        => $request->is_aktif ?? false,
            ]);
        });

        return redirect()->back()->with('success', 'Tahun ajaran berhasil diperbarui.');
    }

    /**
     * Hapus data tahun ajaran.
     */
    public function destroy($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);

        DB::transaction(function () use ($tahunAjaran) {
            $tahunAjaran->semester()->delete();
            $tahunAjaran->delete();
        });

        return redirect()->back()->with('success', 'Tahun ajaran berhasil dihapus.');
    }
}