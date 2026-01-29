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
    
    <!-- Error Messages -->
    @if($errors->any())
        <div class="mx-6 mt-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Terjadi kesalahan saat menyimpan data:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="mx-6 mt-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif
    
    <form action="{{ route('transaction.permintaan-barang.store') }}" method="POST" class="p-6" id="formPermintaan" onsubmit="return validateForm()">
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
                                <option value="{{ $unitKerja->id_unit_kerja }}" {{ old('id_unit_kerja', optional(auth()->user()->pegawai)->id_unit_kerja ?? '') == $unitKerja->id_unit_kerja ? 'selected' : '' }}>
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
                                <option value="{{ $pegawai->id }}" {{ old('id_pemohon', optional(auth()->user()->pegawai)->id ?? '') == $pegawai->id ? 'selected' : '' }}>
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
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Jenis Permintaan <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Tipe Permintaan</label>
                                <div class="space-y-2">
                                    <div class="flex items-center">
                                        <input 
                                            type="radio" 
                                            id="tipe_rutin" 
                                            name="tipe_permintaan" 
                                            value="RUTIN"
                                            {{ old('tipe_permintaan') == 'RUTIN' ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                            onchange="updateSubJenis()"
                                        >
                                        <label for="tipe_rutin" class="ml-2 block text-sm text-gray-700">
                                            Rutin
                                        </label>
                                    </div>
                                    <div class="flex items-center">
                                        <input 
                                            type="radio" 
                                            id="tipe_cito" 
                                            name="tipe_permintaan" 
                                            value="CITO"
                                            {{ old('tipe_permintaan') == 'CITO' ? 'checked' : '' }}
                                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300"
                                            onchange="updateSubJenis()"
                                        >
                                        <label for="tipe_cito" class="ml-2 block text-sm text-gray-700">
                                            CITO (Penting)
                                        </label>
                                    </div>
                                </div>
                                @error('tipe_permintaan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div id="subJenisContainer" class="mt-4 {{ old('tipe_permintaan') ? '' : 'hidden' }}">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Sub Jenis Permintaan <span class="text-red-500">*</span>
                                </label>
                                <div id="subJenisOptions" class="space-y-2">
                                    <!-- Sub jenis akan ditampilkan di sini via JavaScript -->
                                </div>
                                @error('jenis_permintaan')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('jenis_permintaan.*')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
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
                    @if(old('detail'))
                        @foreach(old('detail') as $index => $detail)
                            <div class="item-row bg-gray-50 p-4 rounded-lg border border-gray-200">
                                <div class="grid grid-cols-1 gap-4 sm:grid-cols-12">
                                    <div class="sm:col-span-5">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Data Barang <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            name="detail[{{ $index }}][id_data_barang]" 
                                            required
                                            class="select-data-barang block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('detail.'.$index.'.id_data_barang') border-red-500 @enderror"
                                        >
                                            <option value="">Pilih Data Barang</option>
                                            @foreach($dataBarangs as $dataBarang)
                                                <option value="{{ $dataBarang->id_data_barang }}" 
                                                    data-satuan="{{ $dataBarang->id_satuan }}"
                                                    {{ old('detail.'.$index.'.id_data_barang') == $dataBarang->id_data_barang ? 'selected' : '' }}>
                                                    {{ $dataBarang->kode_data_barang }} - {{ $dataBarang->nama_barang }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('detail.'.$index.'.id_data_barang')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Qty <span class="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="number" 
                                            name="detail[{{ $index }}][qty_diminta]" 
                                            required
                                            min="0.01"
                                            step="0.01"
                                            value="{{ old('detail.'.$index.'.qty_diminta') }}"
                                            placeholder="0"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('detail.'.$index.'.qty_diminta') border-red-500 @enderror"
                                        >
                                        @error('detail.'.$index.'.qty_diminta')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">
                                            Satuan <span class="text-red-500">*</span>
                                        </label>
                                        <select 
                                            name="detail[{{ $index }}][id_satuan]" 
                                            required
                                            class="select-satuan block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('detail.'.$index.'.id_satuan') border-red-500 @enderror"
                                        >
                                            <option value="">Pilih Satuan</option>
                                            @foreach($satuans as $satuan)
                                                <option value="{{ $satuan->id_satuan }}" {{ old('detail.'.$index.'.id_satuan') == $satuan->id_satuan ? 'selected' : '' }}>{{ $satuan->nama_satuan }}</option>
                                            @endforeach
                                        </select>
                                        @error('detail.'.$index.'.id_satuan')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div class="sm:col-span-2">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Keterangan</label>
                                        <input 
                                            type="text" 
                                            name="detail[{{ $index }}][keterangan]" 
                                            value="{{ old('detail.'.$index.'.keterangan') }}"
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
                        @endforeach
                    @endif
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
let itemIndex = {{ old('detail') ? count(old('detail')) : 0 }};

// Fungsi untuk menambahkan item baru
function tambahItem() {
    const template = document.getElementById('itemTemplate');
    const container = document.getElementById('detailContainer');
    
    if (!template || !container) {
        console.error('Template atau container tidak ditemukan');
        return;
    }
    
    const newItem = template.content.cloneNode(true);
    
    // Replace INDEX dengan itemIndex
    const tempDiv = document.createElement('div');
    tempDiv.appendChild(newItem);
    let htmlContent = tempDiv.innerHTML;
    htmlContent = htmlContent.replace(/INDEX/g, itemIndex);
    tempDiv.innerHTML = htmlContent;
    
    const finalItem = tempDiv.firstElementChild;
    container.appendChild(finalItem);
    itemIndex++;
    
    // Auto-set satuan ketika data barang dipilih
    const selectBarang = finalItem.querySelector('.select-data-barang');
    const selectSatuan = finalItem.querySelector('.select-satuan');
    
    if (selectBarang && selectSatuan) {
        selectBarang.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const satuanId = selectedOption.getAttribute('data-satuan');
            if (satuanId) {
                selectSatuan.value = satuanId;
            }
        });
    }
    
    // Hapus item
    const btnHapus = finalItem.querySelector('.btnHapusItem');
    if (btnHapus) {
        btnHapus.addEventListener('click', function() {
            this.closest('.item-row').remove();
        });
    }
}

// Event listener untuk button tambah item
document.addEventListener('DOMContentLoaded', function() {
    const btnTambahItem = document.getElementById('btnTambahItem');
    if (btnTambahItem) {
        btnTambahItem.addEventListener('click', function(e) {
            e.preventDefault();
            tambahItem();
        });
    }
    
    // Hapus item untuk item yang sudah ada
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
    
    // Tambah item pertama jika belum ada (hanya jika tidak ada old input)
    const container = document.getElementById('detailContainer');
    if (container && container.children.length === 0) {
        tambahItem();
    }
    
    // Setup event listeners untuk detail items yang sudah ada (dari old input)
    container.querySelectorAll('.item-row').forEach(row => {
        const selectBarang = row.querySelector('.select-data-barang');
        const selectSatuan = row.querySelector('.select-satuan');
        const btnHapus = row.querySelector('.btnHapusItem');
        
        // Auto-set satuan ketika data barang dipilih
        if (selectBarang && selectSatuan) {
            selectBarang.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const satuanId = selectedOption.getAttribute('data-satuan');
                if (satuanId) {
                    selectSatuan.value = satuanId;
                }
            });
        }
        
        // Hapus item
        if (btnHapus) {
            btnHapus.addEventListener('click', function() {
                this.closest('.item-row').remove();
            });
        }
    });
    
    // Auto-select unit kerja dan pemohon berdasarkan user yang login
    @php
        $userPegawai = auth()->user()->pegawai;
    @endphp
    @if($userPegawai)
        const unitKerjaSelect = document.getElementById('id_unit_kerja');
        const pemohonSelect = document.getElementById('id_pemohon');
        
        if (unitKerjaSelect && !unitKerjaSelect.value) {
            const userUnitKerja = {{ $userPegawai->id_unit_kerja ?? 'null' }};
            if (userUnitKerja) {
                unitKerjaSelect.value = userUnitKerja;
            }
        }
        
        if (pemohonSelect && !pemohonSelect.value) {
            const userPegawaiId = {{ $userPegawai->id ?? 'null' }};
            if (userPegawaiId) {
                pemohonSelect.value = userPegawaiId;
            }
        }
    @endif
    
    // Update sub jenis berdasarkan tipe permintaan yang dipilih
    window.updateSubJenis = function() {
        const tipePermintaan = document.querySelector('input[name="tipe_permintaan"]:checked');
        const subJenisContainer = document.getElementById('subJenisContainer');
        const subJenisOptions = document.getElementById('subJenisOptions');
        
        if (!tipePermintaan) {
            subJenisContainer.classList.add('hidden');
            subJenisOptions.innerHTML = '';
            return;
        }
        
        // Tampilkan container
        subJenisContainer.classList.remove('hidden');
        
        // Sub jenis untuk RUTIN dan CITO sama: ASET, PERSEDIAAN, FARMASI
        const subJenisList = [
            { value: 'ASET', label: 'Aset' },
            { value: 'PERSEDIAAN', label: 'Persediaan' },
            { value: 'FARMASI', label: 'Farmasi' }
        ];
        
        // Get old values untuk pre-select
        const oldJenisPermintaan = @json(old('jenis_permintaan', []));
        
        let html = '';
        subJenisList.forEach(subJenis => {
            const isChecked = oldJenisPermintaan.includes(subJenis.value);
            html += `
                <div class="flex items-center">
                    <input 
                        type="checkbox" 
                        id="subjenis_${subJenis.value.toLowerCase()}" 
                        name="jenis_permintaan[]" 
                        value="${subJenis.value}"
                        ${isChecked ? 'checked' : ''}
                        class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                    >
                    <label for="subjenis_${subJenis.value.toLowerCase()}" class="ml-2 block text-sm text-gray-700">
                        ${subJenis.label}
                    </label>
                </div>
            `;
        });
        
        subJenisOptions.innerHTML = html;
    };
    
    // Initialize sub jenis saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        // Panggil updateSubJenis jika sudah ada tipe yang dipilih
        const tipePermintaan = document.querySelector('input[name="tipe_permintaan"]:checked');
        if (tipePermintaan) {
            updateSubJenis();
        }
    });
    
    // Form validation sebelum submit
    window.validateForm = function() {
        const form = document.getElementById('formPermintaan');
        const tipePermintaan = form.querySelector('input[name="tipe_permintaan"]:checked');
        const jenisPermintaan = form.querySelectorAll('input[name="jenis_permintaan[]"]:checked');
        const detailItems = form.querySelectorAll('.item-row');
        
        // Validasi tipe permintaan
        if (!tipePermintaan) {
            alert('Tipe permintaan harus dipilih (Rutin atau CITO (Penting)).');
            return false;
        }
        
        // Validasi jenis permintaan (sub jenis)
        if (jenisPermintaan.length === 0) {
            alert('Sub jenis permintaan harus dipilih minimal satu (Aset, Persediaan, atau Farmasi).');
            return false;
        }
        
        // Validasi detail items
        if (detailItems.length === 0) {
            alert('Detail permintaan harus diisi minimal satu item.');
            return false;
        }
        
        // Validasi setiap detail item
        let isValid = true;
        detailItems.forEach((item, index) => {
            const idDataBarang = item.querySelector('select[name*="[id_data_barang]"]');
            const qtyDiminta = item.querySelector('input[name*="[qty_diminta]"]');
            const idSatuan = item.querySelector('select[name*="[id_satuan]"]');
            
            if (!idDataBarang || !idDataBarang.value) {
                alert(`Data barang pada item ${index + 1} harus dipilih.`);
                isValid = false;
                return false;
            }
            
            if (!qtyDiminta || !qtyDiminta.value || parseFloat(qtyDiminta.value) <= 0) {
                alert(`Jumlah yang diminta pada item ${index + 1} harus diisi dan lebih dari 0.`);
                isValid = false;
                return false;
            }
            
            if (!idSatuan || !idSatuan.value) {
                alert(`Satuan pada item ${index + 1} harus dipilih.`);
                isValid = false;
                return false;
            }
        });
        
        return isValid;
    };
});
</script>
@endpush
@endsection

