<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            {{ __('Tambah Unit Kerja Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            {{-- Menggunakan gaya kartu yang konsisten --}}
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 sm:p-8 border border-neutral-200 dark:border-neutral-700">
                
                <header>
                    <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">
                        Informasi Unit Kerja
                    </h3>
                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                        Masukkan nama unit kerja baru dan lokasi (jika ada).
                    </p>
                </header>
                
                <form method="POST" action="{{ route('admin.unit-kerja.store') }}" class="mt-6 space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="nama_unit" :value="__('Nama Unit')" />
                        <x-text-input id="nama_unit" class="block mt-1 w-full" type="text" name="nama_unit" :value="old('nama_unit')" required autofocus />
                        <x-input-error :messages="$errors->get('nama_unit')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="lokasi" :value="__('Lokasi (Opsional)')" />
                        <x-text-input id="lokasi" class="block mt-1 w-full" type="text" name="lokasi" :value="old('lokasi')" />
                        <x-input-error :messages="$errors->get('lokasi')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-4">
                        {{-- Tombol Batal untuk UX yang lebih baik --}}
                        <a href="{{ route('admin.unit-kerja.index') }}">
                            <x-secondary-button type="button">
                                {{ __('Batal') }}
                            </x-secondary-button>
                        </a>
                        
                        <x-primary-button>
                            {{ __('Simpan') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>