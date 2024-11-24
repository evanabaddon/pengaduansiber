<?php

namespace App\Models;

use App\Models\Subdit;
use App\Models\Penyidik;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    protected $fillable = ['name', 'subdit_id', 'nama_pimpinan'];

    public function subdit()
    {
        return $this->belongsTo(Subdit::class);
    }

    public function penyidiks()
    {
        return $this->hasMany(Penyidik::class);
    }

    // relasi ke user
    public function user()
    {
        return $this->hasMany(User::class);
    }
}
