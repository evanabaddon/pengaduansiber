<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Exception;

class OllamaService
{
    protected $baseUrl;
    protected $model;
    
    public function __construct()
    {
        $this->baseUrl = config('services.ollama.url', 'http://localhost:11434');
        $this->model = config('services.ollama.model', 'llama2');
    }

    protected function generatePrompt($data)
    {
        $prompt = <<<PROMPT
        PELAPOR: {$data['pelapor']}
        KORBAN: {$data['korban']}
        TERLAPOR: {$data['terlapor']}
        KRONOLOGI: {$data['uraian_peristiwa']}
        PERKARA: {$data['perkara']}
        LOKASI: {$data['tkp']}

        Format JSON yang diharapkan:
        {
            "ringkasan_kronologi": "string berisi analisa kasus secara detail dan terstruktur. Jawab dengan awalan 'Menurut SiberBot, Kasus ini ...'",
            "analisis_hukum": {
                "pidana_umum": "string berisi analisis KUHP",
                "teknologi_informasi": "string berisi analisis UU ITE",
                "perundangan_lain": "string berisi analisis hukum lain"
            },
            "langkah_penyidikan": {
                "barang_bukti_digital": {
                    "data_komunikasi": "Berdasarkan kasus ini, langkah pengamanan data komunikasi yang perlu dilakukan adalah: ...",
                    "data_finansial": "Untuk mendapatkan data finansial, perlu dilakukan: ...",
                    "data_perangkat": "Pengamanan perangkat yang perlu dilakukan adalah: ...",
                    "data_pendukung": "Dokumen pendukung yang diperlukan: ..."
                },
                "analisis_forensik": {
                    "akuisisi_data": "Proses akuisisi data yang harus dilakukan: ...",
                    "pemeriksaan_teknis": "Pemeriksaan teknis yang diperlukan: ...",
                    "dokumentasi_digital": "Dokumentasi yang perlu dilakukan: ..."
                },
                "penelusuran_pelaku": {
                    "jejak_digital": "Penelusuran jejak digital dilakukan dengan dan menggunakan: ...",
                    "aset_digital": "Penelusuran aset digital meliputi: ...",
                    "identifikasi_pelaku": "Untuk mengidentifikasi pelaku perlu dilakukan dan dengan menggunakan tool: ..."
                },
                "tindakan_penyidikan": {
                    "pengamanan_tkp": "Pengamanan TKP yang perlu dilakukan: ...",
                    "penggeledahan": "Proses penggeledahan meliputi: ...",
                    "penyitaan": "Tindakan penyitaan yang diperlukan: ...",
                    "penangkapan": "Proses penangkapan yang harus dilakukan: ..."
                }
            },
            "tingkat_urgensi": {
                "dampak_kejadian": {
                    "level": "string (Tinggi/Sedang/Rendah)",
                    "analisis": "string"
                },
                "nilai_kerugian": {
                    "level": "string (Tinggi/Sedang/Rendah)",
                    "analisis": "string"
                },
                "tingkat_kompleksitas": {
                    "level": "string (Tinggi/Sedang/Rendah)",
                    "analisis": "string"
                },
                "potensi_dampak": {
                    "level": "string (Tinggi/Sedang/Rendah)",
                    "analisis": "string"
                }
            }
        }
        CATATAN PENTING:
        - Jangan menambahkan informasi yang tidak ada dalam uraian
        - Jangan membuat asumsi tentang kerugian jika tidak disebutkan
        - Jangan mencantumkan rekening jika tidak ada dalam uraian
        - Fokus pada fakta yang tertulis saja
        PROMPT;

        return [
            'model' => $this->model,
            'prompt' => $prompt,
            'format' => 'json',
            'stream' => false,
            'max_tokens' => 4096,
            'temperature' => 1,
            'top_p' => 0.9,
            'top_k' => 40,
            'stop' => null,
            'seed' => null,
            'num_predict' => 100,
            'return_metadata' => false,
            'return_prompt' => false,
            'return_runtime' => false,
        ];
    }

    public function analyze($data)
    {
        try {
            $prompt = $this->generatePrompt($data);
            
            $response = Http::timeout(120)
                ->post("{$this->baseUrl}/api/generate", $prompt);
            
            if ($response->successful()) {
                $result = $response->json();
                
                if (!empty($result['response'])) {
                    // Parse JSON string dari response
                    $parsedResponse = json_decode($result['response'], true);
                    
                    if (json_last_error() === JSON_ERROR_NONE) {
                        return [
                            'success' => true,
                            'data' => $parsedResponse
                        ];
                    }
                    
                    \Log::error('JSON Parse Error', [
                        'error' => json_last_error_msg(),
                        'response' => $result['response']
                    ]);
                }
                
                \Log::warning('Empty or Invalid Response', [
                    'result' => $result
                ]);
                
                return [
                    'success' => false,
                    'message' => 'Invalid or empty response from Ollama',
                    'raw_response' => $result
                ];
            }
            
            \Log::error('Ollama Error', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);
            
            return [
                'success' => false,
                'message' => 'Failed to get response from Ollama',
                'error' => $response->body()
            ];
            
        } catch (Exception $e) {
            \Log::error('Ollama Exception', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error processing analysis',
                'error' => $e->getMessage()
            ];
        }
    }
} 