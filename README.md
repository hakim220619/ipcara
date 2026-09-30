# IPCARA

**Infection Prevention and Control Adaptive Recommendation App** — aplikasi pembelajaran adaptif berbasis web untuk mahasiswa keperawatan (proyek tesis).

Stack: Laravel 13 · Inertia.js · Vue 3 · Tailwind CSS v4 · MySQL.

## Alur belajar

login → pre-test → analisis kemampuan per topik → rekomendasi materi (dasar / menengah / lanjut) → belajar (teks, video, kasus) → latihan → remedial bila perlu → post-test → dashboard perkembangan.

## Menjalankan secara lokal

1. Buat database MySQL:
   ```sql
   CREATE DATABASE ipcara CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```
2. Isi kredensial di `.env` (`DB_USERNAME`, `DB_PASSWORD`).
3. Install dependensi dan jalankan migrasi + data contoh:
   ```bash
   composer install
   npm install
   php artisan migrate --seed
   ```
4. Jalankan server pengembangan:
   ```bash
   composer run dev
   ```
   Dengan Laravel Herd, aplikasi juga bisa dibuka di `http://ipcara.test`.

## Akun contoh (dari seeder)

| Peran | Email | Password |
|---|---|---|
| Admin | admin@ipcara.test | password |
| Dosen | dosen@ipcara.test | password |
| Mahasiswa | mahasiswa@ipcara.test | password |

## Struktur database

| Tabel | Fungsi |
|---|---|
| `users` | Akun dengan `role` (mahasiswa/dosen/admin), NIM, prodi, angkatan |
| `topics` | Topik PPI (hand hygiene, APD, sterilisasi, dll.) |
| `materials` | Materi per topik dengan `type` (teks/video/kasus) dan `level` (dasar/menengah/lanjut) |
| `questions`, `question_options` | Bank soal dan pilihan jawaban |
| `tests`, `test_questions` | Instrumen tes: pre_test, latihan, remedial, post_test |
| `test_attempts`, `attempt_answers` | Percobaan tes dan jawaban mahasiswa |
| `ability_assessments` | Hasil analisis kemampuan per topik (rendah/sedang/tinggi) |
| `recommendations` | Rekomendasi materi yang dihasilkan sistem |
| `material_progress` | Progres belajar per materi |
| `remedials` | Penugasan remedial saat skor di bawah KKM |
| `learning_activities` | Log aktivitas untuk analisis penelitian |
