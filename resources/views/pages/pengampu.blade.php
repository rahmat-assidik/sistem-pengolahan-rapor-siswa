@extends('layouts.app')
@section('title', 'Pengampu')
@section('body-attrs') x-data="{ openTambah: false, openEdit: false, openLihat: false, selectedPengampu: {} }" @endsection

@section('content')

    {{-- Modal Tambah --}}
    <x-modal name="openTambah" title="Tambah Penugasan Pengampu">
        <form action="{{ route('pengampu.store') }}" method="POST">
            @csrf
            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mata Pelajaran</label>
                    <div class="relative">
                        <select name="mapel_id" required class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none bg-gray-50 transition-all appearance-none cursor-pointer">
                            <option value="" disabled selected>Pilih Mata Pelajaran</option>
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->kode_mapel }}">{{ $mapel->nama_mapel }} ({{ $mapel->kode_mapel }})</option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">KKM (Kriteria Ketuntasan Minimal)</label>
                    <input type="number" name="kkm" value="75" min="0" max="100" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-all bg-gray-50"
                           placeholder="Contoh: 75">
                </div>

                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: '',
                    selectedName: '',
                    gurus: {{ $gurus->map(fn($g) => ['id' => $g->nip, 'nama' => $g->nama_guru])->toJson() }},
                    get filteredGurus() {
                        if (this.search === '') return this.gurus;
                        return this.gurus.filter(g => g.nama.toLowerCase().includes(this.search.toLowerCase()));
                    },
                    selectGuru(guru) {
                        this.selectedId = guru.id;
                        this.selectedName = guru.nama;
                        this.search = guru.nama;
                        this.open = false;
                    }
                }" class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Guru Pengampu</label>
                    <div class="relative">
                        <input type="text"
                               x-model="search"
                               @focus="open = true"
                               @click.away="open = false; if(!selectedId) search = ''"
                               placeholder="Cari dan pilih guru..."
                               class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-all pr-10 bg-gray-50"
                               autocomplete="off">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs" x-show="!search"></i>
                            <i class="fa-solid fa-xmark text-xs cursor-pointer hover:text-gray-600" x-show="search" @click="search = ''; selectedId = ''; selectedName = ''"></i>
                        </div>
                    </div>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute z-[60] w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-y-auto"
                         style="display: none;">
                        <template x-for="guru in filteredGurus" :key="guru.id">
                            <div @click="selectGuru(guru)"
                                 class="px-4 py-2.5 text-sm cursor-pointer transition-colors flex items-center justify-between"
                                 :class="selectedId === guru.id ? 'bg-blue-50 text-blue-700 font-bold' : 'hover:bg-gray-50 text-gray-700'">
                                <span x-text="guru.nama"></span>
                                <i class="fa-solid fa-check text-[10px]" x-show="selectedId === guru.id"></i>
                            </div>
                        </template>
                        <div x-show="filteredGurus.length === 0" class="px-4 py-8 text-center text-gray-400">
                            <i class="fa-solid fa-user-slash block mb-2 text-lg"></i>
                            <p class="text-xs font-medium">Guru tidak ditemukan</p>
                        </div>
                    </div>
                    <input type="hidden" name="guru_id" :value="selectedId">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kelas</label>
                        <select name="kelas_id" required class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none bg-gray-50 transition-all appearance-none cursor-pointer">
                            <option value="" disabled selected>Pilih Kelas</option>
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}">{{ $k->nama_kelas }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Semester</label>
                        <select name="semester_id" required class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none bg-gray-50 transition-all appearance-none cursor-pointer">
                            @foreach($semesters as $smt)
                                <option value="{{ $smt->id }}" {{ $smt->is_aktif ? 'selected' : '' }}>
                                    {{ $smt->semester }} - {{ $smt->tahunAjaran->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                @if(session('error'))
                    <div class="px-4 py-3 bg-red-50 border border-red-200 rounded text-sm text-red-600">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i> {{ session('error') }}
                    </div>
                @endif

            </div>
            <div class="flex items-center gap-3 mt-8">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition-colors">
                    <i class="fa-solid fa-check"></i><span>Simpan Penugasan</span>
                </button>
                <button type="button" @click="openTambah = false" class="px-6 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded hover:bg-gray-200 transition-colors">Batal</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Edit --}}
    <x-modal name="openEdit" title="Edit Penugasan Pengampu">
        <form method="POST" :action="'/pengampu/' + selectedPengampu.id">
            @csrf
            @method('PUT')
            <div class="space-y-4">

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Mata Pelajaran</label>
                    <div class="relative">
                        <select name="mapel_id" required class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none bg-gray-50 transition-all appearance-none cursor-pointer">
                            @foreach($mapels as $mapel)
                                <option value="{{ $mapel->kode_mapel }}"
                                    x-bind:selected="selectedPengampu.mapel_id === '{{ $mapel->kode_mapel }}'">
                                    {{ $mapel->nama_mapel }} ({{ $mapel->kode_mapel }})
                                </option>
                            @endforeach
                        </select>
                        <i class="fa-solid fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none text-xs"></i>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">KKM (Kriteria Ketuntasan Minimal)</label>
                    <input type="number" name="kkm" x-model="selectedPengampu.kkm" min="0" max="100" required
                           class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-all bg-gray-50">
                </div>

                <div x-data="{
                    open: false,
                    search: '',
                    selectedId: '',
                    selectedName: '',
                    gurus: {{ $gurus->map(fn($g) => ['id' => $g->nip, 'nama' => $g->nama_guru])->toJson() }},
                    get filteredGurus() {
                        if (this.search === '') return this.gurus;
                        return this.gurus.filter(g => g.nama.toLowerCase().includes(this.search.toLowerCase()));
                    },
                    selectGuru(guru) {
                        this.selectedId = guru.id;
                        this.selectedName = guru.nama;
                        this.search = guru.nama;
                        this.open = false;
                    },
                    init() {
                        this.$watch('$root.selectedPengampu', (val) => {
                            if (val && val.guru_id) {
                                const found = this.gurus.find(g => g.id == val.guru_id);
                                if (found) {
                                    this.selectedId = found.id;
                                    this.search = found.nama;
                                }
                            }
                        });
                    }
                }" class="relative">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Guru Pengampu</label>
                    <div class="relative">
                        <input type="text"
                               x-model="search"
                               @focus="open = true"
                               @click.away="open = false; if(!selectedId) search = ''"
                               placeholder="Cari dan pilih guru..."
                               class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-all pr-10 bg-gray-50"
                               autocomplete="off">
                        <div class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400">
                            <i class="fa-solid fa-magnifying-glass text-xs" x-show="!search"></i>
                            <i class="fa-solid fa-xmark text-xs cursor-pointer hover:text-gray-600" x-show="search" @click="search = ''; selectedId = ''; selectedName = ''"></i>
                        </div>
                    </div>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="absolute z-[60] w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-xl max-h-48 overflow-y-auto"
                         style="display: none;">
                        <template x-for="guru in filteredGurus" :key="guru.id">
                            <div @click="selectGuru(guru)"
                                 class="px-4 py-2.5 text-sm cursor-pointer transition-colors flex items-center justify-between"
                                 :class="selectedId === guru.id ? 'bg-blue-50 text-blue-700 font-bold' : 'hover:bg-gray-50 text-gray-700'">
                                <span x-text="guru.nama"></span>
                                <i class="fa-solid fa-check text-[10px]" x-show="selectedId === guru.id"></i>
                            </div>
                        </template>
                        <div x-show="filteredGurus.length === 0" class="px-4 py-8 text-center text-gray-400">
                            <i class="fa-solid fa-user-slash block mb-2 text-lg"></i>
                            <p class="text-xs font-medium">Guru tidak ditemukan</p>
                        </div>
                    </div>
                    <input type="hidden" name="guru_id" :value="selectedId">
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kelas</label>
                        <select name="kelas_id" required class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none bg-gray-50 transition-all appearance-none cursor-pointer">
                            @foreach($kelas as $k)
                                <option value="{{ $k->id }}"
                                    x-bind:selected="selectedPengampu.kelas_id == {{ $k->id }}">
                                    {{ $k->nama_kelas }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Semester</label>
                        <select name="semester_id" required class="w-full px-4 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none bg-gray-50 transition-all appearance-none cursor-pointer">
                            @foreach($semesters as $smt)
                                <option value="{{ $smt->id }}"
                                    x-bind:selected="selectedPengampu.semester_id == {{ $smt->id }}">
                                    {{ $smt->semester }} - {{ $smt->tahunAjaran->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>
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

    {{-- Modal Lihat --}}
    <x-modal name="openLihat" title="Detail Penugasan Pengampu">
        <div class="space-y-4">
            <div class="grid grid-cols-3 py-2 border-b border-gray-100">
                <span class="text-sm font-semibold text-gray-500">Mata Pelajaran</span>
                <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedPengampu.mapel_nama + ' (' + selectedPengampu.mapel_id + ')'"></span>
            </div>
            <div class="grid grid-cols-3 py-2 border-b border-gray-100">
                <span class="text-sm font-semibold text-gray-500">Guru Pengampu</span>
                <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedPengampu.guru_nama"></span>
            </div>
            <div class="grid grid-cols-3 py-2 border-b border-gray-100">
                <span class="text-sm font-semibold text-gray-500">Kelas</span>
                <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedPengampu.kelas_nama"></span>
            </div>
            <div class="grid grid-cols-3 py-2 border-b border-gray-100">
                <span class="text-sm font-semibold text-gray-500">Semester</span>
                <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedPengampu.semester_nama"></span>
            </div>
            <div class="grid grid-cols-3 py-2 border-b border-gray-100">
                <span class="text-sm font-semibold text-gray-500">KKM</span>
                <span class="text-sm font-bold text-gray-900 col-span-2" x-text="selectedPengampu.kkm"></span>
            </div>
        </div>
        <div class="mt-8">
            <button type="button" @click="openLihat = false" class="w-full px-6 py-2.5 text-sm font-semibold text-white bg-gray-900 rounded hover:bg-gray-800 transition-colors">Tutup</button>
        </div>
    </x-modal>

    <div class="max-w-full">
        <div class="bg-white rounded border border-gray-200">
            <x-search-toolbar
                placeholder="Cari pengampu, guru..."
                :filters="[
                    ['name' => 'tahun_ajaran_id', 'label' => 'Tahun Ajaran', 'options' => $tahunAjaranList->pluck('nama', 'id')->toArray()],
                    ['name' => 'semester', 'label' => 'Semester', 'options' => ['Ganjil' => 'Ganjil', 'Genap' => 'Genap']],
                    ['name' => 'mapel_id', 'label' => 'Filter Mapel', 'options' => $mapels->pluck('nama_mapel', 'kode_mapel')->toArray()],
                    ['name' => 'kelas_id', 'label' => 'Filter Kelas', 'options' => $kelas->pluck('nama_kelas', 'id')->toArray()]
                ]"
                :resetUrl="route('pengampu')"
                tambahClick="openTambah = true"
            />
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Kode Mapel</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Nama Mapel</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">KKM</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Pengampu</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Kelas</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Tahun Ajaran/Sem</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($pengampus as $key => $p)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $pengampus->firstItem() + $key }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-semibold tracking-tight">{{ $p->mapel->kode_mapel }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-medium">{{ $p->mapel->nama_mapel }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $p->kkm }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $p->guru->nama_guru }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700">{{ $p->kelas->nama_kelas }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $p->semester->tahunAjaran->nama }} ({{ $p->semester->semester }})</td>
                            <td class="px-6 py-4 text-center">
                                <x-action-buttons
                                    :lihatClick="'selectedPengampu = ' . json_encode([
                                        'id'           => $p->id,
                                        'mapel_id'     => $p->mapel_id,
                                        'mapel_nama'   => $p->mapel->nama_mapel,
                                        'guru_nama'    => $p->guru->nama_guru,
                                        'kelas_nama'   => $p->kelas->nama_kelas,
                                        'semester_nama'=> $p->semester->tahunAjaran->nama . ' (' . $p->semester->semester . ')',
                                        'kkm'          => $p->kkm,
                                    ]) . '; openLihat = true'"
                                    :editClick="'selectedPengampu = ' . json_encode([
                                        'id'          => $p->id,
                                        'mapel_id'    => $p->mapel_id,
                                        'guru_id'     => $p->guru_id,
                                        'kelas_id'    => $p->kelas_id,
                                        'semester_id' => $p->semester_id,
                                        'kkm'         => $p->kkm,
                                    ]) . '; openEdit = true'"
                                    :hapusClick="'konfirmasiHapus(' . $p->id . ', \'' . addslashes($p->mapel->nama_mapel) . '\')'"
                                />
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-6 py-8 text-center text-gray-500">
                                <p class="text-sm font-medium">Tidak ada data pengampu</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="p-6 bg-gray-50/30 border-t border-gray-100"><x-pagination :paginator="$pengampus" /></div>
        </div>
    </div>

    <script>
        function konfirmasiHapus(id, nama) {
            Swal.fire({
                title: 'Hapus Penugasan Pengampu?',
                text: 'Apakah Anda yakin ingin menghapus pengampu ' + nama + '?',
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
                    form.action = '/pengampu/' + id;

                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = document.querySelector('meta[name=csrf-token]').getAttribute('content');

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
    </script>

@endsection