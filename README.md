# ALDI MOTOR

Aplikasi manajemen bengkel motor berbasis Laravel 12. Mencakup autentikasi role
admin/pelanggan, data master bengkel, slot waktu, reservasi, servis, dan log WhatsApp.

## Stack

- PHP 8.2+ (diuji pada PHP 8.5) dan Laravel 12.
- MySQL dengan database `aldi-motor`, zona waktu `Asia/Jakarta`, locale aplikasi `id`.
- `laravel/ui` 4.6 (Bootstrap) untuk autentikasi.
- Template dashboard statis di `public/dist-dashboard`, dimuat langsung melalui helper
  `asset()`.
- Vite/Tailwind tersedia di `package.json`, tetapi halaman auth dan dashboard tidak
  bergantung pada build Vite.

## Setup Lokal

1. Buat database MySQL `aldi-motor`, lalu sesuaikan `DB_*` di `.env`.
2. Gunakan `APP_URL=http://localhost/Tulus-UTY/public` jika project diakses melalui
   document root Apache seperti struktur ini.
3. Jalankan `composer install`, `php artisan key:generate`, dan `php artisan migrate`.
4. Jalankan `php artisan serve` atau buka URL Apache di atas.

Bangun ulang database beserta seluruh data demo dengan:

```bash
php artisan migrate:fresh --seed
```

Seeder dipisah berdasarkan domain dan dipanggil berurutan oleh `DatabaseSeeder`:
`UserSeeder`, `MerkMotorSeeder`, `LayananSeeder`, `SlotWaktuSeeder`, `KendaraanSeeder`,
`ReservasiSeeder`, `ServisSeeder`, dan `WhatsappLogSeeder`.

Akun demo berikut memakai password `password`:

- Admin: `admin@aldi-motor.test`
- Pelanggan: `pelanggan@aldi-motor.test`

## Database Bengkel

Database memakai sepuluh tabel: `users`, `merk_motor`, `layanan`, `slot_waktu`,
`kendaraan`, `reservasi`, `reservasi_layanan`, `servis`, `pengingat_servis`, dan
`whatsapp_logs`. Tabel `reservasi_layanan` adalah pivot reservasi-layanan. Foreign key
dibuat berurutan dan `slot_waktu` memiliki unique gabungan `tanggal` + `jam_mulai`.
Status reservasi default adalah `menunggu_konfirmasi`.

## Autentikasi dan Hak Akses

Autentikasi memakai scaffolding `laravel/ui` (`php artisan ui bootstrap --auth`) yang
disesuaikan pada controller, view, dan middleware. Fitur yang tersedia: register, login,
logout, reset password, konfirmasi password, dan Remember Me.

Setelah login, admin diarahkan ke `/admin` dan pelanggan ke `/booking-saya`. Route `/home`
meneruskan pengguna ke dashboard sesuai role.

Pembatasan akses dilakukan berlapis:

- Middleware `auth`, `aktif`, dan `role` (alias didaftarkan di `bootstrap/app.php`).
- `EnsureUserIsActive` di grup middleware `web` memaksa logout akun nonaktif.
- `RoleMiddleware` membatasi area admin dan pelanggan.
- `ReservasiPolicy` membatasi pelanggan hanya pada reservasinya sendiri.
- `resources/views/errors/403.blade.php` menampilkan halaman akses ditolak.

Pengecekan role di Blade hanya menyembunyikan menu agar UX sesuai role; otorisasi tetap
dilakukan di server. Request yang dimanipulasi tetap diblokir oleh middleware dan Policy.

View autentikasi berada di `resources/views/222` (nama folder sengaja tidak memakai nama
standar). Login dan register ditulis sebagai halaman mandiri, sedangkan halaman password
memakai partial `222/partials/head.blade.php` dan `222/partials/foot.blade.php`.

### Tujuh skenario uji Remember Me

1. Login tanpa mencentang Remember Me: tutup browser, buka lagi, sesi harus berakhir dan
   pengguna diarahkan ke `/login`.
2. Login dengan Remember Me: tutup browser, buka lagi, akses `/booking-saya` harus tetap
   login.
3. Login dengan checkbox tidak dicentang: pastikan checkbox tidak terpilih pada tampilan
   awal dan cookie remember tidak dibuat.
4. Login dengan Remember Me lalu hapus cookie sesi saja: refresh harus tetap terautentikasi
   melalui cookie remember.
