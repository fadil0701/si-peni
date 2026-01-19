@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="mb-6">
        <a href="{{ route('user.requests') }}" class="text-blue-600 hover:text-blue-900">← Kembali ke Daftar Permintaan</a>
    </div>

    <div class="bg-white shadow rounded-lg overflow-hidden">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Detail Permintaan</h3>
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                <div>
                    <dt class="text-sm font-medium text-gray-500">No. Permintaan</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $request->no_permintaan }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Tanggal</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ $request->tanggal_permintaan->format('d M Y') }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Status</dt>
                    <dd class="mt-1">
                        @php
                            $color = match($request->status_permintaan) {
                                'DIAJUKAN' => 'bg-yellow-100 text-yellow-800',
                                'DISETUJUI' => 'bg-green-100 text-green-800',
                                'DITOLAK' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800',
                            };
                        @endphp
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">
                            {{ $request->status_permintaan }}
                        </span>
                    </dd>
                </div>
                @if($request->keterangan)
                    <div class="sm:col-span-2">
                        <dt class="text-sm font-medium text-gray-500">Keterangan</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $request->keterangan }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>
</div>
@endsection

