# 📋 Rencana Langkah Selanjutnya - SI-MANTIK

**Tanggal:** 30 Januari 2026  
**Versi Sistem:** SI-MANTIK v1.0  
**Status:** Development Phase

---

## 🎯 Tujuan Dokumen

Dokumen ini berisi rencana langkah selanjutnya untuk:
1. Melengkapi fitur yang belum terealisasi
2. Memperbaiki masalah yang ada di sistem
3. Meningkatkan kualitas kode dan dokumentasi

---

## 📊 Status Sistem Saat Ini

### ✅ Fitur yang Sudah Lengkap
- ✅ QR Code Generation untuk Inventory Item
- ✅ Register Aset - Create & Store Logic
- ✅ Permintaan Barang dengan Approval Flow
- ✅ Distribusi Barang (SBBK)
- ✅ Penerimaan Barang
- ✅ Approval System (Multi-level)
- ✅ Master Data Management
- ✅ User & Role Management
- ✅ Permission System

### ⚠️ Fitur yang Perlu Perbaikan
- ⚠️ Register Aset - Update Logic (ada TODO comment)
- ⚠️ Retur Barang (perlu review kelengkapan)
- ⚠️ Stock Adjustment & History (belum lengkap)
- ⚠️ Laporan per Modul (perlu dilengkapi)

### ❌ Fitur yang Belum Ada / Perlu Verifikasi
- ⚠️ Kartu Inventaris Ruangan (KIR) - CRUD (Controller ada, perlu verifikasi kelengkapan)
- ⚠️ Mutasi Aset - CRUD (Controller ada, perlu verifikasi kelengkapan)
- ⚠️ Pemakaian Barang (Controller ada, perlu verifikasi kelengkapan)
- ⚠️ Stock Adjustment UI (Controller ada, perlu verifikasi kelengkapan)
- ❌ Perencanaan - Rekap Tahunan
- ❌ Pengadaan - Proses & Kontrak/SP/PO
- ❌ Keuangan - Verifikasi Dokumen & Realisasi Anggaran
- ❌ Pemeliharaan - Riwayat Pemeliharaan
- ❌ Notifikasi System

---

## 🔴 PRIORITAS TINGGI (Sprint 1 - 2-3 Minggu)

### 1. Perbaikan Fitur Existing

#### 1.1 Register Aset - Update Logic
**Status:** ⚠️ Partial Implementation  
**File:** `app/Http/Controllers/Asset/RegisterAsetController.php:326`

**Tugas:**
- [ ] Review method `update()` di RegisterAsetController
- [ ] Lengkapi validasi untuk semua field yang bisa di-update
- [ ] Pastikan logic update sudah lengkap
- [ ] Tambahkan audit trail jika diperlukan
- [ ] Test semua skenario update

**Estimasi:** 1-2 hari

---

#### 1.2 Stock Adjustment & History
**Status:** ❌ Belum Ada UI  
**File:** Tabel `stock_adjustment` sudah ada di database

**Tugas:**
- [ ] Buat controller `StockAdjustmentController`
- [ ] Buat views: index, create, edit, show
- [ ] Tambahkan routes di `web.php`
- [ ] Implementasi approval flow jika diperlukan
- [ ] Buat history view untuk tracking perubahan stock
- [ ] Integrasi dengan semua transaksi stock (masuk/keluar)

**Estimasi:** 3-4 hari

---

#### 1.3 Retur Barang - Review & Lengkapi
**Status:** ⚠️ Perlu Verifikasi  
**File:** `app/Http/Controllers/Transaction/ReturBarangController.php`

**Tugas:**
- [ ] Review implementasi retur barang secara menyeluruh
- [ ] Pastikan semua status retur ter-handle (DRAFT, DIAJUKAN, DITERIMA, DITOLAK)
- [ ] Pastikan stock update saat retur diterima
- [ ] Verifikasi workflow sesuai dokumentasi
- [ ] Test semua skenario retur

**Estimasi:** 2-3 hari

---

### 2. Fitur Baru Prioritas Tinggi

#### 2.1 Kartu Inventaris Ruangan (KIR)
**Status:** ❌ Belum Diimplementasi  
**Dampak:** Fitur penting untuk tracking aset per ruangan

