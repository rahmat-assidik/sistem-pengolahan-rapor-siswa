@extends('layouts.guest')

@section('title', 'Login - Smart Rapor')

@section('content')
    <div class="w-full max-w-md" x-data="{ showPass: false, isLoading: false }">
        {{-- Ultra-Flat Card --}}
        <div class="bg-white p-10 rounded"> {{-- Standardized to 4px --}}
            
            {{-- Simple Logo --}}
            {{-- Preferred Logo Style --}}
            <div class="flex items-center gap-2 mb-10 justify-center">
                <div class="w-8 h-8 bg-gray-900 rounded flex items-center justify-center text-white">
                    <i class="fa-solid fa-graduation-cap text-xs"></i>
                </div>
                <div class="text-left">
                    <h1 class="text-sm font-black tracking-tighter text-gray-900 leading-none">Smart<span class="text-blue-600">Rapor</span></h1>
                    <p class="text-[9px] font-medium text-gray-400 tracking-tight mt-0.5">Management</p>
                </div>
            </div>

            {{-- Form --}}
            <form method="POST" action="{{ route('login.post') }}" class="space-y-6" @submit="isLoading = true">
                @csrf

                @if($errors->has('username'))
                <div class="p-3 bg-red-50 border border-red-200 text-red-600 text-[10px] font-semibold rounded">
                    {{ $errors->first('username') }}
                </div>
                @endif

                <div class="space-y-1">
                    <label for="username" class="text-xs font-semibold text-black">NIP</label>
                    <input type="text" id="username" name="username" required
                           class="w-full px-4 py-2 text-sm border border-gray-300 rounded focus:border-black outline-none transition-none bg-gray-50"
                           placeholder="NIP Anda">
                </div>

                <div class="space-y-1">
                    <div class="flex justify-between items-center">
                        <label for="password" class="text-xs font-semibold text-black">Password</label>
                        <a href="{{ route('lupa_sandi') }}" class="text-xs text-gray-500 hover:text-black">Lupa sandi?</a>
                    </div>
                    <div class="relative">
                        <input type="password" :type="showPass ? 'text' : 'password'" id="password" name="password" required
                               class="w-full px-4 py-2 text-sm border border-gray-300 rounded focus:border-black outline-none transition-none bg-gray-50"
                               placeholder="••••••••">
                        <button type="button" @click="showPass = !showPass" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-black">
                            <i class="fa-solid" :class="showPass ? 'fa-eye-slash' : 'fa-eye' shadow-none"></i>
                        </button>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <input type="checkbox" name="remember" class="w-3.5 h-3.5 border-gray-300 rounded text-black focus:ring-0 cursor-pointer">
                        <span class="text-xs font-semibold text-black transition-colors">Ingat saya</span>
                    </label>
                </div>

                <button type="submit" 
                        :disabled="isLoading"
                        class="w-full py-3 bg-black text-white text-xs font-semibold tracking-tight rounded hover:bg-gray-800 transition-all flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">
                    <template x-if="!isLoading">
                        <span>Login</span>
                    </template>
                    <template x-if="isLoading">
                        <div class="flex items-center gap-2">
                            <i class="fa-solid fa-spinner animate-spin"></i>
                            <span>Memproses...</span>
                        </div>
                    </template>
                </button>
            </form>
        </div>
    </div>
@endsection