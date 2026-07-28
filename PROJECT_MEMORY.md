# HadirKu Project Memory

Catatan ini dibuat pada 2026-05-24 setelah membaca struktur project lokal di `c:\laragon\www\hadirku`.

## Instruksi Kanonik dari Pemilik Project

Bagian ini adalah sumber kebenaran produk dan arsitektur untuk pekerjaan berikutnya. Jika ada perbedaan kecil dengan kondisi dependency lokal, ikuti arah arsitektur ini saat membuat fitur baru, lalu sesuaikan implementasi dengan versi package yang benar-benar terpasang.

- Nama project: HadirKu.
- Subtitle sekolah: SDN 01 Jatipurwo.
- Tujuan: sistem presensi siswa SD berbasis realtime face recognition.
- Target skala: 50-100 siswa.
- Target pengguna: admin, guru, siswa.
- Deployment: shared hosting cPanel dengan HTTPS aktif.
- Sistem harus ringan, modern, mudah dipakai guru SD, stabil di shared hosting, usable di Android, dan production-ready.

## Ringkasan

HadirKu adalah aplikasi presensi sekolah berbasis Laravel, Livewire, Tailwind, dan face recognition di browser. Target saat ini terlihat untuk SDN 01 Jatipurwo. Aplikasi memakai tiga role utama: `admin`, `guru`, dan `siswa`.

Absensi utama dilakukan oleh guru melalui kamera dan `face-api.js`. Siswa lebih dulu menyimpan face descriptor dari akun masing-masing. Admin mengelola data kelas, guru, siswa, pengaturan presensi, input/koreksi manual, dan laporan.

## Stack

- Backend: PHP `^8.3`, Laravel `^13.8`
- UI interaktif: Livewire `^4.3`, Alpine.js, Blade
- CSS/build: Tailwind CSS, Vite
- Auth starter: Laravel Breeze
- Role/permission: `spatie/laravel-permission`
- Export laporan: `maatwebsite/excel`
- PDF tersedia sebagai dependency: `barryvdh/laravel-dompdf`
- Database default: SQLite, file `database/database.sqlite`
- Face recognition: `face-api.js` local-first dari `public/vendor/face-api/face-api.min.js` dengan fallback CDN, model disimpan di `public/models`
- Notifikasi UI: SweetAlert2 dari `resources/js/app.js`

Catatan target dari pemilik project:

- Backend ditargetkan Laravel 13, PHP 8.2+, Livewire v3 style, Spatie Laravel Permission, Laravel Breeze, dan MySQL/MariaDB untuk production.
- Frontend wajib tetap Blade, TailwindCSS, Alpine.js, Livewire, dan SweetAlert2.
- Face recognition wajib browser-based memakai `face-api.js`; tidak boleh memakai external AI API atau cloud face recognition.

Catatan kondisi repo saat dibaca:

- `composer.json` memasang PHP `^8.3`, Laravel `^13.8`, dan Livewire `^4.3`.
- `.env.example` memakai SQLite untuk lokal, tetapi arah production adalah MySQL/MariaDB.
- Default bahasa project adalah Indonesia: `APP_LOCALE=id`, `APP_FALLBACK_LOCALE=id`, dan `APP_FAKER_LOCALE=id_ID`.
- Tanggal yang tampil ke pengguna harus memakai bahasa Indonesia. Untuk nama bulan/hari, gunakan `translatedFormat(...)`, misalnya `translatedFormat('d F Y')`, bukan `format('d M Y')` karena menghasilkan bulan Inggris seperti `May`.

## Role dan Halaman

`admin`

- Dashboard: `admin/dashboard`
- Manajemen kelas: `admin/classes`
- Manajemen guru: `admin/gurus`
- Manajemen siswa: `admin/students`
- Registrasi wajah siswa: `admin/face-registration`
- Pengaturan presensi: `admin/attendance-settings`
- Izin/sakit dan presensi manual: `admin/manual-attendance`
- Laporan presensi: `admin/attendance-report`
- Kalender akademik: `admin/academic-calendar`

`guru`

- Beranda: `guru/dashboard`
- Face attendance realtime: `guru/face-attendance`
- Registrasi wajah siswa: `guru/face-registration`
- Izin/sakit dan presensi manual: `guru/manual-attendance`
- Rekap presensi kelas bawaan: `guru/attendance-report`
- API descriptor kelas: `guru/class-descriptors/{classId}`

`siswa`

- Beranda: `siswa/dashboard`
- Registrasi wajah: `siswa/face-registration`
- Pengajuan izin/sakit: `siswa/leave-request`
- Rekap bulan ini: `siswa/attendance-report`

## Model Domain

