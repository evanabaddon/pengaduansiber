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

    // fungsi ketika unit dihapus, maka penyidiks juga akan dihapus
    public static function boot()
    {
        parent::boot();
        static::deleting(function ($unit) {
            $unit->penyidiks()->delete();

            // set user unit_id to null
            $unit->user()->update(['unit_id' => null]);
        });
    }
}
