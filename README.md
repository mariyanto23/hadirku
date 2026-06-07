# HadirKu

HadirKu adalah aplikasi presensi siswa berbasis web untuk sekolah dasar. Aplikasi ini memakai pengenalan wajah di browser, sehingga proses pemindaian berjalan di perangkat guru/admin tanpa layanan AI eksternal.

Target penggunaan: admin sekolah, guru, dan siswa.

## Fitur Utama

- Presensi wajah untuk admin dan guru.
- Registrasi wajah siswa oleh admin, guru, atau siswa.
- Pengajuan izin/sakit oleh siswa.
- Persetujuan dan koreksi presensi oleh admin/guru.
- Rekap presensi admin, guru, dan siswa.
- Kalender akademik untuk libur nasional, libur semester, libur sekolah, kegiatan sekolah, dan hari lain.
- Pengaturan hari sekolah 5 hari atau 6 hari.
- Alpa otomatis berdasarkan jadwal kelas, hari sekolah, dan kalender akademik.
- Impor data siswa.
- Impor hari libur.
- Pengaturan logo dan favicon aplikasi.
- Tampilan responsif untuk desktop dan Android.

## Stack

- PHP 8.3+
- Laravel 13
- Livewire 4
- Blade
- Tailwind CSS
- Alpine.js
- Spatie Laravel Permission
- Maatwebsite Excel
- DomPDF
- face-api.js lokal di `public/vendor/face-api/face-api.min.js`

## Role

### Admin

- Dashboard
- Presensi
- Kelola Kelas
- Kelola Guru
- Kelola Siswa
- Registrasi Wajah
- Izin/Sakit
- Rekap
- Kalender Akademik
- Pengaturan

### Guru

- Beranda
- Presensi
- Registrasi Wajah
- Izin/Sakit
- Rekap

### Siswa

- Beranda
- Registrasi Wajah
- Izin/Sakit
- Rekap
- Ubah Profil

## Akun Awal

Seeder membuat akun admin awal:

```text
Nama pengguna: admin
Kata sandi: password
```

Setelah instalasi produksi, segera masuk sebagai admin dan ubah kata sandi.

## Menjalankan di Lokal

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
npm run build
```

Jalankan server:

```bash
php artisan serve
npm run dev
```

Atau gunakan Laragon seperti biasa dengan document root mengarah ke folder `public`.

## Perintah Penting

Migrasi dan seeder:

```bash
php artisan migrate --seed
```

Build aset frontend:

```bash
npm run build
```

Membuat storage link:

```bash
php artisan storage:link
```

Membersihkan dan membuat cache produksi:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Menjalankan test:

```bash
composer test
```

## Scheduler

Alpa otomatis dijalankan oleh Laravel scheduler melalui command:

```bash
php artisan schedule:run
```

Di production, cron harus aktif setiap menit. Contoh:

```bash
* * * * * cd /home/USERNAME/hadirku && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

Sesuaikan path project dan path PHP dengan hosting.

## Catatan Kamera

Fitur kamera/pengenalan wajah memerlukan:

- Browser modern.
- Izin kamera dari browser.
- HTTPS di production.
- Model face-api.js tersedia di folder `public/models`.

Pada perangkat Android, gunakan Chrome atau browser modern lain.

## Deployment cPanel

Panduan lengkap instalasi di cPanel tersedia di [INSTALL.md](INSTALL.md).

## Catatan Operasional Setelah Rilis

Sebelum dipakai sekolah:

- Ubah kata sandi admin awal.
- Atur logo dan favicon.
- Atur hari sekolah.
- Atur jam mulai presensi dan batas terlambat.
- Isi atau impor kalender akademik.
- Buat data kelas.
- Buat data guru.
- Impor data siswa.
- Registrasikan wajah siswa minimal 3 descriptor per siswa.
- Pastikan cron scheduler aktif.

## Batasan

- Pengenalan wajah berjalan di browser, sehingga performa dipengaruhi perangkat, kamera, pencahayaan, dan jumlah descriptor.
- Aplikasi ini tidak memakai layanan AI/cloud face recognition eksternal.
- Public registration tidak dipakai; akun guru dan siswa dibuat oleh admin.
