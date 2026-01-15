============MASTER DATA============

master_unit_kerja {
    int id_unit_kerja PK
    varchar nama_unit_kerja
}

master_lokasi {
    int id_lokasi PK
    int id_unit_kerja FK
    varchar kode_lokasi
    varchar nama_lokasi
}

master_ruangan {
    int id_ruangan PK
    int id_lokasi FK
    varchar nama_ruangan
}

master_gudang {
    int id_gudang PK
    int id_lokasi FK
    enum jenis_gudang       -- PUSAT / UNIT
    enum kategori_gudang    -- ASET / PERSEDIAAN / FARMASI
    varchar nama_gudang
}

master_program {
    int id_program PK
    varchar nama_program
}

master_kegiatan {
    int id_kegiatan PK
    int id_program FK
    varchar nama_kegiatan
}

master_sub_kegiatan {
    int id_sub_kegiatan PK
    int id_kegiatan FK
    varchar nama_sub_kegiatan
}


============MASTER BARANG============

master_aset {
    int id_aset PK
    varchar nama_aset
}

master_kode_barang {
    int id_kode_barang PK
    int id_aset FK
    varchar kode_barang
    varchar nama_kode_barang
}

master_kategori_barang {
    int id_kategori_barang PK
    int id_kode_barang FK
    varchar nama_kategori_barang
}

master_jenis_barang {
    int id_jenis_barang PK
    int id_kategori_barang FK
    varchar nama_jenis_barang
}

master_subjenis_barang {
    int id_subjenis_barang PK
    int id_jenis_barang FK
    varchar nama_subjenis_barang
}

master_satuan {
    int id_satuan PK
    varchar nama_satuan
}

master_data_barang {
    int id_data_barang PK
    int id_subjenis_barang FK
    int id_satuan FK
    varchar kode_data_barang
    varchar nama_barang
    text spesifikasi
    varchar merk
    varchar tipe
    int tahun_produksi
    varchar foto_barang
}

master_sumber_anggaran {
    int id_anggaran PK
    varchar nama_anggaran
}


============INVENTORY HEADER============

data_inventory {
    int id_inventory PK

    -- Relasi utama
    int id_data_barang FK
    int id_gudang FK
    int id_anggaran FK
    int id_sub_kegiatan FK

    -- Klasifikasi
    enum jenis_inventory         -- ASET | PERSEDIAAN | FARMASI
    int tahun_anggaran

    -- Informasi kuantitas & harga
    decimal qty_input
    int id_satuan FK
    decimal harga_satuan
    decimal total_harga

    -- Informasi teknis (diambil dari FORM)
    varchar merk
    varchar tipe
    text spesifikasi
    int tahun_produksi

    -- Informasi batch / seri (DINAMIS)
    varchar no_seri              -- NULL jika bukan ASET
    varchar no_batch             -- NULL jika ASET
    date tanggal_kedaluwarsa     -- NULL jika ASET

    -- Status & dokumen
    enum status_inventory        -- DRAFT | AKTIF | DISTRIBUSI | HABIS
    varchar upload_dokumen       -- BA / Faktur / SP
    text auto_qr_code

    -- Audit
    int created_by FK
    datetime created_at
    datetime updated_at
}


============INVENTORY ITEM (AUTO ADD ROW ASET)============

inventory_item {
    int id_item PK
    int id_inventory FK
    varchar kode_register (UNIQUE)
    varchar no_seri
    enum kondisi_item
    enum status_item
    int id_gudang FK
    int id_ruangan FK
    text qr_code
    datetime created_at
}


##LOGIKA##
Jenis	    | Perilaku
----------------------------------------
ASET	    | Auto create qty_input row
PERSEDIAAN	| Tidak buat item
FARMASI	    | Tidak buat item


============STOCK (AGREGAT)============

data_stock {
    int id_stock PK
    int id_data_barang FK
    int id_gudang FK
    decimal qty_total
    int id_satuan FK
    datetime last_updated
}

============PERMINTAAN & TRANSAKSI============

permintaan_barang {
    int id_permintaan PK
    int id_unit_kerja FK
    date tanggal_permintaan
    enum status_permintaan   -- DRAFT / DIAJUKAN / DISETUJUI / DITOLAK
    int id_kepala FK
    text catatan
}

detail_permintaan {
    int id_detail PK
    int id_permintaan FK
    int id_data_barang FK
    decimal qty
}

