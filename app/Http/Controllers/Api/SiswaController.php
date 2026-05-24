<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Http\Requests\Api\StoreSiswaRequest;
use App\Http\Requests\Api\UpdateSiswaRequest;
use Illuminate\Http\JsonResponse;

class SiswaController extends Controller
{
    /**
     * Ambil daftar semua siswa
     */
    public function index(): JsonResponse
    {
        $siswa = Siswa::all();
        
        return response()->json([
            'success' => true,
            'message' => 'Daftar siswa berhasil diambil',
            'data' => $siswa
        ]);
    }

    /**
     * Simpan siswa baru
     */
    public function store(StoreSiswaRequest $request): JsonResponse
    {
        $siswa = Siswa::create($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil ditambahkan',
            'data' => $siswa
        ], 201);
    }

    /**
     * Ambil detail siswa
     */
    public function show(Siswa $siswa): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail siswa berhasil diambil',
            'data' => $siswa
        ]);
    }

    /**
     * Update data siswa
     */
    public function update(UpdateSiswaRequest $request, Siswa $siswa): JsonResponse
    {
        $siswa->update($request->validated());
        
        return response()->json([
            'success' => true,
            'message' => 'Data siswa berhasil diperbarui',
            'data' => $siswa
        ]);
    }

    /**
     * Hapus siswa
     */
    public function destroy(Siswa $siswa): JsonResponse
    {
        $siswa->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Siswa berhasil dihapus'
        ]);
    }
}
