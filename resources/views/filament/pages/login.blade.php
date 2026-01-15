<x-filament-panels::page.simple>
    <div class="flex flex-col items-center justify-center min-h-screen bg-gray-50">
        <div class="w-full max-w-md">
            {{-- Logo & Title --}}
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="w-24 h-24 bg-blue-600 rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">
                    SISTEM MANAJEMEN ASET & INVENTORY
                </h1>
                <p class="text-sm text-gray-600">SI-MANTIK</p>
            </div>

            {{-- Login Form --}}
            <div class="bg-white rounded-lg shadow-md p-8">
                <form wire:submit="authenticate" class="space-y-6">
                    {{-- Email Field --}}
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email
                        </label>
                        <input 
                            type="email" 
                            id="email"
                            wire:model="email"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan email Anda"
                            required
                            autofocus
                        >
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Field --}}
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            Password
                        </label>
                        <input 
                            type="password" 
                            id="password"
                            wire:model="password"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Masukkan password Anda"
                            required
                        >
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Remember Me & Forgot Password --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input 
                                id="remember" 
                                type="checkbox" 
                                wire:model="remember"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            >
                            <label for="remember" class="ml-2 block text-sm text-gray-700">
                                Ingat Saya
                            </label>
                        </div>
                        <div>
                            <a href="{{ route('filament.admin.auth.password-reset.request') }}" class="text-sm text-blue-600 hover:text-blue-800">
                                Lupa Password?
                            </a>
                        </div>
                    </div>

                    {{-- Login Button --}}
                    <button 
                        type="submit"
                        class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150"
                    >
                        Login
                    </button>
                </form>
            </div>

            {{-- Footer --}}
            <div class="mt-6 text-center text-sm text-gray-500">
                <p>&copy; {{ date('Y') }} SI-MANTIK. All rights reserved.</p>
            </div>
        </div>
    </div>
</x-filament-panels::page.simple>
