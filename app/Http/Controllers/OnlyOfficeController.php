<?php

namespace App\Http\Controllers;

use Log;
use App\Models\Surat;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
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
    $localPath = $this->generateSuratFile($surat);
    $publicUrl = env('APP_PUBLIC_URL') . '/storage/docs/' . $surat->kategori_surat . '/' . basename($localPath);
    $callbackUrl = route('onlyoffice.callback', $surat->id);

    // Tentukan return URL berdasarkan panel
    $returnUrl = url('/admin'); // Default
    
    // Deteksi panel dari URL saat ini
    $currentUrl = request()->fullUrl();
    if (str_contains($currentUrl, 'subbagrenmin')) {
        $returnUrl = url('/subbagrenmin');
    } elseif (str_contains($currentUrl, 'sikorwas')) {
        $returnUrl = url('/sikorwas');
    } elseif (str_contains($currentUrl, 'bagwassidik')) {
        $returnUrl = url('/bagwassidik');
    } elseif (str_contains($currentUrl, 'bagbinopsnal')) {
        $returnUrl = url('/bagbinopsnal');
    }

    $config = [
        'document' => [
            'fileType' => 'docx',
            'key' => md5($surat->id . now()),
            'title' => "{$surat->kategori_surat} - {$surat->pejabat_penerbit}.docx",
            'url' => $publicUrl,
        ],
        'documentType' => 'word',
        'editorConfig' => [
            'callbackUrl' => $callbackUrl,
            'lang' => 'id',
            'mode' => 'edit',
            'customization' => [
                'forcesave' => true,
                'goback' => [
                    'url' => $returnUrl,
                    'blank' => false,
                ],
                'autosave' => true,
                'compactHeader' => false,
                'compactToolbar' => false,
                'feedback' => [
                    'url' => $callbackUrl,
                    'visible' => true
                ],
                // Tambahkan konfigurasi untuk auto-close
                'plugins' => false,
                'hideRulers' => true,
            ],
        ],
    ];

    $secret = env('ONLYOFFICE_JWT_SECRET');
    $token = JWT::encode($config, $secret, 'HS256');

    return view('onlyoffice.editor', compact('config', 'token', 'surat'));
}


public function close(Surat $surat)
{
    $surat->update(['status' => 'saved']);
    
    // Ambil return URL dari session atau parameter
    $returnUrl = session('filament_panel_url', '/admin');
    
    if (request()->has('return_url')) {
        $returnUrl = request('return_url');
    }

    return redirect($returnUrl);
}

public function callback(Request $request, Surat $surat)
{
    Log::info('OnlyOffice callback diterima', $request->all());

    $status = $request->input('status');
    $actions = $request->input('actions', []);
    
    Log::info('Callback details:', [
        'status' => $status,
        'actions' => $actions,
        'url' => $request->input('url'),
        'history' => $request->input('history')
    ]);

    // Status yang menandakan dokumen selesai
    $completedStatuses = [2, 3, 4, 6, 7];
    
    if (in_array($status, $completedStatuses)) {
        $surat->update(['status' => 'saved']);
        Log::info('Dokumen berhasil disimpan', ['status' => $status]);
        
        // Jika status 2, 6, atau 7, berikan response untuk close
        if (in_array($status, [2, 6, 7])) {
            return response()->json([
                'error' => 0,
                'close' => true // Flag untuk close editor
            ]);
        }
    }

    return response()->json(['error' => 0]);
}

    public function loading($id)
    {
        $surat = Surat::findOrFail($id);

        // tampilkan tampilan loading ringan
        return view('onlyoffice.loading', compact('surat'));
    }

}
