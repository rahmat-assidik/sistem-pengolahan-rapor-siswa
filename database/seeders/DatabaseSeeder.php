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
use App\Models\Nilai;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 0. MEMBERSIHKAN DATABASE
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        
        $tables = [
            'user', 'guru', 'siswa', 'kelas', 'mapel', 
            'tahun_ajaran', 'semester', 'riwayat_kelas_siswa', 
            'pengampu', 'wali_kelas', 'nilai'
        ];
        
        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }
        
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        DB::beginTransaction();
        try {
            // 1. TAHUN AJARAN & SEMESTER
            $taAktif = TahunAjaran::create([
                'nama' => '2024/2025',
                'tanggal_mulai' => '2024-07-15',
                'tanggal_selesai' => '2025-06-20',
                'is_aktif' => true
            ]);

            $smtAktif = Semester::create([
                'tahun_ajaran_id' => $taAktif->id,
                'semester' => 'Ganjil',
                'is_aktif' => true
            ]);

            Semester::create([
                'tahun_ajaran_id' => $taAktif->id,
                'semester' => 'Genap',
                'is_aktif' => false
            ]);

            // 2. GURU & USER
            $faker = \Faker\Factory::create('id_ID');
            $gurus = [];
            
            // List of Indonesian Names for Gurus
            $namaDepan = ['Budi', 'Siti', 'Agus', 'Dewi', 'Ahmad', 'Rina', 'Joko', 'Ani', 'Eko', 'Putri', 'Fajar', 'Dian', 'Andi', 'Sari', 'Hendra', 'Maya', 'Rizky', 'Nur', 'Wawan', 'Lestari'];
            $namaBelakang = ['Santoso', 'Wijaya', 'Pratama', 'Hidayat', 'Saputra', 'Wulandari', 'Kurniawan', 'Ramadhani', 'Setiawan', 'Permata', 'Nugroho', 'Pratiwi', 'Haryanto', 'Utami', 'Purnomo', 'Indah', 'Pradana', 'Amalia', 'Sulistyo', 'Dewi'];

            for ($i = 0; $i < 20; $i++) {
                $nip = "100" . ($i + 1);
                $gender = $i % 2 == 0 ? 'Laki-laki' : 'Perempuan';
                $nama = $namaDepan[$i] . ' ' . $namaBelakang[$i] . ', S.Pd.';
                $g = Guru::create([
                    'nip' => $nip,
                    'nama_guru' => $nama,
                    'email' => "guru" . ($i + 1) . "@sekolah.sch.id",
                    'jenis_kelamin' => $gender,
                    'no_hp' => '0812' . rand(11111111, 99999999)
                ]);
                $gurus[] = $g;

                User::create([
                    'username' => $g->nip,
                    'email' => $g->email,
                    'password' => Hash::make($g->nip),
                    'role' => 'guru',
                    'guru_id' => $g->nip
                ]);
            }

            User::create([
                'username' => 'admin',
                'email' => 'admin@sekolah.sch.id',
                'password' => Hash::make('12345678'),
                'role' => 'admin'
            ]);

            // 3. KELAS
            $kelasPool = [];
            $tingkat = ['X', 'XI', 'XII'];
            $jurusan = ['MIPA', 'IPS'];
            
            foreach ($tingkat as $t) {
                foreach ($jurusan as $j) {
                    for ($n = 1; $n <= 2; $n++) {
                        $kode = "$t-$j-$n";
                        $kelasPool[] = Kelas::create([
                            'kode_kelas' => $kode,
                            'nama_kelas' => "$t $j $n",
                            'tingkat' => $t
                        ]);
                    }
                }
            }

            // 4. MAPEL
            $mapelData = [
                // Wajib
                ['MTK', 'Matematika', 'Wajib'],
                ['BIN', 'Bahasa Indonesia', 'Wajib'],
                ['BIG', 'Bahasa Inggris', 'Wajib'],
                ['PPK', 'Pendidikan Pancasila', 'Wajib'],
                ['PAI', 'Pendidikan Agama Islam', 'Wajib'],
                // Peminatan
                ['BIO', 'Biologi', 'Peminatan'],
                ['FIS', 'Fisika', 'Peminatan'],
                ['KIM', 'Kimia', 'Peminatan'],
                ['EKO', 'Ekonomi', 'Peminatan'],
                ['GEO', 'Geografi', 'Peminatan'],
                ['SOS', 'Sosiologi', 'Peminatan'],
                // Muatan Lokal
                ['MUL1', 'Bahasa Daerah', 'Muatan Lokal'],
                ['MUL2', 'TIK', 'Muatan Lokal'],
            ];
            $mapels = [];
            foreach ($mapelData as $md) {
                $mapels[] = Mapel::create(['kode_mapel' => $md[0], 'nama_mapel' => $md[1], 'kelompok' => $md[2]]);
            }

            // 5. DATA SISWA (30 data)
            $this->command->info('⏳ Menghasilkan 30 data siswa...');
            $siswaList = Siswa::factory()->count(30)->create();

            // 6. PENEMPATAN KELAS & NILAI SAMPEL
            $this->command->info('⏳ Menempatkan siswa ke kelas...');
            
            foreach ($siswaList as $index => $siswa) {
                // Semua siswa dimasukkan kelas
                $kelasRandom = $kelasPool[array_rand($kelasPool)];
                $riwayat = RiwayatKelasSiswa::create([
                    'nis' => $siswa->nis,
                    'kode_kelas' => $kelasRandom->kode_kelas,
                    'semester_id' => $smtAktif->id,
                ]);

                // Tambahkan nilai acak untuk beberapa mapel
                foreach (array_rand($mapels, 3) as $mIndex) {
                    $mapel = $mapels[$mIndex];
                    $guru = $gurus[array_rand($gurus)];
                    
                    // Cek/buat pengampu
                    $pengampu = Pengampu::firstOrCreate(
                        [
                            'guru_id' => $guru->nip,
                            'mapel_id' => $mapel->kode_mapel,
                            'kelas_id' => $kelasRandom->id,
                            'semester_id' => $smtAktif->id,
                        ],
                        ['kkm' => 75, 'status' => 'Aktif']
                    );

                    $tugas = rand(70, 95);
                    $ulangan = rand(65, 90);
                    $uts = rand(60, 85);
                    $uas = rand(65, 80);
                    
                    Nilai::create([
                        'kelas_siswa_id' => $riwayat->id,
                        'pengampu_id' => $pengampu->id,
                        'tugas' => $tugas,
                        'ulangan' => $ulangan,
                        'uts' => $uts,
                        'uas' => $uas,
                        'nilai_akhir' => ($tugas + $ulangan + $uts + $uas) / 4
                    ]);
                }
            }

            DB::commit();
            $this->command->info('✅ Database Berhasil Diisi dengan Data Banyak!');

        } catch (\Exception $e) {
            DB::rollBack();
            $this->command->error('❌ Gagal: ' . $e->getMessage());
        }
    }
}
