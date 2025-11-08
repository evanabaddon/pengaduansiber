<?php

namespace App\Helpers;

use Illuminate\Support\Facades\File;

class TemplateResolver
{
    /**
     * Cari file template berdasarkan struktur 3 level:
     * Level 1: Jenis Dokumen (mis. "Naskah Dinas")
     * Level 2: Kategori Surat (mis. "Surat Perintah")
     * Level 3: File berdasarkan pejabat penerbit
     */
    public static function resolve(string $jenis, string $kategori, string $pejabat): ?string
    {
        $basePath = storage_path('app/templates');

        // 🔹 1️⃣ Temukan folder Level 1 (Jenis Dokumen)
        $jenisFolders = File::directories($basePath);
        $jenisFolder = collect($jenisFolders)->first(function ($folder) use ($jenis) {
            return stripos(basename($folder), $jenis) !== false;
        });

        if (!$jenisFolder) {
            \Log::warning("TemplateResolver: Jenis dokumen '$jenis' tidak ditemukan di $basePath");
            return null;
        }

        // 🔹 2️⃣ Temukan folder Level 2 (Kategori Surat)
        $kategoriFolders = File::directories($jenisFolder);
        $kategoriFolder = collect($kategoriFolders)->first(function ($folder) use ($kategori) {
            return stripos(basename($folder), $kategori) !== false;
        });

        if (!$kategoriFolder) {
            \Log::warning("TemplateResolver: Kategori surat '$kategori' tidak ditemukan di $jenisFolder");
            return null;
        }

        // 🔹 3️⃣ Temukan file berdasarkan pejabat penerbit
        $files = File::files($kategoriFolder);
        $pejabat = strtolower($pejabat);

        // cari file yang mengandung nama pejabat
        $file = collect($files)->first(function ($f) use ($pejabat) {
            return str_contains(strtolower($f->getFilename()), $pejabat);
        });

        // fallback ke file pertama kalau belum ketemu
        if (!$file && count($files) > 0) {
            $file = $files[0];
        }

        if (!$file) {
            \Log::warning("TemplateResolver: Tidak ditemukan file untuk pejabat '$pejabat' di $kategoriFolder");
            return null;
        }

        // hasil akhir disimpan relatif terhadap base folder
        return basename($jenisFolder) . '/' . basename($kategoriFolder) . '/' . $file->getFilename();
    }
}
