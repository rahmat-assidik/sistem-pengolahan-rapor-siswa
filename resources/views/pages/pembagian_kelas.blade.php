@extends('layouts.app')

@section('title', 'Pembagian Kelas')

@section('content')
    <div class="max-w-full" x-data="{ 
        openImport: false, 
        openMoveAll: false,
    }">
        {{-- Tabs --}}
        <div class="flex gap-4 mb-6 border-b border-gray-200">
            <a href="{{ route('pembagian_kelas') }}" class="px-4 py-2 text-sm font-semibold text-gray-900 border-b-2 border-gray-900">Pembagian Kelas</a>
            <a href="{{ route('set_wali_kelas') }}" class="px-4 py-2 text-sm font-semibold text-gray-500 hover:text-gray-900 border-b-2 border-transparent hover:border-gray-900">Set Wali Kelas</a>
        </div>

        {{-- Modal Impor Data --}}
        <x-modal name="openImport" title="Impor Data dari Semester Lain">
            <form action="{{ route('pembagian_kelas.import') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Semester Sumber</label>
                    <select name="source_semester_id" required class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                        <option value="">-- Pilih Semester --</option>
                        @foreach(\App\Models\Semester::with('tahunAjaran')->orderBy('tahun_ajaran_id', 'desc')->orderBy('semester', 'desc')->get() as $sem)
                            <option value="{{ $sem->id }}">{{ $sem->tahunAjaran->nama ?? '-' }} - {{ $sem->semester }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3 mt-8">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 transition-colors shadow-md">
                        <i class="fa-solid fa-download"></i><span>Impor Data</span>
                    </button>
                    <button type="button" @click="openImport = false" class="px-6 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded hover:bg-gray-200 transition-colors">Batal</button>
                </div>
            </form>
        </x-modal>

        {{-- Modal Pindah Semua Siswa --}}
        <x-modal name="openMoveAll" title="Pindah Semua Siswa Secara Otomatis">
            <form action="{{ route('pembagian_kelas.move_all') }}" method="POST">
                @csrf
                <div class="bg-amber-50 border-l-4 border-amber-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fa-solid fa-circle-exclamation text-amber-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-amber-700">
                                Fitur ini akan memindahkan siswa secara otomatis berdasarkan status rapor dari semester genap tahun sebelumnya:
                                <ul class="list-disc ml-4 mt-2">
                                    <li><strong>Tuntas:</strong> Naik ke tingkat berikutnya.</li>
                                    <li><strong>Tidak Tuntas:</strong> Tetap di kelas.</li>
                                    <li><strong>Kelas XII Tuntas:</strong> Dinyatakan Lulus.</li>
                                </ul>
                            </p>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Dari Kelas (Asal) - Opsional</label>
                    <select name="from_kode_kelas" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                        <option value="">-- Semua Kelas --</option>
                        @foreach(\App\Models\Kelas::orderBy('nama_kelas')->get() as $kelas)
                            <option value="{{ $kelas->kode_kelas }}">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <p class="text-xs text-gray-500 italic">Perubahan ini hanya berlaku untuk semester aktif: <span class="font-bold">{{ $semesterAktif ? $semesterAktif->semester . ' - ' . $semesterAktif->tahunAjaran?->nama : 'Tidak Ada' }}</span></p>
                </div>

                <div class="flex items-center gap-3 mt-8">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-amber-600 text-white text-sm font-semibold rounded hover:bg-amber-700 transition-colors shadow-md">
                        <i class="fa-solid fa-right-left"></i><span>Proses Automasi</span>
                    </button>
                    <button type="button" @click="openMoveAll = false" class="px-6 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded hover:bg-gray-200 transition-colors">Batal</button>
                </div>
            </form>
        </x-modal>

        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            {{-- Toolbar Section --}}
            <div class="flex flex-wrap items-center justify-between gap-4 p-4 border-b border-gray-200 bg-gray-50">
                <div class="flex items-center gap-3">
                    <x-search-toolbar
                        placeholder="Cari kelas..."
                        :filters="[
                            ['name' => 'tingkat', 'label' => 'Tingkat', 'options' => ['X' => 'X', 'XI' => 'XI', 'XII' => 'XII']]
                        ]"
                        :resetUrl="route('pembagian_kelas')"
                        :showTambah="false"
                    />
                </div>
                
                <div class="flex items-center gap-2">
                    <button @click="openImport = true" class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 transition-colors shadow-sm">
                        <i class="fa-solid fa-download"></i>
                        <span>Impor Data Semester</span>
                    </button>
                    <button @click="openMoveAll = true" class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 text-white text-sm font-semibold rounded hover:bg-amber-700 transition-colors shadow-sm">
                        <i class="fa-solid fa-right-left"></i>
                        <span>Pindah Semua Siswa</span>
                    </button>
                </div>
            </div>

            {{-- Table Section --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900 border-b border-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">No</th> 
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Kode Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Nama Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Tingkat</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Jumlah Siswa</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($kelasData as $key => $kelas)
                            <tr class="transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $kelasData->firstItem() + $key }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 font-bold">{{ $kelas->kode_kelas }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $kelas->nama_kelas }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $kelas->tingkat }}</td>
                                <td class="px-6 py-4 text-center">
                                    <x-badge type="info" :dot="false">
                                        {{ $kelas->kelas_siswa_count }} Siswa
                                    </x-badge>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="{{ route('pembagian_kelas.manage', $kelas->kode_kelas) }}" 
                                       class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded transition-colors shadow-sm">
                                        <i class="fa-solid fa-users-gear"></i>
                                        <span>Kelola Siswa</span>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-sm text-gray-500">
                                    <i class="fa-solid fa-inbox text-2xl mb-2 block text-gray-400"></i>
                                    Tidak ada data kelas.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="p-6 bg-gray-50/30 border-t border-gray-100">
                <x-pagination :paginator="$kelasData" />
            </div>
        </div>
    </div>
@endsection