- `User`: akun login, punya role Spatie, field penting `name`, `username`, `email`, `phone`, `photo`, `is_active`, `password`.
- `Student`: profil siswa, relasi ke `User` dan `SchoolClass`, field `nis`, `gender`, `phone`, `photo`, `address`, `birth_date`.
- `SchoolClass`: tabel fisik `classes`, punya banyak siswa dan satu jadwal.
- `ClassSchedule`: jadwal kelas dengan `start_time` dan `end_time`.
- `FaceDescriptor`: descriptor wajah milik siswa, disimpan sebagai JSON array.
- `AttendanceSetting`: konfigurasi global presensi, dibuat otomatis lewat `current()`.
- `Attendance`: catatan presensi harian siswa, status `hadir`, `terlambat`, `izin`, `sakit`, atau `alpha`, serta approval `pending`, `approved`, atau `rejected`.
- `AcademicHoliday`: kalender akademik/libur sekolah, dengan jenis `national`, `semester`, `school`, `event`, atau `other`; rentang `start_date` sampai `end_date`; dan opsi `allow_attendance`.

## Alur Utama

Login memakai `username` dan `password`, bukan email. Siswa memakai NIS sebagai username dan password default saat dibuat admin. Guru memakai username sebagai password default saat dibuat admin. Admin seed default dibuat oleh `AdminSeeder` dengan `admin@hadirku.com`, username `admin`, password `password`.

Admin membuat kelas, guru, dan siswa. Saat membuat siswa, sistem membuat `User` role `siswa` dan `Student`. Saat membuat guru, sistem membuat `User` role `guru`; akun guru bisa dinonaktifkan dengan `is_active`.

Registrasi wajah dapat dilakukan oleh siswa untuk dirinya sendiri, atau oleh admin/guru dengan memilih kelas dan siswa terlebih dahulu. Setelah kamera aktif, pengguna mengambil descriptor wajah siswa. Maksimum descriptor dikontrol oleh `attendance_settings.max_descriptors`; jika penuh, descriptor tertua dihapus.

Guru membuka Face Attendance, memilih kelas, lalu sistem memuat descriptor kelas dari endpoint `guru/class-descriptors/{classId}`. Browser melakukan matching wajah dengan threshold dari pengaturan. Jika wajah dikenali dan siswa belum punya presensi hari ini, Livewire menyimpan `Attendance`.

Status presensi otomatis:

- `hadir` jika scan sebelum atau sama dengan `late_after`
- `terlambat` jika scan setelah `late_after`
- `alpha` otomatis dibuat oleh command `attendance:auto-alpha` jika kelas sudah lewat `end_time`, alpa otomatis aktif, dan siswa belum punya presensi hari ini
- Pada tanggal libur akademik dengan `allow_attendance = false`, presensi wajah ditolak, pengajuan izin/sakit siswa ditolak, dan alpa otomatis dilewati. Jika `allow_attendance = true`, presensi tetap boleh berjalan pada tanggal libur tersebut.
- Hari sekolah disimpan di `attendance_settings.school_days` sebagai angka ISO hari: 1 Senin sampai 7 Minggu. Defaultnya Senin-Sabtu agar perilaku lama tetap aman.

Siswa dapat mengajukan `izin` atau `sakit`. Pengajuan masuk sebagai `approval_status = pending`. Admin/guru dapat menyetujui atau menolak lewat halaman manual attendance. Jika ditolak, status teknis diubah menjadi `alpha` dengan label UI `alpa`, dan approval menjadi `rejected`. Jika koreksi presensi manual mengubah status menjadi `alpha`/alpa, approval juga harus menjadi `rejected`.

## Batas Arsitektur yang Wajib Dijaga

Project harus tetap:

- Laravel monolith.
- Blade-based.
- Laravel session auth only.
- Tanpa SPA framework.
- Tanpa React.
- Tanpa Vue SPA.
- Tanpa REST auth API.
- Tanpa Sanctum, Passport, atau JWT.
- Tanpa websocket dependency.
- Tanpa queue-heavy architecture untuk flow utama.

Saat membuat fitur baru, gunakan Eloquent relationship, eager loading, validation, transaksi untuk operasi kritikal, dan indeks database untuk query yang sering dipakai.

## Aturan Face Recognition

- Model `face-api.js` disimpan lokal di `public/models`.
- Model wajib: `tiny_face_detector`, `face_landmark_68`, dan `face_recognition`.
- Recognition berjalan sepenuhnya di browser.
- Backend hanya menyimpan descriptor dan data presensi.
- Descriptor tersimpan di tabel `face_descriptors`, kolom `descriptor` bertipe JSON.
- Setiap siswa minimal 3 descriptor dan maksimal 10 descriptor.
- Strategi descriptor FIFO: jika melebihi limit, hapus descriptor tertua otomatis.
- Admin/guru boleh membantu registrasi wajah siswa dari menu Registrasi Wajah dengan memilih target siswa.
- Siswa hanya boleh registrasi wajah untuk akun siswanya sendiri.
- Scan guru harus memuat descriptor kelas terpilih saja.
- Optimasi scan wajib dijaga: cooldown, anti double scan, webcam resolution ringan, tidak load model berulang, dan loop recognition tidak boros.

## Aturan UI

