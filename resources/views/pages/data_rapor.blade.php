@extends('layouts.app')
@section('title', 'Data Rapor')

@section('content')
    <div class="max-w-full" x-data="{ 
        openFilter: false,
        openPreview: false,
        previewUrl: '',
        previewLoading: false,
        openPreviewModal(url) {
            this.previewUrl = url;
            this.previewLoading = true;
            this.openPreview = true;
        }
    }" x-init="$watch('openPreview', value => { if (!value) { previewUrl = ''; } })">
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

        {{-- Preview Modal --}}
        <x-modal name="openPreview" title="Preview Rapor" maxWidth="max-w-5xl">
            <div class="w-full">
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-xs text-gray-500 font-medium">Anda sedang melihat dokumen preview rapor siswa.</p>
                    <a :href="previewUrl.replace('/preview/', '/download/')" class="no-progress inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded transition-colors" title="Download PDF">
                        <i class="fa-solid fa-download"></i><span>Download PDF</span>
                    </a>
                </div>
                <div class="relative w-full h-[650px] bg-gray-100 border border-gray-200 rounded overflow-hidden shadow-inner">
                    {{-- Spinner --}}
                    <div class="absolute inset-0 flex items-center justify-center bg-white z-10" x-show="previewLoading">
                        <div class="flex flex-col items-center gap-2">
                            <svg class="animate-spin h-8 w-8 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="text-xs text-gray-500 font-medium">Memuat Dokumen...</span>
                        </div>
                    </div>
                    <iframe 
                        :src="previewUrl" 
                        class="w-full h-full border-none"
                        @load="previewLoading = false">
                    </iframe>
                </div>
            </div>
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
                                <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Peringkat</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Status Rapor</th>
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
                                <td class="px-6 py-4 text-center text-sm font-bold text-gray-900">
                                    {{ $r->ranking ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @php $statusRapor = $r->kelasSiswa?->first()?->status_rapor ?? 'Belum Ditentukan'; @endphp
                                    @if($statusRapor === 'Tuntas')
                                        <x-badge type="success">Tuntas</x-badge>
                                    @elseif($statusRapor === 'Tidak Tuntas')
                                        <x-badge type="danger">Tidak Tuntas</x-badge>
                                    @else
                                        <x-badge type="default">Belum Ditentukan</x-badge>
                                    @endif
                                </td>

                                <td class="px-6 py-4">
                                    <div class="flex flex-col gap-1">
                                        <p class="text-[11px] text-gray-500 italic line-clamp-1 truncate w-40" title="{{ strip_tags($r->kelasSiswa?->first()?->catatan_wali) }}">
                                            {{ strip_tags($r->kelasSiswa?->first()?->catatan_wali) ?? 'Belum ada catatan' }}
                                        </p>


                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button 
                                            @click="openPreviewModal('{{ route('data_rapor.preview', ['nis' => $r->nis, 'semester_id' => $selectedSemester?->id]) }}')"
                                            type="button" 
                                            class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-gray-700 bg-gray-100 border border-gray-300 hover:bg-gray-200 hover:text-gray-900 rounded transition-colors" 
                                            title="Preview Rapor">
                                            <i class="fa-solid fa-eye"></i><span>Preview</span>
                                        </button>
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