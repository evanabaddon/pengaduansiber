<x-filament-panels::page>
    <div class="flex gap-4">
        <!-- Sidebar -->
        <aside 
            x-data="{ open: false }" 
            class="w-64 shrink-0 bg-white dark:bg-gray-800 shadow rounded-lg p-4"
        >
            <button 
                @click="open = !open" 
                class="w-full mb-4 px-3 py-2 bg-primary-500 text-white rounded-lg"
            >
                Toggle Menu
            </button>

            <nav class="space-y-2" x-show="open" x-transition>
                @foreach (\App\Filament\Subbagrenmin\Pages\PersuratanList::sidebarData() as $parent => $children)
                    <div x-data="{ expanded: {{ request('type') === strtolower(str_replace(' ', '_', $parent)) ? 'true' : 'false' }} }">
                        <button 
                            @click="expanded = !expanded"
                            class="flex items-center justify-between w-full px-3 py-2 font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700 rounded"
                        >
                            <span>{{ $parent }}</span>
                            <svg x-show="!expanded" class="w-4 h-4" fill="none" stroke="currentColor"><path d="M6 9l6 6 6-6"/></svg>
                            <svg x-show="expanded" class="w-4 h-4" fill="none" stroke="currentColor"><path d="M18 15l-6-6-6 6"/></svg>
                        </button>

                        <div x-show="expanded" class="ml-4 mt-1 space-y-1" x-transition>
                            @foreach ($children as $child)
                                @php
                                    $url = \App\Filament\Resources\SuratResource::getUrl('index', panel: 'subbagrenmin', parameters: [
                                        'menu' => request('menu', 'urkeu'),
                                        'type' => $child['type'],
                                        'subtype' => $child['subtype'],
                                    ]);
                                    $active = request('type') === $child['type'] && request('subtype') === $child['subtype'];
                                @endphp
                                <a href="{{ $url }}"
                                   class="block px-3 py-1.5 text-sm rounded 
                                          {{ $active ? 'bg-primary-500 text-white' : 'text-gray-600 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700' }}">
                                    {{ $child['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </nav>
        </aside>

        <!-- Content -->
        <div class="flex-1 bg-white dark:bg-gray-800 shadow rounded-lg p-4">
            <h1 class="text-xl font-bold mb-4">Persuratan {{ ucfirst(request('menu')) }}</h1>

            <livewire:filament.resources.surat-resource.pages.list-surats 
                :menu="request('menu')" 
                :type="request('type')" 
                :subtype="request('subtype')"
            />
        </div>
    </div>
</x-filament-panels::page>