- UI tetap memakai Blade, Tailwind, Alpine.js, dan Livewire.
- Style utama: modern, clean spacing, `rounded-2xl`, responsive, dark/light mode.
- Bahasa antarmuka wajib memakai bahasa Indonesia yang baik dan benar sesuai KBBI. Untuk status tidak hadir tanpa keterangan, label pengguna adalah `alpa`; nilai teknis database tetap `alpha`.
- Konsistensi antar halaman wajib dijaga. Sebelum mengubah UI halaman baru, cek halaman sejenis yang sudah final dan ikuti pola visualnya.
- Summary/stat card kanonik mengikuti pola `resources/views/livewire/admin/guru-management.blade.php`: posisinya section terpisah di atas card utama halaman, wrapper `section` memakai `grid grid-cols-2 gap-2 sm:grid-cols-4 sm:gap-4`, kartu memakai `hk-card p-3 sm:p-5`, isi memakai `grid min-h-16 grid-cols-[1fr_auto] items-center gap-2 sm:min-h-20 sm:gap-4`, label `text-[10px] font-bold uppercase tracking-wide ... sm:text-sm`, angka `mt-1 text-2xl font-extrabold ... sm:mt-2 sm:text-3xl`, dan ikon kanan `h-9 w-9 ... sm:h-12 sm:w-12`.
- Dashboard boleh punya hero/section pembuka sendiri, tetapi summary/stat card dashboard tetap mengikuti pola compact yang sama agar tidak terlalu panjang di mobile.
- Untuk tabel dengan banyak kolom, mobile tidak boleh bergantung pada horizontal scroll sebagai pengalaman utama. Gunakan card list khusus mobile (`md:hidden`) yang merangkum data penting secara vertikal, lalu pertahankan tabel desktop mulai breakpoint `md` (`hidden md:block` atau setara).
- Untuk fitur tambah/edit data yang mirip halaman lain, gunakan referensi halaman yang sudah final. Jika pola terbaru memakai tombol lalu modal, jangan tampilkan form permanen di halaman utama.
- Wajib mendukung Android phone, tablet, dan laptop.
- Sidebar harus responsive: drawer, hamburger menu, dan overlay pada mobile.
- Notifikasi memakai SweetAlert2 toast melalui event global `success` dan `error`.
- Hindari pola lama session flash alert block untuk fitur baru.

## Data Awal

Seeder utama `DatabaseSeeder` memanggil:

- `RoleSeeder`: membuat role `admin`, `guru`, `siswa`
- `AdminSeeder`: membuat akun admin default
- `ClassSeeder`: membuat `Kelas 1` sampai `Kelas 6`
- `AttendanceSettingSeeder`: membuat konfigurasi presensi default

Pengaturan default:

- face match threshold: `0.5`
- scan interval: `1000` ms
- mulai presensi: `06:30:00`
- terlambat setelah: `07:00:00`
- max descriptor: `10`
- auto alpha: aktif

## Catatan Penting

- Project ini bukan git repository pada saat dibaca, jadi tidak ada history lokal untuk dijadikan referensi.
- README sudah diganti menjadi dokumentasi HadirKu dan `INSTALL.md` sudah ditambahkan sebagai panduan instalasi cPanel/shared hosting.
- Route `register` dari Breeze masih aktif, tetapi controller register bawaan belum mengisi `username`; karena tabel `users.username` wajib dan unique, registrasi publik kemungkinan gagal atau tidak sesuai alur aplikasi.
- `face-api.js` sudah dibundel lokal di `public/vendor/face-api/face-api.min.js` dan dimuat local-first dari layout utama, dengan fallback ke CDN. Fitur kamera tetap butuh browser dengan izin kamera dan biasanya perlu HTTPS atau localhost.
- File foto siswa/guru disimpan di disk `public`, misalnya `student-photos` dan `guru-photos`.
- Schedule di `routes/console.php` menjalankan `attendance:auto-alpha` setiap menit, tetapi scheduler Laravel harus aktif di environment runtime.

## Progress Peninjauan UI

Peninjauan berjalan halaman demi halaman dari admin, guru, lalu siswa.

- Admin Registrasi Wajah sudah direview dan diperbaiki:
  - UX unggah foto menampilkan status proses yang lebih jelas.
  - Setelah descriptor tersimpan dari unggah foto, mode tetap di unggah foto dan tidak otomatis berpindah ke kamera.
  - Kamera memiliki overlay kotak deteksi wajah.
  - Area kamera memakai rasio potret pada mobile dan tombol flip kamera di dalam area video. Opsi teks `Depan`/`Belakang` tidak ditampilkan lagi pada Registrasi Wajah agar konsisten dengan Presensi Wajah.
  - Registrasi Wajah mobile disamakan dengan Presensi Wajah: kotak video memakai empty state `Kamera belum aktif`, tombol flip kamera berada di pojok kanan bawah video, dan tombol flip baru aktif setelah kamera menyala. Mobile memakai `object-cover` pada video agar kamera depan/belakang tampil potret di dalam frame, sementara browser diminta memberi stream potret 9:16. Kamera depan dimirror seperti kamera selfie; overlay kotak deteksi ikut dihitung balik agar tetap sejajar.
  - Tombol ambil wajah memakai feedback cepat, loading, dan reuse deteksi terakhir agar jeda terasa lebih natural.
  - `face-api.js` dibundel lokal dengan fallback CDN.
