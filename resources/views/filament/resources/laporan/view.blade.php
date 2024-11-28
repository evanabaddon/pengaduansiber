@php
    // Data wilayah pelapor
    $provinsi = $record->pelapors ? (app('wilayah')->getProvinsi()[$record->pelapors->province_id] ?? '-') : '-';
    $kabupaten = $record->pelapors ? (app('wilayah')->getKabupaten($record->pelapors->province_id)[$record->pelapors->city_id] ?? '-') : '-';
    $kecamatan = $record->pelapors ? (app('wilayah')->getKecamatan($record->pelapors->city_id)[$record->pelapors->district_id] ?? '-') : '-';
    $kelurahan = $record->pelapors ? (app('wilayah')->getKelurahan($record->pelapors->district_id)[$record->pelapors->subdistrict_id] ?? '-') : '-';

    // Data wilayah korban
    $provinsiKorban = $record->korbans ? (app('wilayah')->getProvinsi()[$record->korbans->province_id] ?? '-') : '-';
    $kabupatenKorban = $record->korbans ? (app('wilayah')->getKabupaten($record->korbans->province_id)[$record->korbans->city_id] ?? '-') : '-';
    $kecamatanKorban = $record->korbans ? (app('wilayah')->getKecamatan($record->korbans->city_id)[$record->korbans->district_id] ?? '-') : '-';
    $kelurahanKorban = $record->korbans ? (app('wilayah')->getKelurahan($record->korbans->district_id)[$record->korbans->subdistrict_id] ?? '-') : '-';

    // Data wilayah terlapor
    $provinsiTerlapor = $record->terlapors ? (app('wilayah')->getProvinsi()[$record->terlapors->province_id] ?? '-') : '-';
    $kabupatenTerlapor = $record->terlapors ? (app('wilayah')->getKabupaten($record->terlapors->province_id)[$record->terlapors->city_id] ?? '-') : '-';
    $kecamatanTerlapor = $record->terlapors ? (app('wilayah')->getKecamatan($record->terlapors->city_id)[$record->terlapors->district_id] ?? '-') : '-';
    $kelurahanTerlapor = $record->terlapors ? (app('wilayah')->getKelurahan($record->terlapors->district_id)[$record->terlapors->subdistrict_id] ?? '-') : '-';

    // Data wilayah tkp
    $provinsiTkp = app('wilayah')->getProvinsi()[$record->province_id] ?? '-';
    $kabupatenTkp = app('wilayah')->getKabupaten($record->province_id)[$record->city_id] ?? '-';
    $kecamatanTkp = app('wilayah')->getKecamatan($record->city_id)[$record->district_id] ?? '-';
    $kelurahanTkp = app('wilayah')->getKelurahan($record->district_id)[$record->subdistrict_id] ?? '-';
