<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Setup Lokal

1. Buat database MySQL `aldi-motor` di Laragon/XAMPP, lalu sesuaikan `DB_*` di `.env`.
2. Gunakan `APP_URL=http://localhost/Tulus-UTY/public` jika project diakses melalui
   document root Apache seperti struktur ini.
3. Jalankan `composer install`, `php artisan key:generate`, dan `php artisan migrate`.
4. Jalankan `php artisan serve` atau buka URL Apache di atas.

Aplikasi menggunakan zona waktu `Asia/Jakarta`, locale aplikasi `id`, dan locale Carbon
Indonesia. Autentikasi Blade dibuat dengan `laravel/ui` 4.6.3 (`php artisan ui bootstrap
--auth`), termasuk Register, Login, Logout, reset password, dan checkbox Remember Me.
`laravel/ui` dipilih daripada auth manual karena controller, validasi, middleware, session,
dan route standar sudah teruji; auth manual hanya tepat bila alur autentikasi sangat
berbeda atau ingin menghindari dependency tambahan.

Template dashboard di `public/dist-dashboard` dipakai langsung melalui helper `asset()`.
Vite tidak diperlukan untuk halaman auth/dashboard ini dan Bootstrap tidak dikompilasi ulang.
Chart.js disiapkan melalui CDN di layout utama dan dapat dipakai pada fase dashboard.

## Database Bengkel

Database fase 2 memakai sembilan tabel: `users`, `merk_motor`, `layanan`,
`slot_waktu`, `kendaraan`, `reservasi`, `reservasi_layanan`, `servis`, dan
`whatsapp_logs`. Tabel `reservasi_layanan` adalah pivot reservasi-layanan.
Foreign key dibuat berurutan dan `slot_waktu` memiliki unique gabungan
`tanggal` + `jam_mulai`. Status reservasi default adalah
`menunggu_konfirmasi`.

Seeder menyediakan akun demo berikut (keduanya memakai password `password`):

- Admin: `admin@aldi-motor.test`
- Pelanggan: `pelanggan@aldi-motor.test`

Bangun ulang database dan jalankan seluruh seed dengan:

```bash
php artisan migrate:fresh --seed
```

Seeder dipisah berdasarkan domain dan dipanggil berurutan oleh
`DatabaseSeeder`: `UserSeeder`, `MerkMotorSeeder`, `LayananSeeder`,
`SlotWaktuSeeder`, `KendaraanSeeder`, `ReservasiSeeder`, `ServisSeeder`, dan
`WhatsappLogSeeder`.

Verifikasi relasi melalui Tinker:

```bash
php artisan tinker
>>> $u = App\Models\User::where('email', 'pelanggan@aldi-motor.test')->first();
>>> $u->kendaraan()->with('merk')->get();
>>> $u->reservasi()->with(['slot', 'kendaraan.merk', 'layanan'])->get();
>>> App\Models\Servis::with(['kendaraan', 'mekanik', 'reservasi'])->get();
```

## Auth, Role, dan Pengujian Remember Me

Scaffolding `laravel/ui` yang sudah ada dimodifikasi pada:

- `app/Http/Controllers/Auth/RegisterController.php`: validasi/normalisasi
  `no_hp` dan pemaksaan role `pelanggan`.
- `app/Http/Controllers/Auth/LoginController.php`: Remember Me, penolakan
  akun nonaktif, rate limiting bawaan trait, dan redirect role.
- `resources/views/222/register.blade.php`: input nomor HP.
- `bootstrap/app.php`: alias middleware `aktif` dan `role`.
- `app/Http/Middleware/EnsureUserIsActive.php` dan
  `app/Http/Middleware/RoleMiddleware.php`.
- `app/Policies/ReservasiPolicy.php`: pelanggan hanya dapat melihat dan
  membatalkan reservasinya sendiri.
- `resources/views/errors/403.blade.php`: halaman khusus akses ditolak.

Tujuh skenario uji Remember Me:

1. Login tanpa mencentang Remember Me: tutup browser, buka lagi, sesi harus
   berakhir dan pengguna diarahkan ke `/login`.
2. Login dengan Remember Me: tutup browser, buka lagi, akses `/booking-saya`
   harus tetap login.
3. Login dengan checkbox tidak dicentang: pastikan checkbox tidak terpilih
   pada tampilan awal dan cookie remember tidak dibuat.
4. Login dengan Remember Me lalu hapus cookie sesi saja: refresh harus tetap
   terautentikasi melalui cookie remember.
5. Login dengan Remember Me lalu hapus cookie remember: refresh harus meminta
   login kembali.
6. Login dengan password salah berulang kali: throttle harus menolak percobaan
   setelah batas bawaan `laravel/ui` tercapai.
7. Login Remember Me, ubah `aktif` menjadi `false` di database, lalu refresh
   halaman terlindungi: middleware harus logout paksa, menghapus sesi, dan
   menampilkan pesan akun tidak aktif.

Pembatasan server dan tampilan berbeda: middleware `auth`, `aktif`, `role`, dan
Policy tetap memblokir request walaupun URL atau request dimanipulasi. `@auth`
dan pengecekan role di Blade hanya menyembunyikan menu agar UX sesuai role; itu
bukan pengganti otorisasi server.

## Struktur Utama

- `routes/web.php`: route publik dan route autentikasi.
- `app/Http/Controllers`: controller aplikasi dan controller auth.
- `app/Models`: model Eloquent seperti `User`.
- `resources/views`: template Blade, termasuk `layouts`, `auth`, dan halaman aplikasi.
- `public`: document root dan aset statis dashboard.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
