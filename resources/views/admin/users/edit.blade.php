<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-dark leading-tight">
            Edit User: {{ $user->name }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm-px-6 lg-px-8">
            <div class="bg-white shadow-md rounded-2xl p-6">
                <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="name" :value="__('Nama')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $user->name)" required autofocus />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="email" :value="__('Email')" />
                        <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $user->email)" required />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="password" :value="__('Password Baru (Kosongkan jika tidak diubah)')" />
                        <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="role" :value="__('Role')" />
                        <select name="role" id="role" class="block mt-1 w-full border-gray-300 focus-border-indigo-500 focus-ring-indigo-500 rounded-md shadow-sm">
                            <option value="admin" @if(old('role', $user->role) == 'admin') selected @endif>Admin</option>
                            <option value="atasan" @if(old('role', $user->role) == 'atasan') selected @endif>Atasan</option>
                            <option value="pegawai" @if(old('role', $user->role) == 'pegawai') selected @endif>Pegawai</option>
                        </select>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="unit_kerja_id" :value="__('Unit Kerja')" />
                        <select name="unit_kerja_id" id="unit_kerja_id" class="block mt-1 w-full border-gray-300 focus-border-indigo-500 focus-ring-indigo-500 rounded-md shadow-sm">
                            @foreach ($unitKerjas as $unit)
                                <option value="{{ $unit->id }}" @if(old('unit_kerja_id', $user->unit_kerja_id) == $unit->id) selected @endif>
                                    {{ $unit->nama_unit }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="jabatan_id" :value="__('Jabatan')" />
                        <select name="jabatan_id" id="jabatan_id" class="block mt-1 w-full border-gray-300 focus-border-indigo-500 focus-ring-indigo-500 rounded-md shadow-sm">
                            @foreach ($jabatans as $jabatan)
                                <option value="{{ $jabatan->id }}" @if(old('jabatan_id', $user->jabatan_id) == $jabatan->id) selected @endif>
                                    {{ $jabatan->nama_jabatan }} ({{ $jabatan->bidang_kerja }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-4">
                        <x-input-label for="atasan_id" :value="__('Pilih Atasan (jika role Pegawai)')" />
                        <select name="atasan_id" id="atasan_id" class="block mt-1 w-full border-gray-300 focus-border-indigo-500 focus-ring-indigo-500 rounded-md shadow-sm">
                            <option value="">Tidak ada</option>
                            @foreach ($superiors as $superior)
                                <option value="{{ $superior->id }}" @if(old('atasan_id', $user->atasan_id) == $superior->id) selected @endif>
                                    {{ $superior->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>
                            {{ __('Update User') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>