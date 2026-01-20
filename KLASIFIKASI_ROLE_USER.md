# Klasifikasi Role User Berdasarkan Jabatan, Hak Akses, dan Flow Kerja

## 📋 Daftar Isi
1. [Klasifikasi Role Berdasarkan Jabatan](#klasifikasi-role-berdasarkan-jabatan)
2. [Hak Akses Sistem per Role](#hak-akses-sistem-per-role)
3. [Flow Kerja Sistem](#flow-kerja-sistem)
4. [Mapping Jabatan ke Role](#mapping-jabatan-ke-role)
5. [Ringkasan Hak Akses per Modul](#ringkasan-hak-akses-per-modul)

---

## 🎯 Quick Reference: Role & Jabatan

```
┌─────────────────────────────────────────────────────────────┐
│                    ROLE: ADMIN                              │
│  Jabatan: Administrator (Urutan 10)                        │
│  Akses: FULL ACCESS - Semua Modul                          │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                ROLE: ADMIN GUDANG                            │
│  Jabatan:                                                    │
│  • Pengurus Barang (Urutan 3) - Admin Gudang Pusat         │
│  • Admin Gudang (Urutan 7) - Aset/Persediaan/Farmasi        │
│  • Admin Unit Kerja (Urutan 9)                              │
│  Akses: Inventory, Distribusi, Penerimaan, Asset          │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                  ROLE: KEPALA                               │
│  Jabatan:                                                    │
│  • Kepala (Urutan 1) - Pimpinan Tertinggi                   │
│  • Kasubbag TU (Urutan 2) - Dibawah Pimpinan               │
│  • Kepala Unit Kerja (Urutan 8)                             │
│  Akses: Approval, Reports                                   │
└─────────────────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────────────────┐
│                 ROLE: PEGAWAI                               │
│  Jabatan:                                                    │
│  • Pengadaan Barang (Urutan 4)                              │
│  • Perencanaan (Urutan 5)                                   │
│  • Keuangan/Bendahara (Urutan 6)                            │
│  Akses: Request, View                                       │
└─────────────────────────────────────────────────────────────┘
```

---

## 🏢 Klasifikasi Role Berdasarkan Jabatan

### 1. **Role: ADMIN** (`admin`)
**Jabatan:** Administrator (Urutan 10)

**Karakteristik:**
- Pimpinan sistem dengan akses penuh
- Mengelola konfigurasi sistem
- Mengelola user dan role
- Monitoring semua aktivitas

**Jabatan yang menggunakan role ini:**
- Administrator (Seluruh Sistem)

---

### 2. **Role: ADMIN GUDANG** (`admin_gudang`)
**Jabatan:** 
- Pengurus Barang (Urutan 3) - Admin Gudang Pusat
- Admin Gudang (Urutan 7) - Aset/Persediaan/Farmasi
- Admin Unit Kerja (Urutan 9)

**Karakteristik:**
- Mengelola inventory dan stock gudang
- Melakukan distribusi barang (SBBK)
- Menerima dan mengecek barang masuk
- Mengelola register aset
- Melihat laporan stock gudang

**Jabatan yang menggunakan role ini:**
- Pengurus Barang (Admin Gudang Pusat)
- Admin Gudang (Aset/Persediaan/Farmasi)
- Admin Unit Kerja

---

### 3. **Role: KEPALA** (`kepala`)
**Jabatan:**
- Kepala (Urutan 1) - Pimpinan Tertinggi
- Kasubbag TU (Urutan 2) - Dibawah Pimpinan
- Kepala Unit Kerja (Urutan 8)

**Karakteristik:**
- Menyetujui/menolak permintaan barang
- Melihat semua permintaan yang masuk
- Melihat laporan sistem
- Memberikan disposisi ke unit terkait

**Jabatan yang menggunakan role ini:**
- Kepala (Pimpinan Tertinggi)
- Kasubbag TU (Dibawah Pimpinan)
- Kepala Unit Kerja

---

### 4. **Role: PEGAWAI** (`pegawai`)
**Jabatan:**
- Pengadaan Barang (Urutan 4)
- Perencanaan (Urutan 5)
- Keuangan/Bendahara (Urutan 6)

**Karakteristik:**
- Membuat permintaan barang
- Melihat status permintaan
- Melihat aset yang digunakan
- Melihat penerimaan barang

**Jabatan yang menggunakan role ini:**
- Pengadaan Barang
- Perencanaan
- Keuangan/Bendahara

---

## 🔐 Hak Akses Sistem per Role

### **ADMIN** - Akses Penuh

#### Master Manajemen
- ✅ Master Pegawai (CRUD)
- ✅ Master Jabatan (CRUD)
- ✅ Unit Kerja (CRUD)
- ✅ Gudang (CRUD)
- ✅ Ruangan (CRUD)
- ✅ Program (CRUD)
- ✅ Kegiatan (CRUD)
- ✅ Sub Kegiatan (CRUD)

#### Master Data
- ✅ Aset (CRUD)
- ✅ Kode Barang (CRUD)
- ✅ Kategori Barang (CRUD)
- ✅ Jenis Barang (CRUD)
- ✅ Subjenis Barang (CRUD)
- ✅ Data Barang (CRUD)
- ✅ Satuan (CRUD)
- ✅ Sumber Anggaran (CRUD)

#### Inventory
- ✅ Data Stock (View)
- ✅ Data Inventory (CRUD)
- ✅ Inventory Item (CRUD)

#### Transaksi
- ✅ Permintaan Barang (CRUD)
- ✅ Approval Permintaan (Approve/Reject)
- ✅ Distribusi Barang/SBBK (CRUD)
- ✅ Penerimaan Barang (CRUD)

#### Asset & KIR
- ✅ Register Aset (CRUD)
- ✅ Kartu Inventaris Ruangan (CRUD)

#### Planning
- ✅ RKU (Rencana Kebutuhan Unit) (CRUD)

#### Procurement
- ✅ Paket Pengadaan (CRUD)

#### Finance
- ✅ Pembayaran (CRUD)

#### Reports
- ✅ Semua Laporan (View/Export)

#### Admin
- ✅ Manajemen Role (CRUD)
- ✅ Manajemen User (CRUD)

---

### **ADMIN GUDANG** - Manajemen Inventory & Distribusi

#### Master Data
- ✅ Data Barang (View/Edit)
- ❌ Aset, Kode Barang, Kategori, Jenis, Subjenis (No Access)
- ❌ Satuan, Sumber Anggaran (No Access)

#### Master Manajemen
- ✅ Gudang (View/Edit)
- ❌ Master Pegawai, Jabatan, Unit Kerja, Ruangan, Program, Kegiatan, Sub Kegiatan (No Access)

#### Inventory
- ✅ Data Stock (View)
- ✅ Data Inventory (CRUD)
- ✅ Inventory Item (CRUD)

#### Transaksi
- ✅ Distribusi Barang/SBBK (CRUD)
- ✅ Penerimaan Barang (CRUD)
- ✅ Approval Permintaan (View Only)
- ❌ Permintaan Barang (No Access)

#### Asset & KIR
- ✅ Register Aset (CRUD)

#### Reports
- ✅ Stock Gudang (View/Export)
- ❌ Laporan Lainnya (No Access)

#### Planning, Procurement, Finance
- ❌ No Access

---

### **KEPALA** - Approval & Monitoring

#### Transaksi
- ✅ Permintaan Barang (View Only)
- ✅ Approval Permintaan (Approve/Reject)
- ❌ Distribusi, Penerimaan (No Access)

#### Reports
- ✅ Semua Laporan (View/Export)

#### Master & Inventory
- ❌ No Access

---

### **PEGAWAI** - Request & View

#### Dashboard
- ✅ Dashboard (View)

#### User Features
- ✅ User Assets (View)
- ✅ User Requests (Create/View)

#### Transaksi
- ✅ Permintaan Barang (Create/View)
- ✅ Penerimaan Barang (View Only)
- ❌ Approval, Distribusi (No Access)

#### Master, Inventory, Reports
- ❌ No Access

---

## 🔄 Flow Kerja Sistem

### **Flow 1: Permintaan Barang**

```
┌─────────────┐
│   PEGAWAI   │
│  (Pengadaan │
│  Barang,    │
│ Perencanaan,│
│  Keuangan)  │
└──────┬──────┘
       │
       │ 1. Buat Permintaan Barang
       │    - Pilih gudang tujuan
       │    - Pilih barang & qty
       │    - Alasan permintaan
       │
       ▼
┌─────────────────┐
│ PERMINTAAN      │
│ BARANG (DRAFT)  │
└──────┬──────────┘
       │
       │ 2. Ajukan Permintaan
       │
       ▼
┌─────────────────┐
│ KEPALA UNIT     │
│ (Kasubbag TU)   │
└──────┬──────────┘
       │
       │ 3. Mengetahui Permintaan
       │
       ▼
┌─────────────────┐
│ KEPALA PUSAT    │
│ (Kepala)        │
└──────┬──────────┘
       │
       │ 4. Review & Approval
       │    - Approve → Lanjut ke Admin Gudang
       │    - Reject → Kembali ke Pegawai
       │
       ▼
┌─────────────────┐
│ ADMIN GUDANG    │
│ (Pengurus       │
│ Barang)         │
└──────┬──────────┘
       │
       │ 5. Siapkan SBBK
       │    - Buat Distribusi Barang
       │    - Generate No. SBBK
       │    - Pilih barang dari stock
       │
       ▼
┌─────────────────┐
│ DISTRIBUSI      │
│ BARANG (SBBK)   │
└──────┬──────────┘
       │
       │ 6. Kirim Barang
       │    - Update stock (qty_keluar)
       │    - Status: TERKIRIM
       │
       ▼
┌─────────────────┐
│ ADMIN GUDANG    │
│ (Unit Kerja)    │
└──────┬──────────┘
       │
       │ 7. Terima & Cek Barang
       │    - Buat Penerimaan Barang
       │    - Konfirmasi qty diterima
       │    - Update stock (qty_masuk)
       │
       ▼
┌─────────────────┐
│ PENERIMAAN      │
│ BARANG          │
│ (SELESAI)       │
└─────────────────┘
```

### **Flow 2: Inventory Management (Admin Gudang)**

```
┌─────────────────┐
│ ADMIN GUDANG    │
└──────┬──────────┘
       │
       │ 1. Input Data Inventory
       │    - Pilih barang
       │    - Input qty, harga, dll
       │    - Upload foto (opsional)
       │
       ▼
┌─────────────────┐
│ DATA INVENTORY  │
└──────┬──────────┘
       │
       │ 2. Jika Jenis = ASET
       │    - Auto generate Inventory Item
       │    - Generate kode register
       │    - Generate QR Code
       │
       ▼
┌─────────────────┐
│ INVENTORY ITEM  │
│ (per unit aset) │
└──────┬──────────┘
       │
       │ 3. Update Stock
       │    - qty_masuk += qty_input
       │    - qty_akhir = qty_masuk - qty_keluar
       │
       ▼
┌─────────────────┐
│ DATA STOCK      │
│ (Updated)       │
└─────────────────┘
```

### **Flow 3: Approval Process**

```
┌─────────────────┐
│ PERMINTAAN      │
│ BARANG          │
│ Status: DIAJUKAN│
└──────┬──────────┘
       │
       │ 1. Masuk ke menu Approval
       │    (Role: KEPALA)
       │
       ▼
┌─────────────────┐
│ KEPALA          │
│ Review Request  │
└──────┬──────────┘
       │
       ├─── APPROVE ────┐
       │                 │
       │                 ▼
       │         ┌─────────────────┐
       │         │ Status:         │
       │         │ DISETUJUI       │
       │         └──────┬───────────┘
       │                │
       │                │ 2. Notifikasi ke Admin Gudang
       │                │
       │                ▼
       │         ┌─────────────────┐
       │         │ ADMIN GUDANG    │
       │         │ Siapkan SBBK    │
       │         └─────────────────┘
       │
       └─── REJECT ────┐
                        │
                        ▼
                ┌─────────────────┐
                │ Status:         │
                │ DITOLAK         │
                └──────┬──────────┘
                       │
                       │ 3. Notifikasi ke Pegawai
                       │
                       ▼
                ┌─────────────────┐
                │ PEGAWAI         │
                │ Lihat Alasan    │
                │ Penolakan       │
                └─────────────────┘
```

---

## 📊 Mapping Jabatan ke Role

| Urutan | Nama Jabatan | Role | Deskripsi | Hak Akses Utama |
|--------|--------------|------|-----------|-----------------|
| 1 | **Kepala** | `kepala` | Pimpinan Tertinggi | Approval, Reports |
| 2 | **Kasubbag TU** | `kepala` | Dibawah Pimpinan | Approval, Reports |
| 3 | **Pengurus Barang** | `admin_gudang` | Admin Gudang Pusat | Inventory, Distribusi |
| 4 | **Pengadaan Barang** | `pegawai` | Pengadaan Barang | Request, View |
| 5 | **Perencanaan** | `pegawai` | Perencanaan | Request, View |
| 6 | **Keuangan/Bendahara** | `pegawai` | Keuangan/Bendahara | Request, View |
| 7 | **Admin Gudang** | `admin_gudang` | Admin Gudang (Aset/Persediaan/Farmasi) | Inventory, Distribusi |
| 8 | **Kepala Unit Kerja** | `kepala` | Kepala Unit Kerja | Approval, Reports |
| 9 | **Admin Unit Kerja** | `admin_gudang` | Admin Unit Kerja | Inventory, Penerimaan |
| 10 | **Administrator** | `admin` | Seluruh Sistem | Full Access |

---

## 🎯 Ringkasan Hak Akses per Modul

### **Master Manajemen**
- **Admin**: ✅ Full Access
- **Admin Gudang**: ✅ Gudang (View/Edit)
- **Kepala**: ❌ No Access
- **Pegawai**: ❌ No Access

### **Master Data**
- **Admin**: ✅ Full Access
- **Admin Gudang**: ✅ Data Barang (View/Edit)
- **Kepala**: ❌ No Access
- **Pegawai**: ❌ No Access

### **Inventory**
- **Admin**: ✅ Full Access
- **Admin Gudang**: ✅ Full Access
- **Kepala**: ❌ No Access
- **Pegawai**: ❌ No Access

### **Transaksi - Permintaan Barang**
- **Admin**: ✅ Full Access
- **Admin Gudang**: ❌ No Access
- **Kepala**: ✅ View Only
- **Pegawai**: ✅ Create/View

### **Transaksi - Approval**
- **Admin**: ✅ Full Access
- **Admin Gudang**: ✅ View Only
- **Kepala**: ✅ Approve/Reject
- **Pegawai**: ❌ No Access

### **Transaksi - Distribusi**
- **Admin**: ✅ Full Access
- **Admin Gudang**: ✅ Full Access
- **Kepala**: ❌ No Access
- **Pegawai**: ❌ No Access

### **Transaksi - Penerimaan**
- **Admin**: ✅ Full Access
- **Admin Gudang**: ✅ Full Access
- **Kepala**: ❌ No Access
- **Pegawai**: ✅ View Only

### **Asset & KIR**
- **Admin**: ✅ Full Access
- **Admin Gudang**: ✅ Full Access
- **Kepala**: ❌ No Access
- **Pegawai**: ❌ No Access

### **Reports**
- **Admin**: ✅ Full Access
- **Admin Gudang**: ✅ Stock Gudang
- **Kepala**: ✅ Full Access
- **Pegawai**: ❌ No Access

---

## 📝 Catatan Penting

1. **Role Otomatis dari Jabatan**
   - Saat membuat/update pegawai, role user otomatis mengikuti jabatan
   - Jika jabatan diubah, role user otomatis di-update

2. **Hierarki Approval**
   - Pegawai → Ajukan Permintaan
   - Kasubbag TU → Mengetahui
   - Kepala → Approve/Reject
   - Admin Gudang → Eksekusi Distribusi

3. **Stock Management**
   - Hanya Admin Gudang yang bisa input inventory
   - Stock otomatis ter-update saat:
     - Input inventory (qty_masuk)
     - Distribusi (qty_keluar)
     - Penerimaan (qty_masuk)

4. **Auto Register Aset**
   - Saat input inventory dengan jenis = ASET
   - Sistem otomatis membuat Inventory Item per unit
   - Generate kode register unik
   - Generate QR Code untuk tracking

5. **Middleware Protection**
   - Semua route dilindungi dengan middleware role
   - User tanpa role yang sesuai akan mendapat error 403
   - Admin selalu memiliki akses penuh

---

---

## 📊 Matriks Perbandingan Role

| Modul/Fitur | Admin | Admin Gudang | Kepala | Pegawai |
|-------------|:-----:|:------------:|:------:|:-------:|
| **Master Manajemen** |
| Master Pegawai | ✅ | ❌ | ❌ | ❌ |
| Master Jabatan | ✅ | ❌ | ❌ | ❌ |
| Unit Kerja | ✅ | ❌ | ❌ | ❌ |
| Gudang | ✅ | ✅ | ❌ | ❌ |
| Ruangan | ✅ | ❌ | ❌ | ❌ |
| Program/Kegiatan | ✅ | ❌ | ❌ | ❌ |
| **Master Data** |
| Aset/Kode/Kategori | ✅ | ❌ | ❌ | ❌ |
| Data Barang | ✅ | ✅ | ❌ | ❌ |
| Satuan/Anggaran | ✅ | ❌ | ❌ | ❌ |
| **Inventory** |
| Data Stock | ✅ | ✅ | ❌ | ❌ |
| Data Inventory | ✅ | ✅ | ❌ | ❌ |
| Inventory Item | ✅ | ✅ | ❌ | ❌ |
| **Transaksi** |
| Permintaan Barang (Create) | ✅ | ❌ | ❌ | ✅ |
| Permintaan Barang (View) | ✅ | ❌ | ✅ | ✅ |
| Approval (Approve/Reject) | ✅ | ❌ | ✅ | ❌ |
| Approval (View) | ✅ | ✅ | ✅ | ❌ |
| Distribusi/SBBK | ✅ | ✅ | ❌ | ❌ |
| Penerimaan Barang | ✅ | ✅ | ❌ | ✅ |
| **Asset & KIR** |
| Register Aset | ✅ | ✅ | ❌ | ❌ |
| **Planning** |
| RKU | ✅ | ❌ | ❌ | ❌ |
| **Procurement** |
| Paket Pengadaan | ✅ | ❌ | ❌ | ❌ |
| **Finance** |
| Pembayaran | ✅ | ❌ | ❌ | ❌ |
| **Reports** |
| Semua Laporan | ✅ | ❌ | ✅ | ❌ |
| Stock Gudang | ✅ | ✅ | ✅ | ❌ |
| **Admin** |
| Role Management | ✅ | ❌ | ❌ | ❌ |
| User Management | ✅ | ❌ | ❌ | ❌ |

---

## 💡 Contoh Skenario Penggunaan

### Skenario 1: Pegawai Membuat Permintaan Barang

**Aktor:** Pegawai (Pengadaan Barang)
**Role:** `pegawai`

**Langkah:**
1. Login ke sistem
2. Akses menu: **Transaksi > Permintaan Barang**
3. Klik **Tambah Permintaan**
4. Isi form:
   - Pilih Gudang Tujuan
   - Pilih Barang & Quantity
   - Alasan Permintaan
5. Klik **Simpan** (Status: DRAFT)
6. Klik **Ajukan** (Status: DIAJUKAN)

**Hasil:** Permintaan masuk ke menu Approval untuk ditinjau oleh Kepala.

---

### Skenario 2: Kepala Menyetujui Permintaan

**Aktor:** Kepala Pusat
**Role:** `kepala`

**Langkah:**
1. Login ke sistem
2. Akses menu: **Transaksi > Persetujuan**
3. Lihat daftar permintaan yang menunggu approval
4. Klik **Detail** untuk melihat detail permintaan
5. Review:
   - Barang yang diminta
   - Quantity
   - Alasan permintaan
   - Stock tersedia
6. Pilih:
   - **APPROVE** → Status: DISETUJUI (lanjut ke Admin Gudang)
   - **REJECT** → Status: DITOLAK (kembali ke Pegawai dengan alasan)

**Hasil:** 
- Jika APPROVE: Admin Gudang mendapat notifikasi untuk menyiapkan SBBK
- Jika REJECT: Pegawai mendapat notifikasi penolakan

---

### Skenario 3: Admin Gudang Menyiapkan Distribusi

**Aktor:** Pengurus Barang (Admin Gudang Pusat)
**Role:** `admin_gudang`

**Langkah:**
1. Login ke sistem
2. Lihat permintaan yang sudah DISETUJUI
3. Akses menu: **Transaksi > Distribusi (SBBK)**
4. Klik **Tambah Distribusi**
5. Pilih Permintaan Barang yang sudah disetujui
6. Sistem auto-fill:
   - No. Permintaan
   - Barang & Quantity
   - Gudang Asal & Tujuan
7. Generate No. SBBK (otomatis)
8. Pilih barang dari stock gudang
9. Klik **Simpan** (Status: DRAFT)
10. Klik **Kirim** (Status: TERKIRIM)
    - Stock otomatis ter-update (qty_keluar)

**Hasil:** Barang dikirim, stock berkurang, Admin Gudang Unit mendapat notifikasi.

---

### Skenario 4: Admin Gudang Unit Menerima Barang

**Aktor:** Admin Unit Kerja
**Role:** `admin_gudang`

**Langkah:**
1. Login ke sistem
2. Lihat distribusi yang sudah TERKIRIM
3. Akses menu: **Transaksi > Penerimaan Barang**
4. Klik **Tambah Penerimaan**
5. Pilih Distribusi/SBBK yang terkait
6. Sistem auto-fill:
   - No. SBBK
   - Barang & Quantity
   - Tanggal Distribusi
7. Input:
   - Tanggal Penerimaan
   - Quantity Diterima (bisa kurang dari quantity dikirim)
   - Kondisi Barang
8. Klik **Simpan** (Status: DITERIMA)
    - Stock otomatis ter-update (qty_masuk)

**Hasil:** Barang diterima, stock bertambah di gudang tujuan, proses selesai.

---

### Skenario 5: Admin Input Inventory Baru

**Aktor:** Admin Gudang
**Role:** `admin_gudang`

**Langkah:**
1. Login ke sistem
2. Akses menu: **Inventory > Data Inventory**
3. Klik **Tambah Inventory**
4. Isi form:
   - Pilih Barang
   - Jenis Inventory: ASET / PERSEDIAAN / FARMASI
   - Quantity
   - Harga Satuan
   - Gudang
   - Tahun Produksi
   - dll
5. Jika Jenis = ASET:
   - Sistem otomatis membuat Inventory Item per unit
   - Generate kode register: `[UNIT]/[KODE_BARANG]/[TAHUN]/[URUT]`
   - Generate QR Code
6. Klik **Simpan**
    - Stock otomatis ter-update (qty_masuk)

**Hasil:** Inventory baru tersimpan, stock bertambah, jika ASET maka register aset otomatis dibuat.

---

## 🔒 Implementasi Teknis

### 1. Middleware Protection
```php
// Contoh di routes/web.php
Route::prefix('inventory')
    ->middleware(['role:admin,admin_gudang'])
    ->group(function () {
        // Routes inventory
    });
```

### 2. Permission Check di Controller
```php
// Contoh di controller
public function index()
{
    if (!PermissionHelper::canAccess(auth()->user(), 'inventory.data-stock.index')) {
        abort(403, 'Unauthorized');
    }
    // ...
}
```

### 3. View Protection
```blade
@if(PermissionHelper::canAccess(auth()->user(), 'transaction.approval.index'))
    <a href="{{ route('transaction.approval.index') }}">Approval</a>
@endif
```

### 4. Role Auto-Assignment
```php
// Saat create/update pegawai
$jabatan = MasterJabatan::find($request->id_jabatan);
if ($jabatan->role_id) {
    $user->roles()->sync([$jabatan->role_id]);
}
```

---

## 📝 Catatan Penting

1. **Role Otomatis dari Jabatan**
   - Saat membuat/update pegawai, role user otomatis mengikuti jabatan
   - Jika jabatan diubah, role user otomatis di-update
   - Tidak perlu manual assign role

2. **Hierarki Approval**
   - Pegawai → Ajukan Permintaan
   - Kasubbag TU → Mengetahui
   - Kepala → Approve/Reject
   - Admin Gudang → Eksekusi Distribusi

3. **Stock Management**
   - Hanya Admin Gudang yang bisa input inventory
   - Stock otomatis ter-update saat:
     - Input inventory (qty_masuk)
     - Distribusi (qty_keluar)
     - Penerimaan (qty_masuk)

4. **Auto Register Aset**
   - Saat input inventory dengan jenis = ASET
   - Sistem otomatis membuat Inventory Item per unit
   - Generate kode register unik
   - Generate QR Code untuk tracking

5. **Middleware Protection**
   - Semua route dilindungi dengan middleware role
   - User tanpa role yang sesuai akan mendapat error 403
   - Admin selalu memiliki akses penuh

6. **Sidebar Menu**
   - Menu ditampilkan sesuai role user
   - Menggunakan `accessibleMenus` dari `PermissionHelper`
   - Menu yang tidak diizinkan tidak akan muncul

---

**Dokumen ini akan selalu di-update sesuai perkembangan sistem.**

