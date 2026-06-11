@extends('layouts.app')

@section('title', 'Dashboard')

@section('body-attrs')
{{-- No extra body attrs needed --}}
@endsection

@push('head-scripts')
    {{-- Chart.js --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
    {{-- Dashboard Header --}}
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900">Dashboard Overview</h1>
    </div>

    {{-- Statistics Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6 mb-8">
        <x-stat-card label="Total Siswa" value="{{ $totalSiswa }}"
            icon='<svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"></path></svg>' />

        <x-stat-card label="Total Guru" value="{{ $totalGuru }}"
            icon='<svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>' />

        <x-stat-card label="Total Kelas" value="{{ $totalKelas }}"
            icon='<svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 012-2h4.586a2 2 0 011.414.586l7.071 7.071a2 2 0 010 2.828l-4.586 4.586a2 2 0 01-2.828 0l-7.071-7.071A2 2 0 014 9.586V4z" fill-rule="evenodd" clip-rule="evenodd"></path></svg>' />

        <x-stat-card label="Total Mapel" value="{{ $totalMapel }}"
            icon='<svg class="w-6 h-6 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"></path></svg>' />
    </div>

    {{-- Chart Cards --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
        <x-chart-card title="Distribusi Nilai Siswa"
            icon='<svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>'>
            <canvas id="distribusiChart" style="max-height: 250px;"></canvas>
        </x-chart-card>

        <x-chart-card title="Kelengkapan Nilai"
            icon='<svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"></path></svg>'>
            <canvas id="pieChart" style="max-height: 250px;"></canvas>
        </x-chart-card>
    </div>
@endsection

@push('scripts')
    <script>
        // Distribusi Chart
        const distribusiCtx = document.getElementById('distribusiChart').getContext('2d');
        new Chart(distribusiCtx, {
            type: 'bar',
            data: {
                labels: ['A', 'B', 'C', 'D', 'E'],
                datasets: [{ label: 'Jumlah Siswa', data: @json(array_values($distribusi)), backgroundColor: '#1f2937', borderRadius: 4 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, grid: { drawBorder: false } }, x: { grid: { display: false } } }
            }
        });

        // Pie Chart
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Sudah di input', 'Belum di input'],
                datasets: [{ data: [{{ $totalNilaiTerisi }}, {{ max(0, $totalNilaiSlots - $totalNilaiTerisi) }}], backgroundColor: ['#4b5563', '#d1d5db'], borderWidth: 0 }]
            },
            options: {
                responsive: true, maintainAspectRatio: true,
                plugins: { legend: { position: 'bottom', labels: { font: { size: 12 } } } }
            }
        });
    </script>
@endpush