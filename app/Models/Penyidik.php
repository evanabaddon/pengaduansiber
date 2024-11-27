<?php

namespace App\Models;

use App\Models\Unit;
use Illuminate\Database\Eloquent\Model;

class Penyidik extends Model
{
    protected $fillable = ['name', 'unit_id', 'kontak', 'subdit_id', 'pangkat_penyidik', 'nrp_penyidik'];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function subdit()
    {
        return $this->belongsTo(Subdit::class);
    }

    // relasi ke user
    public function user()
    {
        return $this->hasMany(User::class);
    }
}
