@php
    // Data wilayah pelapor
    $provinsi = app('wilayah')->getProvinsi()[$record->pelapors->province_id] ?? '-';
    $kabupaten = app('wilayah')->getKabupaten($record->pelapors->province_id)[$record->pelapors->city_id] ?? '-';
    $kecamatan = app('wilayah')->getKecamatan($record->pelapors->city_id)[$record->pelapors->district_id] ?? '-';
    $kelurahan = app('wilayah')->getKelurahan($record->pelapors->district_id)[$record->pelapors->subdistrict_id] ?? '-';

    // Data wilayah korban
    $provinsiKorban = app('wilayah')->getProvinsi()[$record->korbans->province_id] ?? '-';
    $kabupatenKorban = app('wilayah')->getKabupaten($record->korbans->province_id)[$record->korbans->city_id] ?? '-';
    $kecamatanKorban = app('wilayah')->getKecamatan($record->korbans->city_id)[$record->korbans->district_id] ?? '-';
    $kelurahanKorban = app('wilayah')->getKelurahan($record->korbans->district_id)[$record->korbans->subdistrict_id] ?? '-';

    // Data wilayah terlapor
    $provinsiTerlapor = app('wilayah')->getProvinsi()[$record->terlapors->province_id] ?? '-';
    $kabupatenTerlapor = app('wilayah')->getKabupaten($record->terlapors->province_id)[$record->terlapors->city_id] ?? '-';
    $kecamatanTerlapor = app('wilayah')->getKecamatan($record->terlapors->city_id)[$record->terlapors->district_id] ?? '-';
    $kelurahanTerlapor = app('wilayah')->getKelurahan($record->terlapors->district_id)[$record->terlapors->subdistrict_id] ?? '-';

    // Data wilayah tkp
    $provinsiTkp = app('wilayah')->getProvinsi()[$record->province_id] ?? '-';
    $kabupatenTkp = app('wilayah')->getKabupaten($record->province_id)[$record->city_id] ?? '-';
    $kecamatanTkp = app('wilayah')->getKecamatan($record->city_id)[$record->district_id] ?? '-';
    $kelurahanTkp = app('wilayah')->getKelurahan($record->district_id)[$record->subdistrict_id] ?? '-';
