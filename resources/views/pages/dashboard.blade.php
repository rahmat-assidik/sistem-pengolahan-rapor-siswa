@extends('layouts.app')

@section('title', 'Dashboard')

@push('head-scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    {{-- Welcome Header --}}
    <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Selamat Datang di Sistem Rapor!</h1>
            <p class="text-sm text-gray-500 font-medium">Berikut adalah ringkasan data akademik sistem hari ini.</p>
        </div>
        
        @if($semesterAktif)
        <div class="flex items-center gap-3 px-4 py-2.5 bg-white border border-gray-200 rounded shadow-sm">
            <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded flex items-center justify-center">
                <i class="fa-solid fa-calendar-check text-xs"></i>
            </div>
            <div>
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none mb-1">Semester Aktif</p>
                <p class="text-xs font-black text-gray-900 leading-none">{{ $semesterAktif->semester }} - {{ $semesterAktif->tahunAjaran->nama }}</p>
            </div>
        </div>
        @endif
    </div>

    {{-- Statistics Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        @if(auth()->user()->isAdmin())
            <x-stat-card label="Total Siswa" value="{{ $totalSiswa }}"
                icon='<i class="fa-solid fa-user-graduate text-blue-500"></i>' />
            <x-stat-card label="Total Guru" value="{{ $totalGuru }}"
                icon='<i class="fa-solid fa-chalkboard-user text-emerald-500"></i>' />
            <x-stat-card label="Total Kelas" value="{{ $totalKelas }}"
                icon='<i class="fa-solid fa-school text-amber-500"></i>' />
            <x-stat-card label="Total Mapel" value="{{ $totalMapel }}"
                icon='<i class="fa-solid fa-book text-rose-500"></i>' />
        @else
            <x-stat-card label="Mapel Diampu" value="{{ $mapelDiampu }}"
                icon='<i class="fa-solid fa-book-open text-blue-500"></i>' />
            <x-stat-card label="Kelas Diampu" value="{{ $kelasDiampu }}"
                icon='<i class="fa-solid fa-chalkboard text-emerald-500"></i>' />
            <x-stat-card label="Siswa Diampu" value="{{ $totalSiswaDiampu }}"
                icon='<i class="fa-solid fa-users text-amber-500"></i>' />
            <x-stat-card label="Nilai Terinput" value="{{ $totalNilaiTerisi }}"
                icon='<i class="fa-solid fa-file-signature text-rose-500"></i>' />
        @endif
    </div>

    {{-- Main Insights --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Progress Input Card --}}
        <div class="lg:col-span-1 bg-white rounded border border-gray-200 p-6 shadow-sm flex flex-col">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="fa-solid fa-spinner text-blue-500"></i>
                Progres Pengisian Nilai
            </h3>
            
            <div class="flex-1 flex flex-col justify-center items-center py-4">
                @php
                    $percentage = $totalSiswaDiampu > 0 ? round(($totalNilaiTerisi / $totalSiswaDiampu) * 100) : 0;
                @endphp
                
                <div class="relative w-40 h-40">
                    <canvas id="progressCircle"></canvas>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-3xl font-black text-gray-900">{{ $percentage }}%</span>
                        <span class="text-[10px] font-bold text-gray-500 uppercase">Selesai</span>
                    </div>
                </div>

                <div class="mt-8 w-full space-y-3">
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-gray-500">Nilai Terisi</span>
                        <span class="text-gray-900">{{ $totalNilaiTerisi }}</span>
                    </div>
                    <div class="w-full h-1.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <div class="flex justify-between text-xs font-bold">
                        <span class="text-gray-500">Belum Terisi</span>
                        <span class="text-gray-900">{{ max(0, $totalSiswaDiampu - $totalNilaiTerisi) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Distribution Chart Card --}}
        <div class="lg:col-span-2 bg-white rounded border border-gray-200 p-6 shadow-sm">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-6 flex items-center gap-2">
                <i class="fa-solid fa-chart-column text-emerald-500"></i>
                Distribusi Predikat Per Kelas ({{ $semesterAktif?->semester ?? '-' }})
            </h3>
            
            <div class="h-64">
                <canvas id="distribusiChart"></canvas>
            </div>

            <div class="mt-6 flex flex-wrap items-center justify-center gap-6 p-4 bg-gray-50 rounded border border-gray-100">
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-emerald-500 rounded-sm"></div>
                    <span class="text-[10px] font-bold text-gray-600 uppercase">Predikat A</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-sky-500 rounded-sm"></div>
                    <span class="text-[10px] font-bold text-gray-600 uppercase">Predikat B</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-amber-500 rounded-sm"></div>
                    <span class="text-[10px] font-bold text-gray-600 uppercase">Predikat C</span>
                </div>
                <div class="flex items-center gap-2">
                    <div class="w-3 h-3 bg-rose-500 rounded-sm"></div>
                    <span class="text-[10px] font-bold text-gray-600 uppercase">Predikat D</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Progress Circle Chart
        const progCtx = document.getElementById('progressCircle').getContext('2d');
        new Chart(progCtx, {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [{{ $totalNilaiTerisi }}, {{ max(1, $totalSiswaDiampu - $totalNilaiTerisi) }}],
                    backgroundColor: ['#10b981', '#f3f4f6'],
                    borderWidth: 0,
                    hoverOffset: 0
                }]
            },
            options: {
                cutout: '85%',
                responsive: true,
                maintainAspectRatio: true,
                plugins: { legend: { display: false } }
            }
        });

        // Distribution Bar Chart
        const distribusiCtx = document.getElementById('distribusiChart').getContext('2d');
        const dataPerKelas = @json($distribusiPerKelas);
        const labels = Object.keys(dataPerKelas);
        
        new Chart(distribusiCtx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [
                    { label: 'A', data: labels.map(k => dataPerKelas[k].A), backgroundColor: '#10b981', borderRadius: 4 },
                    { label: 'B', data: labels.map(k => dataPerKelas[k].B), backgroundColor: '#0ea5e9', borderRadius: 4 },
                    { label: 'C', data: labels.map(k => dataPerKelas[k].C), backgroundColor: '#f59e0b', borderRadius: 4 },
                    { label: 'D', data: labels.map(k => dataPerKelas[k].D), backgroundColor: '#ef4444', borderRadius: 4 }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], drawBorder: false },
                        ticks: { font: { size: 10, weight: 'bold' }, stepSize: 1 }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 10, weight: 'bold' } }
                    }
                }
            }
        });
    </script>
@endpush