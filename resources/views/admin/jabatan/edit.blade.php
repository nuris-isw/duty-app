<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            Edit Jabatan: {{ $jabatan->nama_jabatan }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            {{-- Menggunakan gaya kartu yang konsisten --}}
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 sm:p-8 border border-neutral-200 dark:border-neutral-700">
                
                <header>
                    <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">
                        Informasi Jabatan
                    </h3>
                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                        Perbarui detail untuk jabatan ini.
                    </p>
                </header>

                <form method="POST" action="{{ route('admin.jabatan.update', $jabatan) }}" class="mt-6 space-y-6">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="nama_jabatan" :value="__('Nama Jabatan')" />
                        <x-text-input id="nama_jabatan" class="block mt-1 w-full" type="text" name="nama_jabatan" :value="old('nama_jabatan', $jabatan->nama_jabatan)" required autofocus />
                        <x-input-error :messages="$errors->get('nama_jabatan')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="bidang_kerja" :value="__('Bidang Kerja')" />
                        <x-text-input id="bidang_kerja" class="block mt-1 w-full" type="text" name="bidang_kerja" :value="old('bidang_kerja', $jabatan->bidang_kerja)" />
                        <x-input-error :messages="$errors->get('bidang_kerja')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="alias" :value="__('Alias (Opsional)')" />
                        <x-text-input id="alias" class="block mt-1 w-full" type="text" name="alias" :value="old('alias', $jabatan->alias)" />
                        <x-input-error :messages="$errors->get('alias')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end gap-4 pt-2">
                        <a href="{{ route('admin.jabatan.index') }}">
                            <x-secondary-button type="button">
                                {{ __('Batal') }}
                            </x-secondary-button>
                        </a>
                        
                        <x-primary-button>
                            {{ __('Update') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>