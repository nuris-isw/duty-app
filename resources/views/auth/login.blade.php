<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <h1 class="text-xl font-bold leading-tight tracking-tight text-neutral-900 md:text-2xl dark:text-white text-center mb-4">
        Selamat Datang
    </h1>
    <p class="text-sm text-center text-neutral-600 dark:text-neutral-300 mb-6">
        Login untuk melanjutkan ke akun Anda.
    </p>

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full p-1" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full p-1" type="password" name="password" required autocomplete="current-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between">
            <label for="remember_me" class="inline-flex items-center">
                <input id="remember_me" type="checkbox" class="rounded dark:bg-neutral-900 border-neutral-300 dark:border-neutral-600 text-brand shadow-sm focus:ring-brand dark:focus:ring-brand-dark dark:focus:ring-offset-neutral-900" name="remember">
                <span class="ms-2 text-sm text-neutral-600 dark:text-neutral-300">{{ __('Remember me') }}</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm text-brand hover:text-brand-dark dark:text-brand dark:hover:text-brand-light rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-brand" href="{{ route('password.request') }}">
                    {{ __('Forgot your password?') }}
                </a>
            @endif
        </div>
        
        <div class="flex items-center mt-4">
            <x-primary-button class="w-full justify-center">
                {{ __('Log in') }}
            </x-primary-button>
        </div>

        <div class="my-6 flex items-center">
            <hr class="flex-grow border-neutral-300 dark:border-neutral-600">
            <span class="mx-4 text-sm font-medium text-neutral-400 dark:text-neutral-500">ATAU</span>
            <hr class="flex-grow border-neutral-300 dark:border-neutral-600">
        </div>

        <a href="{{ route('google.redirect') }}" 
           class="inline-flex items-center justify-center w-full px-4 py-2 bg-white dark:bg-neutral-800 border border-neutral-300 dark:border-neutral-600 rounded-lg font-medium text-sm text-neutral-700 dark:text-neutral-200 shadow-sm hover:bg-neutral-100 dark:hover:bg-neutral-700 transition ease-in-out duration-150">
            <svg class="w-5 h-5 me-2" viewBox="0 0 24 24" fill="currentColor">
                <path d="M21.35 11.1h-9.18v2.96h5.31c-.23 1.34-.93 2.47-1.98 3.23v2.68h3.2c1.87-1.72 2.95-4.27 2.95-7.28 0-.73-.07-1.44-.3-2.1z" fill="#4285F4"/>
                <path d="M12.17 22c2.67 0 4.91-.87 6.55-2.36l-3.2-2.68c-.87.58-1.99.92-3.35.92-2.58 0-4.75-1.75-5.53-4.09H3.26v2.57C4.9 19.65 8.26 22 12.17 22z" fill="#34A853"/>
                <path d="M6.64 13.79c-.2-.58-.32-1.19-.32-1.82s.12-1.24.32-1.82V7.58H3.26A9.87 9.87 0 0 0 2 12c0 1.61.39 3.13 1.26 4.42l3.38-2.63z" fill="#FBBC05"/>
                <path d="M12.17 4.75c1.45 0 2.75.5 3.78 1.49l2.83-2.83C17.08 1.34 14.84.5 12.17.5 8.26.5 4.9 2.85 3.26 6.42l3.38 2.63c.78-2.34 2.95-4.3 5.53-4.3z" fill="#EA4335"/>
            </svg>
            {{ __('Log in with Google') }}
        </a>
    </form>
</x-guest-layout>
