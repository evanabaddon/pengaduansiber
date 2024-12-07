<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class DataTambahan extends Model
{
    protected $table = 'data_tambahan';
    
    protected $fillable = [
        'nama_data',
        'keterangan',
        'recordable_type',
        'recordable_id'
    ];

    public function recordable()
    {
        return $this->morphTo();
    }
} 