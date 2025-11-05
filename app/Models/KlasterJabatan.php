<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class KlasterJabatan extends Model
{
    use HasFactory;

    protected $table = 'klaster_jabatan';

    protected $fillable = [
        'nama',
        'parent_id',
    ];

    /**
     * Relasi ke parent (atasan dalam struktur klaster).
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Relasi ke children (jabatan di bawahnya).
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Rekursif untuk mendapatkan seluruh anak (jika perlu menampilkan struktur lengkap).
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Scope untuk level tertinggi (misal: Pimpinan, Subdit, Subbag, dst)
     */
    public function scopeRoot($query)
    {
        return $query->whereNull('parent_id');
    }
    
        /**
     * Ambil semua parent (atasan) sampai root, termasuk dirinya sendiri.
     */
    public function ancestorsAndSelf()
    {
        $node = $this;
        $ancestors = collect([$node]);

        while ($node->parent) {
            $ancestors->prepend($node->parent);
            $node = $node->parent;
        }

        return $ancestors;
    }

}
