erDiagram

%% =========================
%% ========== MASTER DATA =
%% =========================

master_pegawai {
    int id PK
    varchar nip_pegawai
    varchar nama_pegawai
    int id_unit_kerja FK
    int id_jabatan FK
    varchar email_pegawai
    varchar no_telp
}

master_unit_kerja {
    int id_unit_kerja PK
    varchar kode_unit_kerja
    varchar nama_unit_kerja
}

master_jabatan {
    int id_jabatan PK
    varchar nama_jabatan
}

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
    varchar kode_kategori_barang
    varchar nama_kategori_barang
}

master_jenis_barang {
    int id_jenis_barang PK
    int id_kategori_barang FK
    varchar kode_jenis_barang
    varchar nama_jenis_barang
}

master_subjenis_barang {
    int id_subjenis_barang PK
    int id_jenis_barang FK
    varchar kode_subjenis_barang
    varchar nama_subjenis_barang
}

master_data_barang {
    int id_data_barang PK
    int id_subjenis_barang FK
    int id_satuan FK
    varchar kode_data_barang
    varchar nama_barang
    text deskripsi
    varchar upload_foto
}

master_satuan {
    int id_satuan PK
    varchar nama_satuan
}

master_sumber_anggaran {
    int id_anggaran PK
    varchar nama_anggaran
}

master_lokasi {
    int id_lokasi PK
    int id_unit_kerja FK
    varchar kode_lokasi
    varchar nama_lokasi
}

master_gudang {
    int id_gudang PK
    int id_lokasi FK
    varchar nama_gudang
    enum jenis_gudang  %% PUSAT / UNIT
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
    varchar kode_sub_kegiatan
}

%% =============================
%% ========== INVENTORY ========
%% =============================

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
    ## HUBUNGAN DENGAN AUTO ADD ROW (ASET) ##
    Saat jenis_inventory = ASET

    qty_input = N

    Sistem AUTO INSERT N ROW ke
    
    inventory_item {
        int id_item PK
        int id_inventory FK
        varchar kode_register UNIQUE
        varchar no_seri
        enum kondisi_item
        enum status_item
        int id_gudang FK
        int id_ruangan FK
        text qr_code
        datetime created_at
    }

data_stock {
    int id_stock PK
    int id_data_barang FK
    int id_gudang FK
    decimal qty_awal
    decimal qty_masuk
    decimal qty_keluar
    decimal qty_akhir
    int id_satuan FK
    datetime last_updated
}

data_stock_opname {
    int id_opname PK
    int id_data_barang FK
    int id_gudang FK
    date tanggal_opname
    decimal qty_sistem
    decimal qty_fisik
    decimal selisih
    text keterangan
    int id_petugas FK
}

%% ==================================
%% ========== PERMINTAAN ============
%% ==================================

permintaan_barang {
    int id_permintaan PK
    varchar no_permintaan
    int id_unit_kerja FK
    int id_pemohon FK
    date tanggal_permintaan
    enum jenis_permintaan %% BARANG / ASET
    enum status_permintaan %% DRAFT / DIAJUKAN / DISETUJUI / DITOLAK
    text keterangan
    datetime created_at
}

detail_permintaan_barang {
    int id_detail_permintaan PK
    int id_permintaan FK
    int id_data_barang FK
    decimal qty_diminta
    int id_satuan FK
    text keterangan
}

%% ==================================
%% ========== APPROVAL ==============
%% ==================================

approval_permintaan {
    int id_approval PK
    varchar modul_approval %% PERMINTAAN_BARANG, PEMELIHARAAN, DLL
    int id_referensi
    int id_approver FK %% Kepala
    enum status_approval %% MENUNGGU / DISETUJUI / DITOLAK
    text catatan
    datetime tanggal_approval
}

%% ==================================
%% ========== DISTRIBUSI (SBBK) ======
%% ==================================

transaksi_distribusi {
    int id_distribusi PK
    varchar no_sbbk
    int id_permintaan FK
    datetime tanggal_distribusi
    int id_gudang_asal FK
    int id_gudang_tujuan FK
    int id_pegawai_pengirim FK
    enum status_distribusi %% DRAFT / DIKIRIM / SELESAI
    text keterangan
    datetime created_at
}

detail_distribusi {
    int id_detail_distribusi PK
    int id_distribusi FK
    int id_inventory FK
    decimal qty_distribusi
    int id_satuan FK
    decimal harga_satuan
    decimal subtotal
    text keterangan
}

%% ==================================
%% ========== PENERIMAAN ============
%% ==================================

penerimaan_barang {
    int id_penerimaan PK
    varchar no_penerimaan
    int id_distribusi FK
    int id_unit_kerja FK
    int id_pegawai_penerima FK
    date tanggal_penerimaan
    enum status_penerimaan %% DITERIMA / DITOLAK
    text keterangan
    datetime created_at
}

detail_penerimaan_barang {
    int id_detail_penerimaan PK
    int id_penerimaan FK
    int id_inventory FK
    decimal qty_diterima
    int id_satuan FK
    text keterangan
}

%% ==================================
%% ========== ASET & KIR ============
%% ==================================

register_aset {
    int id_register_aset PK
    int id_inventory FK
    int id_unit_kerja FK
    int id_lokasi FK
    varchar nomor_register
    enum kondisi_aset %% BAIK / RUSAK_RINGAN / RUSAK_BERAT
    date tanggal_perolehan
    enum status_aset %% AKTIF / NONAKTIF
}

kartu_inventaris_ruangan {
    int id_kir PK
    int id_register_aset FK
    int id_lokasi FK
    int id_penanggung_jawab FK
    date tanggal_penempatan
}

mutasi_aset {
    int id_mutasi PK
    int id_register_aset FK
    int id_lokasi_asal FK
    int id_lokasi_tujuan FK
    date tanggal_mutasi
    text keterangan
}

%% ==================================
%% ========== HISTORI ===============
%% ==================================

history_lokasi {
    int id_history PK
    int id_inventory FK
    int id_gudang_asal FK
    int id_gudang_tujuan FK
    int id_transaksi
    enum jenis_transaksi %% DISTRIBUSI / PENERIMAAN / MUTASI
    datetime tanggal_transaksi
    decimal qty
    int id_satuan FK
}


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
