<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-gray-900">
                                {{ __('Tanda Tangan Digital') }}
                            </h2>
                            <p class="mt-1 text-sm text-gray-600">
                                {{ __('Unggah gambar tanda tangan Anda. Ukuran direkomendasikan 300x150 pixel.') }}
                            </p>
                        </header>

                        {{-- Tampilkan Tanda Tangan Saat Ini --}}
                        @if (Auth::user()->signature)
                            <div class="mt-4">
                                <p class="block font-medium text-sm text-gray-700">Tanda Tangan Saat Ini:</p>
                                <img src="{{ asset('storage/' . Auth::user()->signature) }}" alt="Tanda Tangan" class="mt-2 h-20 border rounded-md">
                            </div>
                        @endif

                        <form method="post" action="{{ route('profile.signature.update') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            <div>
                                <x-input-label for="signature" :value="__('File Gambar Tanda Tangan (PNG/JPG)')" />
                                <x-text-input id="signature" name="signature" type="file" class="mt-1 block w-full" />
                                <x-input-error class="mt-2" :messages="$errors->get('signature')" />
                            </div>

                            <div class="flex items-center gap-4">
                                <x-primary-button>{{ __('Simpan') }}</x-primary-button>
                                @if (session('status') === 'signature-updated')
                                    <p
                                        x-data="{ show: true }"
                                        x-show="show"
                                        x-transition
                                        x-init="setTimeout(() => show = false, 2000)"
                                        class="text-sm text-gray-600"
                                    >{{ __('Tersimpan.') }}</p>
                                @endif
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
