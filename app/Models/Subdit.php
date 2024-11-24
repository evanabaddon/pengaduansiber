<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\Penyidik;
use Illuminate\Database\Eloquent\Model;

class Subdit extends Model
{
    protected $fillable = ['name', 'nama_pimpinan'];

    public function units()
    {
        return $this->hasMany(Unit::class);
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
