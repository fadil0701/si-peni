@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('transaction.permintaan-barang.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Daftar Permintaan Barang
    </a>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-200">
    <div class="px-6 py-5 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Tambah Permintaan Barang</h2>
    </div>
    
    <form action="{{ route('transaction.permintaan-barang.store') }}" method="POST" class="p-6" id="formPermintaan">
        @csrf
        
        <div class="space-y-6">
            <!-- Informasi Permintaan -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Permintaan</h3>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div>
                        <label for="id_unit_kerja" class="block text-sm font-medium text-gray-700 mb-2">
                            Unit Kerja <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="id_unit_kerja" 
                            name="id_unit_kerja" 
                            required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('id_unit_kerja') border-red-500 @enderror"
                        >
                            <option value="">Pilih Unit Kerja</option>
                            @foreach($unitKerjas as $unitKerja)
                                <option value="{{ $unitKerja->id_unit_kerja }}" {{ old('id_unit_kerja') == $unitKerja->id_unit_kerja ? 'selected' : '' }}>
                                    {{ $unitKerja->nama_unit_kerja }}
                                </option>
                            @endforeach
                        </select>
                        @error('id_unit_kerja')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="id_pemohon" class="block text-sm font-medium text-gray-700 mb-2">
                            Pemohon <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="id_pemohon" 
                            name="id_pemohon" 
                            required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('id_pemohon') border-red-500 @enderror"
                        >
                            <option value="">Pilih Pemohon</option>
                            @foreach($pegawais as $pegawai)
                                <option value="{{ $pegawai->id }}" {{ old('id_pemohon') == $pegawai->id ? 'selected' : '' }}>
                                    {{ $pegawai->nama_pegawai }} ({{ $pegawai->nip_pegawai }})
                                </option>
                            @endforeach
                        </select>
                        @error('id_pemohon')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="tanggal_permintaan" class="block text-sm font-medium text-gray-700 mb-2">
                            Tanggal Permintaan <span class="text-red-500">*</span>
                        </label>
                        <input 
                            type="date" 
                            id="tanggal_permintaan" 
                            name="tanggal_permintaan" 
                            required
                            value="{{ old('tanggal_permintaan', date('Y-m-d')) }}"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('tanggal_permintaan') border-red-500 @enderror"
                        >
                        @error('tanggal_permintaan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="jenis_permintaan" class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Permintaan <span class="text-red-500">*</span>
                        </label>
                        <select 
                            id="jenis_permintaan" 
                            name="jenis_permintaan" 
                            required
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('jenis_permintaan') border-red-500 @enderror"
                        >
                            <option value="">Pilih Jenis</option>
                            <option value="BARANG" {{ old('jenis_permintaan') == 'BARANG' ? 'selected' : '' }}>Barang</option>
                            <option value="ASET" {{ old('jenis_permintaan') == 'ASET' ? 'selected' : '' }}>Aset</option>
                        </select>
                        @error('jenis_permintaan')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="keterangan" class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                        <textarea 
                            id="keterangan" 
                            name="keterangan" 
                            rows="3"
                            placeholder="Masukkan keterangan permintaan"
                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                        >{{ old('keterangan') }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Detail Permintaan -->
            <div>
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Detail Permintaan</h3>
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
                href="{{ route('transaction.permintaan-barang.index') }}" 
                class="px-5 py-2.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
                Batal
            </a>
            <button 
                type="submit" 
                class="px-5 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
                Simpan
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
                    Data Barang <span class="text-red-500">*</span>
                </label>
                <select 
                    name="detail[INDEX][id_data_barang]" 
                    required
                    class="select-data-barang block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                >
                    <option value="">Pilih Data Barang</option>
                    @foreach($dataBarangs as $dataBarang)
                        <option value="{{ $dataBarang->id_data_barang }}" data-satuan="{{ $dataBarang->id_satuan }}">
                            {{ $dataBarang->kode_data_barang }} - {{ $dataBarang->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Qty <span class="text-red-500">*</span>
                </label>
                <input 
                    type="number" 
                    name="detail[INDEX][qty_diminta]" 
                    required
                    min="0.01"
                    step="0.01"
                    placeholder="0"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
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

            <div class="sm:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                <input 
                    type="text" 
                    name="detail[INDEX][keterangan]" 
                    placeholder="Opsional"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                >
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
    </div>
</template>

@push('scripts')
<script>
let itemIndex = 0;

document.getElementById('btnTambahItem').addEventListener('click', function() {
    const template = document.getElementById('itemTemplate');
    const container = document.getElementById('detailContainer');
    const newItem = template.content.cloneNode(true);
    
    // Replace INDEX dengan itemIndex
    newItem.innerHTML = newItem.innerHTML.replace(/INDEX/g, itemIndex);
    
    container.appendChild(newItem);
    itemIndex++;
    
    // Auto-set satuan ketika data barang dipilih
    const selectBarang = container.lastElementChild.querySelector('.select-data-barang');
    const selectSatuan = container.lastElementChild.querySelector('.select-satuan');
    
    selectBarang.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const satuanId = selectedOption.getAttribute('data-satuan');
        if (satuanId) {
            selectSatuan.value = satuanId;
        }
    });
    
    // Hapus item
    container.lastElementChild.querySelector('.btnHapusItem').addEventListener('click', function() {
        this.closest('.item-row').remove();
    });
});

// Hapus item untuk item pertama jika ada
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.btnHapusItem').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.item-row').remove();
        });
    });
    
    // Auto-set satuan untuk item yang sudah ada
    document.querySelectorAll('.select-data-barang').forEach(select => {
        select.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const satuanId = selectedOption.getAttribute('data-satuan');
            const row = this.closest('.item-row');
            const selectSatuan = row.querySelector('.select-satuan');
            if (satuanId && selectSatuan) {
                selectSatuan.value = satuanId;
            }
        });
    });
    
    // Tambah item pertama jika belum ada
    if (document.getElementById('detailContainer').children.length === 0) {
        document.getElementById('btnTambahItem').click();
    }
});
</script>
@endpush
@endsection

