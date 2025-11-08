<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Surat;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Storage;

class OnlyOfficeController extends Controller
{
    private function generateSuratFile(Surat $surat): string
    {
        $kategori = $surat->kategori_surat ?? 'Lainnya';
        $targetDir = "public/docs/{$kategori}";
        Storage::makeDirectory($targetDir);

        // Ambil template dasar
        $templatePath = storage_path('app/templates/' . ltrim($surat->template_path, '/\\'));

        if (!file_exists($templatePath)) {
            Log::error('Template surat tidak ditemukan', [
                'checked_path' => $templatePath,
                'surat' => $surat->toArray(),
            ]);
            throw new \Exception("Template tidak ditemukan: {$templatePath}");
        }

        // Pastikan target directory ada
        $kategori = $surat->kategori_surat ?? 'Lainnya';
        $targetDir = storage_path("app/public/docs/{$kategori}");
        if (!file_exists($targetDir)) mkdir($targetDir, 0755, true);

        $filename = "{$surat->id}_" . \Str::slug($surat->kategori_surat . '_' . $surat->pejabat_penerbit) . '.docx';
        $destination = "{$targetDir}/{$filename}";

        // Copy manual karena kita pakai path absolute
        copy($templatePath, $destination);

        // Simpan URL publik ke database
        $surat->update([
            'document_url' => "storage/docs/{$kategori}/{$filename}",
        ]);

        return $destination;

    }

    public function edit(Surat $surat)
    {
        // Pastikan file surat sudah ada, kalau belum buat dulu
        $localPath = $this->generateSuratFile($surat);
        // gunakan URL ngrok publik, bukan asset()
        $publicUrl = env('APP_PUBLIC_URL') . '/storage/docs/' . $surat->kategori_surat . '/' . basename($localPath);

        // Buat URL callback ke route lokal Laravel
        $callbackUrl = route('onlyoffice.callback', $surat->id);

        $config = [
            'document' => [
                'fileType' => 'docx',
                'key' => md5($surat->id . now()),
                'title' => "{$surat->kategori_surat} - {$surat->pejabat_penerbit}.docx",
                'url' => $publicUrl,
            ],
            'documentType' => 'word',
            'editorConfig' => [
                'callbackUrl' => $callbackUrl, // arahkan ke Laravel sendiri
                'lang' => 'id',
                'mode' => 'edit',
                'customization' => [
                    'forcesave' => true,
                    "goback" => [
                        "url" => Filament::getCurrentPanel()->getUrl(),
                    ],
                ],
            ],
        ];

        $secret = env('ONLYOFFICE_JWT_SECRET');
        $token = JWT::encode($config, $secret, 'HS256');

        return view('onlyoffice.editor', compact('config', 'token', 'surat'));
    }

    // public function callback(Request $request, Surat $surat)
    // {
    //     Log::info('OnlyOffice callback diterima', $request->all());

    //     $status = $request->input('status');
    //     $fileUrl = $request->input('url');

    //     // Status 2 = file selesai diedit dan disimpan
    //     if ($status == 2 && $fileUrl) {
    //         $contents = file_get_contents($fileUrl);

    //         $localPath = storage_path('app/' . str_replace('storage/', 'public/', $surat->document_url));
    //         file_put_contents($localPath, $contents);

    //         Log::info('Dokumen berhasil disimpan lokal', ['path' => $localPath]);
    //     }

    //     return response()->json(['error' => 0]);
    // }
    public function callback(Request $request, Surat $surat)
{
    Log::info('OnlyOffice callback diterima', $request->all());

    $status = $request->input('status');

    // Gunakan status 2 sebagai tanda dokumen selesai diedit
    if ($status == 2) {
        $localPath = storage_path('app/public/docs/' . $surat->kategori_surat . '/' . basename($surat->document_url));

        if (file_exists($localPath)) {
            Log::info('Dokumen selesai diedit, file lokal ada', ['path' => $localPath]);
        } else {
            Log::error('File lokal tidak ditemukan meski status 2 diterima', ['path' => $localPath]);
        }
    }

    return response()->json(['error' => 0]);
}



}