- Admin Presensi Wajah sudah ditambahkan:
  - Admin memiliki menu `Presensi` di sidebar tepat di bawah `Dashboard`.
  - Route admin memakai `/admin/face-attendance` dengan nama `admin.face.attendance`.
  - Admin dapat memilih kelas dan melakukan scan wajah siswa menggunakan alur presensi wajah yang sama dengan guru.
  - Descriptor kelas untuk admin dimuat dari endpoint `/admin/class-descriptors/{classId}`.
  - Halaman Presensi Wajah direview dan dirapikan: struktur mengikuti pola admin, kontrol kelas dipisah di card atas sebagai blok `Kelas Pemindaian` dengan kelas aktif dan tombol `Ganti Kelas`/`Pilih Kelas`, area kamera punya empty state, tombol memakai `Mulai Pemindaian`/`Hentikan Pemindaian`, status memakai Bahasa Indonesia, tombol mulai/berhenti saling aktif sesuai status pemindaian, daftar `Presensi Terbaru` menampilkan status, dan indikator kesiapan kelas menampilkan jumlah siswa, siswa dengan data wajah, siswa siap dipindai, serta total descriptor.
  - Success sound presensi memakai `public/sounds/success.mp3`; sound gagal memakai `public/sounds/error.mp3`.
  - Area kamera Presensi Wajah memakai `wire:ignore` agar stream kamera tidak putus ketika Livewire me-render ulang komponen setelah respons presensi, misalnya saat siswa sudah memiliki presensi hari ini.
  - Area kamera Presensi Wajah memakai rasio potret pada mobile dan tetap lanskap pada desktop. Pada mobile, kamera memakai satu tombol ikon `balik kamera` di pojok kanan bawah area video. Tombol flip baru aktif setelah kamera aktif, lalu dapat mengganti stream dari kamera depan ke belakang atau sebaliknya. Mobile memakai `object-cover` pada video agar kamera depan/belakang tampil potret di dalam frame, sementara browser diminta memberi stream potret 9:16. Kamera depan dimirror seperti kamera selfie; overlay kotak deteksi ikut dihitung balik agar tetap sejajar. Desktop tidak menampilkan opsi kamera karena umumnya memakai satu webcam/default browser.
  - Pada mobile, kontrol `Mulai Pemindaian` dan `Hentikan Pemindaian` digabung menjadi satu tombol toggle. Setelah ditekan untuk mulai, teks langsung berubah menjadi `Hentikan Pemindaian` tetapi tombol tetap nonaktif sampai kamera aktif; setelah pemindaian aktif, tombol bisa dipakai untuk menghentikan pemindaian.
  - Jika presensi hari ini ditutup karena Kalender Akademik (`Presensi Tutup`) atau karena bukan `Hari Sekolah`, halaman Presensi Wajah menampilkan banner status sejak awal, tombol `Mulai Pemindaian` berubah menjadi `Presensi Ditutup`, dan kamera tidak perlu dinyalakan.
  - Jika Kalender Akademik membuka presensi sebagai pengecualian (`Presensi Buka`), halaman menampilkan banner info bahwa presensi dibuka untuk kegiatan sekolah dan pemindaian tetap aktif.
- Admin Izin/Sakit sudah direview dan diperbaiki:
  - Input Izin/Sakit manual pindah ke modal.
  - Edit presensi manual memakai modal yang sama.
  - Header, filter, tabel, dan action dirapikan agar tidak terlalu lebar di desktop.
  - Kolom tabel disederhanakan menjadi informasi yang lebih ringkas.
  - Mobile memakai card list khusus agar user tidak perlu scroll kanan/kiri untuk membaca data.
  - Filter mobile memakai pola pencarian + ikon filter yang membuka bottom sheet preferensi. Tombol `Terapkan` menerapkan filter, sedangkan `Atur Ulang` mengembalikan preferensi ke default `Menunggu`. Karena default halaman memakai persetujuan `Menunggu`, badge indikator filter mobile menghitungnya sebagai 1 filter aktif.
  - Summary card `Total Filter`, `Menunggu`, `Disetujui`, `Ditolak` disamakan persis dengan pola summary card `guru-management`.
- Admin Kelola Guru sudah mulai dirapikan:
  - Form tambah guru dipindahkan ke modal yang dibuka melalui tombol `Tambah Guru`.
  - Halaman utama fokus pada ringkasan, filter, dan daftar guru.
  - Istilah tampilan dirapikan, misalnya `Kelas bawaan`, `Atur ulang kata sandi`, dan `Pratinjau`.
