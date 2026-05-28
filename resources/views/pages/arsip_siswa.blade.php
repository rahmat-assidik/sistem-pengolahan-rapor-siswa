@extends('layouts.app')

@section('title', 'Arsip Siswa')

@section('content')
    <div class="max-w-full" x-data="{ 
        openLihat: false, 
        selectedSiswa: {},
        lihatSiswa(siswa) {
            this.selectedSiswa = { ...siswa };
            this.openLihat = true;
        }
    }">


        {{-- Modal Lihat Siswa / Track Record --}}
        <x-modal name="openLihat" title="Track Record Siswa">
            <div class="space-y-6">
                <!-- Data Identitas -->
                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Identitas Siswa</h4>
                    <div class="grid grid-cols-2 gap-x-4 gap-y-2 bg-gray-50 p-4 rounded border border-gray-100">
                        <div>
                            <span class="block text-[11px] font-semibold text-gray-500 uppercase">NIS</span>
                            <span class="text-sm font-bold text-gray-900" x-text="selectedSiswa.nis"></span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-semibold text-gray-500 uppercase">Nama Lengkap</span>
                            <span class="text-sm font-bold text-gray-900" x-text="selectedSiswa.nama_siswa"></span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-semibold text-gray-500 uppercase">Jenis Kelamin</span>
                            <span class="text-sm font-bold text-gray-900" x-text="selectedSiswa.jenis_kelamin"></span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-semibold text-gray-500 uppercase">Angkatan</span>
                            <span class="text-sm font-bold text-gray-900" x-text="selectedSiswa.angkatan"></span>
                        </div>
                        <div class="col-span-2 mt-1">
                            <span class="block text-[11px] font-semibold text-gray-500 uppercase mb-1">Status Terakhir</span>
                            <span class="px-2 py-0.5 rounded text-[10px] uppercase tracking-wider font-bold" 
                                  :class="selectedSiswa.status === 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                                  x-text="selectedSiswa.status"></span>
                        </div>
                    </div>
                </div>

                <!-- Track Record / Riwayat Kelas -->
                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Riwayat Akademik (Kelas)</h4>
                    <div class="overflow-x-auto border border-gray-200 rounded">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-100 border-b border-gray-200">
                                <tr>
                                    <th class="px-4 py-2.5 font-bold text-gray-700 text-xs tracking-wider">Tahun Ajaran</th>
                                    <th class="px-4 py-2.5 font-bold text-gray-700 text-xs tracking-wider">Semester</th>
                                    <th class="px-4 py-2.5 font-bold text-gray-700 text-xs tracking-wider">Kelas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <template x-for="riwayat in selectedSiswa.kelas_siswa" :key="riwayat.id">
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-4 py-2.5 text-gray-700 font-medium" x-text="riwayat.semester?.tahun_ajaran?.nama ?? '-'"></td>
                                        <td class="px-4 py-2.5 text-gray-700 font-medium" x-text="riwayat.semester?.semester ?? '-'"></td>
                                        <td class="px-4 py-2.5 text-gray-900 font-bold" x-text="riwayat.kelas?.nama_kelas ?? '-'"></td>
                                    </tr>
                                </template>
                                <template x-if="!selectedSiswa.kelas_siswa || selectedSiswa.kelas_siswa.length === 0">
                                    <tr>
                                        <td colspan="3" class="px-4 py-6 text-center text-gray-500 text-xs font-semibold">
                                            Belum ada riwayat kelas
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-8">
                <button type="button" @click="openLihat = false" class="w-full px-6 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded hover:bg-gray-800 transition-colors">Tutup</button>
            </div>
        </x-modal>

        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            {{-- Toolbar Section --}}
            <x-search-toolbar 
                placeholder="Cari arsip berdasarkan NIS atau Nama..." 
                :showTambah="false"
                :filters="[
                    ['name' => 'angkatan', 'label' => 'Angkatan', 'options' => $angkatanList],
                    ['name' => 'status', 'label' => 'Status Siswa', 'options' => ['Aktif' => 'Aktif', 'Nonaktif' => 'Nonaktif', 'Alumni' => 'Alumni', 'Mutasi' => 'Mutasi']]
                ]"
                :resetUrl="route('arsip_siswa')"
            />

            {{-- Table Section --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900 border-b border-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">NIS</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Nama Siswa</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Angkatan</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Jenis Kelamin</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($siswaData as $i => $s)
                        @if($s)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $siswaData->firstItem() + $i }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $s->nis ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-semibold">{{ $s->nama_siswa ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                <span class="px-2 py-1 bg-gray-100 border border-gray-200 rounded text-[10px] font-bold text-gray-700">
                                    {{ $s->angkatan ?? '-' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $s->jenis_kelamin ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm"><x-badge :type="$s->status === 'Aktif' ? 'success' : 'danger'">{{ $s->status ?? '-' }}</x-badge></td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex items-center justify-center gap-2 flex-wrap">
                                    <button type="button" @click='lihatSiswa(@json($s))' title="Lihat Track Record" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded transition-colors">
                                        <i class="fa-solid fa-clock-rotate-left"></i><span>Track Record</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-sm font-medium">Tidak ada data arsip siswa</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-gray-50/30 border-t border-gray-100">
                <x-pagination :paginator="$siswaData" />
            </div>
        </div>
    </div>
@endsection