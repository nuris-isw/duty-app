<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            {{ __('Tambah Jenis Cuti Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 sm:p-8 border border-neutral-200 dark:border-neutral-700">
                
                <header>
                    <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">
                        Detail Jenis Cuti
                    </h3>
                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                        Atur properti untuk jenis cuti baru yang akan berlaku bagi pegawai.
                    </p>
                </header>
                
                <form method="POST" action="{{ route('admin.leave-types.store') }}" class="mt-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Nama Cuti --}}
                        <div class="md:col-span-2">
                            <x-input-label for="nama_cuti" :value="__('Nama Jenis Cuti')" />
                            <x-text-input id="nama_cuti" class="block mt-1 w-full" type="text" name="nama_cuti" :value="old('nama_cuti')" required autofocus />
                        </div>

                        {{-- Kuota --}}
                        <div>
                            <x-input-label for="kuota" :value="__('Kuota')" />
                            <x-text-input id="kuota" class="block mt-1 w-full" type="number" name="kuota" value="{{ old('kuota', 0) }}" required />
                        </div>

                        {{-- Satuan --}}
                        <div>
                            <x-input-label for="satuan" :value="__('Satuan Kuota')" />
                            <x-select-input id="satuan" name="satuan" class="block mt-1 w-full">
                                <option value="hari" @selected(old('satuan') == 'hari')>Hari</option>
                                <option value="kali" @selected(old('satuan') == 'kali')>Kali</option>
                            </x-select-input>
                        </div>

                        {{-- Periode Reset --}}
                        <div class="md:col-span-2">
                            <x-input-label for="periode_reset" :value="__('Periode Reset Kuota')" />
                            <x-select-input id="periode_reset" name="periode_reset" class="block mt-1 w-full">
                                <option value="tahunan" @selected(old('periode_reset') == 'tahunan')>Tahunan</option>
                                <option value="tidak_ada" @selected(old('periode_reset') == 'tidak_ada')>Tidak Ada</option>
                            </x-select-input>
                        </div>

                        {{-- Checkbox Aturan --}}
                        <div class="md:col-span-2 space-y-4">
                            <label for="memerlukan_dokumen" class="flex items-center">
                                <input type="checkbox" id="memerlukan_dokumen" name="memerlukan_dokumen" value="1" @checked(old('memerlukan_dokumen')) class="rounded border-neutral-300 dark:border-neutral-600 text-brand dark:bg-neutral-700 shadow-sm focus:ring-brand">
                                <span class="ms-2 text-sm text-neutral-600 dark:text-neutral-300">Wajib melampirkan dokumen pendukung</span>
                            </label>

                            <label for="bisa_retroaktif" class="flex items-center">
                                <input type="checkbox" id="bisa_retroaktif" name="bisa_retroaktif" value="1" @checked(old('bisa_retroaktif')) class="rounded border-neutral-300 dark:border-neutral-600 text-brand dark:bg-neutral-700 shadow-sm focus:ring-brand">
                                <span class="ms-2 text-sm text-neutral-600 dark:text-neutral-300">Bisa diajukan untuk tanggal yang sudah lewat</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <a href="{{ route('admin.leave-types.index') }}">
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