<nav class="filament-sidebar-nav">
    <ul class="space-y-1">
        @foreach($navigationItems as $item)
            @php
                $hasSubItems = !empty($item->subItems);
                $isActive = $item->isActive() || ($hasSubItems && collect($item->subItems)->contains(fn($sub) => request()->fullUrlIs($sub['url'])));
            @endphp
            
            <li class="filament-sidebar-group">
                @if($item->group)
                    <div class="filament-sidebar-group-label">
                        {{ $item->group }}
                    </div>
                @endif
                
                <ul class="space-y-1">
                    <li class="filament-sidebar-item {{ $isActive ? 'active' : '' }}">
                        <a 
                            href="{{ $hasSubItems ? '#' : $item->url }}" 
                            class="flex items-center justify-between w-full px-3 py-2 text-sm font-medium rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700"
                            @if($hasSubItems)
                                x-data="{ open: {{ $isActive ? 'true' : 'false' }} }"
                                @click="open = !open"
                            @endif
                        >
                            <div class="flex items-center gap-2">
                                @if($item->icon)
                                    <x-dynamic-component 
                                        :component="$item->icon" 
                                        class="w-5 h-5" 
                                    />
                                @endif
                                <span>{{ $item->label }}</span>
                            </div>
                            
                            @if($hasSubItems)
                                <svg x-show="!open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                                <svg x-show="open" class="w-4 h-4 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            @endif
                        </a>
                        
                        @if($hasSubItems)
                            <ul 
                                x-show="open"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 transform -translate-y-2"
                                x-transition:enter-end="opacity-100 transform translate-y-0"
                                class="pl-6 mt-1 space-y-1"
                                style="display: none;"
                            >
                                @foreach($item->subItems as $subItem)
                                    @php
                                        $isSubActive = request()->fullUrlIs($subItem['url']);
                                    @endphp
                                    <li>
                                        <a 
                                            href="{{ $subItem['url'] }}" 
                                            class="flex items-center gap-2 px-3 py-2 text-sm rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 {{ $isSubActive ? 'bg-primary-50 text-primary-600 dark:bg-primary-900' : '' }}"
                                        >
                                            <span>{{ $subItem['label'] }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                </ul>
            </li>
        @endforeach
    </ul>
</nav>

<!-- AlpineJS untuk interaksi dropdown -->
<script src="//unpkg.com/alpinejs" defer></script>

<style>
.filament-sidebar-group {
    margin-bottom: 1rem;
}

.filament-sidebar-group-label {
    padding: 0.5rem 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    color: #6b7280;
}

.filament-sidebar-item.active > a {
    background-color: #1e2754;
    color: white;
}

.filament-sidebar-item.active > a:hover {
    background-color: #2d3b8b;
}
</style>