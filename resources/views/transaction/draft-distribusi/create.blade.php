@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('transaction.draft-distribusi.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Daftar Disposisi
    </a>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-200">
    <div class="px-6 py-5 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Proses Disposisi - {{ $kategoriGudang }}</h2>
        <p class="mt-1 text-sm text-gray-600">No. Permintaan: {{ $approvalLog->permintaan->no_permintaan }}</p>
    </div>
    
    <form action="{{ route('transaction.draft-distribusi.store') }}" method="POST" class="p-6" id="formDraftDistribusi">
        @csrf
        <input type="hidden" name="id_permintaan" value="{{ $approvalLog->permintaan->id_permintaan }}">
        
        <div class="space-y-6">
            <!-- Informasi Permintaan -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Permintaan</h3>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Unit Kerja</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $approvalLog->permintaan->unitKerja->nama_unit_kerja ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Pemohon</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $approvalLog->permintaan->pemohon->nama_pegawai ?? '-' }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal Permintaan</label>
                            <p class="mt-1 text-sm text-gray-900">{{ $approvalLog->permintaan->tanggal_permintaan->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Jenis Permintaan</label>
                            <div class="mt-1 flex flex-wrap gap-2">
                                @if(is_array($approvalLog->permintaan->jenis_permintaan))
                                    @foreach($approvalLog->permintaan->jenis_permintaan as $jenis)
                                        <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ $jenis }}</span>
                                    @endforeach
                                @else
                                    <span class="px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800">{{ $approvalLog->permintaan->jenis_permintaan }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detail Permintaan untuk Kategori {{ $kategoriGudang }} -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Permintaan - {{ $kategoriGudang }}</h3>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 mb-4">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Barang</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Qty Diminta</th>
                                <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($detailPermintaan as $detail)
                                <tr>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $detail->dataBarang->nama_barang ?? '-' }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ number_format($detail->qty_diminta, 2) }}</td>
                                    <td class="px-4 py-2 text-sm text-gray-900">{{ $detail->satuan->nama_satuan ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Detail Distribusi -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Distribusi - {{ $kategoriGudang }}</h3>
                
                <div class="mb-4 flex justify-end">
                    <button 
                        type="button" 
                        id="btnTambahItem"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                    >
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Tambah Item
                    </button>
                </div>

                <div id="detailContainer" class="space-y-4">
                    <!-- Item akan ditambahkan di sini via JavaScript -->
                </div>

                @error('detail')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3 border-t border-gray-200 pt-6">
            <a 
                href="{{ route('transaction.draft-distribusi.index') }}" 
                class="px-5 py-2.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
                Batal
            </a>
            <button 
                type="submit" 
                class="px-5 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
                Simpan & Siapkan untuk Distribusi
            </button>
        </div>
    </form>
</div>

<!-- Template untuk item detail (hidden) -->
<template id="itemTemplate">
    <div class="item-row bg-gray-50 p-4 rounded-lg border border-gray-200">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-12">
            <div class="sm:col-span-5">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Inventory <span class="text-red-500">*</span>
                </label>
                <select 
                    name="detail[INDEX][id_inventory]" 
                    required
                    class="select-inventory block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    onchange="updateHargaSatuan(this)"
                >
                    <option value="">Pilih Inventory</option>
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Gudang Asal <span class="text-red-500">*</span>
                </label>
                <select 
                    name="detail[INDEX][id_gudang_asal]" 
                    required
                    class="select-gudang block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    onchange="loadInventoryByGudang(this)"
                >
                    <option value="">Pilih Gudang</option>
                    @foreach($gudangs as $gudang)
                        <option value="{{ $gudang->id_gudang }}">{{ $gudang->nama_gudang }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Qty Distribusi <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    name="detail[INDEX][qty_distribusi]" 
                    required
                    min="0.01"
                    step="0.01"
                    placeholder="0"
                    class="qty-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    onchange="calculateSubtotal(this)"
                >
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Satuan <span class="text-red-500">*</span>
                </label>
                <select 
                    name="detail[INDEX][id_satuan]" 
                    required
                    class="select-satuan block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                >
                    <option value="">Pilih Satuan</option>
                    @foreach($satuans as $satuan)
                        <option value="{{ $satuan->id_satuan }}">{{ $satuan->nama_satuan }}</option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-1 flex items-end">
                <button 
                    type="button" 
                    class="btnHapusItem w-full px-3 py-2 border border-red-300 text-red-700 bg-white hover:bg-red-50 rounded-md text-sm font-medium focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    Hapus
                </button>
            </div>
        </div>
        <div class="mt-2 grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Harga Satuan <span class="text-red-500">*</span></label>
                <input 
                    type="number" 
                    name="detail[INDEX][harga_satuan]" 
                    required
                    min="0"
                    step="0.01"
                    placeholder="0"
                    class="harga-satuan-input block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                    onchange="calculateSubtotal(this)"
                >
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <input 
                    type="text" 
                    name="detail[INDEX][keterangan]" 
                    placeholder="Opsional"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                >
            </div>
        </div>
    </div>
</template>

@push('scripts')
<script>
let itemIndex = 0;
let inventoryData = {};

// Load inventory berdasarkan gudang
function loadInventoryByGudang(select) {
    const gudangId = select.value;
    const row = select.closest('.item-row');
    const inventorySelect = row.querySelector('.select-inventory');
    
    if (!gudangId) {
        inventorySelect.innerHTML = '<option value="">Pilih Inventory</option>';
        return;
    }
    
    fetch(`/api/gudang/${gudangId}/inventory`)
        .then(response => response.json())
        .then(data => {
            inventorySelect.innerHTML = '<option value="">Pilih Inventory</option>';
            data.inventory.forEach(inv => {
                // Filter hanya inventory sesuai kategori
                if (inv.jenis_inventory === '{{ $kategoriGudang }}') {
                    const option = document.createElement('option');
                    option.value = inv.id_inventory;
                    const kodeText = inv.kode_barang ? ` (${inv.kode_barang})` : '';
                    option.textContent = `${inv.nama_barang}${kodeText} - Stok: ${inv.qty_available}`;
                    option.setAttribute('data-harga', inv.harga_satuan);
                    option.setAttribute('data-satuan', inv.id_satuan);
                    inventorySelect.appendChild(option);
                }
            });
        })
        .catch(error => {
            console.error('Error loading inventory:', error);
        });
}

// Update harga satuan saat inventory dipilih
function updateHargaSatuan(select) {
    const row = select.closest('.item-row');
    const hargaInput = row.querySelector('.harga-satuan-input');
    const satuanSelect = row.querySelector('.select-satuan');
    
    const selectedOption = select.options[select.selectedIndex];
    if (selectedOption.value) {
        const harga = selectedOption.getAttribute('data-harga');
        const satuanId = selectedOption.getAttribute('data-satuan');
        
        if (harga) {
            hargaInput.value = harga;
        }
        if (satuanId) {
            satuanSelect.value = satuanId;
        }
        
        calculateSubtotal(hargaInput);
    }
}

// Calculate subtotal
function calculateSubtotal(input) {
    const row = input.closest('.item-row');
    const qtyInput = row.querySelector('.qty-input');
    const hargaInput = row.querySelector('.harga-satuan-input');
    
    // Subtotal akan dihitung di backend
}

// Tambah item
function addItemRow() {
    const template = document.getElementById('itemTemplate');
    const container = document.getElementById('detailContainer');
    
    if (!template || !container) {
        console.error('Template or container not found');
        alert('Terjadi kesalahan saat menambahkan item. Silakan refresh halaman.');
        return;
    }
    
    // Clone template content dan replace INDEX
    const tempDiv = document.createElement('div');
    tempDiv.innerHTML = template.innerHTML.replace(/INDEX/g, itemIndex);
    const newItem = tempDiv.firstElementChild;
    
    if (!newItem) {
        console.error('Failed to clone template');
        alert('Terjadi kesalahan saat menambahkan item. Silakan refresh halaman.');
        return;
    }
    
    container.appendChild(newItem);
    
    const newRow = container.lastElementChild;
    const inventorySelect = newRow.querySelector('.select-inventory');
    const gudangSelect = newRow.querySelector('.select-gudang');
    
    if (!inventorySelect || !gudangSelect) {
        console.error('Select elements not found in new row');
        return;
    }
    
    // Attach event handler untuk update harga satuan
    inventorySelect.addEventListener('change', function() {
        updateHargaSatuan(this);
    });
    
    // Attach event handler untuk load inventory saat gudang dipilih
    gudangSelect.addEventListener('change', function() {
        loadInventoryByGudang(this);
    });
    
    // Hapus item
    const btnHapus = newRow.querySelector('.btnHapusItem');
    if (btnHapus) {
        btnHapus.addEventListener('click', function() {
            this.closest('.item-row').remove();
        });
    }
    
    itemIndex++;
    console.log('Item row added successfully, current index:', itemIndex);
}

// Event listener untuk tombol tambah item dan initialization
document.addEventListener('DOMContentLoaded', function() {
    // Setup tombol tambah item
    const btnTambahItem = document.getElementById('btnTambahItem');
    if (btnTambahItem) {
        btnTambahItem.addEventListener('click', function(e) {
            e.preventDefault();
            addItemRow();
        });
    }
    
    // Tambah item pertama jika belum ada
    const detailContainer = document.getElementById('detailContainer');
    if (detailContainer && detailContainer.children.length === 0) {
        addItemRow();
    }
    
    // Validasi form sebelum submit
    const formDraftDistribusi = document.getElementById('formDraftDistribusi');
    if (formDraftDistribusi) {
        formDraftDistribusi.addEventListener('submit', function(e) {
            const detailRows = detailContainer.querySelectorAll('.item-row');
            if (detailRows.length === 0) {
                e.preventDefault();
                alert('Minimal harus ada 1 item distribusi. Silakan klik tombol "Tambah Item" terlebih dahulu.');
                return false;
            }
            
            // Validasi setiap item
            let isValid = true;
            let emptyFields = [];
            detailRows.forEach((row, index) => {
                const idInventory = row.querySelector('[name*="[id_inventory]"]');
                const idGudangAsal = row.querySelector('[name*="[id_gudang_asal]"]');
                const qtyDistribusi = row.querySelector('[name*="[qty_distribusi]"]');
                const idSatuan = row.querySelector('[name*="[id_satuan]"]');
                const hargaSatuan = row.querySelector('[name*="[harga_satuan]"]');
                
                if (!idInventory || !idInventory.value) {
                    isValid = false;
                    emptyFields.push(`Item ${index + 1}: Inventory`);
                }
                if (!idGudangAsal || !idGudangAsal.value) {
                    isValid = false;
                    emptyFields.push(`Item ${index + 1}: Gudang Asal`);
                }
                if (!qtyDistribusi || !qtyDistribusi.value || parseFloat(qtyDistribusi.value) <= 0) {
                    isValid = false;
                    emptyFields.push(`Item ${index + 1}: Qty Distribusi`);
                }
                if (!idSatuan || !idSatuan.value) {
                    isValid = false;
                    emptyFields.push(`Item ${index + 1}: Satuan`);
                }
                if (!hargaSatuan || !hargaSatuan.value || parseFloat(hargaSatuan.value) <= 0) {
                    isValid = false;
                    emptyFields.push(`Item ${index + 1}: Harga Satuan`);
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Mohon lengkapi semua field yang wajib diisi:\n' + emptyFields.join('\n'));
                return false;
            }
        });
    }
});
</script>
@endpush
@endsection

