@extends('layouts.app')

@section('title', 'Data Kelas')

@section('content')
    <div class="max-w-full" x-data="{ 
        openTambah: false, 
        openEdit: false, 
        openLihat: false, 
        selectedKelas: {},
        editKelas(kelas) {
            this.selectedKelas = { ...kelas };
            this.openEdit = true;
        },
        lihatKelas(kelas) {
            this.selectedKelas = { ...kelas };
            this.openLihat = true;
        },
        konfirmasiHapus(id, nama) {
            Swal.fire({
                title: 'Hapus Data Kelas?',
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
                    form.action = '/data_kelas/' + id;

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
        {{-- Modal Tambah Kelas --}}
        <x-modal name="openTambah" title="Tambah Kelas Baru">
            <form action="{{ route('data_kelas.store') }}" method="POST">
                @csrf
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kode Kelas</label>
                        <input type="text" name="kode_kelas" required placeholder="Contoh: 10-A" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kelas</label>
                        <input type="text" name="nama_kelas" required placeholder="Contoh: Kelas 10-A" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tingkat</label>
                    <input type="text" name="tingkat" required placeholder="Contoh: 1 atau 10" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Wali Kelas</label>
                    <select name="wali_id" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                        <option value="">Pilih Wali Kelas</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->nip }}">{{ $guru->nama_guru }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3 mt-8">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition-colors">
                        <i class="fa-solid fa-save"></i><span>Simpan Data</span>
                    </button>
                    <button type="button" @click="openTambah = false" class="px-6 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded hover:bg-gray-200 transition-colors">Batal</button>
                </div>
            </form>
        </x-modal>

        {{-- Modal Edit Kelas --}}
        <x-modal name="openEdit" title="Edit Data Kelas">
            <form :action="`/data_kelas/${selectedKelas.id}`" method="POST">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kode Kelas</label>
                        <input type="text" name="kode_kelas" x-model="selectedKelas.kode_kelas" required class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Kelas</label>
                        <input type="text" name="nama_kelas" x-model="selectedKelas.nama_kelas" required class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tingkat</label>
                    <input type="text" name="tingkat" x-model="selectedKelas.tingkat" required class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Wali Kelas</label>
                    <select name="wali_id" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                        <option value="">Pilih Wali Kelas</option>
                        @foreach($guruList as $guru)
                            <option value="{{ $guru->nip }}"
                                x-bind:selected="selectedKelas.wali_id == '{{ $guru->nip }}'">
                                {{ $guru->nama_guru }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center gap-3 mt-8">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition-colors">
                        <i class="fa-solid fa-save"></i><span>Simpan Perubahan</span>
                    </button>
                    <button type="button" @click="openEdit = false" class="px-6 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded hover:bg-gray-200 transition-colors">Batal</button>
                </div>
            </form>
        </x-modal>

        {{-- Modal Lihat Kelas --}}
        <x-modal name="openLihat" title="Detail Informasi Kelas">
            <div class="space-y-4">
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Kode Kelas</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedKelas.kode_kelas"></span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Nama Kelas</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedKelas.nama_kelas"></span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Tingkat</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedKelas.tingkat"></span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Wali Kelas</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedKelas.wali ? selectedKelas.wali.nama_guru : '-'"></span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Jumlah Siswa</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="(selectedKelas.kelas_siswa_count ?? 0) + ' siswa'"></span>
                </div>
            </div>
            <div class="mt-8">
                <button type="button" @click="openLihat = false" class="w-full px-6 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded hover:bg-gray-800 transition-colors">Tutup</button>
            </div>
        </x-modal>

        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            {{-- Toolbar Section --}}
            <x-search-toolbar 
                placeholder="Cari kelas berdasarkan Kode atau Nama..." 
                :filters="[
                    ['name' => 'tingkat', 'label' => 'Tingkat', 'options' => ['10' => '10', '11' => '11', '12' => '12']]
                ]"
                :resetUrl="route('data_kelas')"
                tambahClick="openTambah = true"
            />

            {{-- Table Section --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900 border-b border-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Kode Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Nama Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Tingkat</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Wali Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Jumlah Siswa</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($kelasData as $i => $k)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $kelasData->firstItem() + $i }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $k->kode_kelas }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-semibold">{{ $k->nama_kelas }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $k->tingkat }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $k->wali?->nama_guru ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $k->kelas_siswa_count ?? 0 }} siswa</td>
                            <td class="px-6 py-4 text-center">
                                <x-action-buttons 
                                    :lihatClick="'lihatKelas(' . json_encode($k) . ')'"
                                    :editClick="'editKelas(' . json_encode($k) . ')'"
                                    :hapusClick="'konfirmasiHapus(' . $k->id . ', \'' . addslashes($k->nama_kelas) . '\')'"
                                />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-sm font-medium">Tidak ada data kelas</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-gray-50/30 border-t border-gray-100">
                <x-pagination :paginator="$kelasData" />
            </div>
        </div>
    </div>
@endsection