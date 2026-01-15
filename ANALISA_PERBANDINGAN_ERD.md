# Analisa Perbandingan ERD SISTEM.MD vs ERD SISTEM copy.MD

## 📊 Ringkasan Perbedaan

### **ERD SISTEM.MD** (File Utama)
- **Lebih lengkap dan detail** dengan 32+ tabel
- Struktur lebih kompleks dengan banyak relasi
- Fokus pada sistem enterprise dengan workflow lengkap

### **ERD SISTEM copy.MD** (File Copy)
- **Lebih sederhana** dengan ~15 tabel
- Struktur lebih ringkas
- Fokus pada core functionality

---

## 🔍 Perbedaan Detail per Tabel

### 1. **master_unit_kerja**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| kode_unit_kerja | ✅ Ada | ❌ Tidak ada |
| nama_unit_kerja | ✅ Ada | ✅ Ada |

### 2. **master_gudang**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| jenis_gudang | ✅ PUSAT/UNIT | ✅ PUSAT/UNIT |
| kategori_gudang | ❌ Tidak ada | ✅ ASET/PERSEDIAAN/FARMASI |
| nama_gudang | ✅ Ada | ✅ Ada |

**⚠️ PENTING**: ERD copy.MD menambahkan `kategori_gudang` yang bisa mempengaruhi logika gudang.

### 3. **master_ruangan**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| kode_ruangan | ✅ Ada | ❌ Tidak ada |
| nama_ruangan | ✅ Ada | ✅ Ada |

### 4. **master_sub_kegiatan**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| kode_sub_kegiatan | ✅ Ada | ❌ Tidak ada |
| nama_sub_kegiatan | ✅ Ada | ✅ Ada |

### 5. **master_kategori_barang**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| kode_kategori_barang | ✅ Ada | ❌ Tidak ada |
| nama_kategori_barang | ✅ Ada | ✅ Ada |

### 6. **master_jenis_barang**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| kode_jenis_barang | ✅ Ada | ❌ Tidak ada |
| nama_jenis_barang | ✅ Ada | ✅ Ada |

### 7. **master_subjenis_barang**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| kode_subjenis_barang | ✅ Ada | ❌ Tidak ada |
| nama_subjenis_barang | ✅ Ada | ✅ Ada |

### 8. **master_data_barang**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| deskripsi | ✅ Ada | ❌ Tidak ada |
| upload_foto | ✅ Ada | ❌ Tidak ada |
| spesifikasi | ❌ Tidak ada | ✅ Ada |
| merk | ❌ Tidak ada | ✅ Ada |
| tipe | ❌ Tidak ada | ✅ Ada |
| tahun_produksi | ❌ Tidak ada | ✅ Ada |
| foto_barang | ❌ Tidak ada | ✅ Ada |

**⚠️ PENTING**: ERD copy.MD memindahkan informasi teknis (merk, tipe, spesifikasi, tahun_produksi) ke `master_data_barang`, sedangkan ERD.MD menyimpannya di `data_inventory`.

### 9. **data_stock**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| qty_awal | ✅ Ada | ❌ Tidak ada |
| qty_masuk | ✅ Ada | ❌ Tidak ada |
| qty_keluar | ✅ Ada | ❌ Tidak ada |
| qty_akhir | ✅ Ada | ❌ Tidak ada |
| qty_total | ❌ Tidak ada | ✅ Ada |

**⚠️ PENTING**: ERD.MD lebih detail dengan tracking qty_awal/masuk/keluar/akhir, sedangkan ERD copy.MD hanya qty_total.

### 10. **permintaan_barang**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| no_permintaan | ✅ Ada | ❌ Tidak ada |
| id_pemohon | ✅ Ada | ❌ Tidak ada |
| jenis_permintaan | ✅ BARANG/ASET | ❌ Tidak ada |
| keterangan | ✅ Ada | ❌ Tidak ada |
| id_kepala | ❌ Tidak ada | ✅ Ada |
| catatan | ❌ Tidak ada | ✅ Ada |

**⚠️ PENTING**: ERD.MD lebih lengkap dengan tracking pemohon dan jenis permintaan.

### 11. **detail_permintaan_barang**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| id_satuan | ✅ Ada | ❌ Tidak ada |
| keterangan | ✅ Ada | ❌ Tidak ada |
| qty_diminta | ✅ Ada | ✅ qty |

### 12. **transaksi_distribusi**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| id_permintaan | ✅ Ada | ❌ Tidak ada |
| tanggal_distribusi | ✅ datetime | ✅ date (tanggal) |
| id_pegawai_pengirim | ✅ Ada | ✅ id_petugas |
| status_distribusi | ✅ DRAFT/DIKIRIM/SELESAI | ✅ status (enum) |
| keterangan | ✅ Ada | ❌ Tidak ada |

