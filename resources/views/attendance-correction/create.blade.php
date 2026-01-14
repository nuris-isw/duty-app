<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-800 dark:text-neutral-200 leading-tight">
            {{ __('Form Pengajuan Koreksi Absensi') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 shadow-lg rounded-2xl border border-neutral-200 dark:border-neutral-700 p-8">
                
                {{-- Validasi Error --}}
                @if ($errors->any())
                    <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-500 text-red-700 rounded-lg">
                        <div class="font-bold mb-1">Terjadi Kesalahan:</div>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('attendance-correction.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="space-y-6">
                        
                        {{-- 1. Pilih Tanggal --}}
                        <div>
                            <x-input-label for="date" :value="__('Tanggal Absensi yang Dikoreksi')" />
                            
                            {{-- MODIFIKASI: Menangkap request('date') agar otomatis terisi dari link riwayat --}}
                            <x-text-input id="date" class="block mt-1 w-full" type="date" name="date" 
                                :value="old('date', request('date'))" 
                                required max="{{ date('Y-m-d') }}" />
                                
                            <p class="mt-1.5 text-xs text-neutral-500 dark:text-neutral-400">
                                * Pilih tanggal di masa lalu yang datanya ingin diperbaiki.
                            </p>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- 2. Jam Masuk --}}
                            <div>
                                <x-input-label for="proposed_start_time" :value="__('Koreksi Jam Masuk (Opsional)')" />
                                <x-text-input id="proposed_start_time" class="block mt-1 w-full" type="time" name="proposed_start_time" :value="old('proposed_start_time')" />
                                <p class="mt-1 text-xs text-neutral-400">Biarkan kosong jika tidak ingin mengubah jam masuk.</p>
                            </div>

                            {{-- 3. Jam Pulang --}}
                            <div>
                                <x-input-label for="proposed_end_time" :value="__('Koreksi Jam Pulang (Opsional)')" />
                                <x-text-input id="proposed_end_time" class="block mt-1 w-full" type="time" name="proposed_end_time" :value="old('proposed_end_time')" />
                                <p class="mt-1 text-xs text-neutral-400">Biarkan kosong jika tidak ingin mengubah jam pulang.</p>
                            </div>
                        </div>

                        {{-- 4. Alasan --}}
                        <div>
                            <x-input-label for="reason" :value="__('Alasan Koreksi')" />
                            <x-textarea-input id="reason" class="block mt-1 w-full" name="reason" rows="3" required placeholder="Contoh: Lupa scan pulang karena buru-buru, Mesin fingerprint mati listrik, dll.">{{ old('reason') }}</x-textarea-input>
                        </div>

                        {{-- 5. Dokumen Bukti --}}
                        <div>
                            <x-input-label for="dokumen_pendukung" :value="__('Bukti Pendukung (Opsional)')" />
                            <div class="mt-1 flex items-center justify-center w-full">
                                <label for="dokumen_pendukung" class="flex flex-col items-center justify-center w-full h-32 border-2 border-neutral-300 border-dashed rounded-lg cursor-pointer bg-neutral-50 hover:bg-neutral-100 dark:bg-neutral-800 dark:border-neutral-600 dark:hover:border-neutral-500 dark:hover:bg-neutral-700 transition duration-150">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        {{-- Icon Upload --}}
                                        <svg class="w-8 h-8 mb-4 text-neutral-500 dark:text-neutral-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-neutral-500 dark:text-neutral-400"><span class="font-semibold">Klik untuk upload</span> atau drag & drop</p>
                                        <p class="text-xs text-neutral-500 dark:text-neutral-400">JPG, PNG, atau PDF (Max. 2MB)</p>
                                    </div>
                                    <input id="dokumen_pendukung" type="file" name="dokumen_pendukung" class="hidden" accept=".jpg,.jpeg,.png,.pdf" />
                                </label>
                            </div>
                            {{-- Script Sederhana untuk Menampilkan Nama File (Opsional tapi UX Friendly) --}}
                            <div id="file-name" class="mt-2 text-sm text-indigo-600 font-medium"></div>
                        </div>

                    </div>

                    <div class="flex items-center justify-end mt-8 gap-4 pt-6 border-t border-neutral-100 dark:border-neutral-700">
                        <a href="{{ route('attendance-correction.index') }}">
                            <x-secondary-button type="button">{{ __('Batal') }}</x-secondary-button>
                        </a>
                        <x-primary-button class="bg-indigo-600 hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900">
                            {{ __('Kirim Pengajuan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Script untuk menampilkan nama file yang diupload --}}
    <script>
        document.getElementById('dokumen_pendukung').addEventListener('change', function(e) {
            const fileName = e.target.files[0]?.name;
            const display = document.getElementById('file-name');
            if (fileName) {
                display.textContent = 'File terpilih: ' + fileName;
            } else {
                display.textContent = '';
            }
        });
    </script>
</x-app-layout>