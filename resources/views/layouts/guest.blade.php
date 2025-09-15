<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-neutral-900 antialiased">
        <div class="min-h-screen flex flex-col items-center pt-6 sm:pt-0 bg-neutral-100 dark:bg-neutral-900">
            
            <main class="flex flex-col items-center justify-center w-full flex-grow px-6">
                <div>
                    <a href="/">
                        <x-application-logo class="w-20 h-20 fill-current text-brand" />
                    </a>
                </div>

                <div class="w-full sm:max-w-sm mt-6 p-8 bg-white dark:bg-neutral-900 shadow-lg overflow-hidden sm:rounded-lg border border-neutral-100">
                    {{ $slot }}
                </div>
            </main>

            <footer class="w-full py-4 text-center text-xs text-neutral-600 dark:text-neutral-300">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Dikembangkan oleh Pusat Perencanaan, Inovasi, dan Pengembangan Strategis PERPENAS Banyuwangi.
            </footer>

        </div>
    </body>
</html>
