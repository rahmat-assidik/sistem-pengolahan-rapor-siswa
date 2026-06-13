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
                        <i class="fa-solid fa-address-card text-sm"></i>
                        <span>Informasi Profil</span>
                    </button>
                    <button @click="activeTab = 'keamanan'" 
                            :class="activeTab === 'keamanan' ? 'bg-gray-900 text-white shadow-sm' : 'text-gray-600 hover:bg-gray-50'"
                            class="w-full px-4 py-2.5 text-xs font-bold rounded transition-all flex items-center gap-3 mt-1">
                        <i class="fa-solid fa-shield-halved text-sm"></i>
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
                            <h2 class="text-lg font-bold text-gray-900 tracking-tight">Pembaruan Kata Sandi</h2>
                            <p class="text-xs text-gray-500 font-semibold mt-1">Kami menyarankan Anda untuk mengganti kata sandi secara berkala.</p>
                        </div>

                        <div class="p-6">
                            @if(session('status'))
                                <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 text-xs font-bold rounded flex items-center gap-3">
                                    <i class="fa-solid fa-circle-check text-lg"></i>
                                    {{ session('status') }}
                                </div>
                            @endif

                            <form action="{{ route('password.update') }}" method="POST" class="space-y-6">
                                @csrf
                                @method('PUT')
                                
                                <div>
                                    <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Kata Sandi Saat Ini</label>
                                    <div class="relative">
                                        <input :type="showOld ? 'text' : 'password'" name="current_password" required
                                               class="w-full pl-4 pr-12 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-all bg-gray-50 @error('current_password') border-red-500 @enderror">
                                        <button type="button" @click="showOld = !showOld" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 transition-colors">
                                            <i class="fa-solid" :class="showOld ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                    @error('current_password')
                                        <p class="text-[10px] text-red-600 mt-1.5 font-bold uppercase tracking-tight">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Kata Sandi Baru</label>
                                        <div class="relative">
                                            <input :type="showNew ? 'text' : 'password'" name="new_password" x-model="newPassword" required
                                                   class="w-full pl-4 pr-12 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-all bg-gray-50 @error('new_password') border-red-500 @enderror">
                                            <button type="button" @click="showNew = !showNew" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 transition-colors">
                                                <i class="fa-solid" :class="showNew ? 'fa-eye-slash' : 'fa-eye'"></i>
                                            </button>
                                        </div>
                                        
                                        {{-- Strength Meter --}}
                                        <div class="mt-3" x-show="newPassword.length > 0">
                                            <div class="flex items-center justify-between mb-1.5">
                                                <span class="text-[10px] font-bold uppercase tracking-tight" :class="strengthClass" x-text="'Keamanan: ' + strengthText"></span>
                                                <span class="text-[10px] font-bold text-gray-400" x-text="newPassword.length + '/8 Karakter'"></span>
                                            </div>
                                            <div class="h-1.5 w-full bg-gray-100 rounded overflow-hidden flex gap-0.5">
                                                <div class="h-full transition-all duration-500" :style="'width: ' + (strengthScore >= 1 ? '25%' : '0%')" :class="strengthScore >= 1 ? (strengthScore === 1 ? 'bg-rose-500' : (strengthScore === 2 ? 'bg-amber-500' : 'bg-emerald-500')) : 'bg-transparent'"></div>
                                                <div class="h-full transition-all duration-500" :style="'width: ' + (strengthScore >= 2 ? '25%' : '0%')" :class="strengthScore >= 2 ? (strengthScore === 2 ? 'bg-amber-500' : 'bg-emerald-500') : 'bg-transparent'"></div>
                                                <div class="h-full transition-all duration-500" :style="'width: ' + (strengthScore >= 3 ? '25%' : '0%')" :class="strengthScore >= 3 ? (strengthScore === 3 ? 'bg-emerald-500' : 'bg-emerald-500') : 'bg-transparent'"></div>
                                                <div class="h-full transition-all duration-500" :style="'width: ' + (strengthScore >= 4 ? '25%' : '0%')" :class="strengthScore >= 4 ? 'bg-emerald-500' : 'bg-transparent'"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1.5">Konfirmasi Sandi Baru</label>
                                        <div class="relative">
                                            <input :type="showConfirm ? 'text' : 'password'" name="new_password_confirmation" x-model="confirmPassword" required
                                                   class="w-full pl-4 pr-12 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-all bg-gray-50">
                                            <button type="button" @click="showConfirm = !showConfirm" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-900 transition-colors">
                                                <i class="fa-solid" :class="showConfirm ? 'fa-eye-slash' : 'fa-eye'"></i>
                                            </button>
                                        </div>
                                        <p x-show="confirmPassword && newPassword !== confirmPassword" class="text-[10px] text-rose-600 mt-1.5 font-bold uppercase tracking-tight">Kata sandi tidak cocok</p>
                                    </div>
                                </div>

                                <div class="pt-6 border-t border-gray-100 flex items-center justify-end">
                                    <button type="submit" :disabled="newPassword.length < 8 || newPassword !== confirmPassword"
                                            class="px-6 py-2.5 bg-gray-900 text-white text-xs font-bold rounded hover:bg-gray-800 transition-all shadow-sm flex items-center gap-2 disabled:opacity-50 disabled:cursor-not-allowed uppercase tracking-wider">
                                        <i class="fa-solid fa-check"></i>
                                        <span>Perbarui Kata Sandi</span>
                                    </button>
                                </div>
                            </form>
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

        init() {
            this.$watch('activeTab', val => localStorage.setItem('settingTab', val));
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
        }
    }
}
</script>
@endpush

