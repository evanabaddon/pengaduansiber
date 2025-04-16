<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LaporanPolisiAnalysis extends Model
{
    protected $fillable = [
        'laporan_id',
        'kronologi_analysis',
        'possible_laws',
        'investigation_steps',
        'priority_level',
        'raw_response'
    ];

    public function laporan()
    {
        return $this->belongsTo(LaporanPolisi::class, 'laporan_id');
    }   
}
