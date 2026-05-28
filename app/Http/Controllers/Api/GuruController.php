<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Http\Requests\Api\StoreGuruRequest;
use App\Http\Requests\Api\UpdateGuruRequest;
use Illuminate\Http\JsonResponse;

class GuruController extends Controller
{
    /**
     * Ambil daftar semua guru
     */
    public function index(): JsonResponse
    {
        $guru = Guru::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar guru berhasil diambil',
            'data' => $guru
        ]);
    }

    /**
     * Simpan guru baru
     */
    public function store(StoreGuruRequest $request): JsonResponse
    {
        $guru = Guru::create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Guru berhasil ditambahkan',
            'data' => $guru
        ], 201);
    }

    /**
     * Ambil detail guru
     */
    public function show(Guru $guru): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail guru berhasil diambil',
            'data' => $guru
        ]);
    }

    /**
     * Update data guru
     */
    public function update(UpdateGuruRequest $request, Guru $guru): JsonResponse
    {
        $guru->update($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Data guru berhasil diperbarui',
            'data' => $guru
        ]);
    }

    /**
     * Hapus guru
     */
    public function destroy(Guru $guru): JsonResponse
    {
        // Cek apakah guru memiliki akun user atau data pengampu
        if ($guru->user || $guru->pengampu()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Guru tidak bisa dihapus karena masih memiliki akun atau data pengampu'
            ], 422);
        }
        
        $guru->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Guru berhasil dihapus'
        ]);
    }
}
