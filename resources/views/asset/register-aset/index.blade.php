@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="mb-6 flex justify-between items-center">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Register Aset</h1>
        <p class="mt-1 text-sm text-gray-600">Daftar semua register aset dan KIR</p>
    </div>
    @if(auth()->user()->hasAnyRole(['admin', 'admin_gudang']))
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

<!-- Table -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No Register</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Barang</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Kerja</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kondisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Perolehan</th>
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
                        {{ $aset->inventory->nama_barang ?? '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $aset->unitKerja->nama_unit_kerja ?? '-' }}
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
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $aset->tanggal_perolehan ? $aset->tanggal_perolehan->format('d/m/Y') : '-' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <div class="flex justify-end space-x-2">
                            <a 
                                href="{{ route('asset.register-aset.show', $aset->id_register_aset) }}" 
                                class="text-blue-600 hover:text-blue-900"
                            >
                                Detail
                            </a>
                            @if(auth()->user()->hasAnyRole(['admin', 'admin_gudang']) || 
                                (auth()->user()->hasAnyRole(['kepala_unit', 'pegawai']) && 
                                 auth()->user()->pegawai && 
                                 auth()->user()->pegawai->id_unit_kerja == $aset->id_unit_kerja))
                            <a 
                                href="{{ route('asset.register-aset.edit', $aset->id_register_aset) }}" 
                                class="text-indigo-600 hover:text-indigo-900"
                            >
                                Edit
                            </a>
                            @endif
                            @if(auth()->user()->hasAnyRole(['admin', 'admin_gudang']))
                            <form 
                                action="{{ route('asset.register-aset.destroy', $aset->id_register_aset) }}" 
                                method="POST" 
                                class="inline"
                                onsubmit="return confirm('Apakah Anda yakin ingin menghapus register aset ini?');"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-900">
                                    Hapus
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
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
