# Panduan Instalasi HadirKu di cPanel

Dokumen ini berisi langkah instalasi HadirKu di shared hosting/cPanel. Urutan bisa sedikit berbeda tergantung penyedia hosting, tetapi prinsipnya sama.

## 1. Kebutuhan Hosting

Pastikan hosting mendukung:

- PHP 8.3 atau lebih baru.
- Composer.
- MySQL/MariaDB.
- Ekstensi PHP umum Laravel: `openssl`, `pdo`, `pdo_mysql`, `mbstring`, `tokenizer`, `xml`, `ctype`, `json`, `fileinfo`, `gd`, `zip`.
- Cron Jobs.
- HTTPS/SSL aktif.
- Akses Terminal/SSH cPanel sangat disarankan.

Jika hosting tidak menyediakan Terminal/SSH, instalasi masih bisa dilakukan, tetapi lebih merepotkan karena dependency Composer dan build frontend perlu disiapkan dari lokal.

## 2. Struktur Folder yang Disarankan

Struktur paling aman:

```text
/home/USERNAME/hadirku
/home/USERNAME/public_html
```

Isi project Laravel berada di:

```text
/home/USERNAME/hadirku
```

Document root domain/subdomain diarahkan ke:

```text
/home/USERNAME/hadirku/public
```

Jika cPanel mendukung pengaturan document root saat membuat subdomain/domain, pilih folder `hadirku/public`.

## 3. Upload Project

Upload semua file project ke:

```text
/home/USERNAME/hadirku
```

Folder yang wajib ikut:

```text
app
bootstrap
config
database
public
resources
routes
storage
vendor
```

Jika `vendor` belum ada, nanti jalankan `composer install` di server.

Folder/file yang tidak wajib diupload ke production:

```text
node_modules
.git
tests
```

## 4. Install Dependency

Masuk ke Terminal cPanel:

```bash
cd /home/USERNAME/hadirku
composer install --no-dev --optimize-autoloader
```

Jika hosting tidak mengizinkan Composer di server, jalankan di komputer lokal:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

Lalu upload folder `vendor` dan folder `public/build` ke server.

## 5. Build Frontend

Jika Node.js tersedia di hosting:

```bash
npm install
npm run build
```

Jika Node.js tidak tersedia, jalankan build di lokal lalu upload folder:

```text
public/build
```

## 6. Buat Database

Di cPanel:

1. Buka `MySQL Databases`.
2. Buat database, misalnya `username_hadirku`.
3. Buat user database, misalnya `username_hadirku_user`.
4. Beri hak akses user ke database dengan `All Privileges`.
5. Catat nama database, username, dan password.

## 7. Buat File `.env`

Salin `.env.example` menjadi `.env`:

```bash
cp .env.example .env
```

Contoh konfigurasi production:

```env
APP_NAME=HadirKu
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://domain-sekolah.sch.id

APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=username_hadirku
DB_USERNAME=username_hadirku_user
DB_PASSWORD=password_database

SESSION_DRIVER=database
QUEUE_CONNECTION=database
CACHE_STORE=database
FILESYSTEM_DISK=public

MAIL_MAILER=log
```

Lalu buat app key:

```bash
php artisan key:generate
```

## 8. Permission Folder

Pastikan folder berikut bisa ditulis aplikasi:

```text
storage
bootstrap/cache
```

Umumnya permission:

```bash
chmod -R 775 storage bootstrap/cache
```

Jika hosting memakai user yang sama untuk web server, permission ini biasanya cukup.

## 9. Migrasi dan Seeder

Jalankan:

```bash
php artisan migrate --seed --force
```

Seeder akan membuat:

- Role `admin`, `guru`, `siswa`.
- Akun admin awal.
- Kelas 1 sampai Kelas 6.
- Pengaturan presensi awal.

Akun admin awal:

```text
Nama pengguna: admin
Kata sandi: password
```

Segera ubah kata sandi setelah login pertama.

## 10. Storage Link

Jalankan:

```bash
php artisan storage:link
```

Perintah ini membuat link:

```text
public/storage -> storage/app/public
```

Ini diperlukan agar logo, favicon, foto siswa, foto guru, dan foto profil bisa tampil.

Jika `storage:link` gagal karena batasan hosting, buat symlink manual dari cPanel Terminal:

