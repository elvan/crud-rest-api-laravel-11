<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

# CRUD REST API - Laravel 11

## Tentang Proyek
CRUD REST API yang diimplementasikan menggunakan Laravel 11 dan MySQL. API ini menyediakan operasi CRUD untuk manajemen pengguna dan endpoint pencarian data dari sumber eksternal.

## Fitur
- Autentikasi JWT
- Manajemen pengguna (CRUD)
- Pencarian data berdasarkan:
  - NAMA = Turner Mia
  - NIM = 9352078461
  - YMD = 20230405
- Dokumentasi API dengan Swagger

## Persyaratan Sistem
- PHP 8.2 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- Composer
- Node.js dan npm (untuk pengembangan frontend)

## Cara Mulai Cepat

### 1. Instalasi

```bash
# Clone repository (jika menggunakan git)
git clone [URL_REPOSITORY]
cd crud-rest-api-laravel-11

# Instal dependensi PHP
composer install

# Instal dependensi JavaScript (jika ada)
npm install
```

### 2. Konfigurasi Lingkungan
Salin file `.env.example` menjadi `.env` dan sesuaikan pengaturan berikut:

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=crud_rest_api_laravel
DB_USERNAME=root
DB_PASSWORD=password

JWT_SECRET=rahasia_jwt_anda
JWT_TTL=60
```

Setelah menyesuaikan pengaturan, jalankan perintah untuk menghasilkan kunci aplikasi:

```bash
php artisan key:generate
```

### 3. Migrasi Database
Jalankan migrasi untuk membuat struktur database:

```bash
php artisan migrate
```

Untuk menambahkan data awal (seeder):
```bash
php artisan db:seed
```

### 4. Menjalankan Aplikasi
Untuk pengembangan lokal, gunakan:

```bash
php artisan serve
```

Aplikasi akan berjalan di `http://localhost:8000` secara default.

### 5. Akses Dokumentasi API
Setelah aplikasi berjalan, Anda dapat mengakses dokumentasi Swagger di:
```
http://localhost:8000/api/docs
```

## Struktur Proyek
```
.
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   └── API/        # Controller API
│   │   ├── Middleware/    # Middleware autentikasi
│   │   └── Requests/      # Form request untuk validasi
│   ├── Models/            # Model Eloquent
│   └── Services/          # Service layer (opsional)
├── database/
│   ├── migrations/       # File migrasi
│   └── seeders/          # Seeder database
├── routes/
│   └── api.php           # Definisi rute API
├── storage/
│   └── api-docs/          # Dokumentasi Swagger
├── resources/
│   └── views/
│       └── swagger/        # Tampilan Swagger UI
├── .env                   # Konfigurasi lingkungan
├── composer.json         # Dependensi Composer
└── README.md             # Dokumentasi proyek ini
```

## Endpoint API

### Autentikasi
- `POST /api/auth/login` - Login pengguna
- `GET /api/auth/me` - Mendapatkan profil pengguna yang sedang login
- `POST /api/auth/logout` - Logout pengguna

### Pengguna
- `GET /api/users` - Mendapatkan semua pengguna
- `GET /api/users/{id}` - Mendapatkan pengguna berdasarkan ID
- `POST /api/users` - Membuat pengguna baru
- `PUT /api/users/{id}` - Memperbarui pengguna
- `DELETE /api/users/{id}` - Menghapus pengguna

### Pencarian
- `GET /api/search/name` - Mencari data berdasarkan nama (Turner Mia)
- `GET /api/search/nim` - Mencari data berdasarkan NIM (9352078461)
- `GET /api/search/ymd` - Mencari data berdasarkan YMD (20230405)

## Lisensi
MIT
