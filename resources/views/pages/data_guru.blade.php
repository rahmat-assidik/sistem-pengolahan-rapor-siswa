@extends('layouts.app')

@section('title', 'Data Guru')

@section('content')
    <div class="max-w-full" x-data="{ 
        openTambah: false, 
        openEdit: false, 
        openLihat: false, 
        selectedGuru: {},
        editGuru(guru) {
            this.selectedGuru = { 
                ...guru, 
                original_nip: guru.nip
            };
            this.openEdit = true;
        },
        lihatGuru(guru) {
            this.selectedGuru = { ...guru };
            this.openLihat = true;
        },
        konfirmasiHapus(id, nama) {
            Swal.fire({
                title: 'Hapus Data Guru?',
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
                    form.action = '/data_guru/' + id;
                    
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
        {{-- Modal Tambah Guru --}}
        <x-modal name="openTambah" title="Tambah Guru Baru">
            <form action="{{ route('data_guru.store') }}" method="POST">
                @csrf
              <div class="grid grid-cols-2 gap-4 mb-4">

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
            NIP/Kode Guru
        </label>

        <input
            type="text"
            name="nip"
            required
            maxlength="10"
            placeholder="Masukkan NIP"
            oninput="this.value=this.value.replace(/[^0-9]/g,'')"
            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
    </div>

    <div>
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
            Nama Lengkap
        </label>

        <input
            type="text"
            name="nama_guru"
            required
            placeholder="Masukkan nama guru"
            class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
    </div>

</div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" required placeholder="Masukkan email guru" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Kelamin</label>
                        <select name="jenis_kelamin" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                            <option value="">Pilih Jenis Kelamin</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">No HP</label>
                        <input type="text" name="no_hp" placeholder="Masukkan nomor HP" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                    <select name="status" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
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

        {{-- Modal Edit Guru --}}
        <x-modal name="openEdit" title="Edit Data Guru">
            <form :action="`/data_guru/${selectedGuru.original_nip}`" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="original_nip" x-bind:value="selectedGuru.original_nip">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">NIP/Kode Guru</label>
                        <input type="text" name="nip" x-model="selectedGuru.nip" required class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="nama_guru" x-model="selectedGuru.nama_guru" required class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email</label>
                    <input type="email" name="email" x-model="selectedGuru.email" required class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Jenis Kelamin</label>
                        <select name="jenis_kelamin" x-model="selectedGuru.jenis_kelamin" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">No HP</label>
                        <input type="text" name="no_hp" x-model="selectedGuru.no_hp" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Status</label>
                    <select name="status" x-model="selectedGuru.status" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                        <option value="Aktif">Aktif</option>
                        <option value="Tidak Aktif">Tidak Aktif</option>
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

        {{-- Modal Lihat Guru --}}
        <x-modal name="openLihat" title="Detail Informasi Guru">
            <div class="space-y-4">
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">NIP/Kode</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedGuru.nip"></span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Nama Lengkap</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedGuru.nama_guru"></span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Email</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedGuru.email || '-'"></span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Jenis Kelamin</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedGuru.jenis_kelamin || '-'"></span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">No HP</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedGuru.no_hp || '-'"></span>
                </div>
                <div class="grid grid-cols-3 py-2 border-b border-gray-50">
                    <span class="text-sm font-semibold text-gray-500">Status Saat Ini</span>
                    <span class="text-sm font-bold text-gray-900 col-span-2">
                        <span class="px-2 py-0.5 rounded text-[10px] uppercase tracking-wider" 
                              :class="selectedGuru.status === 'Aktif' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'"
                              x-text="selectedGuru.status"></span>
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
                placeholder="Cari guru berdasarkan NIP atau Nama..." 
                :filters="[
                    ['name' => 'status', 'label' => 'Status Guru', 'options' => ['Aktif' => 'Aktif', 'Tidak Aktif' => 'Tidak Aktif']]
                ]"
                :resetUrl="route('data_guru')"
                tambahClick="openTambah = true"
            />

            {{-- Table Section --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900 border-b border-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">NIP/Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Nama Guru</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Email</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Jenis Kelamin</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">No HP</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Status</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($guruData as $i => $g)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $guruData->firstItem() + $i }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $g->nip }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-semibold">{{ $g->nama_guru }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $g->email ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $g->jenis_kelamin ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $g->no_hp ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm"><x-badge :type="$g->status === 'Aktif' ? 'success' : 'danger'">{{ $g->status }}</x-badge></td>
                            <td class="px-6 py-4 text-center">
                                <x-action-buttons 
                                    :lihatClick="'lihatGuru(' . json_encode($g) . ')'"
                                    :editClick="'editGuru(' . json_encode($g) . ')'"
                                    :hapusClick="'konfirmasiHapus(\'' . $g->nip . '\', \'' . addslashes($g->nama_guru) . '\')'"
                                />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-sm font-medium">Tidak ada data guru</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-gray-50/30 border-t border-gray-100">
                <x-pagination :paginator="$guruData" />
            </div>
        </div>
    </div>
@endsection