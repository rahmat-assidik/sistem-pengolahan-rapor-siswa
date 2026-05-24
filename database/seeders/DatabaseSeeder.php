<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\TahunAjaran;
use App\Models\Semester;
use App\Models\RiwayatKelasSiswa;
use App\Models\Pengampu;
use App\Models\WaliKelas;
use App\Models\Nilai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. MEMBERSIHKAN DATABASE (CLEAN STATE)
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = [
            'user', 'guru', 'siswa', 'kelas', 'mapel', 
            'tahun_ajaran', 'semester', 'riwayat_kelas_siswa', 
            'pengampu', 'wali_kelas', 'nilai', 'komponen_nilai'
        ];
        
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::beginTransaction();
        try {
            // 1. TAHUN AJARAN & SEMESTER
            $years = [
                '2022/2023' => false,
                '2023/2024' => false,
                '2024/2025' => true,
            ];

            $smtList = [];
            foreach ($years as $name => $active) {
                $ta = TahunAjaran::create([
                    'nama' => $name,
                    'tanggal_mulai' => explode('/', $name)[0] . '-07-15',
                    'tanggal_selesai' => explode('/', $name)[1] . '-06-20',
                    'is_aktif' => $active
                ]);

                foreach (['Ganjil', 'Genap'] as $sName) {
                    $smtList[$name][$sName] = Semester::create([
                        'tahun_ajaran_id' => $ta->id,
                        'semester' => $sName,
                        'is_aktif' => ($active && $sName == 'Ganjil')
                    ]);
                }
            }

            // 2. GURU & USER
            $guruAhmad = Guru::create(['nip' => '1001', 'nama_guru' => 'Drs. Ahmad Fauzi', 'jenis_kelamin' => 'Laki-laki', 'no_hp' => '081200001001']);
            $guruSri = Guru::create(['nip' => '1002', 'nama_guru' => 'Sri Wahyuni, S.Pd.', 'jenis_kelamin' => 'Perempuan', 'no_hp' => '081200001002']);
            $guruBambang = Guru::create(['nip' => '1003', 'nama_guru' => 'Bambang S., M.Pd.', 'jenis_kelamin' => 'Laki-laki', 'no_hp' => '081200001003']);
            $guruDewi = Guru::create(['nip' => '1004', 'nama_guru' => 'Dewi Kartika, S.Pd.', 'jenis_kelamin' => 'Perempuan', 'no_hp' => '081200001004']);

            $allGurus = [$guruAhmad, $guruSri, $guruBambang, $guruDewi];
            foreach ($allGurus as $g) {
                User::create([
                    'username' => $g->nip,
                    'nama' => $g->nama_guru,
                    'email' => strtolower(explode(' ', $g->nama_guru)[0]) . '@sekolah.sch.id',
                    'password' => Hash::make($g->nip),
                    'role' => 'guru',
                    'guru_id' => $g->nip
                ]);
            }
            User::create([
                'username' => 'admin',
                'nama' => 'Administrator',
                'email' => 'admin@sekolah.sch.id',
                'password' => Hash::make('12345678'),
                'role' => 'admin'
            ]);

            // 3. KELAS
            $k10 = Kelas::create(['kode_kelas' => 'X-MIPA-1', 'nama_kelas' => 'X MIPA 1', 'tingkat' => 'X']);
            $k11 = Kelas::create(['kode_kelas' => 'XI-MIPA-1', 'nama_kelas' => 'XI MIPA 1', 'tingkat' => 'XI']);
            $k12 = Kelas::create(['kode_kelas' => 'XII-MIPA-1', 'nama_kelas' => 'XII MIPA 1', 'tingkat' => 'XII']);

            // 4. WALI KELAS
            foreach ($smtList as $yearSmt) {
                foreach ($yearSmt as $smt) {
                    WaliKelas::create(['kelas_id' => $k10->id, 'semester_id' => $smt->id, 'guru_id' => $guruBambang->id]);
                    WaliKelas::create(['kelas_id' => $k11->id, 'semester_id' => $smt->id, 'guru_id' => $guruSri->id]);
                    WaliKelas::create(['kelas_id' => $k12->id, 'semester_id' => $smt->id, 'guru_id' => $guruAhmad->id]);
                }
            }

            // 5. MAPEL
            $mtk = Mapel::create(['kode_mapel' => 'MTK', 'nama_mapel' => 'Matematika', 'kelompok' => 'Wajib']);
            $bin = Mapel::create(['kode_mapel' => 'BIN', 'nama_mapel' => 'Bahasa Indonesia', 'kelompok' => 'Wajib']);

            // 6. DATA SISWA
            $studentNames = [
                2022 => ['Budi Santoso', 'Citra Lestari', 'Dedi Kurniawan'],
                2023 => ['Eka Saputra', 'Fani Rahmawati', 'Gani Ramadhan'],
                2024 => ['Hadi Wijaya', 'Indah Permata', 'Joko Susilo']
            ];

            $students = [];
            foreach ($studentNames as $year => $names) {
                foreach ($names as $i => $name) {
                    $nis = $year . "00" . ($i + 1);
                    $students[$year][] = Siswa::create([
                        'nis' => $nis,
                        'angkatan' => $year,
                        'nama_siswa' => $name, 
                        'jenis_kelamin' => ($name == 'Citra Lestari' || $name == 'Fani Rahmawati' || $name == 'Indah Permata') ? 'Perempuan' : 'Laki-laki', 
                        'status' => 'Aktif'
                    ]);
                }
            }

            // 7. PENEMPATAN KELAS
            $allKelasSiswa = [];
            // 2022/2023
            foreach ($students[2022] as $s) {
                $allKelasSiswa[] = RiwayatKelasSiswa::create(['nis' => $s->nis, 'semester_id' => $smtList['2022/2023']['Ganjil']->id, 'kode_kelas' => $k10->kode_kelas]);
                $allKelasSiswa[] = RiwayatKelasSiswa::create(['nis' => $s->nis, 'semester_id' => $smtList['2022/2023']['Genap']->id, 'kode_kelas' => $k10->kode_kelas]);
            }
            // 2023/2024
            foreach ($students[2022] as $s) {
                $allKelasSiswa[] = RiwayatKelasSiswa::create(['nis' => $s->nis, 'semester_id' => $smtList['2023/2024']['Ganjil']->id, 'kode_kelas' => $k11->kode_kelas]);
                $allKelasSiswa[] = RiwayatKelasSiswa::create(['nis' => $s->nis, 'semester_id' => $smtList['2023/2024']['Genap']->id, 'kode_kelas' => $k11->kode_kelas]);
            }
            foreach ($students[2023] as $s) {
                $allKelasSiswa[] = RiwayatKelasSiswa::create(['nis' => $s->nis, 'semester_id' => $smtList['2023/2024']['Ganjil']->id, 'kode_kelas' => $k10->kode_kelas]);
                $allKelasSiswa[] = RiwayatKelasSiswa::create(['nis' => $s->nis, 'semester_id' => $smtList['2023/2024']['Genap']->id, 'kode_kelas' => $k10->kode_kelas]);
            }
            // 2024/2025 (Semester Aktif)
            $smtAktif = $smtList['2024/2025']['Ganjil'];
            $activeKelasSiswa = [];
            foreach ($students[2022] as $s) {
                $activeKelasSiswa[] = RiwayatKelasSiswa::create(['nis' => $s->nis, 'semester_id' => $smtAktif->id, 'kode_kelas' => $k12->kode_kelas]);
            }
            foreach ($students[2023] as $s) {
                $activeKelasSiswa[] = RiwayatKelasSiswa::create(['nis' => $s->nis, 'semester_id' => $smtAktif->id, 'kode_kelas' => $k11->kode_kelas]);
            }
            foreach ($students[2024] as $s) {
                $activeKelasSiswa[] = RiwayatKelasSiswa::create(['nis' => $s->nis, 'semester_id' => $smtAktif->id, 'kode_kelas' => $k10->kode_kelas]);
            }

            // 8. PENGAMPU
            $pengampus = [];
            $pengampus[] = Pengampu::create(['guru_id' => $guruAhmad->id, 'mapel_id' => $mtk->id, 'kelas_id' => $k12->id, 'semester_id' => $smtAktif->id, 'kkm' => 75, 'status' => 'Aktif']);
            $pengampus[] = Pengampu::create(['guru_id' => $guruSri->id, 'mapel_id' => $bin->id, 'kelas_id' => $k11->id, 'semester_id' => $smtAktif->id, 'kkm' => 75, 'status' => 'Aktif']);
            $pengampus[] = Pengampu::create(['guru_id' => $guruBambang->id, 'mapel_id' => $bin->id, 'kelas_id' => $k10->id, 'semester_id' => $smtAktif->id, 'kkm' => 75, 'status' => 'Aktif']);
            $pengampus[] = Pengampu::create(['guru_id' => $guruDewi->id, 'mapel_id' => $mtk->id, 'kelas_id' => $k10->id, 'semester_id' => $smtAktif->id, 'kkm' => 75, 'status' => 'Aktif']);
            $pengampus[] = Pengampu::create(['guru_id' => $guruDewi->id, 'mapel_id' => $mtk->id, 'kelas_id' => $k11->id, 'semester_id' => $smtAktif->id, 'kkm' => 75, 'status' => 'Aktif']);

            // 9. ISI KOMPONEN & NILAI SAMPEL
            foreach ($pengampus as $p) {
                // Buat Komponen Nilai Dinamis (Hanya Tugas & UH)
                $k1 = \App\Models\KomponenNilai::create(['pengampu_id' => $p->id, 'nama_komponen' => 'Tugas Mandiri', 'tipe' => 'p_tugas']);
                $k2 = \App\Models\KomponenNilai::create(['pengampu_id' => $p->id, 'nama_komponen' => 'Ulangan Harian', 'tipe' => 'p_uh']);

                // Cari semua siswa yang ada di kelas pengampu ini
                $siswaDiKelas = array_filter($activeKelasSiswa, function($ks) use ($p) {
                    return $ks->kode_kelas == $p->kelas->kode_kelas;
                });

                foreach ($siswaDiKelas as $ks) {
                    // 1. Isi Nilai Komponen Dinamis (Tugas & UH)
                    foreach ([$k1, $k2] as $komp) {
                        Nilai::create([
                            'kelas_siswa_id' => $ks->id,
                            'pengampu_id' => $p->id,
                            'komponen_nilai_id' => $komp->id,
                            'jenis_nilai' => 'dynamic', // Kode standar untuk komponen dinamis
                            'skor' => rand(75, 98)
                        ]);
                    }

                    // 2. Isi Nilai Statis (UTS & UAS) - Langsung via jenis_nilai
                    Nilai::create(['kelas_siswa_id' => $ks->id, 'pengampu_id' => $p->id, 'jenis_nilai' => 'p_uts', 'skor' => rand(75, 95)]);
                    Nilai::create(['kelas_siswa_id' => $ks->id, 'pengampu_id' => $p->id, 'jenis_nilai' => 'p_uas', 'skor' => rand(70, 90)]);

                    // 3. Isi Nilai Keterampilan
                    Nilai::create(['kelas_siswa_id' => $ks->id, 'pengampu_id' => $p->id, 'jenis_nilai' => 'k_praktik', 'skor' => rand(78, 95)]);

                    // 4. Isi Nilai Sikap
                    Nilai::create(['kelas_siswa_id' => $ks->id, 'pengampu_id' => $p->id, 'jenis_nilai' => 's_spiritual', 'skor' => rand(3, 4)]);
                    Nilai::create(['kelas_siswa_id' => $ks->id, 'pengampu_id' => $p->id, 'jenis_nilai' => 's_sosial', 'skor' => rand(3, 4)]);
                }
            }

            DB::commit();
            $this->command->info('✅ Seeder siap! Data Siswa, Guru, Kelas, dan Nilai Sampel telah diisi.');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Gagal: ' . $e->getMessage() . ' line ' . $e->getLine());
        }
    }
}
