<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormDraft extends Model
{
    protected $fillable = [
        'user_id',
        'form_type',
        'main_data',
        'pelapor_data',
        'korban_data',
        'terlapor_data',
        'current_step'
    ];

    protected $casts = [
        'main_data' => 'array',
        'pelapor_data' => 'array',
        'korban_data' => 'array',
        'terlapor_data' => 'array'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 