# Update Role User dan Hak Akses Berdasarkan KLASIFIKASI_USER.MD

## 📋 Ringkasan Perubahan

Sistem telah diupdate untuk mengikuti klasifikasi role user dan flow approval berjenjang sesuai dengan dokumen `KLASIFIKASI_USER.MD` dan diagram ERD.

---

## 🔄 Perubahan Role

### Role Baru yang Ditambahkan:

1. **`kepala_unit`** - Kepala Unit (Kepala Seksi / Kepala Sub Unit)
   - Hanya mengetahui permintaan dari unitnya
   - Tidak bisa approve/reject

2. **`kasubbag_tu`** - Kasubbag TU
   - Verifikasi administrasi permintaan
   - Bisa mengembalikan jika tidak lengkap
   - Tidak bisa approve final

3. **`kepala_pusat`** - Kepala Pusat (Pimpinan)
   - Approve/Reject permintaan
   - Memberikan disposisi

4. **`perencanaan`** - Unit Perencanaan
   - Menindaklanjuti disposisi pimpinan

5. **`pengadaan`** - Unit Pengadaan
   - Menindaklanjuti disposisi pimpinan

6. **`keuangan`** - Unit Keuangan
   - Menindaklanjuti disposisi pimpinan

### Role yang Diupdate:

- **`admin`** → **`admin`** (Admin Sistem)
- **`pegawai`** → **`pegawai`** (Pegawai/Pemohon)
- **`admin_gudang`** → **`admin_gudang`** (Admin Gudang/Pengurus Barang)
- **`kepala`** → Diganti menjadi **`kepala_pusat`** (untuk pimpinan yang approve)

---

## 📊 Status Permintaan Barang (Multi-Level)

Status permintaan telah diupdate untuk mendukung approval berjenjang:

1. **DRAFT** - Permintaan masih dalam draft
2. **DIAJUKAN** - Permintaan telah diajukan oleh pegawai
3. **DIKETAHUI_UNIT** - Kepala Unit telah mengetahui
4. **DIKETAHUI_TU** - Kasubbag TU telah memverifikasi
5. **DISETUJUI_PIMPINAN** - Kepala Pusat telah menyetujui
6. **DITOLAK** - Permintaan ditolak
7. **DIDISPOSISIKAN** - Permintaan telah didisposisikan
8. **DIPROSES** - Sedang diproses oleh Admin Gudang
9. **SELESAI** - Permintaan telah selesai

---

## 🗄️ Struktur Database Baru

### 1. `approval_flow_definition`
Tabel untuk mendefinisikan flow approval berjenjang:

- `modul_approval` - Modul yang menggunakan flow (PERMINTAAN_BARANG, dll)
- `step_order` - Urutan step (1, 2, 3, ...)
- `role_id` - Role yang bertanggung jawab untuk step ini
- `nama_step` - Nama step (Diajukan, Diketahui Unit, dll)
- `status` - Status step
- `is_required` - Apakah step ini wajib
- `can_reject` - Apakah step ini bisa reject
- `can_approve` - Apakah step ini bisa approve

### 2. `approval_log`
Tabel untuk tracking approval:

- `modul_approval` - Modul yang di-approve
- `id_referensi` - ID dari modul yang di-approve
- `id_approval_flow` - Reference ke approval_flow_definition
- `user_id` - User yang melakukan approval
- `role_id` - Role user saat melakukan approval
- `status` - Status approval
- `catatan` - Catatan approval
- `approved_at` - Waktu approval

---

## 🔐 Hak Akses per Role

### 1. **Admin Sistem** (`admin`)
- ✅ Akses penuh ke semua modul
- ✅ Master Manajemen (CRUD)
- ✅ Master Data (CRUD)
- ✅ Inventory (CRUD)
- ✅ Transaksi (semua)
- ✅ Admin (Role & User Management)

### 2. **Pegawai (Pemohon)** (`pegawai`)
- ✅ Dashboard
- ✅ User Assets (View)
- ✅ User Requests (Create/View)
- ✅ Permintaan Barang (Create/View/Edit)
- ✅ Penerimaan Barang (View)

### 3. **Kepala Unit** (`kepala_unit`)
- ✅ Permintaan Barang (View)
- ✅ Approval - Mengetahui
- ❌ Tidak bisa approve/reject

