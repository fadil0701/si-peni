@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('transaction.retur-barang.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Daftar Retur
    </a>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-200">
    <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Detail Retur Barang</h2>
            <p class="text-sm text-gray-600 mt-1">No. Retur: <span class="font-semibold">{{ $retur->no_retur }}</span></p>
        </div>
        <div class="flex space-x-3">
            @if(in_array($retur->status_retur, ['DRAFT', 'DIAJUKAN']))
                <a 
                    href="{{ route('transaction.retur-barang.edit', $retur->id_retur) }}" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
            @endif
        </div>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 gap-6">
            <!-- Informasi Retur -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Retur</h3>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">No. Retur</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $retur->no_retur }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Status</dt>
                        <dd class="text-sm font-semibold text-gray-900">
                            @php
                                $statusColor = match($retur->status_retur) {
                                    'DRAFT' => 'bg-gray-100 text-gray-800',
                                    'DIAJUKAN' => 'bg-yellow-100 text-yellow-800',
                                    'DITERIMA' => 'bg-green-100 text-green-800',
                                    'DITOLAK' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                {{ $retur->status_retur }}
                            </span>
                        </dd>
                    </div>
                    @if($retur->penerimaan)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">No. Penerimaan</dt>
                        <dd class="text-sm font-semibold text-gray-900">
                            <a href="{{ route('transaction.penerimaan-barang.show', $retur->penerimaan->id_penerimaan) }}" class="text-blue-600 hover:text-blue-900">
                                {{ $retur->penerimaan->no_penerimaan ?? '-' }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    @if($retur->distribusi)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">No. SBBK</dt>
                        <dd class="text-sm font-semibold text-gray-900">
                            <a href="{{ route('transaction.distribusi.show', $retur->distribusi->id_distribusi) }}" class="text-blue-600 hover:text-blue-900">
                                {{ $retur->distribusi->no_sbbk ?? '-' }}
                            </a>
                        </dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Unit Kerja</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $retur->unitKerja->nama_unit_kerja ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Gudang Asal</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $retur->gudangAsal->nama_gudang ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Gudang Tujuan</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $retur->gudangTujuan->nama_gudang ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Pegawai Pengirim</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $retur->pegawaiPengirim->nama_pegawai ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Tanggal Retur</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $retur->tanggal_retur->format('d/m/Y') }}</dd>
                    </div>
                    @if($retur->alasan_retur)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Alasan Retur</dt>
                        <dd class="text-sm text-gray-900">{{ $retur->alasan_retur }}</dd>
                    </div>
                    @endif
                    @if($retur->keterangan)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Keterangan</dt>
                        <dd class="text-sm text-gray-900">{{ $retur->keterangan }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Detail Retur -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Retur ({{ $retur->detailRetur->count() }} item)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty Retur</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Alasan Retur Item</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($retur->detailRetur as $index => $detail)
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $detail->inventory->dataBarang->nama_barang ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($detail->qty_retur, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $detail->satuan->nama_satuan ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $detail->alasan_retur_item ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $detail->keterangan ?? '-' }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection



