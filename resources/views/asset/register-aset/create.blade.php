@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('asset.register-aset.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Daftar Register Aset
    </a>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-200">
    <div class="px-6 py-5 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Tambah Register Aset</h2>
        <p class="mt-1 text-sm text-gray-600">Buat register aset baru secara manual</p>
    </div>
    
    <form action="{{ route('asset.register-aset.store') }}" method="POST" class="p-6">
        @csrf
        
        <div class="space-y-6">
            <!-- Informasi Inventory -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Pilih Inventory</h3>
                <div>
                    <label for="id_inventory" class="block text-sm font-medium text-gray-700 mb-2">
                        Data Inventory (ASET) <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="id_inventory" 
                        name="id_inventory" 
                        required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('id_inventory') border-red-500 @enderror"
                    >
                        <option value="">Pilih Inventory</option>
                        @foreach($inventories as $inventory)
                            <option value="{{ $inventory->id_inventory }}" {{ old('id_inventory') == $inventory->id_inventory ? 'selected' : '' }}>
                                {{ $inventory->dataBarang->nama_barang ?? '-' }} 
                                ({{ $inventory->gudang->nama_gudang ?? '-' }})
                                - Qty: {{ number_format($inventory->qty_input, 0) }}
                            </option>
                        @endforeach
                    </select>
                    @error('id_inventory')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Pilih inventory dengan jenis ASET yang akan dibuat register aset</p>
                </div>
            </div>

            <!-- Informasi Unit Kerja -->
            <div class="bg-gray-50 rounded-lg p-6 border border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Unit Kerja</h3>
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
            </div>

            <!-- Form Fields -->
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="nomor_register" class="block text-sm font-medium text-gray-700 mb-2">
                        Nomor Register <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="text" 
                        id="nomor_register" 
                        name="nomor_register" 
                        value="{{ old('nomor_register') }}"
                        required
                        placeholder="Contoh: DKI-PPKP/LAP/2025/0001"
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('nomor_register') border-red-500 @enderror"
                    >
                    @error('nomor_register')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Format: [UNIT]/[KODE_BARANG]/[TAHUN]/[URUT]</p>
                </div>

                <div>
                    <label for="kondisi_aset" class="block text-sm font-medium text-gray-700 mb-2">
                        Kondisi Aset <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="kondisi_aset" 
                        name="kondisi_aset" 
                        required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('kondisi_aset') border-red-500 @enderror"
                    >
                        <option value="">Pilih Kondisi</option>
                        <option value="BAIK" {{ old('kondisi_aset') == 'BAIK' ? 'selected' : '' }}>Baik</option>
                        <option value="RUSAK_RINGAN" {{ old('kondisi_aset') == 'RUSAK_RINGAN' ? 'selected' : '' }}>Rusak Ringan</option>
                        <option value="RUSAK_BERAT" {{ old('kondisi_aset') == 'RUSAK_BERAT' ? 'selected' : '' }}>Rusak Berat</option>
                    </select>
                    @error('kondisi_aset')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="status_aset" class="block text-sm font-medium text-gray-700 mb-2">
                        Status Aset <span class="text-red-500">*</span>
                    </label>
                    <select 
                        id="status_aset" 
                        name="status_aset" 
                        required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('status_aset') border-red-500 @enderror"
                    >
                        <option value="">Pilih Status</option>
                        <option value="AKTIF" {{ old('status_aset', 'AKTIF') == 'AKTIF' ? 'selected' : '' }}>Aktif</option>
                        <option value="NONAKTIF" {{ old('status_aset') == 'NONAKTIF' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                    @error('status_aset')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="tanggal_perolehan" class="block text-sm font-medium text-gray-700 mb-2">
                        Tanggal Perolehan <span class="text-red-500">*</span>
                    </label>
                    <input 
                        type="date" 
                        id="tanggal_perolehan" 
                        name="tanggal_perolehan" 
                        value="{{ old('tanggal_perolehan', date('Y-m-d')) }}"
                        required
                        class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('tanggal_perolehan') border-red-500 @enderror"
                    >
                    @error('tanggal_perolehan')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="mt-1 text-xs text-gray-500">Tanggal ketika aset diperoleh</p>
                </div>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3 border-t border-gray-200 pt-6">
            <a 
                href="{{ route('asset.register-aset.index') }}" 
                class="px-5 py-2.5 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
                Batal
            </a>
            <button 
                type="submit" 
                class="px-5 py-2.5 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
            >
                Simpan Register Aset
            </button>
        </div>
    </form>
</div>
@endsection
