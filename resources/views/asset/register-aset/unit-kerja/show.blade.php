@extends('layouts.app')

@php
    use App\Helpers\PermissionHelper;
    $user = auth()->user();
@endphp

@section('content')
<div class="mb-4">
    <a href="{{ route('asset.register-aset.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Index
    </a>
</div>

<!-- Page Header -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">{{ $title }} - Register Aset</h1>
        <p class="mt-1 text-sm text-gray-600">Daftar register aset di {{ $title }}</p>
    </div>
</div>

<!-- Badge Filter -->
<div class="mb-4 flex items-center space-x-2">
    <span class="text-sm font-medium text-gray-700">Filter:</span>
    <div class="flex space-x-2">
        <a href="{{ route('asset.register-aset.unit-kerja.show', ['unit_kerja' => $unitKerjaId, 'filter' => 'kib']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ $filter == 'kib' ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            KIB
        </a>
        <a href="{{ route('asset.register-aset.unit-kerja.show', ['unit_kerja' => $unitKerjaId, 'filter' => 'kir']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ $filter == 'kir' ? 'bg-orange-600 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            KIR
        </a>
        <a href="{{ route('asset.register-aset.unit-kerja.show', ['unit_kerja' => $unitKerjaId, 'filter' => 'semua']) }}" 
           class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                  {{ $filter == 'semua' ? 'bg-white text-gray-900 border-2 border-gray-300' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}">
            Semua
        </a>
    </div>
</div>

<!-- Filter Gudang (jika ada) -->
@if($gudangs->isNotEmpty())
<div class="mb-4 bg-white p-4 rounded-lg border border-gray-200">
    <form method="GET" action="{{ route('asset.register-aset.unit-kerja.show', ['unit_kerja' => $unitKerjaId]) }}" class="flex items-end space-x-4">
        <input type="hidden" name="filter" value="{{ $filter }}">
        <div class="flex-1">
            <label for="id_gudang" class="block text-sm font-medium text-gray-700 mb-1">
                Filter Gudang
            </label>
            <select 
                id="id_gudang" 
                name="id_gudang" 
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                onchange="this.form.submit()"
            >
                <option value="">Semua Gudang</option>
                @foreach($gudangs as $gudang)
                    <option value="{{ $gudang->id_gudang }}" {{ request('id_gudang') == $gudang->id_gudang ? 'selected' : '' }}>
                        {{ $gudang->nama_gudang }}
                        @if($gudang->unitKerja)
                            ({{ $gudang->unitKerja->nama_unit_kerja }})
                        @endif
                    </option>
                @endforeach
            </select>
        </div>
        @if(request('id_gudang'))
        <a href="{{ route('asset.register-aset.unit-kerja.show', ['unit_kerja' => $unitKerjaId, 'filter' => $filter]) }}" 
           class="px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50">
            Reset
        </a>
        @endif
    </form>
</div>
@endif

<!-- Table -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Register</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Merk</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gudang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Badge</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($registerAsets as $aset)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        {{ $aset->nomor_register }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $aset->inventory->dataBarang->nama_barang ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $aset->inventory->merk ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $aset->inventory->gudang->nama_gudang ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $kondisiColors = [
                                'BAIK' => 'bg-green-100 text-green-800',
                                'RUSAK_RINGAN' => 'bg-yellow-100 text-yellow-800',
                                'RUSAK_BERAT' => 'bg-red-100 text-red-800',
                            ];
                            $color = $kondisiColors[$aset->kondisi_aset] ?? 'bg-gray-100 text-gray-800';
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                            {{ str_replace('_', ' ', $aset->kondisi_aset) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($aset->status_aset == 'AKTIF')
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                AKTIF
                            </span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                NONAKTIF
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @php
                            $isKIB = $aset->inventory->gudang->jenis_gudang == 'PUSAT';
                            $isKIR = $aset->inventory->gudang->jenis_gudang == 'UNIT';
                        @endphp
                        @if($isKIB)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                KIB
                            </span>
                        @elseif($isKIR)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-orange-100 text-orange-800">
                                KIR
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                -
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <a 
                                href="{{ route('asset.register-aset.show', $aset->id_register_aset) }}" 
                                class="text-blue-600 hover:text-blue-900"
                            >
                                Detail
                            </a>
                            @if(PermissionHelper::canAccess($user, 'asset.register-aset.edit'))
                            <a 
                                href="{{ route('asset.register-aset.edit', $aset->id_register_aset) }}" 
                                class="text-indigo-600 hover:text-indigo-900"
                            >
                                Edit
                            </a>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="px-6 py-4 text-center text-sm text-gray-500">
                        Tidak ada data register aset.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($registerAsets->hasPages())
    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
        {{ $registerAsets->links() }}
    </div>
    @endif
</div>
@endsection

