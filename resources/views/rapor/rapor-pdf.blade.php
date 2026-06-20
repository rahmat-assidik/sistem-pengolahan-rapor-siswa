<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Hasil Belajar (Rapor)</title>
    <style>
        body { font-family: 'Arial', sans-serif; font-size: 9px; line-height: 1.1; }
        .container { width: 100%; padding: 5px; }
        .header-title { text-align: center; font-size: 12px; font-weight: bold; margin-bottom: 8px; text-transform: uppercase; }
        
        .info-table { width: 100%; margin-bottom: 10px; border-collapse: collapse; }
        .info-table td { padding: 1px; }
        .label { font-weight: bold; width: 150px; }

        .nilai-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
        .nilai-table th, .nilai-table td { border: 1px solid #000; padding: 3px; text-align: center; }
        .nilai-table th { background: #e0e0e0; font-weight: bold; }
        .group-header { text-align: left; background: #f9f9f9 !important; font-weight: bold; }
        .text-left { text-align: left; }

        .catatan-section { border: 1px solid #000; padding: 5px; margin-bottom: 10px; }
        .signatures { width: 100%; margin-top: 15px; }
        .signature-box { width: 33%; float: left; text-align: center; }
        .signature-space { height: 60px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header-title">Laporan Hasil Belajar Peserta Didik</div>

        <table class="info-table">
            <tr><td class="label">Nama Peserta Didik</td><td>: {{ $siswa->nama_siswa }}</td><td class="label">Kelas</td><td>: {{ $kelasSiswa->kelas->nama_kelas ?? '-' }}</td></tr>
            <tr><td class="label">NIS</td><td>: {{ $siswa->nis }}</td><td class="label">Semester</td><td>: {{ $semester->semester == 1 ? 'Ganjil' : 'Genap' }}</td></tr>
            <tr><td colspan="2"></td><td class="label">Tahun Pelajaran</td><td>: {{ $semester->tahunAjaran->nama }}</td></tr>
        </table>

        @php
            // Re-fetch nilai with Mapel and group by Kelompok from DB
            $nilaiCollection = $kelasSiswa->nilai->map(function ($nilai) {
                return [
                    'nama_mapel' => $nilai->pengampu->mapel->nama_mapel ?? '-',
                    'kelompok' => $nilai->pengampu->mapel->kelompok ?? 'Lainnya',
                    'kkm' => $nilai->pengampu->kkm ?? 75,
                    'nilai_akhir' => $nilai->nilai_akhir,
                ];
            });
            
            $groupedNilai = $nilaiCollection->groupBy('kelompok');
            
            // Sort groups: Wajib (A), Peminatan (B), Muatan Lokal (C)
            $order = ['Wajib' => 1, 'Peminatan' => 2, 'Muatan Lokal' => 3];
            $groupedNilai = $groupedNilai->sortKeysUsing(function ($a, $b) use ($order) {
                return ($order[$a] ?? 99) <=> ($order[$b] ?? 99);
            });
            
            $totalNilai = $nilaiCollection->sum('nilai_akhir');
            $countNilai = $nilaiCollection->count();
            $rataRata = $countNilai > 0 ? $totalNilai / $countNilai : 0;
            
            function terbilang($n) {
                $n = (int)$n;
                $dasar = array(0 => '', 1 => 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan', 'Sepuluh', 'Sebelas');
                if ($n == 0) return '';
                if ($n < 12) return $dasar[$n];
                if ($n < 20) return terbilang($n - 10) . ' Belas';
                if ($n < 100) return terbilang($n / 10) . ' Puluh ' . terbilang($n % 10);
                if ($n == 100) return 'Seratus';
                return 'Angka tidak valid';
            }

            function getDeskripsi($nilai) {
                if ($nilai >= 90) return 'Sangat baik dalam penguasaan materi.';
                if ($nilai >= 80) return 'Baik dalam penguasaan materi.';
                if ($nilai >= 70) return 'Cukup dalam penguasaan materi, perlu latihan lagi.';
                return 'Perlu bimbingan lebih lanjut dalam memahami materi.';
            }
        @endphp

        <table class="nilai-table">
            <thead>
                <tr>
                    <th rowspan="2" style="width: 5%">No</th>
                    <th rowspan="2" style="width: 30%">Mata Pelajaran</th>
                    <th rowspan="2" style="width: 8%">KKM</th>
                    <th colspan="2" style="width: 20%">Nilai</th>
                    <th rowspan="2" style="width: 37%">Deskripsi</th>
                </tr>
                <tr>
                    <th>Angka</th>
                    <th>Huruf</th>
                </tr>
            </thead>
            <tbody>
                @foreach($groupedNilai as $kelompok => $items)
                <tr><td colspan="6" class="group-header">Kelompok {{ $kelompok }}</td></tr>
                @foreach($items as $index => $n)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td class="text-left">{{ $n['nama_mapel'] }}</td>
                    <td>{{ $n['kkm'] }}</td>
                    <td>{{ number_format($n['nilai_akhir'], 0) }}</td>
                    <td>{{ terbilang(round($n['nilai_akhir'])) }}</td>
                    <td class="text-left">{{ getDeskripsi($n['nilai_akhir']) }}</td>
                </tr>
                @endforeach
                @endforeach
            </tbody>
            <tfoot>
                <tr style="font-weight: bold; background: #eee;">
                    <td colspan="3" style="text-align: right;">Jumlah</td>
                    <td>{{ number_format($totalNilai, 0) }}</td>
                    <td>-</td>
                    <td></td>
                </tr>
                <tr style="font-weight: bold; background: #eee;">
                    <td colspan="3" style="text-align: right;">Rata-rata</td>
                    <td>{{ number_format($rataRata, 1) }}</td>
                    <td>{{ terbilang($rataRata) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="catatan-section">
            <strong>Catatan Wali Kelas:</strong><br>
            {{ $kelasSiswa->catatan_wali ?? '-' }}
        </div>

        <div class="signatures">
            <div class="signature-box">
                <p>Orang Tua/Wali</p>
                <div class="signature-space"></div>
                <p>( . . . . . . . . . . . . . )</p>
            </div>
            <div class="signature-box">
                <p>Kepala Sekolah</p>
                <div class="signature-space">
                    @php $ksTtd = \App\Models\TandaTangan::where('key', 'kepala_sekolah_ttd_path')->first(); @endphp
                    @if($ksTtd && file_exists(storage_path('app/public/' . $ksTtd->value)))
                        <img src="{{ storage_path('app/public/' . $ksTtd->value) }}" style="height: 70px; width: auto; max-width: 150px; margin-bottom: 10px;">
                    @endif
                </div>
                <p>
                    @php $ksNama = \App\Models\TandaTangan::where('key', 'kepala_sekolah_nama')->value('value'); @endphp
                    {{ $ksNama ?? '____________________' }}
                </p>
            </div>
            <div class="signature-box">
                <p>Wali Kelas</p>
                <div class="signature-space">
                    @if($waliKelas?->guru->signature_path && file_exists(storage_path('app/public/' . $waliKelas->guru->signature_path)))
                        <img src="{{ storage_path('app/public/' . $waliKelas->guru->signature_path) }}" style="height: 70px; width: auto; max-width: 150px; margin-bottom: 10px;">
                    @endif
                </div>
                <p>{{ $waliKelas?->guru->nama_guru ?? '____________________' }}</p>
            </div>
            <div class="signature-box">
                <p>Wali Kelas</p>
                <div class="signature-space"></div>
                <p>{{ $waliKelas?->guru->nama_guru ?? '____________________' }}</p>
            </div>
        </div>
    </div>
</body>
</html>
