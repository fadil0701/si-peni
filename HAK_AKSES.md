# Dokumentasi Hak Akses Sistem

## Overview
Sistem menggunakan Role-Based Access Control (RBAC) dimana hak akses ditentukan berdasarkan Role User yang terhubung dengan Master Jabatan.

---

## Konvensi Variabel (Akses & Role) — Satu Acuan

Agar persepsi sama di seluruh tim, **nama variabel untuk akses dan role ditetapkan sebagai berikut**. Gunakan **hanya** nama ini di view, controller, dan dokumentasi.

### Variabel untuk User yang Login

| Nama variabel (custom) | Tipe | Arti |
|------------------------|------|------|
| **`$currentUser`** | `User\|null` | User yang sedang login. `null` jika guest. |

**Jangan pakai:** `auth()->user()`, `Auth::user()` di view — pakai `$currentUser`.

---

### Variabel untuk Role User

| Nama variabel (custom) | Tipe | Arti |
|------------------------|------|------|
| **`$userRoles`** | `array` | Daftar **nama** role user, contoh: `['admin_gudang', 'pegawai']`. |
| **`$userRoleIds`** | `array` | Daftar **id** role user, contoh: `[1, 3]`. |
| **`$userPrimaryRole`** | `Role\|null` | Role utama user (role pertama). Dipakai untuk tampilan, misal `display_name`. |

**Contoh di view:**  
- Cek punya role admin: `in_array('admin', $userRoles)`  
- Tampilkan jabatan: `$userPrimaryRole?->display_name ?? 'User'`

---

### Variabel untuk Akses (Hak Akses / Menu)

| Nama variabel (custom) | Tipe | Arti |
|------------------------|------|------|
| **`$accessibleMenus`** | `array` | Menu yang **boleh diakses** user saat ini (dari PermissionHelper). Dipakai untuk sidebar & visibility menu. |

**Contoh di view:**  
- Boleh akses master data: `isset($accessibleMenus['master-data'])`  
- Cek permission spesifik: `PermissionHelper::canAccess($currentUser, 'inventory.data-stock.index')`

---

### Ringkasan — Hanya 5 Variabel Ini

| Untuk | Variabel |
|-------|----------|
| User login | `$currentUser` |
| Akses / menu | `$accessibleMenus` |
| Role (nama) | `$userRoles` |
| Role (id) | `$userRoleIds` |
| Role utama (tampilan) | `$userPrimaryRole` |

Variabel di atas di-set di `AppServiceProvider` (View Composer) dan tersedia di **semua Blade view**. Untuk guest, `$currentUser` dan `$userPrimaryRole` = `null`, `$accessibleMenus` / `$userRoles` / `$userRoleIds` = `[]`.

---

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

### 2. **Admin Gudang** — Pemisahan per kategori agar tidak konflik

Setiap role admin gudang **hanya** mengakses kategori/jenis gudang yang ditetapkan. Filter diterapkan di controller (inventory, distribusi, penerimaan, dll).

| Role | Bisa mengakses kategori | Tidak bisa akses |
|------|-------------------------|-------------------|
| **admin_gudang_aset** | Aset | Persediaan, Farmasi |
| **admin_gudang_persediaan** | Persediaan | Aset, Farmasi |
| **admin_gudang_farmasi** | Farmasi | Aset, Persediaan |
| **admin_gudang_unit** | Inventory unit kerja (gudang UNIT) | Gudang pusat |
| **admin_gudang** (umum) | Semua (Pengurus Barang / admin gudang pusat) | — |

**Jabatan contoh:** Pengurus Barang → `admin_gudang`; Admin Gudang Aset → `admin_gudang_aset`; Admin Gudang Unit → `admin_gudang_unit`.

**Hak Akses (umum):**
- ✅ Inventory (Data Stock, Data Inventory) — difilter per role di atas
- ✅ Transaksi Distribusi (SBBK) — gudang asal/tujuan difilter per kategori/unit
- ✅ Transaksi Penerimaan Barang
- ✅ Approval (view only)
- ✅ Asset & KIR (Register Aset)
- ✅ Master Data Barang (view/edit), Master Gudang (view/edit)
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

### 5. Variabel View
Nama variabel untuk akses dan role mengikuti **Konvensi Variabel** di atas (`$currentUser`, `$accessibleMenus`, `$userRoles`, `$userRoleIds`, `$userPrimaryRole`). Variabel tersebut di-set di `AppServiceProvider` View Composer dan tersedia di semua Blade view. Untuk cek permission di view: `PermissionHelper::canAccess($currentUser, 'permission.name')`.

## Permintaan Barang (Rutin/Cito)

- **Sub jenis:** Hanya **Persediaan** dan **Farmasi**. Aset bersifat non-stock dan **tidak** masuk dalam permintaan rutin/cito.
- **Satu SPB:** Bisa ke **satu gudang** (hanya Persediaan atau hanya Farmasi) atau ke **semua gudang** (Persediaan + Farmasi sekaligus).
- **Validasi:** Di form buat/edit permintaan hanya tersedia pilihan Persediaan dan Farmasi; alur approval dan disposisi hanya memproses kedua kategori ini.

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

