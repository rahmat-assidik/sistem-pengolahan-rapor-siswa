<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruController extends Controller
{
    public function showGuru(Request $request)
    {
        $query = Guru::query();

        // Filter Search
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('nama_guru', 'like', '%' . $request->search . '%')
                  ->orWhere('nip', 'like', '%' . $request->search . '%');
            });
        }

        // Filter Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $guruData = $query->with('user')->orderBy('nama_guru')->paginate(20)->withQueryString();

        return view('pages.data_guru', compact('guruData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nip' => 'required|string|unique:guru',
            'nama_guru' => 'required|string',
            'jenis_kelamin' => 'required|in:Laki-laki,Perempuan',
            'email' => 'required|email|unique:user,email',
            'no_hp' => 'nullable|string',
            'status' => 'required|in:Aktif,Tidak Aktif',
        ]);

        // 1. Simpan Data Guru
        $guru = Guru::create([
            'nip' => $request->nip,
            'nama_guru' => $request->nama_guru,
            'jenis_kelamin' => $request->jenis_kelamin,
            'no_hp' => $request->no_hp,
            'status' => $request->status,
        ]);

        // 2. Buat Akun User secara Otomatis
        \App\Models\User::create([
            'nama' => $guru->nama_guru,
            'username' => $guru->nip, // Username default menggunakan NIP
            'email' => $request->email,
            'password' => \Illuminate\Support\Facades\Hash::make($guru->nip), // Password default menggunakan NIP
            'role' => 'guru',
            'guru_id' => $guru->id, // Hubungkan dengan data guru
        ]);

        return redirect()->back()->with('success', 'Data guru berhasil ditambahkan dan akun berhasil disiapkan.');
    }
public function destroy($id)
{
    $guru = Guru::findOrFail($id);

    \Illuminate\Support\Facades\DB::transaction(function () use ($guru) {

        // Hapus semua data pengampu
        $guru->pengampu()->delete();

        // Hapus akun user jika ada
        if ($guru->user) {
            $guru->user()->delete();
        }

        // Hapus data guru
        $guru->delete();
    });

    return redirect()->back()
        ->with('success', 'Data guru berhasil dihapus.');
}
}