# 🎓 Academy+
> **Pengembangan Sistem Informasi Manajemen Sekolah Berbasis Web dan Mobile Menggunakan Laravel dan Flutter pada Platform Academy+**

---

## 👥 Tim Pengembang

| Nama | NIM | Golongan |
|------|-----|----------|
| Aditya Nusa Syahputra | E31242097 | D |
| Muhammad Amadio Imamsyah | E31242128 | D |
| Adinda Dheanova | E31242071 | D |
| Clarisha Amaris Christy Joy | E31241762 | C |
| Farra Ghaisa Nanda L | E31241361 | C |

---

## 📖 Deskripsi

**Academy+** adalah platform sistem informasi manajemen sekolah yang dirancang untuk membantu institusi pendidikan dalam mengelola berbagai aspek operasional, mulai dari manajemen siswa, guru, kelas, hingga analisis performa siswa berbasis kecerdasan buatan.

Platform ini dibangun di atas **tiga teknologi utama yang saling terintegrasi**:

- **Laravel** — Backend web dengan panel admin, guru, dan siswa
- **Flutter Web** — Antarmuka mobile-first berbasis browser untuk guru dan siswa
- **Flask (Python)** — Engine machine learning untuk analisis pengelompokan siswa (K-Means Clustering)

---

## ✨ Fitur Utama

### 🖥️ Laravel Web

#### Admin
- Manajemen akun pengguna (guru & siswa) tanpa fitur registrasi mandiri
- Kelola data kelas, mata pelajaran, ruang, dan barang/inventaris
- Manajemen jadwal pelajaran dan kalender akademik
- Peminjaman ruang dan barang
- Manajemen tamu dan kerja sama (MoU)
- Buat dan kelola pengumuman untuk semua role
- Menjalankan **Analisis AI K-Means** untuk seluruh siswa sekaligus

#### Guru
- Dashboard dengan fitur analisis AI K-Means
- Prediksi kluster untuk siswa baru (input nilai manual)
- Melihat dan mengelola jadwal mengajar
- Input presensi 
- Melihat dataset siswa dan hasil kluster

#### Siswa
- Dashboard dengan data diri lengkap (NISN, kelas, profil)
- Melihat jadwal pelajaran
- Melihat riwayat presensi
- Menerima pengumuman dari admin 

---

### 📱 Flutter Web

> Flutter Web berjalan di browser. Hanya dapat diakses oleh **Guru** dan **Siswa** 

#### Guru
| Modul | Fitur |
|-------|-------|
| **Dashboard** | Statistik kelas, materi, tugas, dan jumlah siswa |
| **Jadwal** | Jadwal mengajar hari ini per kelas |
| **Absensi** | Input kehadiran siswa (Hadir / Izin / Alpha) per nama |
| **E-Learning** | Kelola mata pelajaran, tambah & edit materi, buat & kelola tugas |
| **Pengumuman** | Menerima pengumuman dari admin |

#### Siswa
| Modul | Fitur |
|-------|-------|
| **Dashboard** | Profil siswa dan pengumuman terbaru |
| **Jadwal** | Jadwal pelajaran mingguan (hari, mapel, jam, ruangan) dengan filter |
| **Absensi** | Riwayat kehadiran, rekap Hadir/Izin/Alpha, persentase kehadiran |
| **E-Learning** | Akses daftar mata pelajaran, buka materi, lihat & kerjakan tugas |

---

### 🤖 AI Machine Learning (Flask Python)

Academy+ mengintegrasikan **K-Means Clustering** untuk pengelompokan otomatis siswa berdasarkan performa akademik.

**Dataset:** [Students Performance in Exams — Kaggle](https://www.kaggle.com/datasets/spscientist/students-performance-in-exams) (1.000+ data)

**Alur kerja:**
```
Guru / Admin
    ↓  Klik "Jalankan Analisis AI"
Laravel
    ↓  POST → JSON data nilai siswa
Flask API
    ↓  Preprocessing + Normalisasi
    ↓  K-Means (k=3, scikit-learn)
    ↓  Labeling kluster
    ↓  JSON response
Laravel
    ↓  Simpan ke koleksi clustering_results
    ↓  Push notifikasi
Dashboard (visualisasi hasil kluster)
```

**Hasil kluster:**

| Kluster | Label | Deskripsi |
|---------|-------|-----------|
| Cluster 0 | 🟢 Siswa Berprestasi | Nilai tinggi di seluruh bidang |
| Cluster 1 | 🟡 Siswa Rata-rata | Nilai cukup, perlu pemantauan |
| Cluster 2 | 🔴 Siswa Butuh Bimbingan | Nilai rendah, perlu perhatian ekstra |

Guru juga dapat melakukan **prediksi kluster untuk siswa baru** dengan memasukkan nilai Matematika, Reading, dan Writing secara manual langsung dari dashboard.

---

## 🛠️ Instalasi & Menjalankan Proyek

### Prasyarat

- PHP >= 8.1
- Composer
- Node.js & NPM
- MongoDB (lokal atau Atlas)
- Python 3.x + pip (untuk Flask AI)
- Flutter SDK (untuk Flutter Web)

---

### 1. Laravel Web

```bash
# Clone repositori
git clone https://github.com/ditftyyy/academy_project.git
cd academy-plus/laravel

# Install dependensi PHP
composer install

# Salin file environment
cp .env.example .env

# Generate kunci aplikasi
php artisan key:generate

# Atur konfigurasi MongoDB di .env
# DB_CONNECTION=mongodb
# DB_HOST=127.0.0.1
# DB_PORT=27017
# DB_DATABASE=academy

# Migrasi dan seeding database
php artisan migrate:fresh --seed

# Jalankan server
php artisan serve
```

Buka aplikasi di browser: `http://127.0.0.1:8000`

---

### 2. Flask AI (Machine Learning)

```bash
cd academy-plus/flask-ai

# Install dependensi Python
pip install -r requirements.txt

# Jalankan Flask server
python app.py
```

Flask berjalan di: `http://127.0.0.1:5000`

---

### 3. Flutter Web

```bash
cd academy-plus/flutter

# Install dependensi Flutter
flutter pub get

# Jalankan di browser
flutter run -d chrome
```

Flutter Web berjalan di: `http://localhost:52084`

---

## 🔑 Akun Default

> Semua akun dikelola oleh Admin. Tidak ada fitur registrasi mandiri untuk guru maupun siswa.

| Role | Username | Password | Platform |
|------|----------|----------|----------|
| **Admin** | `root` | `admin` | Laravel Web saja |
| **Guru** | `guru` | `guru` | Laravel Web + Flutter Web |
| **Siswa** | `siswa` | `siswa` | Laravel Web + Flutter Web |

---

## 📄 Lisensi

Proyek ini dikembangkan sebagai Tugas Akhir mata kuliah Pengembangan Aplikasi Web & Mobile.  
© 2026 Kelompok Academy+ — Program Studi Teknologi Informasi.
