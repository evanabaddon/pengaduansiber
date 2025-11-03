<x-filament::page>
    <div class="bg-white rounded-xl shadow p-6">
        {{-- HEADER --}}
        <div class="flex items-center gap-4 pb-2" style="margin-bottom: 20px;">
            <div class="flex-shrink-0">
                <img 
                    src="{{ asset('images/logo-siber-polri.png') }}" 
                    alt="Logo Siber" 
                    class="w-20 h-20 object-contain"
                >
            </div>
            <div class="flex flex-col leading-tight">
                <h3 class="text-md font-semibold uppercase tracking-wide">
                    KEPOLISIAN NEGARA REPUBLIK INDONESIA
                </h3>
                <h3 class="text-md font-semibold uppercase">
                    DAERAH JAWA TIMUR
                </h3>
                <h3 class="text-md font-semibold uppercase">
                    DIREKTORAT RESERSE SIBER
                </h3>
            </div>
        </div>

        {{-- Judul Tengah --}}
        <div class="text-center pb-3 mb-8 mt-10">
            <h5 class="mt-2 text-black font-semibold text-base uppercase tracking-wide mb-10 underline">
                DATA PERSONEL DITRESSIBER
            </h5>
        </div>

        {{-- DATA UTAMA: TABEL 3 KOLOM --}}
        <div class="overflow-x-auto mb-8 mt-8">
            <table class="min-w-full border-collapse w-full">
                <tr class="align-top">
                    {{-- KOLOM 1: FOTO + STATUS --}}
                    <td class="w-[25%] text-center p-4 border-r border-gray-200">
                        <div class="flex flex-col items-center">
                            <div class="border rounded-lg overflow-hidden w-40 h-48 bg-gray-100 flex items-center justify-center">
                                @if(!empty($record->photo))
                                    <img src="{{ asset('storage/' . $record->photo) }}" alt="Foto {{ $record->nama }}" class="w-full h-full object-cover">
                                @else
                                    <span class="text-gray-500 text-sm">Tidak ada foto</span>
                                @endif
                            </div>
                            <div class="mt-4">
                                <span class="px-3 py-1  text-black text-xs font-semibold">
                                    STATUS PERSONEL : {{ strtoupper($record->status_personil ?? 'AKTIF') }}
                                </span>
                            </div>
                        </div>
                    </td>

                    {{-- KOLOM 2: DATA DIRI --}}
                    <td class="w-[40%] px-6 border-r border-gray-200">
                        <table class="text-sm border-collapse w-full">
                            <tbody>
                                @foreach([
                                    'Nama/Usia' => $record->nama . 
                                        (!empty($record->tanggal_lahir) 
                                            ? '/' . \Carbon\Carbon::parse($record->tanggal_lahir)->age . ' TAHUN' 
                                            : ''
                                        ),
                                    'Pangkat/NRP' => trim(($record->pangkatTerakhir ?? '-') . 
                                        (!empty($record->nrp) ? '/' . $record->nrp : '')
                                    ),
                                    'Jabatan/TMT' => trim(
                                        ($record->jabatanTerakhir ?? '-') .
                                        (
                                            $record->tmtJabatanTerakhir
                                                ? '/' . \Carbon\Carbon::parse($record->tmtJabatanTerakhir)->translatedFormat('d-m-Y')
                                                : ''
                                        )
                                    ),
                                    'Lama Jabatan' => $record->lamaJabatanTerakhir,
                                    'Tempat, Tanggal Lahir' => trim(($record->tempat_lahir ?? '') . 
                                        ($record->tanggal_lahir ? ', ' . \Carbon\Carbon::parse($record->tanggal_lahir)->translatedFormat('d F Y') : '')
                                    ),
                                    'Golongan Darah' => $record->golongan_darah,
                                    'Kepersertaan BPJS' => !empty($record->bpjs) ? 'Ada' : 'Tidak Ada',
                                    'Agama' => $record->agama,
                                    'Suku' => $record->suku,
                                    'Alamat' => trim(
                                        ($record->alamat ?? '-') . 
                                        (!empty($record->village_id) ? ', ' . ucwords(strtolower(app('wilayah')->getKelurahan($record->district_id)[$record->village_id] ?? '-')) : '') .
                                        (!empty($record->district_id) ? ', ' . ucwords(strtolower(app('wilayah')->getKecamatan($record->city_id)[$record->district_id] ?? '-')) : '') .
                                        (!empty($record->city_id) ? ', ' . ucwords(strtolower(app('wilayah')->getKabupaten($record->province_id)[$record->city_id] ?? '-')) : '') .
                                        (!empty($record->province_id) ? ', ' . ucwords(strtolower(app('wilayah')->getProvinsi()[$record->province_id] ?? '-')) : '')
                                    ),
                                    'Telp.' => $record->telp,
                                ] as $label => $value)
                                    <tr>
                                        <td class="w-40 font-medium align-top py-1" style="vertical-align: top">{{ $label }}</td>
                                        <td class="w-1 align-top py-1">:</td>
                                        <td class="align-top py-1">{{ $value ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </td>

                    {{-- KOLOM 3: DATA KELUARGA --}}
                    <td class="w-[35%] px-6">
                        {{-- Pasangan --}}
                        @if(!empty($record->pasangan))
                            <table class="text-sm border-collapse w-full mb-4">
                                <tbody>
                                    <tr>
                                        <td class="w-40 font-medium py-1">Nama (Istri/Suami)</td>
                                        <td>:</td>
                                        <td>
                                            {{ $record->pasangan['nama'] ?? '-' }}
                                            @if(!empty($record->pasangan['tanggal_lahir']))
                                            /{{ \Carbon\Carbon::parse($record->pasangan['tanggal_lahir'])->age }} TAHUN
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="font-medium py-1">Tempat, Tanggal Lahir</td>
                                        <td>:</td>
                                        <td>
                                            {{ trim(($record->pasangan['tempat_lahir'] ?? '') . 
                                                (!empty($record->pasangan['tanggal_lahir']) ? ', ' . \Carbon\Carbon::parse($record->pasangan['tanggal_lahir'])->translatedFormat('d F Y') : '')
                                            ) ?: '-' }}
                                        </td>
                                    </tr>
                                    <tr><td class="font-medium py-1">Golongan Darah</td><td>:</td><td>{{ $record->pasangan['golongan_darah'] ?? '-' }}</td></tr>
                                    <tr>
                                        <td class="font-medium py-1">Kartu Pengenal Istri</td>
                                        <td>:</td>
                                        <td>{{ !empty($record->pasangan['kartu_ktp']) ? 'Ada' : 'Tidak Ada' }}</td>
                                    </tr>
                                    <tr>
                                        <td class="font-medium py-1">Kepersertaan BPJS</td>
                                        <td>:</td>
                                        <td>{{ !empty($record->pasangan['bpjs']) ? 'Ada' : 'Tidak Ada' }}</td>
                                    </tr>
                                    <tr><td class="font-medium py-1">Telp.</td><td>:</td><td>{{ $record->pasangan['telp'] ?? '-' }}</td></tr>
                                    {{-- Jumlah Anak --}}
                                    @php
                                        $anak = $record->keluarga ?? [];
                                        if(is_string($anak)) {
                                            $anak = json_decode($anak, true) ?? [];
                                        }
                                    @endphp
                                    <tr>
                                        <td class="font-medium py-1">Jumlah Anak</td>
                                        <td>:</td>
                                        <td>{{ count($anak) }}</td>
                                    </tr>

                                    {{-- Nama & Usia Anak --}}
                                    <tr>
                                        <td class="font-medium py-1">Nama Anak/Usia</td>
                                        <td>:</td>
                                        <td>
                                            @if(count($anak) > 0)
                                                @foreach($anak as $k)
                                                    {{ $k['nama'] ?? '-' }}
                                                    @if(!empty($k['tanggal_lahir']))
                                                        / {{ \Carbon\Carbon::parse($k['tanggal_lahir'])->age }} TAHUN
                                                    @endif
                                                    @if(!$loop->last)<br>@endif
                                                @endforeach
                                            @else
                                                -
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <p class="text-sm text-gray-500 mb-4">Belum ada data pasangan.</p>
                        @endif
                    </td>
                </tr>
            </table>
        </div>

        <table class="w-full border-collapse border text-sm mt-10">
            <thead>
                <tr>
                    <th class="w-1/3 border bg-gray-100 dark:bg-gray-700 font-semibold p-2" style="background-color:#1b234c;color:white;">I. PENDIDIKAN KEPOLISIAN</th>
                    <th class="w-1/3 border bg-gray-100 dark:bg-gray-700  font-semibold p-2" style="background-color:#1b234c;color:white;">II. PENDIDIKAN UMUM</th>
                    <th class="w-1/3 border bg-gray-100 dark:bg-gray-700  font-semibold p-2" style="background-color:#1b234c;color:white;">III. RIWAYAT PANGKAT</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    {{-- Kolom 1: Pendidikan Kepolisian --}}
                    <td class="align-top border p-1">
                        <table class="w-full border-collapse border text-sm">
                            <thead class="bg-gray-100 font-semibold">
                                <tr>
                                    {{-- <th class="p-2 border">No</th> --}}
                                    <th class="p-2 border">Tingkat</th>
                                    <th class="p-2 border">Tahun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $pendidikanPolri = collect($record->pendidikan_polri ?? [])
                                        ->sortByDesc(fn($item) => $item['tahun'] ?? 0)
                                        ->values(); // reset index
                                @endphp

                                @forelse($pendidikanPolri as $index => $pp) {{-- <-- gunakan $pendidikanPolri --}}
                                    <tr>
                                        {{-- <td class="p-2 border">{{ $index + 1 }}</td> --}}
                                        <td class="p-2 border">{{ $pp['tingkat'] ?? '-' }}</td>
                                        <td class="p-2 border">{{ $pp['tahun'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-2 border text-center text-gray-500">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </td>
        
                    {{-- Kolom 2: Pendidikan Umum --}}
                    <td class="align-top border p-1">
                        <table class="w-full border-collapse border text-sm">
                            <thead class="bg-gray-100 font-semibold">
                                <tr>
                                    {{-- <th class="p-2 border">No</th> --}}
                                    <th class="p-2 border">Tingkat</th>
                                    <th class="p-2 border">Institusi</th>
                                    <th class="p-2 border">Tahun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $pendidikanUmum = collect($record->pendidikan_umum ?? [])
                                        ->sortByDesc(fn($item) => $item['tahun'] ?? 0)
                                        ->values(); // reset index
                                @endphp

                                @forelse($pendidikanUmum as $index => $pu)
                                    <tr>
                                        {{-- <td class="p-2 border">{{ $index + 1 }}</td> --}}
                                        <td class="p-2 border">{{ $pu['tingkat'] ?? '-' }}</td>
                                        <td class="p-2 border">{{ $pu['nama_institusi'] ?? '-' }}</td>
                                        <td class="p-2 border">{{ $pu['tahun'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-2 border text-center text-gray-500">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </td>
        
                    {{-- Kolom 3: Riwayat Pangkat --}}
                    <td class="align-top border p-1">
                        <table class="w-full border-collapse border text-sm">
                            <thead class="bg-gray-100 font-semibold">
                                <tr>
                                    {{-- <th class="p-2 border">No</th> --}}
                                    <th class="p-2 border">Pangkat</th>
                                    <th class="p-2 border">TMT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $riwayatPangkat = collect($record->riwayat_pangkat ?? [])
                                        ->sortByDesc(fn($item) => $item['tmt'] ?? '0000-00-00') // urut dari TMT terbaru
                                        ->values(); // reset index
                                @endphp

                                @forelse($riwayatPangkat as $index => $rp)
                                    <tr>
                                        {{-- <td class="p-2 border">{{ $index + 1 }}</td> --}}
                                        <td class="p-2 border">{{ $record->getPangkatLabel($rp['pangkat']) }}</td>
                                        <td class="p-2 border">
                                            {{ !empty($rp['tmt']) ? \Carbon\Carbon::parse($rp['tmt'])->format('d-m-Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-2 border text-center text-gray-500">Belum ada data</td>
                                    </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        
        {{-- IV. Riwayat Jabatan --}}
        <table class="w-full border-collapse border text-sm mt-6">
            <thead>
                <tr>
                    <th colspan="3" class=" bg-gray-100 dark:bg-gray-700 font-semibold p-2" style="background-color:#1b234c;color:white;">IV. RIWAYAT JABATAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="align-top border p-1">
                        <table class="w-full border-collapse border text-sm">
                            <thead class="bg-gray-100 font-semibold">
                                <tr>
                                    {{-- <th class="p-2 border">No</th> --}}
                                    <th class="p-2 border">Jabatan</th>
                                    <th class="p-2 border">TMT</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $riwayatJabatan = collect($record->riwayat_jabatan ?? [])
                                        ->sortByDesc(fn($item) => $item['tmt'] ?? '0000-00-00')
                                        ->values();
                                @endphp
                                @forelse($riwayatJabatan as $index => $rj)
                                    <tr>
                                        {{-- <td class="p-2 border">{{ $index + 1 }}</td> --}}
                                        <td class="p-2 border">{{ $rj['jabatan'] ?? '-' }}</td>
                                        <td class="p-2 border">
                                            {{ !empty($rj['tmt']) ? \Carbon\Carbon::parse($rj['tmt'])->format('d-m-Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-2 border text-center text-gray-500">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- V. Pendidikan Pengembangan & Pelatihan --}}
        <table class="w-full border-collapse border text-sm mt-6">
            <thead>
                <tr>
                    <th colspan="3" class=" bg-gray-100 dark:bg-gray-700 font-semibold p-2" style="background-color:#1b234c;color:white;">V. PENDIDIKAN PENGEMBANGAN & PELATIHAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="align-top border p-1">
                        <table class="w-full border-collapse border text-sm">
                            <thead class="bg-gray-100 font-semibold">
                                <tr>
                                    {{-- <th class="p-2 border">No</th> --}}
                                    <th class="p-2 border">Dikbang</th>
                                    <th class="p-2 border">Tahun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $pendidikanPelatihan = collect($record->dikbang_pelatihan ?? [])
                                        ->sortByDesc(fn($item) => $item['tmt'] ?? 0)
                                        ->values();
                                @endphp
                                @forelse($pendidikanPelatihan as $index => $pp)
                                    <tr>
                                        {{-- <td class="p-2 border">{{ $index + 1 }}</td> --}}
                                        <td class="p-2 border">{{ $pp['nama_pelatihan'] ?? '-' }}</td>
                                        <td class="p-2 border">
                                            {{ !empty($pp['tmt']) ? \Carbon\Carbon::parse($pp['tmt'])->format('d-m-Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-2 border text-center text-gray-500">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- VI. Tanda Kehormatan --}}
        <table class="w-full border-collapse border text-sm mt-6">
            <thead>
                <tr>
                    <th colspan="3" class=" bg-gray-100 dark:bg-gray-700 font-semibold p-2" style="background-color:#1b234c;color:white;">VI. TANDA KEHORMATAN</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="align-top border p-1">
                        <table class="w-full border-collapse border text-sm">
                            <thead class="bg-gray-100 font-semibold">
                                <tr>
                                    {{-- <th class="p-2 border">No</th> --}}
                                    <th class="p-2 border">Nama Tanda Kehormatan</th>
                                    <th class="p-2 border">Tahun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $tandaKehormatan = collect($record->tanda_kehormatan ?? [])->values();
                                @endphp
                                @forelse($tandaKehormatan as $index => $tk)
                                    <tr>
                                        {{-- <td class="p-2 border">{{ $index + 1 }}</td> --}}
                                        <td class="p-2 border">{{ $tk['nama_tanda'] ?? '-' }}</td>
                                        <td class="p-2 border">
                                            {{ !empty($tk['tmt']) ? \Carbon\Carbon::parse($tk['tmt'])->format('d-m-Y') : '-' }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-2 border text-center text-gray-500">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- VII. Kemampuan Bahasa --}}
        <table class="w-full border-collapse border text-sm mt-6">
            <thead>
                <tr>
                    <th colspan="3" class=" bg-gray-100 dark:bg-gray-700 font-semibold p-2" style="background-color:#1b234c;color:white;">VII. KEMAMPUAN BAHASA</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="align-top border p-1">
                        <table class="w-full border-collapse border text-sm">
                            <thead class="bg-gray-100 font-semibold">
                                <tr>
                                    {{-- <th class="p-2 border">No</th> --}}
                                    <th class="p-2 border">Bahasa</th>
                                    <th class="p-2 border">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $bahasa = collect($record->kemampuan_bahasa ?? [])->values();
                                @endphp
                                @forelse($bahasa as $index => $b)
                                    <tr>
                                        {{-- <td class="p-2 border">{{ $index + 1 }}</td> --}}
                                        <td class="p-2 border">{{ $b['bahasa'] ?? '-' }}</td>
                                        <td class="p-2 border">{{ $b['status'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-2 border text-center text-gray-500">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- VIII. Penugasan Luar Negeri --}}
        <table class="w-full border-collapse border text-sm mt-6 mb-6">
            <thead>
                <tr>
                    <th colspan="3" class=" bg-gray-100 dark:bg-gray-700 font-semibold p-2" style="background-color:#1b234c;color:white;">VIII. PENUGASAN LUAR NEGERI</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="align-top border p-1">
                        <table class="w-full border-collapse border text-sm">
                            <thead class="bg-gray-100 font-semibold">
                                <tr>
                                    {{-- <th class="p-2 border">No</th> --}}
                                    <th class="p-2 border">Penugasan</th>
                                    <th class="p-2 border">Negara</th>
                                    <th class="p-2 border">Tahun</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $penugasanLN = collect($record->penugasan_ln ?? [])->values();
                                @endphp
                                @forelse($penugasanLN as $index => $pln)
                                    <tr>
                                        {{-- <td class="p-2 border">{{ $index + 1 }}</td> --}}
                                        <td class="p-2 border">{{ $pln['penugasan'] ?? '-' }}</td>
                                        <td class="p-2 border">{{ $pln['lokasi'] ?? '-' }}</td>
                                        <td class="p-2 border">{{ $pln['tmt'] ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-2 border text-center text-gray-500">Belum ada data</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>

    </div>
</x-filament::page>
