<x-filament::page>
    <div class="space-y-6">
        {{-- informasi laporan --}}
        <x-filament::section>
            @if($record->no_laporan)
                <x-slot name="heading">Nomor Laporan</x-slot>
                <p class="text-gray-600">{{ $record->no_laporan }}</p>
            @endif
            <x-slot name="heading">Uraian Peristiwa Asli</x-slot>
            <p class="text-gray-600">{{ $record->uraian_peristiwa }}</p>
        </x-filament::section>
        {{-- Kronologi Section --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-cpu-chip class="w-5 h-5 text-primary-500"/>
                    <span>Ringkasan Kronologi</span>
                </div>
            </x-slot>
            @php
                $kronologi = json_decode($record->analysis->kronologi_analysis, true);
            @endphp
            <p class="text-gray-600">{{ $kronologi['ringkasan'] ?? '-' }}</p>
        </x-filament::section>

        {{-- Analisis Hukum Section --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-cpu-chip class="w-5 h-5 text-primary-500"/>
                    <span>Analisis Hukum</span>
                </div>
            </x-slot>
            <div class="space-y-4">
                @php
                    $laws = json_decode($record->analysis->possible_laws, true);
                @endphp
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium mb-2">Pidana Umum</h4>
                    <p class="text-gray-600">{{ $laws['pidana_umum'] ?? '-' }}</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium mb-2">Teknologi Informasi</h4>
                    <p class="text-gray-600">{{ $laws['teknologi_informasi'] ?? '-' }}</p>
                </div>
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium mb-2">Perundangan Lain</h4>
                    <p class="text-gray-600">{{ $laws['perundangan_lain'] ?? '-' }}</p>
                </div>
            </div>
        </x-filament::section>

        {{-- Langkah Penyidikan Section --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-cpu-chip class="w-5 h-5 text-primary-500"/>
                    <span>Langkah Penyidikan</span>
                </div>
            </x-slot>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $investigation = json_decode($record->analysis->investigation_steps, true);
                @endphp

                {{-- Barang Bukti Digital --}}
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium mb-2">Barang Bukti Digital</h4>
                    <ul class="list-disc list-inside space-y-2 text-gray-600">
                        @foreach($investigation['barang_bukti_digital'] as $key => $value)
                            <li><span class="font-medium">{{ ucwords(str_replace('_', ' ', $key)) }}:</span> {{ $value }}</li>
                        @endforeach
                    </ul>
                </div>

                {{-- Analisis Forensik --}}
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium mb-2">Analisis Forensik</h4>
                    <ul class="list-disc list-inside space-y-2 text-gray-600">
                        @foreach($investigation['analisis_forensik'] as $key => $value)
                            <li><span class="font-medium">{{ ucwords(str_replace('_', ' ', $key)) }}:</span> {{ $value }}</li>
                        @endforeach
                    </ul>
                </div>

                {{-- Penelusuran Pelaku --}}
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium mb-2">Penelusuran Pelaku</h4>
                    <ul class="list-disc list-inside space-y-2 text-gray-600">
                        @foreach($investigation['penelusuran_pelaku'] as $key => $value)
                            <li><span class="font-medium">{{ ucwords(str_replace('_', ' ', $key)) }}:</span> {{ $value }}</li>
                        @endforeach
                    </ul>
                </div>

                {{-- Tindakan Penyidikan --}}
                <div class="border rounded-lg p-4">
                    <h4 class="font-medium mb-2">Tindakan Penyidikan</h4>
                    <ul class="list-disc list-inside space-y-2 text-gray-600">
                        @foreach($investigation['tindakan_penyidikan'] as $key => $value)
                            <li><span class="font-medium">{{ ucwords(str_replace('_', ' ', $key)) }}:</span> {{ $value }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </x-filament::section>

        {{-- Priority Level Section --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-cpu-chip class="w-5 h-5 text-primary-500"/>
                    <span>Tingkat Prioritas</span>
                </div>
            </x-slot>
            <div class="space-y-4">
                <div class="flex items-center space-x-2">
                    <span class="font-semibold">Level:</span>
                    @php
                        $priorityData = json_decode($record->analysis->priority_level, true);
                    @endphp
                    <x-filament::badge 
                        :color="match($priorityData['calculated_level'] ?? 'Sedang') {
                            'Tinggi' => 'danger',
                            'Sedang' => 'warning',
                            'Rendah' => 'success',
                            default => 'gray'
                        }">
                        {{ $priorityData['calculated_level'] ?? '-' }}
                    </x-filament::badge>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach([
                        'dampak_kejadian' => 'Dampak Kejadian', 
                        'nilai_kerugian' => 'Nilai Kerugian', 
                        'tingkat_kompleksitas' => 'Tingkat Kompleksitas', 
                        'potensi_dampak' => 'Potensi Dampak'
                    ] as $key => $label)
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium mb-2">{{ $label }}</h4>
                            <div class="space-y-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600">Level:</span>
                                    @php
                                        $priorityData = json_decode($record->analysis->priority_level, true);
                                    @endphp
                                    <x-filament::badge 
                                        :color="match($priorityData['urgensi'][$key]['level']) {
                                            'Tinggi' => 'danger',
                                            'Sedang' => 'warning',
                                            'Rendah' => 'success',
                                            default => 'gray'
                                        }">
                                        {{ $priorityData['urgensi'][$key]['level'] }}
                                    </x-filament::badge>
                                </div>
                                <p class="text-sm text-gray-600">{{ $priorityData['urgensi'][$key]['analisis'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </x-filament::section>
        
        {{-- tambahkan meta data dan disclaimer --}}
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5 text-warning-500"/>
                    <span>Disclaimer</span>
                </div>
            </x-slot>
            <p class="text-gray-600">Hasil analisis ini hanya merupakan hasil dari SiberBOT model AI dan tidak dapat dijadikan sebagai acuan hukum yang berkaitan dengan peraturan perundangan yang berlaku. <span class="font-bold">SiberBOT</span> adalah asisten AI Direktorat Reserse Siber Polda Jawa Timur</p>

            {{-- tambahkan meta data di analisis kapan dan diupdate kapan menggunakan text kecil dan text gray --}}
            <p class="text-gray-600 text-sm">Dianalisis pada: {{ $record->analysis->created_at->format('d-m-Y H:i:s') }}</p>
            <p class="text-gray-600 text-sm">Diperbarui pada: {{ $record->analysis->updated_at->format('d-m-Y H:i:s') }}</p>
        </x-filament::section>
    </div>
</x-filament::page> 