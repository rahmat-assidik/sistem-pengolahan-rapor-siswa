@extends('layouts.guest')

@section('title', 'Reset Sandi - Smart Rapor')

@section('content')
    <div class="w-full max-w-md">
        <div class="bg-white p-10 text-center rounded">
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
                <h2 class="text-sm font-black text-black tracking-tighter uppercase">Reset Sandi</h2>
            </div>

            @if ($errors->any())
                <div class="mb-4 text-xs text-red-600">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}" class="space-y-6 text-left">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="space-y-1">
                    <label for="email" class="text-xs font-semibold text-black">Email</label>
                    <input type="email" id="email" name="email" required
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded focus:border-black outline-none transition-none bg-gray-50"
                            placeholder="email@sekolah.sch.id" value="{{ $email ?? old('email') }}">
                </div>

                <div class="space-y-1">
                    <label for="password" class="text-xs font-semibold text-black">Kata Sandi Baru</label>
                    <input type="password" id="password" name="password" required
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded focus:border-black outline-none transition-none bg-gray-50">
                </div>

                <div class="space-y-1">
                    <label for="password_confirmation" class="text-xs font-semibold text-black">Konfirmasi Kata Sandi</label>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                            class="w-full px-4 py-2 text-sm border border-gray-300 rounded focus:border-black outline-none transition-none bg-gray-50">
                </div>

                <button type="submit" class="w-full py-3 bg-black text-white text-xs font-semibold tracking-tight rounded hover:bg-gray-800 transition-none">
                    Reset Sandi
                </button>
            </form>
        </div>
    </div>
@endsection
