@php
    $extra = $getExtraAttributes();

    $prefix = $extra['prefix'] ?? null;
    if ($prefix instanceof \Closure) {
        $prefix = $evaluate($prefix);
    }

    $suffix = $extra['suffix'] ?? null;
    if ($suffix instanceof \Closure) {
        $suffix = $evaluate($suffix);
    }
@endphp

<x-dynamic-component
    :component="$getFieldWrapperView()"
    :field="$field"
>
    <div
        class="fi-input-wrp flex rounded-lg shadow-sm ring-1 transition duration-75 bg-gray-100 dark:bg-white/5 
            overflow-hidden ring-gray-950/10 dark:ring-white/20
            disabled:cursor-not-allowed disabled:opacity-70"
    >
        {{-- Prefix --}}
        @if ($prefix)
            <div class="items-center gap-x-3 ps-3 flex border-e border-gray-200 pe-3 dark:border-white/10">
                <span class="fi-input-wrp-label whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $prefix }}
                </span>
            </div>
        @endif

        {{-- Value --}}
        <div class="min-w-0 flex-1">
            <div
                class="fi-input block w-full border-none py-1.5 text-base text-gray-950 dark:text-white
                       transition duration-75 sm:text-sm sm:leading-6
                       disabled:text-gray-500 dark:disabled:text-gray-400 ps-3 pe-3"
            >
                {{ number_format((int) $getState(), 0, '.', ',') }}
            </div>
        </div>

        {{-- Suffix --}}
        @if ($suffix)
            <div class="items-center gap-x-3 pe-3 flex border-s border-gray-200 ps-3 dark:border-white/10">
                <span class="fi-input-wrp-label whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ $suffix }}
                </span>
            </div>
        @endif
    </div>
</x-dynamic-component>
