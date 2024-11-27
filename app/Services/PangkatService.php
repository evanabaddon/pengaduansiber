<?php

namespace App\Services;

class PangkatService
{
    public static function getAllPangkat()
    {
        $jsonPath = public_path('pangkat.json');
        $jsonContent = file_get_contents($jsonPath);
        $pangkatList = json_decode($jsonContent, true);

        // Ubah format untuk select options
        $options = collect($pangkatList)->pluck('pangkat')->toArray();

        return $options;
    }

    public static function getPangkatList()
    {
        $jsonPath = public_path('pangkat.json');
        $jsonContent = file_get_contents($jsonPath);
        return json_decode($jsonContent, true);
    }
} 