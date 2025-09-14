<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-brand-dark leading-tight">
            Edit Unit Kerja: {{ $unitKerja->nama_unit }}
        </h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-md rounded-2xl p-6">
                <form method="POST" action="{{ route('admin.unit-kerja.update', $unitKerja) }}">
                    @csrf
                    @method('PATCH')

                    <div>
                        <x-input-label for="nama_unit" :value="__('Nama Unit')" />
                        <x-text-input id="nama_unit" class="block mt-1 w-full" type="text" name="nama_unit" :value="old('nama_unit', $unitKerja->nama_unit)" required autofocus />
                        <x-input-error :messages="$errors->get('nama_unit')" class="mt-2" />
                    </div>

                    <div class="mt-4">
                        <x-input-label for="lokasi" :value="__('Lokasi (Opsional)')" />
                        <x-text-input id="lokasi" class="block mt-1 w-full" type="text" name="lokasi" :value="old('lokasi', $unitKerja->lokasi)" />
                        <x-input-error :messages="$errors->get('lokasi')" class="mt-2" />
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