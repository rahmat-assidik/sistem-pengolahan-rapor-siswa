@extends('layouts.app')
@section('title', 'Status Rapor')

@section('content')
    <div class="max-w-full" x-data="{ 
        openFilter: false,
        bulkMode: false,
        selectedIds: [],
        bulkStatus: 'Tuntas',
        toggleAll(checked) {
            if (checked) {
                this.selectedIds = [...document.querySelectorAll('input[name=kelas_siswa_checkbox]')].map(el => el.value);
            } else {
                this.selectedIds = [];
            }
        }
    }">
        {{-- Filter Modal --}}
        <x-modal name="openFilter" title="Pilih Kelas & Semester">
            <form action="{{ route('status_rapor') }}" method="GET">
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
                            @foreach($managedKelas as $kelas)
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

        {{-- Bulk Update Modal --}}
        <x-modal name="bulkMode" title="Ubah Status Rapor Massal">
            <form action="{{ route('status_rapor.bulk_update') }}" method="POST">
                @csrf
                @method('PUT')
                <template x-for="id in selectedIds" :key="id">
                    <input type="hidden" name="kelas_siswa_ids[]" :value="id">
                </template>
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-3">Anda akan mengubah status rapor untuk <span class="font-bold text-gray-900" x-text="selectedIds.length"></span> siswa yang dipilih.</p>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pilih Status</label>
                    <select name="status_rapor" x-model="bulkStatus" class="w-full border border-gray-300 rounded p-2 text-sm">
                        <option value="Tuntas">Tuntas</option>
                        <option value="Tidak Tuntas">Tidak Tuntas</option>
                        <option value="Belum Ditentukan">Belum Ditentukan</option>
                    </select>
                </div>
                <div class="flex items-center gap-3 mt-6">
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition-colors">
                        <i class="fa-solid fa-check-double"></i><span>Simpan Perubahan</span>
                    </button>
                    <button type="button" @click="bulkMode = false" class="px-6 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded hover:bg-gray-200 transition-colors">Batal</button>
                </div>
            </form>
        </x-modal>

        <div class="bg-white rounded border border-gray-200 shadow-sm">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <div>
                    <h2 class="text-lg font-bold text-gray-900">Status Rapor Siswa</h2>
                    <p class="text-xs text-gray-500 mt-0.5">Kelola status rapor (Tuntas / Tidak Tuntas) untuk siswa di kelas Anda</p>
                </div>
                <div class="flex gap-2">
                    @if($selectedKelas)
                        <div class="bg-gray-900 text-white px-4 py-2 rounded font-bold text-xs">
                            Kelas: {{ $selectedKelas->nama_kelas }}
                        </div>
                    @endif
                    <button @click="openFilter = true" class="px-4 py-2 bg-gray-900 text-white text-xs font-semibold rounded hover:bg-gray-800">
                        <i class="fa-solid fa-magnifying-glass"></i> Pilih Kelas
                    </button>
                </div>
            </div>

            @if($siswaData instanceof \Illuminate\Pagination\LengthAwarePaginator && $siswaData->isNotEmpty())
                {{-- Bulk Action Bar --}}
                <div class="px-5 py-3 bg-gray-50 border-b border-gray-100 flex items-center justify-between" x-show="selectedIds.length > 0" x-cloak>
                    <p class="text-xs font-medium text-gray-700">
                        <span class="text-gray-900 font-bold" x-text="selectedIds.length"></span> siswa dipilih
                    </p>
                    <button @click="bulkMode = true" class="inline-flex items-center gap-1.5 px-4 py-2 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded transition-colors">
                        <i class="fa-solid fa-check-double"></i> Ubah Status Massal
                    </button>
                </div>

                <div class="overflow-visible">
                    <table class="w-full">
                        <thead class="bg-gray-900">
                            <tr>
                                <th class="px-4 py-4 text-center">
                                    <input type="checkbox" class="rounded border-gray-300" @change="toggleAll($event.target.checked)">
                                </th>
                                <th class="px-4 py-4 text-left text-xs font-bold text-white tracking-wider">No</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">NIS</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Nama Siswa</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Rata-rata</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Status Rapor</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @foreach($siswaData as $i => $siswa)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-4 py-4 text-center">
                                    @if($siswa->kelas_siswa_id)
                                        <input type="checkbox" name="kelas_siswa_checkbox" value="{{ $siswa->kelas_siswa_id }}" class="rounded border-gray-300"
                                               x-model="selectedIds" :value="'{{ $siswa->kelas_siswa_id }}'">
                                    @endif
                                </td>
                                <td class="px-4 py-4 text-sm font-medium text-gray-900">{{ $siswaData->firstItem() + $i }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $siswa->nis }}</td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-semibold">{{ $siswa->nama_siswa }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($siswa->rata_rata !== null)
                                        <span class="text-sm font-bold {{ $siswa->rata_rata >= 75 ? 'text-emerald-600' : ($siswa->rata_rata >= 65 ? 'text-amber-600' : 'text-rose-600') }}">
                                            {{ $siswa->rata_rata }}
                                        </span>
                                    @else
                                        <span class="text-sm text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($siswa->status_rapor_value === 'Tuntas')
                                        <x-badge type="success">Tuntas</x-badge>
                                    @elseif($siswa->status_rapor_value === 'Tidak Tuntas')
                                        <x-badge type="danger">Tidak Tuntas</x-badge>
                                    @else
                                        <x-badge type="default">Belum Ditentukan</x-badge>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($siswa->kelas_siswa_id)
                                    <div x-data="{ openAction: false }" class="relative inline-block">
                                        <button @click="openAction = !openAction" class="inline-flex items-center gap-1.5 px-3 py-2 text-xs font-semibold text-gray-700 bg-gray-100 border border-gray-300 hover:bg-gray-200 rounded transition-colors">
                                            <i class="fa-solid fa-pen-to-square"></i> Ubah Status
                                            <i class="fa-solid fa-chevron-down text-[8px] ml-1"></i>
                                        </button>
                                        <div x-show="openAction" @click.away="openAction = false" x-cloak
                                             class="absolute right-0 mt-1 w-44 bg-white border border-gray-200 rounded shadow-lg z-50">
                                            <form action="{{ route('status_rapor.update') }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="kelas_siswa_id" value="{{ $siswa->kelas_siswa_id }}">
                                                <button type="submit" name="status_rapor" value="Tuntas" @click="openAction = false"
                                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-xs text-left hover:bg-emerald-50 transition-colors {{ $siswa->status_rapor_value === 'Tuntas' ? 'bg-emerald-50 font-bold text-emerald-700' : 'text-gray-700' }}">
                                                    <i class="fa-solid fa-circle-check text-emerald-500"></i> Tuntas
                                                </button>
                                            </form>
                                            <form action="{{ route('status_rapor.update') }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="kelas_siswa_id" value="{{ $siswa->kelas_siswa_id }}">
                                                <button type="submit" name="status_rapor" value="Tidak Tuntas" @click="openAction = false"
                                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-xs text-left hover:bg-rose-50 transition-colors {{ $siswa->status_rapor_value === 'Tidak Tuntas' ? 'bg-rose-50 font-bold text-rose-700' : 'text-gray-700' }}">
                                                    <i class="fa-solid fa-circle-xmark text-rose-500"></i> Tidak Tuntas
                                                </button>
                                            </form>
                                            <form action="{{ route('status_rapor.update') }}" method="POST">
                                                @csrf
                                                @method('PUT')
                                                <input type="hidden" name="kelas_siswa_id" value="{{ $siswa->kelas_siswa_id }}">
                                                <button type="submit" name="status_rapor" value="Belum Ditentukan" @click="openAction = false"
                                                        class="w-full flex items-center gap-2 px-4 py-2.5 text-xs text-left hover:bg-gray-50 transition-colors {{ $siswa->status_rapor_value === 'Belum Ditentukan' ? 'bg-gray-100 font-bold text-gray-900' : 'text-gray-700' }}">
                                                    <i class="fa-solid fa-minus-circle text-gray-400"></i> Belum Ditentukan
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                    @else
                                        <span class="text-xs text-gray-400">-</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="p-6 bg-gray-50/30 border-t border-gray-100"><x-pagination :paginator="$siswaData" /></div>
            @elseif($siswaData instanceof \Illuminate\Pagination\LengthAwarePaginator && $siswaData->isEmpty())
                <div class="p-10 text-center text-gray-500">
                    <i class="fa-solid fa-clipboard-list text-4xl mb-3 text-gray-300"></i>
                    <p class="text-sm font-medium">Tidak ada siswa ditemukan di kelas ini.</p>
                    <p class="text-xs">Pastikan siswa sudah ditempatkan di kelas pada semester yang dipilih.</p>
                </div>
            @else
                <div class="p-10 text-center text-gray-500">
                    <i class="fa-solid fa-clipboard-list text-4xl mb-3 text-gray-300"></i>
                    <p class="text-sm font-medium">Belum ada data yang ditampilkan.</p>
                    <p class="text-xs">Silakan gunakan tombol "Pilih Kelas" untuk memilih kelas dan semester.</p>
                </div>
            @endif
        </div>
    </div>
@endsection
