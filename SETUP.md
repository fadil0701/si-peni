# Setup SI-MANTIK

## ✅ Status Setup

- [x] Laravel 12.x terinstall
- [x] Filament 4.x terinstall
- [x] Dependencies tambahan terinstall:
  - [x] Maatwebsite/Excel (untuk export laporan)
  - [x] SimpleSoftwareIO/simple-qrcode (untuk QR Code)
- [x] Konfigurasi aplikasi:
  - [x] Nama aplikasi: SI-MANTIK
  - [x] Timezone: Asia/Jakarta
  - [x] Locale: id (Indonesia)
- [x] Struktur folder dibuat

## 📝 Langkah Selanjutnya

### 1. Setup Database
Edit file `.env` dan sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=si_mantik
DB_USERNAME=root
DB_PASSWORD=
```

Kemudian buat database:
```sql
CREATE DATABASE si_mantik CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

### 2. Buat User Admin
```bash
php artisan make:filament-user
```

### 3. Jalankan Migration (setelah membuat migration)
```bash
php artisan migrate
```

### 4. Jalankan Server Development
```bash
php artisan serve
```

Akses admin panel di: `http://localhost:8000/admin`

## 🎨 Customisasi Branding

Untuk menambahkan logo dan favicon:
1. Simpan logo di `public/images/logo.png`
2. Simpan favicon di `public/images/favicon.ico`

Logo dan favicon sudah dikonfigurasi di `app/Providers/Filament/AdminPanelProvider.php`

## 📦 Package yang Terinstall

- **filament/filament** (v4.0.0) - Admin panel
- **maatwebsite/excel** (v3.1.67) - Excel export/import
- **simplesoftwareio/simple-qrcode** (v4.2.0) - QR Code generator

## 🔄 Next Steps

1. Buat migration untuk semua tabel sesuai ERD
2. Buat Models dengan relationships
3. Buat Filament Resources untuk CRUD
4. Implementasi Auto Register Aset
5. Buat Dashboard Widgets
6. Setup Role & Permission
7. Buat Laporan

---

**SI-MANTIK** - Sistem Informasi Manajemen Terintegrasi


