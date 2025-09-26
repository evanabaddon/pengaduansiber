<x-filament::page>
    <div class="w-full" style="height: calc(100vh - 100px);">
        <iframe 
            src="{{ route('anggaran.convertPdf', $record->id) }}" 
            class="w-full h-full border-0 rounded-lg shadow"
            frameborder="0"
        ></iframe>
    </div>
</x-filament::page>