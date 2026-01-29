# Sistem Manajemen Permission Dinamis

## 📋 Overview

Sistem sekarang mendukung manajemen role dan permission secara dinamis melalui UI. Administrator dapat membuat role baru dan menentukan hak akses dengan cara checklist tanpa perlu mengubah kode.

---

## 🗄️ Struktur Database

### 1. **Table: `permissions`**
Menyimpan daftar semua permission yang tersedia di sistem.

**Kolom:**
- `id` - Primary key
- `name` - Nama permission (unique), contoh: `inventory.data-stock.index`
- `display_name` - Nama yang ditampilkan, contoh: `View Data Stock`
- `module` - Modul permission, contoh: `inventory`, `transaction`
- `group` - Group permission, contoh: `inventory.data-stock`
- `description` - Deskripsi permission
- `sort_order` - Urutan tampil
- `timestamps`

### 2. **Table: `permission_role`**
Pivot table untuk menghubungkan permission dengan role.

**Kolom:**
- `id` - Primary key
- `permission_id` - Foreign key ke `permissions`
- `role_id` - Foreign key ke `roles`
- `timestamps`
- Unique constraint: `(permission_id, role_id)`

---

## 🎯 Fitur Utama

### 1. **Membuat Role Baru dengan Permissions**
- Administrator dapat membuat role baru melalui UI
- Setelah membuat role, dapat langsung assign permissions dengan checklist
- Permissions dikelompokkan berdasarkan module untuk kemudahan

### 2. **Edit Role & Permissions**
- Administrator dapat mengubah permissions yang dimiliki oleh role
- Menggunakan checkbox untuk memilih/deselect permissions
- Perubahan langsung tersimpan ke database

### 3. **View Role & Permissions**
- Halaman detail role menampilkan semua permissions yang dimiliki
- Dikelompokkan berdasarkan module
- Menampilkan jumlah user yang memiliki role tersebut

---

## 🔐 Cara Kerja Permission Check

### Priority Check:
1. **Admin Role** → Selalu memiliki akses penuh (bypass semua check)
2. **Database Permissions** → Check permission dari database (dinamis)
3. **Static Permissions** → Fallback ke `PermissionHelper::getRolePermissions()` (untuk backward compatibility)

### Method `User::hasPermission()`
```php
$user->hasPermission('inventory.data-stock.index');
```

Method ini akan:
1. Check jika user adalah admin → return true
2. Load roles dengan permissions
3. Check exact match permission name
4. Check wildcard permissions (e.g., `inventory.*` matches `inventory.data-stock.index`)

---

## 📝 Cara Menggunakan

### 1. Membuat Role Baru
1. Login sebagai Administrator
2. Akses menu: **Admin > Manajemen Role**
3. Klik **Tambah Role**
4. Isi form:
   - Nama Role (contoh: `custom_role`)
   - Display Name (contoh: `Custom Role`)
   - Deskripsi (opsional)
5. Pilih permissions dengan checklist (dikelompokkan per module)
6. Klik **Simpan**

### 2. Edit Permissions Role
1. Akses menu: **Admin > Manajemen Role**
2. Klik **Detail** pada role yang ingin diubah
3. Klik **Edit Role & Permissions**
4. Ubah checklist permissions sesuai kebutuhan
5. Klik **Simpan**

### 3. Menambahkan Permission Baru
Saat ini permission ditambahkan melalui seeder. Untuk menambahkan permission baru:

**Option 1: Via Seeder**
```php
// Edit database/seeders/PermissionSeeder.php
// Tambahkan permission baru di array $permissions
// Jalankan: php artisan db:seed --class=PermissionSeeder
```

**Option 2: Via Database (Manual)**
```sql
INSERT INTO permissions (name, display_name, module, group, description, sort_order)
VALUES ('new.permission.name', 'Display Name', 'module_name', 'group_name', 'Description', 100);
```

**Option 3: Via UI (Future Enhancement)**
- Buat controller dan view untuk manage permissions
- Tambahkan route untuk CRUD permissions

---

## 🔄 Integrasi dengan Sistem

### 1. **Middleware CheckRole**
Middleware tetap menggunakan role-based check:
```php
middleware(['role:admin,admin_gudang'])
```

### 2. **PermissionHelper**
`PermissionHelper::canAccess()` sekarang menggunakan:
- Database permissions (priority)
- Static permissions (fallback)

### 3. **Controller Authorization**
Contoh penggunaan di controller:
```php
if (!PermissionHelper::canAccess(auth()->user(), 'inventory.data-stock.index')) {
    abort(403, 'Unauthorized');
}
```

---

## 📊 Daftar Permissions yang Tersedia

Permissions dikelompokkan berdasarkan module:

### **Dashboard**
- `user.dashboard` - View Dashboard

### **Master Manajemen**
- `master-manajemen.master-pegawai.*` - CRUD Master Pegawai
- `master-manajemen.master-jabatan.*` - CRUD Master Jabatan
- `master.unit-kerja.*` - CRUD Unit Kerja
- `master.gudang.*` - CRUD Gudang
- `master.ruangan.*` - CRUD Ruangan

### **Inventory**
- `inventory.data-stock.index` - View Data Stock
- `inventory.data-inventory.*` - CRUD Data Inventory
- `inventory.inventory-item.*` - CRUD Inventory Item

### **Transaction**
- `transaction.permintaan-barang.*` - CRUD Permintaan Barang
- `transaction.approval.*` - Approval Actions
- `transaction.distribusi.*` - CRUD Distribusi
- `transaction.penerimaan-barang.*` - CRUD Penerimaan
- `transaction.retur.*` - CRUD Retur

### **Asset & KIR**
- `asset.register-aset.*` - CRUD Register Aset

### **Reports**
- `reports.*` - All Reports
- `reports.stock-gudang` - Stock Gudang Report

### **Admin**
- `admin.roles.*` - Role Management
- `admin.users.*` - User Management

---

## 🎨 UI Features

### **Checkbox Grouping**
- Permissions dikelompokkan berdasarkan module
- Setiap permission menampilkan:
  - Display Name (bold)
  - Description (jika ada)
  - Permission Name (monospace, gray)

### **Select All per Module** (Future Enhancement)
- Bisa ditambahkan checkbox "Select All" untuk setiap module
- Memudahkan assign semua permissions dalam satu module

---

## ⚠️ Catatan Penting

1. **Admin Role**: Role dengan nama `admin` selalu memiliki akses penuh, tidak perlu assign permissions
2. **Wildcard Permissions**: Permission dengan format `module.*` akan match semua permission dalam module tersebut
3. **Backward Compatibility**: Sistem tetap menggunakan static permissions sebagai fallback
4. **Performance**: Permissions di-load dengan eager loading untuk menghindari N+1 queries

---

## 🚀 Langkah Selanjutnya

1. **Jalankan migrations dan seeder:**
```bash
php artisan migrate
php artisan db:seed --class=PermissionSeeder
```

2. **Assign permissions ke role yang sudah ada:**
   - Edit setiap role melalui UI
   - Pilih permissions sesuai kebutuhan
   - Simpan

3. **Test permission check:**
   - Login dengan user yang memiliki role tertentu
   - Coba akses route yang memerlukan permission
   - Pastikan akses sesuai dengan permissions yang di-assign

---

**Sistem sekarang mendukung manajemen permission secara dinamis melalui UI!**






