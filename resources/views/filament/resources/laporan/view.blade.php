@php
    // Data wilayah pelapor
    $provinsi = $record->pelapors ? (app('wilayah')->getProvinsi()[$record->pelapors->province_id] ?? '-') : '-';
    $kabupaten = $record->pelapors ? (app('wilayah')->getKabupaten($record->pelapors->province_id)[$record->pelapors->city_id] ?? '-') : '-';
    $kecamatan = $record->pelapors ? (app('wilayah')->getKecamatan($record->pelapors->city_id)[$record->pelapors->district_id] ?? '-') : '-';
    $kelurahan = $record->pelapors ? (app('wilayah')->getKelurahan($record->pelapors->district_id)[$record->pelapors->subdistrict_id] ?? '-') : '-';

    // Data wilayah pelapor kedua
    $provinsiKedua = $record->pelapors ? (app('wilayah')->getProvinsi()[$record->pelapors->province_id_2] ?? '-') : '-';
    $kabupatenKedua = $record->pelapors ? (app('wilayah')->getKabupaten($record->pelapors->province_id_2)[$record->pelapors->city_id_2] ?? '-') : '-';
    $kecamatanKedua = $record->pelapors ? (app('wilayah')->getKecamatan($record->pelapors->city_id_2)[$record->pelapors->district_id_2] ?? '-') : '-';
    $kelurahanKedua = $record->pelapors ? (app('wilayah')->getKelurahan($record->pelapors->district_id_2)[$record->pelapors->subdistrict_id_2] ?? '-') : '-';

    // Data wilayah korban
    $provinsiKorban = $record->korbans ? (app('wilayah')->getProvinsi()[$record->korbans->province_id] ?? '-') : '-';
    $kabupatenKorban = $record->korbans ? (app('wilayah')->getKabupaten($record->korbans->province_id)[$record->korbans->city_id] ?? '-') : '-';
    $kecamatanKorban = $record->korbans ? (app('wilayah')->getKecamatan($record->korbans->city_id)[$record->korbans->district_id] ?? '-') : '-';
    $kelurahanKorban = $record->korbans ? (app('wilayah')->getKelurahan($record->korbans->district_id)[$record->korbans->subdistrict_id] ?? '-') : '-';

    // Data wilayah korban kedua
    $provinsiKorbanKedua = $record->korbans ? (app('wilayah')->getProvinsi()[$record->korbans->province_id_2] ?? '-') : '-';
    $kabupatenKorbanKedua = $record->korbans ? (app('wilayah')->getKabupaten($record->korbans->province_id_2)[$record->korbans->city_id_2] ?? '-') : '-';
    $kecamatanKorbanKedua = $record->korbans ? (app('wilayah')->getKecamatan($record->korbans->city_id_2)[$record->korbans->district_id_2] ?? '-') : '-';
    $kelurahanKorbanKedua = $record->korbans ? (app('wilayah')->getKelurahan($record->korbans->district_id_2)[$record->korbans->subdistrict_id_2] ?? '-') : '-';

    // Data wilayah terlapor
    $provinsiTerlapor = $record->terlapors ? (app('wilayah')->getProvinsi()[$record->terlapors->province_id] ?? '-') : '-';
    $kabupatenTerlapor = $record->terlapors ? (app('wilayah')->getKabupaten($record->terlapors->province_id)[$record->terlapors->city_id] ?? '-') : '-';
    $kecamatanTerlapor = $record->terlapors ? (app('wilayah')->getKecamatan($record->terlapors->city_id)[$record->terlapors->district_id] ?? '-') : '-';
    $kelurahanTerlapor = $record->terlapors ? (app('wilayah')->getKelurahan($record->terlapors->district_id)[$record->terlapors->subdistrict_id] ?? '-') : '-';

    // Data wilayah terlapor kedua
    $provinsiTerlaporKedua = $record->terlapors ? (app('wilayah')->getProvinsi()[$record->terlapors->province_id_2] ?? '-') : '-';
    $kabupatenTerlaporKedua = $record->terlapors ? (app('wilayah')->getKabupaten($record->terlapors->province_id_2)[$record->terlapors->city_id_2] ?? '-') : '-';
    $kecamatanTerlaporKedua = $record->terlapors ? (app('wilayah')->getKecamatan($record->terlapors->city_id_2)[$record->terlapors->district_id_2] ?? '-') : '-';
    $kelurahanTerlaporKedua = $record->terlapors ? (app('wilayah')->getKelurahan($record->terlapors->district_id_2)[$record->terlapors->subdistrict_id_2] ?? '-') : '-';

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
                    <p class="text-base mt-2">Jl. Ahmad Yani No. 116, Surabaya - 60231</p>
                </div>
                <!-- Garis Pembatas -->
                <div class="border-b-2 border-black my-4"></div>
                <!-- Nomor Surat -->
                <p class="text-xl font-semibold mt-4">{{ $record instanceof \App\Models\LaporanInformasi ? 'INFORMASI / SURAT MASYARAKAT (DUMAS)' : 'LAPORAN POLISI' }}</p>
            </div>

            <!-- Konten Utama -->
            <div class="space-y-6">
                <!-- Pelapor dan Korban dalam satu baris -->
                <div class="flex gap-8">
                    <!-- Informasi Pelapor -->
                    <div class="w-1/2">
                        <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">A. IDENTITAS PELAPOR</h3>
                        <div class="space-y-2 pl-4 text-sm">
                            <div class="flex items-start">
                                <div class="w-32">Nama</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->pelapors?->nama ?? '-' }}</div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-32">No Identitas</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->pelapors->identity_no ?? '-' }}</div>
                            </div>
                            {{-- Kewarganegaraan --}}
                            <div class="flex items-start">
                                <div class="w-32">Kewarganegaraan</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">@php
                                    $countryCode = $record->pelapors?->kewarganegaraan;
                                    if ($countryCode) {
                                        $countries = \PeterColes\Countries\CountriesFacade::lookup(null, 'id')->toArray();
                                        echo array_flip($countries)[$countryCode] ?? $countryCode;
                                    } else {
                                        echo '-';
                                    }
                                @endphp</div>
                            </div>
                            {{-- Jenis Kelamin --}}
                            <div class="flex items-start">
                                <div class="w-32">Jenis Kelamin</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->pelapors->jenis_kelamin ?? '-' }}</div>
                            </div>
                            {{-- Tempat Tanggal Lahir --}}
                            <div class="flex items-start">
                                <div class="w-32">Tempat/Tgl Lahir</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->pelapors->tempat_lahir ?? '-' }}, {{ $record->pelapors?->tanggal_lahir ? date('d F Y', strtotime($record->pelapors?->tanggal_lahir)) : '-' }}</div>
                            </div>
                            {{-- Agama --}}
                            <div class="flex items-start">
                                <div class="w-32">Agama</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->pelapors->agama ?? '-' }}</div>
                            </div>
                            {{-- Alamat --}}
                            <div class="flex items-start">
                                <div class="w-32">Alamat</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 break-words">{{ $record->pelapors?->alamat ?? '-' }}, 
                                    {{ ucwords(strtolower($kelurahan)) ?? '-' }}, 
                                    {{ ucwords(strtolower($kecamatan)) ?? '-' }}, 
                                    {{ ucwords(strtolower($kabupaten)) ?? '-' }}, 
                                    {{ ucwords(strtolower($provinsi)) ?? '-' }}</div>
                            </div>
                            {{-- Alamat Kedua --}}
                            @if($record->pelapors?->alamat_2)
                            <div class="flex items-start">
                                <div class="w-32">Alamat Kedua</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 break-words">{{ $record->pelapors?->alamat_2 }}, 
                                    {{ ucwords(strtolower($kelurahanKedua)) ?? '-' }}, 
                                    {{ ucwords(strtolower($kecamatanKedua)) ?? '-' }}, 
                                    {{ ucwords(strtolower($kabupatenKedua)) ?? '-' }}, 
                                    {{ ucwords(strtolower($provinsiKedua)) ?? '-' }}</div>
                            </div>
                            @endif
                            {{-- Kontak --}}
                            <div class="flex items-start">
                                <div class="w-32">Kontak</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->pelapors->kontak ?? '-' }}</div>
                            </div>
                            {{-- Kontak Kedua --}}
                            @if($record->pelapors?->kontak_2)
                            <div class="flex items-start">
                                <div class="w-32">Kontak Kedua</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->pelapors?->kontak_2 ?? '-' }}</div>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Informasi Korban -->
                    <div class="w-1/2">
                        <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">B. IDENTITAS KORBAN</h3>
                        <div class="space-y-2 pl-4 text-sm">
                            <div class="flex items-start">
                                <div class="w-32">Nama</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->korbans->nama ?? '-' }}</div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-32">No Identitas</div>
                                <div class="w-4">:</div>
                                <p>{{ $record->korbans->identity_no ?? '-' }}</p>
                            </div>
                            <div class="flex items-start">
                                <div class="w-32">Kewarganegaraan</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">@php
                                    $countryCode = $record->korbans?->kewarganegaraan;
                                    if ($countryCode) {
                                        $countries = \PeterColes\Countries\CountriesFacade::lookup(null, 'id')->toArray();
                                        echo array_flip($countries)[$countryCode] ?? $countryCode;
                                    } else {
                                        echo '-';
                                    }
                                @endphp</div>
                            </div>
                            <div class="flex items-start">
                                <div class="w-32">Jenis Kelamin</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->korbans->jenis_kelamin ?? '-' }}</div>
                            </div>
                            {{-- Tempat Tanggal Lahir --}}
                            <div class="flex items-start">
                                <div class="w-32">Tempat/Tgl Lahir</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->korbans->tempat_lahir ?? '-' }}, {{ $record->korbans?->tanggal_lahir ? date('d F Y', strtotime($record->korbans?->tanggal_lahir)) : '-' }}</div>
                            </div>
                            {{-- Agama --}}
                            <div class="flex items-start">
                                <div class="w-32">Agama</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->korbans->agama ?? '-' }}</div>
                            </div>
                            {{-- Alamat --}}
                            <div class="flex items-start">
                                <div class="w-32">Alamat</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 break-words">{{ $record->korbans->alamat ?? '-' }}, 
                                    {{ ucwords(strtolower($kelurahanKorban)) ?? '-' }}, 
                                    {{ ucwords(strtolower($kecamatanKorban)) ?? '-' }}, 
                                    {{ ucwords(strtolower($kabupatenKorban)) ?? '-' }}, 
                                    {{ ucwords(strtolower($provinsiKorban)) ?? '-' }}</div>
                            </div>
                            {{-- Alamat Kedua --}}
                            @if($record->korbans?->alamat_2)
                            <div class="flex items-start">
                                <div class="w-32">Alamat Kedua</div>
                                <div class="w-4">:</div>
                                <div class="flex-1 break-words">{{ $record->korbans?->alamat_2 ?? '-' }}, 
                                    {{ ucwords(strtolower($kelurahanKorbanKedua)) ?? '-' }}, 
                                    {{ ucwords(strtolower($kecamatanKorbanKedua)) ?? '-' }}, 
                                    {{ ucwords(strtolower($kabupatenKorbanKedua)) ?? '-' }}, 
                                    {{ ucwords(strtolower($provinsiKorbanKedua)) ?? '-' }}</div>
                            </div>
                            @endif
                            <div class="flex items-start">
                                <div class="w-32">Kontak</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->korbans->kontak ?? '-' }}</div>
                            </div>
                            @if($record->korbans?->kontak_2)
                            <div class="flex items-start">
                                <div class="w-32">Kontak Kedua</div>
                                <div class="w-4">:</div>
                                <div class="flex-1">{{ $record->korbans?->kontak_2 ?? '-' }}</div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Informasi Terlapor (Full Width) -->
                <div class="mb-6">
                    <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">C. IDENTITAS TERLAPOR</h3>
                    <div class="space-y-2 pl-4 text-sm">
                        <div class="flex items-start">
                            <div class="w-32">Nama</div>
                            <div class="w-4">:</div>
                            <div class="flex-1">{{ $record->terlapors->nama ?? '-' }}</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32">No Identitas</div>
                            <div class="w-4">:</div>
                            <div class="flex-1">{{ $record->terlapors->identity_no ?? '-' }}</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32">Kewarganegaraan</div>
                            <div class="w-4">:</div>
                            <div class="flex-1">@php
                                $countryCode = $record->terlapors?->kewarganegaraan;
                                if ($countryCode) {
                                    $countries = \PeterColes\Countries\CountriesFacade::lookup(null, 'id')->toArray();
                                    echo array_flip($countries)[$countryCode] ?? $countryCode;
                                } else {
                                    echo '-';
                                }
                            @endphp</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32">Jenis Kelamin</div>
                            <div class="w-4">:</div>
                            <div class="flex-1">{{ $record->terlapors->jenis_kelamin ?? '-' }}</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32">Tempat/Tgl Lahir</div>
                            <div class="w-4">:</div>
                            <div class="flex-1">{{ $record->terlapors?->tempat_lahir ?? '-' }}, {{ $record->terlapors?->tanggal_lahir ? date('d F Y', strtotime($record->terlapors?->tanggal_lahir)) : '-' }}</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32">Agama</div>
                            <div class="w-4">:</div>
                            <div class="flex-1">{{ $record->terlapors->agama ?? '-' }}</div>
                        </div>
                        <div class="flex items-start">
                            <div class="w-32">Alamat</div>
                            <div class="w-4">:</div>
                            <div class="flex-1 break-words">{{ $record->terlapors->alamat ?? '-' }}, 
                                {{ ucwords(strtolower($kelurahanTerlapor)) ?? '-' }}, 
                                {{ ucwords(strtolower($kecamatanTerlapor)) ?? '-' }}, 
                                {{ ucwords(strtolower($kabupatenTerlapor)) ?? '-' }}, 
                                {{ ucwords(strtolower($provinsiTerlapor)) ?? '-' }}</div>
                        </div>
                        @if($record->terlapors?->alamat_2)
                        <div class="flex items-start">
                            <div class="w-32">Alamat Kedua</div>
                            <div class="w-4">:</div>
                            <div class="flex-1 break-words">{{ $record->terlapors?->alamat_2 ?? '-' }}, 
                                {{ ucwords(strtolower($kelurahanTerlaporKedua)) ?? '-' }}, 
                                {{ ucwords(strtolower($kecamatanTerlaporKedua)) ?? '-' }}, 
                                {{ ucwords(strtolower($kabupatenTerlaporKedua)) ?? '-' }}, 
                                {{ ucwords(strtolower($provinsiTerlaporKedua)) ?? '-' }}</div>
                        </div>
                        @endif
                        <div class="flex items-start">
                            <div class="w-32">Kontak</div>
                            <div class="w-4">:</div>
                            <div class="flex-1">{{ $record->terlapors->kontak ?? '-' }}</div>
                        </div>
                        @if($record->terlapors?->kontak_2)
                        <div class="flex items-start">
                            <div class="w-32">Kontak Kedua</div>
                            <div class="w-4">:</div>
                            <div class="flex-1">{{ $record->terlapors?->kontak_2 ?? '-' }}</div>
                        </div>
                        @endif
                        <div class="flex items-start">
                            <div class="w-32">Data Tambahan</div>
                            <div class="w-4">:</div>
                            <div class="flex-1">{{ $record->terlapors?->data_tambahan ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="">
                    <!-- Informasi Perkara -->
                    <div>
                        <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">D. INFORMASI PERKARA</h3>
                        <div class="flex gap-8">
                            <div class="w-1/2">
                                <div class="space-y-2 pl-4 text-sm">
                                    <div class="flex items-start">
                                        <div class="w-32">TKP</div>
                                        <div class="w-4">:</div>
                                        <div class="flex-1">{{ $record->tkp }}, {{ ucwords(strtolower($kelurahanTkp)) }}, {{ ucwords(strtolower($kecamatanTkp)) }}, {{ ucwords(strtolower($kabupatenTkp)) }}, {{ ucwords(strtolower($provinsiTkp)) }}</div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-32">Tgl. Lapor</div>
                                        <div class="w-4">:</div>
                                        <div class="flex-1">{{ date('d F Y', strtotime($record->tanggal_lapor)) }}</div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-32">Tgl. Kejadian</div>
                                        <div class="w-4">:</div>
                                        <div class="flex-1">{{ date('d F Y', strtotime($record->tanggal_kejadian)) }}</div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-32">Perkara</div>
                                        <div class="w-4">:</div>
                                        <div class="flex-1">{{ $record->perkara }}</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Uraian Peristiwa -->
                            <div class="w-1/2">
                                <div class="space-y-2 pl-4 text-sm">
                                    <div class="flex items-start">
                                        <div class="w-32">Uraian Peristiwa</div>
                                        <div class="w-4">:</div>
                                        <div class="flex-1">{{ $record->uraian_peristiwa }}</div>
                                    </div>
                                    <div class="flex items-start">
                                        <div class="w-32">Kerugian</div>
                                        <div class="w-4">:</div>
                                        <div class="flex-1">{{ $record->kerugian }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="flex gap-8">
                    <!-- Barang Bukti -->
                    <div class="w-1/2">
                        <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">E. BARANG BUKTI</h3>
                        @if ($record->barangBuktis && count($record->barangBuktis) > 0)
                            <div class="pl-8">
                                <ol class="list-decimal space-y-2">
                                    @foreach ($record->barangBuktis as $index => $barangBukti)
                                        <li class="text-sm">
                                            <div class="flex">
                                                <div class="w-8">•</div>
                                                <div class="flex-1">{{ $barangBukti->jumlah }} {{ $barangBukti->satuan }} {{ $barangBukti->nama_barang }}</div>
                                            </div>
                                        </li>
                                    @endforeach
                                </ol>
                            </div>
                        @else
                            <div class="p-4 text-center text-gray-500">
                                Tidak ada barang bukti yang dilampirkan
                            </div>
                        @endif
                    </div>
                    <div class="w-1/2">
                    <!-- Status Laporan -->
                    <div class="mb-6">
                        <h3 class="text-base font-semibold bg-gray-100 dark:bg-gray-700 p-2 mb-3">F. YANG MENANGANI</h3>
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
                    </div>
                </div>
                <!-- Dokumen Pendukung -->
                    <div class="mb-6">
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
</x-filament-panels::page>