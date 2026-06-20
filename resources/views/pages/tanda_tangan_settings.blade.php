@extends('layouts.app')

@section('title', 'Pengaturan Tanda Tangan')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h2 class="text-xl font-bold text-gray-900 mb-6">Pengaturan Tanda Tangan Kepala Sekolah</h2>
        
        <form action="{{ route('admin.signatures.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Nama Kepala Sekolah</label>
                <input type="text" name="kepala_sekolah_nama" value="{{ $settings['kepala_sekolah_nama'] ?? '' }}" class="w-full px-3 py-2.5 text-sm border border-gray-300 rounded focus:border-gray-900 outline-none transition-colors bg-gray-50" placeholder="Masukkan nama lengkap kepala sekolah">
            </div>

            @if(isset($settings['kepala_sekolah_ttd_path']))
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-2">Tanda tangan saat ini:</p>
                    <img src="{{ asset('storage/' . $settings['kepala_sekolah_ttd_path']) }}" alt="Tanda Tangan KS" class="w-48 h-auto border rounded">
                </div>
            @endif

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Unggah Tanda Tangan Baru</label>
                <input type="file" name="kepala_sekolah_ttd" class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded file:border-0 file:text-sm file:font-semibold file:bg-gray-900 file:text-white hover:file:bg-gray-800" accept="image/*">
            </div>

            <button type="submit" class="bg-gray-900 text-white px-4 py-2 rounded font-semibold text-sm hover:bg-gray-800">Simpan Perubahan</button>
        </form>
    </div>
</div>
@endsection
