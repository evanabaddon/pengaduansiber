<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Surat extends Model
{
    use HasFactory;

    protected $fillable = [
        'nomor_surat',
        'perihal',
        'jenis_dokumen',
        'kategori_surat',
        'pejabat_penerbit',
        'template_path',
        'document_url',
        'subdit_id',
        'unit_id',
        'penyidik_id',
    ];

    /**
     * 🔗 Relasi ke tabel lain
     */
    public function subdit(): BelongsTo
    {
        return $this->belongsTo(Subdit::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function penyidik(): BelongsTo
    {
        return $this->belongsTo(Penyidik::class);
    }

    /**
     * 🧩 Accessor untuk template_path (jika perlu otomatis)
     */
    public function getTemplateFullPathAttribute(): ?string
    {
        if ($this->template_path) {
            return storage_path('app/' . ltrim($this->template_path, '/'));
        }

        return null;
    }

    /**
     * 🧩 Contoh helper untuk generate path otomatis (bisa dipakai nanti di form)
     */
    public static function generateTemplatePath($kategori, $pejabat, $opsi = null)
    {
        // Contoh hasil: templates/1. SURAT PERINTAH/1. SURAT PERINTAH KAPOLDA.docx
        $base = 'templates/';
        $folder = match (strtoupper($kategori)) {
            'SURAT PERINTAH' => '1. SURAT PERINTAH',
            'SURAT TUGAS' => '2. SURAT TUGAS',
            'NOTA DINAS' => '3. NOTA DINAS',
            'SURAT TELEGRAM' => '4. SURAT TELEGRAM (TR)',
            'SURAT' => '5. SURAT',
            'SURAT PENGANTAR' => '6. SURAT PENGANTAR',
            'SURAT UNDANGAN' => '7. SURAT UNDANGAN',
            default => $kategori,
        };

        $file = strtoupper($kategori) . ' ' . strtoupper($pejabat) . ($opsi ? " ({$opsi})" : '') . '.docx';

        return $base . $folder . '/' . $file;
    }
}