- Admin Kelola Siswa sudah mulai dirapikan:
  - Form tambah siswa dipindahkan ke modal yang dibuka melalui tombol `Tambah Siswa`.
  - Modal impor siswa tetap terpisah dan dibuka melalui tombol `Impor`.
  - Halaman utama fokus pada filter, ekspor, dan daftar siswa.
  - Istilah tampilan dirapikan, misalnya `Atur ulang kata sandi` dan `Pratinjau`.
- Admin Rekap sudah mulai dirapikan:
  - Judul halaman memakai `Rekap Presensi`.
  - Summary card diposisikan sebagai section terpisah di atas card utama dan mengikuti pola `guru-management`.
  - Filter desktop dibuat lega dengan pencarian pada baris sendiri, filter kelas/status/persetujuan pada baris berikutnya, grup tombol cepat `Periode`, dan tombol `Atur Ulang`.
  - Desktop menampilkan chip filter aktif di bawah filter agar pengguna tahu data sedang tersaring apa.
  - Rekap memakai grup tombol cepat `Periode` pada desktop dan mobile. Opsi periode: `Hari Ini`, `7 Hari`, `Bulan Ini`, dan `Kustom`; tidak ada opsi semua tanggal agar query default tidak terlalu berat. Default periode adalah `Bulan Ini`, termasuk saat filter diatur ulang. Input `Tanggal Mulai`/`Tanggal Selesai` hanya tampil saat `Kustom` dipilih.
  - Halaman menampilkan info jumlah hasil, misalnya `3 data ditemukan`.
  - Rentang tanggal divalidasi: tanggal selesai harus sama dengan atau setelah tanggal mulai.
  - Filter mobile memakai pencarian + tombol ikon filter. Ikon membuka bottom sheet preferensi berisi kelas, status, persetujuan, dan grup tombol cepat `Periode`. Perubahan filter mobile baru diterapkan saat tombol `Terapkan` ditekan; `Atur Ulang` mengembalikan preferensi ke periode `Bulan Ini`. Indikator filter menghitung rentang tanggal sebagai satu filter. Bottom sheet harus diletakkan di luar `hk-card`/kontainer blur agar `position: fixed` benar-benar mengikuti viewport.
  - Tombol ekspor digabung menjadi satu tombol `Ekspor` dengan dropdown pilihan `Excel` dan `PDF`. Berkas Excel memakai `rekap-presensi.xlsx`; PDF memakai `rekap-presensi.pdf`.
  - Nama file ekspor Rekap dinamis mengikuti rentang tanggal jika ada, misalnya `rekap-presensi-2026-05-01-sampai-2026-05-29.xlsx`; jika tidak ada rentang, memakai tanggal unduh.
  - Ekspor Excel memakai heading native, auto-size kolom, tanggal/jam terformat, dan label status/persetujuan berbahasa Indonesia.
  - Empty state Rekap menampilkan pesan `Tidak ada presensi untuk filter yang dipilih.` dan tombol `Atur Ulang`.
  - Mobile memakai card list khusus agar user tidak perlu scroll kanan/kiri untuk membaca data presensi. Card mobile Rekap dibuat ringkas: nama, NIS, kelas, tanggal kecil, status, dan ikon chevron. Detail seperti jam, persetujuan, dan keterangan muncul di bottom sheet saat card diklik.
  - Tabel desktop Rekap diringkas menjadi kolom `Siswa`, `Waktu`, `Presensi`, dan `Keterangan`.
  - Di bawah section tabel/paginasi Rekap ada card terpisah `Tren Kehadiran` seperti summary card/section sendiri, bukan nested di card tabel. Grafik berupa SVG responsif tanpa dependency chart tambahan.
  - Card `Tren Kehadiran` punya filter tanggal sendiri yang terpisah dari filter tabel: `7 Hari Terakhir`, `14 Hari Terakhir`, `30 Hari Terakhir`, `Bulan Ini`, serta input tanggal mulai/selesai. Default filter tren adalah `Bulan Ini` agar konsisten dengan Rekap. Grafik menampilkan seri `Hadir`, `Terlambat`, `Izin`, `Sakit`, dan `Tidak Hadir`.
  - Grafik `Tren Kehadiran` bersifat interaktif: saat kursor diarahkan ke area tanggal, tampil tooltip berisi nilai `Hadir`, `Terlambat`, `Izin`, `Sakit`, dan `Tidak Hadir`.
  - Di bawah grafik tren ada dua card ranking terpisah: `Siswa Paling Rajin` dan `Sering Tidak Hadir`. Keduanya mengikuti filter tanggal tren, bukan filter tabel Rekap. Ranking tidak menampilkan siswa dengan nilai 0 hari agar informasi tetap bermakna.
