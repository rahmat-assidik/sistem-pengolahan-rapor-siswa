@extends('layouts.app')
@section('title', 'Data Rapor')

@section('content')
    <div class="max-w-full" x-data="{ 
        openCatatan: false, 
        openFilter: false,
        currentSiswaId: null, 
        currentNama: '', 
        currentCatatan: '',
        openModal(id, nama, catatan) {
            this.currentSiswaId = id;
            this.currentNama = nama;
            this.currentCatatan = catatan;
            this.openCatatan = true;
        }
    }">
        {{-- Filter Modal --}}
        <x-modal name="openFilter" title="Pilih Kelas & Semester">
            <form action="{{ route('data_rapor') }}" method="GET">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Semester</label>
                        <select name="semester_id" class="w-full mt-1 border border-gray-300 rounded p-2 text-sm" required>
                            @foreach($semesterOptions as $id => $label)
                                <option value="{{ $id }}" {{ request('semester_id') == $id ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Kelas</label>
                        <select name="kelas_id" class="w-full mt-1 border border-gray-300 rounded p-2 text-sm" required>
                            <option value="">Pilih Kelas</option>
                            @foreach($kelasList as $kelas)
                                <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800">Tampilkan Data</button>
                    <button type="button" @click="openFilter = false" class="px-6 py-2.5 bg-gray-100 text-gray-600 text-sm font-semibold rounded hover:bg-gray-200">Batal</button>
                </div>
            </form>
        </x-modal>

        {{-- Modal Catatan Wali --}}
        <x-modal name="openCatatan" title="Catatan Wali Kelas">
            <form action="{{ route('data_rapor.catatan') }}" method="POST">
                @csrf
                <input type="hidden" name="siswa_id" :value="currentSiswaId">
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">Memberikan catatan perkembangan untuk siswa: <span class="font-bold text-gray-900" x-text="currentNama"></span></p>
                    <textarea name="catatan" x-model="currentCatatan" rows="4" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors resize-none" placeholder="Masukkan catatan wali kelas di sini..."></textarea>
                </div>
                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition-colors">
                        <i class="fa-solid fa-save"></i><span>Simpan Catatan</span>
                    </button>
                    <button type="button" @click="openCatatan = false" class="px-6 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded hover:bg-gray-200 transition-colors">Batal</button>
                </div>
            </form>
        </x-modal>


        <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-bold text-gray-900">Data Rapor Siswa</h2>
                <div class="flex gap-2">
                    @if($selectedKelas)
                        <div class="bg-gray-900 text-white px-4 py-2 rounded font-bold text-xs">
                            Kelas: {{ $selectedKelas->nama_kelas }}
                        </div>
                    @endif
                    <button @click="openFilter = true" class="px-4 py-2 bg-gray-900 text-white text-xs font-semibold rounded hover:bg-gray-800">
                        <i class="fa-solid fa-magnifying-glass"></i> Cari Rapor
                    </button>
                </div>
            </div>

            @if($siswaData->isEmpty())
                <div class="p-10 text-center text-gray-500">
                    <i class="fa-solid fa-folder-open text-4xl mb-3 text-gray-300"></i>
                    <p class="text-sm font-medium">Belum ada data yang ditampilkan.</p>
                    <p class="text-xs">Silakan gunakan tombol "Cari Rapor" untuk memfilter berdasarkan kelas dan semester.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-900">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">NIS</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Nama Siswa</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Kelas</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Catatan Wali</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($siswaData as $i => $r)
                            <tr>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $siswaData->firstItem() + $i }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $r->nis ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ $r->nama_siswa ?? '-' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $r->kelasSiswa?->first()?->kelas?->nama_kelas ?? '-' }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($r->status_lulus === 'Lulus')
                                        <x-badge type="success">Lulus</x-badge>
                                    @elseif($r->status_lulus === 'Kondisional')
                                        <x-badge type="warning">Kondisional</x-badge>
                                    @elseif($r->status_lulus === 'Tidak Lulus')
                                        <x-badge type="danger">Tidak Lulus</x-badge>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <p class="text-[11px] text-gray-500 italic line-clamp-1 truncate w-40">{{ $r->kelasSiswa?->first()?->catatan_wali ?? 'Belum ada catatan' }}</p>
                                        @if(auth()->user()->isGuru() && auth()->user()->guru_id === $r->kelasSiswa?->first()?->kelas?->wali_id)
                                        <button @click="openModal('{{ $r->id }}', '{{ $r->nama_siswa ?? 'Siswa' }}', '{{ $r->kelasSiswa?->first()?->catatan_wali ?? '' }}')" class="text-[10px] font-semibold text-blue-600 hover:text-blue-800 text-left">
                                            <i class="fa-solid fa-pen-to-square"></i> Edit Catatan
                                        </button>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center">
                                        <a href="{{ route('data_rapor.download', ['nis' => $r->nis, 'semester_id' => $selectedSemester?->id]) }}" class="no-progress inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded transition-colors" title="Cetak Rapor (PDF)">
                                            <i class="fa-solid fa-print"></i><span>Cetak Rapor</span>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 bg-gray-50/30 border-t border-gray-100"><x-pagination :paginator="$siswaData" /></div>
            @endif
        </div>
    </div>
@endsection