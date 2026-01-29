@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Register Aset (KIB & KIR)</h1>
        <p class="mt-1 text-sm text-gray-600">Pilih gudang unit untuk melihat detail aset</p>
    </div>
    @php
        use App\Helpers\PermissionHelper;
        $user = auth()->user();
    @endphp
    @if(PermissionHelper::canAccess($user, 'asset.register-aset.create'))
    <a 
        href="{{ route('asset.register-aset.create') }}" 
        class="inline-flex items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
    >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
        </svg>
        Tambah Register Aset
    </a>
    @endif
</div>

<!-- Badge Unit Kerja Grid -->
<div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
    <!-- Card Gudang Pusat (KIB) -->
    @if($gudangPusatData && $gudangPusatData['total_aset'] > 0)
    <a href="{{ route('asset.register-aset.unit-kerja.show', ['unit_kerja' => 'pusat']) }}" 
       class="bg-white rounded-lg shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow cursor-pointer">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ $gudangPusatData['nama'] }}</h3>
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                KIB
            </span>
        </div>
        <div class="space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Total Aset:</span>
                <span class="text-sm font-semibold text-gray-900">{{ number_format($gudangPusatData['total_aset'], 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">KIB:</span>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                    {{ number_format($gudangPusatData['kib_count'], 0, ',', '.') }}
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">KIR:</span>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                    {{ number_format($gudangPusatData['kir_count'], 0, ',', '.') }}
                </span>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200">
            <span class="text-xs text-blue-600 font-medium">Klik untuk melihat detail →</span>
        </div>
    </a>
    @endif
    
    <!-- Card Gudang Unit -->
    @forelse($gudangUnits as $gudang)
    <a href="{{ route('asset.register-aset.unit-kerja.show', ['unit_kerja' => $gudang->id_gudang]) }}" 
       class="bg-white rounded-lg shadow-md border border-gray-200 p-6 hover:shadow-lg transition-shadow cursor-pointer">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900">{{ $gudang->nama_gudang }}</h3>
            <span class="px-3 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                KIR
            </span>
        </div>
        <div class="space-y-2">
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">Total Aset:</span>
                <span class="text-sm font-semibold text-gray-900">{{ number_format($gudang->total_aset, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">KIB:</span>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                    {{ number_format($gudang->kib_count ?? 0, 0, ',', '.') }}
                </span>
            </div>
            <div class="flex justify-between items-center">
                <span class="text-sm text-gray-600">KIR:</span>
                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                    {{ number_format($gudang->kir_count ?? 0, 0, ',', '.') }}
                </span>
            </div>
        </div>
        <div class="mt-4 pt-4 border-t border-gray-200">
            <span class="text-xs text-blue-600 font-medium">Klik untuk melihat detail →</span>
        </div>
    </a>
    @empty
    <div class="col-span-full">
        <div class="bg-white rounded-lg shadow-md border border-gray-200 p-6 text-center">
            <p class="text-gray-500">Belum ada gudang unit dengan aset</p>
        </div>
    </div>
    @endforelse
</div>

@if($gudangUnits->isEmpty() && (!$gudangPusatData || $gudangPusatData['total_aset'] == 0))
<div class="mt-6 bg-white rounded-lg shadow-md border border-gray-200 p-6 text-center">
    <p class="text-gray-500">Belum ada data register aset</p>
</div>
@endif
@endsection
