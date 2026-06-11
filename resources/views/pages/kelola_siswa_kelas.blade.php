@extends('layouts.app')

@section('title', 'Kelola Siswa Kelas ' . $kelas->nama_kelas)

@section('content')
    <div class="max-w-full" x-data="{
        selectedIds: [],
        selectAll: false,
        isLoading: false,

        toggleSelectAll() {
            this.selectAll = !this.selectAll;
            if (this.selectAll) {
                this.selectedIds = Array.from(document.querySelectorAll('.siswa-checkbox')).map(el => el.value);
            } else {
                this.selectedIds = [];
            }
        },

        konfirmasiKeluarkan(id, namaSiswa) {
            Swal.fire({
                title: 'Keluarkan Siswa?',
                text: 'Apakah Anda yakin ingin mengeluarkan ' + namaSiswa + ' dari kelas ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Keluarkan!',
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
        {{-- Header Information --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <div>
                <nav class="flex mb-2" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <a href="{{ route('pembagian_kelas') }}" class="text-sm font-medium text-gray-700 hover:text-blue-600">Pembagian Kelas</a>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <i class="fa-solid fa-chevron-right text-gray-400 text-xs mx-2"></i>
                                <span class="text-sm font-medium text-gray-500">Kelola Siswa</span>
                            </div>
                        </li>
                    </ol>
                </nav>
                <h2 class="text-2xl font-bold text-gray-900">Kelas {{ $kelas->nama_kelas }} ({{ $kelas->kode_kelas }})</h2>
                <p class="text-sm text-gray-500 mt-1">Semester: <span class="font-semibold text-gray-700">{{ $semesterAktif->semester }} - {{ $semesterAktif->tahunAjaran->nama }}</span></p>
            </div>
            <a href="{{ route('pembagian_kelas') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-semibold rounded hover:bg-gray-200 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            {{-- Column 1: Students in Class --}}
            <div class="flex flex-col gap-4">
                <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex items-center justify-between">
                        <h3 class="font-bold text-gray-900 flex items-center gap-2">
                            <i class="fa-solid fa-user-check text-green-600"></i>
                            <span>Siswa di Kelas Ini ({{ $siswaDiKelas->count() }})</span>
                        </h3>
                    </div>
                    <div class="overflow-x-auto max-h-[600px] overflow-y-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100 border-b border-gray-200 sticky top-0 z-10">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Siswa</th>
                                    <th class="px-4 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($siswaDiKelas as $index => $item)
                                    <tr class="transition-colors">
                                        <td class="px-4 py-3 text-sm text-gray-500">{{ $index + 1 }}</td>
                                        <td class="px-4 py-3">
                                            <div class="text-sm font-semibold text-gray-900">{{ $item->siswa->nama_siswa }}</div>
                                            <div class="text-[10px] text-gray-500">NIS: {{ $item->siswa->nis }}</div>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <x-action-buttons 
                                                :showLihat="false"
                                                :showEdit="false"
                                                hapusTitle="Keluarkan Siswa"
                                                :hapusClick="'konfirmasiKeluarkan(' . $item->id . ', \'' . addslashes($item->siswa->nama_siswa) . '\')'"
                                            />
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                            Belum ada siswa di kelas ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Column 2: Available Students (Pool) --}}
            <div class="flex flex-col gap-4">
                <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
                    <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                        <h3 class="font-bold text-blue-900 flex items-center gap-2">
                            <i class="fa-solid fa-user-plus text-blue-600"></i>
                            <span>Siswa Belum Ada Kelas ({{ $siswaTersedia->count() }})</span>
                        </h3>
                    </div>

                    {{-- Local Search & Filter for Unassigned Students --}}
                    <div class="p-4 border-b border-gray-200 bg-gray-50/50">
                        <form action="{{ route('pembagian_kelas.manage', $kelas->kode_kelas) }}" method="GET" class="flex flex-wrap gap-2">
                            <div class="flex-1 min-w-[200px]">
                                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau NIS..." 
                                       class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:border-blue-500 outline-none">
                            </div>
                            <div class="w-[120px]">
                                <select name="angkatan" class="w-full px-3 py-2 text-sm border border-gray-300 rounded focus:border-blue-500 outline-none">
                                    <option value="">Angkatan</option>
                                    @foreach($angkatanList as $angkatan)
                                        <option value="{{ $angkatan }}" {{ request('angkatan') == $angkatan ? 'selected' : '' }}>{{ $angkatan }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 transition-colors">
                                <i class="fa-solid fa-magnifying-glass"></i>
                            </button>
                            @if(request()->anyFilled(['search', 'angkatan']))
                                <a href="{{ route('pembagian_kelas.manage', $kelas->kode_kelas) }}" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded hover:bg-gray-300 transition-colors">
                                    <i class="fa-solid fa-rotate-left"></i>
                                </a>
                            @endif
                        </form>
                    </div>

                    <form action="{{ route('pembagian_kelas.bulk') }}" method="POST" @submit="isLoading = true">
                        @csrf
                        <input type="hidden" name="kode_kelas" value="{{ $kelas->kode_kelas }}">
                        
                        <div class="overflow-x-auto max-h-[480px] overflow-y-auto">
                            <table class="w-full">
                                <thead class="bg-gray-100 border-b border-gray-200 sticky top-0 z-10">
                                    <tr>
                                        <th class="px-4 py-3 text-center">
                                            <input type="checkbox" @click="toggleSelectAll()" x-model="selectAll" class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Siswa</th>
                                        <th class="px-4 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">Angkatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100">
                                    @forelse($siswaTersedia as $siswa)
                                        <tr class="transition-colors" :class="selectedIds.includes('{{ $siswa->nis }}') ? 'bg-blue-50' : ''">
                                            <td class="px-4 py-3 text-center">
                                                <input type="checkbox" name="selected_nis[]" value="{{ $siswa->nis }}" x-model="selectedIds" 
                                                       class="siswa-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                            </td>
                                            <td class="px-4 py-3">
                                                <div class="text-sm font-semibold text-gray-900">{{ $siswa->nama_siswa }}</div>
                                                <div class="text-[10px] text-gray-500">NIS: {{ $siswa->nis }}</div>
                                            </td>
                                            <td class="px-4 py-3 text-sm text-gray-600">{{ $siswa->angkatan }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-6 py-8 text-center text-sm text-gray-500 italic">
                                                Tidak ada siswa tersedia untuk dimasukkan.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- Pagination Links for Pool --}}
                        <div class="p-4 bg-gray-50/30 border-t border-gray-100">
                            <x-pagination :paginator="$siswaTersedia" />
                        </div>

                        <div class="p-4 bg-gray-50 border-t border-gray-200 sticky bottom-0 z-10">
                            <div class="flex items-center justify-between gap-4">
                                <div class="flex flex-col">
                                    <span class="text-xs font-medium text-gray-600"><span x-text="selectedIds.length"></span> siswa dipilih</span>
                                    @if($siswaTersedia->total() > 0)
                                        <span class="text-[10px] text-gray-400">Total: {{ $siswaTersedia->total() }} siswa belum ada kelas</span>
                                    @endif
                                </div>
                                <button type="submit" x-show="selectedIds.length > 0" x-transition
                                        :disabled="isLoading"
                                        class="inline-flex items-center gap-2 px-6 py-2 bg-blue-600 text-white text-sm font-bold rounded hover:bg-blue-700 transition-all shadow-md disabled:opacity-70 disabled:cursor-not-allowed">
                                    <template x-if="!isLoading">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-plus-circle"></i>
                                            <span>Masukkan ke Kelas</span>
                                        </div>
                                    </template>
                                    <template x-if="isLoading">
                                        <div class="flex items-center gap-2">
                                            <i class="fa-solid fa-spinner animate-spin"></i>
                                            <span>Memproses...</span>
                                        </div>
                                    </template>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
