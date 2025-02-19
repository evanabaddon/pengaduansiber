<x-filament::widget class="filament-widgets-map col-span-full">
    <x-filament::card class="relative overflow-hidden">
        <div 
            x-data="{ 
                mapData: @js($this->getData()),
                chart: null,
                provinsiMapping: @js([
                    'ACEH' => 'id-ac',
                    'SUMATERA UTARA' => 'id-su',
                    'SUMATERA BARAT' => 'id-sb',
                    'RIAU' => 'id-ri',
                    'JAMBI' => 'id-ja',
                    'SUMATERA SELATAN' => 'id-sl',
                    'BENGKULU' => 'id-be',
                    'LAMPUNG' => 'id-la',
                    'KEPULAUAN BANGKA BELITUNG' => 'id-bb',
                    'KEPULAUAN RIAU' => 'id-kr',
                    'DKI JAKARTA' => 'id-jk',
                    'JAWA BARAT' => 'id-jr',
                    'JAWA TENGAH' => 'id-jt',
                    'DAERAH ISTIMEWA YOGYAKARTA' => 'id-yo',
                    'JAWA TIMUR' => 'id-ji',
                    'BANTEN' => 'id-bt',
                    'BALI' => 'id-ba',
                    'NUSA TENGGARA BARAT' => 'id-nb',
                    'NUSA TENGGARA TIMUR' => 'id-nt',
                    'KALIMANTAN BARAT' => 'id-kb',
                    'KALIMANTAN TENGAH' => 'id-kt',
                    'KALIMANTAN SELATAN' => 'id-ks',
                    'KALIMANTAN TIMUR' => 'id-ki',
                    'KALIMANTAN UTARA' => 'id-ku',
                    'SULAWESI UTARA' => 'id-sw',
                    'SULAWESI TENGAH' => 'id-st',
                    'SULAWESI SELATAN' => 'id-se',
                    'SULAWESI TENGGARA' => 'id-sg',
                    'GORONTALO' => 'id-go',
                    'SULAWESI BARAT' => 'id-sr',
                    'MALUKU' => 'id-ma',
                    'MALUKU UTARA' => 'id-mu',
                    'PAPUA BARAT' => 'id-pb',
                    'P A P U A' => 'id-pp'
                ]),

                init() {
                    this.initializeMap();
                    this.$wire.$on('updateMap', () => {
                        this.updateMapData(@js($this->getData()));
                    });
                },

                updateMapData(newData) {
                    this.mapData = newData;
                    if (this.chart) {
                        const series = this.chart.series[0];
                        const newSeriesData = Object.entries(newData).map(([provinsi, data]) => {
                            const hcKey = this.provinsiMapping[provinsi.toUpperCase()];
                            if (!hcKey) return null;
                            return {
                                'hc-key': hcKey,
                                value: Object.values(data).reduce((a, b) => a + b, 0),
                                LaporanInfo: data.LaporanInfo || 0,
                                LaporanInformasi: data.LaporanInformasi || 0,
                                LaporanPolisi: data.LaporanPolisi || 0,
                                Pengaduan: data.Pengaduan || 0,
                                name: provinsi
                            };
                        }).filter(item => item !== null);
                        
                        series.setData(newSeriesData, true);
                    }
                },

                initializeMap() {
                    if (typeof Highcharts === 'undefined' || 
                        typeof Highcharts.maps === 'undefined' || 
                        typeof Highcharts.maps['countries/id/id-all'] === 'undefined') {
                        setTimeout(() => this.initializeMap(), 100);
                        return;
                    }

                    this.chart = Highcharts.mapChart('map-container', {
                        chart: {
                            map: 'countries/id/id-all',
                            backgroundColor: '#fff',
                            height: '600',
                            style: {
                                fontFamily: getComputedStyle(document.body).getPropertyValue('--font-family')
                            },
                            events: {
                                render: function() {
                                    if (!this.logo) {
                                        this.logo = this.renderer.image(
                                            '/images/logo-siber-polri.png',
                                            10,
                                            10,
                                            40,
                                            40
                                        ).add();
                                    }
                                }
                            },
                        },
                        
                        title: {
                            useHTML: true,
                            text: 'Sebaran TKP Laporan Per Provinsi',
                            style: {
                                fontSize: '1.5rem',
                                fontWeight: '600'
                            }
                        },
                        subtitle: {
                            text: 'Sumber: Data Laporan Ditressiber Polda Jatim',
                            style: {
                                fontSize: '1rem'
                            }
                        },
                        mapNavigation: {
                            enabled: true,
                            buttonOptions: {
                                verticalAlign: 'bottom'
                            }
                        },
                        legend: {
                            enabled: true,
                            align: 'right',
                            verticalAlign: 'middle',
                            layout: 'vertical'
                        },
                        colorAxis: {
                            min: 0,
                            stops: [
                                [0, '#EFEFFF'],
                                [0.5, '#4444FF'],
                                [1, '#000044']
                            ]
                        },
                        series: [{
                            mapData: Highcharts.maps['countries/id/id-all'],
                            data: Object.entries(this.mapData).map(([provinsi, data]) => {
                                const hcKey = this.provinsiMapping[provinsi.toUpperCase()];
                                if (!hcKey) return null;
                                return {
                                    'hc-key': hcKey,
                                    value: Object.values(data).reduce((a, b) => a + b, 0),
                                    LaporanInfo: data.LaporanInfo || 0,
                                    LaporanInformasi: data.LaporanInformasi || 0,
                                    LaporanPolisi: data.LaporanPolisi || 0,
                                    Pengaduan: data.Pengaduan || 0,
                                    name: provinsi
                                };
                            }).filter(item => item !== null),
                            name: 'Total Laporan',
                            states: {
                                hover: {
                                    color: '#BADA55'
                                }
                            },
                            dataLabels: {
                                enabled: true,
                                format: '{point.name}',
                                style: {
                                    fontSize: '0.8rem'
                                }
                            },
                            tooltip: {
                                pointFormat: '<b>{point.name}</b><br/>' +
                                    'Laporan Informasi (LI): {point.LaporanInfo}<br/>' +
                                    'Informasi / Surat Masyarakat (Dumas): {point.LaporanInformasi}<br/>' +
                                    'Laporan Polisi (LP): {point.LaporanPolisi}<br/>' +
                                    'Laporan / Pengaduan Masyarakat (LPM): {point.Pengaduan}<br/>' +
                                    'Total: {point.value}'
                            }
                        }]
                    });
                }
            }"
            wire:poll.30s
            wire:ignore
        >
            <div id="map-container" class="w-full" style="height: 600px;"></div>
        </div>
    </x-filament::card>
</x-filament::widget>