DASHBOARD
│
├── Master Manajemen
│   ├── Unit Kerja
│   ├── Lokasi
│   ├── Ruangan
│   ├── Gudang
│   ├── Program              ← Master Perencanaan
│   ├── Kegiatan             ← Master Perencanaan
│   └── Sub Kegiatan         ← WAJIB di Perencanaan
│
├── Master Data
│   ├── Aset
│   ├── Kode Barang
│   ├── Kategori Barang
│   ├── Jenis Barang
│   ├── Sub Jenis Barang
│   ├── Data Barang
│   ├── Satuan
│   └── Sumber Anggaran
│
├── 🟦 PERENCANAAN
│   ├── Rencana Kebutuhan Unit (RKU)
│   │   ├── Barang
│   │   └── Aset
│   ├── Pengajuan Perencanaan
│   ├── Persetujuan Pimpinan Unit   ⭐
│   ├── Rekap Perencanaan Tahunan
│   └── Status Perencanaan
│
├── 🟧 PENGADAAN
│   ├── Paket Pengadaan
│   ├── Proses Pengadaan
│   ├── Kontrak / SP / PO
│   └── Monitoring Pengadaan
│
├── 🟨 KEUANGAN & PEMBAYARAN
│   ├── Verifikasi Dokumen Pengadaan
│   ├── Pembayaran
│   │   ├── Uang Muka
│   │   ├── Termin
│   │   └── Pelunasan
│   ├── Realisasi Anggaran (per Sub Kegiatan)
│   └── Laporan Pembayaran
│
├── Inventory
│   ├── Stock Gudang
│   ├── Batch Barang
│   ├── Stock Adjustment
│   └── Stock History
│
├── 🟫 PERMINTAAN & TRANSAKSI
│   ├── Permintaan Barang            ⭐
│   ├── Persetujuan Kepala           ⭐
│   ├── Distribusi Barang (SBBK)     ← KELUAR GUDANG
│   ├── Penerimaan Barang            ← KONFIRMASI UNIT
│   ├── Retur
│   └── Pemakaian
│
├── 🟩 ASET & KIR
│   ├── Register Aset
│   ├── Kartu Inventaris Ruangan (KIR)
│   └── Mutasi Aset
│
├── 🟪 PEMELIHARAAN & KALIBRASI
│   ├── Permintaan Pemeliharaan   ⭐
│   ├── Persetujuan Kepala        ⭐
│   ├── Jadwal Maintenance
│   ├── Kalibrasi Aset
│   ├── Service Report
│   └── Riwayat Pemeliharaan
│
├── Laporan
│   ├── Perencanaan
│   │   ├── per Program
│   │   ├── per Kegiatan
│   │   └── per Sub Kegiatan
│   ├── Pengadaan
│   ├── Keuangan & Realisasi
│   ├── Stock
│   ├── Transaksi
│   ├── Aset & KIR
│   ├── Pemeliharaan & Kalibrasi
│   └── BMD / Audit
│
└── Settings
    ├── User & Role
    ├── Hak Akses
    └── Notifikasi