@endphp
<x-filament-panels::page>
    <div class="bg-white dark:bg-gray-800">
        <div class="p-6">
            <!-- Header Surat dengan Logo -->
            <div class="text-center mb-8 border-b-2 pb-4">
                <!-- Logo Siber -->
                <div class="flex justify-center mb-4">
                    <div class="w-[100px]">
                        <img src="{{ asset('images/logo-siber-polri.png') }}" style="width: 100px; height: auto;" alt="Logo Siber" class="w-full h-auto">
                    </div>
                </div>
                <!-- Judul dan Alamat -->
                <div class="text-center">
                    <h2 class="text-xl font-bold mt-1">DIREKTORAT RESERSE SIBER POLDA JAWA TIMUR</h2>
                    <p class="text-base mt-2">Jl. Ahmad Yani No. 116, Surabaya - 60235</p>
                </div>
                <!-- Garis Pembatas -->
                <div class="border-b-2 border-black my-4"></div>
                <!-- Nomor Surat -->
                <p class="text-xl font-semibold mt-4">{{ $record instanceof \App\Models\LaporanInformasi ? 'INFORMASI / SURAT MASYARAKAT' : 'LAPORAN POLISI' }}</p>
                <p class="text-lg">Nomor: {{ $record->id }}/{{ $record instanceof \App\Models\LaporanInformasi ? 'LIM' : 'LP' }}/{{ date('Y') }}</p>
            </div>

            <!-- Konten Utama -->
            <div class="space-y-6">
                <!-- Pelapor dan Korban dalam satu baris -->
                <div class="flex gap-8">
                    <!-- Informasi Pelapor -->
                    <div class="w-1/2">
                        <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">A. IDENTITAS PELAPOR</h3>
                        <div class="space-y-2 pl-4 text-sm">
                            <div class="flex gap-2">
                                <p class="w-32">Nama</p>
                                <p>: {{ $record->pelapors->nama ?? '-' }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">No Identitas</p>
                                <p>: {{ $record->pelapors->identity_no ?? '-' }}</p>
                            </div>
                            {{-- Kewarganegaraan --}}
                            <div class="flex gap-2">
                                <p class="w-32">Kewarganegaraan</p>
                                <p>: @php
                                    $countryCode = $record->pelapors?->kewarganegaraan;
                                    if ($countryCode) {
                                        $countries = \PeterColes\Countries\CountriesFacade::lookup(null, 'id')->toArray();
                                        echo array_flip($countries)[$countryCode] ?? $countryCode;
                                    } else {
                                        echo '-';
                                    }
                                @endphp</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Jenis Kelamin</p>
                                <p>: {{ $record->pelapors->jenis_kelamin ?? '-' }}</p>
                            </div>
                            {{-- Tempat Tanggal Lahir --}}
                            <div class="flex gap-2">
                                <p class="w-32">Tempat/Tgl Lahir</p>
                                <p>: {{ $record->pelapors->tempat_lahir ?? '-' }}, {{ $record->pelapors?->tanggal_lahir ? date('d F Y', strtotime($record->pelapors?->tanggal_lahir)) : '-' }}</p>
                            </div>
                            {{-- Agama --}}
                            <div class="flex gap-2">
                                <p class="w-32">Agama</p>
                                <p>: {{ $record->pelapors->agama ?? '-' }}</p>
                            </div>
                            {{-- Alamat --}}
                            <div class="flex gap-2">
                                <p class="w-32">Alamat</p>
                                <p>: {{ $record->pelapors->alamat ?? '-' }}, {{ ucwords(strtolower($kelurahan)) ?? '-' }}, {{ ucwords(strtolower($kecamatan)) ?? '-' }}, {{ ucwords(strtolower($kabupaten)) ?? '-' }}, {{ ucwords(strtolower($provinsi)) ?? '-' }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Kontak</p>
                                <p>: {{ $record->pelapors->kontak ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Korban -->
                    <div class="w-1/2">
                        <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">B. IDENTITAS KORBAN</h3>
                        <div class="space-y-2 pl-4 text-sm">
                            <div class="flex gap-2">
                                <p class="w-32">Nama</p>
                                <p>: {{ $record->korbans->nama ?? '-' }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">No Identitas</p>
                                <p>: {{ $record->korbans->identity_no ?? '-' }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Kewarganegaraan</p>
                                <p>: @php
                                    $countryCode = $record->korbans?->kewarganegaraan;
                                    if ($countryCode) {
                                        $countries = \PeterColes\Countries\CountriesFacade::lookup(null, 'id')->toArray();
                                        echo array_flip($countries)[$countryCode] ?? $countryCode;
                                    } else {
                                        echo '-';
                                    }
                                @endphp</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Jenis Kelamin</p>
                                <p>: {{ $record->korbans->jenis_kelamin ?? '-' }}</p>
                            </div>
                            {{-- Tempat Tanggal Lahir --}}
                            <div class="flex gap-2">
                                <p class="w-32">Tempat/Tgl Lahir</p>
                                <p>: {{ $record->korbans->tempat_lahir ?? '-' }}, {{ $record->korbans?->tanggal_lahir ? date('d F Y', strtotime($record->korbans?->tanggal_lahir)) : '-' }}</p>
                            </div>
                            {{-- Agama --}}
                            <div class="flex gap-2">
                                <p class="w-32">Agama</p>
                                <p>: {{ $record->korbans->agama ?? '-' }}</p>
                            </div>
                            {{-- Alamat --}}
                            <div class="flex gap-2">
                                <p class="w-32">Alamat</p>
                                <p>: {{ $record->korbans->alamat ?? '-' }}, {{ ucwords(strtolower($kelurahanKorban)) ?? '-' }}, {{ ucwords(strtolower($kecamatanKorban)) ?? '-' }}, {{ ucwords(strtolower($kabupatenKorban)) ?? '-' }}, {{ ucwords(strtolower($provinsiKorban)) ?? '-' }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Kontak</p>
                                <p>: {{ $record->korbans->kontak ?? '-' }}</p>
                            </div>
                            
                        </div>
                    </div>
                </div>

                <!-- Informasi Terlapor (Full Width) -->
                <div class="mb-6">
                    <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">C. IDENTITAS TERLAPOR</h3>
                    <div class="space-y-2 pl-4 text-sm">
                        <div class="flex gap-2">
                            <p class="w-32">Nama</p>
                            <p>: {{ $record->terlapors->nama ?? '-' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">No Identitas</p>
                            <p>: {{ $record->terlapors->identity_no ?? '-' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Kewarganegaraan</p>
                            <p>: @php
                                $countryCode = $record->terlapors?->kewarganegaraan;
                                if ($countryCode) {
                                    $countries = \PeterColes\Countries\CountriesFacade::lookup(null, 'id')->toArray();
                                    echo array_flip($countries)[$countryCode] ?? $countryCode;
                                } else {
                                    echo '-';
                                }
                            @endphp</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Jenis Kelamin</p>
                            <p>: {{ $record->terlapors->jenis_kelamin ?? '-' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Tempat/Tgl Lahir</p>
                            <p>: {{ $record->terlapors?->tempat_lahir ?? '-' }}, {{ $record->terlapors?->tanggal_lahir ? date('d F Y', strtotime($record->terlapors?->tanggal_lahir)) : '-' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Agama</p>
                            <p>: {{ $record->terlapors->agama ?? '-' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Alamat</p>
                            <p>: {{ $record->terlapors->alamat ?? '-' }}, {{ ucwords(strtolower($kelurahanTerlapor)) ?? '-' }}, {{ ucwords(strtolower($kecamatanTerlapor)) ?? '-' }}, {{ ucwords(strtolower($kabupatenTerlapor)) ?? '-' }}, {{ ucwords(strtolower($provinsiTerlapor)) ?? '-' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Kontak</p>
                            <p>: {{ $record->terlapors->kontak ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Informasi Perkara (Full Width) -->
                <div class="mb-6">
                    <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">D. INFORMASI PERKARA</h3>
                    <div class="space-y-2 pl-4 text-sm">
                        <div class="flex gap-2">
                            <p class="w-32">TKP</p>
                            <p>: {{ $record->tkp }}, {{ ucwords(strtolower($kelurahanTkp)) }}, {{ ucwords(strtolower($kecamatanTkp)) }}, {{ ucwords(strtolower($kabupatenTkp)) }}, {{ ucwords(strtolower($provinsiTkp)) }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Tgl. Lapor</p>
                            <p>: {{ date('d F Y', strtotime($record->tanggal_lapor)) }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Tgl. Kejadian</p>
                            <p>: {{ date('d F Y', strtotime($record->tanggal_kejadian)) }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Perkara</p>
                            <p>: {{ $record->perkara }}</p>
                        </div>
                        <div class="mt-4">
                            <p class="font-semibold mb-2">Uraian Peristiwa:</p>
                            <p class="pl-4 text-justify">{{ $record->uraian_peristiwa }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Kerugian</p>
                            <p>: Rp {{ number_format($record->kerugian, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Status Laporan -->
                <div class="mb-6">
                    <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">E. YANG MENANGANI</h3>
                    <div class="space-y-2 pl-4 text-sm">
                        <div class="flex gap-2">
                            <p class="w-32">Subdit</p>
                            <p>: {{ $record->subdit->name ?? '-' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Unit</p>
                            <p>: {{ $record->unit->name ?? '-'  }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Penyidik</p>
                            <p>: {{ $record->penyidik->name ?? '-' }}</p>
                        </div>
                        <div class="flex gap-2">
                            <p class="w-32">Status</p>
                            <p>: {{ $record->status }}</p>
                        </div>
                    </div>
                </div>

                 {{-- tambahkan menggunakan space --}}
                <div class="mb-12"></div>

                <!-- Tanda Tangan -->
                <div class="mt-12 text-right pr-12 text-sm">
                    {{-- <p>Surabaya, {{ date('d F Y') }}</p> --}}
                    {{-- <p class="mb-20">Penyidik yang Menangani,</p>
                    <p class="font-bold">{{ $record->penyidik->name }}</p> --}}
                </div>

                <!-- Media Section -->
                <div class="flex gap-8">
                    <!-- Barang Bukti -->
                    <div class="w-1/2">
                        <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">F. BARANG BUKTI</h3>
                        @if ($record->barangBuktis && count($record->barangBuktis) > 0)
                            @foreach ($record->barangBuktis as $barangBukti)
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-4">
                                    <div class="space-y-2 text-sm flex justify-between items-start p-4">
                                        <div class="space-y-2 flex-1">
                                            <div class="flex gap-2">
                                                <p class="w-32">Jumlah</p>
                                                <p>: {{ $barangBukti->jumlah }}</p>
                                            </div>
                                            <div class="flex gap-2">
                                                <p class="w-32">Nama Barang</p>
                                                <p>: {{ $barangBukti->nama_barang }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-4 text-center text-gray-500">
                                Tidak ada barang bukti yang dilampirkan
                            </div>
                        @endif
                    </div>

                    <!-- Dokumen Pendukung -->
                    <div class="w-1/2">
                        <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">G. DOKUMEN PENDUKUNG</h3>
                        @if ($record->media && count($record->media) > 0)
                            @foreach ($record->media as $media)
                                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden mb-4">
                                    <div class="space-y-2 text-sm flex justify-start items-center p-4">
                                        <p class="w-full">{{ basename($media) }}</p>
                                        <a href="{{ Storage::url($media) }}" 
                                           target="_blank"
                                           class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-semibold rounded-lg hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all shadow-sm">
                                            <svg xmlns="http://www.w3.org/2000/svg" 
                                                 class="w-5 h-5 mr-2" 
                                                 fill="none" 
                                                 viewBox="0 0 24 24" 
                                                 stroke="currentColor">
                                                    <path stroke-linecap="round" 
                                                          stroke-linejoin="round" 
                                                          stroke-width="2" 
                                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" 
                                                          stroke-linejoin="round" 
                                                          stroke-width="2" 
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            <span>Lihat</span>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="p-4 text-center text-gray-500">
                                Tidak ada media yang dilampirkan
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>