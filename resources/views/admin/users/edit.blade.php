<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            Edit User: {{ $user->name }}
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
                        Perbarui informasi akun, peran, dan detail pekerjaan user.
                    </p>
                </header>

                <form method="POST" action="{{ route('admin.users.update', $user->id) }}" class="mt-6">
                    @csrf
                    @method('PATCH')

                    {{-- Layout Grid 2 Kolom --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        
                        {{-- Kolom Kiri --}}
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="name" :value="__('Nama')" />
                                <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                            </div>
                            <div>
                                <x-input-label for="email" :value="__('Email')" />
                                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                            </div>
                            <div>
                                <x-input-label for="password" :value="__('Password Baru (Kosongkan jika tidak diubah)')" />
                                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                            </div>
                        </div>

                        {{-- Kolom Kanan --}}
                        <div class="space-y-6">
                            <div>
                                <x-input-label for="role" :value="__('Role')" />
                                <x-select-input name="role" id="role" class="block mt-1 w-full">
                                    <option value="sys_admin" @selected(old('role', $user->role) == 'sys_admin')>Admin Sistem</option>
                                    <option value="unit_admin" @selected(old('role', $user->role) == 'unit_admin')>Admin Unit</option>
                                    <option value="user" @selected(old('role', $user->role) == 'user')>Pegawai</option>
                                </x-select-input>
                            </div>
                            <div>
                                <x-input-label for="unit_kerja_id" :value="__('Unit Kerja')" />
                                <x-select-input name="unit_kerja_id" id="unit_kerja_id" class="block mt-1 w-full">
                                    @foreach ($unitKerjas as $unit)
                                        <option value="{{ $unit->id }}" @selected(old('unit_kerja_id', $user->unit_kerja_id) == $unit->id)>{{ $unit->nama_unit }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            <div>
                                <x-input-label for="jabatan_id" :value="__('Jabatan')" />
                                <x-select-input name="jabatan_id" id="jabatan_id" class="block mt-1 w-full">
                                    @foreach ($jabatans as $jabatan)
                                        <option value="{{ $jabatan->id }}" @selected(old('jabatan_id', $user->jabatan_id) == $jabatan->id)>{{ $jabatan->nama_jabatan }} - {{ $jabatan->bidang_kerja }}</option>
                                    @endforeach
                                </x-select-input>
                            </div>
                            <div>
                                <x-input-label for="atasan_id" :value="__('Pilih Atasan (opsional)')" />
                                <x-select-input name="atasan_id" id="atasan_id" class="block mt-1 w-full">
                                    <option value="">Tidak ada</option>
                                    @foreach ($superiors as $superior)
                                        <option value="{{ $superior->id }}" @selected(old('atasan_id', $user->atasan_id) == $superior->id)>{{ $superior->name }}</option>
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
                            {{ __('Update User') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>