**Tugas:**
- [ ] Review migration untuk tabel `kartu_inventaris_ruangan`
- [ ] Buat controller `KartuInventarisRuanganController`
- [ ] Buat views: index, create, edit, show
- [ ] Tambahkan routes di `web.php`
- [ ] Integrasi dengan Register Aset
- [ ] Tambahkan filter berdasarkan ruangan, unit kerja
- [ ] Tambahkan export Excel/PDF untuk KIR

**Estimasi:** 4-5 hari

---

#### 2.2 Mutasi Aset
**Status:** ❌ Belum Diimplementasi  
**Dampak:** Tidak bisa mencatat perpindahan aset

**Tugas:**
- [ ] Review migration untuk tabel `mutasi_aset`
- [ ] Buat controller `MutasiAsetController`
- [ ] Buat views: index, create, edit, show
- [ ] Tambahkan routes di `web.php`
- [ ] Integrasi dengan Register Aset dan KIR
- [ ] Tambahkan approval flow jika diperlukan
- [ ] Update KIR otomatis saat mutasi

**Estimasi:** 4-5 hari

---

#### 2.3 Pemakaian Barang
**Status:** ❌ Belum Diimplementasi  
**Dampak:** Tidak bisa mencatat pemakaian barang

**Tugas:**
- [ ] Review migration untuk tabel `pemakaian_barang`
- [ ] Buat controller `PemakaianBarangController`
- [ ] Buat views: index, create, edit, show
- [ ] Tambahkan routes di `web.php`
- [ ] Integrasi dengan inventory untuk update stock
- [ ] Tambahkan filter dan search
- [ ] Tambahkan laporan pemakaian

**Estimasi:** 3-4 hari

---

## 🟡 PRIORITAS MENENGAH (Sprint 2 - 3-4 Minggu)

### 3. Fitur Perencanaan

#### 3.1 Rekap Perencanaan Tahunan
**Status:** ❌ Belum Diimplementasi

**Tugas:**
- [ ] Buat method `rekapTahunan()` di `RkuController` atau controller baru
- [ ] Buat view untuk rekap perencanaan tahunan
- [ ] Tambahkan filter berdasarkan tahun
- [ ] Tampilkan data per Program, Kegiatan, Sub Kegiatan
- [ ] Tambahkan export Excel
- [ ] Tambahkan grafik/chart jika diperlukan

**Estimasi:** 3-4 hari

---

#### 3.2 Status Perencanaan
**Status:** ❌ Belum Diimplementasi

**Tugas:**
- [ ] Buat view untuk status perencanaan
- [ ] Tampilkan status semua RKU
- [ ] Tambahkan filter berdasarkan status
- [ ] Integrasi dengan approval flow

**Estimasi:** 2-3 hari

---

### 4. Fitur Pengadaan

#### 4.1 Proses Pengadaan
**Status:** ❌ Belum Diimplementasi

**Tugas:**
- [ ] Buat controller `ProsesPengadaanController`
- [ ] Buat migration untuk `proses_pengadaan` jika belum ada
- [ ] Buat views: index, create, edit, show
- [ ] Tambahkan routes di `web.php`
- [ ] Integrasi dengan Paket Pengadaan
- [ ] Tambahkan workflow proses pengadaan

**Estimasi:** 4-5 hari

---

#### 4.2 Kontrak/SP/PO
**Status:** ❌ Belum Diimplementasi

**Tugas:**
- [ ] Review migration untuk tabel `kontrak`
- [ ] Buat controller `KontrakController`
- [ ] Buat views: index, create, edit, show
- [ ] Tambahkan routes di `web.php`
- [ ] Handle upload dokumen kontrak/SP/PO
- [ ] Integrasi dengan Paket Pengadaan dan Proses Pengadaan
- [ ] Tambahkan tracking status kontrak

**Estimasi:** 5-6 hari

---

#### 4.3 Monitoring Pengadaan
**Status:** ❌ Belum Diimplementasi

**Tugas:**
- [ ] Buat dashboard monitoring pengadaan
- [ ] Tampilkan status pengadaan, progress
- [ ] Tambahkan filter dan search
- [ ] Tambahkan grafik/chart
- [ ] Integrasi dengan semua modul pengadaan

**Estimasi:** 3-4 hari

---

### 5. Fitur Keuangan

#### 5.1 Verifikasi Dokumen Pengadaan
**Status:** ❌ Belum Diimplementasi

**Tugas:**
- [ ] Buat controller `VerifikasiDokumenController`
- [ ] Buat migration untuk `verifikasi_dokumen` jika diperlukan
- [ ] Buat views: index, create, edit, show
- [ ] Tambahkan routes di `web.php`
- [ ] Integrasi dengan Pengadaan dan Pembayaran
- [ ] Tambahkan workflow verifikasi