@endphp
{{-- <x-filament-panels::page> --}}
    <!-- Wrapper dengan ukuran A4 -->
    <div class="bg-white mx-auto" style="width: 210mm; min-height: 297mm; padding-bottom: 100px; padding-left: 20px; padding-right: 20px; padding-top: 20px;">
        <div class="p-8">
            <!-- Header Surat dengan Logo -->
            <div class="text-center mb-8 border-b-2 pb-4">
                <!-- Logo Siber -->
                <div class="flex justify-center mb-4">
                    <div class="w-[80px]">
                        <img src="{{ asset('images/logo-siber-polri.png') }}" alt="Logo Siber" class="w-full h-auto" style="max-width: 100px;">
                    </div>
                </div>
                <!-- Judul dan Alamat -->
                <div class="text-center">
                    <h2 class="text-lg font-bold mt-1">DIREKTORAT SIBER POLDA JAWA TIMUR</h2>
                    <p class="text-sm mt-2">Jl. Ahmad Yani No. 116, Surabaya - 60235</p>
                    {{-- <p class="text-sm">Telp: 031-xxxxxxx, Email: siber@jatim.polri.go.id</p> --}}
                </div>
                <!-- Garis Pembatas -->
                <div class="border-b-2 border-black my-4"></div>
                <!-- Nomor Surat -->
                <p class="text-lg font-semibold mt-4">LAPORAN POLISI</p>
                <p class="">Nomor: {{ $record->id }}/LP/{{ date('Y') }}</p>
            </div>

            <!-- Konten dalam 2 kolom -->
            <div class="grid grid-cols-2 gap-6">
                <!-- Kolom Kiri -->
                <div>
                    <!-- Informasi Pelapor -->
                    <div class="mb-6">
                        <h3 class="text-base font-semibold bg-gray-100 p-2 mb-3">A. IDENTITAS PELAPOR</h3>
                        <div class="space-y-2 pl-4 text-sm">
                            <div class="flex gap-2">
                                <p class="w-32">NIK/Passport</p>
                                <p>: {{ $record->pelapors->identity_no }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Nama</p>
                                <p>: {{ $record->pelapors->nama }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Tempat Lahir</p>
                                <p>: {{ $record->pelapors->tempat_lahir }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Tanggal Lahir</p>
                                <p>: {{ date('d F Y', strtotime($record->pelapors->tanggal_lahir)) }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Jenis Kelamin</p>
                                <p>: {{ $record->pelapors->jenis_kelamin }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Kontak</p>
                                <p>: {{ $record->pelapors->kontak }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Alamat</p>
                                <p>: {{ $record->pelapors->alamat }}, {{ $kelurahan }}, {{ $kecamatan }}, {{ $kabupaten }}, {{ $provinsi }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Korban -->
                    <div class="mb-6">
                        <h3 class="text-base font-semibold bg-gray-100 p-2 mb-3">B. IDENTITAS KORBAN</h3>
                        <div class="space-y-2 pl-4 text-sm">
                            <div class="flex gap-2">
                                <p class="w-32">NIK/Passport</p>
                                <p>: {{ $record->korbans->identity_no }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Nama</p>
                                <p>: {{ $record->korbans->nama }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Tempat Lahir</p>
                                <p>: {{ $record->korbans->tempat_lahir }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Tanggal Lahir</p>
                                <p>: {{ date('d F Y', strtotime($record->korbans->tanggal_lahir)) }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Jenis Kelamin</p>
                                <p>: {{ $record->korbans->jenis_kelamin }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Kontak</p>
                                <p>: {{ $record->korbans->kontak }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Alamat</p>
                                <p>: {{ $record->korbans->alamat }}, {{ $kelurahanKorban }}, {{ $kecamatanKorban }}, {{ $kabupatenKorban }}, {{ $provinsiKorban }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan -->
                <div>
                    <!-- Informasi Terlapor -->
                    <div class="mb-6">
                        <h3 class="text-base font-semibold bg-gray-100 p-2 mb-3">C. IDENTITAS TERLAPOR</h3>
                        <div class="space-y-2 pl-4 text-sm">
                            <div class="flex gap-2">
                                <p class="w-32">NIK/Passport</p>
                                <p>: {{ $record->terlapors->identity_no }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Nama</p>
                                <p>: {{ $record->terlapors->nama }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Jenis Kelamin</p>
                                <p>: {{ $record->terlapors->jenis_kelamin }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Alamat</p>
                                <p>: {{ $record->terlapors->alamat }}, {{ $kelurahanTerlapor }}, {{ $kecamatanTerlapor }}, {{ $kabupatenTerlapor }}, {{ $provinsiTerlapor }}</p>
                            </div>
                            <div class="flex gap-2">
                                <p class="w-32">Usia</p>
                                <p>: {{ $record->terlapors->usia }} Tahun</p>
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Perkara (Full Width) -->
                    <div class="mb-6">
                        <h3 class="text-base font-semibold bg-gray-100 p-2 mb-3">D. INFORMASI PERKARA</h3>
                        <div class="space-y-2 pl-4 text-sm">
                            <div class="flex gap-2">
                                <p class="w-32">TKP</p>
                                <p>: {{ $record->tkp }}, {{ $kelurahanTkp }}, {{ $kecamatanTkp }}, {{ $kabupatenTkp }}, {{ $provinsiTkp }}</p>
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
                        <h3 class="text-base font-semibold bg-gray-100 p-2 mb-3">E. STATUS LAPORAN</h3>
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

                    
                
                </div>
            </div>
        </div>
    </div>
{{-- </x-filament-panels::page> --}}