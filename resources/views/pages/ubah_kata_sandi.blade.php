@extends('layouts.app')
@section('title', 'Pengaturan Akun')

@section('content')
    <div class="max-w-5xl mx-auto" x-data="accountSettings()">
        <div class="mb-6 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 tracking-tight">Pengaturan Akun</h1>
                <p class="text-sm text-gray-500 mt-1 font-medium">Kelola identitas dan keamanan akses Anda ke sistem.</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-1 bg-emerald-600 text-white text-[10px] font-bold rounded uppercase tracking-wider">
                    Akun {{ ucfirst(auth()->user()->role) }}
                </span>
                <span class="px-2.5 py-1 bg-sky-600 text-white text-[10px] font-bold rounded uppercase tracking-wider">
                    Status: Aktif
                </span>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
            {{-- Navigation Sidebar --}}
            <div class="lg:col-span-3 space-y-4">
                <div class="bg-white rounded border border-gray-200 p-2 shadow-sm">
                    <button @click="activeTab = 'profil'" 
                            :class="activeTab === 'profil' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                            class="w-full px-4 py-2.5 text-xs font-bold rounded transition-all flex items-center gap-3">
                        <span>Informasi Profil</span>
                    </button>
                    <button @click="activeTab = 'keamanan'" 
                            :class="activeTab === 'keamanan' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                            class="w-full px-4 py-2.5 text-xs font-bold rounded transition-all flex items-center gap-3 mt-1">
                        <span>Keamanan & Sandi</span>
                    </button>
                </div>

                <div class="bg-sky-600 rounded p-6 text-white shadow-sm relative overflow-hidden group">
                    <div class="relative z-10">
                        <h4 class="text-xs font-bold uppercase tracking-widest opacity-80 mb-2">Tips Keamanan</h4>
                        <p class="text-[11px] leading-relaxed opacity-90 font-medium">Jangan pernah memberitahukan kata sandi Anda kepada siapapun, termasuk pihak admin sistem.</p>
                    </div>
                    <i class="fa-solid fa-lock absolute -right-4 -bottom-4 text-6xl opacity-10 group-hover:scale-110 transition-transform"></i>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="lg:col-span-9">
                <div class="bg-white rounded border border-gray-200 shadow-sm overflow-hidden min-h-[500px]">
                    
                    {{-- Tab: Profil --}}
                    <div x-show="activeTab === 'profil'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                            <div class="flex items-center gap-5">
                                <div class="w-16 h-16 bg-gray-900 text-white rounded-full flex items-center justify-center text-2xl font-bold shadow-sm border-2 border-white">
                                    {{ strtoupper(substr(auth()->user()->nama, 0, 1)) }}
                                </div>
                                <div>
                                    <h2 class="text-lg font-bold text-gray-900 tracking-tight">{{ auth()->user()->nama }}</h2>
                                    <p class="text-xs text-gray-500 font-semibold">{{ auth()->user()->role === 'admin' ? 'Administrator Sistem' : 'Guru Pengampu' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-5">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Nama Lengkap</label>
                                        <div class="flex items-center gap-3 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded text-sm font-semibold text-gray-700">
                                            <i class="fa-solid fa-user text-gray-400 text-xs"></i>
                                            {{ auth()->user()->nama }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Username / NIP</label>
                                        <div class="flex items-center gap-3 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded text-sm font-semibold text-gray-700">
                                            <i class="fa-solid fa-id-badge text-gray-400 text-xs"></i>
                                            {{ auth()->user()->username }}
                                        </div>
                                    </div>
                                </div>

                                <div class="space-y-5">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Role Akses</label>
                                        <div class="flex items-center gap-3 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded text-sm font-semibold text-gray-700">
                                            <i class="fa-solid fa-key text-gray-400 text-xs"></i>
                                            {{ ucfirst(auth()->user()->role) }}
                                        </div>
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Terakhir Login</label>
                                        <div class="flex items-center gap-3 px-4 py-2.5 bg-gray-50 border border-gray-200 rounded text-sm font-semibold text-gray-700">
                                            <i class="fa-solid fa-clock-rotate-left text-gray-400 text-xs"></i>
                                            {{ now()->format('d M Y, H:i') }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 p-4 bg-amber-50 border border-amber-200 rounded flex items-start gap-3">
                                <div class="w-8 h-8 bg-amber-100 text-amber-600 rounded flex items-center justify-center shrink-0">
                                    <i class="fa-solid fa-circle-exclamation"></i>
                                </div>
                                <div>
                                    <h4 class="text-xs font-bold text-amber-900 mb-0.5 uppercase tracking-wide">Butuh Ubah Data Profil?</h4>
                                    <p class="text-[11px] text-amber-700 leading-relaxed font-medium">Untuk alasan keamanan, perubahan nama dan NIP hanya dapat dilakukan oleh Administrator melalui menu Manajemen Guru.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Tab: Keamanan --}}
                    <div x-show="activeTab === 'keamanan'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-x-4" x-transition:enter-end="opacity-100 translate-x-0">
                        <div class="p-6 border-b border-gray-100 bg-gray-50/50">
                            <h2 class="text-lg font-bold text-gray-900 tracking-tight">Keamanan & Akses</h2>
                            <p class="text-xs text-gray-500 font-semibold mt-1">Kelola kata sandi dan proteksi akun Anda.</p>
                        </div>

                        <div class="p-6">
                            {{-- Success Alert --}}
                            @if(session('status'))
                                <div x-data="{ show: true }" x-show="show" 
                                     class="mb-6 p-4 bg-emerald-600 border border-emerald-700 rounded shadow-sm flex items-start gap-4 text-white animate-gentle-pulse">
                                    <div class="w-8 h-8 bg-emerald-500/50 rounded flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-xs font-bold uppercase tracking-wider mb-0.5">Berhasil</h4>
                                        <p class="text-[11px] font-medium leading-relaxed opacity-95">{{ session('status') }}</p>
                                    </div>
                                    <button @click="show = false" class="text-white/50 hover:text-white transition-colors">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </div>
                            @endif

                            {{-- Unified Error Alert --}}
                            @if($errors->any())
                                <div x-data="{ show: true }" x-show="show"
                                     class="mb-6 p-4 bg-rose-600 border border-rose-700 rounded shadow-sm flex items-start gap-4 text-white animate-gentle-pulse">
                                    <div class="w-8 h-8 bg-rose-500/50 rounded flex items-center justify-center shrink-0">
                                        <i class="fa-solid fa-circle-exclamation"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="text-xs font-bold uppercase tracking-wider mb-0.5">Terjadi Kesalahan</h4>
                                        <ul class="list-disc list-inside text-[11px] font-medium leading-relaxed opacity-95">
                                            @foreach($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                    <button @click="show = false" class="text-white/50 hover:text-white transition-colors">
                                        <i class="fa-solid fa-xmark text-xs"></i>
                                    </button>
                                </div>
                            @endif

                            <div class="grid grid-cols-1 xl:grid-cols-12 gap-8">
                                {{-- Form Section --}}
                                <div class="xl:col-span-7">
                                    <form action="{{ route('password.update') }}" method="POST" class="space-y-5" @submit="isLoading = true">
                                        @csrf
                                        @method('PUT')
                                        
                                        <div class="space-y-2">
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kata Sandi Saat Ini</label>
                                            <div class="relative group">
                                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-gray-900 transition-colors">
                                                    <i class="fa-solid fa-lock-open text-xs"></i>
                                                </div>
                                                <input :type="showOld ? 'text' : 'password'" name="current_password" required
                                                       class="w-full pl-10 pr-12 py-3 text-sm border border-gray-200 rounded focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-all bg-white @error('current_password') border-rose-500 bg-rose-50/30 @enderror"
                                                       placeholder="••••••••">
                                                <button type="button" @click="showOld = !showOld" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 transition-colors">
                                                    <i class="fa-solid" :class="showOld ? 'fa-eye-slash' : 'fa-eye'"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="h-px bg-gray-100 my-2"></div>

                                        <div class="space-y-2">
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Kata Sandi Baru</label>
                                            <div class="relative group">
                                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-gray-900 transition-colors">
                                                    <i class="fa-solid fa-key text-xs"></i>
                                                </div>
                                                <input :type="showNew ? 'text' : 'password'" name="new_password" x-model="newPassword" required
                                                       class="w-full pl-10 pr-12 py-3 text-sm border border-gray-200 rounded focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-all bg-white @error('new_password') border-rose-500 bg-rose-50/30 @enderror"
                                                       placeholder="Minimal 8 karakter">
                                                <button type="button" @click="showNew = !showNew" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 transition-colors">
                                                    <i class="fa-solid" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="space-y-2">
                                            <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest">Konfirmasi Sandi Baru</label>
                                            <div class="relative group">
                                                <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-gray-900 transition-colors">
                                                    <i class="fa-solid fa-check-double text-xs"></i>
                                                </div>
                                                <input :type="showConfirm ? 'text' : 'password'" name="new_password_confirmation" x-model="confirmPassword" required
                                                       class="w-full pl-10 pr-12 py-3 text-sm border border-gray-200 rounded focus:border-gray-900 focus:ring-1 focus:ring-gray-900 outline-none transition-all bg-white"
                                                       placeholder="Ulangi kata sandi baru">
                                                <button type="button" @click="showConfirm = !showConfirm" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 transition-colors">
                                                    <i class="fa-solid" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="pt-4">
                                            <button type="submit" :disabled="isLoading"
                                                    class="w-full md:w-auto px-8 py-3 bg-gray-900 text-white text-sm font-bold rounded hover:bg-gray-800 transition-all flex items-center justify-center gap-3 disabled:opacity-50 disabled:cursor-not-allowed tracking-wide">
                                                <i x-show="!isLoading" class="fa-solid fa-floppy-disk text-xs"></i>
                                                <i x-show="isLoading" class="fa-solid fa-spinner fa-spin text-xs"></i>
                                                <span x-text="isLoading ? 'Menyimpan...' : 'Simpan Perubahan'"></span>
                                            </button>
                                        </div>
                                    </form>
                                </div>

                                {{-- Requirements & Strength Section --}}
                                <div class="xl:col-span-5 space-y-6">
                                    {{-- Strength Card --}}
                                    <div class="p-5 bg-gray-50 border border-gray-200 rounded shadow-sm">
                                        <h4 class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-4">Kekuatan Sandi</h4>
                                        
                                        <div class="space-y-4">
                                            <div class="flex items-end justify-between">
                                                <div class="flex flex-col">
                                                    <span class="text-[10px] font-bold uppercase tracking-tight mb-1" :class="strengthClass" x-text="strengthText"></span>
                                                    <span class="text-lg font-black text-gray-900" x-text="Math.min(strengthScore * 25, 100) + '%'"></span>
                                                </div>
                                                <div class="text-[10px] font-bold text-gray-400 bg-white px-2 py-1 rounded border border-gray-100" x-text="newPassword.length + ' / 8+'"></div>
                                            </div>

                                            <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden flex gap-1 p-0.5">
                                                <template x-for="i in 4">
                                                    <div class="h-full flex-1 rounded-full transition-all duration-500" 
                                                         :class="strengthScore >= i ? strengthBgClass : 'bg-gray-100'"></div>
                                                </template>
                                            </div>
                                        </div>

                                        <div class="mt-6 space-y-3">
                                            <template x-for="(req, index) in requirementList" :key="index">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-5 h-5 rounded flex items-center justify-center transition-all duration-300"
                                                         :class="req.check ? 'bg-emerald-600 text-white shadow-sm' : 'bg-white border border-gray-200 text-gray-300'">
                                                        <i class="fa-solid" :class="req.check ? 'fa-check text-[10px]' : 'fa-circle text-[6px]'"></i>
                                                    </div>
                                                    <span class="text-[11px] font-bold transition-colors duration-300"
                                                          :class="req.check ? 'text-gray-900' : 'text-gray-400'"
                                                          x-text="req.label"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Info Alert - Standardized Style --}}
                                    <div class="p-4 bg-sky-600 border border-sky-700 rounded shadow-md flex items-start gap-4 text-white">
                                        <div class="w-8 h-8 bg-sky-500/50 rounded flex items-center justify-center shrink-0">
                                            <i class="fa-solid fa-circle-info"></i>
                                        </div>
                                        <div>
                                            <h4 class="text-xs font-bold uppercase tracking-wider mb-0.5">Panduan Keamanan</h4>
                                            <p class="text-[11px] font-medium leading-relaxed opacity-95">Kata sandi yang kuat setidaknya mengandung kombinasi huruf besar, angka, dan simbol untuk mencegah akses tidak sah.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
function accountSettings() {
    return {
        activeTab: localStorage.getItem('settingTab') || 'profil',
        showOld: false,
        showNew: false,
        showConfirm: false,
        newPassword: '',
        confirmPassword: '',
        isLoading: false,

        init() {
            this.$watch('activeTab', val => localStorage.setItem('settingTab', val));
        },

        get requirementList() {
            return [
                { label: 'Minimal 8 Karakter', check: this.newPassword.length >= 8 },
                { label: 'Huruf Besar (A-Z)', check: /[A-Z]/.test(this.newPassword) },
                { label: 'Angka (0-9)', check: /[0-9]/.test(this.newPassword) },
                { label: 'Simbol Khusus (@$!%*?)', check: /[^A-Za-z0-9]/.test(this.newPassword) },
            ];
        },

        get strengthScore() {
            let score = 0;
            if (this.newPassword.length >= 8) score++;
            if (/[A-Z]/.test(this.newPassword)) score++;
            if (/[0-9]/.test(this.newPassword)) score++;
            if (/[^A-Za-z0-9]/.test(this.newPassword)) score++;
            return score;
        },

        get strengthText() {
            const texts = ['Sangat Lemah', 'Lemah', 'Sedang', 'Kuat', 'Sangat Kuat'];
            return texts[this.strengthScore] || 'Sangat Lemah';
        },

        get strengthClass() {
            const classes = ['text-rose-500', 'text-rose-400', 'text-amber-500', 'text-emerald-500', 'text-emerald-600'];
            return classes[this.strengthScore] || 'text-rose-500';
        },

        get strengthBgClass() {
            const classes = ['bg-rose-500', 'bg-rose-500', 'bg-amber-500', 'bg-emerald-500', 'bg-emerald-600'];
            return classes[this.strengthScore] || 'bg-rose-500';
        }
    }
}
</script>
@endpush