5. Login dengan Remember Me lalu hapus cookie remember: refresh harus meminta login kembali.
6. Login dengan password salah berulang kali: throttle harus menolak percobaan setelah
   batas bawaan `laravel/ui` tercapai.
7. Login Remember Me, ubah `aktif` menjadi `false` di database, lalu refresh halaman
   terlindungi: middleware harus logout paksa, menghapus sesi, dan menampilkan pesan akun
   tidak aktif.

## Data Master Admin

Data master hanya dapat diakses melalui middleware `auth` dan `role:admin`. Resource CRUD
Merk Motor menjadi pola dasar: `MerkMotorRequest` memvalidasi input, `MerkMotorController`
menjalankan operasi, dan view Blade berada di `resources/views/admin/merk-motor`. Merk yang
sudah dipakai kendaraan tidak dihapus; aksi hapus otomatis mengubahnya menjadi nonaktif.

Layanan dikelola di `resources/views/admin/layanan` dengan nama, deskripsi, durasi,
interval hari/km, dan status aktif. Tidak ada field harga.

Slot waktu memiliki form pembuatan massal berdasarkan rentang tanggal, jam operasional,
durasi per slot, dan kapasitas. Slot yang memiliki reservasi aktif ditolak saat dihapus.
Kapasitas dan status slot tetap dapat diedit.

Daftar pelanggan mendukung pencarian nama/email/nomor HP, aktivasi/nonaktivasi, dan reset
password. Reset password mengganti password acak serta `remember_token`, sehingga cookie
Remember Me lama langsung tidak berlaku.

Alur contoh CRUD Merk Motor:

1. Admin membuka menu **Merk Motor** pada sidebar.
2. Tombol **Tambah Merk** membuka `GET /admin/merk-motor/create`.
3. Form dikirim ke `POST /admin/merk-motor`.
4. `MerkMotorRequest` memeriksa role admin, nama wajib, dan keunikannya.
5. Controller menyimpan data melalui `MerkMotor::create()`.
6. Controller redirect ke index dengan flash message.
7. Untuk edit, alurnya memakai `GET .../{merk_motor}/edit` lalu `PUT`.
8. Untuk hapus, controller memeriksa relasi `kendaraan`; bila ada, hanya `aktif` yang
   diubah menjadi `false`.

Semua halaman admin memakai aset statis dashboard di `public/dist-dashboard`. Halaman
dashboard dan daftar ditulis mandiri, sedangkan halaman form memakai partial
`resources/views/admin/partials/page-head.blade.php` dan `page-foot.blade.php`, sehingga
tampilan tetap konsisten tanpa layout Blade bersama.

Verifikasi relasi melalui Tinker:

```bash
php artisan tinker
>>> $u = App\Models\User::where('email', 'pelanggan@aldi-motor.test')->first();
>>> $u->kendaraan()->with('merk')->get();
>>> $u->reservasi()->with(['slot', 'kendaraan.merk', 'layanan'])->get();
>>> App\Models\Servis::with(['kendaraan', 'mekanik', 'reservasi'])->get();
```

## Cek Slot Publik, Kendaraan, dan Booking Saya

Halaman publik `GET /cek-slot?tanggal=YYYY-MM-DD` dapat dibuka tanpa login. Setiap slot
menampilkan jam, kapasitas, jumlah terpakai, sisa, dan status:

- `tersedia` (hijau)
- `hampir penuh` (kuning) bila sisa > 0 dan sisa <= setengah kapasitas
- `penuh` (merah) bila sisa 0

Reservasi yang dihitung sebagai pemakai kapasitas adalah `menunggu_konfirmasi`,
`dikonfirmasi`, dan `check_in`. Status `selesai` dan `dibatalkan` tidak dihitung. Slot
yang penuh atau sudah lewat tidak dapat dipilih. Perhitungan dipusatkan di
`App\Services\SlotAvailabilityService`. Pelanggan yang sudah login membuka halaman yang
sama di dalam layout customer sehingga sidebar tetap tersedia; tamu memakai halaman
publik tanpa sidebar.

Pelanggan mengelola kendaraan di `/kendaraan` (index, tambah, edit, hapus). Nomor polisi
dinormalisasi menjadi huruf besar dan spasi tunggal sebelum validasi keunikan. Policy
`KendaraanPolicy` membatasi perubahan hanya untuk pemilik. Kendaraan yang sudah memiliki
riwayat reservasi atau servis tidak dapat dihapus.