- Admin Kalender Akademik sudah ditambahkan:
  - Admin memiliki menu `Kalender Akademik`, route `/admin/academic-calendar`, dan komponen `App\Livewire\Admin\AcademicCalendar`.
  - Data libur disimpan di tabel `academic_holidays` melalui model `AcademicHoliday`.
  - Admin dapat menambah, mengedit, menghapus, mencari, memfilter jenis, dan memfilter tahun libur.
  - Admin dapat mengimpor hari libur dari CSV/XLS/XLSX melalui modal impor dengan pratinjau valid/tidak valid sebelum simpan.
  - Template impor libur tersedia dari tombol `Unduh Template`. Kolom utama: `nama_libur`, `jenis`, `tanggal_mulai`, `tanggal_selesai`, `presensi`, dan `keterangan`.
  - Jenis libur: `Libur Nasional`, `Libur Semester`, `Libur Sekolah`, `Kegiatan Sekolah`, dan `Lainnya`.
  - Setiap data libur memiliki opsi `Presensi Tutup` atau `Presensi Buka`. Defaultnya `Presensi Tutup`.
  - Sistem menolak rentang tanggal libur yang bertumpang tindih agar kalender tidak ambigu.
  - Presensi wajah dan pengajuan izin/sakit siswa ditolak pada tanggal libur yang menutup presensi. Command `attendance:auto-alpha` juga melewati tanggal tersebut.
  - Data dengan `Presensi Buka`, misalnya kegiatan sekolah pada Sabtu/Minggu, dapat membuka presensi meskipun tanggal tersebut bukan hari sekolah reguler.
  - Rekap Siswa menampilkan penanda `Libur` di kalender bulan berjalan berdasarkan data kalender akademik.
- Admin Pengaturan Presensi sudah mulai dirapikan:
  - Header gradient lama diganti pola halaman konsisten: judul sederhana, subtitle, dan card/section terpisah.
  - `Logo Aplikasi` dan favicon berada dalam section/card `Identitas Aplikasi`, tetapi pengaturannya dipisah sebagai dua blok berurutan: logo di atas, favicon di bawah. Masing-masing punya pratinjau dan tombol pilih/hapus sendiri, lalu disimpan melalui `Simpan Identitas`.
  - Favicon disimpan di `attendance_settings.favicon_path`, diunggah ke disk `public/app-favicons`, dan dipasang di layout admin serta guest melalui tag `<link rel="icon">`.
  - Form utama dibagi menjadi section/card `Pengenalan Wajah` dan `Aturan Presensi`.
  - Bahasa tampilan dibuat Indonesia: `Ambang Kecocokan Wajah`, `Interval Pemindaian`, `Batas Data Wajah`, `Jam Mulai Presensi`, `Batas Terlambat`, dan `Alpa Otomatis`.
  - `Ambang Kecocokan Wajah` memakai slider plus input angka, dengan konteks `Ketat`, `Seimbang`, dan `Longgar`.
  - `Alpa Otomatis` memakai toggle visual, bukan checkbox polos.
  - `Hari Sekolah` ditambahkan di section `Aturan Presensi`. Admin dapat memilih hari aktif, misalnya Senin-Jumat untuk sekolah 5 hari atau Senin-Sabtu untuk sekolah 6 hari.
  - Hari di luar pilihan `Hari Sekolah` tidak membuka presensi wajah, tidak menerima pengajuan izin/sakit siswa, dan tidak dibuat alpa otomatis, kecuali Kalender Akademik mengatur tanggal tersebut sebagai `Presensi Buka`.
  - Ada pratinjau aturan waktu yang menjelaskan jam mulai, batas terlambat, dan status alpa otomatis.
  - Deskripsi singkat umum di halaman Pengaturan Presensi dihapus agar tampilan lebih ringkas, tetapi teks bantuan yang berisi syarat/ketentuan/konsekuensi tetap dipertahankan, misalnya format unggahan logo/favicon, batas nilai, dan dampak pengaturan.
  - Tombol simpan dibedakan: `Simpan Identitas` untuk logo/favicon dan `Simpan Aturan Presensi` untuk aturan presensi.
- Konsistensi copy lintas halaman:
  - Deskripsi singkat umum yang hanya mengulang fungsi halaman/section dihapus dari Rekap, Izin/Sakit, Kelola Siswa, Dashboard Admin, Beranda Guru, Beranda Siswa, Pengajuan Izin/Sakit Siswa, dan Presensi Guru.
  - Teks bantuan yang berisi syarat, ketentuan, konsekuensi, atau instruksi penting tetap dipertahankan, misalnya format unggahan foto/logo/favicon, konsekuensi persetujuan, kelas bawaan guru, dan panduan descriptor.
