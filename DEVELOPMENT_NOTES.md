# HadirKu Development Notes

File ini adalah pegangan teknis singkat untuk lanjut mengembangkan project HadirKu.

## Prinsip Utama

HadirKu adalah Laravel monolith production-oriented untuk presensi siswa SD memakai realtime face recognition di browser. Saat mengembangkan, pertahankan arsitektur ini:

- Tetap Blade + Livewire + Alpine.js + TailwindCSS.
- Tetap Laravel session auth.
- Jangan menambah SPA framework, React, Vue SPA, REST auth API, Sanctum, Passport, atau JWT.
- Jangan menambah dependency websocket atau arsitektur queue-heavy untuk flow utama.
- Optimalkan untuk shared hosting cPanel dengan HTTPS.
- Jaga performa untuk skala 50-100 siswa dan perangkat Android.
- Face recognition wajib memakai `face-api.js` di browser; backend hanya menyimpan descriptor dan attendance.
- Bahasa default project adalah Indonesia. Gunakan `APP_LOCALE=id`, `APP_FALLBACK_LOCALE=id`, `APP_FAKER_LOCALE=id_ID`, dan `translatedFormat(...)` untuk tanggal yang tampil ke pengguna agar bulan seperti `Mei` tidak berubah menjadi `May`.

Jika ada ketidaksesuaian antara target dan repo lokal, cek package yang terpasang sebelum coding. Saat catatan ini dibuat, repo lokal memakai Laravel `^13.8`, PHP `^8.3`, Livewire `^4.3`, dan SQLite lokal; target production dari pemilik project adalah PHP 8.2+, Livewire v3 style, dan MySQL/MariaDB.

## Cara Menjalankan

Install dependency:

```bash
composer install
npm install
```

Setup aplikasi:

```bash
cp .env.example .env
php artisan key:generate
php artisan migrate --seed
```

Mode development:

```bash
composer run dev
```

Alternatif manual:

```bash
php artisan serve
npm run dev
php artisan queue:listen --tries=1 --timeout=0
```

Build frontend:

```bash
npm run build
```

Test:

```bash
composer test
```

Format kode PHP:

```bash
vendor/bin/pint
```

## File dan Folder Penting

- `README.md`: dokumentasi ringkas HadirKu, fitur, role, perintah lokal, scheduler, dan checklist operasional.
- `INSTALL.md`: panduan instalasi di cPanel/shared hosting, termasuk struktur folder, `.env`, database, storage link, cron scheduler, HTTPS, dan troubleshooting.
- `routes/web.php`: entry route utama, include route auth/admin/guru/siswa.
- `routes/admin.php`: halaman admin dan Livewire admin.
- `routes/guru.php`: halaman guru, face attendance, registrasi wajah, manual attendance, dan endpoint descriptor kelas.
- `routes/siswa.php`: beranda siswa (`/dashboard`), registrasi wajah, dan pengajuan izin/sakit.
- `routes/console.php`: scheduler `attendance:auto-alpha`.
- `app/Livewire/Admin`: fitur admin.
- `app/Livewire/Admin/AcademicCalendar.php`: CRUD Kalender Akademik untuk libur nasional, libur semester, libur sekolah, kegiatan sekolah, dan libur lain.
- `app/Models/AcademicHoliday.php`: model kalender akademik/libur yang juga dipakai untuk memblokir presensi pada tanggal libur.
- `app/Imports/StudentsImport.php`: import siswa dari CSV/XLSX dengan kolom `nis`, `nama`, `kelas`, `gender`.
- `app/Livewire/Guru/FaceAttendance.php`: simpan presensi hasil scan wajah.
- `app/Livewire/Guru/AttendanceReport.php`: rekap presensi guru untuk kelas bawaan, termasuk filter, ekspor Excel/PDF, tabel desktop, card mobile, dan detail mobile.
- `app/Livewire/Siswa/FaceRegistration.php`: simpan descriptor wajah siswa; siswa otomatis memakai dirinya sendiri, admin/guru memilih kelas dan siswa.
- `app/Livewire/Siswa/LeaveRequest.php`: pengajuan izin/sakit siswa.
- `app/Livewire/Attendance/ManualAttendance.php`: input manual, koreksi, approve, reject.
- `resources/views/livewire/guru/face-attendance.blade.php`: JavaScript scan wajah realtime.
- `resources/views/livewire/siswa/face-registration.blade.php`: JavaScript ambil descriptor wajah.
- `resources/views/layouts/app.blade.php`: layout utama aplikasi, Livewire scripts, loader `face-api.js` local-first dengan fallback CDN.
- `resources/js/app.js`: SweetAlert toast dan confirm dialog global.
- `resources/css/app.css`: komponen Tailwind `hk-*`.
- `database/migrations`: struktur tabel aplikasi.
- `database/seeders`: role, admin default, kelas default, setting default.
- `public/models`: model face-api.js.
- `public/vendor/face-api/face-api.min.js`: library face-api.js lokal untuk mengurangi risiko gagal load dari CDN.
- `public/sounds/success.mp3`: suara sukses presensi.
- `public/sounds/error.mp3`: suara gagal presensi.

## Pola Kode yang Sudah Dipakai

Livewire dipakai sebagai controller halaman sekaligus state server-side. Banyak komponen memakai `WithPagination`, property publik untuk filter/form, dan `dispatch('hadirku-toast', ...)` untuk feedback.

Model memakai Eloquent sederhana dengan relasi eksplisit. Untuk perubahan data yang membuat lebih dari satu record atau menyentuh upload file, komponen memakai `DB::transaction()`.

UI memakai Blade + Tailwind utility class. Komponen CSS global memakai prefix `hk-`, misalnya `hk-card`, `hk-input`, `hk-btn-primary`, `hk-table`.

Role guard memakai middleware custom `role` di `app/Http/Middleware/RoleMiddleware.php`, didaftarkan di `bootstrap/app.php`.

## Pola UI Kanonik

Sebelum mengubah halaman, cek halaman sejenis yang sudah final agar konsistensi visual tidak pecah. Untuk summary/stat card halaman CRUD, halaman operasional, dan dashboard, gunakan pola `resources/views/livewire/admin/guru-management.blade.php` sebagai acuan. Summary/stat card harus menjadi section terpisah di atas card utama halaman, bukan berada di dalam card konten.

Bahasa antarmuka wajib memakai bahasa Indonesia yang baik dan benar sesuai KBBI. Gunakan label `alpa` untuk status tidak hadir tanpa keterangan; `alpha` hanya dipakai sebagai nilai teknis database/enum dan nama command lama.

Pola summary/stat card:

```blade
<section class="grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-4">
    <div class="hk-card p-3 sm:p-5">
        <div class="grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4">
            <div>
                <div class="text-[10px] font-bold uppercase tracking-wide text-blue-600 dark:text-blue-300 sm:text-sm">
                    Label
                </div>
                <div class="mt-1 text-2xl font-extrabold text-slate-900 dark:text-white sm:mt-2 sm:text-3xl">
                    {{ $value }}
                </div>
            </div>
            <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-2xl bg-blue-100 text-blue-600 dark:bg-blue-500/20 dark:text-blue-300 sm:h-12 sm:w-12">
                <!-- Icon SVG -->
            </div>
        </div>
    </div>
</section>
```

Dashboard boleh punya hero/section pembuka sendiri, tetapi summary/stat card dashboard tetap memakai pola compact ini agar mobile tidak terlalu panjang.

Untuk tabel padat dengan banyak kolom, jangan jadikan horizontal scroll sebagai pengalaman utama di mobile. Gunakan dua representasi:

- Mobile: card list `md:hidden` berisi identitas utama, badge status, metadata waktu, catatan ringkas, dan action button yang mudah disentuh.
- Desktop/tablet: tabel tetap dipakai mulai `md` dengan `hidden md:block` atau pola setara.

Pola ini sudah diterapkan di `resources/views/livewire/attendance/manual-attendance.blade.php`.

Untuk fitur tambah/edit data yang sejenis dengan halaman yang sudah final, jadikan halaman final itu sebagai referensi. Pola terbaru untuk tambah data adalah tombol aksi di header lalu modal formulir, bukan form permanen yang memakan ruang halaman utama.

## Aturan Generate Kode

Saat membuat kode untuk project ini:

- Berikan implementasi penuh, bukan pseudo code.
- Untuk Blade, tulis full Blade file.
- Untuk Livewire, tulis full class dan full Blade view.
- Untuk migration, tulis full migration.
- Untuk model, tulis full model.
- Untuk route, tulis full route.
- Untuk JavaScript, tulis full script.
- Jangan meminta user mengedit manual bagian yang bisa dikerjakan langsung.
- Ikuti pola folder, naming, event toast, validasi, transaksi, dan UI yang sudah ada.
- Gunakan SweetAlert2 toast global, bukan session flash alert block baru.
- Gunakan eager loading pada list dan laporan.
- Gunakan transaksi untuk operasi yang menyentuh beberapa tabel atau file upload.
- Jaga query tetap ringan dan shared-hosting friendly.
- Import siswa memakai NIS sebagai kunci update; kelas pada file harus sudah ada di master kelas.

## Aturan Bisnis yang Perlu Dijaga

- Satu siswa hanya boleh punya satu presensi per tanggal.
- Presensi hasil scan langsung `approved`.
- Izin/sakit dari siswa harus `pending` sampai disetujui atau ditolak.
- Manual attendance dari admin/guru langsung `approved`, kecuali koreksi status menjadi `alpha`/alpa harus menjadi `rejected`.
- Reject pengajuan mengubah status menjadi `alpha`/alpa dan approval menjadi `rejected`.
- Siswa yang pengajuannya pernah `rejected` boleh mengirim ulang untuk tanggal yang sama.
- Guru nonaktif tidak boleh login.
- Descriptor wajah dibatasi oleh `attendance_settings.max_descriptors`.
- Minimal descriptor yang diharapkan untuk siswa adalah 3.
- Maksimum descriptor siswa adalah 10.
- Descriptor memakai strategi FIFO: ketika limit terlampaui, descriptor tertua dihapus otomatis.
- Registrasi wajah bisa dilakukan oleh siswa, admin, dan guru.
- Admin/guru harus memilih siswa target sebelum mengambil descriptor.
- Siswa hanya meregistrasikan descriptor untuk dirinya sendiri.
- Face matching memakai threshold dari `attendance_settings.face_match_threshold`.
- Alpa otomatis hanya berjalan jika setting `auto_alpha` aktif dan kelas memiliki `ClassSchedule`.
- Alpa otomatis harus melewati tanggal libur akademik dengan `allow_attendance = false`.
- Status presensi valid: `hadir`, `terlambat`, `izin`, `sakit`, `alpha`. Label UI untuk `alpha` adalah `alpa`.
- Scan sebelum batas terlambat menghasilkan `hadir`.
- Scan setelah batas terlambat menghasilkan `terlambat`.
- Siswa tanpa presensi sampai jam kelas selesai menjadi `alpha`/alpa otomatis.
- Kalender akademik memakai tabel `academic_holidays`. Jenis libur valid: `national`, `semester`, `school`, `event`, dan `other`; label UI-nya berbahasa Indonesia.
- Rentang tanggal libur tidak boleh bertumpang tindih. Jika butuh pengecualian, ubah aturan ini secara sadar karena saat ini validasi Livewire menolak overlap.
- Tanggal libur dengan `allow_attendance = false` menolak presensi wajah dan pengajuan izin/sakit siswa. Jika sekolah tetap mengadakan presensi pada tanggal tertentu, admin harus memilih `Presensi Buka`.
- Hari sekolah reguler disimpan di `attendance_settings.school_days` sebagai array angka ISO hari: 1 Senin sampai 7 Minggu. Defaultnya `[1,2,3,4,5,6]` atau Senin-Sabtu.
- Hari di luar `school_days` menolak presensi wajah, menolak pengajuan izin/sakit siswa, dan melewati alpa otomatis. Kalender Akademik dengan `allow_attendance = true` dapat menjadi pengecualian agar presensi tetap dibuka pada tanggal tertentu.

## Flow Face Attendance Guru

Flow yang harus dipertahankan:

1. Guru login.
2. Guru memilih kelas.
3. Guru menekan start scan.
4. Browser load model face-api jika belum pernah load.
5. Browser load descriptor untuk kelas terpilih saja.
6. Browser melakukan realtime recognition dengan webcam.
7. Presensi otomatis tersimpan jika wajah dikenali dan belum absen hari itu.
8. Sistem menerapkan cooldown anti double scan.
9. Guru bisa stop scan dan kamera dimatikan.

## Flow Registrasi Wajah

Flow UX yang sudah dipakai dan perlu dipertahankan:

1. `face-api.js` dimuat local-first dari `public/vendor/face-api/face-api.min.js`, lalu fallback CDN jika file lokal gagal.
2. Model tetap dimuat dari `public/models`.
3. Mode unggah foto menampilkan status proses upload/deteksi wajah.
4. Setelah descriptor tersimpan dari unggah foto, mode tetap di unggah foto.
5. Mode kamera menampilkan overlay kotak deteksi wajah.
6. Tombol ambil wajah memberi feedback cepat/loading dan memakai deteksi wajah terakhir yang masih segar agar jeda terasa lebih halus.

## Progress Peninjauan Halaman

Status peninjauan UI terakhir:

