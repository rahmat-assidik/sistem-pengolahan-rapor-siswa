@extends('layouts.app')
@section('title', 'Rekap Nilai')

@section('content')
    <div class="max-w-full">
        <div class="bg-white rounded-lg border border-gray-200">
            <x-search-toolbar 
                placeholder="Cari nama siswa..." 
                :filters="[
                    ['name' => 'kelas_id', 'label' => 'Semua Kelas', 'options' => $kelasList->pluck('nama_kelas', 'id')->toArray()],
                    ['name' => 'mapel_id', 'label' => 'Semua Mapel', 'options' => $mapelList->pluck('nama_mapel', 'id')->toArray()]
                ]"
                :showTambah="false"
                :resetUrl="route('rekap_nilai')"
            />
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Nama Siswa</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Mapel</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Tugas</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Ulangan</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">UTS</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">UAS</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Nilai Akhir</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Kelengkapan</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($nilaiData as $i => $n)
                        @if($n)
                        <tr>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $nilaiData->firstItem() + $i }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ $n->siswa?->nama_siswa ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $n->nama_kelas ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $n->nama_mapel ?? '-' }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-700 font-medium">{{ $n->tugas ?? '-' }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-700 font-medium">{{ $n->ulangan ?? '-' }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-700 font-medium">{{ $n->uts ?? '-' }}</td>
                            <td class="px-6 py-4 text-center text-sm text-gray-700 font-medium">{{ $n->uas ?? '-' }}</td>
                            <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">{{ $n->rata_pengetahuan ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                <x-badge :type="$n->is_lengkap ? 'success' : 'danger'">{{ $n->is_lengkap ? 'Lengkap' : 'Belum Lengkap' }}</x-badge>
                            </td>
                            @php $kkm = $n->kkm; $tuntas = $n->rata_pengetahuan !== null && $n->rata_pengetahuan >= $kkm; @endphp
                            <td class="px-6 py-4 text-center"><x-badge :type="$tuntas ? 'success' : 'warning'">{{ $tuntas ? 'Tuntas' : 'Belum Tuntas' }}</x-badge></td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="11" class="px-6 py-8 text-center text-gray-500"><p class="text-sm font-medium">Tidak ada data nilai</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-gray-50/30 border-t border-gray-100"><x-pagination :paginator="$nilaiData" /></div>
        </div>

        <div class="mt-6 bg-white rounded-lg border border-gray-200 p-6">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Keterangan Status</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="flex items-center gap-3">
                    <x-badge type="success">Tuntas</x-badge>
                    <p class="text-sm text-gray-600">Siswa telah mencapai standar ketuntasan minimum</p>
                </div>
                <div class="flex items-center gap-3">
                    <x-badge type="warning">Belum Tuntas</x-badge>
                    <p class="text-sm text-gray-600">Siswa masih perlu perhatian dan remediasi</p>
                </div>
            </div>
        </div>
    </div>
@endsection