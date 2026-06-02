@extends('layouts.app')

@section('title', 'Pembagian Kelas')

@section('content')
    <div class="max-w-full" x-data="{ 
        openTambah: false,
        openEdit: false,
        selectedSiswa: null,
        selectedKelasSiswa: {},
        selectedNis: '',
        editKelasSiswa(id, kelasSiswa, siswa) {
            this.selectedKelasSiswa = { ...kelasSiswa, id: id };
            this.selectedSiswa = siswa;
            this.openEdit = true;
        },
        tambahKelas(siswa) {
            this.selectedNis = siswa.nis;
            this.selectedSiswa = siswa;
            this.openTambah = true;
        },
        konfirmasiHapus(id, namaSiswa, namaKelas) {
            Swal.fire({
                title: 'Hapus Penempatan Kelas?',
                text: 'Apakah Anda yakin ingin menghapus penempatan ' + namaSiswa + ' dari kelas ' + namaKelas + '?',
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
                    form.action = '/pembagian_kelas/' + id;

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
        {{-- Modal Tambah Penempatan Kelas --}}
        <x-modal name="openTambah" title="Tambah Penempatan Kelas">
            <form action="{{ route('pembagian_kelas.store') }}" method="POST">
                @csrf
                <input type="hidden" name="nis" x-model="selectedNis">
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Siswa</label>
                    <input type="text" :value="selectedSiswa?.nama_siswa" disabled class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded bg-gray-100 text-gray-600">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Kelas</label>
                    <select name="kode_kelas" required class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasData as $kelas)
                            <option value="{{ $kelas->kode_kelas }}">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Semester</label>
                    <select name="semester_id" required class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                        <option value="">-- Pilih Semester --</option>
                        @foreach($semesterData as $semester)
                            <option value="{{ $semester->id }}">{{ $semester->semester }} - {{ $semester->tahunAjaran?->nama ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-center gap-3 mt-8">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition-colors">
                        <i class="fa-solid fa-save"></i><span>Tambahkan</span>
                    </button>
                    <button type="button" @click="openTambah = false" class="px-6 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded hover:bg-gray-200 transition-colors">Batal</button>
                </div>
            </form>
        </x-modal>

        {{-- Modal Edit Penempatan Kelas --}}
        <x-modal name="openEdit" title="Edit Penempatan Kelas">
            <form :action="`/pembagian_kelas/${selectedKelasSiswa.id}`" method="POST">
                @csrf
                @method('PUT')
                
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Siswa</label>
                    <input type="text" :value="selectedSiswa?.nama_siswa" disabled class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded bg-gray-100 text-gray-600">
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Kelas</label>
                    <select name="kode_kelas" required class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                        <option value="">-- Pilih Kelas --</option>
                        @foreach($kelasData as $kelas)
                            <option value="{{ $kelas->kode_kelas }}" x-bind:selected="selectedKelasSiswa.kode_kelas === '{{ $kelas->kode_kelas }}'">{{ $kelas->nama_kelas }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Pilih Semester</label>
                    <select name="semester_id" required class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50 text-gray-700 cursor-pointer">
                        <option value="">-- Pilih Semester --</option>
                        @foreach($semesterData as $semester)
                            <option value="{{ $semester->id }}" x-bind:selected="selectedKelasSiswa.semester_id === {{ $semester->id }}">{{ $semester->semester }} - {{ $semester->tahunAjaran?->nama ?? 'N/A' }}</option>
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

        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            {{-- Toolbar Section --}}
            <x-search-toolbar 
                placeholder="Cari siswa berdasarkan Nama atau NIS..." 
                :filters="[
                    ['name' => 'angkatan', 'label' => 'Angkatan', 'options' => $angkatanList],
                    ['name' => 'status', 'label' => 'Status', 'options' => ['Aktif' => 'Aktif', 'Nonaktif' => 'Nonaktif', 'Alumni' => 'Alumni', 'Mutasi' => 'Mutasi']]
                ]"
                :resetUrl="route('pembagian_kelas')"
                :showTambah="false"
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
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Penempatan Kelas</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($siswaData as $key => $siswa)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $siswaData->firstItem() + $key }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $siswa->nis }}</td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $siswa->nama_siswa }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $siswa->angkatan }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="inline-flex px-2.5 py-1 text-xs font-bold rounded-full
                                        @if($siswa->status === 'Aktif') bg-green-100 text-green-800
                                        @elseif($siswa->status === 'Nonaktif') bg-red-100 text-red-800
                                        @elseif($siswa->status === 'Alumni') bg-blue-100 text-blue-800
                                        @else bg-yellow-100 text-yellow-800
                                        @endif
                                    ">
                                        {{ $siswa->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm">
                                    @if($siswa->kelasSiswa && $siswa->kelasSiswa->count() > 0)
                                        <div class="flex flex-col gap-2">
                                            @foreach($siswa->kelasSiswa as $kelas)
                                                <div class="flex items-center justify-between gap-2 bg-blue-50 px-3 py-2 rounded text-xs font-medium">
                                                    <div>
                                                        <p class="text-blue-900">{{ $kelas->kelas?->nama_kelas ?? 'N/A' }}</p>
                                                        <p class="text-blue-700 text-xs">{{ $kelas->semester?->semester ?? 'N/A' }} - {{ $kelas->semester?->tahunAjaran?->nama ?? 'N/A' }}</p>
                                                    </div>
                                                    <div class="flex gap-1">
                                                        <button type="button" 
                                                            @click="editKelasSiswa({{ $kelas->id }}, {{ json_encode($kelas) }}, {{ json_encode($siswa) }})"
                                                            title="Edit Penempatan"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-amber-600 hover:bg-amber-700 rounded transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                                            <span>Edit</span>
                                                        </button>
                                                        <button type="button"
                                                            @click="konfirmasiHapus({{ $kelas->id }}, '{{ addslashes($siswa->nama_siswa) }}', '{{ addslashes($kelas->kelas?->nama_kelas ?? 'N/A') }}')"
                                                            title="Hapus Penempatan"
                                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded transition-colors">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                                            <span>Hapus</span>
                                                        </button>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="inline-flex px-2.5 py-1 text-xs font-medium bg-gray-100 text-gray-600 rounded-full">
                                            Belum ditempat-kan
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button 
                                        @click="tambahKelas({{ json_encode($siswa) }})"
                                        title="Tambah Penempatan Kelas"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-green-600 hover:bg-green-700 rounded transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                        <span>Tambah</span>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-sm text-gray-500">
                                    <i class="fa-solid fa-inbox text-2xl mb-2 block text-gray-400"></i>
                                    Tidak ada data siswa.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($siswaData->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between bg-gray-50">
                    <p class="text-sm text-gray-600">
                        Menampilkan <span class="font-medium">{{ $siswaData->firstItem() }}</span> hingga 
                        <span class="font-medium">{{ $siswaData->lastItem() }}</span> dari 
                        <span class="font-medium">{{ $siswaData->total() }}</span> data
                    </p>
                    <div class="flex gap-2">
                        {{ $siswaData->links('pagination::tailwind') }}
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection
