# Deskripsi Aplikasi
Aplikasi "Academy" adalah sebuah platform berbasis Laravel yang dirancang untuk membantu institusi pendidikan dalam mengelola berbagai aspek operasional sekolah, termasuk manajemen siswa, guru, dan administrasi sekolah. Aplikasi ini akan mempermudah pengelolaan data, pengarsipan, dan interaksi antara stakeholder dalam lingkungan pendidikan.


## Fitur Utama
Aplikasi "Academy" akan memiliki beberapa fitur utama, termasuk:
1. Manajemen Siswa: Mendaftarkan, mengupdate, dan mengarsipkan data siswa.
2. Manajemen Guru: Mengelola data guru, jadwal mengajar, dan informasi pribadi guru.
3. Manajemen Kelas: Membuat dan mengelola kelas, serta menghubungkannya dengan guru dan siswa.
4. Penilaian dan Raport: Mencatat penilaian siswa dan menghasilkan raport secara otomatis.
5. Manajemen Administrasi: Mengelola inventaris, dan administrasi sekolah lainnya.
6. Komunikasi: Memfasilitasi komunikasi antara siswa, guru, dan orang tua melalui pesan dan pemberitahuan.

## Panduan Penggunaan
1. Instal dependensi dengan Composer.
    ```
    composer install
    ```
2. Salin file .env.example menjadi .env dan atur konfigurasi database.
    ```
    cp .env.example .env
    ```
3. Generate kunci aplikasi untuk .env.
    ```
    php artisan key:generate
    ```
4. Migrasi dan seeding basis data.
    ```
    php artisan migrate:fresh --seed
    ```
5. Jalankan server lokal.
    ```
    php artisan serve
    ```
6. Buka aplikasi di browser dengan alamat yang muncul pada cmd setelah menjalankan perintah diatas

# 🚀 LIST DEFAULT ACCOUNT
### 📌 **Admin/root access**
- **Username:** root
- **Password:** admin
### 📌 **Guru/teacher access**
- **Username:** guru
- **Password:** guru
### 📌 **Murid/student access**
- **Username:** siswa
- **Password:** siswa