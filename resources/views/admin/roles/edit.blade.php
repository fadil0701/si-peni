@extends('layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.roles.index') }}" class="text-blue-600 hover:text-blue-900 inline-flex items-center">
        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Kembali ke Daftar Role
    </a>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-200">
    <div class="px-6 py-5 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-gray-900">Edit Role</h2>
    </div>
    
    <form action="{{ route('admin.roles.update', $role->id) }}" method="POST" class="p-6">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nama Role <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    required
                    value="{{ old('name', $role->name) }}"
                    placeholder="contoh: admin, admin_gudang, kepala, pegawai"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('name') border-red-500 @enderror"
                >
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="display_name" class="block text-sm font-medium text-gray-700 mb-2">
                    Display Name <span class="text-red-500">*</span>
                </label>
                <input 
                    type="text" 
                    id="display_name" 
                    name="display_name" 
                    required
                    value="{{ old('display_name', $role->display_name) }}"
                    placeholder="contoh: Admin, Admin Gudang, Kepala/Pimpinan"
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm @error('display_name') border-red-500 @enderror"
                >
                @error('display_name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                <textarea 
                    id="description" 
                    name="description" 
                    rows="3"
                    placeholder="Masukkan deskripsi role..."
                    class="block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm"
                >{{ old('description', $role->description) }}</textarea>
            </div>
        </div>

        <div class="mt-8 flex justify-end space-x-3 border-t border-gray-200 pt-6">
            <a 
                href="{{ route('admin.roles.index') }}" 
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
@endsection

