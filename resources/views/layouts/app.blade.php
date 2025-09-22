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
        
        {{-- FullCalendar --}}
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
    </head>
    <body class="font-sans antialiased bg-neutral-100 dark:bg-neutral-900 text-neutral-900 dark:text-neutral-100">
        <div class="min-h-screen flex flex-col">
            
            {{-- Navigasi --}}
            @include('layouts.navigation')

            {{-- Header Opsional --}}
            @isset($header)
                <header class="bg-white dark:bg-neutral-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            {{-- Konten Utama --}}
            <main class="flex-grow">
                {{ $slot }}
            </main>

            {{-- Footer --}}
            <footer class="w-full py-4 text-center text-xs bg-brand text-white dark:bg-brand-dark dark:text-neutral-100">
                &copy; {{ date('Y') }} {{ config('app.name') }}.
                Dikembangkan oleh Pusat Perencanaan, Inovasi, dan Pengembangan Strategis PERPENAS Banyuwangi.
            </footer>
        </div>

        @stack('scripts')
    </body>
</html>
