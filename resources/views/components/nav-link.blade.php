@props(['active' => false])

@php
// Logika kelas dipisah agar lebih bersih dan mudah dibaca
$baseClasses     = 'inline-flex items-center px-1 pt-1 border-b-2 text-sm font-medium leading-5 focus:outline-none transition duration-150 ease-in-out';

$activeClasses   = 'border-brand text-brand font-bold dark:border-brand-light dark:text-brand-light';

$inactiveClasses = 'border-transparent text-neutral-600 dark:text-neutral-300 hover:text-brand hover:border-brand/[0.3] dark:hover:text-brand-light dark:hover:border-brand-light/[0.3]';

$classes = $baseClasses . ' ' . ($active ? $activeClasses : $inactiveClasses);
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>