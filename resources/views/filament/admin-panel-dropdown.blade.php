{{-- D:\CODING\pengaduansiber\resources\views\filament\admin-panel-dropdown.blade.php --}}
<div class="fi-sidebar-nav-groups -mx-2 flex flex-col gap-y-7 ">
    <x-filament::dropdown placement="bottom-start">
        <x-slot name="trigger">
            <button type="button" class="fi-sidebar-group-button flex items-center gap-x-3 w-full text-sm font-medium text-gray-700 dark:text-gray-200 px-2 py-3 rounded" style="padding-top: 0.8rem; padding-bottom: 0.8rem; padding-left: 1rem;">
                <span class="w-5 h-5 font-semibold bg-white rounded-full shrink-0 text-primary-500">
                    {{ strtoupper(substr($label,0,1)) }}
                </span>
                <span>{{ $label }}</span>
                <x-filament::icon icon="heroicon-m-chevron-down" class="w-5 h-5 ms-auto shrink-0 text-gray-400"/>
            </button>
        </x-slot>

        <x-filament::dropdown.list class="fi-sidebar-group-items flex flex-col gap-y-1">
            @foreach ($items as $url => $name)
                <x-filament::dropdown.list.item :href="$url" tag="a"
                    class="fi-sidebar-item flex items-center gap-x-3 px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                    {{ $name }}
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div>
{{-- <div class="fi-sidebar-nav-groups -mx-2 flex flex-col gap-y-7 ">
    <x-filament::dropdown placement="bottom-start">
        <x-slot name="trigger">
            <button type="button" class="fi-sidebar-group-button flex items-center gap-x-3 w-full text-sm font-medium text-gray-700 dark:text-gray-200 px-2 py-3 rounded">
                <span class="w-5 h-5 font-semibold bg-white rounded-full shrink-0 text-primary-500">
                    {{ strtoupper(substr($label,0,1)) }}
                </span>
                <span>{{ $label }}</span>
                <x-filament::icon icon="heroicon-m-chevron-down" class="w-5 h-5 ms-auto shrink-0 text-gray-400"/>
            </button>
        </x-slot>

        <x-filament::dropdown.list class="fi-sidebar-group-items flex flex-col gap-y-1">
            @foreach ($items as $url => $name)
                <x-filament::dropdown.list.item :href="$url" tag="a"
                    class="fi-sidebar-item flex items-center gap-x-3 px-2 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded">
                    {{ $name }}
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
</div> --}}
