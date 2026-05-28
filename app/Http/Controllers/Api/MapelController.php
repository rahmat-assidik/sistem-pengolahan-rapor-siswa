<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mapel;
use App\Http\Requests\Api\StoreMapelRequest;
use App\Http\Requests\Api\UpdateMapelRequest;
use Illuminate\Http\JsonResponse;

class MapelController extends Controller
{
    /**
     * Ambil daftar semua mata pelajaran
     */
    public function index(): JsonResponse
    {
        $mapel = Mapel::all();

        return response()->json([
            'success' => true,
            'message' => 'Daftar mata pelajaran berhasil diambil',
            'data' => $mapel
        ]);
    }

    /**
     * Simpan mata pelajaran baru
     */
    public function store(StoreMapelRequest $request): JsonResponse
    {
        $mapel = Mapel::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil ditambahkan',
            'data' => $mapel
        ], 201);
    }

    /**
     * Ambil detail mata pelajaran
     */
    public function show(Mapel $mapel): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Detail mata pelajaran berhasil diambil',
            'data' => $mapel
        ]);
    }

    /**
     * Update data mata pelajaran
     */
    public function update(UpdateMapelRequest $request, Mapel $mapel): JsonResponse
    {
        $mapel->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Data mata pelajaran berhasil diperbarui',
            'data' => $mapel
        ]);
    }

    /**
     * Hapus mata pelajaran
     */
    public function destroy(Mapel $mapel): JsonResponse
    {
        if ($mapel->pengampu()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Mata pelajaran tidak bisa dihapus karena masih memiliki data pengampu'
            ], 422);
        }

        $mapel->delete();

        return response()->json([
            'success' => true,
            'message' => 'Mata pelajaran berhasil dihapus'
        ]);
    }
}
