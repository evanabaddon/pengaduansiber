<?php

namespace App\Models;

use App\Models\Unit;
use App\Models\Penyidik;
use Illuminate\Database\Eloquent\Model;

class Subdit extends Model
{
    protected $fillable = ['name', 'nama_pimpinan', 'pangkat_pimpinan', 'nrp_pimpinan', 'kontak_pimpinan', 'subdit_id'];

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

    // fungsi ketika subdit dihapus, maka units dan penyidiks juga akan dihapus
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($subdit) {
            // Hapus penyidik yang terkait dengan subdit ini terlebih dahulu
            $subdit->penyidiks()->where('subdit_id', $subdit->id)->delete();

            // Kemudian hapus unit yang terkait
            $subdit->units()->delete();

            // Set user subdit_id to null
            $subdit->user()->update(['subdit_id' => null]);
        });
    }
}
