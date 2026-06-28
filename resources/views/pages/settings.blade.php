@extends('layouts.app')
@section('title', 'Data Bobot Nilai')

@section('content')
<div class="w-full">
    <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900 border-b border-gray-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Guru</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Tugas (%)</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Ulangan (%)</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">UTS (%)</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">UAS (%)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($specificSettings as $setting)
                        <tr class="transition-colors">
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $setting->pengampu->mapel->nama_mapel }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $setting->pengampu->guru->nama_guru }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $setting->pengampu->kelas->nama_kelas }}</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-700">{{ $setting->bobot_tugas }}%</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-700">{{ $setting->bobot_ulangan }}%</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-700">{{ $setting->bobot_uts }}%</td>
                            <td class="px-6 py-4 text-sm text-center text-gray-700">{{ $setting->bobot_uas }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-gray-400 font-medium">Belum ada bobot khusus yang diatur oleh guru.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
