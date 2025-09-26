<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $fillable = [
        'name',
        'pangkat_staff',
        'nrp_staff',
        'kontak',
        'jabatan',
    ];

    public function getPangkatStaffLabelAttribute()
    {
        $map = [
            1 => 'KOMBESPOL',
            2 => 'AKBP',
            3 => 'KOMPOL',
            4 => 'AKP',
            5 => 'IPTU',
            6 => 'IPDA',
            7 => 'AIPTU',
            8 => 'AIPDA',
            9 => 'BRIPKA',
            10 => 'BRIGPOL',
            11 => 'BRIPTU',
            12 => 'BRIPDA',
            13 => 'STAFF',
        ];

        return $map[$this->pangkat_staff] ?? '-';
    }

}
