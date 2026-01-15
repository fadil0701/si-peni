<?php

namespace App\Observers;

use App\Models\DataInventory;
use App\Models\InventoryItem;
use App\Models\DataStock;

class DataInventoryObserver
{
    /**
     * Handle the DataInventory "created" event.
     */
    public function created(DataInventory $dataInventory): void
    {
        // Auto Register Aset: Jika jenis inventory = ASET dan qty_input > 0
        if ($dataInventory->jenis_inventory === 'ASET' && $dataInventory->qty_input > 0) {
            $this->createInventoryItems($dataInventory);
            $this->updateStock($dataInventory);
        }
    }

    /**
     * Handle the DataInventory "updated" event.
     */
    public function updated(DataInventory $dataInventory): void
    {
        // Jika qty_input berubah dan jenis = ASET
        if ($dataInventory->jenis_inventory === 'ASET' && $dataInventory->isDirty('qty_input')) {
            $oldQty = $dataInventory->getOriginal('qty_input') ?? 0;
            $newQty = $dataInventory->qty_input;

            if ($newQty > $oldQty) {
                // Tambah inventory items
                $this->createInventoryItems($dataInventory, $oldQty, $newQty);
            }
            
            // Update stock
            $this->updateStock($dataInventory);
        }
    }

    /**
     * Create inventory items untuk aset
     */
    protected function createInventoryItems(DataInventory $dataInventory, int $startFrom = 0, ?int $totalQty = null): void
    {
        $qty = $totalQty ?? (int) $dataInventory->qty_input;
        $startIndex = $startFrom + 1;

        // Load relationships
        $dataInventory->load(['dataBarang', 'gudang.lokasi.unitKerja']);

        // Get kode unit kerja
        $kodeUnit = $dataInventory->gudang->lokasi->unitKerja->kode_unit_kerja ?? 'UNKNOWN';
        
        // Get kode data barang
        $kodeBarang = $dataInventory->dataBarang->kode_data_barang ?? 'UNKNOWN';
        
        // Get tahun anggaran
        $tahun = $dataInventory->tahun_anggaran;

        // Get urut terakhir untuk kode_barang dan tahun ini
        $lastUrut = $this->getLastUrut($kodeBarang, $tahun);

        // Prepare data untuk bulk insert
        $items = [];
        
        for ($i = $startIndex; $i <= $qty; $i++) {
            $urut = $lastUrut + ($i - $startIndex) + 1;
            $kodeRegister = $this->generateKodeRegister($kodeUnit, $kodeBarang, $tahun, $urut);
            
            $items[] = [
                'id_inventory' => $dataInventory->id_inventory,
                'kode_register' => $kodeRegister,
                'no_seri' => $dataInventory->no_seri, // Bisa null atau mass input
                'kondisi_item' => 'BAIK', // Default
                'status_item' => 'AKTIF', // Default
                'id_gudang' => $dataInventory->id_gudang,
                'id_ruangan' => null, // Belum ditempatkan
                'qr_code' => $this->generateQRCode($kodeRegister),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert untuk performa lebih baik
        if (!empty($items)) {
            InventoryItem::insert($items);
        }
    }

    /**
     * Generate kode register dengan format: [UNIT]/[KODE_BARANG]/[TAHUN]/[URUT]
     */
    protected function generateKodeRegister(string $kodeUnit, string $kodeBarang, int $tahun, int $urut): string
    {
        $urutFormatted = str_pad($urut, 4, '0', STR_PAD_LEFT);
        return "{$kodeUnit}/{$kodeBarang}/{$tahun}/{$urutFormatted}";
    }

    /**
     * Get urut terakhir untuk kode_barang dan tahun tertentu
     */
    protected function getLastUrut(string $kodeBarang, int $tahun): int
    {
        // Cari semua inventory_item yang memiliki kode_register dengan pattern: */KODE_BARANG/TAHUN/*
        $pattern = "%/{$kodeBarang}/{$tahun}/%";
        
        $lastItem = InventoryItem::where('kode_register', 'like', $pattern)
            ->orderBy('kode_register', 'desc')
            ->first();

        if ($lastItem && $lastItem->kode_register) {
            // Extract urut dari kode_register: [UNIT]/[KODE]/[TAHUN]/[URUT]
            $parts = explode('/', $lastItem->kode_register);
            if (count($parts) === 4) {
                return (int) $parts[3];
            }
        }

        return 0;
    }

    /**
     * Generate QR Code untuk inventory item
     */
    protected function generateQRCode(string $kodeRegister): ?string
    {
        // TODO: Implementasi QR Code generation menggunakan library seperti SimpleSoftwareIO/simple-qrcode
        // Untuk sekarang, return kode_register sebagai placeholder
        return $kodeRegister;
    }

    /**
     * Update atau create data stock
     */
    protected function updateStock(DataInventory $dataInventory): void
    {
        $dataInventory->load(['dataBarang', 'gudang']);

        $stock = DataStock::firstOrNew([
            'id_data_barang' => $dataInventory->id_data_barang,
            'id_gudang' => $dataInventory->id_gudang,
        ]);

        if ($stock->exists) {
            // Update existing stock
            $oldQty = $dataInventory->getOriginal('qty_input') ?? 0;
            $newQty = $dataInventory->qty_input;
            $diff = $newQty - $oldQty;

            if ($dataInventory->jenis_inventory === 'ASET') {
                // Untuk ASET, qty_masuk bertambah
                $stock->qty_masuk += $diff;
            } else {
                // Untuk PERSEDIAAN, qty_masuk bertambah
                $stock->qty_masuk += $diff;
            }

            $stock->qty_akhir = $stock->qty_awal + $stock->qty_masuk - $stock->qty_keluar;
        } else {
            // Create new stock
            $stock->qty_awal = 0;
            $stock->qty_masuk = $dataInventory->qty_input;
            $stock->qty_keluar = 0;
            $stock->qty_akhir = $dataInventory->qty_input;
            $stock->id_satuan = $dataInventory->id_satuan;
        }

        $stock->last_updated = now();
        $stock->save();
    }
}
