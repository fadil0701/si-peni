# Update Hak Akses Kepala Unit & Admin Unit untuk Gudang Unit

## 📋 Ringkasan Perubahan

Kepala Unit dan Admin Unit (Pegawai) sekarang dapat mengakses Gudang Unit, melakukan stock management, dan melakukan return ke gudang pusat.

---

## 🔐 Hak Akses yang Ditambahkan

### **Kepala Unit** (`kepala_unit`)
**Akses Baru:**
- ✅ Inventory - Data Stock (hanya untuk gudang unit)
- ✅ Inventory - Data Inventory (view untuk gudang unit)
- ✅ Penerimaan Barang (CRUD)
- ✅ Retur Barang ke Gudang Pusat (CRUD)

### **Admin Unit / Pegawai** (`pegawai`)
**Akses Baru:**
- ✅ Inventory - Data Stock (hanya untuk gudang unit)
- ✅ Inventory - Data Inventory (view untuk gudang unit)
- ✅ Penerimaan Barang (CRUD)
- ✅ Retur Barang ke Gudang Pusat (CRUD)

---

## 🏢 Pembatasan Akses Berdasarkan Jenis Gudang

### Gudang Pusat (`jenis_gudang = 'PUSAT'`)
- **Admin Sistem**: ✅ Full Access
- **Admin Gudang**: ✅ Full Access
- **Kepala Unit**: ❌ No Access
- **Admin Unit**: ❌ No Access

### Gudang Unit (`jenis_gudang = 'UNIT'`)
- **Admin Sistem**: ✅ Full Access
- **Admin Gudang**: ✅ Full Access
- **Kepala Unit**: ✅ View Stock & Inventory
- **Admin Unit**: ✅ View Stock & Inventory

---

## 📝 Catatan Implementasi

### 1. Filter Gudang di Controller
Controller perlu memfilter gudang berdasarkan:
- **Kepala Unit & Admin Unit**: Hanya bisa akses gudang dengan `jenis_gudang = 'UNIT'` yang terkait dengan unit kerja mereka
- **Admin Gudang**: Bisa akses semua gudang
- **Admin**: Bisa akses semua gudang

### 2. Retur Barang
- Route sudah ditambahkan di `routes/web.php`
- Controller dan view perlu diimplementasikan
- Flow: Gudang Unit → Retur → Gudang Pusat
- Update stock: `qty_keluar` di gudang unit, `qty_masuk` di gudang pusat

### 3. Stock Management
- Kepala Unit & Admin Unit hanya bisa melihat stock di gudang unit mereka
- Tidak bisa melakukan input inventory baru (hanya Admin Gudang yang bisa)
- Bisa melihat history stock dan melakukan stock adjustment untuk gudang unit

---

## 🔄 Flow Retur Barang

```
┌─────────────────┐
│ ADMIN UNIT /    │
│ KEPALA UNIT     │
│ (Gudang Unit)   │
└──────┬──────────┘
       │
       │ 1. Buat Retur Barang
       │    - Pilih barang dari stock gudang unit
       │    - Input qty retur
       │    - Alasan retur
       │
       ▼
┌─────────────────┐
│ RETUR BARANG    │
│ (DRAFT)         │
└──────┬──────────┘
       │
       │ 2. Submit Retur
       │    - Status: DIAJUKAN
       │
       ▼
┌─────────────────┐
│ ADMIN GUDANG    │
│ (Gudang Pusat)  │
└──────┬──────────┘
       │
       │ 3. Terima Retur
       │    - Konfirmasi qty diterima
       │    - Update stock:
       │      • Gudang Unit: qty_keluar += qty_retur
       │      • Gudang Pusat: qty_masuk += qty_retur
       │
       ▼
┌─────────────────┐
│ RETUR SELESAI   │
└─────────────────┘
```

---

## 📊 File yang Diupdate

### 1. `app/Helpers/PermissionHelper.php`
- Menambahkan hak akses inventory untuk `kepala_unit` dan `pegawai`
- Menambahkan hak akses retur untuk `kepala_unit` dan `pegawai`
- Update menu inventory untuk include role baru

### 2. `routes/web.php`
- Update middleware inventory route untuk include `kepala_unit` dan `pegawai`
- Menambahkan route retur barang
- Update middleware penerimaan barang untuk include `kepala_unit`

---

## ⚠️ Catatan Penting

1. **Filter Gudang**: Controller perlu memfilter berdasarkan `jenis_gudang` dan `id_unit_kerja` user
2. **Retur Controller**: Perlu dibuat controller untuk retur barang
3. **Stock Update**: Retur harus update stock di kedua gudang (unit dan pusat)
4. **Validasi**: Pastikan user hanya bisa retur dari gudang unit mereka sendiri

---

**Update ini memungkinkan Kepala Unit dan Admin Unit untuk mengelola stock di gudang unit mereka dan melakukan return ke gudang pusat.**






