# ERD Approval System (Database Design)
## Sistem Manajemen Aset & Inventory

## 📊 Entity Relationship Diagram

```
┌─────────────────────────────────────────────────────────────────┐
│                         USERS                                    │
│  ┌─────────────┐                                                │
│  │ id (PK)     │                                                │
│  │ name        │                                                │
│  │ email       │                                                │
│  │ password    │                                                │
│  │ id_unit_kerja (FK) → master_unit_kerja.id_unit_kerja        │
│  └─────────────┘                                                │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ Relationships:                                           │   │
│  │ - BelongsToMany: roles (via role_user pivot)            │   │
│  │ - BelongsTo: unitKerja (master_unit_kerja)              │   │
│  │ - HasMany: approvalLogs (as approver)                   │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ (via role_user pivot)
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                         ROLES                                    │
│  ┌─────────────┐                                                │
│  │ id (PK)     │                                                │
│  │ name        │ (kepala_unit, kasubbag_tu, kepala_pusat, etc)│
│  │ display_name│                                                │
│  │ description │                                                │
│  └─────────────┘                                                │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ Relationships:                                           │   │
│  │ - BelongsToMany: users (via role_user pivot)            │   │
│  │ - HasMany: approvalFlowDefinitions                       │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ (role_id FK)
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│              APPROVAL_FLOW_DEFINITION                           │
│  ┌──────────────────────────────────────────────────────────┐ │
│  │ id (PK)                                                    │ │
│  │ modul_approval (VARCHAR)                                   │ │
│  │   - PERMINTAAN_BARANG                                      │ │
│  │   - PEMELIHARAAN                                           │ │
│  │   - MUTASI_ASET                                            │ │
│  │ step_order (INT)                                           │ │
│  │ role_id (FK) → roles.id                                    │ │
│  │ nama_step (VARCHAR)                                        │ │
│  │   - "Diajukan"                                            │ │
│  │   - "Diketahui Unit"                                      │ │
│  │   - "Diketahui TU"                                        │ │
│  │   - "Disetujui Pimpinan"                                  │ │
│  │   - "Didisposisikan"                                      │ │
│  │   - "Diproses"                                            │ │
│  │ status (ENUM)                                              │ │
│  │   - MENUNGGU                                               │ │
│  │   - DIKETAHUI                                              │ │
│  │   - DIVERIFIKASI                                           │ │
│  │   - DISETUJUI                                              │ │
│  │   - DITOLAK                                                │ │
│  │   - DIDISPOSISIKAN                                         │ │
│  │ status_text (TEXT)                                         │ │
│  │ is_required (BOOLEAN)                                      │ │
│  │ can_reject (BOOLEAN)                                       │ │
│  │ can_approve (BOOLEAN)                                      │ │
│  │ created_at, updated_at                                     │ │
│  └──────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ Relationships:                                           │   │
│  │ - BelongsTo: role                                        │   │
│  │ - HasMany: approvalLogs                                  │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ (id_approval_flow FK)
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                    PERMINTAAN_BARANG                            │
│  ┌──────────────────────────────────────────────────────────┐ │
│  │ id_permintaan (PK)                                        │ │
│  │ no_permintaan (VARCHAR, UNIQUE)                           │ │
│  │ id_unit_kerja (FK) → master_unit_kerja.id_unit_kerja      │ │
│  │ id_pemohon (FK) → master_pegawai.id                        │ │
│  │ tanggal_permintaan (DATE)                                  │ │
│  │ jenis_permintaan (JSON)                                    │ │
│  │   - ["BARANG", "ASET"]                                     │ │
│  │ status_permintaan (ENUM)                                   │ │
│  │   - DRAFT                                                  │ │
│  │   - DIAJUKAN                                               │ │
│  │   - DIKETAHUI                                              │ │
│  │   - DIVERIFIKASI                                           │ │
│  │   - DISETUJUI                                              │ │
│  │   - DITOLAK                                                │ │
│  │   - DIDISPOSISIKAN                                         │ │
│  │ keterangan (TEXT)                                          │ │
│  │ created_at, updated_at                                     │ │
│  └──────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ Relationships:                                           │   │
│  │ - BelongsTo: unitKerja                                    │   │
│  │ - BelongsTo: pemohon (master_pegawai)                    │   │
│  │ - HasMany: detailPermintaanBarang                         │   │
│  │ - HasMany: approvalLogs (via modul_approval + id_referensi)│
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              │ (id_referensi FK)
                              ▼
┌─────────────────────────────────────────────────────────────────┐
│                      APPROVAL_LOG                                │
│  ┌──────────────────────────────────────────────────────────┐ │
│  │ id (PK)                                                    │ │
│  │ modul_approval (VARCHAR)                                   │ │
│  │   - PERMINTAAN_BARANG                                      │ │
│  │ id_referensi (INT)                                         │ │
│  │   - Untuk PERMINTAAN_BARANG: id_permintaan                 │ │
│  │ id_approval_flow (FK) → approval_flow_definition.id       │ │
│  │ user_id (FK, NULLABLE) → users.id                         │ │
│  │   - NULL jika belum di-approve                             │ │
│  │ role_id (FK) → roles.id                                    │ │
│  │   - Role yang bertanggung jawab untuk step ini             │ │
│  │ status (ENUM)                                              │ │
│  │   - MENUNGGU                                               │ │
│  │   - DIKETAHUI                                              │ │
│  │   - DIVERIFIKASI                                           │ │
│  │   - DISETUJUI                                              │ │
│  │   - DITOLAK                                                │ │
│  │   - DIDISPOSISIKAN                                         │ │
│  │ catatan (TEXT, NULLABLE)                                   │ │
│  │ approved_at (DATETIME, NULLABLE)                           │ │
│  │ created_at, updated_at                                     │ │
│  └──────────────────────────────────────────────────────────┘ │
│                                                                   │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │ Relationships:                                           │   │
│  │ - BelongsTo: approvalFlow                                 │   │
│  │ - BelongsTo: user (approver, nullable)                  │   │
│  │ - BelongsTo: role                                        │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────────┘
```

