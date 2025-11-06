<?php

namespace Database\Seeders;

use App\Models\KlasterJabatan;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KlasterJabatanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Add this line to delete existing records before seeding
        // KlasterJabatan::truncate();

        $createRecursively = function (array $items, $parentId = null) use (&$createRecursively) {
            foreach ($items as $key => $value) {
                if (is_int($key)) {
                    $name = $value;
                    $children = [];
                } else {
                    $name = $key;
                    $children = is_array($value) ? $value : [];
                }

                $jab = KlasterJabatan::firstOrCreate(
                    ['nama' => $name, 'parent_id' => $parentId],
                    ['nama' => $name, 'parent_id' => $parentId]
                );

                if (!empty($children)) {
                    $createRecursively($children, $jab->id);
                }
            }
        };

        // helper roman
        $roman = function (int $num): string {
            $map = [
                1000 => 'M', 900 => 'CM', 500 => 'D', 400 => 'CD',
                100 => 'C', 90 => 'XC', 50 => 'L', 40 => 'XL',
                10 => 'X', 9 => 'IX', 5 => 'V', 4 => 'IV', 1 => 'I'
            ];
            $res = '';
            foreach ($map as $k => $v) {
                while ($num >= $k) {
                    $res .= $v;
                    $num -= $k;
                }
            }
            return $res;
        };

        // buat array Kanit I..V
        $kanitRange = function (int $from = 1, int $to = 5) use ($roman) {
            $out = [];
            foreach (range($from, $to) as $i) {
                $out[] = 'Kanit ' . $roman($i);
            }
            return $out;
        };

        // buat array Banit I..V
        $banitRange = function (int $from = 1, int $to = 5) use ($roman) {
            $out = [];
            foreach (range($from, $to) as $i) {
                $out[] = 'Banit ' . $roman($i);
            }
            return $out;
        };

        // struktur sesuai koreksi: Banit di bawah Subdit (sejajar Kasubdit)
        $struktur = [
            // PIMPINAN
            'Pimpinan' => [
                'Direktur',
                'Wakil Direktur',
            ],

            // PEMBANTU PIMPINAN / STAF
            'Pembantu Pimpinan / Staf' => [
                'Subbagrenmin' => [
                    'Kasubbagrenmin',
                    'Kaur' => [
                        'Kaurren',
                        'Kaurmintu',
                        'Kaurkeu',
                    ],
                    'Pamin',
                    'Bamin / Banum',
                ],

                'Bagbinopsnal' => [
                    'Kabagbinopsnal',
                    'Kasubbag' => [
                        'Kasubbagminopsnal',
                        'Kasubbaganev',
                    ],
                    'Paur' => [
                        'Paur Subbagminopsnal',
                        'Paur Subbaganev',
                    ],
                    'Banum',
                ],

                'Bagwassidik' => [
                    'Kabagwassidik',
                    'Kanit' => [
                        'Kanit I',
                        'Kanit II',
                        'Kanit III',
                    ],
                    'Panit' => [
                        'Panit I',
                        'Panit II',
                        'Panit III',
                    ],
                    'Banum',
                ],

                'Sikorwas PPNS' => [
                    'Kasikorwas PPNS',
                    'Kasubsi' => [
                        'Kasubsibansidik',
                        'Kasubsibinpuan',
                    ],
                    'Bamin / Banum',
                ],
            ],

            // PELAKSANA TUGAS POKOK / SUBDIT
            'Pelaksana Tugas Pokok / Subdit' => [
                'Subdit I' => [
                    'Kasubdit I',
                    'Kanit' => $kanitRange(),
                    'Panit' => [
                        'Panit I',
                        'Panit II',
                        'Panit III',
                        'Panit IV',
                        'Panit V',
                    ],
                    'Banit' => $banitRange(),
                    'Banum Subdit I',
                ],

                'Subdit II' => [
                    'Kasubdit II',
                    'Kanit' => $kanitRange(),
                    'Panit' => [
                        'Panit I',
                        'Panit II',
                        'Panit III',
                        'Panit IV',
                        'Panit V',
                    ],
                    'Banit' => $banitRange(),
                    'Banum Subdit II',
                ],

                'Subdit III' => [
                    'Kasubdit III',
                    'Kanit' => $kanitRange(),
                    'Panit' => [
                        'Panit I',
                        'Panit II',
                        'Panit III',
                        'Panit IV',
                        'Panit V',
                    ],
                    'Banit' => $banitRange(),
                    'Banum Subdit III',
                ],
            ],

        ];

        $createRecursively($struktur);
    }
}