**Estimasi:** 4-5 hari

---

#### 5.2 Realisasi Anggaran per Sub Kegiatan
**Status:** ❌ Belum Diimplementasi

**Tugas:**
- [ ] Buat controller `RealisasiAnggaranController`
- [ ] Buat view untuk realisasi anggaran
- [ ] Integrasi dengan Sub Kegiatan
- [ ] Tampilkan perbandingan anggaran vs realisasi
- [ ] Tambahkan export Excel
- [ ] Tambahkan grafik/chart

**Estimasi:** 4-5 hari

---

### 6. Fitur Pemeliharaan

#### 6.1 Riwayat Pemeliharaan
**Status:** ❌ Belum Diimplementasi

**Tugas:**
- [ ] Buat controller atau tambahkan method di `PermintaanPemeliharaanController`
- [ ] Buat view untuk riwayat pemeliharaan
- [ ] Tampilkan history pemeliharaan per aset
- [ ] Tambahkan filter dan search
- [ ] Tambahkan export Excel

**Estimasi:** 3-4 hari

---

## 🟢 PRIORITAS RENDAH (Sprint 3 - 4-6 Minggu)

### 7. Laporan Lengkap

#### 7.1 Laporan Per Modul
**Status:** ⚠️ Partial Implementation  
**File:** `app/Http/Controllers/Report/ReportController.php`

**Tugas:**
- [ ] Review semua laporan yang ada
- [ ] Lengkapi laporan yang belum ada:
  - [ ] Laporan Perencanaan (per Program, Kegiatan, Sub Kegiatan)
  - [ ] Laporan Pengadaan
  - [ ] Laporan Keuangan & Realisasi
  - [ ] Laporan Stock
  - [ ] Laporan Transaksi
  - [ ] Laporan Aset & KIR
  - [ ] Laporan Pemeliharaan & Kalibrasi
  - [ ] Laporan BMD / Audit
- [ ] Pastikan semua laporan bisa di-export Excel
- [ ] Tambahkan filter dan parameter untuk setiap laporan

**Estimasi:** 8-10 hari

---

### 8. Sistem Notifikasi

#### 8.1 Notifikasi System
**Status:** ❌ Belum Diimplementasi

**Tugas:**
- [ ] Buat migration untuk tabel `notifications`
- [ ] Buat model `Notification`
- [ ] Buat controller `NotificationController`
- [ ] Buat view untuk daftar notifikasi
- [ ] Implementasi notifikasi untuk:
  - [ ] Approval yang menunggu
  - [ ] Permintaan yang disetujui/ditolak
  - [ ] Distribusi yang sudah dikirim
  - [ ] Penerimaan yang perlu dikonfirmasi
- [ ] Tambahkan real-time notification jika memungkinkan
- [ ] Tambahkan badge counter di menu

**Estimasi:** 6-8 hari

---

## 🔧 PERBAIKAN & TECHNICAL DEBT (Sprint 4 - 2-3 Minggu)

### 9. Error Handling & Validation

**Tugas:**
- [ ] Review semua controller untuk error handling
- [ ] Pastikan validasi form sudah lengkap
- [ ] Tambahkan custom error messages
- [ ] Pastikan error messages user-friendly
- [ ] Tambahkan logging untuk error yang penting
- [ ] Buat custom exception handler jika diperlukan

**Estimasi:** 4-5 hari

---

### 10. Code Quality

**Tugas:**
- [ ] Hapus debug comments yang tidak diperlukan
- [ ] Refactor code yang duplikat
- [ ] Tambahkan type hints di semua method
- [ ] Pastikan semua method memiliki dokumentasi
- [ ] Review dan perbaiki code smell
- [ ] Setup PHPStan atau Psalm untuk static analysis

**Estimasi:** 5-6 hari

---

### 11. Testing

**Tugas:**
- [ ] Setup PHPUnit
- [ ] Buat unit test untuk models
- [ ] Buat feature test untuk controllers
- [ ] Buat test untuk business logic
- [ ] Setup CI/CD untuk automated testing
- [ ] Target coverage minimal 60%

**Estimasi:** 8-10 hari

---

### 12. Dokumentasi

