@extends('layouts.app')
@section('title', 'Pengaturan Bobot Nilai')

@push('head-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto" x-data="bobotManager()">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- Form Section --}}
            <div class="lg:col-span-3 bg-white rounded border border-gray-200 overflow-hidden shadow-sm">
                <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-900 rounded flex items-center justify-center text-white shadow-sm">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-sm font-bold text-gray-900 leading-none">Pengaturan Bobot Nilai</h1>
                            <p class="text-[10px] text-gray-500 font-medium mt-1 uppercase tracking-wider">Tentukan persentase penilaian global</p>
                        </div>
                    </div>
                </div>

                <form action="{{ route('settings.update') }}" method="POST" class="p-6">
                    @csrf
                    @method('PUT')

                    <div class="space-y-6">
                        <div class="p-4 bg-sky-50 border border-sky-100 rounded flex items-start gap-3">
                            <i class="fa-solid fa-circle-info text-sky-500 mt-0.5"></i>
                            <p class="text-[11px] text-sky-700 leading-relaxed font-medium">
                                Total persentase dari keempat komponen di bawah ini <span class="font-bold">harus berjumlah 100%</span>. 
                                Perubahan akan langsung berdampak pada perhitungan Nilai Akhir di seluruh mata pelajaran.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Bobot Tugas (%)</label>
                                <input type="number" name="bobot_tugas" x-model.number="tugas" @input="updateChart()" min="0" max="100" required
                                       class="w-full px-4 py-2.5 text-sm font-bold border border-gray-300 rounded focus:border-gray-900 outline-none transition-all bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Bobot Ulangan (%)</label>
                                <input type="number" name="bobot_ulangan" x-model.number="ulangan" @input="updateChart()" min="0" max="100" required
                                       class="w-full px-4 py-2.5 text-sm font-bold border border-gray-300 rounded focus:border-gray-900 outline-none transition-all bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Bobot UTS (%)</label>
                                <input type="number" name="bobot_uts" x-model.number="uts" @input="updateChart()" min="0" max="100" required
                                       class="w-full px-4 py-2.5 text-sm font-bold border border-gray-300 rounded focus:border-gray-900 outline-none transition-all bg-gray-50">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Bobot UAS (%)</label>
                                <input type="number" name="bobot_uas" x-model.number="uas" @input="updateChart()" min="0" max="100" required
                                       class="w-full px-4 py-2.5 text-sm font-bold border border-gray-300 rounded focus:border-gray-900 outline-none transition-all bg-gray-50">
                            </div>
                        </div>

                        {{-- Total Progress --}}
                        <div class="pt-4 border-t border-gray-100">
                            <div class="flex items-center justify-between mb-2">
                                <span class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Total Persentase</span>
                                <span class="text-xs font-bold" :class="total === 100 ? 'text-emerald-600' : 'text-rose-600'" x-text="total + '%'"></span>
                            </div>
                            <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                                <div class="h-full transition-all duration-500" 
                                     :style="'width: ' + Math.min(total, 100) + '%'"
                                     :class="total === 100 ? 'bg-emerald-500' : 'bg-rose-500'"></div>
                            </div>
                        </div>

                        @if(session('error'))
                            <div class="p-3 bg-rose-50 border border-rose-200 rounded text-[11px] font-bold text-rose-600 flex items-center gap-2 uppercase tracking-tight">
                                <i class="fa-solid fa-triangle-exclamation"></i>
                                {{ session('error') }}
                            </div>
                        @endif

                        @if(session('success'))
                            <div class="p-3 bg-emerald-50 border border-emerald-200 rounded text-[11px] font-bold text-emerald-600 flex items-center gap-2 uppercase tracking-tight">
                                <i class="fa-solid fa-circle-check"></i>
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
                        <button type="submit" :disabled="total !== 100" 
                                class="px-6 py-2.5 bg-gray-900 text-white text-xs font-bold rounded hover:bg-gray-800 transition-all shadow-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed uppercase tracking-wider">
                            <i class="fa-solid fa-check"></i>
                            <span>Simpan Perubahan</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Visualization Section --}}
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded border border-gray-200 p-6 shadow-sm">
                    <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                        <i class="fa-solid fa-chart-pie text-gray-400"></i>
                        Visualisasi Bobot
                    </h3>
                    <div class="relative aspect-square">
                        <canvas id="bobotChart"></canvas>
                    </div>
                    <div class="mt-8 space-y-3">
                        <template x-for="(val, key) in {Tugas: tugas, Ulangan: ulangan, UTS: uts, UAS: uas}" :key="key">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="w-2 h-2 rounded-full" :class="{
                                        'bg-emerald-500': key === 'Tugas',
                                        'bg-sky-500': key === 'Ulangan',
                                        'bg-amber-500': key === 'UTS',
                                        'bg-violet-500': key === 'UAS'
                                    }"></div>
                                    <span class="text-[11px] font-bold text-gray-600 uppercase tracking-tight" x-text="key"></span>
                                </div>
                                <span class="text-xs font-bold text-gray-900" x-text="val + '%'"></span>
                            </div>
                        </template>
                    </div>
                </div>

                <div class="bg-gray-900 rounded border border-gray-800 p-6 shadow-sm text-white">
                    <h3 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-3">Info Perhitungan</h3>
                    <p class="text-[11px] text-gray-300 leading-relaxed font-medium italic">
                        "Nilai Akhir = (Tugas × <span x-text="tugas"></span>%) + (Ulangan × <span x-text="ulangan"></span>%) + (UTS × <span x-text="uts"></span>%) + (UAS × <span x-text="uas"></span>%)"
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function bobotManager() {
    return {
        tugas: {{ $setting->bobot_tugas }},
        ulangan: {{ $setting->bobot_ulangan }},
        uts: {{ $setting->bobot_uts }},
        uas: {{ $setting->bobot_uas }},
        chart: null,

        get total() {
            return (Number(this.tugas) || 0) + (Number(this.ulangan) || 0) + (Number(this.uts) || 0) + (Number(this.uas) || 0);
        },

        init() {
            const ctx = document.getElementById('bobotChart').getContext('2d');
            this.chart = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Tugas', 'Ulangan', 'UTS', 'UAS'],
                    datasets: [{
                        data: [this.tugas, this.ulangan, this.uts, this.uas],
                        backgroundColor: ['#10b981', '#0ea5e9', '#f59e0b', '#8b5cf6'],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: true,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false }
                    }
                }
            });
        },

        updateChart() {
            this.chart.data.datasets[0].data = [this.tugas, this.ulangan, this.uts, this.uas];
            this.chart.update();
        }
    }
}
</script>
@endpush
