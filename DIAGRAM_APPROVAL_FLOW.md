# Diagram Resmi Approval Berjenjang (Multi-Level)
## Sistem Manajemen Aset & Inventory

## 📊 Flow Diagram Approval

```
┌─────────────────────────────────────────────────────────────┐
│                    PEGAWAI (ADMIN)                           │
│                    Pemohon                                   │
│                    Status: Draft / Diajukan                  │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│              KEPALA UNIT (MENGETAHUI)                        │
│              Status: Diketahui Unit                          │
└───────────────────────┬─────────────────────────────────────┘
                        │
                        ▼
┌─────────────────────────────────────────────────────────────┐
│            KASUBBAG TU (VERIFIKASI)                          │
│            Status: Diketahui TU / Ditolak                    │
└───────┬───────────────────────────────────────┬─────────────┘
        │                                       │
        │ (Alternatif)                          │ (Normal Flow)
        │                                       │
        ▼                                       ▼
┌──────────────────────────────┐   ┌─────────────────────────────────────────────┐
│   ADMIN GUDANG / UNIT        │   │         KEPALA PUSAT                         │
│   TERKAIT                    │   │         Status: Disetujui / Ditolak /         │
│   Status: Didisposisikan     │   │                Didisposisikan                │
└──────────────┬───────────────┘   └───────────────────┬─────────────────────────┘
               │                                        │
               │                                        │
               └──────────────────┬─────────────────────┘
                                  │
                                  ▼
                    ┌─────────────────────────────────────────────┐
                    │     ADMIN GUDANG / UNIT TERKAIT            │
                    │     Status: Didisposisikan / Diproses      │
                    └───────────────────┬─────────────────────────┘
                                        │
                                        ▼
                    ┌─────────────────────────────────────────────┐
                    │         DISTRIBUSI OLEH PEGAWAI            │
                    │         Status: Selesai                    │
                    └─────────────────────────────────────────────┘
```

## 🔄 Status Approval

| Status | Deskripsi | Warna |
|--------|-----------|-------|
| **Draft** | Permintaan masih dalam tahap draft, belum diajukan | Gray |
| **Diajukan** | Permintaan telah diajukan oleh pegawai | Blue |
| **Diketahui Unit** | Kepala Unit telah mengetahui permintaan | Green |
| **Diketahui TU** | Kasubbag TU telah memverifikasi administrasi | Yellow |
| **Disetujui Pimpinan** | Kepala Pusat telah menyetujui permintaan | Purple |
| **Ditolak** | Permintaan ditolak oleh approver | Red |
| **Didisposisikan** | Permintaan telah didisposisikan ke Admin Gudang | Indigo |
| **Diproses** | Admin Gudang sedang memproses distribusi | Orange |
| **Selesai** | Distribusi telah selesai dilakukan | Teal |

## 👥 Roles dalam Flow Approval

1. **Pegawai (Admin Unit)**
   - Membuat permintaan barang
   - Status: Draft → Diajukan

2. **Kepala Unit**
   - Mengetahui permintaan dari unitnya
   - Status: Diajukan → Diketahui Unit
   - Tidak bisa reject atau approve final

3. **Kasubbag TU**
   - Memverifikasi administrasi permintaan
   - Status: Diketahui Unit → Diketahui TU
   - Bisa mengembalikan jika tidak lengkap
   - Bisa langsung disposisi ke Admin Gudang (alternatif)

4. **Kepala Pusat**
   - Menyetujui atau menolak permintaan
   - Status: Diketahui TU → Disetujui Pimpinan / Ditolak
   - Bisa melakukan disposisi ke Admin Gudang

5. **Admin Gudang / Unit Terkait**
   - Menerima disposisi
   - Memproses distribusi barang
   - Status: Didisposisikan → Diproses → Selesai

6. **Pegawai (Penerima)**
   - Menerima distribusi barang
   - Status: Selesai

## 🔀 Alur Alternatif

1. **Kasubbag TU → Admin Gudang (Langsung)**
   - Jika permintaan sudah lengkap dan tidak memerlukan persetujuan pimpinan
   - Status: Diketahui TU → Didisposisikan

2. **Kepala Pusat → Admin Gudang**
   - Setelah disetujui, langsung disposisi ke Admin Gudang
   - Status: Disetujui Pimpinan → Didisposisikan

3. **Reject Flow**
   - Setiap approver bisa menolak permintaan
   - Status: Ditolak
   - Permintaan kembali ke status Draft





