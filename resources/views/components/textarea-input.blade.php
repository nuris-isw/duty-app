@props(['disabled' => false])

<textarea @disabled($disabled) {{ $attributes->merge([
    'class' => 'bg-neutral-50 dark:bg-neutral-800 border-neutral-300 dark:border-neutral-700 text-neutral-900 dark:text-neutral-300 focus:border-brand dark:focus:border-brand-light focus:ring-brand dark:focus:ring-brand-light rounded-md shadow-sm px-3 py-2'
]) }}>{{ $slot }}</textarea>