============DISTRIBUSI (SBBK)============

transaksi_distribusi {
    int id_distribusi PK
    varchar no_sbbk
    date tanggal
    int id_gudang_asal FK
    int id_gudang_tujuan FK
    int id_petugas FK
    enum status
}

detail_distribusi {
    int id_detail PK
    int id_distribusi FK
    int id_item FK       -- NULL jika persediaan
    decimal qty
}


============ASET & KIR (DIPISAH)============

register_aset {
    int id_register PK
    int id_item FK
    int id_ruangan FK
    varchar nomor_register
    enum kondisi
}


============PEMELIHARAAN / KALIBRASI============

pemeliharaan_aset {
    int id_pemeliharaan PK
    int id_item FK
    enum jenis_pemeliharaan     -- RUTIN / KALIBRASI / PERBAIKAN
    date tanggal
    varchar vendor
    decimal biaya
    varchar laporan_service
}

============APPROVAL GLOBAL============

approval_log {
    int id_approval PK
    varchar modul            -- PERMINTAAN / PERENCANAAN / PEMELIHARAAN
    int id_referensi
    int id_approver FK
    enum status
    datetime tanggal
}





==================================================================



%% =====================
%% MASTER MANAJEMEN
%% =====================
UNIT_KERJA ||--o{ LOKASI : memiliki
LOKASI ||--o{ RUANGAN : memiliki
UNIT_KERJA ||--o{ GUDANG : memiliki

PROGRAM ||--o{ KEGIATAN : memiliki
KEGIATAN ||--o{ SUB_KEGIATAN : memiliki

%% =====================
%% MASTER DATA BARANG
%% =====================
ASET ||--o{ KODE_BARANG : klasifikasi
KODE_BARANG ||--o{ KATEGORI_BARANG : klasifikasi
KATEGORI_BARANG ||--o{ JENIS_BARANG : klasifikasi
JENIS_BARANG ||--o{ SUBJENIS_BARANG : klasifikasi
SUBJENIS_BARANG ||--o{ DATA_BARANG : detail

SATUAN ||--o{ DATA_BARANG : satuan
SUMBER_ANGGARAN ||--o{ DATA_INVENTORY : sumber

%% =====================
%% PERENCANAAN
%% =====================
UNIT_KERJA ||--o{ RKU_HEADER : mengajukan
SUB_KEGIATAN ||--o{ RKU_HEADER : dasar
RKU_HEADER ||--o{ RKU_DETAIL : memiliki
DATA_BARANG ||--o{ RKU_DETAIL : direncanakan

%% =====================
%% PENGADAAN & KEUANGAN
%% =====================
SUB_KEGIATAN ||--o{ PENGADAAN_PAKET : dasar
PENGADAAN_PAKET ||--|| KONTRAK : menghasilkan
KONTRAK ||--o{ PEMBAYARAN : dibayar

%% =====================
%% INVENTORY
%% =====================
DATA_BARANG ||--o{ DATA_INVENTORY : dicatat
GUDANG ||--o{ DATA_INVENTORY : menyimpan

%% =====================
%% TRANSAKSI BARANG
%% =====================
UNIT_KERJA ||--o{ PERMINTAAN_BARANG : mengajukan
PERMINTAAN_BARANG ||--|| DISTRIBUSI_SBBK : direalisasi
DISTRIBUSI_SBBK ||--|| PENERIMAAN_BARANG : diterima

%% =====================
%% ASET & KIR
%% =====================
DATA_INVENTORY ||--o{ REGISTER_ASET : menjadi
RUANGAN ||--o{ REGISTER_ASET : ditempatkan
REGISTER_ASET ||--o{ MUTASI_ASET : mutasi

%% =====================
%% PEMELIHARAAN
%% =====================
REGISTER_ASET ||--o{ PERMINTAAN_PEMELIHARAAN : diminta
PERMINTAAN_PEMELIHARAAN ||--o{ SERVICE_REPORT : diservis

%% =====================
%% APPROVAL (GLOBAL)
%% =====================
APPROVAL ||--o{ RKU_HEADER : approval
APPROVAL ||--o{ PERMINTAAN_BARANG : approval
APPROVAL ||--o{ PERMINTAAN_PEMELIHARAAN : approval
APPROVAL ||--o{ MUTASI_ASET : approval
