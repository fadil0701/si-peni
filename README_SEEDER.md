# 📋 Panduan Seeder User Transaksi

## 🎯 Tujuan

Seeder ini akan:
1. **Menghapus semua user** kecuali yang memiliki role `admin`
2. **Membuat user baru** sesuai kebutuhan alur transaksi dengan role yang sesuai

## 🚀 Cara Menjalankan

### Opsi 1: Jalankan Seeder Langsung
```bash
php artisan db:seed --class=TransactionUserSeeder
```

### Opsi 2: Tambahkan ke DatabaseSeeder
Tambahkan `TransactionUserSeeder::class` ke `database/seeders/DatabaseSeeder.php`:

```php
public function run(): void
{
    $this->call([
        RoleSeeder::class,
        PermissionSeeder::class,
        MasterJabatanSeeder::class,
        AdminUserSeeder::class,
        ApprovalFlowDefinitionSeeder::class,
        TransactionUserSeeder::class, // Tambahkan ini
    ]);
}
```

Kemudian jalankan:
```bash
php artisan db:seed
```

## 👥 Daftar User yang Akan Dibuat

### 1. **PEGAWAI** (Pemohon Barang)
- **Email:** `pegawai1@example.com` | **Password:** `password`
- **Email:** `pegawai2@example.com` | **Password:** `password`
- **Email:** `pegawai3@example.com` | **Password:** `password`
- **Role:** `pegawai`
- **Fungsi:** Membuat permintaan barang, menerima barang, retur barang

### 2. **KEPALA UNIT** (Approval Level 1)
- **Email:** `kepala_unit1@example.com` | **Password:** `password`
- **Email:** `kepala_unit2@example.com` | **Password:** `password`
- **Role:** `kepala_unit`
- **Fungsi:** Mengetahui permintaan, approval level 1, menerima barang, retur

### 3. **KASUBBAG TU** (Verifikasi)
- **Email:** `kasubbag_tu@example.com` | **Password:** `password`
- **Role:** `kasubbag_tu`
- **Fungsi:** Verifikasi permintaan, bisa mengembalikan jika tidak lengkap

### 4. **KEPALA PUSAT** (Approval Final)
- **Email:** `kepala_pusat@example.com` | **Password:** `password`
- **Role:** `kepala_pusat`
- **Fungsi:** Approval/reject final permintaan

### 5. **ADMIN GUDANG** (Disposisi, Compile, Distribusi)
- **Email:** `admin_gudang@example.com` | **Password:** `password`
- **Role:** `admin_gudang`
- **Fungsi:** Disposisi permintaan, compile SBBK, distribusi barang

### 6. **ADMIN GUDANG KATEGORI** (Proses Disposisi)
- **Email:** `admin_gudang_aset@example.com` | **Password:** `password`
- **Role:** `admin_gudang_aset`
- **Fungsi:** Proses disposisi untuk kategori ASET

- **Email:** `admin_gudang_persediaan@example.com` | **Password:** `password`
- **Role:** `admin_gudang_persediaan`
- **Fungsi:** Proses disposisi untuk kategori PERSEDIAAN

- **Email:** `admin_gudang_farmasi@example.com` | **Password:** `password`
- **Role:** `admin_gudang_farmasi`
- **Fungsi:** Proses disposisi untuk kategori FARMASI

### 7. **UNIT TERKAIT** (Monitoring)
- **Email:** `perencanaan@example.com` | **Password:** `password`
- **Role:** `perencanaan`
- **Fungsi:** Monitoring dan disposisi

- **Email:** `pengadaan@example.com` | **Password:** `password`
- **Role:** `pengadaan`
- **Fungsi:** Monitoring dan disposisi

- **Email:** `keuangan@example.com` | **Password:** `password`
- **Role:** `keuangan`
- **Fungsi:** Monitoring dan disposisi

## ⚠️ Peringatan

1. **Seeder ini akan menghapus semua user kecuali admin!**
   - Pastikan Anda sudah membuat user admin terlebih dahulu
   - User admin harus memiliki role `admin`

2. **Pastikan role sudah ada di database**
   - Seeder membutuhkan role: `pegawai`, `kepala_unit`, `kasubbag_tu`, `kepala_pusat`, `admin_gudang`, `admin_gudang_aset`, `admin_gudang_persediaan`, `admin_gudang_farmasi`, `perencanaan`, `pengadaan`, `keuangan`
   - Jalankan `RoleSeeder` terlebih dahulu jika belum ada

3. **Password default: `password`**
   - Semua user menggunakan password yang sama: `password`
   - **Sangat disarankan untuk mengubah password setelah login pertama kali!**

## 🔄 Alur Transaksi dengan User

```
1. PEGAWAI → Membuat Permintaan Barang
   ↓
2. KEPALA UNIT → Mengetahui (Approval Level 1)
   ↓
3. KASUBBAG TU → Verifikasi
   ↓
4. KEPALA PUSAT → Approve/Reject (Approval Final)
   ↓
5. ADMIN GUDANG → Disposisi ke Admin Gudang Kategori
   ↓
6. ADMIN GUDANG KATEGORI → Proses Disposisi
   ↓
7. ADMIN GUDANG → Compile SBBK
   ↓
8. ADMIN GUDANG → Distribusi/Kirim
   ↓
9. PEGAWAI/KEPALA UNIT → Penerimaan Barang
   ↓
10. PEGAWAI/KEPALA UNIT → Retur (jika diperlukan)
```

## 📝 Catatan

- Semua email menggunakan domain `@example.com` (ubah sesuai kebutuhan)
- Semua password adalah `password` (ubah setelah login)
- User yang sudah ada dengan email yang sama akan dilewati (tidak dihapus)
- Seeder akan menampilkan informasi detail saat dijalankan

---

**Last Updated:** {{ date('d/m/Y') }}


