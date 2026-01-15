🔄 FLOW AUTO CREATE REGISTER ASET
(Saat Input Data Inventory – Jenis = ASET)
1️⃣ KONDISI AWAL (FORM INPUT)

User mengisi Form Data Inventory:

Field	Contoh
Jenis Inventory	ASET
Barang	Laptop
Gudang	Gudang Pusat Aset
Qty Input	5
Tahun Anggaran	2025
Harga Satuan	10.000.000
Merk	Dell
Tipe	Latitude 5440
No Seri	opsional / bisa mass input

➡️ User klik SIMPAN

2️⃣ PROSES BACKEND – LEVEL HEADER
2.1 Insert ke data_inventory
INSERT INTO data_inventory
→ menghasilkan id_inventory = 1001


Contoh data:

id_inventory = 1001
jenis_inventory = ASET
qty_input = 5


➡️ BELUM ADA REGISTER ASET

3️⃣ LOGIKA AUTO ADD ROW (CORE SYSTEM)
3.1 Sistem membaca:
IF jenis_inventory == 'ASET'
AND qty_input > 0

3.2 LOOP OTOMATIS
FOR i = 1 TO qty_input

4️⃣ PEMBENTUKAN NOMOR REGISTER (KRITIKAL)
4.1 FORMAT REGISTER (REKOMENDASI)
[UNIT]/[KODE_BARANG]/[TAHUN]/[URUT]


Contoh:

DKI-PPKP/LAP/2025/0001
DKI-PPKP/LAP/2025/0002
DKI-PPKP/LAP/2025/0003

4.2 LOGIKA PENENTUAN URUTAN
SELECT MAX(urut) 
FROM inventory_item 
WHERE kode_barang = 'LAP'
AND tahun = 2025


➡️ lalu +1

5️⃣ INSERT KE inventory_item (PER ASET)
INSERT INTO inventory_item
(
    id_inventory,
    kode_register,
    no_seri,
    kondisi_item,
    status_item,
    id_gudang,
    qr_code,
    created_at
)


➡️ Dijalankan sebanyak QTY

Contoh hasil:
id_item	kode_register	status
1	DKI-PPKP/LAP/2025/0001	AKTIF
2	DKI-PPKP/LAP/2025/0002	AKTIF
3	DKI-PPKP/LAP/2025/0003	AKTIF
4	DKI-PPKP/LAP/2025/0004	AKTIF
5	DKI-PPKP/LAP/2025/0005	AKTIF
6️⃣ UPDATE STOCK (AGREGAT)
data_stock.qty_total += qty_input


📌 Stock tetap 1 baris
📌 Register aset ada di inventory_item

7️⃣ KETERKAITAN DENGAN KIR
Saat aset dipindahkan ke ruangan:
Mutasi Gudang → Ruangan


➡️ Sistem:

Update inventory_item.id_ruangan

Auto muncul di KIR Ruangan

register_aset {
    id_item FK
    id_ruangan FK
    kondisi
}

8️⃣ FLOW DIAGRAM (TEKS)
[Form Input Inventory]
        |
        v
[Insert data_inventory]
        |
        v
[IF jenis = ASET]
        |
        v
[Loop qty_input]
        |
        v
[Generate kode_register]
        |
        v
[Insert inventory_item]
        |
        v
[Update data_stock]
        |
        v
[Selesai]

9️⃣ KEUNTUNGAN DESAIN INI

✅ Tidak ada aset tanpa register
✅ Register otomatis & konsisten
✅ Siap KIR & BMD
✅ Mudah audit (1 barang = 1 record)
✅ Aman untuk mutasi, pemeliharaan, penghapusan