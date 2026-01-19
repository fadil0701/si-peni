<?php

namespace App\Http\Controllers\Inventory;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use App\Models\MasterGudang;
use App\Models\MasterRuangan;

class InventoryItemController extends Controller
{
    public function edit($id)
    {
        $inventoryItem = InventoryItem::with(['inventory', 'gudang.unitKerja', 'ruangan'])->findOrFail($id);
        $gudangs = MasterGudang::all();
        
        // Load ruangans berdasarkan unit kerja dari gudang
        $unitKerjaId = $inventoryItem->gudang->id_unit_kerja ?? null;
        $ruangans = $unitKerjaId ? MasterRuangan::where('id_unit_kerja', $unitKerjaId)->get() : collect();
        
        return view('inventory.inventory-item.edit', compact('inventoryItem', 'gudangs', 'ruangans'));
    }

    public function update(Request $request, $id)
    {
        $inventoryItem = InventoryItem::findOrFail($id);

        $validated = $request->validate([
            'no_seri' => 'nullable|string|max:255',
            'kondisi_item' => 'required|in:BAIK,RUSAK_RINGAN,RUSAK_BERAT',
            'status_item' => 'required|in:AKTIF,DISTRIBUSI,NONAKTIF',
            'id_gudang' => 'required|exists:master_gudang,id_gudang',
            'id_ruangan' => 'nullable|exists:master_ruangan,id_ruangan',
        ]);

        $inventoryItem->update($validated);

        return redirect()->route('inventory.data-inventory.show', $inventoryItem->id_inventory)
            ->with('success', 'Data Inventory Item berhasil diperbarui.');
    }
}