- Admin Registrasi Wajah: sudah dirapikan UX upload, kamera, capture, dan loader `face-api.js` lokal. Area kamera memakai rasio potret pada mobile, rasio lanskap pada desktop, serta pilihan kamera `Depan`/`Belakang` sebelum kamera diaktifkan.
- Admin/Guru Presensi Wajah: admin memiliki menu `Presensi` di sidebar tepat di bawah `Dashboard`, route `/admin/face-attendance` bernama `admin.face.attendance`, dan dapat memilih kelas untuk melakukan scan wajah siswa. Endpoint descriptor admin tersedia di `/admin/class-descriptors/{classId}`. UI memakai view presensi wajah yang sama dengan guru agar alur scan tetap konsisten. Halaman sudah direview: struktur mengikuti pola admin, kontrol kelas dipisah di card atas sebagai blok `Kelas Pemindaian` dengan kelas aktif dan tombol `Ganti Kelas`/`Pilih Kelas`, area kamera punya empty state, tombol memakai `Mulai Pemindaian`/`Hentikan Pemindaian`, status memakai Bahasa Indonesia, tombol mulai/berhenti saling aktif sesuai status pemindaian, daftar `Presensi Terbaru` menampilkan status, dan indikator kesiapan kelas menampilkan jumlah siswa, siswa dengan data wajah, siswa siap dipindai, serta total descriptor. Tombol/select ganti kelas dikunci saat pemindaian berjalan agar kelas tidak berubah di tengah proses. Success sound presensi memakai `public/sounds/success.mp3`; sound gagal memakai `public/sounds/error.mp3`. Area kamera memakai `wire:ignore` agar stream kamera tidak putus ketika Livewire me-render ulang komponen setelah respons presensi, misalnya saat siswa sudah memiliki presensi hari ini. Area kamera memakai rasio potret pada mobile, tetap lanskap pada desktop, dan menyediakan pilihan kamera `Depan`/`Belakang` sebelum pemindaian dimulai. Jika presensi ditutup karena libur `Presensi Tutup` atau bukan `Hari Sekolah`, halaman menampilkan banner sejak awal dan tombol mulai berubah menjadi `Presensi Ditutup`; JavaScript juga menjaga tombol tidak aktif ulang. Jika Kalender Akademik memberi pengecualian `Presensi Buka`, halaman menampilkan banner info dan scan tetap bisa dimulai.
- Admin Izin/Sakit: form input manual dan edit memakai modal, filter/action dirapikan, tabel desktop disederhanakan, mobile memakai card list tanpa horizontal scroll, filter mobile memakai pencarian + ikon filter yang membuka bottom sheet preferensi dengan tombol `Terapkan` dan `Atur Ulang`, dan default persetujuan `Menunggu` dihitung sebagai 1 filter aktif pada badge ikon mobile. Summary card disamakan dengan `guru-management`.
- Admin Dashboard: summary card disamakan dengan pola compact `guru-management`, sehingga 8 statistik tampil 2 kolom di mobile dan 4 kolom di layar lebih besar.
- Admin Kelola Guru: form tambah guru dipindahkan ke modal melalui tombol `Tambah Guru`; halaman utama fokus pada ringkasan, filter, dan daftar guru.
- Admin Kelola Siswa: form tambah siswa dipindahkan ke modal melalui tombol `Tambah Siswa`; modal `Impor` tetap terpisah; halaman utama fokus pada filter, ekspor, dan daftar siswa.
- Registrasi Wajah: summary card disamakan dengan pola `guru-management` dan diposisikan sebagai section terpisah di atas card utama.
- Admin Rekap: judul dirapikan menjadi `Rekap Presensi`, summary card mengikuti pola compact `guru-management`, filter desktop dibuat lega dengan pencarian pada baris sendiri, chip filter aktif, grup tombol cepat periode (`Hari Ini`, `7 Hari`, `Bulan Ini`, `Kustom`) tanpa opsi semua tanggal, default periode `Bulan Ini`, input rentang `Tanggal Mulai`/`Tanggal Selesai` yang hanya tampil saat `Kustom`, validasi rentang tanggal, dan tombol `Atur Ulang` yang kembali ke `Bulan Ini`; filter mobile memakai pencarian + ikon filter yang membuka bottom sheet preferensi dengan tombol `Terapkan` dan `Atur Ulang`, termasuk grup tombol cepat periode yang sama. Indikator filter mobile menghitung rentang tanggal sebagai satu filter. Halaman menampilkan info jumlah hasil. Ekspor digabung menjadi satu tombol dropdown `Ekspor` dengan pilihan `Excel` dan `PDF`; nama file ekspor dinamis mengikuti rentang tanggal atau tanggal unduh, Excel memakai heading native, auto-size kolom, rentang tanggal, serta label Indonesia. Ekspor PDF tersedia melalui view `resources/views/exports/attendance-report-pdf.blade.php`. Tabel desktop diringkas menjadi `Siswa`, `Waktu`, `Presensi`, dan `Keterangan`. Empty state menyertakan tombol `Atur Ulang`. Tampilan mobile memakai card list ringkas tanpa horizontal scroll sebagai pengalaman utama. Detail mobile Rekap seperti jam, persetujuan, dan keterangan muncul di bottom sheet saat card diklik. Di bawah section tabel/paginasi ada card terpisah `Tren Kehadiran` seperti summary card/section sendiri, berbasis SVG responsif tanpa dependency chart tambahan; filter grafik terpisah dari filter tabel dan mendukung `7 Hari Terakhir`, `14 Hari Terakhir`, `30 Hari Terakhir`, `Bulan Ini`, serta rentang tanggal manual. Default filter grafik adalah `Bulan Ini`. Grafik menampilkan seri `Hadir`, `Terlambat`, `Izin`, `Sakit`, dan `Tidak Hadir`, serta tooltip interaktif saat kursor diarahkan ke area tanggal. Di bawah grafik ada dua card ranking terpisah, `Siswa Paling Rajin` dan `Sering Tidak Hadir`, yang mengikuti filter tanggal tren; ranking tidak menampilkan siswa bernilai 0 hari. Bottom sheet/mobile modal harus diletakkan di luar `hk-card` atau kontainer blur supaya `position: fixed` tidak terikat ke kartu.
- Admin Kalender Akademik: menu `Kalender Akademik` ditambahkan di sidebar admin, route `/admin/academic-calendar`, komponen `App\Livewire\Admin\AcademicCalendar`, model `App\Models\AcademicHoliday`, dan migration `academic_holidays`. Halaman memakai summary card compact dan card utama dengan tombol `Tambah Libur`, tombol `Impor`, filter pencarian/jenis/tahun, tabel desktop, card mobile, modal tambah/edit, serta modal impor. Impor libur menerima CSV/XLS/XLSX dengan pratinjau valid/tidak valid sebelum simpan. Template impor memakai kolom `nama_libur`, `jenis`, `tanggal_mulai`, `tanggal_selesai`, `presensi`, dan `keterangan`; `presensi` bernilai `Tutup` atau `Buka`, jika kosong dianggap `Tutup`. Default data libur menutup presensi. Rentang tanggal libur yang bertumpang tindih ditolak. Presensi wajah, pengajuan izin/sakit siswa, dan alpa otomatis menghormati libur yang menutup presensi. Kalender Rekap Siswa menandai libur dengan legenda `Libur`.
- Admin Pengaturan Presensi: header gradient lama diganti pola halaman konsisten, logo dan favicon berada dalam card `Identitas Aplikasi` tetapi dipisah sebagai dua blok berurutan (logo di atas, favicon di bawah), favicon disimpan di `attendance_settings.favicon_path` dan dipasang di layout admin/guest melalui `<link rel="icon">`, form utama dibagi menjadi card `Pengenalan Wajah` dan `Aturan Presensi`, istilah Inggris diganti ke Bahasa Indonesia, `Ambang Kecocokan Wajah` memakai slider + input angka dengan konteks ketat/seimbang/longgar, `Hari Sekolah` memakai pilihan hari Senin-Minggu untuk mendukung sekolah 5 hari atau 6 hari, `Alpa Otomatis` memakai toggle visual, ada pratinjau aturan waktu, deskripsi singkat umum dihapus agar ringkas tetapi teks bantuan berisi syarat/ketentuan/konsekuensi tetap dipertahankan, dan tombol simpan dibedakan menjadi `Simpan Identitas` serta `Simpan Aturan Presensi`.
- Konsistensi copy lintas halaman: deskripsi singkat umum yang hanya mengulang fungsi halaman/section sudah dihapus dari Rekap, Izin/Sakit, Kelola Siswa, Dashboard Admin, Beranda Guru, Beranda Siswa, Pengajuan Izin/Sakit Siswa, dan Presensi Guru. Teks bantuan yang berisi syarat, ketentuan, konsekuensi, atau instruksi penting tetap dipertahankan, misalnya format unggahan foto/logo/favicon, konsekuensi persetujuan, kelas bawaan guru, dan panduan descriptor.
- Konsistensi modal/pop up: modal formulir memakai overlay gelap blur, panel `rounded-2xl`, border `slate`, shadow besar, tinggi maksimal `90vh`, dan scroll internal. Header modal memakai pola eyebrow kecil, judul tebal, tombol tutup ikon di kanan, dan border bawah. Modal edit kelas, edit guru, edit siswa, impor siswa, registrasi wajah, serta komponen modal Breeze/profil sudah disamakan; bottom sheet mobile tetap memakai pola sheet bawah dengan handle, judul ringkas, dan tombol tutup ikon. Header modal tidak memakai deskripsi singkat generik. Informasi penting dipindahkan ke konteks field atau info box di dalam form, misalnya kredensial awal siswa/guru, kolom impor, dan konsekuensi presensi manual.
- Ubah Profil: header gradient besar diganti judul sederhana, dengan tombol `Kembali` sejajar di kanan judul dan diarahkan ke dashboard sesuai role, bukan `url()->previous()`. Label halaman/menu memakai `Ubah Profil`. Konten memakai card terpisah untuk `Informasi Profil` dan `Ubah Kata Sandi`. Fitur hapus akun tidak dipakai di project ini untuk admin, guru, ataupun siswa; jangan menambahkan tombol/modal/route hapus akun di halaman profil. Deskripsi singkat generik di header card dihapus; teks bantuan penting tetap diletakkan dekat field atau info box, misalnya format foto, pengelolaan nama pengguna, kata sandi kuat, dan verifikasi surel. Foto profil menerima JPG, PNG, dan WEBP maksimal 2 MB, dengan pratinjau langsung, nama file, tombol `Batalkan Pilihan`, serta tombol `Hapus Foto`/`Batal Hapus Foto` sebelum disimpan. Form profil dan kata sandi memakai state submit `Menyimpan...`. Form kata sandi memakai aturan backend dan checklist frontend yang selaras: minimal 8 karakter, berisi huruf, berisi angka, berbeda dari kata sandi saat ini, dan konfirmasi harus sama; form juga menampilkan indikator kekuatan kata sandi. Status sukses profil/kata sandi memakai toast global `hadirku-toast`.
- Catatan foto profil: jangan menambahkan `disabled` pada input file saat submit, karena berkas bisa tidak ikut terkirim meskipun pratinjau berhasil. Untuk role siswa, perubahan/hapus foto profil harus menyinkronkan `users.photo` dan `students.photo`.
- Login: halaman memakai Bahasa Indonesia (`Nama Pengguna atau NIS`, `Kata Sandi`, tombol `Masuk`) dan state submit `Masuk...`. Logo aplikasi tampil di dalam card login pada desktop dan mobile, dengan ukuran logo lebih besar agar padding background warna lebih tipis dan logo lebih jelas. Desktop login memakai satu section besar; area brand ada di kiri dan card masuk berada di kanan di dalam section yang sama. Teks bantuan login ringkas: `Masuk menggunakan NIS atau nama pengguna.` Panel kiri desktop tidak memakai statistik teknis dan tidak menampilkan daftar poin fitur. Pesan gagal login dan validasi kosong memakai Bahasa Indonesia langsung di `LoginRequest`. Guru nonaktif tetap tidak boleh login dan error tampil melalui alert di atas form. Registrasi publik tetap nonaktif; akun dibuat admin.
- Guru Beranda: header hero gradient lama dihapus; gunakan header sederhana dengan judul `Beranda` dan jam realtime. Summary card compact mengikuti pola `guru-management` dengan `Kelas Bawaan`, `Presensi Hari Ini`, `Menunggu`, dan `Siap Dipindai`. Data beranda guru dibatasi ke kelas bawaan guru; jika belum ada kelas bawaan, tampil empty state agar guru menghubungi admin. Aksi utama dibuat 3 card konsisten: `Presensi Wajah`, `Registrasi Wajah`, dan `Izin/Sakit`. Card `Tips` dihapus. Beranda menampilkan `Aktivitas Terakhir` dan `Pengajuan Menunggu` berdasarkan kelas bawaan. Untuk guru dan siswa, label menu/judul halaman memakai `Beranda`, meskipun nama route tetap `*.dashboard`.
- Siswa Beranda: header hero gradient lama dihapus; gunakan header sederhana dengan judul `Beranda`, identitas siswa/kelas, dan jam realtime. Summary card compact menampilkan `Status Wajah`, `Presensi Hari Ini`, `Pengajuan Aktif`, dan `Riwayat Terakhir`. Status wajah berdasarkan jumlah descriptor: `Belum Ada`, `Perlu Ditambah`, atau `Siap Digunakan` jika minimal 3 descriptor terpenuhi. Aksi utama memakai card `Registrasi Wajah` dan `Izin/Sakit`; panduan minimal descriptor dipindah ke card `Registrasi Wajah`, bukan card panduan terpisah. Beranda menampilkan card `Pengajuan Aktif` dan `Riwayat Presensi` maksimal 3 data agar ringan di mobile.
- Siswa Rekap: menu `Rekap` ditambahkan di sidebar siswa, route `/siswa/attendance-report`, komponen `App\Livewire\Siswa\AttendanceReport`. Data wajib dibatasi hanya untuk siswa login dan hanya bulan berjalan; jangan tambahkan filter tanggal bebas atau ekspor pada versi siswa kecuali diminta. Halaman memakai summary card `Hadir`, `Terlambat`, `Izin/Sakit`, dan `Alpa`; summary `Total Data` tidak dipakai. Filter status kecil dan card list menjadi tampilan utama. Di bawah daftar ada card `Kalender Bulan Ini` berupa kalender mini dengan warna status per tanggal, penanda `Libur` dari Kalender Akademik, dan penanda `Bukan Hari Sekolah` dari pengaturan hari sekolah. Grafik tidak dipakai di Rekap Siswa karena data per siswa lebih cocok dibaca sebagai kalender/list. Beranda Siswa memiliki tautan `Lihat Rekap` pada card `Riwayat Presensi`.
- Navigasi guru mobile: gunakan bottom navigation tetap di bawah layar, bukan sidebar geser. Urutan menu wajib: `Beranda`, `Izin`, `Presensi` sebagai tombol tengah menonjol, `Face ID`, dan `Rekap`. Sidebar guru tetap ada untuk desktop; tombol hamburger mobile disembunyikan untuk guru.
- Navigasi siswa mobile: gunakan bottom navigation tetap di bawah layar, bukan sidebar geser. Urutan menu wajib: `Beranda`, `Izin`, `Face ID`, `Rekap`, dan `Profil`. Sidebar siswa tetap ada untuk desktop; tombol hamburger mobile disembunyikan untuk siswa. Bottom navigation siswa tidak memakai tombol tengah menonjol.
- Guru Rekap: menu `Rekap` ditambahkan di sidebar guru setelah `Izin/Sakit`, route `/guru/attendance-report`, komponen `App\Livewire\Guru\AttendanceReport`. Tampilan awal memakai kelas bawaan guru (`default_class_id`) sebagai filter kelas, tetapi guru boleh memilih `Semua Kelas` atau kelas lain dari filter kelas desktop maupun bottom sheet mobile. Tombol `Atur Ulang` mengembalikan filter ke kelas bawaan dan periode `Bulan Ini`; jika kelas bawaan belum ada, tampilan awal memakai `Semua Kelas`. Halaman memakai summary card compact, filter pencarian/kelas/status/persetujuan/periode dengan default `Bulan Ini`, tabel desktop, card mobile, detail mobile, serta ekspor dropdown `Excel`/`PDF`. Grafik tren belum ditambahkan untuk menjaga halaman tetap ringan.
- Peninjauan berikutnya dapat dilanjutkan dari halaman Izin/Sakit role guru atau halaman lain sesuai arahan user.

