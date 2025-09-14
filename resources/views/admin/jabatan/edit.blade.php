<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-dark leading-tight">
            Edit Jabatan: {{ $jabatan->nama_jabatan }} {{ $jabatan->alias }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-2xl p-6">
                <form method="POST" action="{{ route('admin.jabatan.update', $jabatan) }}">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="nama_jabatan" :value="__('Nama Jabatan')" />
                        <x-text-input id="nama_jabatan" class="block mt-1 w-full" type="text" name="nama_jabatan" :value="old('nama_jabatan', $jabatan->nama_jabatan)" required autofocus />
                        <x-input-error :messages="$errors->get('nama_jabatan')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="bidang_kerja" :value="__('Bidang Kerja')" />
                        <x-text-input id="bidang_kerja" class="block mt-1 w-full" type="text" name="bidang_kerja" :value="old('bidang_kerja', $jabatan->bidang_kerja)" />
                        <x-input-error :messages="$errors->get('bidang_kerja')" class="mt-2" />
                    </div>

                     <div class="mt-4">
                        <x-input-label for="alias" :value="__('Alias')" />
                        <x-text-input id="alias" class="block mt-1 w-full" type="text" name="alias" :value="old('alias', $jabatan->alias)" />
                        <x-input-error :messages="$errors->get('alias')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-6">
                        <x-primary-button>
                            {{ __('Update') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>