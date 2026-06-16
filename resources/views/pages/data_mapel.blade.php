@extends('layouts.app')

@section('title', 'Data Mata Pelajaran')

@section('content')
    <div class="max-w-full" x-data="{ 
        openTambah: false, 
        openEdit: false, 
        openLihat: false, 
        selectedMapel: {},
        originalKode: '',
        editMapel(mapel) {
            this.selectedMapel = { ...mapel };
            this.originalKode = mapel.kode_mapel;
            this.openEdit = true;
        },
        lihatMapel(mapel) {
            this.selectedMapel = { ...mapel };
            this.openLihat = true;
        },
        konfirmasiHapus(id, nama) {
            Swal.fire({
                title: 'Hapus Data Mata Pelajaran?',
                text: 'Apakah Anda yakin ingin menghapus ' + nama + '?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#111827',
                cancelButtonColor: '#ef4444',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/data_mapel/' + id;
                    
                    const csrfToken = document.querySelector('meta[name=csrf-token]').getAttribute('content');
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    
                    form.appendChild(csrfInput);
                    form.appendChild(methodInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    }">
        {{-- Modal Tambah Mata Pelajaran --}}
<x-modal name="openTambah" title="Tambah Mata Pelajaran Baru">
    <form action="{{ route('data_mapel.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

            {{-- Kode Mapel --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Kode Mata Pelajaran
                </label>

                <input
                    type="text"
                    name="kode_mapel"
                    value="{{ old('kode_mapel') }}"
                    placeholder="Contoh: MTK"
                    class="w-full px-3 py-2.5 text-sm border rounded outline-none transition-colors bg-gray-50
                    @error('kode_mapel')
                        border-red-500
                    @else
                        border-gray-300 focus:border-gray-900
                    @enderror">

                @error('kode_mapel')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Nama Mapel --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Nama Mata Pelajaran
                </label>

                <input
                    type="text"
                    name="nama_mapel"
                    value="{{ old('nama_mapel') }}"
                    placeholder="Masukkan nama mapel"
                    class="w-full px-3 py-2.5 text-sm border rounded outline-none transition-colors bg-gray-50
                    @error('nama_mapel')
                        border-red-500
                    @else
                        border-gray-300 focus:border-gray-900
                    @enderror">

                @error('nama_mapel')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">

            {{-- Kelompok --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Kelompok
                </label>

                <select
                    name="kelompok"
                    class="w-full px-3 py-2.5 text-sm border rounded bg-gray-50 text-gray-700
                    @error('kelompok')
                        border-red-500
                    @else
                        border-gray-300
                    @enderror">

                    <option value="">
                        -- Pilih Kelompok --
                    </option>

                    <option value="Wajib"
                        {{ old('kelompok') == 'Wajib' ? 'selected' : '' }}>
                        Wajib
                    </option>

                    <option value="Peminatan"
                        {{ old('kelompok') == 'Peminatan' ? 'selected' : '' }}>
                        Peminatan
                    </option>

                </select>

                @error('kelompok')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            {{-- Status --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                    Status
                </label>

                <select
                    name="status"
                    class="w-full px-3 py-2.5 text-sm border rounded bg-gray-50 text-gray-700
                    @error('status')
                        border-red-500
                    @else
                        border-gray-300
                    @enderror">

                    <option value="Aktif"
                        {{ old('status') == 'Aktif' ? 'selected' : '' }}>
                        Aktif
                    </option>

                    <option value="Tidak Aktif"
                        {{ old('status') == 'Tidak Aktif' ? 'selected' : '' }}>
                        Tidak Aktif
                    </option>

                </select>

                @error('status')
                    <p class="mt-1 text-sm text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

        </div>

        <div class="flex items-center gap-3 mt-8">

            <button
                type="submit"
                class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition-colors">

                <i class="fa-solid fa-save"></i>
                <span>Simpan Data</span>

            </button>

            <button
                type="button"
                @click="openTambah = false"
                class="px-6 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded hover:bg-gray-200 transition-colors">

                Batal

            </button>

        </div>
    </form>
</x-modal>

        {{-- Modal Edit Mata Pelajaran --}}
        <x-modal name="openEdit" title="Edit Data Mata Pelajaran">
            <form method="POST" :action="'/data_mapel/' + originalKode">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kode Mata Pelajaran</label>
                        <input type="text" name="kode_mapel" x-model="selectedMapel.kode_mapel" required readonly class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded outline-none bg-gray-100 text-gray-500 cursor-not-allowed">
                        <p class="text-xs text-gray-400 mt-1">Kode mapel tidak dapat diubah</p>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Mata Pelajaran</label>
                        <input type="text" name="nama_mapel" x-model="selectedMapel.nama_mapel" required class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4 mb-4">
                   <div>
    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kelompok</label>
    <select name="kelompok" x-model="selectedMapel.kelompok" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
        <option value="Wajib">Wajib</option>
        <option value="Peminatan">Peminatan</option>
    </select>
</div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                        <select name="status" x-model="selectedMapel.status" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 mt-8">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition-colors">
                        <i class="fa-solid fa-save"></i><span>Simpan Perubahan</span>
                    </button>
                    <button type="button" @click="openEdit = false" class="px-6 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded hover:bg-gray-200 transition-colors">Batal</button>
                </div>
            </form>
        </x-modal>

        {{-- Modal Lihat Mata Pelajaran --}}
        <x-modal name="openLihat" title="Detail Informasi Mata Pelajaran">
            <div class="space-y-4">
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Kode Mapel</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedMapel.kode_mapel"></span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Nama Mapel</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedMapel.nama_mapel"></span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Kelompok</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedMapel.kelompok || '-'"></span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Status Saat Ini</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2">
                        <span class="px-2 py-0.5 rounded text-[10px] uppercase tracking-wider" 
                              :class="selectedMapel.status === 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                              x-text="selectedMapel.status"></span>
                    </span>
                </div>
            </div>
            <div class="mt-8">
                <button type="button" @click="openLihat = false" class="w-full px-6 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded hover:bg-gray-800 transition-colors">Tutup</button>
            </div>
        </x-modal>

        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            {{-- Toolbar Section --}}
            <x-search-toolbar 
                placeholder="Cari mata pelajaran berdasarkan Kode atau Nama..." 
                :filters="[
                    ['name' => 'status', 'label' => 'Status Mapel', 'options' => ['Aktif' => 'Aktif', 'Tidak Aktif' => 'Tidak Aktif']]
                ]"
                :resetUrl="route('data_mapel')"
                tambahClick="openTambah = true" 
            />

            {{-- Table Section --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900 border-b border-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Kode Mapel</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Nama Mata Pelajaran</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Kelompok</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($mapelData as $i => $m)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $mapelData->firstItem() + $i }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $m->kode_mapel }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-semibold">{{ $m->nama_mapel }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $m->kelompok ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm"><x-badge :type="$m->status === 'Aktif' ? 'success' : 'danger'">{{ $m->status }}</x-badge></td>
                            <td class="px-6 py-4 text-center">
                                <x-action-buttons 
                                    :lihatClick="'lihatMapel(' . json_encode($m) . ')'"
                                    :editClick="'editMapel(' . json_encode($m) . ')'"
                                    :hapusClick="'konfirmasiHapus(\'' . $m->kode_mapel . '\', \'' . addslashes($m->nama_mapel) . '\')'"
                                />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-sm font-medium">Tidak ada data mata pelajaran</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-gray-50/30 border-t border-gray-100">
                <x-pagination :paginator="$mapelData" />
            </div>
        </div>
    </div>
@endsection