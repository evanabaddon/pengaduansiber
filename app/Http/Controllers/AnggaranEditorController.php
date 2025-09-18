<?php
namespace App\Http\Controllers;

use Firebase\JWT\JWT;
use App\Models\Anggaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class AnggaranEditorController extends Controller
{
    
    protected function generateFileFromTemplate($record)
    {
        $templatePath = storage_path('app/templates/anggaran.xlsx'); // pastikan ini ada
        $outputPath   = storage_path('app/public/' . $record->file_path);
    
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
    
        // Isi data dari database
        $sheet->setCellValue('A5', $record->nama);
        $sheet->setCellValue('A6', 'TAHUN ANGGARAN ' . $record->tahun_anggaran);

        // Unlock semua sel dulu
        $sheet->getStyle($sheet->calculateWorksheetDimension())
            ->getProtection()
            ->setLocked(false);

        // Lock sel tertentu
        $sheet->getStyle('A1:G3')->getProtection()->setLocked(true);
        $sheet->getStyle('A5:A6')->getProtection()->setLocked(true);

        // Proteksi sheet
        // $sheet->getProtection()->setSheet(true);
        // $sheet->getProtection()->setPassword('secret');
    
        $writer = new Xlsx($spreadsheet);
        $writer->save($outputPath);
    }
    
    public function edit($id)
    {
        $record = Anggaran::findOrFail($id);

        $publicBase = env('APP_PUBLIC_URL', config('app.url')); 

        // Generate file terbaru dari template
        $this->generateFileFromTemplate($record);

        // Tambahkan query string unik untuk menghindari cache browser/OnlyOffice
        $fileUrl = $publicBase . "/storage/{$record->file_path}?v=" . time();

        // Gunakan key unik setiap load
        $docKey = md5($record->id . ($record->updated_at ?? now()) . microtime(true));

        $config = [
            'document' => [
                'fileType' => pathinfo($record->file_path, PATHINFO_EXTENSION),
                'key' => $docKey,
                'title' => "{$record->nama}-{$record->tahun_anggaran}." . pathinfo($record->file_path, PATHINFO_EXTENSION),
                'url' => $fileUrl,
            ],
            'documentType' => 'cell', 
            'type' => 'desktop',
            'height' => '100%',
            'width' => '100%',
            'editorConfig' => [
                'callbackUrl' => $publicBase . "/subbagrenmin/anggaran/editor/callback/{$record->id}",
                'lang' => 'id',
                'mode' => 'edit',
                'customization' => [
                    'zoom' => 120,
                    'forcesave' => true,
                    'feedback' => false,
                ],
            ],
        ];

        $token = JWT::encode($config, env('ONLYOFFICE_JWT_SECRET'), 'HS256');

        return view('filament.subbagrenmin.pages.anggaran-editor', compact('record', 'config', 'token'));
    }

    // public function edit($id)
    // {
    //     $record = Anggaran::findOrFail($id);

    //     // Gunakan URL publik jika ingin akses dari browser via ngrok atau production
    //     $publicBase = env('APP_PUBLIC_URL', config('app.url')); 
    //     // APP_PUBLIC_URL di .env = "https://xxxx.ngrok-free.app" saat develop

    //     $this->generateFileFromTemplate($record);

    //     $fileUrl = $publicBase . "/storage/{$record->file_path}";

    //     $config = [
    //         'document' => [
    //             'fileType' => pathinfo($record->file_path, PATHINFO_EXTENSION),
    //             'key' => md5($record->id . ($record->updated_at ?? now())),
    //             'title' => "{$record->nama}-{$record->tahun_anggaran}." . pathinfo($record->file_path, PATHINFO_EXTENSION),
    //             'url' => $fileUrl,
    //         ],
    //         'documentType' => 'cell', // spreadsheet
    //         'type' => 'desktop',
    //         'height' => '100%',
    //         'width' => '100%',
    //         'editorConfig' => [
    //             'callbackUrl' => $publicBase . "/subbagrenmin/anggaran/editor/callback/{$record->id}",
    //             'lang' => 'id',
    //             'mode' => 'edit',
    //             'customization' => [
    //                 'zoom' => 120,
    //                 'forcesave' => true,
    //                 'feedback' => false,
    //             ],
    //         ],
    //     ];

    //     $token = JWT::encode($config, env('ONLYOFFICE_JWT_SECRET'), 'HS256');

    //     return view('filament.subbagrenmin.pages.anggaran-editor', compact('record', 'config', 'token'));
    // }

    public function callback($id, Request $request)
    {
        $record = Anggaran::findOrFail($id);

        // Ambil status dan URL dari OnlyOffice
        $status = $request->input('status');
        $fileUrl = $request->input('url');

        Log::info("Callback OnlyOffice diterima untuk anggaran #{$id}", [
            'status' => $status,
            'url' => $fileUrl,
            'request_all' => $request->all(),
        ]);

        // Status 2 = dokumen selesai diedit
        if ($status == 2) {
            try {
                $localFile = storage_path('app/public/' . $record->file_path);

                // Cek file lokal ada atau tidak
                if (!file_exists($localFile)) {
                    Log::error("File lokal tidak ditemukan", ['path' => $localFile]);
                    return response()->json(['error' => 1, 'message' => 'File lokal tidak ditemukan'], 500);
                }

                // Backup file lama
                $backupDir = storage_path('app/public/backup/');
                if (!file_exists($backupDir)) mkdir($backupDir, 0755, true);
                $backupPath = $backupDir . basename($record->file_path) . '-' . now()->format('YmdHis') . '.' . pathinfo($record->file_path, PATHINFO_EXTENSION);
                copy($localFile, $backupPath);
                Log::info("File lama di-backup ke {$backupPath}");

                // Simpan file baru
                if ($fileUrl) {
                    // Jika OnlyOffice memberikan URL file, download file dari URL publik
                    $contents = @file_get_contents($fileUrl);
                    if (!$contents) {
                        Log::error("Gagal mendownload file dari OnlyOffice", ['url' => $fileUrl]);
                        return response()->json(['error' => 1, 'message' => 'Failed to download file dari OnlyOffice'], 500);
                    }
                    file_put_contents($localFile, $contents);
                } else {
                    // Jika fileUrl null, gunakan file lokal (edit langsung di server)
                    Log::info("File URL null, menggunakan file lokal");
                }

                Log::info("File berhasil diupdate", ['file_path' => $record->file_path]);

            } catch (\Exception $e) {
                Log::error("Error saat menyimpan file OnlyOffice", [
                    'message' => $e->getMessage(),
                    'file' => $fileUrl,
                ]);
                return response()->json(['error' => 1, 'message' => $e->getMessage()], 500);
            }
        }

        return response()->json(['error' => 0]);
    }

    public function view($id)
    {
        $record = Anggaran::findOrFail($id);
        $publicBase = env('APP_PUBLIC_URL', config('app.url'));
        $fileUrl = $publicBase . "/storage/{$record->file_path}";

        $config = [
            'document' => [
                'fileType' => pathinfo($record->file_path, PATHINFO_EXTENSION),
                'key' => md5($record->id . ($record->updated_at ?? now())),
                'title' => "{$record->nama}-{$record->tahun_anggaran}." . pathinfo($record->file_path, PATHINFO_EXTENSION),
                'url' => $fileUrl,
            ],
            'documentType' => 'cell', // spreadsheet
            'type' => 'desktop',
            'height' => '100%',
            'width' => '100%',
            'editorConfig' => [
                'mode' => 'view', // <-- hanya view, tidak bisa edit
                'lang' => 'id',
                'customization' => [
                    'forcesave' => true,
                    'feedback' => false,
                ],
            ],
            
        ];

        $token = JWT::encode($config, env('ONLYOFFICE_JWT_SECRET'), 'HS256');

        return view('filament.subbagrenmin.pages.anggaran-viewer', compact('record', 'config', 'token'));
    }

    // Fungsi cetak / download
    public function download($id)
    {
        $record = Anggaran::findOrFail($id);
        $filePath = storage_path('app/public/' . $record->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($filePath, "{$record->nama}-{$record->tahun_anggaran}." . pathinfo($record->file_path, PATHINFO_EXTENSION));
    }

}
