<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Hasil Belajar (Rapor)</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Calibri', 'Arial', sans-serif;
            line-height: 1.4;
            color: #333;
            background: white;
        }

        .container {
            width: 190mm;
            margin: 0 auto;
            padding: 5mm 5mm;
            background: white;
            font-size: 11px;
        }

        /* Title */
        .title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 8px;
            color: #000;
        }

        /* Info Section */
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 10px;
            font-size: 9px;
            border-collapse: collapse;
        }

        .info-grid > div {
            display: table-cell;
            width: 50%;
            padding: 0 5px;
            vertical-align: top;
        }

        .info-row {
            display: flex;
            margin-bottom: 3px;
        }

        .info-label {
            width: 90px;
            font-weight: bold;
            color: #000;
            flex-shrink: 0;
        }

        .info-value {
            flex: 1;
            padding-left: 5px;
            font-size: 9px;
        }

        .info-colon {
            width: 10px;
        }

        /* Table */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 8.5px;
            margin-bottom: 10px;
        }

        table thead {
            background: #ccc;
            font-weight: bold;
        }

        table th {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            color: #000;
            font-size: 8px;
        }

        table td {
            border: 1px solid #000;
            padding: 4px 2px;
            text-align: center;
            height: 20px;
        }

        table td.text-left {
            text-align: left;
        }

        table tbody tr:nth-child(odd) {
            background: #fff;
        }

        table tbody tr:nth-child(even) {
            background: #fff;
        }

        .table-title {
            font-weight: bold;
            font-size: 9px;
            margin-top: 8px;
            margin-bottom: 5px;
        }

        /* Ketidakhadiran */
        .absence-section {
            display: table;
            width: 100%;
            margin-bottom: 12px;
            font-size: 9px;
        }

        .absence-section > div {
            display: table-cell;
            width: 50%;
            padding: 0 5px;
        }

        .absence-table {
            width: 100%;
            border-collapse: collapse;
        }

        .absence-table td {
            border: 1px solid #000;
            padding: 5px 3px;
            height: 18px;
            font-size: 8.5px;
        }

        .absence-table td:first-child {
            font-weight: bold;
            width: 55%;
            font-size: 8.5px;
        }

        .absence-table td:last-child {
            text-align: center;
            width: 45%;
            font-size: 8.5px;
        }

        .place-date {
            text-align: right;
            font-size: 8px;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        /* Signatures */
        .signatures {
            display: table;
            width: 100%;
            font-size: 8px;
            margin-top: 15px;
            border-collapse: collapse;
        }

        .signature-box {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 3px;
        }

        .signature-label {
            font-weight: bold;
            margin-bottom: 25px;
            font-size: 8px;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            height: 25px;
            margin-bottom: 3px;
        }

        .signature-name {
            font-size: 8px;
        }

        .center-sig {
            margin-top: 10px;
        }

        /* Print */
        @media print {
            body {
                margin: 0;
                padding: 0;
            }
            .container {
                width: 190mm;
                padding: 3mm 3mm;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Title -->
        <div class="title">Format Laporan Hasil Belajar (Rapor)</div>

        <!-- Info Section -->
        <div class="info-grid">
            <div>
                <div class="info-row">
                    <span class="info-label">Nama Peserta Didik</span>
                    <span class="info-colon">:</span>
                    <span class="info-value"><strong>{{ $siswa->nama_siswa }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">NISN</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">{{ $siswa->nis }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Sekolah</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">SMA</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Alamat</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">-</span>
                </div>
            </div>
            <div>
                <div class="info-row">
                    <span class="info-label">Kelas</span>
                    <span class="info-colon">:</span>
                    <span class="info-value"><strong>{{ $kelasSiswa->kelas->nama_kelas ?? '-' }}</strong></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Fase</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">-</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Semester</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">{{ $semester->semester == 1 ? 'Ganjil' : 'Genap' }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Tahun Pelajaran</span>
                    <span class="info-colon">:</span>
                    <span class="info-value">{{ $semester->tahunAjaran->nama }}</span>
                </div>
            </div>
        </div>

        <!-- Table Mata Pelajaran -->
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No.</th>
                    <th style="width: 50%">Mata Pelajaran</th>
                    <th style="width: 20%">Nilai Akhir</th>
                    <th style="width: 25%">Capaian Kompetensi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nilaiDetails as $index => $nilai)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td class="text-left">{{ $nilai['nama_mapel'] }}</td>
                        <td>
                            @if($nilai['nilai_akhir'] !== null)
                                <strong>{{ number_format($nilai['nilai_akhir'], 0) }}</strong>
                            @endif
                        </td>
                        <td class="text-left">
                            @if($nilai['predikat'] == 'A')
                                Sangat Baik
                            @elseif($nilai['predikat'] == 'B')
                                Baik
                            @elseif($nilai['predikat'] == 'C')
                                Cukup
                            @elseif($nilai['predikat'] == 'D')
                                Kurang
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: #999;">Tidak ada data nilai</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Ekstrakurikuler Section -->
        <div class="table-title">Ekstrakurikuler</div>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%">No.</th>
                    <th style="width: 50%">Ekstrakurikuler</th>
                    <th style="width: 45%">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>1</td>
                    <td class="text-left">-</td>
                    <td class="text-left">-</td>
                </tr>
                <tr>
                    <td>2</td>
                    <td class="text-left">-</td>
                    <td class="text-left">-</td>
                </tr>
                <tr>
                    <td>dst.</td>
                    <td class="text-left">-</td>
                    <td class="text-left">-</td>
                </tr>
            </tbody>
        </table>

        <!-- Ketidakhadiran Section -->
        <div class="absence-section">
            <div>
                <div class="table-title">Ketidakhadiran</div>
                <table class="absence-table">
                    <tr>
                        <td>Sakit</td>
                        <td>. . . hari</td>
                    </tr>
                    <tr>
                        <td>Izin</td>
                        <td>. . . hari</td>
                    </tr>
                    <tr>
                        <td>Tanpa Keterangan</td>
                        <td>. . . hari</td>
                    </tr>
                </table>
            </div>
            <div class="place-date">
                <div style="margin-bottom: 10px;">Tempat, Tanggal rapor</div>
                <div>_________________________</div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-label">TTD Orang Tua Peserta Didik</div>
                <div class="signature-line"></div>
                <div class="signature-name">_________________________</div>
            </div>
            <div class="signature-box center-sig">
                <div class="signature-label">TTD Kepala Sekolah</div>
                <div class="signature-line"></div>
                <div class="signature-name">_________________________</div>
            </div>
            <div class="signature-box">
                <div class="signature-label">TTD Wali Kelas</div>
                <div class="signature-line"></div>
                <div class="signature-name">{{ $waliKelas?->guru->nama_guru ?? '_________________________' }}</div>
            </div>
        </div>
    </div>
</body>
</html>
