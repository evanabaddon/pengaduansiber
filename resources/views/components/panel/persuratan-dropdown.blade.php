@props(['group', 'menu'])

<div class="fi-sidebar-nav-groups -mx-2 flex flex-col gap-y-2 mt-2">
    <div x-data="{ open: true }" class="flex flex-col">
        <button @click="open = !open"
                class="flex items-center justify-between px-2 py-2 w-full text-sm font-medium text-gray-700 hover:bg-gray-100 dark:text-gray-200 dark:hover:bg-gray-700 rounded">
            Persuratan
            <svg :class="{ 'rotate-180': open }" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div x-show="open" class="flex flex-col ml-4 mt-1 space-y-1">
            <a href="{{ url('/subbagrenmin/surats/naskah-dinas?menu='.$menu) }}"
               class="text-gray-700 dark:text-gray-200 text-sm px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700
               {{ request()->fullUrl() === url('/subbagrenmin/surats/naskah-dinas?menu='.$menu) ? 'bg-gray-200 dark:bg-gray-700 font-semibold' : '' }}">
               Naskah Dinas
            </a>
            <a href="{{ url('/subbagrenmin/surats/minlidik-sidik?menu='.$menu) }}"
               class="text-gray-700 dark:text-gray-200 text-sm px-2 py-1 rounded hover:bg-gray-100 dark:hover:bg-gray-700
               {{ request()->fullUrl() === url('/subbagrenmin/surats/minlidik-sidik?menu='.$menu) ? 'bg-gray-200 dark:bg-gray-700 font-semibold' : '' }}">
               Minlidik Sidik
            </a>
        </div>
    </div>
</div>
