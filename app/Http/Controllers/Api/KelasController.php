<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Http\Requests\Api\StoreKelasRequest;
use App\Http\Requests\Api\UpdateKelasRequest;
use Illuminate\Http\JsonResponse;

class KelasController extends Controller
{
    /**
     * Ambil daftar semua kelas
     */
    public function index(): JsonResponse
    {
        $kelas = Kelas::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar kelas berhasil diambil',
            'data' => $kelas
        ]);
    }

    /**
     * Simpan kelas baru
     */
    public function store(StoreKelasRequest $request): JsonResponse
    {
        $kelas = Kelas::create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil ditambahkan',
            'data' => $kelas
        ], 201);
    }

    /**
     * Ambil detail kelas
     */
    public function show(Kelas $kelas): JsonResponse
    {
        $kelasWithRelations = $kelas->load('siswa', 'waliKelas', 'pengampu');
        
        return response()->json([
            'success' => true,
            'message' => 'Detail kelas berhasil diambil',
            'data' => $kelasWithRelations
        ]);
    }

    /**
     * Update data kelas
     */
    public function update(UpdateKelasRequest $request, Kelas $kelas): JsonResponse
    {
        $kelas->update($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Data kelas berhasil diperbarui',
            'data' => $kelas
        ]);
    }

    /**
     * Hapus kelas
     */
    public function destroy(Kelas $kelas): JsonResponse
    {
        // Cek apakah kelas memiliki siswa atau wali kelas
        if ($kelas->siswa()->exists() || $kelas->waliKelas()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Kelas tidak bisa dihapus karena masih memiliki siswa atau wali kelas'
            ], 422);
        }
        
        $kelas->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Kelas berhasil dihapus'
        ]);
    }
}
