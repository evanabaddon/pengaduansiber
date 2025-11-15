<div class="fi-sidebar">
    {{-- === DASHBOARD === --}}
    <div class="fi-sidebar-item">
        <span href="{{ url('/subbagrenmin') }}"
           class="fi-sidebar-item-button flex items-center gap-x-3 w-full text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-white/5">
            <x-heroicon-o-check-circle class="fi-sidebar-item-icon h-6 w-6" />
            <span class="fi-sidebar-item-label">Subbagrenmin</span>
        </span>
    </div>

    {{-- === URREN === --}}
    <div class="fi-sidebar-group">
        <div x-on:click="$store.sidebar.toggleCollapsedGroup('urren')" 
             class="fi-sidebar-group-button flex items-center gap-x-3 py-2 mt-4 cursor-pointer">
            <span class="fi-sidebar-group-label flex-1 text-sm font-medium leading-6 text-gray-500 dark:text-gray-400">
                Urren
            </span>
            <button class="fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 -m-2 h-9 w-9 text-gray-400 hover:text-gray-500 focus-visible:ring-primary-600 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:ring-primary-500 fi-color-gray fi-sidebar-group-collapse-button" 
                    title="Urren" 
                    type="button" 
                    x-on:click.stop="$store.sidebar.toggleCollapsedGroup('urren')" 
                    x-bind:class="{ '-rotate-180': !$store.sidebar.groupIsCollapsed('urren') }">
                <span class="sr-only">Urren</span>
                <x-heroicon-o-chevron-down class="fi-icon-btn-icon h-5 w-5" />
            </button>
        </div>

        <div x-show="!$store.sidebar.groupIsCollapsed('urren')" 
             x-collapse 
             class="fi-sidebar-group-collapsible mt-2 space-y-1">
            <div class="pl-2">
                {{-- Menu Persuratan sebagai parent --}}
                <div class="fi-sidebar-item">
                    <div class="fi-sidebar-item-button flex items-center gap-x-3 py-2 w-full text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-white/5 cursor-pointer"
                         onclick="toggleSubMenu('urren-persuratan')">
                        <x-heroicon-o-document-text class="fi-sidebar-item-icon h-6 w-6" />
                        <span class="fi-sidebar-item-label flex-1">Persuratan</span>
                        <x-heroicon-o-chevron-right class="h-4 w-4 transition-transform duration-200" id="urren-persuratan-chevron" />
                    </div>
                    
                    {{-- Sub menu Persuratan --}}
                    <div id="urren-persuratan-submenu" class="mt-1 ml-4 hidden space-y-1 border-l border-gray-200 dark:border-gray-600 pl-8">
                        <a href="{{ url('/subbagrenmin/surats?menu=urren&jenis_dokumen=Naskah Dinas') }}"
                           class="fi-sidebar-item-button flex items-center gap-x-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-white/5">
                           <x-heroicon-o-ellipsis-horizontal class="fi-sidebar-item-icon h-5 w-5" />  
                           <span class="fi-sidebar-item-label">Naskah Dinas</span>
                        </a>
                        {{-- <a href="{{ url('/subbagrenmin/surats?menu=urren&jenis_dokumen=Min. Lidik & Sidik') }}"
                           class="fi-sidebar-item-button flex items-center gap-x-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-white/5">
                           <x-heroicon-o-ellipsis-horizontal class="fi-sidebar-item-icon h-5 w-5" />  
                           <span class="fi-sidebar-item-label">Min. Lidik & Sidik</span>
                        </a> --}}
                        <span class="fi-sidebar-item-button flex items-center gap-x-3 py-2 text-sm font-medium text-gray-400 dark:text-gray-500 rounded-lg opacity-50 cursor-not-allowed">
                            <x-heroicon-o-ellipsis-horizontal class="fi-sidebar-item-icon h-5 w-5" />  
                            <span class="fi-sidebar-item-label">Min. Lidik & Sidik</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === URMINTU === --}}
    <div class="fi-sidebar-group">
        <div x-on:click="$store.sidebar.toggleCollapsedGroup('urmintu')" 
             class="fi-sidebar-group-button flex items-center gap-x-3 py-2 mt-4 cursor-pointer">
            <span class="fi-sidebar-group-label flex-1 text-sm font-medium leading-6 text-gray-500 dark:text-gray-400">
                Urmintu
            </span>
            <button class="fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 -m-2 h-9 w-9 text-gray-400 hover:text-gray-500 focus-visible:ring-primary-600 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:ring-primary-500 fi-color-gray fi-sidebar-group-collapse-button" 
                    title="Urmintu" 
                    type="button" 
                    x-on:click.stop="$store.sidebar.toggleCollapsedGroup('urmintu')" 
                    x-bind:class="{ '-rotate-180': !$store.sidebar.groupIsCollapsed('urmintu') }">
                <span class="sr-only">Urmintu</span>
                <x-heroicon-o-chevron-down class="fi-icon-btn-icon h-5 w-5 text-gray-50" />
            </button>
        </div>

        <div x-show="!$store.sidebar.groupIsCollapsed('urmintu')" 
             x-collapse 
             class="fi-sidebar-group-collapsible mt-2 space-y-1">
            <div class="pl-2">
                {{-- Menu Personel --}}
                <a href="{{ url('/subbagrenmin/personel') }}"
                   class="fi-sidebar-item-button flex items-center gap-x-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-white/5">
                    <x-heroicon-o-user-group class="fi-sidebar-item-icon h-6 w-6" />
                    <span class="fi-sidebar-item-label">Personel</span>
                </a>

                {{-- Menu Persuratan sebagai parent --}}
                <div class="fi-sidebar-item">
                    <div class="fi-sidebar-item-button flex items-center gap-x-3 py-2 w-full text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-white/5 cursor-pointer"
                         onclick="toggleSubMenu('urmintu-persuratan')">
                        <x-heroicon-o-document-text class="fi-sidebar-item-icon h-6 w-6" />
                        <span class="fi-sidebar-item-label flex-1">Persuratan</span>
                        <x-heroicon-o-chevron-right class="h-4 w-4 transition-transform duration-200" id="urmintu-persuratan-chevron" />
                    </div>
                    
                    {{-- Sub menu Persuratan --}}
                    <div id="urmintu-persuratan-submenu" class="mt-1 ml-4 hidden space-y-1 border-l border-gray-200 dark:border-gray-600 pl-3">
                        <a href="{{ url('/subbagrenmin/surats?menu=urmintu&jenis_dokumen=Naskah Dinas') }}"
                           class="fi-sidebar-item-button flex items-center gap-x-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-white/5">
                           <x-heroicon-o-ellipsis-horizontal class="fi-sidebar-item-icon h-5 w-5" />  
                           <span class="fi-sidebar-item-label">Naskah Dinas</span>
                        </a>
                        {{-- <a href="{{ url('/subbagrenmin/surats?menu=urmintu&jenis_dokumen=Min. Lidik & Sidik') }}"
                           class="fi-sidebar-item-button flex items-center gap-x-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-white/5">
                           <x-heroicon-o-ellipsis-horizontal class="fi-sidebar-item-icon h-6 w-5" />  
                           <span class="fi-sidebar-item-label">Min. Lidik & Sidik</span>
                        </a> --}}
                        <span class="fi-sidebar-item-button flex items-center gap-x-3 py-2 text-sm font-medium text-gray-400 dark:text-gray-500 rounded-lg opacity-50 cursor-not-allowed">
                            <x-heroicon-o-ellipsis-horizontal class="fi-sidebar-item-icon h-5 w-5" />  
                            <span class="fi-sidebar-item-label">Min. Lidik & Sidik</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- === URKEU === --}}
    <div class="fi-sidebar-group">
        <div x-on:click="$store.sidebar.toggleCollapsedGroup('urkeu')" 
             class="fi-sidebar-group-button flex items-center gap-x-3 py-2 mt-4 cursor-pointer">
            <span class="fi-sidebar-group-label flex-1 text-sm font-medium leading-6 text-gray-500 dark:text-gray-400">
                Urkeu
            </span>
            <button class="fi-icon-btn relative flex items-center justify-center rounded-lg outline-none transition duration-75 focus-visible:ring-2 -m-2 h-9 w-9 text-gray-400 hover:text-gray-500 focus-visible:ring-primary-600 dark:text-gray-500 dark:hover:text-gray-400 dark:focus-visible:ring-primary-500 fi-color-gray fi-sidebar-group-collapse-button" 
                    title="Urkeu" 
                    type="button" 
                    x-on:click.stop="$store.sidebar.toggleCollapsedGroup('urkeu')" 
                    x-bind:class="{ '-rotate-180': !$store.sidebar.groupIsCollapsed('urkeu') }">
                <span class="sr-only">Urkeu</span>
                <x-heroicon-o-chevron-down class="fi-icon-btn-icon h-5 w-5" />
            </button>
        </div>

        <div x-show="!$store.sidebar.groupIsCollapsed('urkeu')" 
             x-collapse 
             class="fi-sidebar-group-collapsible mt-2 space-y-1">
            <div class="pl-2">
                {{-- Menu Anggaran --}}
                <a href="{{ url('/subbagrenmin/anggaran') }}"
                   class="fi-sidebar-item-button flex items-center gap-x-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-white/5">
                    <x-heroicon-o-currency-dollar class="fi-sidebar-item-icon h-6 w-6" />
                    <span class="fi-sidebar-item-label">Anggaran</span>
                </a>

                {{-- Menu Persuratan sebagai parent --}}
                <div class="fi-sidebar-item">
                    <div class="fi-sidebar-item-button flex items-center gap-x-3 py-2 w-full text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-white/5 cursor-pointer"
                         onclick="toggleSubMenu('urkeu-persuratan')">
                        <x-heroicon-o-document-text class="fi-sidebar-item-icon h-6 w-6" />
                        <span class="fi-sidebar-item-label flex-1">Persuratan</span>
                        <x-heroicon-o-chevron-right class="h-4 w-4 transition-transform duration-200" id="urkeu-persuratan-chevron" />
                    </div>
                    
                    {{-- Sub menu Persuratan --}}
                    <div id="urkeu-persuratan-submenu" class="mt-1 ml-4 hidden space-y-1 border-l border-gray-200 dark:border-gray-600 pl-3">
                        <a href="{{ url('/subbagrenmin/surats?menu=urkeu&jenis_dokumen=Naskah Dinas') }}"
                           class="fi-sidebar-item-button flex items-center gap-x-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-white/5">
                           <x-heroicon-o-ellipsis-horizontal class="fi-sidebar-item-icon h-5 w-5" /> 
                           <span class="fi-sidebar-item-label">Naskah Dinas</span>
                        </a>
                        {{-- <a href="{{ url('/subbagrenmin/surats?menu=urkeu&jenis_dokumen=Min. Lidik & Sidik') }}"
                           class="fi-sidebar-item-button flex items-center gap-x-3 px-5 py-2 text-sm font-medium text-gray-700 dark:text-gray-200 rounded-lg hover:bg-gray-400 dark:hover:bg-white/5">
                           <x-heroicon-o-ellipsis-horizontal class="fi-sidebar-item-icon h-5 w-5" />  
                           <span class="fi-sidebar-item-label">Min. Lidik & Sidik</span>
                        </a> --}}
                        <span class="fi-sidebar-item-button flex items-center gap-x-3 py-2 text-sm font-medium text-gray-400 dark:text-gray-500 rounded-lg opacity-50 cursor-not-allowed">
                            <x-heroicon-o-ellipsis-horizontal class="fi-sidebar-item-icon h-5 w-5" />  
                            <span class="fi-sidebar-item-label">Min. Lidik & Sidik</span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script>
    function toggleSubMenu(id) {
        const submenu = document.getElementById(`${id}-submenu`);
        const chevron = document.getElementById(`${id}-chevron`);
        
        if (submenu && chevron) {
            submenu.classList.toggle('hidden');
            chevron.classList.toggle('rotate-90');
        }
    }
</script>