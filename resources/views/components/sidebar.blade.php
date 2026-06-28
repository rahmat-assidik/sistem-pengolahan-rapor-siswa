{{-- resources/views/components/sidebar.blade.php --}}

<div x-data="{ sidebarOpen: false }">
    {{-- Mobile Hamburger Button --}}
    <button @click="sidebarOpen = true"
            class="fixed top-4 left-4 z-40 lg:hidden w-10 h-10 bg-white border border-gray-200 rounded shadow-sm flex items-center justify-center text-gray-700 hover:bg-gray-50 transition-colors"
            x-show="!sidebarOpen"
            x-transition>
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
    </button>

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         class="fixed inset-0 z-40 bg-gray-900/40 lg:hidden"
         style="display: none;"></div>

    {{-- Sidebar --}}
    <aside id="sidebar"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed top-0 left-0 z-50 flex flex-col w-52 h-screen bg-white border-r border-gray-200 transition-transform duration-300 ease-in-out lg:translate-x-0">

        {{-- Logo --}}
        <div class="flex-shrink-0 px-5 py-5 border-b border-gray-100">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 bg-gray-900 rounded flex items-center justify-center text-white">
                        <i class="fa-solid fa-graduation-cap text-xs"></i>
                    </div>
                    <div>
                        <h1 class="text-sm font-black tracking-tighter text-gray-900 leading-none">Smart<span class="text-blue-600">Rapor</span></h1>
                        <p class="text-[9px] font-medium text-gray-400 tracking-tight mt-0.5">Management</p>
                    </div>
                </div>
                {{-- Close button on mobile --}}
                <button @click="sidebarOpen = false" class="lg:hidden w-8 h-8 flex items-center justify-center text-gray-400 hover:text-gray-900 transition-colors">
                    <i class="fa-solid fa-xmark text-sm"></i>
                </button>
            </div>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-4 overflow-y-auto scrollbar-thin">

            {{-- Dashboard --}}
            <div class="px-2 mb-2">
                <a href="{{ route('dashboard') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('dashboard')
                             ? 'bg-gray-900 text-white font-bold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M3 4a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 12a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1H4a1 1 0 01-1-1v-4zM11 4a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1V4zM11 12a1 1 0 011-1h4a1 1 0 011 1v4a1 1 0 01-1 1h-4a1 1 0 01-1-1v-4z"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </div>


            {{-- Master Data Section --}}
            @if(auth()->check() && auth()->user()->isAdmin())
            <div class="px-2 mb-4">
                <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Master Data</div>
                
                <a href="{{ route('data_siswa') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('data_siswa')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                    </svg>
                    <span>Siswa</span>
                </a>
                
                <a href="{{ route('data_guru') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('data_guru')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                    </svg>
                    <span>Guru</span>
                </a>
                

                
                <a href="{{ route('data_kelas') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('data_kelas')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm4 2v4h4V8H6zm6 0v4h4V8h-4z"/>
                    </svg>
                    <span>Kelas</span>
                </a>
                
                <a href="{{ route('pembagian_kelas') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('pembagian_kelas*')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v4h8v-4zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"/>
                    </svg>
                    <span>Pembagian Kelas</span>
                </a>
                
                <a href="{{ route('data_mapel') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('data_mapel')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10.99A7.965 7.965 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.965 7.965 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10.99A7.968 7.968 0 0014.5 4c-1.669 0-3.218-.51-4.5-1.385A7.968 7.968 0 009 4.804z"/>
                    </svg>
                    <span>Mata Pelajaran</span>
                </a>
                
                <a href="{{ route('akademik') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('akademik')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/>
                    </svg>
                    <span>Tahun Ajaran</span>
                </a>
            </div>
            @endif


            {{-- Akademik Section --}}
            <div class="px-2 mb-4">
                <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Akademik</div>
                
                @if(auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('pengampu') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('pengampu')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M10.5 1.5H5.75A2.25 2.25 0 003.5 3.75v12.5A2.25 2.25 0 005.75 18.5h8.5a2.25 2.25 0 002.25-2.25V6.5m-12 0h12m-12 0V3.75a.75.75 0 01.75-.75h4.5"/>
                    </svg>
                    <span>Pengampu</span>
                </a>
                @endif


                
                @if(auth()->check() && auth()->user()->isGuru())
                <a href="{{ route('input_nilai') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('input_nilai')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Input Nilai</span>
                </a>
                
                <a href="{{ route('guru.bobot') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('bobot_nilai_guru')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                    <span>Konfigurasi Bobot</span>
                </a>
                @endif
                
                @if(auth()->check() && (auth()->user()->isAdmin() || auth()->user()->isWaliKelas()))
                <a href="{{ route('data_rapor') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('data_rapor')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm2 6a1 1 0 011-1h6a1 1 0 110 2H7a1 1 0 01-1-1zm1 3a1 1 0 100 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
                    </svg>
                    <span>Rapor Siswa</span>
                </a>
                @endif

                @if(auth()->check() && auth()->user()->isWaliKelas())
                <a href="{{ route('status_rapor') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('status_rapor')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <span>Status Rapor</span>
                </a>
                @endif
            </div>

    {{-- Arsip Section --}}
    @if(auth()->check() && auth()->user()->isAdmin())
    <div class="px-2 mb-4">
        <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Arsip</div>
        
        <a href="{{ route('arsip_rapor') }}"
           @click="sidebarOpen = false"
           class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                  {{ request()->is('arsip_rapor')
                     ? 'bg-gray-900 text-white font-semibold'
                     : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
            <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                <path d="M10.5 1.5H5.75A2.25 2.25 0 003.5 3.75v12.5A2.25 2.25 0 005.75 18.5h8.5a2.25 2.25 0 002.25-2.25V6.5m-12 0h12m-12 0V3.75a.75.75 0 01.75-.75h4.5"/>
            </svg>
            <span>Arsip Rapor</span>
        </a>
    </div>
    @endif

            {{-- Pengaturan Section --}}
            <div class="px-2 mb-4">
                <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Pengaturan</div>
                
                @if(auth()->check() && auth()->user()->isAdmin())
                <a href="{{ route('settings.bobot') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('bobot_nilai')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M2 11a1 1 0 011-1h2a1 1 0 011 1v5a1 1 0 01-1 1H3a1 1 0 01-1-1v-5zM8 7a1 1 0 011-1h2a1 1 0 011 1v9a1 1 0 01-1 1H9a1 1 0 01-1-1V7zM14 4a1 1 0 011-1h2a1 1 0 011 1v12a1 1 0 01-1 1h-2a1 1 0 01-1-1V4z"/>
                    </svg>
                    <span>Bobot Nilai</span>
                </a>
                <a href="{{ route('admin.signatures.index') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('settings/signature')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <i class="fa-solid fa-signature w-4 h-4 flex-shrink-0"></i>
                    <span>Tanda Tangan KS</span>
                </a>
                @endif

                @if(auth()->check() && auth()->user()->isWaliKelas())
                <a href="{{ route('guru.signature.show') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('guru/signature')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <i class="fa-solid fa-signature w-4 h-4 flex-shrink-0"></i>
                    <span>Tanda Tangan Wali Kelas</span>
                </a>
                @endif

                <a href="{{ route('ubah_kata_sandi') }}"
                   @click="sidebarOpen = false"
                   class="relative flex items-center gap-3 px-3 py-2 rounded text-xs font-medium transition-all duration-150
                          {{ request()->is('ubah_kata_sandi')
                             ? 'bg-gray-900 text-white font-semibold'
                             : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900' }}">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M11.49 3.17c-.38-1.56-2.6-1.56-2.98 0a1.532 1.532 0 01-2.286.948c-1.372-.836-2.942.734-2.106 2.106.54.886.061 2.042-.947 2.287-1.561.379-1.561 2.6 0 2.978a1.532 1.532 0 01.947 2.287c-.836 1.372.734 2.942 2.106 2.106a1.532 1.532 0 012.287.947c.379 1.561 2.6 1.561 2.978 0a1.533 1.533 0 012.287-.947c1.372.836 2.942-.734 2.106-2.106a1.533 1.533 0 01.947-2.287c1.561-.379 1.561-2.6 0-2.978a1.532 1.532 0 01-.947-2.287c.836-1.372-.734-2.942-2.106-2.106a1.532 1.532 0 01-2.287-.947zM10 13a3 3 0 100-6 3 3 0 000 6z" clip-rule="evenodd" />
                    </svg>
                    <span>Pengaturan Akun</span>
                </a>
            </div>


        </nav>

        {{-- Logout --}}
        <div class="flex-shrink-0 p-3 border-t border-gray-200">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="w-full flex items-center justify-center gap-2 px-4 py-2.5 text-xs font-semibold text-white bg-red-600 rounded
                               hover:bg-red-700 active:bg-red-800 transition-all duration-150 cursor-pointer shadow-sm hover:shadow-md">
                    <svg class="w-4 h-4 flex-shrink-0" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M3 3a1 1 0 00-1 1v12a1 1 0 102 0V4a1 1 0 00-1-1zm10.293 9.293a1 1 0 001.414 1.414l3-3a1 1 0 000-1.414l-3-3a1 1 0 10-1.414 1.414L14.586 9H7a1 1 0 100 2h7.586l-1.293 1.293z" clip-rule="evenodd"/>
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>

    </aside>