### 13. **detail_distribusi**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| id_inventory | ✅ Ada | ❌ Tidak ada |
| id_item | ❌ Tidak ada | ✅ Ada (bisa NULL) |
| qty_distribusi | ✅ Ada | ✅ qty |
| id_satuan | ✅ Ada | ❌ Tidak ada |
| harga_satuan | ✅ Ada | ❌ Tidak ada |
| subtotal | ✅ Ada | ❌ Tidak ada |
| keterangan | ✅ Ada | ❌ Tidak ada |

**⚠️ PENTING**: ERD copy.MD menggunakan `id_item` (bisa NULL untuk persediaan), sedangkan ERD.MD menggunakan `id_inventory`.

### 14. **register_aset**
| Field | ERD.MD | ERD copy.MD |
|-------|--------|-------------|
| id_inventory | ✅ Ada | ❌ Tidak ada |
| id_item | ❌ Tidak ada | ✅ Ada |
| id_unit_kerja | ✅ Ada | ❌ Tidak ada |
| id_lokasi | ✅ Ada | ❌ Tidak ada |
| id_ruangan | ❌ Tidak ada | ✅ Ada |
| nomor_register | ✅ Ada | ✅ Ada |
| kondisi_aset | ✅ BAIK/RUSAK_RINGAN/RUSAK_BERAT | ✅ kondisi (enum) |
| tanggal_perolehan | ✅ Ada | ❌ Tidak ada |
| status_aset | ✅ AKTIF/NONAKTIF | ❌ Tidak ada |

**⚠️ PENTING**: ERD.MD lebih lengkap dengan tracking unit kerja, lokasi, dan status aset.

---

## 📋 Tabel yang Hanya Ada di ERD.MD

1. **master_pegawai** - Data pegawai dengan NIP, email, dll
2. **master_jabatan** - Data jabatan
3. **data_stock_opname** - Stock opname/audit
4. **penerimaan_barang** - Konfirmasi penerimaan dari distribusi
5. **detail_penerimaan_barang** - Detail penerimaan
6. **kartu_inventaris_ruangan (KIR)** - Kartu inventaris per ruangan
7. **mutasi_aset** - Mutasi/pindah lokasi aset
8. **history_lokasi** - History perpindahan lokasi

---

## 📋 Tabel yang Hanya Ada di ERD copy.MD

1. **pemeliharaan_aset** - Pemeliharaan/kalibrasi aset
   - id_item FK
   - jenis_pemeliharaan (RUTIN/KALIBRASI/PERBAIKAN)
   - tanggal, vendor, biaya, laporan_service

---

## 🎯 Rekomendasi

### **Gunakan ERD SISTEM.MD sebagai Base** karena:
1. ✅ Lebih lengkap dan enterprise-ready
2. ✅ Memiliki workflow lengkap (permintaan → distribusi → penerimaan)
3. ✅ Memiliki tracking history dan audit trail
4. ✅ Memiliki KIR dan mutasi aset
5. ✅ Memiliki stock opname untuk audit

### **Ambil dari ERD copy.MD**:
1. ✅ **kategori_gudang** di `master_gudang` - Berguna untuk klasifikasi gudang
2. ✅ **Informasi teknis di master_data_barang** - Lebih efisien jika data teknis disimpan di master
3. ✅ **Tabel pemeliharaan_aset** - Penting untuk modul pemeliharaan

### **Keputusan yang Perlu Diambil**:

1. **Lokasi Informasi Teknis**:
   - **Opsi A**: Simpan di `master_data_barang` (ERD copy.MD) - Data teknis default per barang
   - **Opsi B**: Simpan di `data_inventory` (ERD.MD) - Data teknis per batch/inventory
   - **Rekomendasi**: **Hybrid** - Default di `master_data_barang`, bisa override di `data_inventory`

2. **Detail Distribusi**:
   - **ERD.MD**: Menggunakan `id_inventory` (untuk semua jenis)
   - **ERD copy.MD**: Menggunakan `id_item` (NULL untuk persediaan)
   - **Rekomendasi**: **Gunakan ERD.MD** karena lebih konsisten

3. **Register Aset**:
   - **ERD.MD**: Relasi ke `id_inventory` + `id_unit_kerja` + `id_lokasi`
   - **ERD copy.MD**: Relasi ke `id_item` + `id_ruangan`
   - **Rekomendasi**: **Gunakan ERD.MD** karena lebih fleksibel

---

## ✅ Kesimpulan

**ERD SISTEM.MD** lebih cocok sebagai base karena lebih lengkap. Namun, ada beberapa fitur dari **ERD copy.MD** yang bisa diintegrasikan:
- Tambahkan `kategori_gudang` ke `master_gudang`
- Pertimbangkan memindahkan informasi teknis ke `master_data_barang`
- Tambahkan tabel `pemeliharaan_aset`

**Migration yang sudah dibuat mengikuti ERD.MD**, jadi perlu review apakah perlu penyesuaian.

