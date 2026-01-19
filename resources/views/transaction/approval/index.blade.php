@extends('layouts.app')

@section('content')
<!-- Page Header -->
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Persetujuan Permintaan Barang</h1>
    <p class="mt-1 text-sm text-gray-600">Daftar permintaan barang yang menunggu persetujuan</p>
</div>

<!-- Filters -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 p-4 mb-6">
    <form method="GET" action="{{ route('transaction.approval.index') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div>
            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select 
                id="status" 
                name="status" 
                class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
            >
                <option value="">Semua Status</option>
                <option value="MENUNGGU" {{ request('status') == 'MENUNGGU' ? 'selected' : '' }}>Menunggu</option>
                <option value="DISETUJUI" {{ request('status') == 'DISETUJUI' ? 'selected' : '' }}>Disetujui</option>
                <option value="DITOLAK" {{ request('status') == 'DITOLAK' ? 'selected' : '' }}>Ditolak</option>
            </select>
        </div>

        <div class="sm:col-span-2 flex items-end">
            <button 
                type="submit" 
                class="w-full sm:w-auto px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
                Filter
            </button>
            @if(request('status'))
                <a 
                    href="{{ route('transaction.approval.index') }}" 
                    class="ml-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-md shadow-sm text-gray-700 bg-white hover:bg-gray-50"
                >
                    Reset
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Success/Error Messages -->
@if(session('success'))
    <div class="mb-4 bg-green-50 border-l-4 border-green-400 p-4 rounded">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-green-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    </div>
@endif

@if(session('error'))
    <div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
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

<!-- Table Card -->
<div class="bg-white shadow-sm rounded-lg border border-gray-200 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID Referensi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Modul</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($approvals as $approval)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">#{{ $approval->id_referensi }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $approval->modul_approval }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $statusColor = match($approval->status_approval) {
                                    'MENUNGGU' => 'bg-yellow-100 text-yellow-800',
                                    'DISETUJUI' => 'bg-green-100 text-green-800',
                                    'DITOLAK' => 'bg-red-100 text-red-800',
                                    default => 'bg-gray-100 text-gray-800',
                                };
                            @endphp
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColor }}">
                                {{ $approval->status_approval }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $approval->created_at->format('d/m/Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <a 
                                href="{{ route('transaction.approval.show', $approval->id_approval) }}" 
                                class="text-blue-600 hover:text-blue-900 transition-colors"
                            >
                                Detail
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada data</h3>
                            <p class="mt-1 text-sm text-gray-500">Tidak ada permintaan yang menunggu persetujuan.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    @if($approvals->hasPages())
        <div class="bg-gray-50 px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $approvals->links() }}
        </div>
    @endif
</div>
@endsection

