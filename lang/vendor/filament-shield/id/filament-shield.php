<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Table Columns
    |--------------------------------------------------------------------------
    */

    'column.name' => 'Nama',
    'column.guard_name' => 'Nama Penjaga',
    'column.roles' => 'Peran',
    'column.permissions' => 'Izin',
    'column.updated_at' => 'Dirubah',

    /*
    |--------------------------------------------------------------------------
    | Form Fields
    |--------------------------------------------------------------------------
    */

    'field.name' => 'Nama',
    'field.guard_name' => 'Nama Penjaga',
    'field.permissions' => 'Izin',
    'field.select_all.name' => 'Pilih Semua',
    'field.select_all.message' => 'Aktifkan semua izin yang <span class="text-primary font-medium">Tersedia</span> untuk Peran ini.',
    'field.team' => 'Tim',
    'field.team.placeholder' => 'Pilih Tim',

    /*
    |--------------------------------------------------------------------------
    | Navigation & Resource
    |--------------------------------------------------------------------------
    */

    'nav.group' => 'Setting',
    // navigation sort
    'nav.sort' => 9,
    'nav.role.label' => 'Peran',
    'nav.role.icon' => 'heroicon-o-shield-check',
    'resource.label.role' => 'Peran',
    'resource.label.roles' => 'Peran',

    /*
    |--------------------------------------------------------------------------
    | Section & Tabs
    |--------------------------------------------------------------------------
    */

    'section' => 'Entitas',
    'resources' => 'Sumber Daya',
    'widgets' => 'Widget',
    'pages' => 'Halaman',
    'custom' => 'Izin Kustom',

    /*
    |--------------------------------------------------------------------------
    | Messages
    |--------------------------------------------------------------------------
    */

    'forbidden' => 'Kamu tidak punya izin akses',

    /*
    |--------------------------------------------------------------------------
    | Resource Permissions' Labels
    |--------------------------------------------------------------------------
    */

    'resource_permission_prefixes_labels' => [
        'view' => 'Lihat',
        'view_any' => 'Lihat Apa Saja',
        'create' => 'Buat',
        'update' => 'Perbarui',
        'delete' => 'Hapus',
        'delete_any' => 'Hapus Apa Saja',
        'force_delete' => 'Paksa Hapus',
        'force_delete_any' => 'Paksa Hapus Apa Saja',
        'restore' => 'Pulihkan',
        'replicate' => 'Replikasi',
        'reorder' => 'Susun Ulang',
        'restore_any' => 'Pulihkan Apa Saja',
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource Permission Labels
    |--------------------------------------------------------------------------
    */
    'generator' => [
        'option' => 'policies_and_permissions',
        'resource_label_override' => [
            'LaporanInfo' => 'Laporan Informasi (LI)',
            // tambahkan resource lain yang perlu di-override
        ],
    ],
    
];
