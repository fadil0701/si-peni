<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    Alur Distribusi Barang
                </h3>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    Proses distribusi barang dari gudang pusat ke unit
                </p>
            </div>
            
            <div class="flex items-center justify-center space-x-4 py-4">
                <!-- Gudang Pusat -->
                <div class="bg-gray-100 dark:bg-gray-800 rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-2xl mb-2">📦</div>
                    <div class="font-semibold text-gray-900 dark:text-white">Gudang Pusat</div>
                </div>
                
                <!-- Arrow -->
                <div class="text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </div>
                
                <!-- Gudang Unit -->
                <div class="bg-blue-100 dark:bg-blue-900 rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-2xl mb-2">🏢</div>
                    <div class="font-semibold text-gray-900 dark:text-white">Gudang Unit</div>
                </div>
                
                <!-- Arrow Down -->
                <div class="text-gray-400 flex flex-col items-center">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </div>
            </div>
            
            <div class="flex items-center justify-center space-x-4">
                <!-- Kirim ke Ruangan -->
                <div class="bg-green-100 dark:bg-green-900 rounded-lg p-4 text-center min-w-[120px]">
                    <div class="text-2xl mb-2">🚪</div>
                    <div class="font-semibold text-gray-900 dark:text-white">Kirim ke Ruangan</div>
                </div>
                
                <!-- Arrow -->
                <div class="text-gray-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                    </svg>
                </div>
                
                <!-- Pemakaian -->
                <div class="bg-blue-600 dark:bg-blue-800 rounded-lg p-4 text-center min-w-[120px] text-white">
                    <div class="text-2xl mb-2">💼</div>
                    <div class="font-semibold">Pemakaian</div>
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>

