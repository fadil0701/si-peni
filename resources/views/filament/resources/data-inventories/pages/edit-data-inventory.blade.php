<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Form Section (Left - 2 columns) --}}
        <div class="lg:col-span-2">
            <x-filament-panels::form wire:submit="save">
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
                        @if($this->record->dataBarang && $this->record->dataBarang->upload_foto)
                            <img 
                                src="{{ asset('storage/' . $this->record->dataBarang->upload_foto) }}"
                                alt="Product Image"
                                class="w-full h-auto rounded-lg"
                            />
                        @else
                            <div class="text-center text-gray-400">
                                <svg class="w-24 h-24 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-sm">Tidak ada gambar</p>
                            </div>
                        @endif
                    </div>
                    
                    {{-- Product Details --}}
                    <div class="space-y-2">
                        <h4 class="font-semibold text-gray-900 dark:text-white">
                            {{ $this->record->dataBarang->nama_barang ?? '-' }}
                        </h4>
                        <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <p><span class="font-medium">Merk:</span> {{ $this->record->merk ?? '-' }}</p>
                            <p><span class="font-medium">Type:</span> {{ $this->record->tipe ?? '-' }}</p>
                            <p><span class="font-medium">Spesifikasi:</span> {{ $this->record->spesifikasi ?? '-' }}</p>
                            <p><span class="font-medium">Tahun:</span> {{ $this->record->tahun_produksi ?? '-' }}</p>
                        </div>
                    </div>
                    
                    {{-- QR Code Section --}}
                    @php
                        $qrData = $this->record->id_data_inventory . '-' . ($this->record->dataBarang->kode_barang ?? 'INV');
                    @endphp
                    <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h4 class="font-semibold text-gray-900 dark:text-white mb-3">
                            QR Code
                        </h4>
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg flex items-center justify-center">
                            <div class="w-48 h-48">
                                {!! \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate($qrData) !!}
                            </div>
                        </div>
                        <a 
                            href="{{ route('inventory.qrcode.download', $this->record->id_data_inventory) }}"
                            class="block w-full mt-3 bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition text-center"
                        >
                            Unduh QR >
                        </a>
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

