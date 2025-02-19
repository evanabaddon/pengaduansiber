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
                    'LAMPUNG' => 'id-1024',
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
                    'MALUKU UTARA' => 'id-la',        // Diubah dari 'id-mu' ke 'id-la'
                    'PAPUA BARAT' => 'id-ib',         // Diubah dari 'id-pb' ke 'id-ib'
                    'P A P U A' => 'id-pa',                // Menghapus spasi di 'P A P U A'
                ]),

                init() {
                    this.initializeMap();
                    this.$wire.$on('updateMap', () => {
                        this.updateMapData(@js($this->getData()));
                    });

                    // Tambahkan observer untuk dark mode
                    const observer = new MutationObserver((mutations) => {
                        mutations.forEach((mutation) => {
                            if (mutation.attributeName === 'class') {
                                const isDarkMode = document.documentElement.classList.contains('dark');
                                if (this.chart) {
                                    // Simpan posisi dan zoom level saat ini
                                    const currentZoom = this.chart.mapView && this.chart.mapView.zoom;
                                    const currentCenter = this.chart.mapView && this.chart.mapView.center;

                                    // Update chart dengan konfigurasi baru
                                    this.chart.update({
                                        title: {
                                            style: {
                                                color: isDarkMode ? '#fff' : '#000'
                                            }
                                        },
                                        subtitle: {
                                            style: {
                                                color: isDarkMode ? '#ccc' : '#666'
                                            }
                                        },
                                        legend: {
                                            itemStyle: {
                                                color: isDarkMode ? '#fff' : '#000'
                                            }
                                        },
                                        colorAxis: {
                                            stops: isDarkMode ? [
                                                [0, '#1a1a3a'],
                                                [0.5, '#4444FF'],
                                                [1, '#8888FF']
                                            ] : [
                                                [0, '#EFEFFF'],
                                                [0.5, '#4444FF'],
                                                [1, '#000044']
                                            ]
                                        }
                                    }, false);

                                    // Redraw chart dengan animasi false
                                    this.chart.redraw(false);

                                    // Kembalikan posisi dan zoom level
                                    if (currentZoom && currentCenter) {
                                        this.chart.mapView.update({
                                            zoom: currentZoom,
                                            center: currentCenter
                                        }, false);
                                    }
                                }
                            }
                        });
                    });

                    observer.observe(document.documentElement, {
                        attributes: true,
                        attributeFilter: ['class']
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

                    const isDarkMode = document.documentElement.classList.contains('dark');

                    this.chart = Highcharts.mapChart('map-container', {
                        chart: {
                            map: 'countries/id/id-all',
                            backgroundColor: 'none',
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
                                fontWeight: '600',
                                color: isDarkMode ? '#fff' : '#000',
                            }
                        },
                        subtitle: {
                            text: 'Sumber: Data Laporan Ditressiber Polda Jatim',
                            style: {
                                fontSize: '1rem',
                                color: isDarkMode ? '#ccc' : '#666'
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
                            stops: isDarkMode ? [
                                [0, '#1a1a3a'],
                                [0.5, '#4444FF'],
                                [1, '#8888FF']
                            ] : [
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
                                    brightness: 0.2,
                                    borderColor: isDarkMode ? '#ffffff' : '#000000'
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