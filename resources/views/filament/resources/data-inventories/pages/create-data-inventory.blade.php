<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form Section (Left - 2 columns) --}}
        <div class="lg:col-span-2">
            <x-filament-panels::form wire:submit="create">
                {{ $this->form }}
                
                <x-filament-panels::form.actions
                    :actions="$this->getCachedFormActions()"
                    :full-width="$this->hasFullWidthFormActions()"
                />
            </x-filament-panels::form>
        </div>
        
        {{-- Preview Panel (Right - 1 column) --}}
        <div class="lg:col-span-1">
            <x-filament::section>
                <div class="space-y-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                        Preview Produk
                    </h3>
                    
                    {{-- Product Image --}}
                    <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 aspect-square flex items-center justify-center">
                        <div x-show="!$wire.get('data.dataBarang')" class="text-center text-gray-400">
                            <svg class="w-24 h-24 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            <p class="text-sm">Pilih barang untuk melihat preview</p>
                        </div>
                        <div x-show="$wire.get('data.dataBarang')" class="text-center">
                            <img 
                                x-bind:src="$wire.get('dataBarang.upload_foto') ? '/storage/' + $wire.get('dataBarang.upload_foto') : 'https://via.placeholder.com/300?text=No+Image'"
                                alt="Product Image"
                                class="w-full h-auto rounded-lg"
                            />
                        </div>
                    </div>
                    
                    {{-- Product Details --}}
                    <div x-show="$wire.get('data.dataBarang')" class="space-y-2">
                        <h4 class="font-semibold text-gray-900 dark:text-white" x-text="$wire.get('dataBarang.nama_barang') || '-'"></h4>
                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <p><span class="font-medium">Merk:</span> <span x-text="$wire.get('data.merk') || '-'"></span></p>
                            <p><span class="font-medium">Type:</span> <span x-text="$wire.get('data.tipe') || '-'"></span></p>
                            <p><span class="font-medium">Spesifikasi:</span> <span x-text="$wire.get('data.spesifikasi') || '-'"></span></p>
                            <p><span class="font-medium">Tahun:</span> <span x-text="$wire.get('data.tahun_produksi') || '-'"></span></p>
                        </div>
                    </div>
                    
                    {{-- QR Code Section --}}
                    <div x-show="$wire.get('data.id_data_barang')" class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">
                            QR Code
                        </h4>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg flex items-center justify-center">
                            <div class="w-48 h-48 text-gray-400 text-center flex items-center justify-center">
                                <p class="text-sm">QR Code akan tersedia setelah data disimpan</p>
                            </div>
                        </div>
                        <button 
                            type="button"
                            class="w-full mt-3 bg-gray-400 text-white font-medium py-2 px-4 rounded-lg transition cursor-not-allowed"
                            disabled
                        >
                            Unduh QR >
                        </button>
                    </div>
                </div>
            </x-filament::section>
        </div>
    </div>
    
    <script>
        function downloadQRCode() {
            // TODO: Implement QR code download
            alert('Fitur download QR Code akan segera tersedia');
        }
    </script>
</x-filament-panels::page>

