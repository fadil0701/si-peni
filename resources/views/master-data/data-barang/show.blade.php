@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('master-data.data-barang.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Daftar Data Barang
    </a>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-200">
    <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
        <h2 class="text-xl font-semibold text-gray-900">Detail Data Barang</h2>
        <a href="{{ route('master-data.data-barang.edit', $dataBarang->id_data_barang) }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Edit
        </a>
    </div>
    
    <div class="p-6">
        <div class="space-y-6">
            <!-- Informasi Dasar -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Kode Data Barang</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $dataBarang->kode_data_barang }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Nama Barang</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $dataBarang->nama_barang }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Subjenis Barang</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $dataBarang->subjenisBarang->nama_subjenis_barang ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Satuan</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $dataBarang->satuan->nama_satuan ?? '-' }}</dd>
                    </div>
                </dl>
            </div>

            <!-- Informasi Tambahan -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Tambahan</h3>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    @if($dataBarang->deskripsi)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Deskripsi</dt>
                        <dd class="text-sm text-gray-900">{{ $dataBarang->deskripsi }}</dd>
                    </div>
                    @endif
                    @if($dataBarang->upload_foto || $dataBarang->foto_barang)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Foto Barang</dt>
                        <dd class="text-sm text-gray-900">
                            <img src="{{ $dataBarang->upload_foto ? asset('storage/' . $dataBarang->upload_foto) : $dataBarang->foto_barang }}" 
                                 alt="Foto Barang" 
                                 class="h-48 w-auto rounded-md border border-gray-300 shadow-sm"
                                 onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'200\' height=\'200\'%3E%3Crect width=\'200\' height=\'200\' fill=\'%23f3f4f6\'/%3E%3Ctext x=\'50%25\' y=\'50%25\' text-anchor=\'middle\' dy=\'.3em\' fill=\'%239ca3af\' font-family=\'sans-serif\' font-size=\'14\'%3EFoto tidak ditemukan%3C/text%3E%3C/svg%3E';">
                        </dd>
                    </div>
                    @endif
                </dl>
            </div>
        </div>
    </div>
</div>
@endsection

