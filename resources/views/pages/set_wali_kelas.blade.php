@extends('layouts.app')
@section('title', 'Set Wali Kelas')

@section('content')
    <div class="max-w-full">
        {{-- Tabs --}}
        <div class="flex gap-4 mb-6 border-b border-gray-200">
            <a href="{{ route('pembagian_kelas') }}" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-900">Pembagian Kelas</a>
            <a href="{{ route('set_wali_kelas') }}" class="px-4 py-2 text-sm font-semibold text-gray-900 border-b-2 border-gray-900">Set Wali Kelas</a>
        </div>

        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            <div class="p-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-lg font-bold text-gray-900">Set Wali Kelas - Semester {{ $semesterAktif ? $semesterAktif->semester . ' ' . $semesterAktif->tahunAjaran->nama : 'Tidak Aktif' }}</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900 border-b border-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Wali Kelas Saat Ini</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Set Wali Kelas</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($kelasData as $kelas)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $kelas->nama_kelas }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $kelas->waliKelas->first()?->guru->nama_guru ?? '-' }}</td>
                            <td class="px-6 py-4">
                                <form action="{{ route('set_wali_kelas.update') }}" method="POST" class="flex gap-2">
                                    @csrf
                                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                                    <select name="guru_id" class="px-3 py-2 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none w-64" required autocomplete="off">
                                        <option value="0" @if(empty($kelas->waliKelas->first()?->guru_id)) selected @endif>-- Pilih Guru --</option>
                                        @foreach($guruList as $guru)
                                            <option value="{{ $guru->nip }}" @if($kelas->waliKelas->first()?->guru_id == $guru->nip) selected @endif>
                                                {{ $guru->nama_guru }} 
                                            </option>
                                        @endforeach
                                    </select>
                                    <button type="submit" class="px-4 py-2 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800">Simpan</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