```bash
ln -s /home/USERNAME/hadirku/storage/app/public /home/USERNAME/hadirku/public/storage
```

Jika symlink juga tidak diizinkan, hubungi penyedia hosting. Tanpa storage link, upload gambar dapat tersimpan tetapi tidak tampil.

## 11. Cache Production

Setelah konfigurasi benar:

```bash
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika setelah mengubah `.env` aplikasi masih membaca konfigurasi lama, jalankan:

```bash
php artisan optimize:clear
php artisan config:cache
```

## 12. Cron Scheduler

Alpa otomatis membutuhkan scheduler Laravel.

Di cPanel buka `Cron Jobs`, tambahkan cron setiap menit:

```bash
* * * * * cd /home/USERNAME/hadirku && /usr/local/bin/php artisan schedule:run >> /dev/null 2>&1
```

Jika path PHP berbeda, cek melalui Terminal:

```bash
which php
```

Contoh path lain yang sering dipakai hosting:

```text
/usr/bin/php
/opt/cpanel/ea-php83/root/usr/bin/php
```

## 13. HTTPS dan Kamera

Fitur kamera membutuhkan HTTPS di production.

Pastikan:

- SSL aktif di cPanel.
- `APP_URL` memakai `https://`.
- Browser mendapat izin kamera.
- Model face-api.js tersedia di `public/models`.

Jika kamera tidak muncul:

- Cek izin kamera di browser.
- Cek apakah domain sudah HTTPS.
- Cek Console browser untuk error JavaScript.
- Cek apakah file `public/vendor/face-api/face-api.min.js` dan folder `public/models` ikut terupload.

## 14. Setelah Login Pertama

Masuk sebagai admin, lalu lakukan:

1. Ubah kata sandi admin.
2. Atur logo dan favicon.
3. Atur jam presensi.
4. Atur hari sekolah.
5. Isi atau impor Kalender Akademik.
6. Buat data kelas.
7. Buat data guru.
8. Impor atau tambah data siswa.
9. Registrasikan wajah siswa.
10. Uji presensi wajah dari perangkat Android.

## 15. Update Aplikasi di cPanel

Saat ada update kode:

```bash
cd /home/USERNAME/hadirku
composer install --no-dev --optimize-autoloader
php artisan migrate --force
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Jika ada perubahan frontend:

```bash
npm install
npm run build
```

Jika Node.js tidak ada di hosting, build dari lokal lalu upload ulang `public/build`.

## 16. Troubleshooting

### Halaman error 500

Cek file log:

```text
storage/logs/laravel.log
```

Pastikan:

- `.env` benar.
- `APP_KEY` sudah dibuat.
- Database bisa diakses.
- Permission `storage` dan `bootstrap/cache` benar.
- Cache sudah dibersihkan.

### Gambar tidak tampil

Jalankan:

```bash
php artisan storage:link
```

Pastikan folder `storage/app/public` dan `public/storage` tersedia.

### Alpa otomatis tidak berjalan

Pastikan cron scheduler aktif setiap menit.

Coba jalankan manual:

```bash
php artisan schedule:run
```

Pastikan juga:

- `Alpa Otomatis` aktif di Pengaturan Presensi.
- Jadwal kelas sudah dibuat.
- Hari tersebut termasuk Hari Sekolah.
- Tanggal tersebut bukan libur `Presensi Tutup`.

### Presensi wajah tidak bisa mulai

Cek:

- Hari ini bukan libur `Presensi Tutup`.
- Hari ini termasuk Hari Sekolah, atau Kalender Akademik membuka `Presensi Buka`.
- Kelas sudah dipilih.
- Siswa sudah punya minimal 3 descriptor wajah.
- Browser memakai HTTPS dan izin kamera aktif.

### Import Excel gagal

Cek:

- Format file CSV/XLS/XLSX.
- Kolom sesuai template.
- Ukuran file maksimal 2 MB.
- Data kelas sudah ada sebelum impor siswa.
- Rentang libur tidak bertumpang tindih sebelum impor kalender akademik.

## 17. Catatan Keamanan

- Jangan biarkan `APP_DEBUG=true` di production.
- Jangan gunakan kata sandi admin default.
- Jangan taruh seluruh project langsung di `public_html` kecuali document root diarahkan dengan benar ke folder `public`.
- Batasi akses file `.env`.
- Gunakan HTTPS.
- Backup database secara berkala.
