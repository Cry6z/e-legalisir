# E-Legalisir Universitas Muhammadiyah Bengkulu

![Laravel 12](https://img.shields.io/badge/Laravel-12.x-ff2d20?logo=laravel&logoColor=white)
![PHP 8.3](https://img.shields.io/badge/PHP-8.3-777bb4?logo=php&logoColor=white)
![Livewire](https://img.shields.io/badge/Livewire-Volt%20%2B%20Flux-9333ea?logo=livewire&logoColor=white)

Platform internal untuk mengelola pengajuan legalisir ijazah & transkrip alumni Universitas Muhammadiyah Bengkulu. Sistem ini dibangun di atas Laravel 12, Livewire Flux, dan Fortify untuk menghadirkan alur self-service bagi alumni serta workflow verifikasi multi-level untuk staf fakultas, dekan, dan superadmin.

---

## Daftar Isi

1. [Ringkasan Proyek](#ringkasan-proyek)
2. [Fitur Sorotan](#fitur-sorotan)
3. [Arsitektur & Teknologi](#arsitektur--teknologi)
4. [Prasyarat Sistem](#prasyarat-sistem)
5. [Struktur Proyek](#struktur-proyek)
6. [Instalasi & Setup](#instalasi--setup)
7. [Konfigurasi Lingkungan](#konfigurasi-lingkungan)
8. [Menjalankan Aplikasi](#menjalankan-aplikasi)
9. [Alur Bisnis & Modul](#alur-bisnis--modul)
10. [Skema Basis Data Inti](#skema-basis-data-inti)
11. [Testing & QA](#testing--qa)
12. [Checklist Deployment](#checklist-deployment)
13. [Troubleshooting Cepat](#troubleshooting-cepat)
14. [Kontribusi](#kontribusi)
15. [Lisensi](#lisensi)

---

## Ringkasan Proyek

- **Nama**: E-Legalisir UMB
- **Tujuan**: Digitalisasi proses legalisir dokumen akademik agar transparan, terukur, dan terdokumentasi.
- **Pengguna utama**:
  - Alumni (pemohon)
  - Staf fakultas (validator)
  - Dekan (approver)
  - Superadmin BAAK (monitoring & manajemen peran)
- **Paradigma**: Single-page dashboard berbasis Livewire dengan state reaktif dan dukungan antrean (queue) untuk proses async seperti email OTP.

### Sasaran Bisnis

1. Mempercepat penerbitan legalisir dengan menghapus formulir manual.
2. Memberikan pelacakan status real-time bagi alumni.
3. Menyediakan audit trail lengkap bagi fakultas dan pimpinan.
4. Menstandarkan komunikasi biaya, pengiriman, dan bukti digital.

## Fitur Sorotan

| Kategori | Deskripsi |
| --- | --- |
| Registrasi OTP | Alumni mendaftar menggunakan OTP email berdurasi 10 menit dengan limit percobaan untuk mencegah penyalahgunaan. |
| Workflow Pengajuan | Pengajuan melewati status **Diajukan → Diverifikasi Staf → Disetujui / Ditolak → Selesai** dengan cap waktu dan petugas di setiap tahap. |
| Dashboard Role-Based | Livewire component terpisah untuk alumni, staf, dekan, dan superadmin; setiap peran hanya melihat aksi yang relevan. |
| Manajemen Biaya | Staf dapat mengisi rincian biaya (legalisir, fotokopi, ongkir) beserta catatan pembayaran dan bukti transfer. |
| Pengiriman & Dokumen Digital | Sistem menyimpan PDF bertanda tangan, nomor resi, dan bukti pengiriman untuk memo tracking. |
| Pengaturan Pengguna | Profil, kata sandi, preferensi tampilan, dan 2FA dikelola via Fortify & komponen Livewire khusus. |

## Arsitektur & Teknologi

| Lapisan | Teknologi |
| --- | --- |
| Backend | PHP 8.2, Laravel 12, Laravel Fortify (auth & security), Livewire Flux (komponen reaktif), Queue driver database. |
| Frontend | Vite, Tailwind CSS 4, Flux UI components, Axios untuk permintaan HTTP. |
| Tooling | Composer scripts (`setup`, `dev`, `test`), npm scripts (`dev`, `build`), PestPHP, Laravel Pint, Laravel Pail. |
| Infrastruktur | Default SQLite untuk lokal, dapat dialihkan ke MySQL/PostgreSQL; dukungan Redis opsional untuk cache/queue. |

## Prasyarat Sistem

- PHP **>= 8.2** dengan ekstensi standar Laravel.
- Composer **>= 2.5**.
- Node.js **>= 20** dan npm.
- Driver database (SQLite bawaan, atau MySQL/PostgreSQL).
- Supervisor atau Laravel Horizon (opsional) untuk queue di produksi.

## Struktur Proyek

```
app/
  Livewire/           # Komponen dashboard & settings per role
  Models/             # User, Pengajuan, Status, RegistrationOtp, dll.
bootstrap/
config/
database/
  migrations/         # Termasuk tabel pengajuan & OTP pendaftaran
  seeders/
resources/
  views/              # Landing page dan layout Livewire
routes/
  web.php             # Route publik, auth Fortify, dashboard role
```

> Jalur lengkap dapat diperiksa menggunakan perintah `php artisan list` dan `php artisan route:list` setelah instalasi.

## Instalasi & Setup

1. **Clone repository**
   ```bash
   git clone <repo-url>
   cd e-legalisir
   ```
2. **Instal dependensi PHP**
   ```bash
   composer install
   ```
3. **Salin konfigurasi contoh & generate key**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
4. **Konfigurasikan database di `.env`** (lihat tabel variabel di bawah).
5. **Migrasi & seed**
   ```bash
   php artisan migrate --seed
   ```
6. **Instal dependensi frontend dan build**
   ```bash
   npm install
   npm run build
   ```

> Gunakan `composer run setup` untuk menjalankan seluruh langkah otomatis (install PHP deps → setup `.env` → migrate → npm install → build).

## Konfigurasi Lingkungan

| Variabel | Penjelasan |
| --- | --- |
| `APP_NAME`, `APP_URL` | Branding aplikasi dan root URL untuk Artisan/queue. |
| `DB_CONNECTION`, `DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Pengaturan basis data. Default `sqlite`. |
| `QUEUE_CONNECTION` | Default `database`. Ubah ke `redis` jika menggunakan Redis worker. |
| `CACHE_STORE`, `SESSION_DRIVER` | Disarankan `database`/`redis` di produksi agar scalable. |
| `MAIL_*` | Wajib diisi SMTP produksi untuk pengiriman OTP dan notifikasi. |
| `PAYMENT_ACCOUNT_NOTE` | Pesan rekening resmi yang akan muncul di UI. |
| `VITE_APP_NAME` | Digunakan saat build frontend oleh Vite. |

## Menjalankan Aplikasi

### Mode pengembangan terpadu

```bash
composer run dev
```

Script ini men-disable process timeout dan menjalankan:
- `php artisan serve`
- `php artisan queue:listen --tries=1`
- `npm run dev`

### Mode manual

```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

### Storage & asset publik

- Jalankan `php artisan storage:link` sebelum mengunggah berkas.
- Untuk bundel produksi jalankan `npm run build` dan `php artisan config:cache route:cache view:cache`.

### Background worker

- Pastikan listener queue aktif (Supervisor/Horizon) agar pembaruan status, pengiriman email OTP, dan proses lain tidak menumpuk.
- Gunakan `php artisan queue:work --tries=1` pada lingkungan produksi.

## Alur Bisnis & Modul

### 1. Registrasi OTP Alumni

1. Alumni mengisi nama, email, dan kata sandi.
2. Sistem membuat entri `registration_otps` berisi OTP 6 digit, kata sandi terenkripsi, token sesi, serta TTL 10 menit.
3. Email OTP dikirim; alumni memasukkan kode dan sistem memvalidasi limit percobaan.
4. Setelah berhasil, user dibuat dengan role `alumni`, email diverifikasi, dan sesi OTP dihapus.

### 2. Siklus Pengajuan Legalisir

1. **Diajukan** – Alumni mengunggah dokumen, menulis catatan, dan menentukan jumlah eksemplar.
2. **Diverifikasi Staf** – Staf memeriksa kelengkapan, menambahkan biaya, dan menyimpan bukti pembayaran.
3. **Disetujui / Ditolak oleh Dekan** – Dekan memberi keputusan final (status `DISETUJUI` atau `DITOLAK`).
4. **Selesai** – Staf mengunggah PDF bertanda tangan, menginput nomor resi, menandai tanggal pengiriman.

Setiap status menyimpan cap waktu (`validated_at`, `approved_at`, `shipping_sent_at`) dan petugas terkait untuk audit.

### 3. Role & Dashboard

- **Alumni**: Form pengajuan, riwayat status, unggah ulang dokumen.
- **Staf / Dekan**: Antarmuka tabular untuk pengajuan aktif vs riwayat, pencarian berdasarkan kode/nama, serta ringkasan status.
- **Superadmin**: Statistik agregat pengguna dan pengajuan, serta modul manajemen role melalui Livewire.

### 4. Biaya, Pembayaran, & Pengiriman

- Field biaya meliputi `legalisir_fee`, `photocopy_fee`, `shipping_fee`, `total_fee`, plus catatan pembayaran.
- Sistem menyimpan bukti (`payment_proof_path`), nomor resi, serta file resi (opsional).
- Catatan rekening resmi dapat disesuaikan lewat `PAYMENT_ACCOUNT_NOTE`.

### 5. Notifikasi & Keamanan

- Email OTP menggunakan mailer Laravel dengan antrean.
- Fortify menyediakan pengelolaan profil, kata sandi, appearance, dan Two-Factor Authentication (opsional).

## Skema Basis Data Inti

| Tabel | Deskripsi Singkat |
| --- | --- |
| `users` | Menyimpan akun dengan kolom `role` (`alumni`, `staf`, `dekan`, `superadmin`). |
| `registration_otps` | Buffer pendaftaran OTP: nama, email, password terenkripsi, kode OTP, TTL, dan token sesi unik. |
| `statuses` | Master status pengajuan beserta urutan dan flag `is_final`. |
| `pengajuan` | Data inti permohonan (dokumen, biaya, bukti bayar, validator/approver, resi, token verifikasi). |
| `personal_access_tokens`, `password_reset_tokens`, dll. | Standar Laravel untuk autentikasi & reset. |

Seeder default menambahkan lima status dasar serta empat akun uji (alumni, staf, dekan, superadmin) dengan sandi `password`.

## Testing & QA

- Jalankan seluruh test unit/feature menggunakan Pest:
  ```bash
  composer run test
  # atau
  php artisan test
  ```
- Script test akan otomatis membersihkan cache konfigurasi sebelum dijalankan (`php artisan config:clear --ansi`).
- Gunakan SQLite in-memory atau database terpisah untuk pipeline CI.

## Checklist Deployment

1. **Konfigurasi**: pastikan `.env` sudah berisi `APP_KEY`, `APP_URL`, kredensial database, dan SMTP produksi.
2. **Migrasi**: jalankan `php artisan migrate --force`.
3. **Build Frontend**: `npm ci && npm run build`.
4. **Cache**: `php artisan config:cache route:cache view:cache`.
5. **Storage**: `php artisan storage:link` dan pastikan direktori `storage/` memiliki izin tulis.
6. **Queue Worker**: daftarkan service Supervisor/Horizon untuk `queue:work`.
7. **Monitoring**: awasi log di `storage/logs/laravel.log` dan metrik antrean.

## Troubleshooting Cepat

| Gejala | Solusi |
| --- | --- |
| Email OTP tidak masuk | Periksa kredensial `MAIL_*`, jalankan queue worker, dan cek log mail. |
| Pengajuan tidak berpindah status | Pastikan listener queue aktif dan tabel `statuses` sudah ter-seed. |
| Berkas tidak muncul di publik | Jalankan `php artisan storage:link` dan cek permission folder `storage/app`. |
| Build Tailwind gagal | Pastikan Node.js minimal versi 20 dan install ulang `node_modules`. |
| Error 500 setelah deploy | Jalankan `php artisan config:clear` kemudian `php artisan config:cache` untuk memuat ulang env baru. |

## Kontribusi

1. Fork repository & buat branch fitur.
2. Jalankan `./vendor/bin/pint` dan tambahkan test jika menambah logika.
3. Lengkapi deskripsi PR dengan alur fitur dan langkah uji.
4. Pastikan migration baru disertai dokumentasi singkat di README/CHANGELOG.

## Lisensi

Proyek ini menggunakan lisensi **MIT** sebagaimana tercantum pada `composer.json`. Anda bebas menggunakan, memodifikasi, dan mendistribusikan dengan tetap menyertakan atribusi.
