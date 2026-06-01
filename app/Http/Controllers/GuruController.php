<?php

namespace App\Http\Controllers;

use App\Models\Guru;
use Illuminate\Http\Request;

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

public function store(Request $request)
{
    $request->validate([
        'nip' => 'required|unique:guru,nip',
        'nama_guru' => 'required',
        'email' => 'required|email|unique:guru,email',
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
        // NIP tidak diupdate
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