- Konsistensi modal/pop up:
  - Modal formulir memakai overlay gelap blur, panel `rounded-2xl`, border `slate`, shadow besar, tinggi maksimal `90vh`, dan scroll internal.
  - Header modal memakai pola eyebrow kecil, judul tebal, tombol tutup ikon di kanan, serta border bawah.
  - Modal edit kelas, edit guru, edit siswa, impor siswa, registrasi wajah, dan komponen modal Breeze/profil disamakan dengan pola ini.
  - Bottom sheet mobile untuk filter/detail tetap memakai pola sheet bawah dengan handle, judul ringkas, dan tombol tutup ikon.
  - Header modal tidak memakai deskripsi singkat generik. Informasi penting dipindahkan ke konteks field atau info box di dalam form, misalnya kredensial awal siswa/guru, kolom impor, dan konsekuensi presensi manual.
- Ubah Profil sudah direview dan dirapikan:
  - Header gradient besar diganti judul sederhana agar konsisten dengan halaman operasional lain.
  - Tombol `Kembali` berada sejajar di kanan judul halaman, bukan turun di bawah judul.
  - Tombol `Kembali` diarahkan ke dashboard sesuai role (`admin.dashboard`, `guru.dashboard`, atau `siswa.dashboard`), bukan `url()->previous()`.
  - Label halaman/menu memakai `Ubah Profil`, bukan `Edit Profil`.
  - Halaman utama memakai dua card terpisah: `Informasi Profil` dan `Ubah Kata Sandi`.
  - Fitur hapus akun tidak dipakai di project ini untuk admin, guru, ataupun siswa. Jangan menambahkan tombol/modal/route hapus akun di halaman profil.
  - Deskripsi singkat generik di header card dihapus, sedangkan informasi penting tetap dipertahankan di dekat konteksnya, misalnya format foto profil, pengelolaan nama pengguna, kekuatan kata sandi, dan verifikasi surel.
  - Kontrol foto profil dibuat lebih jelas dengan pratinjau langsung, nama file terpilih, tombol `Pilih Foto`, tombol `Batalkan Pilihan`, dan tombol `Hapus Foto`/`Batal Hapus Foto`.
  - Foto profil menerima JPG, PNG, dan WEBP dengan ukuran maksimal 2 MB.
  - Input file foto profil tidak boleh dinonaktifkan saat submit, karena field file yang disabled bisa tidak ikut terkirim walaupun pratinjau tampil.
  - Saat siswa mengubah atau menghapus foto profil dari halaman Ubah Profil, `users.photo` dan `students.photo` disinkronkan agar navbar dan halaman yang membaca foto siswa tetap konsisten.
  - Form profil dan kata sandi memakai state submit `Menyimpan...` agar tidak mudah terkirim dobel.
  - Form kata sandi menampilkan syarat eksplisit, indikator kekuatan, checklist langsung, dan indikator jika konfirmasi kata sandi belum sama.
  - Aturan kata sandi baru: minimal 8 karakter, berisi huruf, berisi angka, berbeda dari kata sandi saat ini, dan konfirmasi harus sama. Aturan backend dan checklist frontend harus dijaga tetap selaras.
  - Status sukses profil/kata sandi memakai toast global `hadirku-toast`, bukan badge inline atau teks polos.
- Login sudah mulai direview dan dirapikan:
  - Halaman login memakai bahasa Indonesia: label `Nama Pengguna atau NIS`, placeholder `Masukkan nama pengguna atau NIS`, `Kata Sandi`, dan tombol `Masuk`.
  - Form login memakai state submit `Masuk...` dan tombol submit dinonaktifkan sementara agar tidak mudah terkirim dobel.
  - Logo aplikasi tampil di dalam card login pada desktop dan mobile, dengan ukuran logo lebih besar agar padding background warna lebih tipis dan logo lebih jelas.
  - Teks bantuan login ringkas: `Masuk menggunakan NIS atau nama pengguna.`
  - Desktop login memakai satu section besar; area brand ada di kiri dan card masuk berada di kanan di dalam section yang sama.
  - Panel kiri desktop tidak lagi memakai statistik teknis seperti jumlah role, descriptor, atau realtime scan, dan tidak menampilkan daftar poin fitur.
  - Pesan gagal login memakai Bahasa Indonesia langsung: `Nama pengguna/NIS atau kata sandi tidak sesuai.`
  - Validasi kosong login memakai pesan khusus: `Nama pengguna atau NIS wajib diisi.` dan `Kata sandi wajib diisi.`
  - Guru nonaktif tetap tidak boleh login dan pesan error ditampilkan lebih terlihat melalui alert di atas form.
  - Registrasi publik tetap tidak tersedia; akun guru dan siswa dibuat oleh admin.
- Guru Beranda sudah direview dan dirapikan:
  - Header hero gradient lama dihapus. Halaman beranda guru memakai header sederhana dengan judul `Beranda` dan jam realtime.
  - Summary card compact mengikuti pola `guru-management`: `Kelas Bawaan`, `Presensi Hari Ini`, `Menunggu`, dan `Siap Dipindai`.
  - Data beranda guru dibatasi ke kelas bawaan guru. Jika kelas bawaan belum diatur, tampil empty state `Kelas bawaan belum diatur. Hubungi admin agar beranda dapat menampilkan data kelas.`
  - Card aksi utama dibuat konsisten dalam 3 card sejajar desktop dan 1 kolom mobile: `Presensi Wajah`, `Registrasi Wajah`, dan `Izin/Sakit`.
  - Label aksi memakai `Aksi Utama`; label lama `Presensi Utama` tidak dipakai.
  - Card `Tips` dihapus dari beranda guru.
  - Beranda menampilkan card `Aktivitas Terakhir` dan `Pengajuan Menunggu` berdasarkan kelas bawaan guru, masing-masing dengan empty state.
