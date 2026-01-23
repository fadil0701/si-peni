# 🔧 Troubleshooting Permission & Menu

## Masalah: Menu Tidak Muncul Setelah Update Checklist Permission

### ✅ Perbaikan yang Sudah Dilakukan

1. **Memperbaiki `User::hasPermission()`**
   - Sekarang query langsung ke database untuk mendapatkan semua permission user
   - Lebih efisien dan akurat

2. **Memperbaiki `PermissionHelper::getAccessibleMenus()`**
   - Menu muncul berdasarkan permission yang dicentang di database
   - Menu parent muncul jika ada minimal 1 submenu yang accessible

3. **Memperbaiki Layout Menu**
   - Menu sekarang menggunakan `$accessibleMenus` yang sudah difilter berdasarkan permission
   - Tidak lagi menggunakan hardcoded role checking

4. **Menambahkan Middleware `LoadUserPermissions`**
   - Memastikan roles dan permissions ter-load untuk setiap request
   - Mencegah masalah permission tidak ter-load

5. **Memperbaiki LoginController**
   - Memastikan roles dan permissions ter-load setelah login

### 🔍 Cara Troubleshooting

#### 1. Cek Permission User
```bash
php artisan user:check-permissions kasubbag_tu@example.com
php artisan user:check-permissions kepala_pusat@example.com
```

Command ini akan menampilkan:
- Roles user
- Permissions dari database
- Test permission check
- Accessible menus

#### 2. Clear Cache
```bash
php artisan view:clear
php artisan cache:clear
php artisan config:clear
```

#### 3. Logout dan Login Lagi
- **PENTING**: Setelah update permission, user harus **logout dan login lagi** untuk refresh session
- Session menyimpan data user, jadi perlu di-refresh setelah permission diubah

#### 4. Cek Permission di Database
```sql
-- Cek permission yang di-assign ke role
SELECT r.name as role_name, p.name as permission_name, p.display_name
FROM roles r
JOIN permission_role pr ON r.id = pr.role_id
JOIN permissions p ON pr.permission_id = p.id
WHERE r.name = 'kasubbag_tu'
ORDER BY p.module, p.name;
```

#### 5. Cek User Role
```sql
-- Cek role user
SELECT u.email, r.name as role_name
FROM users u
JOIN role_user ru ON u.id = ru.user_id
JOIN roles r ON ru.role_id = r.id
WHERE u.email = 'kasubbag_tu@example.com';
```

### 📋 Checklist Permission yang Perlu Di-Checklist

Untuk menu **Transaksi** muncul, user minimal harus punya salah satu permission berikut:

#### Menu Transaksi (Parent)
- `transaction.*` (wildcard - semua submenu)
- ATAU minimal 1 submenu permission

#### Submenu Transaksi
- `transaction.permintaan-barang.index` → Menu "Permintaan Barang"
- `transaction.approval.index` → Menu "Persetujuan"
- `transaction.draft-distribusi.index` → Menu "Proses Disposisi"
- `transaction.compile-distribusi.index` → Menu "Compile SBBK"
- `transaction.distribusi.index` → Menu "Distribusi (SBBK)"
- `transaction.penerimaan-barang.index` → Menu "Penerimaan Barang"
- `transaction.retur-barang.index` → Menu "Retur Barang"

### 🎯 Contoh Konfigurasi

#### Untuk Kasubbag TU (melihat semua transaksi untuk monitoring):
```
✓ transaction.approval.index
✓ transaction.approval.show
✓ transaction.approval.verifikasi
✓ transaction.permintaan-barang.index
✓ transaction.permintaan-barang.show
✓ reports.*
```

#### Untuk Kepala Pusat (approval final):
```
✓ transaction.approval.*
✓ transaction.permintaan-barang.index
✓ transaction.permintaan-barang.show
✓ reports.*
```

### ⚠️ Catatan Penting

1. **Menu Parent vs Submenu**
   - Menu parent (misal "Transaksi") muncul jika ada **minimal 1 submenu** yang accessible
   - Tidak perlu checklist `transaction.*` jika sudah checklist submenu spesifik

2. **Wildcard Permission**
   - `transaction.*` = semua permission yang dimulai dengan `transaction.`
   - `inventory.*` = semua permission yang dimulai dengan `inventory.`

3. **Permission Check Priority**
   - Database permissions (dari checklist) → **PRIORITAS UTAMA**
   - Static permissions (dari `getRolePermissions()`) → Fallback

4. **Cache & Session**
   - Setelah update permission, **WAJIB logout dan login lagi**
   - Clear cache jika masih tidak muncul

### 🐛 Debug Mode

Jika masih tidak muncul, tambahkan debug di `resources/views/layouts/app.blade.php`:

```php
@php
    // Debug: Tampilkan accessible menus
    if (auth()->check()) {
        \Log::info('Accessible Menus', [
            'user' => auth()->user()->email,
            'menus' => $accessibleMenus,
            'canAccessTransaksi' => $canAccessTransaksi,
        ]);
    }
@endphp
```

Lalu cek file `storage/logs/laravel.log` untuk melihat debug info.

---

**Last Updated:** {{ date('d/m/Y') }}


