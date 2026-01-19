<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'SI-MANTIK' }} - Sistem Informasi Manajemen Terintegrasi</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <aside class="w-64 bg-blue-900 text-white flex-shrink-0">
            <div class="h-full flex flex-col">
                <!-- Logo -->
                <div class="p-4 border-b border-blue-800">
                    <div class="flex items-center">
                        <img src="{{ asset('images/logo.png') }}" alt="SI-MANTIK" class="h-10 w-auto">
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="flex-1 overflow-y-auto p-4">
                    <ul class="space-y-2">
                        <li>
                            <a href="{{ route('user.dashboard') }}" class="flex items-center px-4 py-2 rounded-lg bg-blue-700 text-white">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </a>
                        </li>
                        <li>
                            <div class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 cursor-pointer" onclick="toggleSubmenu('master-manajemen')">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Master Manajemen
                                <svg id="master-manajemen-arrow" class="w-4 h-4 ml-auto transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <ul id="master-manajemen-submenu" class="hidden pl-4 mt-2 space-y-1">
                                <li><a href="{{ route('master-manajemen.master-pegawai.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Master Pegawai</a></li>
                                <li><a href="{{ route('master.unit-kerja.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Unit Kerja</a></li>
                                <li><a href="{{ route('master.gudang.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Gudang</a></li>
                                <li><a href="{{ route('master.ruangan.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Ruangan</a></li>
                                <li><a href="{{ route('master.program.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Program</a></li>
                                <li><a href="{{ route('master.kegiatan.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Kegiatan</a></li>
                                <li><a href="{{ route('master.sub-kegiatan.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Sub Kegiatan</a></li>
                            </ul>
                        </li>
                        <li>
                            <div class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 cursor-pointer" onclick="toggleSubmenu('master-data')">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4" />
                                </svg>
                                Master Data
                                <svg id="master-data-arrow" class="w-4 h-4 ml-auto transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <ul id="master-data-submenu" class="hidden pl-4 mt-2 space-y-1">
                                <li><a href="{{ route('master-data.aset.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Aset</a></li>
                                <li><a href="{{ route('master-data.kode-barang.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Kode Barang</a></li>
                                <li><a href="{{ route('master-data.kategori-barang.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Kategori Barang</a></li>
                                <li><a href="{{ route('master-data.jenis-barang.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Jenis Barang</a></li>
                                <li><a href="{{ route('master-data.subjenis-barang.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Subjenis Barang</a></li>
                                <li><a href="{{ route('master-data.data-barang.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Data Barang</a></li>
                                <li><a href="{{ route('master-data.satuan.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Satuan</a></li>
                                <li><a href="{{ route('master-data.sumber-anggaran.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Sumber Anggaran</a></li>
                            </ul>
                        </li>
                        <li>
                            <div class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 cursor-pointer" onclick="toggleSubmenu('inventory')">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                                Inventory
                                <svg id="inventory-arrow" class="w-4 h-4 ml-auto transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                            <ul id="inventory-submenu" class="hidden pl-4 mt-2 space-y-1">
                                <li><a href="{{ route('inventory.data-stock.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Data Stock</a></li>
                                <li><a href="{{ route('inventory.data-inventory.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Data Inventory</a></li>
                            </ul>
                        </li>
                        <li>
                            <div class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 cursor-pointer" onclick="toggleSubmenu('transaksi')">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                Transaksi
                                <svg id="transaksi-arrow" class="w-4 h-4 ml-auto transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </div>
                           <ul id="transaksi-submenu" class="hidden pl-4 mt-2 space-y-1">
                               <li><a href="{{ route('transaction.permintaan-barang.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Permintaan Barang</a></li>
                               <li><a href="{{ route('transaction.approval.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Persetujuan</a></li>
                               <li><a href="{{ route('transaction.distribusi.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Distribusi (SBBK)</a></li>
                               <li><a href="{{ route('transaction.penerimaan-barang.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Penerimaan Barang</a></li>
                           </ul>
                        </li>
                        <li>
                            <a href="{{ route('asset.register-aset.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Aset & KIR
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('reports.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800">
                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Laporan
                            </a>
                        </li>
                        @if(auth()->check())
                            @php
                                $currentUser = auth()->user();
                                $hasAdminRole = $currentUser && method_exists($currentUser, 'hasRole') && $currentUser->hasRole('admin');
                            @endphp
                            @if($hasAdminRole)
                            <li>
                                <div class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 cursor-pointer" onclick="toggleSubmenu('admin')">
                                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Admin
                                    <svg id="admin-arrow" class="w-4 h-4 ml-auto transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </div>
                                <ul id="admin-submenu" class="hidden pl-4 mt-2 space-y-1">
                                    <li><a href="{{ route('admin.roles.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Manajemen Role</a></li>
                                    <li><a href="{{ route('admin.users.index') }}" class="flex items-center px-4 py-2 rounded-lg text-blue-200 hover:bg-blue-800 text-sm">Manajemen User</a></li>
                                </ul>
                            </li>
                            @endif
                        @endif
                    </ul>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Header -->
            <header class="bg-white border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-xl font-bold text-gray-900">SISTEM MANAJEMEN ASET & INVENTORY</h1>
                            <p class="text-xs text-gray-500 mt-0.5">SINGLE DASHBOARD - ROLE-BASED VIEW</p>
                        </div>
                        <div class="flex items-center space-x-4">
                            <!-- Notifications -->
                            <button class="relative p-2 text-gray-600 hover:text-gray-900">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                </svg>
                                <span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-red-500 ring-2 ring-white"></span>
                            </button>
                            <button class="relative p-2 text-gray-600 hover:text-gray-900">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <span class="absolute top-0 right-0 block h-5 w-5 rounded-full bg-red-500 text-white text-xs flex items-center justify-center ring-2 ring-white">3</span>
                            </button>
                            <!-- User Menu -->
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name ?? 'AD') }}&background=1e40af&color=fff&size=128" alt="User" class="h-10 w-10 rounded-full">
                                <div class="text-left">
                                    <p class="text-sm font-medium text-gray-900">Administrator</p>
                                    <p class="text-xs text-gray-500">Admin</p>
                                </div>
                                <button type="button" class="text-gray-400 hover:text-gray-600 focus:outline-none">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-100">
                <div class="p-6">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    @stack('scripts')
    <script>
        function toggleSubmenu(id) {
            const submenu = document.getElementById(id + '-submenu');
            const arrow = document.getElementById(id + '-arrow');
            submenu.classList.toggle('hidden');
            arrow.classList.toggle('rotate-90');
        }
    </script>
</body>
</html>
