@extends('layouts.app')
@section('title', 'Konfigurasi Bobot Nilai')

@section('content')
<div class="w-full">
    <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
        @if(session('success'))
            <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 text-xs font-bold text-emerald-600 uppercase tracking-tight">
                <i class="fa-solid fa-circle-check mr-1"></i> {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="px-6 py-4 bg-rose-50 border-b border-rose-100 text-xs font-bold text-rose-600 uppercase tracking-tight">
                <i class="fa-solid fa-circle-xmark mr-1"></i> {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-900 border-b border-gray-800">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Mata Pelajaran</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Kelas</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Tugas (%)</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Ulangan (%)</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">UTS (%)</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">UAS (%)</th>
                        <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengampus as $pengampu)
                        <tr class="transition-colors" x-data="{
                            initTugas: {{ $bobots[$pengampu->id]->bobot_tugas ?? 0 }},
                            initUlangan: {{ $bobots[$pengampu->id]->bobot_ulangan ?? 0 }},
                            initUts: {{ $bobots[$pengampu->id]->bobot_uts ?? 0 }},
                            initUas: {{ $bobots[$pengampu->id]->bobot_uas ?? 0 }},
                            tugas: {{ $bobots[$pengampu->id]->bobot_tugas ?? 0 }},
                            ulangan: {{ $bobots[$pengampu->id]->bobot_ulangan ?? 0 }},
                            uts: {{ $bobots[$pengampu->id]->bobot_uts ?? 0 }},
                            uas: {{ $bobots[$pengampu->id]->bobot_uas ?? 0 }},
                            
                            get total() { 
                                return (Number(this.tugas) || 0) + (Number(this.ulangan) || 0) + (Number(this.uts) || 0) + (Number(this.uas) || 0); 
                            },
                            get isDirty() { 
                                return Number(this.tugas || 0) !== Number(this.initTugas || 0) || 
                                       Number(this.ulangan || 0) !== Number(this.initUlangan || 0) || 
                                       Number(this.uts || 0) !== Number(this.initUts || 0) || 
                                       Number(this.uas || 0) !== Number(this.initUas || 0); 
                            },
                            get inputClass() {
                                if (this.total !== 100) return 'border-rose-400 bg-rose-50';
                                if (this.isDirty) return 'border-amber-400 bg-amber-50';
                                return 'border-gray-300 bg-gray-50';
                            }
                        }">
                            <form action="{{ route('guru.bobot.update', $pengampu->id) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $pengampu->mapel->nama_mapel }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $pengampu->kelas->nama_kelas }}</td>
                                <td class="px-3 py-4"><input type="number" name="bobot_tugas" x-model.number="tugas" :class="inputClass" class="w-20 mx-auto px-2 py-1.5 text-center text-sm border rounded focus:border-gray-900 outline-none block transition-colors"></td>
                                <td class="px-3 py-4"><input type="number" name="bobot_ulangan" x-model.number="ulangan" :class="inputClass" class="w-20 mx-auto px-2 py-1.5 text-center text-sm border rounded focus:border-gray-900 outline-none block transition-colors"></td>
                                <td class="px-3 py-4"><input type="number" name="bobot_uts" x-model.number="uts" :class="inputClass" class="w-20 mx-auto px-2 py-1.5 text-center text-sm border rounded focus:border-gray-900 outline-none block transition-colors"></td>
                                <td class="px-3 py-4"><input type="number" name="bobot_uas" x-model.number="uas" :class="inputClass" class="w-20 mx-auto px-2 py-1.5 text-center text-sm border rounded focus:border-gray-900 outline-none block transition-colors"></td>
                                <td class="px-6 py-4 text-center">
                                    <button type="submit" 
                                            :disabled="total != 100 || !isDirty"
                                            class="inline-flex items-center gap-2 px-4 py-2 bg-gray-900 text-white text-[10px] font-bold uppercase rounded hover:bg-gray-800 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <i class="fa-solid fa-save"></i> Simpan
                                    </button>
                                    <span x-show="total != 100" class="block mt-1 text-[10px] font-bold text-rose-600" x-text="total + '%'"></span>
                                </td>
                            </form>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-gray-400 font-medium">Tidak ada mata pelajaran yang diampu.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