Form **Buat Reservasi** di `/booking-saya` memilih kendaraan yang sudah ada atau
menambahkan kendaraan baru langsung di modal, memilih slot, layanan, dan mengisi keluhan.
Layanan tidak memiliki harga; form menampilkan “Biaya menyesuaikan merk dan kondisi motor,
dikonfirmasi bengkel”. Reservasi disimpan dalam `DB::transaction()` dengan
`lockForUpdate()` pada baris slot, lalu dicek kapasitasnya. Status awal adalah
`menunggu_konfirmasi` dan kode booking memakai kolom `reservasi.kode`.

Pelanggan yang memilih slot dari halaman publik diarahkan ke login. Setelah login,
`redirect()->intended()` mengembalikan mereka ke `/booking-saya?slot=...` dengan modal dan
slot terpilih. Halaman **Booking Saya** menampilkan daftar reservasi, progres servis,
detail terpisah, alasan penolakan, dan tombol pembatalan untuk status `menunggu_konfirmasi`
atau `dikonfirmasi`. Dashboard customer juga menampilkan ringkasan reservasi aktif, servis
berlangsung, jumlah kendaraan, jumlah riwayat servis, lima servis terakhir, slot terdekat,
dan kartu kendaraan. Semua angka diambil dari tabel `reservasi`, `servis`, `kendaraan`,
dan `slot_waktu`; tidak ada lagi kartu demo template. Scrollbar sidebar customer
disembunyikan tanpa menghilangkan kemampuan menu untuk digulir di layar pendek.

Pengujian FASE 7:

```bash
php artisan test --filter=PublicSlotTest
php artisan test --filter=KendaraanTest
php artisan test --filter=SlotConcurrencyTest
php artisan test --filter=ReservationFlowTest
```

`SlotConcurrencyTest` menguji dua pelanggan yang memperebutkan slot terakhir secara
berurutan dan memastikan query `lockForUpdate()` benar-benar dijalankan. Uji paralel
sungguhan dengan dua koneksi database belum disertakan.

## Reservasi, Notifikasi Real-Time, dan Log WhatsApp

Pelanggan membuat reservasi dari modal **Buat Reservasi** di `/booking-saya`. Form memilih
kendaraan, slot yang masih tersedia, layanan, dan keluhan. Slot dikunci saat transaksi
dibuat agar dua pelanggan tidak mengambil kapasitas terakhir bersamaan. Reservasi baru
berstatus `menunggu_konfirmasi` dan dapat dibatalkan pelanggan selama berstatus
`menunggu_konfirmasi` atau `dikonfirmasi`.

Admin mengelola reservasi di `/admin/reservasi`: pencarian kode/nama/nomor HP/plat nomor,
filter status dan rentang tanggal, detail reservasi, konfirmasi, penolakan/pembatalan,
serta check in kendaraan. Setiap aksi juga membuat log WhatsApp.

Reservasi berstatus `menunggu_konfirmasi` yang dibatalkan admin dianggap **penolakan**:
alasan wajib diisi, reservasi menjadi `dibatalkan`, dan log `penolakan_reservasi` dibuat.
Pembatalan reservasi yang sudah `dikonfirmasi` memakai alasan opsional dan log
`pembatalan_reservasi`.

### Alur servis

Check in hanya tersedia untuk reservasi berstatus `dikonfirmasi`. Admin mengisi
`odometer_awal`, lalu dalam satu `DB::transaction()` reservasi berubah menjadi `check_in`
dan record `servis` dibuat berstatus `menunggu`. Halaman **Servis** di
`/admin/servis` mendukung pencarian kode/nama/nomor HP/plat nomor, filter status, filter
rentang `tanggal_masuk`, serta form catatan pengerjaan.

Status servis bergerak maju satu langkah setiap kali:

```text
menunggu -> dikerjakan -> selesai -> diambil
```

Transisi yang melompati atau mundur ditolak. Saat menjadi `dikerjakan`, `waktu_mulai`
diisi. Saat menjadi `selesai`, `waktu_selesai` dan `tanggal_selesai` diisi serta log
WhatsApp `servis_selesai` dibuat. Saat menjadi `diambil`, reservasi berubah menjadi
`selesai`, `kendaraan.odometer_terakhir` diperbarui, dan `pengingat_servis` dibuat dari
interval hari/km layanan. Semua perubahan status memakai `DB::transaction()` dan
`lockForUpdate()`.

