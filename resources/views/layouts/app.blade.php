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
    <body class="font-sans antialiased">
        <div class="min-h-screen flex flex-col bg-neutral-100 dark:bg-neutral-900">
            @include('layouts.navigation')

            @isset($header)
                <header class="bg-white dark:bg-neutral-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 text-neutral-900 dark:text-neutral-100">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <main class="flex-grow">
                {{ $slot }}
            </main>

            <footer class="w-full py-4 text-center text-xs bg-brand text-white">
                &copy; {{ date('Y') }} {{ config('app.name') }}. Dikembangkan oleh Pusat Perencanaan, Inovasi, dan Pengembangan Strategis PERPENAS Banyuwangi.
            </footer>
        </div>
    </body>
</html>