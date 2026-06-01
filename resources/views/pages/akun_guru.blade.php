@extends('layouts.app')

@section('title', 'Kelola Akun Guru')

@section('content')
    <div class="max-w-full" x-data="{ 
        konfirmasiMembuat(id, nama) {
            Swal.fire({
                title: 'Buat Akun Guru?',
                text: 'Akun akan dibuat dengan username: ' + id + ' dan password default: ' + id + '\n\nAtas nama: ' + nama,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#111827',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, Buat Akun!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '/akun_guru';
                    
                    const csrfToken = document.querySelector('meta[name=csrf-token]').getAttribute('content');
                    
                    const csrfInput = document.createElement('input');
                    csrfInput.type = 'hidden';
                    csrfInput.name = '_token';
                    csrfInput.value = csrfToken;
                    
                    const nipInput = document.createElement('input');
                    nipInput.type = 'hidden';
                    nipInput.name = 'nip';
                    nipInput.value = id;
                    
                    form.appendChild(csrfInput);
                    form.appendChild(nipInput);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    }">
        <div class="bg-white rounded border border-gray-200 overflow-hidden">
            @if ($message = Session::get('success'))
                <div class="m-4 p-4 bg-green-50 border border-green-200 rounded text-green-800 text-sm">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-check mt-0.5"></i>
                        <div>
                            <p class="font-semibold">Berhasil!</p>
                            <p class="text-sm mt-1">{{ $message }}</p>
                        </div>
                    </div>
                </div>
            @endif

            @if ($message = Session::get('error'))
                <div class="m-4 p-4 bg-red-50 border border-red-200 rounded text-red-800 text-sm">
                    <div class="flex items-start gap-3">
                        <i class="fa-solid fa-circle-xmark mt-0.5"></i>
                        <div>
                            <p class="font-semibold">Gagal!</p>
                            <p class="text-sm mt-1">{{ $message }}</p>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Search Section --}}
            <x-search-toolbar 
                placeholder="Cari guru berdasarkan NIP atau Nama..." 
                :resetUrl="route('akun_guru')"
                :showTambah="false"
            />

            {{-- Table Section --}}
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-900 border-b border-gray-800">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">No</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">NIP/Kode</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Nama Guru</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-white tracking-wider">Email</th>
                            <th class="px-6 py-4 text-center text-xs font-bold text-white tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($guruData as $i => $g)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $guruData->firstItem() + $i }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">{{ $g->nip }}</td>
                            <td class="px-6 py-4 text-sm text-gray-700 font-semibold">{{ $g->nama_guru }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $g->email ?? '-' }}</td>
                            <td class="px-6 py-4 text-center">
                                @if(!$g->username)
                                    <button 
                                        type="button" 
                                        @click="konfirmasiMembuat('{{ $g->nip }}', '{{ $g->nama_guru }}')"
                                        class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded hover:bg-blue-700 transition-colors"
                                    >
                                        <i class="fa-solid fa-user-plus"></i>
                                        <span>Buat Akun</span>
                                    </button>
                                @else
                                    <span class="text-xs text-gray-500">Akun aktif</span>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <div class="text-gray-500">
                                    <i class="fa-solid fa-circle-info text-2xl mb-3 block"></i>
                                    <p class="text-sm font-medium">Tidak ada data guru</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="p-6 bg-gray-50/30 border-t border-gray-100">
                <x-pagination :paginator="$guruData" />
            </div>
        </div>
    </div>
@endsection
