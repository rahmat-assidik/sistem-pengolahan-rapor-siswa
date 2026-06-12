@extends('layouts.app')

@section('title', 'Pengaturan Akademik')

@section('content')
<div x-data="{ 
    openTA: false, 
    openEditTA: false, 
    openSemester: false, 
    taId: null, 
    taNama: '', 
    taMulai: '', 
    taSelesai: '' 
}">
    <div class="space-y-6">
    {{-- Toolbar --}}
    <div class="bg-white border border-gray-200 rounded p-4 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-gray-900 rounded flex items-center justify-center text-white shadow-sm">
                <i class="fa-solid fa-calendar-check text-xs"></i>
            </div>
            <div>
                <h1 class="text-sm font-bold text-gray-900 leading-none">Manajemen Periode Akademik</h1>
                <p class="text-[10px] text-gray-500 font-medium mt-1">Kelola tahun ajaran dan aktifkan semester berjalan.</p>
            </div>
        </div>
        <button @click="openTA = true" 
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gray-900 text-white text-xs font-bold rounded hover:bg-gray-800 transition-all shadow-sm">
            <i class="fa-solid fa-plus text-[10px]"></i>
            <span>Tambah Tahun Ajaran</span>
        </button>
    </div>
 
    {{-- Grid Content --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @forelse($tahunAjaran as $ta)
        <div class="bg-white border border-gray-200 rounded shadow-sm overflow-hidden flex flex-col transition-all">
            {{-- Year Header --}}
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded bg-white border border-gray-200 shadow-sm flex items-center justify-center text-gray-900 font-bold">
                        <i class="fa-solid fa-calendar-days text-xs"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-gray-900 tracking-tight">Tahun Pelajaran {{ $ta->nama }}</h3>
                        <p class="text-[10px] text-gray-400 font-medium mt-0.5 tracking-wide">
                            <i class="fa-regular fa-clock mr-1 text-[9px]"></i>
                            {{ $ta->tanggal_mulai->format('d M Y') }} — {{ $ta->tanggal_selesai->format('d M Y') }}
                        </p>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    @if($ta->is_aktif)
                        <span class="px-2 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-bold rounded uppercase tracking-wider border border-emerald-200">Aktif</span>
                    @endif
                    <button @click="
                        openEditTA = true; 
                        taId = '{{ $ta->id }}'; 
                        taNama = '{{ $ta->nama }}';
                        taMulai = '{{ $ta->tanggal_mulai->format('Y-m-d') }}';
                        taSelesai = '{{ $ta->tanggal_selesai->format('Y-m-d') }}';
                    " class="w-8 h-8 flex items-center justify-center bg-white border border-gray-200 rounded text-gray-500 hover:text-gray-900 hover:border-gray-900 transition-all shadow-sm">
                        <i class="fa-solid fa-pen-to-square text-[10px]"></i>
                    </button>
                </div>
            </div>

            {{-- Semester Options --}}
            <div class="p-5 grid grid-cols-1 sm:grid-cols-2 gap-4 flex-1 bg-white">
                @foreach(['Ganjil', 'Genap'] as $smt_type)
                    @php 
                        $smt = $ta->semester->where('semester', $smt_type)->first(); 
                    @endphp
                    
                    <div class="p-4 rounded border {{ $smt && $smt->is_aktif ? 'bg-blue-50/50 border-blue-200' : 'bg-gray-50 border-gray-100' }} flex flex-col justify-between">
                        <div class="mb-4">
                            <span class="text-[10px] font-bold uppercase tracking-wider {{ $smt && $smt->is_aktif ? 'text-blue-600' : 'text-gray-400' }}">Semester</span>
                            <h4 class="text-base font-bold text-gray-900">{{ $smt_type }}</h4>
                        </div>

                        @if($smt)
                            @if($smt->is_aktif)
                                <form action="{{ route('akademik.ta.nonaktifkan', $ta->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-2 bg-red-600 text-white text-xs font-bold rounded shadow-sm hover:bg-red-700 transition-all flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-lock text-[10px]"></i> Tutup
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('akademik.set_aktif', $smt->id) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full px-3 py-2 bg-gray-900 text-white text-xs font-bold rounded shadow-sm hover:bg-gray-800 transition-all flex items-center justify-center gap-2">
                                        <i class="fa-solid fa-play text-[10px]"></i> Aktifkan
                                    </button>
                                </form>
                            @endif
                        @else
                            <div class="w-full px-3 py-2 bg-white border border-gray-200 text-gray-300 text-[10px] font-bold rounded text-center italic cursor-not-allowed uppercase tracking-wider">
                                Kosong
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white border border-gray-200 rounded p-12 text-center shadow-sm">
            <div class="w-16 h-16 bg-gray-50 rounded flex items-center justify-center mx-auto mb-4 text-gray-300 border border-gray-100">
                <i class="fa-solid fa-calendar-xmark text-2xl"></i>
            </div>
            <h4 class="text-sm font-bold text-gray-900 uppercase tracking-widest">Data Kosong</h4>
            <p class="text-xs text-gray-500 mt-1">Silakan tambah tahun ajaran baru.</p>
        </div>
        @endforelse
    </div>

    </div> {{-- Closing space-y-6 --}}
 
    {{-- Modal Tahun Ajaran --}}
    <x-modal name="openTA" title="Tambah Tahun Ajaran">
        <form action="{{ route('akademik.ta.store') }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="p-3 bg-blue-50 border border-blue-100 rounded flex items-start gap-3">
                    <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                    <p class="text-[11px] text-blue-700 leading-relaxed">Nama Tahun Pelajaran akan dibuat secara otomatis dalam format <span class="font-bold">YYYY/YYYY</span> berdasarkan Tanggal Mulai yang Anda pilih.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-8">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition-colors">
                    <i class="fa-solid fa-check"></i><span>Simpan Tahun Ajaran</span>
                </button>
                <button type="button" @click="openTA = false" class="px-6 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded hover:bg-gray-200 transition-colors">Batal</button>
            </div>
        </form>
    </x-modal>

    {{-- Modal Edit Tahun Ajaran --}}
    <x-modal name="openEditTA" title="Edit Tahun Ajaran">
        <form :action="'{{ route('akademik.ta.update', ['id' => 'ID_PLACEHOLDER']) }}'.replace('ID_PLACEHOLDER', taId)" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div class="p-3 bg-blue-50 border border-blue-100 rounded flex items-start gap-3">
                    <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                    <p class="text-[11px] text-blue-700 leading-relaxed">Nama Tahun Pelajaran <span class="font-bold" x-text="taNama"></span> akan diperbarui secara otomatis berdasarkan Tanggal Mulai yang baru.</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" required x-model="taMulai"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" required x-model="taSelesai"
                               class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50">
                    </div>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-8">
                <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-gray-900 text-white text-sm font-semibold rounded hover:bg-gray-800 transition-colors">
                    <i class="fa-solid fa-floppy-disk"></i><span>Simpan Perubahan</span>
                </button>
                <button type="button" @click="openEditTA = false" class="px-6 py-2.5 text-sm font-semibold text-gray-500 bg-gray-100 rounded hover:bg-gray-200 transition-colors">Batal</button>
            </div>
        </form>
    </x-modal>
</div> {{-- Closing x-data --}}
@endsection