## Hal yang Perlu Diwaspadai

- `RegisteredUserController` masih bawaan Breeze dan tidak mengisi `username`; kalau registrasi publik tetap dipakai, perlu disesuaikan dengan skema login HadirKu.
- Endpoint `guru/class-descriptors/{classId}` mengembalikan seluruh descriptor kelas untuk guru yang login, tanpa pembatasan guru-kelas.
- Face recognition berjalan di browser, jadi performa tergantung perangkat dan jumlah descriptor kelas.
- `face-api.js` sudah tersedia lokal di `public/vendor/face-api/face-api.min.js` dengan fallback CDN. Jika file lokal hilang atau cache build bermasalah, fitur kamera bisa gagal memuat library.
- Scheduler auto alpha tidak jalan hanya dengan web server; perlu `php artisan schedule:work` atau cron/scheduler Laravel.
- Field `email` sudah nullable, tetapi migration awal masih unique. Database umum mengizinkan banyak null, tetapi perilaku bisa bergantung engine.
- `ClassManagement::delete()` menghapus kelas dan karena FK cascade, siswa di kelas tersebut ikut terhapus. Ini perilaku besar dan perlu konfirmasi UI yang kuat.

## Ide Perbaikan Lanjutan

- Ganti README bawaan Laravel dengan dokumentasi HadirKu.
- Matikan atau rapikan registrasi publik jika user hanya dibuat admin.
- Tambahkan heading Excel, filter range tanggal, dan opsi PDF untuk laporan.
- Tambahkan pembatasan akses descriptor kelas jika nanti guru hanya boleh mengelola kelas tertentu.
- Tambahkan test untuk duplicate attendance, approval izin/sakit, guru inactive login, dan auto alpha.
- Tambahkan visual regression/manual screenshot checklist untuk memastikan konsistensi summary card, header, filter, dan tabel antar halaman.
