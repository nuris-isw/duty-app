<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-neutral-900 dark:text-neutral-100 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Menggunakan gaya kartu yang konsisten dengan dashboard --}}
            <div class="p-4 sm:p-8 bg-white dark:bg-neutral-800 shadow-md sm:rounded-2xl border border-neutral-200 dark:border-neutral-700">
                <div class="max-w-xl">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-neutral-800 shadow-md sm:rounded-2xl border border-neutral-200 dark:border-neutral-700">
                <div class="max-w-xl">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <div class="p-4 sm:p-8 bg-white dark:bg-neutral-800 shadow-md sm:rounded-2xl border border-neutral-200 dark:border-neutral-700">
                <div class="max-w-xl">
                    <section>
                        <header>
                            <h2 class="text-lg font-medium text-neutral-900 dark:text-neutral-100">
                                {{ __('Tanda Tangan Digital') }}
                            </h2>

                            <p class="mt-1 text-sm text-neutral-600 dark:text-neutral-300">
                                {{ __('Unggah gambar tanda tangan Anda. Ukuran direkomendasikan 300x150 pixel.') }}
                            </p>
                        </header>

                        {{-- Tampilkan Tanda Tangan Saat Ini --}}
                        @if (Auth::user()->signature)
                            <div class="mt-4">
                                <p class="block font-medium text-sm text-neutral-700 dark:text-neutral-200">Tanda Tangan Saat Ini:</p>
                                <img src="{{ asset('storage/' . Auth::user()->signature) }}" alt="Tanda Tangan" class="mt-2 h-20 border border-neutral-300 dark:border-neutral-600 rounded-md">
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
                                        class="text-sm text-green-600 dark:text-green-400"
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