Form catatan servis hanya memuat `odometer`, `rincian_pekerjaan`, dan `catatan`; tidak ada
input biaya atau sparepart. Catatan dikunci setelah status `diambil`. Pelanggan melihat
progres servis berbentuk timeline di **Booking Saya** dan detail reservasi, serta riwayat
per kendaraan di `/riwayat-servis` yang memuat tanggal, layanan, rincian pekerjaan,
odometer, catatan, dan status.

### Kirim WhatsApp semi-otomatis

Di modal detail reservasi, tombol **Kirim WA** muncul sesuai kondisi:

| Tombol | Muncul saat |
| --- | --- |
| Kirim WA Konfirmasi | reservasi `dikonfirmasi` |
| Kirim WA Penolakan | reservasi `dibatalkan` karena ditolak admin |
| Kirim WA Servis Selesai | `servis.status = selesai` atau `diambil` |
| Kirim WA Pengingat | reservasi `selesai` dan `servis.status = selesai` atau `diambil` |

Saat tombol ditekan, browser mengirim POST ke
`POST /admin/reservasi/{reservasi}/kirim-wa` di tab baru. Controller mencatat log
(`jenis`, `reservasi_id`, `nomor_tujuan`, `pesan`, status `menunggu`), lalu redirect ke
`https://wa.me/{nomor}?text={pesan}`. Pendekatan form POST + `target="_blank"` dipilih
karena tidak terkena popup blocker seperti `window.open()` setelah `fetch()`.

Nomor HP dinormalisasi oleh `App\Support\WhatsappLink`: `08...`, `8...`, `62...`, dan
`+62...` menjadi `62...`. Nomor kosong atau tidak valid tidak menghasilkan log dan
menampilkan pesan error; tombol Kirim WA tidak ditampilkan.

Template yang dipakai: konfirmasi (kode + jadwal), penolakan (alasan + ajakan memilih
jadwal lain), servis selesai (nopol + silakan diambil), dan pengingat servis berkala
(layanan + nopol). Template servis selesai tidak memuat biaya.

Notifikasi real-time memakai Pusher dan event `App\Events\ReservationCreated` pada channel
privat `admin.reservations`. Untuk mengaktifkannya:

1. Isi `PUSHER_APP_ID`, `PUSHER_APP_KEY`, `PUSHER_APP_SECRET`, dan `PUSHER_APP_CLUSTER`
   di `.env`.
2. Ubah `BROADCAST_CONNECTION=pusher`.
3. Jalankan `php artisan config:clear`, lalu muat ulang `/admin/reservasi`.

Selama kredensial Pusher belum diisi, halaman admin memakai polling
`GET /admin/reservasi/notifikasi` setiap 30 detik. Otorisasi channel privat berada di
`routes/channels.php` dan endpoint auth didaftarkan melalui `bootstrap/app.php`.

Log WhatsApp dibuat otomatis untuk reservasi baru, konfirmasi, penolakan, pembatalan, dan
servis selesai. Setiap klik **Kirim WA** menambah satu baris log baru. Karena gateway
WhatsApp belum terhubung, status log tetap `menunggu` (bukan klaim `terkirim`). Log dapat
dilihat pada detail reservasi atau halaman `/admin/whatsapp-log`, yang mendukung filter
jenis, status, pencarian, dan rentang tanggal (`dari`/`sampai` berdasarkan `created_at`).

Pengujian alur reservasi:

```bash
php artisan test --filter=ReservationFlowTest
php artisan test --filter=ServiceFlowTest
php artisan test --filter=WhatsappLinkTest
```

## Struktur Utama

- `routes/web.php`: route publik, autentikasi, admin, dan reservasi.
- `app/Http/Controllers`: controller aplikasi dan controller auth.
- `app/Http/Middleware` dan `app/Policies`: pembatasan akses role dan reservasi.
- `app/Models` dan `app/Enums`: model Eloquent dan enum domain bengkel.
- `resources/views/222`: halaman autentikasi (nama folder sengaja disamarkan).
- `resources/views/admin` dan `resources/views/customer`: halaman mandiri beserta partial.
- `resources/views/errors`: halaman error, termasuk `403`.
- `public`: document root dan aset statis dashboard di `dist-dashboard`.
- `database/migrations` dan `database/seeders`: skema dan data demo per domain.

## Lisensi

Dirilis di bawah [MIT license](https://opensource.org/licenses/MIT).
