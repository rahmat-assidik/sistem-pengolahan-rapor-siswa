<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TahunAjaran;
use App\Http\Requests\Api\StoreTahunAjaranRequest;
use App\Http\Requests\Api\UpdateTahunAjaranRequest;
use Illuminate\Http\JsonResponse;

class TahunAjaranController extends Controller
{
    /**
     * Ambil daftar semua tahun ajaran
     */
    public function index(): JsonResponse
    {
        $tahunAjaran = TahunAjaran::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar tahun ajaran berhasil diambil',
            'data' => $tahunAjaran
        ]);
    }

    /**
     * Simpan tahun ajaran baru
     */
    public function store(StoreTahunAjaranRequest $request): JsonResponse
    {
        // Jika is_aktif = true, nonaktifkan tahun ajaran lainnya
        if ($request->is_aktif ?? false) {
            TahunAjaran::where('is_aktif', true)->update(['is_aktif' => false]);
        }
        
        $tahunAjaran = TahunAjaran::create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Tahun ajaran berhasil ditambahkan',
            'data' => $tahunAjaran
        ], 201);
    }

    /**
     * Ambil detail tahun ajaran
     */
    public function show(TahunAjaran $tahunAjaran): JsonResponse
    {
        $tahunAjaranWithRelations = $tahunAjaran->load('semester');
        
        return response()->json([
            'success' => true,
            'message' => 'Detail tahun ajaran berhasil diambil',
            'data' => $tahunAjaranWithRelations
        ]);
    }

    /**
     * Update data tahun ajaran
     */
    public function update(UpdateTahunAjaranRequest $request, TahunAjaran $tahunAjaran): JsonResponse
    {
        // Jika is_aktif = true, nonaktifkan tahun ajaran lainnya
        if (($request->is_aktif ?? false) && !$tahunAjaran->is_aktif) {
            TahunAjaran::where('is_aktif', true)->update(['is_aktif' => false]);
        }
        
        $tahunAjaran->update($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Data tahun ajaran berhasil diperbarui',
            'data' => $tahunAjaran
        ]);
    }

    /**
     * Hapus tahun ajaran
     */
    public function destroy(TahunAjaran $tahunAjaran): JsonResponse
    {
        // Cek apakah tahun ajaran memiliki semester
        if ($tahunAjaran->semester()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Tahun ajaran tidak bisa dihapus karena masih memiliki semester'
            ], 422);
        }
        
        $tahunAjaran->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Tahun ajaran berhasil dihapus'
        ]);
    }
}
