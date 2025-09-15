<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            {{ __('Tambah User Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            {{-- Menggunakan gaya kartu yang konsisten --}}
            <div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 sm:p-8 border border-neutral-200 dark:border-neutral-700">
                
                <header>
                    <h3 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">
                        Informasi User
                    </h3>
                    <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-400">
                        Buat akun baru dan tentukan peran, unit kerja, serta jabatannya.
                    </p>
                </header>
                
                <form method="POST" action="{{ route('admin.users.store') }}" class="mt-6">
                    @csrf

                    {{-- Menggunakan Grid untuk layout 2 kolom di layar medium ke atas --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Kolom Kiri --}}
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="name" :value="__('Nama')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                                <x-input-error :messages="$errors->get('email')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password" :value="__('Password')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>
                        </div>

                        {{-- Kolom Kanan --}}
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="role" :value="__('Role')" />
                                <x-select-input name="role" id="role" class="block mt-1 w-full">
                                    <option value="admin">Admin</option>
                                    <option value="atasan">Atasan</option>
                                    <option value="pegawai" selected>Pegawai</option>
                                </x-select-input>
                            </div>

                            <div>
                                <x-input-label for="unit_kerja_id" :value="__('Unit Kerja')" />
                                <x-select-input name="unit_kerja_id" id="unit_kerja_id" class="block mt-1 w-full">
                                    @foreach ($unitKerjas as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->nama_unit }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>

                            <div>
                                <x-input-label for="jabatan_id" :value="__('Jabatan')" />
                                <x-select-input name="jabatan_id" id="jabatan_id" class="block mt-1 w-full">
                                    @foreach ($jabatans as $jabatan)
                                        <option value="{{ $jabatan->id }}">{{ $jabatan->nama_jabatan }} - {{ $jabatan->bidang_kerja }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>

                            <div>
                                <x-input-label for="atasan_id" :value="__('Pilih Atasan (opsional)')" />
                                <x-select-input name="atasan_id" id="atasan_id" class="block mt-1 w-full">
                                    <option value="">Tidak ada</option>
                                    @foreach ($superiors as $superior)
                                        <option value="{{ $superior->id }}">{{ $superior->name }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-4 mt-8">
                        <a href="{{ route('admin.users.index') }}">
                            <x-secondary-button type="button">
                                {{ __('Batal') }}
                            </x-secondary-button>
                        </a>
                        
                        <x-primary-button>
                            {{ __('Simpan User') }}
                        </x-primary-button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</x-app-layout>