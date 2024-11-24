<?php

// config for Teguh02/IndonesiaTerritoryForms
return [

    /**
     * The name of the form fields
     * Note : this form fields are used to get the value from the form
     *        and then use it to get the data from the database
     */
    'forms_name' => [
        'province' => 'province_id',
        'city' => 'city_id',
        'district' => 'district_id',
        'sub_district' => 'subdistrict_id',
        'postal_code' => 'postal_code',
    ],

    /**
     * The visibility of the form fields
     * Note : this form fields are used to show or hide 
     *        the form fields, and we can't disable the province
     *        because the province is the main data
     */
    'forms_visibility' => [
        'city' => true,
        'district' => true,
        'sub_district' => true,
        'postal_code' => true,
    ],

    /**
     * The icons for the form fields
     */
    'forms_icons' => [
        'enable' => true,
        'icons' => [
            'province' => 'heroicon-o-globe-asia-australia',
            'city' => 'heroicon-o-map',
            'district' => 'heroicon-o-map',
            'sub_district' => 'heroicon-o-map',
            'postal_code' => 'heroicon-o-map',
        ],
    ],

    /**
     * The cache time to live
     * Note : this cache time to live is used to cache the data
     *        from the database, so it will reduce the query to the database.
     *        To disable the cache, you can set it to 0
     */
    'cache_ttl' => 3600,
];