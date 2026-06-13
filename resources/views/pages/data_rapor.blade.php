@extends('layouts.app')
@section('title', 'Data Rapor')

@section('content')
    <div class="max-w-full" x-data="{ 
        openCatatan: false, 
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
            {{-- Info Periode (Inside Container) --}}
            <div class="px-5 py-3 bg-gray-50/50 border-b border-gray-100 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-2 h-2 rounded-full bg-blue-500"></div>
                    <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Periode Laporan</span>
                </div>
                <div class="text-[11px] font-bold text-gray-700 bg-white px-3 py-1 rounded border border-gray-200 shadow-sm">
                    <i class="fa-solid fa-calendar-day text-blue-500 mr-1.5"></i>
                    {{ $selectedSemester ? $selectedSemester->tahunAjaran->nama . ' — ' . $selectedSemester->semester : 'Pilih Semester' }}
                </div>
            </div>

            <x-search-toolbar 
                placeholder="Cari nama siswa..." 
                :filters="[
                    ['name' => 'semester_id', 'label' => 'Pilih Semester', 'options' => $semesterOptions],
                    ['name' => 'kelas_id', 'label' => 'Semua Kelas', 'options' => $kelasList->pluck('nama_kelas', 'id')->toArray()]
                ]"
                :showTambah="false"
                :resetUrl="route('data_rapor')"
            />
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">NIS</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Nama Siswa</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Tahun Ajaran</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Rata-Rata Nilai</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Catatan Wali</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($siswaData as $i => $r)
                        @if($r)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $siswaData->firstItem() + $i }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $r->nis ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ $r->nama_siswa ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $r->kelasSiswa?->first()?->kelas?->nama_kelas ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-medium">
                                {{ $r->kelasSiswa?->first()?->semester?->tahunAjaran?->nama ?? '-' }} 
                                <span class="text-[10px] text-gray-400 font-normal">({{ $r->kelasSiswa?->first()?->semester?->semester ?? '-' }})</span>
                            </td>
                            <td class="px-6 py-4 text-sm text-center font-bold {{ $r->rata_rata !== null ? ($r->rata_rata >= 80 ? 'text-green-600' : ($r->rata_rata >= 70 ? 'text-blue-600' : 'text-red-600')) : 'text-gray-400' }}">{{ $r->rata_rata ?? '-' }}</td>
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
                                    <a href="{{ route('data_rapor.download', ['nis' => $r->nis, 'semester_id' => $selectedSemester?->id]) }}" title="Cetak Rapor (PDF)" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded transition-colors">
                                        <i class="fa-solid fa-print"></i><span>Cetak Rapor</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endif
                        @empty
                        <tr><td colspan="9" class="px-6 py-8 text-center text-gray-500"><p class="text-sm font-medium">Tidak ada data rapor</p></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-gray-50/30 border-t border-gray-100"><x-pagination :paginator="$siswaData" /></div>
        </div>
    </div>
@endsection