@extends('layouts.app')
@section('title', 'Input Nilai')

@section('content')
    <div class="max-w-full" x-data="inputNilai()">

        <div class="bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
            <!-- Form Simpan (POST) -->
            <form action="{{ route('input_nilai.store') }}" method="POST" id="formSimpanNilai">
                @csrf
                <input type="hidden" name="pengampu_id" value="{{ $selectedPengampu?->id }}">

                <!-- Toolbar & Filter -->
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex flex-col lg:flex-row items-start lg:items-center justify-between gap-6">
                        <div class="flex flex-wrap items-center gap-3">
                            <select name="mapel_id_filter" @change="location.href='{{ route('input_nilai') }}?mapel_id='+$event.target.value" class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-300 rounded bg-white focus:border-gray-900 outline-none transition-colors cursor-pointer">
                                @foreach($mapelList as $mapel)
                                    <option value="{{ $mapel->kode_mapel }}" {{ ($selectedPengampu && $selectedPengampu->mapel_id == $mapel->kode_mapel) ? 'selected' : '' }}>{{ $mapel->nama_mapel }}</option>
                                @endforeach
                            </select>
                            <select name="kelas_id_filter" @change="location.href='{{ route('input_nilai') }}?mapel_id={{ $selectedPengampu?->mapel_id }}&kelas_id='+$event.target.value" class="px-4 py-2 text-sm font-semibold text-gray-700 border border-gray-300 rounded bg-white focus:border-gray-900 outline-none transition-colors cursor-pointer">
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}" {{ ($selectedPengampu && $selectedPengampu->kelas_id == $kelas->id) ? 'selected' : '' }}>{{ $kelas->nama_kelas }}</option>
                                @endforeach
                            </select>

                            <div class="flex items-center gap-2 px-4 py-1.5 border border-gray-300 rounded bg-white">
                                <span class="text-xs font-bold text-gray-500 uppercase tracking-wider">KKM:</span>
                                <input type="number" name="kkm" x-model="kkm" :disabled="!isEditing"
                                       class="w-12 text-sm font-bold text-center text-gray-900 focus:outline-none disabled:bg-transparent"
                                       min="0" max="100">
                            </div>
                        </div>

                        <div class="flex items-center gap-3 w-full lg:w-auto">
                            <button type="button" 
                                    @click="isEditing = !isEditing"
                                    :class="isEditing ? 'bg-amber-600' : 'bg-blue-600'" 
                                    class="px-5 py-2 text-white text-sm font-semibold rounded transition-colors flex items-center gap-2">
                                <i class="fa-solid" :class="isEditing ? 'fa-xmark' : 'fa-pen-to-square'"></i>
                                <span x-text="isEditing ? 'Batal Edit' : 'Edit'"></span>
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
                                        <input type="hidden" name="nilai[{{ $siswa['nis'] }}][nilai_akhir]" :value="siswaList[{{ $index }}].nilai_akhir">
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded border transition-all" 
                                              :class="getNilaiAkhirClass(siswaList[{{ $index }}])" 
                                              x-text="siswaList[{{ $index }}].nilai_akhir || '-'"></span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="inline-flex items-center px-3 py-1 text-xs font-bold rounded border transition-all" 
                                              :class="getPredikatClass(siswaList[{{ $index }}])" 
                                              x-text="siswaList[{{ $index }}].predikat || '-'"></span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </form>

            <div class="p-6 bg-gray-50/30 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-[11px] text-gray-400 font-medium italic"><i class="fa-solid fa-circle-info mr-1"></i> Nilai akhir dihitung otomatis dari rata-rata Tugas, Ulangan, UTS, dan UAS.</p>
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
        kkm: {{ $selectedPengampu->kkm ?? 75 }},
        weights: {
            tugas: {{ $settings->bobot_tugas ?? 30 }},
            ulangan: {{ $settings->bobot_ulangan ?? 20 }},
            uts: {{ $settings->bobot_uts ?? 25 }},
            uas: {{ $settings->bobot_uas ?? 25 }}
        },
        siswaList: @json($siswaJsonData),

        updateAll(siswa) {
            // Hitung nilai akhir otomatis berdasarkan bobot
            const t = parseFloat(siswa.tugas) || 0;
            const u = parseFloat(siswa.ulangan) || 0;
            const uts = parseFloat(siswa.uts) || 0;
            const uas = parseFloat(siswa.uas) || 0;
            
            // Hitung total bobot yang tersedia (jika ada komponen yang kosong, 
            // apakah tetap dihitung atau diproporsikan? Biasanya tetap dihitung 0 atau sesuai aturan sekolah.
            // Di sini kita gunakan rumus standar: (T*bT + U*bU + UTS*bUTS + UAS*bUAS) / 100
            
            const total = (t * this.weights.tugas) + 
                          (u * this.weights.ulangan) + 
                          (uts * this.weights.uts) + 
                          (uas * this.weights.uas);
            
            siswa.nilai_akhir = Math.round((total / 100) * 100) / 100;

            if (siswa.tugas === null && siswa.ulangan === null && siswa.uts === null && siswa.uas === null) {
                siswa.nilai_akhir = null;
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
            if(p === 'A') return 'bg-emerald-600 text-white border-emerald-600'; 
            if(p === 'B') return 'bg-sky-600 text-white border-sky-600';
            if(p === 'C') return 'bg-amber-500 text-white border-amber-500'; 
            if(p === 'D') return 'bg-rose-600 text-white border-rose-600';
            return 'bg-gray-100 text-gray-400 border-gray-200';
        },
        getNilaiAkhirClass(siswa) {
            if(!siswa.nilai_akhir) return 'bg-gray-100 text-gray-400 border-gray-200';
            return 'bg-violet-600 text-white border-violet-600';
        }
    }
}
</script>
@endpush