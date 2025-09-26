<?php
namespace App\Http\Controllers;

use App\Models\Staff;
use Firebase\JWT\JWT;
use App\Models\Anggaran;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Settings;
use Illuminate\Support\Facades\Storage;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Writer\Pdf\Mpdf as PdfWriter;

class AnggaranEditorController extends Controller
{
    
    public  function generateFileFromTemplate($record)
    {
        $templatePath = storage_path('app/templates/anggaran.xlsx'); // pastikan ini ada
        $outputPath   = storage_path('app/public/' . $record->file_path);
    
        $spreadsheet = IOFactory::load($templatePath);
        $sheet = $spreadsheet->getActiveSheet();
    
        // Isi data dari database
        $sheet->setCellValue('A6', 'TAHUN ANGGARAN ' . $record->tahun_anggaran);

        // Isi tanggal 
        $tanggal = now()->locale('id')->translatedFormat('d F Y');

        // Isi kolom Y45
        $sheet->setCellValue('Y45', 'Surabaya, ' . $tanggal);

        // Ambil pejabat dengan jabatan "kaurkeu"
        $pejabat = Staff::where('jabatan', 'kaurkeu')->first();

        if ($pejabat) {
            $sheet->setCellValue('Y51', $pejabat->name); // nama pejabat
            $sheet->setCellValue('Y52', $pejabat->pangkat_staff_label . ' NRP ' . $pejabat->nrp_staff);
        }

        $mapping = [
            // 'pagu' => 'H9',
            'belanja_pegawai_pagu' => 'H11',
            // 'belanja_barang_pagu' => 'G12',
            // 'lidik_sidik_pagu' => 'G13', 
            'dukops_giat_pagu' => 'H26',
            'harwat_r4_6_10_pagu' => 'H27',
            // 'harwat_fungsional_pagu' => '',

            // Subdit / unit - PAGU
            // 'subdit1_lidik_sidik_pagu' => 'G32',
            'subdit1_unit1_lidik_sidik_pagu' => 'H15',
            'subdit1_unit2_lidik_sidik_pagu' => 'H16',
            'subdit1_unit3_lidik_sidik_pagu' => 'H17',
            'subdit1_unit4_lidik_sidik_pagu' => 'H18',
            'subdit1_unit5_lidik_sidik_pagu' => 'H19',

            // 'subdit2_lidik_sidik_pagu' => 'P32',
            'subdit2_unit1_lidik_sidik_pagu' => 'H21',
            'subdit2_unit2_lidik_sidik_pagu' => 'H22',
            'subdit2_unit3_lidik_sidik_pagu' => 'H23',
            'subdit2_unit4_lidik_sidik_pagu' => 'H24',
            'subdit2_unit5_lidik_sidik_pagu' => 'H25',

            // 'subdit3_har_alsus_pagu' => 'G17',
            'subdit3_unit1_har_alsus_pagu' => 'H31',
            'subdit3_unit2_har_alsus_pagu' => 'H32',
            'subdit3_unit3_har_alsus_pagu' => 'H33',
            'subdit3_unit4_har_alsus_pagu' => 'H34',
            'subdit3_unit5_har_alsus_pagu' => 'H35',

            // 'subdit3_lisensi_latfung_pagu' => 'G18',
            'subdit3_unit1_lisensi_latfung_pagu' => 'H37',
            'subdit3_unit2_lisensi_latfung_pagu' => 'H38',
            'subdit3_unit3_lisensi_latfung_pagu' => 'H39',
            'subdit3_unit4_lisensi_latfung_pagu' => 'H40',
            'subdit3_unit5_lisensi_latfung_pagu' => 'H41',

            /*
            II. REALISASI
            */
            // 'realisasi' => '',
            'realisasi_belanja_pegawai' => 'Q11',
            // 'realisasi_belanja_barang' => 'G23',
            // 'realisasi_lidik_sidik' => 'G24',
            'realisasi_dukops_giat' => 'Q26',
            'realisasi_harwat_r4_6_10' => 'Q27',
            // 'realisasi_harwat_fungsional' => '',

            // Subdit / unit - REALISASI
            // 'subdit1_lidik_sidik_realisasi' => '',
            'subdit1_unit1_lidik_sidik_realisasi' => 'Q15',
            'subdit1_unit2_lidik_sidik_realisasi' => 'Q16',
            'subdit1_unit3_lidik_sidik_realisasi' => 'Q17',
            'subdit1_unit4_lidik_sidik_realisasi' => 'Q18',
            'subdit1_unit5_lidik_sidik_realisasi' => 'Q19',

            // 'subdit2_lidik_sidik_realisasi' => '',
            'subdit2_unit1_lidik_sidik_realisasi' => 'Q21',
            'subdit2_unit2_lidik_sidik_realisasi' => 'Q22',
            'subdit2_unit3_lidik_sidik_realisasi' => 'Q23',
            'subdit2_unit4_lidik_sidik_realisasi' => 'Q24',
            'subdit2_unit5_lidik_sidik_realisasi' => 'Q25',

            // 'subdit3_har_alsus_realisasi' => '',
            'subdit3_unit1_har_alsus_realisasi' => 'Q31',
            'subdit3_unit2_har_alsus_realisasi' => 'Q32',
            'subdit3_unit3_har_alsus_realisasi' => 'Q33',
            'subdit3_unit4_har_alsus_realisasi' => 'Q34',
            'subdit3_unit5_har_alsus_realisasi' => 'Q35',

            // 'subdit3_lisensi_latfung_realisasi' => '',
            'subdit3_unit1_lisensi_latfung_realisasi' => 'Q37',
            'subdit3_unit2_lisensi_latfung_realisasi' => 'Q38',
            'subdit3_unit3_lisensi_latfung_realisasi' => 'Q39',
            'subdit3_unit4_lisensi_latfung_realisasi' => 'Q40',
            'subdit3_unit5_lisensi_latfung_realisasi' => 'Q41',

            /*
            III. SILPA (lengkap / mirroring PAGU & REALISASI)
            */
            // 'silpa' => '',
            // 'silpa_belanja_pegawai' => '',
            // 'silpa_belanja_barang' => '',
            // 'silpa_lidik_sidik' => '',
            // 'silpa_dukops_giat' => '',
            // 'silpa_harwat_r4_6_10' => '',
            // 'silpa_harwat_fungsional' => '',
            // 'silpa_har_alsus' => '',
            // 'silpa_lisensi_latfung' => '',

            // Subdit / unit - SILPA (Subdit I)
            // 'subdit1_lidik_sidik_silpa' => '',
            // 'subdit1_unit1_lidik_sidik_silpa' => '',
            // 'subdit1_unit2_lidik_sidik_silpa' => '',
            // 'subdit1_unit3_lidik_sidik_silpa' => '',
            // 'subdit1_unit4_lidik_sidik_silpa' => '',
            // 'subdit1_unit5_lidik_sidik_silpa' => '',

            // Subdit / unit - SILPA (Subdit II)
            // 'subdit2_lidik_sidik_silpa' => '',
            // 'subdit2_unit1_lidik_sidik_silpa' => '',
            // 'subdit2_unit2_lidik_sidik_silpa' => '',
            // 'subdit2_unit3_lidik_sidik_silpa' => '',
            // 'subdit2_unit4_lidik_sidik_silpa' => '',
            // 'subdit2_unit5_lidik_sidik_silpa' => '',

            // Sub-rincian silpa harwat fungsional (Subdit III)
            // 'subdit3_har_alsus_silpa' => '',
            // 'subdit3_unit1_har_alsus_silpa' => '',
            // 'subdit3_unit2_har_alsus_silpa' => '',
            // 'subdit3_unit3_har_alsus_silpa' => '',
            // 'subdit3_unit4_har_alsus_silpa' => '',
            // 'subdit3_unit5_har_alsus_silpa' => '',

            // 'subdit3_lisensi_latfung_silpa' => '',
            // 'subdit3_unit1_lisensi_latfung_silpa' => '',
            // 'subdit3_unit2_lisensi_latfung_silpa' => '',
            // 'subdit3_unit3_lisensi_latfung_silpa' => '',
            // 'subdit3_unit4_lisensi_latfung_silpa' => '',
            // 'subdit3_unit5_lisensi_latfung_silpa' => '',
            
        ];
    
        foreach ($mapping as $field => $cell) {
            if (!is_null($record->$field)) {
                $sheet->setCellValue($cell, $record->$field);
            }
        }
    
        $writer = new Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(true); // ini penting biar formula dihitung Excel saat file dibuka
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
                'title' => "Anggaran_Ditressiber_Polda_Jatim_Tahun-{$record->tahun_anggaran}." . pathinfo($record->file_path, PATHINFO_EXTENSION),
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

    public function convertExcelToPdf($id)
    {
        $record = \App\Models\Anggaran::findOrFail($id);
        $excelPath = storage_path('app/public/' . $record->file_path);
    
        if (!file_exists($excelPath)) {
            abort(404, 'File Excel tidak ditemukan');
        }
    
        $pdfDir = storage_path('app/public/docs');
        if (!file_exists($pdfDir)) {
            mkdir($pdfDir, 0755, true);
        }
    
        $pdfFilename = pathinfo($record->file_path, PATHINFO_FILENAME) . '.pdf';
        $pdfPath = $pdfDir . '/' . $pdfFilename;
    
        try {
            // Load Excel file
            $spreadsheet = IOFactory::load($excelPath);
            $worksheet = $spreadsheet->getActiveSheet();
    
            // Ganti font Aptos dengan font standar yang tersedia di server
            $cellCollection = $worksheet->getCellCollection();
            foreach ($cellCollection as $cellCoordinate) {
                $cell = $worksheet->getCell($cellCoordinate);
                $style = $worksheet->getStyle($cellCoordinate);
                $font = $style->getFont();
                
                // Jika font adalah Aptos atau similar, ganti dengan Arial atau font standar
                $fontName = $font->getName();
                if (stripos($fontName, 'Aptos') !== false || 
                    stripos($fontName, 'Calibri') !== false ||
                    stripos($fontName, 'Segoe') !== false) {
                    $font->setName('Arial'); // atau 'Helvetica', 'DejaVu Sans'
                }
            }
    
            // Atur orientation landscape
            $pageSetup = $worksheet->getPageSetup();
            $pageSetup->setOrientation(
                \PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE
            );
            $pageSetup->setFitToWidth(1);
            $pageSetup->setFitToHeight(1);
    
            // Margin kecil
            $margins = $worksheet->getPageMargins();
            $margins->setTop(0.3);
            $margins->setRight(0.3);
            $margins->setLeft(1);
            $margins->setBottom(0.3);
    
            // Save as PDF
            $writer = IOFactory::createWriter($spreadsheet, 'Mpdf');
            $writer->save($pdfPath);
    
            $record->update(['pdf_path' => 'docs/' . $pdfFilename]);
    
            return response()->file($pdfPath);
    
        } catch (\Exception $e) {
            \Log::error('PDF Conversion Error: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengkonversi PDF: ' . $e->getMessage()], 500);
        }
    }

    // Fungsi cetak / download
    public function download($id)
    {
        $record = Anggaran::findOrFail($id);
        $filePath = storage_path('app/public/' . $record->file_path);

        if (!file_exists($filePath)) {
            abort(404, 'File tidak ditemukan');
        }

        return response()->download($filePath, "Anggaran_Ditressiber_Polda_Jatim_Tahun-{$record->tahun_anggaran}." . pathinfo($record->file_path, PATHINFO_EXTENSION));
    }

}
