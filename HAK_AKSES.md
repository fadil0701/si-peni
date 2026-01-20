# Dokumentasi Hak Akses Sistem

## Overview
Sistem menggunakan Role-Based Access Control (RBAC) dimana hak akses ditentukan berdasarkan Role User yang terhubung dengan Master Jabatan.

## Role dan Hak Akses

### 1. **Admin** (`admin`)
**Jabatan:** Administrator
**Hak Akses:**
- ✅ Akses penuh ke semua modul
- ✅ Master Manajemen (Pegawai, Jabatan, Unit Kerja, Gudang, Ruangan, Program, Kegiatan, Sub Kegiatan)
- ✅ Master Data (semua)
- ✅ Inventory (semua)
- ✅ Transaksi (semua)
- ✅ Asset & KIR
- ✅ Planning (RKU)
- ✅ Procurement
- ✅ Finance
- ✅ Reports
- ✅ Admin (Role & User Management)

### 2. **Admin Gudang** (`admin_gudang`)
**Jabatan:** 
- Pengurus Barang (Admin Gudang Pusat)
- Admin Gudang (Aset/Persediaan/Farmasi)
- Admin Unit Kerja

**Hak Akses:**
- ✅ Inventory (Data Stock, Data Inventory)
- ✅ Transaksi Distribusi (SBBK)
- ✅ Transaksi Penerimaan Barang
- ✅ Approval (view only)
- ✅ Asset & KIR (Register Aset)
- ✅ Master Data Barang (view/edit)
- ✅ Master Gudang (view/edit)
- ✅ Reports (Stock Gudang)

### 3. **Kepala** (`kepala`)
**Jabatan:**
- Kepala (Pimpinan Tertinggi)
- Kasubbag TU
- Kepala Unit Kerja

**Hak Akses:**
- ✅ Transaksi Permintaan Barang (view)
- ✅ Approval Permintaan Barang (approve/reject)
- ✅ Reports (semua laporan)

### 4. **Pegawai** (`pegawai`)
**Jabatan:**
- Pengadaan Barang
- Perencanaan
- Keuangan/Bendahara

**Hak Akses:**
- ✅ Dashboard
- ✅ User Assets (view)
- ✅ User Requests (create/view)
- ✅ Transaksi Permintaan Barang (create/view)
- ✅ Transaksi Penerimaan Barang (view)

## Mapping Jabatan ke Role

| Urutan | Jabatan | Role | Deskripsi |
|--------|---------|------|-----------|
| 1 | Kepala | `kepala` | Pimpinan Tertinggi |
| 2 | Kasubbag TU | `kepala` | Dibawah Pimpinan |
| 3 | Pengurus Barang | `admin_gudang` | Admin Gudang Pusat |
| 4 | Pengadaan Barang | `pegawai` | Pengadaan Barang |
| 5 | Perencanaan | `pegawai` | Perencanaan |
| 6 | Keuangan/Bendahara | `pegawai` | Keuangan/Bendahara |
| 7 | Admin Gudang | `admin_gudang` | Admin Gudang (Aset/Persediaan/Farmasi) |
| 8 | Kepala Unit Kerja | `kepala` | Kepala Unit Kerja |
| 9 | Admin Unit Kerja | `admin_gudang` | Admin Unit Kerja |
| 10 | Administrator | `admin` | Seluruh Sistem |

## Implementasi Teknis

### 1. Middleware
- **File:** `app/Http/Middleware/CheckRole.php`
- **Penggunaan:** `middleware(['role:admin,admin_gudang'])`
- **Fungsi:** Mengecek apakah user memiliki salah satu role yang diizinkan

### 2. Permission Helper
- **File:** `app/Helpers/PermissionHelper.php`
- **Fungsi:**
  - `getRolePermissions()`: Mapping role ke permission
  - `canAccess()`: Check apakah user bisa akses route tertentu
  - `getAccessibleMenus()`: Get menu yang bisa diakses user

### 3. Routes Protection
Semua route sudah dilindungi dengan middleware role sesuai kebutuhan:
```php
// Contoh
Route::prefix('inventory')->middleware(['role:admin,admin_gudang'])->group(...)
Route::prefix('transaction')->middleware(['role:admin,kepala'])->group(...)
```

### 4. Sidebar Menu
Sidebar akan menampilkan menu sesuai role user yang login menggunakan `accessibleMenus` dari `PermissionHelper`.

## Flow Approval Berdasarkan Role

1. **Pegawai** → Membuat Permintaan Barang
2. **Kepala/Kasubbag TU** → Mengetahui permintaan
3. **Kepala Pusat** → Approve/Reject permintaan
4. **Admin Gudang/Pengurus Barang** → Siapkan SBBK & Kirim Barang
5. **Admin Gudang Unit** → Terima & Cek Barang

## Catatan Penting

- Role user **otomatis** mengikuti jabatan yang dipilih saat create/update pegawai
- Jika jabatan diubah, role user akan otomatis di-update
- Admin selalu memiliki akses penuh ke semua modul
- Middleware akan menolak akses jika user tidak memiliki role yang sesuai

