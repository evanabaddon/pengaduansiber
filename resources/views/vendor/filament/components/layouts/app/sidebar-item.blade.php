@php
    $isActive = $item->isActive() || $item->isChildItemsActive();
@endphp

<li x-data="{ open: {{ $isActive ? 'true' : 'false' }} }" class="mb-1">
    <button
        @if ($item->getUrl())
            onclick="window.location='{{ $item->getUrl() }}'"
        @else
            @click="open = !open"
        @endif
        class="flex items-center justify-between w-full px-3 py-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 {{ $isActive ? 'bg-gray-100 dark:bg-gray-800' : '' }}"
    >
        <div class="flex items-center gap-2">
            @if ($item->getIcon())
                <x-dynamic-component :component="$item->getIcon()" class="w-5 h-5" />
            @endif
            <span>{{ $item->getLabel() }}</span>
        </div>
        @if (count($item->getChildItems()))
            <x-heroicon-o-chevron-down x-show="!open" class="w-4 h-4" />
            <x-heroicon-o-chevron-up x-show="open" class="w-4 h-4" />
        @endif
    </button>

    @if (count($item->getChildItems()))
        <ul x-show="open" x-collapse class="ml-6 mt-1 space-y-1">
            @foreach ($item->getChildItems() as $child)
                <li>
                    <a
                        href="{{ $child->getUrl() }}"
                        class="block px-3 py-1.5 text-sm rounded-lg hover:bg-gray-100 dark:hover:bg-gray-800 {{ $child->isActive() ? 'bg-gray-100 dark:bg-gray-800 font-semibold' : '' }}"
                    >
                        {{ $child->getLabel() }}
                    </a>
                </li>
            @endforeach
        </ul>
    @endif
</li>
