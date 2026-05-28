<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Siswa;

class ArsipSiswaController extends Controller
{
    public function showArsipSiswa(Request $request)
    {
        $query = Siswa::query();

        // Filter Search (Nama atau NIS)
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_siswa', 'like', '%' . $request->search . '%')
                  ->orWhere('nis', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Angkatan
        if ($request->filled('angkatan')) {
            $query->where('angkatan', $request->angkatan);
        }

        // Filter Status (only from non-active options)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $siswaData = $query->with(['kelasSiswa.semester.tahunAjaran', 'kelasSiswa.kelas'])
                           ->orderBy('nama_siswa')
                           ->paginate(20)
                           ->withQueryString();

        // Ambil daftar angkatan unik untuk filter
        $angkatanList = Siswa::whereNotNull('angkatan')->distinct()->orderBy('angkatan', 'desc')->pluck('angkatan', 'angkatan')->toArray();

        return view('pages.arsip_siswa', compact('siswaData', 'angkatanList'));
    }
}