- Navigasi guru mobile memakai bottom navigation, bukan sidebar geser. Urutan menu mobile guru: `Beranda`, `Izin`, `Presensi` sebagai tombol tengah menonjol, `Face ID`, dan `Rekap`. Sidebar guru tetap dipakai di desktop.
- Navigasi siswa mobile memakai bottom navigation, bukan sidebar geser. Urutan menu mobile siswa: `Beranda`, `Izin`, `Face ID`, `Rekap`, dan `Profil`. Sidebar siswa tetap dipakai di desktop. Bottom navigation siswa tidak memakai tombol tengah menonjol agar lima menu tetap seimbang.
- Guru Rekap sudah ditambahkan:
  - Guru memiliki menu `Rekap` di sidebar setelah `Izin/Sakit`, route `/guru/attendance-report`, dan nama route `guru.attendance.report`.
  - Tampilan awal Rekap Guru memakai `default_class_id` sebagai filter kelas bawaan. Guru tetap bisa memilih `Semua Kelas` atau kelas lain dari filter kelas, termasuk di bottom sheet mobile. Tombol `Atur Ulang` mengembalikan filter ke kelas bawaan dan periode `Bulan Ini`.
  - Jika kelas bawaan belum diatur, tampilan awal memakai `Semua Kelas`.
  - Halaman memakai pola ringkas seperti Admin Rekap: summary card `Total Data`, `Hadir`, `Terlambat`, `Alpa`; filter pencarian/kelas/status/persetujuan/periode; default periode `Bulan Ini`; tabel desktop; card mobile; detail mobile; dan tombol ekspor dropdown `Excel`/`PDF`.
  - Grafik tren belum ditambahkan di Rekap Guru agar halaman tetap ringan; bisa dipertimbangkan setelah versi dasar stabil.
- Siswa Beranda sudah direview dan dirapikan:
  - Header hero gradient lama diganti header sederhana dengan judul `Beranda`, identitas siswa/kelas, dan jam realtime.
  - Summary card compact menampilkan `Status Wajah`, `Presensi Hari Ini`, `Pengajuan Aktif`, dan `Riwayat Terakhir`.
  - Status wajah mengikuti jumlah descriptor siswa: `Belum Ada`, `Perlu Ditambah`, atau `Siap Digunakan` jika minimal 3 descriptor terpenuhi.
  - Aksi utama dibuat 2 card: `Registrasi Wajah` dan `Izin/Sakit`. Panduan minimal descriptor dipindah ke card `Registrasi Wajah`, bukan card panduan terpisah.
  - Beranda menampilkan card `Pengajuan Aktif` dan `Riwayat Presensi` masing-masing maksimal 3 data agar ringan di mobile.
  - Mobile siswa memakai bottom navigation 5 menu: `Beranda`, `Izin`, `Face ID`, `Rekap`, dan `Profil`; tombol hamburger disembunyikan untuk siswa.
- Siswa Rekap sudah ditambahkan:
  - Siswa memiliki menu `Rekap` di sidebar, route `/siswa/attendance-report`, nama route `siswa.attendance.report`, dan komponen `App\Livewire\Siswa\AttendanceReport`.
  - Data dibatasi hanya presensi milik siswa login pada bulan berjalan. Tidak ada filter tanggal bebas dan tidak ada ekspor.
  - Halaman memakai summary card `Hadir`, `Terlambat`, `Izin/Sakit`, dan `Alpa`; summary `Total Data` tidak dipakai agar tampilan siswa lebih ringkas.
  - Daftar utama berupa card list dengan filter status kecil (`Semua`, `Hadir`, `Terlambat`, `Izin`, `Sakit`, `Alpa`).
  - Di bawah daftar ada card `Kalender Bulan Ini` berupa kalender mini dengan warna status per tanggal, penanda `Libur` dari Kalender Akademik, dan penanda `Bukan Hari Sekolah` dari pengaturan hari sekolah. Grafik tidak dipakai di Rekap Siswa karena data per siswa lebih cocok dibaca sebagai kalender/list.
  - Beranda Siswa memiliki tautan `Lihat Rekap` pada card `Riwayat Presensi`.
- Summary card pada Registrasi Wajah juga sudah disamakan dengan pola `guru-management` dan diposisikan sebagai section terpisah di atas card utama.
- Summary card Dashboard Admin sudah disamakan dengan pola compact `guru-management`: 2 kolom di mobile dan 4 kolom pada layar lebih besar.
