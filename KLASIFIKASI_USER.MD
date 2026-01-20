1️⃣ DOKUMEN FINAL

Klasifikasi Role User, Hak Akses Sistem & Flow Kerja

Sistem Permintaan – Approval – Distribusi – Penerimaan Barang

A. Prinsip Dasar Sistem (Landasan Resmi)

Approval berjenjang dan terpisah fungsi

Tidak ada conflict of interest

Setiap proses tercatat (audit trail)

Satu role = satu tanggung jawab utama

Kepala Unit hanya mengetahui, bukan menyetujui

B. Klasifikasi Role User Berdasarkan Jabatan

1️⃣ ADMIN SISTEM

Jabatan

Admin IT / Pengelola Aplikasi

Hak Akses

Kelola user & role

Master data (unit, barang, satuan)

Konfigurasi sistem

Monitoring keseluruhan proses

Flow Kerja

Tidak terlibat dalam proses bisnis permintaan barang

2️⃣ PEGAWAI (PEMOHON)

Jabatan

Staf Unit Kerja

Pelaksana Teknis

Hak Akses

Membuat permintaan barang

Melihat status permintaan

Menerima barang

Konfirmasi penerimaan

Flow Kerja

Input Permintaan → Monitoring Status → Terima Barang

3️⃣ KEPALA UNIT

Jabatan

Kepala Seksi / Kepala Sub Unit

Hak Akses

Melihat permintaan dari unitnya

Memberi status “Mengetahui”

Monitoring permintaan unit

Catatan Penting
❌ Tidak memiliki hak approve
❌ Tidak dapat menolak permintaan

Flow Kerja

Permintaan Masuk → Mengetahui → Lanjut ke Kasubbag TU

4️⃣ KASUBBAG TU

Jabatan

Kepala Sub Bagian Tata Usaha

Hak Akses

Verifikasi administrasi permintaan

Cek kelengkapan & kesesuaian

Mengembalikan permintaan jika tidak lengkap

Catatan
❌ Tidak memiliki hak approve final

Flow Kerja

Mengetahui Kepala Unit → Verifikasi → Lanjut ke Kepala Pusat

5️⃣ KEPALA PUSAT (PIMPINAN)

Jabatan

Kepala Pusat / Kepala UPT

Hak Akses

Approve / Reject permintaan

Memberikan disposisi

Menentukan unit pelaksana

Hak Disposisi

Admin Gudang / Pengurus Barang

Perencanaan

Pengadaan

Keuangan

Flow Kerja

Verifikasi TU → Approve / Reject → Disposisi

6️⃣ ADMIN GUDANG / PENGURUS BARANG

Jabatan

Pengurus Barang

Admin Gudang

Hak Akses

Kelola stok barang

Proses distribusi barang

Cetak bukti serah terima (SBBK)

Register aset (jika barang inventaris)

Flow Kerja

Disposisi → Distribusi → Serah Terima → Update Stok

7️⃣ UNIT TERKAIT (Opsional)

Role

Perencanaan

Pengadaan

Keuangan

Hak Akses

Menindaklanjuti disposisi pimpinan

Input progres sesuai tugas