### 4. **Kasubbag TU** (`kasubbag_tu`)
- ✅ Permintaan Barang (View)
- ✅ Approval - Verifikasi
- ✅ Approval - Kembalikan (jika tidak lengkap)
- ❌ Tidak bisa approve final

### 5. **Kepala Pusat** (`kepala_pusat`)
- ✅ Permintaan Barang (View)
- ✅ Approval - Approve/Reject
- ✅ Approval - Disposisi
- ✅ Reports (semua)

### 6. **Admin Gudang** (`admin_gudang`)
- ✅ Inventory (CRUD)
- ✅ Distribusi Barang (CRUD)
- ✅ Penerimaan Barang (CRUD)
- ✅ Approval (View/Disposisi)
- ✅ Asset & KIR (CRUD)
- ✅ Reports (Stock Gudang)

### 7. **Unit Terkait** (`perencanaan`, `pengadaan`, `keuangan`)
- ✅ Approval (View/Disposisi)
- ✅ Menindaklanjuti disposisi pimpinan

---

## 🔄 Flow Approval Berjenjang

```
1. PEGAWAI
   └─> Buat Permintaan (DRAFT)
       └─> Ajukan Permintaan (DIAJUKAN)

2. KEPALA UNIT
   └─> Mengetahui (DIKETAHUI_UNIT)
       └─> Lanjut ke Kasubbag TU

3. KASUBBAG TU
   └─> Verifikasi Administrasi
       ├─> Jika lengkap → Lanjut ke Kepala Pusat (DIKETAHUI_TU)
       └─> Jika tidak lengkap → Kembalikan ke Pegawai

4. KEPALA PUSAT
   └─> Review & Approval
       ├─> APPROVE → Disposisi (DISETUJUI_PIMPINAN → DIDISPOSISIKAN)
       └─> REJECT → Kembali ke Pegawai (DITOLAK)

5. ADMIN GUDANG / UNIT TERKAIT
   └─> Terima Disposisi
       └─> Proses (DIPROSES)
           └─> Distribusi Barang

6. SELESAI
   └─> Barang diterima (SELESAI)
```

---

## 📝 File yang Diupdate

### Migrations:
- `2026_01_20_095537_update_status_permintaan_barang_for_multilevel_approval.php`
- `2026_01_20_095539_create_approval_flow_definition_table.php`
- `2026_01_20_095541_create_approval_log_table.php`

### Seeders:
- `RoleSeeder.php` - Menambahkan role baru
- `MasterJabatanSeeder.php` - Update jabatan sesuai klasifikasi baru
- `ApprovalFlowDefinitionSeeder.php` - Seed flow approval berjenjang
- `DatabaseSeeder.php` - Include ApprovalFlowDefinitionSeeder

### Models:
- `ApprovalFlowDefinition.php` - Model untuk flow definition
- `ApprovalLog.php` - Model untuk approval log

### Helpers:
- `PermissionHelper.php` - Update hak akses untuk role baru

### Routes:
- `web.php` - Update middleware untuk route approval

---

## 🚀 Cara Menjalankan Update

1. **Jalankan migrations:**
```bash
php artisan migrate
```

2. **Jalankan seeders:**
```bash
php artisan db:seed
```

Atau seed secara spesifik:
```bash
php artisan db:seed --class=RoleSeeder
php artisan db:seed --class=MasterJabatanSeeder
php artisan db:seed --class=ApprovalFlowDefinitionSeeder
```

---

## ⚠️ Catatan Penting

1. **Role lama `kepala`** telah diganti menjadi **`kepala_pusat`**
   - Pastikan user yang memiliki role `kepala` di-update ke `kepala_pusat`

2. **Status permintaan** telah diubah dari enum sederhana menjadi multi-level
   - Data lama akan tetap ada, tapi perlu di-update manual jika diperlukan

3. **Approval flow** sekarang menggunakan sistem berjenjang
   - Setiap step memiliki role yang bertanggung jawab
   - Approval log mencatat setiap langkah approval

4. **Kepala Unit** hanya bisa mengetahui, tidak bisa approve/reject
   - Sesuai dengan prinsip dasar sistem: "Kepala Unit hanya mengetahui, bukan menyetujui"

---

**Update ini mengikuti prinsip dasar sistem:**
- ✅ Approval berjenjang dan terpisah fungsi
- ✅ Tidak ada conflict of interest
- ✅ Setiap proses tercatat (audit trail)
- ✅ Satu role = satu tanggung jawab utama





