# PS Finder Samarinda (Laravel)

Aplikasi web pemilihan rental PlayStation di Samarinda: survei penilaian + peringkat metode SAW + peta.
Folder ini berisi file aplikasi saja. Letakkan di atas proyek Laravel yang baru.

## Cara memasang
1. Buat proyek Laravel baru (butuh PHP 8.2+ dan Composer):
   composer create-project laravel/laravel ps-finder
2. Salin seluruh isi folder ini ke dalam folder `ps-finder`, pilih "timpa/replace" jika ditanya
   (yang tertimpa: routes/web.php dan database/seeders/DatabaseSeeder.php).
3. Siapkan database. Laravel terbaru memakai SQLite secara bawaan, jadi tidak perlu mengubah apa pun.
   Untuk MySQL, ubah DB_* di file .env.
4. Jalankan migrasi dan data 5 rental:
   php artisan migrate --seed
5. Jalankan server:
   php artisan serve
   lalu buka http://127.0.0.1:8000

## Uji coba tanpa menunggu responden (opsional)
php artisan db:seed --class=DemoRatingSeeder
Ini mengisi nilai ACAK. Hapus sebelum presentasi: php artisan migrate:fresh --seed

## Struktur
- routes/web.php: rute halaman peringkat, API ranking, dan survei
- app/Services/SawService.php: perhitungan SAW
- app/Http/Controllers: RentalController dan SurveyController
- app/Models: Rental dan Rating
- database/migrations dan seeders: tabel rentals, ratings, dan data 5 rental
- resources/views: layout, home (peta + peringkat), survei (form)
- public/css/app.css dan public/js/home.js: tampilan dan interaksi peta
