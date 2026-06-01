<?php

/**
 * DOKUMENTASI: Email Management System - User & Guru
 * 
 * STRUKTUR DATABASE:
 * ==================
 * 
 * Tabel GURU:
 * - nip (PK, varchar 20)
 * - nama_guru
 * - email (unique, varchar 255) → Format: guru_{nip}@sekolah.local
 * - jenis_kelamin
 * - no_hp (nullable)
 * - status (Aktif/Tidak Aktif)
 * - created_at, updated_at
 * 
 * Tabel USER:
 * - username (PK, varchar 50)
 * - nama
 * - email (unique, varchar 255) → Sama dengan guru.email untuk user bertipe guru
 * - password
 * - role (admin/guru)
 * - guru_id (FK → guru.nip) - Hanya diisi untuk user dengan role 'guru'
 * - created_at, updated_at
 * 
 * RELASI:
 * =======
 * 
 * User → Guru (via guru_id):
 *   $user->guru() // BelongsTo relationship
 *   
 * Contoh:
 *   $user = User::where('username', '1001')->first();
 *   $guru = $user->guru; // Guru dengan nip '1001'
 *   echo $guru->email; // guru_1001@sekolah.local
 * 
 * PENTING:
 * ========
 * 
 * 1. TIDAK ada foreign key pada user.email ke guru.email
 *    Alasan: guru_id sudah menghubungkan relasi. Foreign key pada email
 *    akan membuat masalah ketika email guru perlu diubah.
 *    
 * 2. user.email untuk guru HARUS SAMA dengan guru.email
 *    Ini untuk consistency dan user login menggunakan email guru.
 *    
 * 3. Email untuk admin user tidak terkait dengan guru
 *    
 * PENGGUNAAN EMAIL:
 * =================
 * 
 * Saat membuat User untuk Guru:
 *   1. Pastikan Guru sudah ada dengan email yang diisi
 *   2. Copy email dari guru ke user:
 *   
 *   $guru = Guru::findOrFail($nip);
 *   $user = User::create([
 *       'username' => $guru->nip,
 *       'nama' => $guru->nama_guru,
 *       'email' => $guru->email,  // Gunakan email dari guru
 *       'password' => Hash::make('password'),
 *       'role' => 'guru',
 *       'guru_id' => $guru->nip
 *   ]);
 *   
 * Saat update email guru:
 *   1. Update guru.email
 *   2. HARUS juga update user.email yang terkait:
 *   
 *   $guru = Guru::findOrFail('1001');
 *   $guru->email = 'guru_1001@newemail.com';
 *   $guru->save();
 *   
 *   if ($guru->user) {
 *       $guru->user->email = $guru->email;
 *       $guru->user->save();
 *   }
 *   
 * CONTOH DATA:
 * ============
 * 
 * GURU:
 * nip  | nama_guru           | email
 * 1001 | Drs. Ahmad Fauzi    | guru_1001@sekolah.local
 * 1002 | Sri Wahyuni, S.Pd.  | guru_1002@sekolah.local
 * 1003 | Bambang S., M.Pd.   | guru_1003@sekolah.local
 * 1004 | Dewi Kartika, S.Pd. | guru_1004@sekolah.local
 * 
 * USER (untuk guru):
 * username | nama_guru           | email                    | role | guru_id
 * 1001     | Drs. Ahmad Fauzi    | guru_1001@sekolah.local  | guru | 1001
 * 1002     | Sri Wahyuni, S.Pd.  | guru_1002@sekolah.local  | guru | 1002
 * 1003     | Bambang S., M.Pd.   | guru_1003@sekolah.local  | guru | 1003
 * 1004     | Dewi Kartika, S.Pd. | guru_1004@sekolah.local  | guru | 1004
 * admin    | Administrator       | admin@sekolah.sch.id     | admin| NULL
 */
