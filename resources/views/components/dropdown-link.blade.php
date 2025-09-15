<a {{ $attributes->merge(['class' => 'block w-full px-4 py-2 text-start text-sm leading-5 text-neutral-700 dark:text-neutral-200 hover:bg-brand hover:text-white dark:hover:bg-brand-dark focus:outline-none focus:bg-brand focus:text-white transition duration-150 ease-in-out']) }}>
    {{ $slot }}
</a>