**Tugas:**
- [ ] Update README dengan informasi terbaru
- [ ] Lengkapi dokumentasi API jika ada
- [ ] Buat user manual (step-by-step guide)
- [ ] Buat developer guide
- [ ] Dokumentasi deployment
- [ ] Dokumentasi troubleshooting

**Estimasi:** 5-6 hari

---

## 🐛 BUG & ISSUE YANG PERLU DIPERBAIKI

### 1. Debug Comments
**Lokasi:** Beberapa controller masih ada debug comments

**File yang perlu dibersihkan:**
- `app/Http/Controllers/Transaction/DraftDistribusiController.php`
- `app/Http/Controllers/Transaction/PermintaanBarangController.php`

**Tugas:**
- [ ] Hapus semua debug comments
- [ ] Ganti dengan proper logging jika diperlukan

---

### 2. Route untuk Approval Pemeliharaan
**Lokasi:** `app/Helpers/PermissionHelper.php:376`

**Issue:** Route untuk approval pemeliharaan dan pengadaan belum dibuat

**Tugas:**
- [ ] Buat route untuk approval pemeliharaan
- [ ] Buat route untuk approval pengadaan
- [ ] Update PermissionHelper

---

## 📅 TIMELINE ESTIMASI

### Sprint 1 (2-3 Minggu) - Prioritas Tinggi
- Register Aset Update Logic: 1-2 hari
- Stock Adjustment & History: 3-4 hari
- Retur Barang Review: 2-3 hari
- Kartu Inventaris Ruangan: 4-5 hari
- Mutasi Aset: 4-5 hari
- Pemakaian Barang: 3-4 hari

**Total:** ~18-23 hari kerja

---

### Sprint 2 (3-4 Minggu) - Prioritas Menengah
- Rekap Perencanaan Tahunan: 3-4 hari
- Status Perencanaan: 2-3 hari
- Proses Pengadaan: 4-5 hari
- Kontrak/SP/PO: 5-6 hari
- Monitoring Pengadaan: 3-4 hari
- Verifikasi Dokumen: 4-5 hari
- Realisasi Anggaran: 4-5 hari
- Riwayat Pemeliharaan: 3-4 hari

**Total:** ~28-36 hari kerja

---

### Sprint 3 (4-6 Minggu) - Prioritas Rendah
- Laporan Lengkap: 8-10 hari
- Sistem Notifikasi: 6-8 hari

**Total:** ~14-18 hari kerja

---

### Sprint 4 (2-3 Minggu) - Technical Debt
- Error Handling: 4-5 hari
- Code Quality: 5-6 hari
- Testing: 8-10 hari
- Dokumentasi: 5-6 hari

**Total:** ~22-27 hari kerja

---

## 🎯 REKOMENDASI PRIORITAS IMPLEMENTASI

### Fase 1: Core Features (Sprint 1)
Fokus pada fitur yang paling penting untuk operasional:
1. Kartu Inventaris Ruangan (KIR)
2. Mutasi Aset
3. Stock Adjustment & History
4. Pemakaian Barang

### Fase 2: Business Process (Sprint 2)
Fokus pada proses bisnis yang belum lengkap:
1. Proses Pengadaan & Kontrak
2. Verifikasi Dokumen & Realisasi Anggaran
3. Rekap Perencanaan

### Fase 3: Enhancement (Sprint 3)
Fokus pada peningkatan fitur:
1. Laporan Lengkap
2. Sistem Notifikasi

### Fase 4: Quality & Maintenance (Sprint 4)
Fokus pada kualitas kode dan maintenance:
1. Testing
2. Error Handling
3. Dokumentasi

---

## 📝 CATATAN PENTING

1. **Database Schema:** Sebelum implementasi fitur baru, pastikan database schema sudah sesuai dengan ERD. Beberapa fitur mungkin memerlukan migration baru.

2. **Dependencies:** Beberapa fitur memiliki dependencies dengan fitur lain. Pastikan urutan implementasi sesuai.

3. **Testing:** Setiap fitur baru harus di-test sebelum di-deploy ke production.

4. **Dokumentasi:** Update dokumentasi setiap kali ada perubahan atau penambahan fitur.

5. **Code Review:** Lakukan code review untuk setiap perubahan sebelum merge ke main branch.

---

## 🔄 UPDATE DOKUMEN

Dokumen ini akan di-update secara berkala sesuai progress development. Setiap sprint akan di-review dan di-update statusnya.

**Last Updated:** 30 Januari 2026

---

**SI-MANTIK** - Sistem Informasi Manajemen Terintegrasi © 2026
