@extends('layouts.app')
@section('title', 'Input Nilai')

@section('content')
    <div class="max-w-full" x-data="inputNilai()">
        <!-- Header Section - Flat & Clean -->
        <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Input Capaian Belajar</h2>
            <p class="text-sm text-gray-500 mt-1">Kelola nilai akademik siswa untuk mata pelajaran dan kelas yang diampu.</p>
        </div>

        <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
            <!-- Form Simpan (POST) -->
            <form action="{{ route('input_nilai.store') }}" method="POST" id="formSimpanNilai">
                @csrf
                <input type="hidden" name="pengampu_id" value="{{ $selectedPengampu?->id }}">

                <!-- Toolbar & Filter -->
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                        <div class="flex flex-wrap items-center gap-3">
                            <select name="mapel_id_filter" @change="location.href='{{ route('input_nilai') }}?mapel_id='+$event.target.value+'&kelas_id={{ request('kelas_id') }}'" class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-300 rounded bg-white focus:border-gray-900 outline-none transition-colors cursor-pointer">
                                @foreach($mapelList as $mapel)
                                    <option value="{{ $mapel->id }}" {{ ($selectedPengampu && $selectedPengampu->mapel_id == $mapel->id) || request('mapel_id') == $mapel->id ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </select>
                            <select name="kelas_id_filter" @change="location.href='{{ route('input_nilai') }}?mapel_id={{ request('mapel_id') }}&kelas_id='+$event.target.value" class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-300 rounded bg-white focus:border-gray-900 outline-none transition-colors cursor-pointer">
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ ($selectedPengampu && $selectedPengampu->kelas_id == $kelas->id) || request('kelas_id') == $kelas->id ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-3 w-full lg:w-auto">
                            <button type="button" 
                                    @click="isEditing = !isEditing"
                                    :class="isEditing ? 'bg-amber-600' : 'bg-blue-600'" 
                                    class="px-5 py-2 text-white text-sm font-semibold rounded transition-colors flex items-center gap-2">
                                <i class="fa-solid" :class="isEditing ? 'fa-xmark' : 'fa-pen-to-square'"></i>
                                <span x-text="isEditing ? 'Batal Edit' : 'Edit Nilai'"></span>
                            </button>
                            
                            <button type="submit" x-show="isEditing"
                                    class="px-5 py-2 bg-gray-900 text-white text-sm font-semibold rounded transition-colors flex items-center gap-2">
                                <i class="fa-solid fa-floppy-disk"></i>
                                <span>Simpan Perubahan</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full border-collapse">
                        <thead class="bg-gray-900">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-tight">Nama Siswa</th>
                                <th class="px-4 py-4 text-center text-xs font-bold text-white tracking-tight">Tugas</th>
                                <th class="px-4 py-4 text-center text-xs font-bold text-white tracking-tight">Ulangan</th>
                                <th class="px-4 py-4 text-center text-xs font-bold text-white tracking-tight">UTS</th>
                                <th class="px-4 py-4 text-center text-xs font-bold text-white tracking-tight">UAS</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-tight">Nilai Akhir</th>
                                <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-tight">Predikat</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($siswaJsonData as $index => $siswa)
                                <tr class="">
                                    <td class="px-6 py-4">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900">{{ $siswa['nama'] }}</span>
                                            <span class="text-[11px] text-gray-400 font-medium">{{ $siswa['nis'] }}</span>
                                        </div>
                                    </td>

                                    <td class="px-4 py-4 text-center">
                                        <input type="number" step="0.01" name="nilai[{{ $siswa['nis'] }}][tugas]" x-model.number="siswaList[{{ $index }}].tugas" @input="updateAll(siswaList[{{ $index }}])" :disabled="!isEditing" class="w-16 px-1 py-2 text-sm font-semibold text-center border border-gray-200 rounded focus:border-gray-900 outline-none transition-colors" :class="!isEditing ? 'bg-gray-50 border-transparent text-gray-500' : 'bg-white'">
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <input type="number" step="0.01" name="nilai[{{ $siswa['nis'] }}][ulangan]" x-model.number="siswaList[{{ $index }}].ulangan" @input="updateAll(siswaList[{{ $index }}])" :disabled="!isEditing" class="w-16 px-1 py-2 text-sm font-semibold text-center border border-gray-200 rounded focus:border-gray-900 outline-none transition-colors" :class="!isEditing ? 'bg-gray-50 border-transparent text-gray-500' : 'bg-white'">
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <input type="number" step="0.01" name="nilai[{{ $siswa['nis'] }}][uts]" x-model.number="siswaList[{{ $index }}].uts" @input="updateAll(siswaList[{{ $index }}])" :disabled="!isEditing" class="w-16 px-1 py-2 text-sm font-semibold text-center border border-gray-200 rounded focus:border-gray-900 outline-none transition-colors" :class="!isEditing ? 'bg-gray-50 border-transparent text-gray-500' : 'bg-white'">
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <input type="number" step="0.01" name="nilai[{{ $siswa['nis'] }}][uas]" x-model.number="siswaList[{{ $index }}].uas" @input="updateAll(siswaList[{{ $index }}])" :disabled="!isEditing" class="w-16 px-1 py-2 text-sm font-semibold text-center border border-gray-200 rounded focus:border-gray-900 outline-none transition-colors" :class="!isEditing ? 'bg-gray-50 border-transparent text-gray-500' : 'bg-white'">
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <input type="number" step="0.01" name="nilai[{{ $siswa['nis'] }}][nilai_akhir]" x-model.number="siswaList[{{ $index }}].nilai_akhir" @input="updateAll(siswaList[{{ $index }}], true)" :disabled="!isEditing" class="w-16 px-1 py-2 text-sm font-bold text-center border border-gray-200 rounded focus:border-gray-900 outline-none transition-colors text-blue-600" :class="!isEditing ? 'bg-blue-50 border-transparent text-blue-500' : 'bg-blue-50'">
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <div class="inline-block px-3 py-1 text-[10px] font-bold rounded-lg" :class="getPredikatClass(siswaList[{{ $index }}])" x-text="siswaList[{{ $index }}].predikat || '-'"></div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

            <div class="p-6 bg-gray-50/30 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-[11px] text-gray-400 font-medium italic"><i class="fa-solid fa-circle-info mr-1"></i> Nilai akhir dihitung otomatis rata-rata (Tugas, Ulangan, UTS, UAS) jika kosong, tapi bisa diedit manual.</p>
                <div class="w-full md:w-auto">
                    <x-pagination :paginator="$siswaList" />
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function inputNilai() {
    return {
        isEditing: false,
        siswaList: @json($siswaJsonData),

        updateAll(siswa, manualEdit = false) {
            // Hitung nilai akhir otomatis jika tidak diedit manual
            if (!manualEdit) {
                const t = parseFloat(siswa.tugas) || 0;
                const u = parseFloat(siswa.ulangan) || 0;
                const uts = parseFloat(siswa.uts) || 0;
                const uas = parseFloat(siswa.uas) || 0;
                
                let count = 0;
                let sum = 0;
                if(siswa.tugas !== null && siswa.tugas !== '') { sum += t; count++; }
                if(siswa.ulangan !== null && siswa.ulangan !== '') { sum += u; count++; }
                if(siswa.uts !== null && siswa.uts !== '') { sum += uts; count++; }
                if(siswa.uas !== null && siswa.uas !== '') { sum += uas; count++; }

                if (count > 0) {
                    siswa.nilai_akhir = Math.round((sum / count) * 100) / 100;
                } else {
                    siswa.nilai_akhir = null;
                }
            }

            siswa.predikat = this.calcPredikat(siswa.nilai_akhir);
        },
        calcPredikat(n) { 
            if(n === null || n === '') return '';
            if(n >= 90) return 'A'; 
            if(n >= 80) return 'B'; 
            if(n >= 70) return 'C'; 
            if(n > 0) return 'D'; 
            return ''; 
        },
        getPredikatClass(siswa) {
            const p = siswa.predikat;
            if(p === 'A') return 'bg-green-100 text-green-700'; if(p === 'B') return 'bg-blue-100 text-blue-700';
            if(p === 'C') return 'bg-yellow-100 text-yellow-700'; if(p === 'D') return 'bg-red-100 text-red-700';
            return 'bg-gray-100 text-gray-400';
        }
    }
}
</script>
@endpush