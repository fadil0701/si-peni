<x-filament-panels::page>
    {{-- Custom Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            SISTEM MANAJEMEN ASET & INVENTORY
        </h1>
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            SINGLE DASHBOARD - ROLE-BASED VIEW
        </p>
    </div>
    
    {{-- Widgets --}}
    <x-filament-widgets::widgets
        :widgets="$this->getWidgets()"
        :columns="$this->getColumns()"
    />
</x-filament-panels::page>

