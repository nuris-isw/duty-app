@props(['title', 'value', 'iconBgColor' => 'bg-brand'])

<div class="bg-white dark:bg-neutral-800 shadow-md rounded-2xl p-6 flex items-center space-x-6">
    <div class="{{ $iconBgColor }} text-white p-4 rounded-xl shadow-sm">
        {{ $slot }} {{-- Slot untuk SVG Icon --}}
    </div>
    <div>
        <h4 class="text-lg font-semibold text-neutral-600 dark:text-neutral-300">{{ $title }}</h4>
        <p class="text-3xl font-bold text-neutral-900 dark:text-neutral-100">{{ $value }}</p>
    </div>
</div>