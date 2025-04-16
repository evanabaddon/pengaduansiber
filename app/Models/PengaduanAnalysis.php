<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengaduanAnalysis extends Model
{
    protected $fillable = [
        'pengaduan_id',
        'kronologi_analysis',
        'possible_laws',
        'investigation_steps',
        'priority_level',
        'raw_response'
    ];

    public function pengaduan()
    {
        return $this->belongsTo(Pengaduan::class, 'pengaduan_id');
    }
}