## 🔗 Relationship Summary

### 1. **Users ↔ Roles** (Many-to-Many)
- Pivot table: `role_user`
- Satu user bisa memiliki banyak role
- Satu role bisa dimiliki banyak user

### 2. **Roles ↔ ApprovalFlowDefinition** (One-to-Many)
- Satu role bisa memiliki banyak flow definition (untuk modul berbeda)
- Setiap flow definition memiliki satu role yang bertanggung jawab

### 3. **ApprovalFlowDefinition ↔ ApprovalLog** (One-to-Many)
- Satu flow definition bisa memiliki banyak log (untuk request berbeda)
- Setiap log mengacu pada satu flow definition

### 4. **PermintaanBarang ↔ ApprovalLog** (One-to-Many)
- Satu permintaan bisa memiliki banyak log (untuk setiap step)
- Setiap log mengacu pada satu permintaan (via `modul_approval` + `id_referensi`)

### 5. **Users ↔ ApprovalLog** (One-to-Many)
- Satu user bisa melakukan banyak approval
- Setiap log memiliki satu approver (nullable jika belum di-approve)

## 📋 Flow Approval untuk PERMINTAAN_BARANG

| Step Order | Role | Nama Step | Status | Can Reject | Can Approve |
|------------|------|-----------|--------|------------|-------------|
| 1 | pegawai | Diajukan | MENUNGGU | ❌ | ❌ |
| 2 | kepala_unit | Diketahui Unit | MENUNGGU | ❌ | ❌ |
| 3 | kasubbag_tu | Diketahui TU | MENUNGGU | ✅ | ❌ |
| 4 | kepala_pusat | Disetujui Pimpinan | MENUNGGU | ✅ | ✅ |
| 5 | admin_gudang | Didisposisikan | MENUNGGU | ❌ | ❌ |
| 6 | admin_gudang | Diproses | MENUNGGU | ❌ | ❌ |

## 🔄 Status Flow

```
DRAFT → DIAJUKAN → DIKETAHUI → DIVERIFIKASI → DISETUJUI → DIDISPOSISIKAN → DIPROSES → SELESAI
         │           │            │              │
         └───────────┴────────────┴──────────────┴──→ DITOLAK (bisa terjadi di step 3 atau 4)
```

## 📝 Notes

1. **modul_approval**: Field ini memungkinkan sistem approval digunakan untuk berbagai modul (Permintaan Barang, Pemeliharaan, Mutasi Aset, dll)

2. **id_referensi**: Mengacu pada ID dari tabel yang sesuai dengan `modul_approval`. Contoh:
   - Jika `modul_approval = 'PERMINTAAN_BARANG'`, maka `id_referensi = id_permintaan`
   - Jika `modul_approval = 'PEMELIHARAAN'`, maka `id_referensi = id_pemeliharaan`

3. **user_id NULLABLE**: Field ini bisa NULL karena:
   - ApprovalLog dibuat saat permintaan diajukan (belum ada approver)
   - Setelah di-approve, `user_id` diisi dengan ID user yang melakukan approval

4. **Unique Constraint**: `approval_flow_definition` memiliki unique constraint pada `(modul_approval, step_order)` untuk memastikan tidak ada duplikasi step dalam satu modul.






