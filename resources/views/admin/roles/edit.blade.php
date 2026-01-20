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

            <!-- Permissions -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-4">
                    Hak Akses (Permissions) <span class="text-red-500">*</span>
                </label>
                <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 max-h-96 overflow-y-auto">
                    @foreach($permissions as $module => $modulePermissions)
                        @php
                            $modulePermissionIds = $modulePermissions->pluck('id')->toArray();
                            $checkedInModule = array_intersect($modulePermissionIds, $rolePermissions);
                            $allChecked = count($checkedInModule) === count($modulePermissionIds);
                            $someChecked = count($checkedInModule) > 0 && count($checkedInModule) < count($modulePermissionIds);
                        @endphp
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-3">
                                <h4 class="text-sm font-semibold text-gray-900 uppercase tracking-wide">
                                    {{ str_replace('-', ' ', $module) }}
                                </h4>
                                <label class="flex items-center text-xs text-blue-600 hover:text-blue-800 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        class="h-3 w-3 text-blue-600 focus:ring-blue-500 border-gray-300 rounded module-select-all"
                                        data-module="{{ $module }}"
                                        {{ $allChecked ? 'checked' : '' }}
                                        {{ $someChecked ? 'indeterminate' : '' }}
                                    >
                                    <span class="ml-1">Pilih Semua</span>
                                </label>
                            </div>
                            <div class="space-y-2 pl-4 module-permissions" data-module="{{ $module }}">
                                @foreach($modulePermissions as $permission)
                                    <label class="flex items-start">
                                        <input 
                                            type="checkbox" 
                                            name="permissions[]" 
                                            value="{{ $permission->id }}"
                                            {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }}
                                            class="mt-1 h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded permission-checkbox"
                                            data-module="{{ $module }}"
                                        >
                                        <div class="ml-2">
                                            <span class="text-sm text-gray-700 font-medium">{{ $permission->display_name }}</span>
                                            @if($permission->description)
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $permission->description }}</p>
                                            @endif
                                            <p class="text-xs text-gray-400 mt-0.5 font-mono">{{ $permission->name }}</p>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <p class="mt-2 text-xs text-gray-500">Pilih hak akses yang akan diberikan kepada role ini</p>
            </div>

            @push('scripts')
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Handle "Select All" per module
                document.querySelectorAll('.module-select-all').forEach(function(selectAll) {
                    selectAll.addEventListener('change', function() {
                        const module = this.dataset.module;
                        const checkboxes = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
                        checkboxes.forEach(function(checkbox) {
                            checkbox.checked = selectAll.checked;
                        });
                    });
                });

                // Update "Select All" checkbox when individual checkboxes change
                document.querySelectorAll('.permission-checkbox').forEach(function(checkbox) {
                    checkbox.addEventListener('change', function() {
                        const module = this.dataset.module;
                        const moduleCheckboxes = document.querySelectorAll(`.permission-checkbox[data-module="${module}"]`);
                        const checkedCount = Array.from(moduleCheckboxes).filter(cb => cb.checked).length;
                        const selectAll = document.querySelector(`.module-select-all[data-module="${module}"]`);
                        if (selectAll) {
                            selectAll.checked = checkedCount === moduleCheckboxes.length;
                            selectAll.indeterminate = checkedCount > 0 && checkedCount < moduleCheckboxes.length;
                        }
                    });
                });
            });
            </script>
            @endpush
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

