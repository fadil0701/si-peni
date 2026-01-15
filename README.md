# SI-MANTIK
## Sistem Informasi Manajemen Terintegrasi

Sistem manajemen aset dan inventory terintegrasi dengan workflow lengkap dari perencanaan hingga pemeliharaan.

## 🚀 Teknologi Stack

- **Backend**: Laravel 12.x
- **Admin Panel**: Filament 4.x
- **Database**: MySQL 8+ / PostgreSQL
- **Frontend**: Alpine.js + Tailwind CSS (built-in Filament)
- **QR Code**: SimpleSoftwareIO/simple-qrcode
- **Excel Export**: Maatwebsite/Excel

## 📋 Fitur Utama

### Master Manajemen
- Unit Kerja, Lokasi, Ruangan, Gudang
- Program, Kegiatan, Sub Kegiatan

### Master Data
- Hierarki Barang (Aset → Kode → Kategori → Jenis → Sub Jenis → Data Barang)
- Satuan, Sumber Anggaran

### Perencanaan
- RKU (Rencana Kebutuhan Unit)
- Pengajuan & Persetujuan Pimpinan
- Rekap Perencanaan Tahunan

### Pengadaan
- Paket Pengadaan, Proses, Kontrak/SP/PO
- Monitoring Pengadaan

### Keuangan & Pembayaran
- Verifikasi Dokumen
- Pembayaran (Uang Muka, Termin, Pelunasan)
- Realisasi Anggaran per Sub Kegiatan

### Inventory
- Stock Gudang, Batch Barang
- Stock Adjustment & History
- **Auto Register Aset** (otomatis membuat register per unit aset)

### Permintaan & Transaksi
- Permintaan Barang (dengan approval)
- Distribusi Barang (SBBK)
- Penerimaan Barang
- Retur & Pemakaian

### Aset & KIR
- Register Aset
- Kartu Inventaris Ruangan (KIR)
- Mutasi Aset

### Pemeliharaan & Kalibrasi
- Permintaan Pemeliharaan (dengan approval)
- Jadwal Maintenance
- Kalibrasi, Service Report

### Laporan & Settings
- Laporan per modul
- User & Role Management
- Hak Akses & Notifikasi

## 🛠️ Instalasi

1. **Clone repository**
```bash
git clone [repository-url]
cd si-peni
```

2. **Install dependencies**
```bash
composer install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Konfigurasi database di `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=si_mantik
DB_USERNAME=root
DB_PASSWORD=
```

5. **Jalankan migration**
```bash
php artisan migrate
```

6. **Buat user admin**
```bash
php artisan make:filament-user
```

7. **Jalankan server**
```bash
php artisan serve
```

8. **Akses admin panel**
```
http://localhost:8000/admin
```

## 📁 Struktur Project

```
app/
├── Filament/
│   ├── Resources/      # Filament Resources untuk CRUD
│   ├── Pages/          # Custom Pages
│   └── Widgets/        # Dashboard Widgets
├── Models/             # Eloquent Models
├── Services/           # Business Logic Services
└── Helpers/            # Helper Functions

database/
├── migrations/         # Database Migrations
└── seeders/           # Database Seeders

public/
├── images/            # Logo, favicon, dll
└── storage/           # File uploads
```

## 🔐 Default User

Setelah membuat user dengan `php artisan make:filament-user`, login dengan kredensial yang dibuat.

## 📝 Dokumentasi

- [DASHBOARD MODEL.MD](./DASHBOARD%20MODEL.MD) - Struktur menu dashboard
- [ERD SISTEM.MD](./ERD%20SISTEM.MD) - Entity Relationship Diagram
- [FLOW AUTO ADD ROW.MD](./FLOW%20AUTO%20ADD%20ROW.MD) - Flow auto register aset
- [TEKNOLOGI.MD](./TEKNOLOGI.MD) - Rekomendasi teknologi stack

## 🎯 Fitur Khusus

### Auto Register Aset
Saat input inventory dengan jenis = ASET dan qty = N, sistem otomatis:
- Membuat N baris di `inventory_item`
- Generate nomor register unik: `[UNIT]/[KODE_BARANG]/[TAHUN]/[URUT]`
- Generate QR Code untuk tracking

### Role-Based Access Control
Sistem mendukung multiple role:
- Admin
- Admin Gudang
- Kepala/Pimpinan
- Pegawai/User

## 📞 Support

Untuk pertanyaan atau bantuan, silakan hubungi tim development.

---

**SI-MANTIK** - Sistem Informasi Manajemen Terintegrasi © 2025
