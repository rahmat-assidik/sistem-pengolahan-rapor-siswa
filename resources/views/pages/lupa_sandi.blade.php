@extends('layouts.guest')

@section('title', 'Lupa Sandi - Smart Rapor')

@section('content')
    <div class="w-full max-w-md">
        {{-- Ultra-Flat Card --}}
        <div class="bg-white p-10 text-center rounded">
            
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

            <div class="mb-6">
                <h2 class="text-sm font-black text-black tracking-tighter uppercase">Lupa Sandi</h2>
                <p class="text-[11px] text-gray-500 mt-2">Masukkan email Anda untuk instruksi pemulihan.</p>
            </div>

            {{-- Form --}}
            <div x-data="{ loading: false, sent: false }">
                <form method="POST" action="#" @submit.prevent="loading = true; setTimeout(() => { loading = false; sent = true; }, 1500)" class="space-y-6 text-left" x-show="!sent">
                    @csrf

                    <div class="space-y-1">
                        <label for="email" class="text-xs font-semibold text-black">Email</label>
                        <input type="email" id="email" name="email" required
                               class="w-full px-4 py-2 text-sm border border-gray-300 rounded focus:border-black outline-none transition-none bg-gray-50"
                               placeholder="email@sekolah.sch.id">
                    </div>

                    <button type="submit" :disabled="loading" class="w-full py-3 bg-black text-white text-xs font-semibold tracking-tight rounded hover:bg-gray-800 transition-none disabled:opacity-50">
                        <span x-show="!loading">Kirim Link Reset</span>
                        <span x-show="loading" style="display: none;"><i class="fa-solid fa-spinner fa-spin mr-2"></i> Mengirim...</span>
                    </button>
                </form>

                {{-- Success Message --}}
                <div x-show="sent" style="display: none;" class="text-center p-4 bg-green-50 rounded border border-green-100">
                    <i class="fa-solid fa-envelope-circle-check text-2xl text-green-600 mb-2"></i>
                    <h3 class="text-sm font-bold text-gray-900 mb-1">Email Terkirim!</h3>
                    <p class="text-[11px] text-gray-600">Periksa kotak masuk email Anda untuk instruksi reset kata sandi.</p>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t border-gray-100 text-xs">
                <a href="{{ route('login') }}" class="font-semibold text-gray-400 hover:text-black">Kembali ke login</a>
            </div>
        </div>
    </div>
@endsection