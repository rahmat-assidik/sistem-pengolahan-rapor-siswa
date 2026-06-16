<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $query = Guru::query();

        if ($request->search) {
            $query->where('nip', 'like', '%' . $request->search . '%')
                  ->orWhere('nama_guru', 'like', '%' . $request->search . '%');
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $guruData = $query->paginate(10);

        return view('pages.data_guru', compact('guruData'));
    }

    /**
     * Menampilkan halaman untuk membuat akun guru
     */
    public function manageAkunGuru(Request $request)
    {
        $query = Guru::leftJoin('user', 'guru.nip', '=', 'user.guru_id')
                      ->select('guru.*', 'user.username')
                      ->distinct();

        if ($request->search) {
            $query->where('guru.nip', 'like', '%' . $request->search . '%')
                  ->orWhere('guru.nama_guru', 'like', '%' . $request->search . '%');
        }

        // Filter berdasarkan status akun
        if ($request->akun_status === 'sudah') {
            $query->whereNotNull('user.username');
        } elseif ($request->akun_status === 'belum') {
            $query->whereNull('user.username');
        }

        if ($request->status) {
            $query->where('guru.status', $request->status);
        }

        $guruData = $query->paginate(10);

        return view('pages.akun_guru', compact('guruData'));
    }

    /**
     * Membuat akun guru baru
     */
    public function storeAkunGuru(Request $request)
    {
        $request->validate([
            'nip' => 'required|exists:guru,nip|unique:user,guru_id',
        ]);

        $guru = Guru::findOrFail($request->nip);

        // Cek apakah akun sudah ada
        if ($guru->user) {
            return redirect()->route('akun_guru')->with('error', 'Guru ini sudah memiliki akun');
        }

        // Buat user dengan password default (NIP)
        User::create([
            'username' => $guru->nip,
            'nama' => $guru->nama_guru,
            'email' => $guru->email,
            'password' => Hash::make($guru->nip),
            'role' => 'guru',
            'guru_id' => $guru->nip,
        ]);

        return redirect()->route('akun_guru')->with('success', 'Akun guru berhasil dibuat. Password default: ' . $guru->nip);
    }

public function store(Request $request)
{
    $request->validate([
    'nip' => [
        'required',
        'digits:4',
        'regex:/^[0-9]+$/',
        'unique:guru,nip'
    ],
    'nama_guru' => 'required',
    'email' => 'required|email|unique:guru,email',
], [
    'nip.required' => 'NIP wajib diisi.',
    'nip.digits'   => 'NIP harus terdiri dari 4 digit angka.',
    'nip.regex'    => 'NIP hanya boleh berisi angka.',
    'nip.unique'   => 'NIP sudah digunakan.',

    'nama_guru.required' => 'Nama guru wajib diisi.',

    'email.required' => 'Email wajib diisi.',
    'email.email'    => 'Format email tidak valid.',
    'email.unique'   => 'Email sudah digunakan.',
]);

    Guru::create([
        'nip'          => $request->nip,
        'nama_guru'    => $request->nama_guru,
        'email'        => $request->email,
        'jenis_kelamin'=> $request->jenis_kelamin,
        'no_hp'        => $request->no_hp,
        'status'       => $request->status,
    ]);

    return redirect()->route('data_guru')->with('success', 'Data guru berhasil ditambahkan');
}

public function update(Request $request, $nip)
{
    $guru = Guru::findOrFail($nip);

    $request->validate([
        'nama_guru' => 'required',
        'email' => 'required|email|unique:guru,email,' . $nip . ',nip',
    ]);

    $guru->update([
        'nama_guru'    => $request->nama_guru,
        'email'        => $request->email,
        'jenis_kelamin'=> $request->jenis_kelamin,
        'no_hp'        => $request->no_hp,
        'status'       => $request->status,
    ]);
    

    return redirect()->route('data_guru')->with('success', 'Data guru berhasil diupdate');
}

    public function destroy($nip)
    {
        $guru = Guru::findOrFail($nip);

        $guru->delete();

        return redirect()->route('data_guru')
            ->with('success', 'Data guru berhasil dihapus');
    }
}