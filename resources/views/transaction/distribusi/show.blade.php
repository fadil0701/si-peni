@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('transaction.distribusi.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Daftar Distribusi
    </a>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-200">
    <div class="px-6 py-5 border-b border-gray-200 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-semibold text-gray-900">Detail Distribusi Barang (SBBK)</h2>
            <p class="text-sm text-gray-600 mt-1">No. SBBK: <span class="font-semibold">{{ $distribusi->no_sbbk }}</span></p>
        </div>
        <div class="flex space-x-3">
            @if($distribusi->status_distribusi == 'DRAFT')
                <a 
                    href="{{ route('transaction.distribusi.edit', $distribusi->id_distribusi) }}" 
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors"
                >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                    Edit
                </a>
                <form 
                    action="{{ route('transaction.distribusi.kirim', $distribusi->id_distribusi) }}" 
                    method="POST" 
                    class="inline"
                    onsubmit="return confirm('Apakah Anda yakin ingin mengirim distribusi ini? Stok gudang akan diperbarui.');"
                >
                    @csrf
                    <button 
                        type="submit" 
                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
                    >
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        Kirim Distribusi
                    </button>
                </form>
            @endif
        </div>
    </div>
    
    <div class="p-6">
        <div class="grid grid-cols-1 gap-6">
            <!-- Informasi Distribusi -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Distribusi</h3>
                <dl class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">No. SBBK</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $distribusi->no_sbbk }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Status</dt>
                        <dd class="text-sm font-semibold text-gray-900">
                            @php
                                $statusColor = match($distribusi->status_distribusi) {
                                    'DIKIRIM' => 'bg-blue-100 text-blue-800',
                                    'SELESAI' => 'bg-green-100 text-green-800',
                                    'DRAFT' => 'bg-yellow-100 text-yellow-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                {{ $distribusi->status_distribusi }}
                            </span>
                        </dd>
                    </div>
                    @if($distribusi->permintaan)
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">No. Permintaan</dt>
                        <dd class="text-sm font-semibold text-gray-900">
                            <a href="{{ route('transaction.permintaan-barang.show', $distribusi->permintaan->id_permintaan) }}" class="text-blue-600 hover:text-blue-900">
                                {{ $distribusi->permintaan->no_permintaan }}
                            </a>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Unit Kerja Pemohon</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $distribusi->permintaan->unitKerja->nama_unit_kerja ?? '-' }}</dd>
                    </div>
                    @endif
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Tanggal Distribusi</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $distribusi->tanggal_distribusi->format('d/m/Y H:i') }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Gudang Asal</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $distribusi->gudangAsal->nama_gudang ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Gudang Tujuan</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $distribusi->gudangTujuan->nama_gudang ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Pegawai Pengirim</dt>
                        <dd class="text-sm font-semibold text-gray-900">{{ $distribusi->pegawaiPengirim->nama_pegawai ?? '-' }}</dd>
                    </div>
                    @if($distribusi->keterangan)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Keterangan</dt>
                        <dd class="text-sm text-gray-900">{{ $distribusi->keterangan }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <!-- Detail Distribusi -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Distribusi ({{ $distribusi->detailDistribusi->count() }} item)</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">No</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Barang</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Qty Distribusi</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Satuan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga Satuan</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @php $total = 0; @endphp
                            @foreach($distribusi->detailDistribusi as $index => $detail)
                            @php $total += $detail->subtotal; @endphp
                            <tr>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $index + 1 }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                    {{ $detail->inventory->dataBarang->nama_barang ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ number_format($detail->qty_distribusi, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $detail->satuan->nama_satuan ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">Rp {{ number_format($detail->harga_satuan, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm font-semibold text-gray-900">Rp {{ number_format($detail->subtotal, 2, ',', '.') }}</td>
                                <td class="px-4 py-3 text-sm text-gray-900">{{ $detail->keterangan ?? '-' }}</td>
                            </tr>
                            @endforeach
                            <tr class="bg-gray-50 font-semibold">
                                <td colspan="5" class="px-4 py-3 text-sm text-gray-900 text-right">Total</td>
                                <td class="px-4 py-3 text-sm text-gray-900">Rp {{ number_format($total, 2, ',', '.') }}</td>
                                <td></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

