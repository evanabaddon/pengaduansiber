<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;
use DeepSeek\DeepSeekClient;


class DeepSeekService
{
    protected $baseUrl;
    protected $model;
    protected $apiKey;
    protected $timeout;
    
    public function __construct()
    {
        $this->baseUrl = config('services.deepseek.url', env('DEEPSEEK_API_BASE_URL', 'https://api.deepseek.com/v1'));
        $this->model = config('services.deepseek.model', env('DEEPSEEK_MODEL', 'deepseek-chat'));
        $this->apiKey = config('services.deepseek.api_key', env('DEEPSEEK_API_KEY'));
        $this->timeout = env('DEEPSEEK_API_TIMEOUT', 360);
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
            'messages' => [
                [
                    'role' => 'system',
                    'content' => 'Anda adalah asisten hukum khusus untuk analisis kasus cyber crime. Berikan respons dalam format JSON yang valid tanpa markdown.'
                ],
                [
                    'role' => 'user',
                    'content' => $prompt
                ]
            ],
            'temperature' => 0.7,
            'max_tokens' => 4096,
            'top_p' => 0.9,
            'response_format' => ['type' => 'json_object'],
            'stream' => false
        ];
    }

    public function analyze($data)
    {
        // Increase PHP execution time limit for this request
        set_time_limit(600); // Set to 10 minutes

        try {
            Log::info('Starting DeepSeek analysis with data', ['data' => $data]);
            
            $deepseek = app(DeepSeekClient::class);
            
            // Set the timeout for the HTTP client
            if (method_exists($deepseek, 'setTimeout')) {
                $deepseek->setTimeout($this->timeout);
                Log::info('Set timeout to', ['timeout' => $this->timeout]);
            } else {
                // Alternative approach if setTimeout doesn't exist
                $httpClient = Http::timeout($this->timeout);
                if (method_exists($deepseek, 'setHttpClient')) {
                    $deepseek->setHttpClient($httpClient);
                    Log::info('Set HTTP client with timeout', ['timeout' => $this->timeout]);
                }
            }
            
            // Generate prompt with actual case data
            $promptData = $this->generatePrompt($data);
            Log::info('Generated prompt data', ['promptData' => $promptData]);
            
            // Call the API with the system message and user prompt
            Log::info('Calling DeepSeek API with system and user messages');
            $response = $deepseek
                ->query($promptData['messages'][0]['content'], 'system')
                ->query($promptData['messages'][1]['content'], 'user')
                ->withModel($this->model)
                ->setTemperature($promptData['temperature'])
                ->run();
            
            Log::info('Raw API response', ['response' => $response]);
            
            // Convert the string response to an array
            $responseData = json_decode($response, true);
            Log::info('Parsed API response', ['responseData' => $responseData]);
            
            // Debug the structure of the response
            if (isset($responseData['choices'])) {
                Log::info('Found choices in response', ['choices_count' => count($responseData['choices'])]);
            } else {
                Log::warning('No choices found in response');
            }
            
            if (isset($responseData['choices'][0]['message'])) {
                Log::info('Found message in first choice', ['message' => $responseData['choices'][0]['message']]);
            } else {
                Log::warning('No message found in first choice');
            }
            
            // Extract the content from the DeepSeek response format
            $content = null;
            if (isset($responseData['choices'][0]['message']['content'])) {
                $rawContent = $responseData['choices'][0]['message']['content'];
                Log::info('Raw content from response', ['content' => $rawContent]);
                
                try {
                    // The content might be a JSON string, try to decode it
                    $content = json_decode($rawContent, true);
                    
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        Log::warning('Failed to parse content as JSON', ['error' => json_last_error_msg()]);
                        
                        // Try to clean up the content - sometimes there might be markdown or extra characters
                        $cleanedContent = preg_replace('/```json|```/', '', $rawContent);
                        Log::info('Cleaned content for parsing', ['cleaned' => $cleanedContent]);
                        
                        $content = json_decode($cleanedContent, true);
                        
                        if (json_last_error() !== JSON_ERROR_NONE) {
                            Log::warning('Failed to parse cleaned content as JSON', ['error' => json_last_error_msg()]);
                            $content = $rawContent; // Use raw string as fallback
                        } else {
                            Log::info('Successfully parsed cleaned content');
                        }
                    } else {
                        Log::info('Successfully parsed content as JSON');
                    }
                } catch (\Exception $e) {
                    Log::error('Error while parsing response content', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ]);
                    $content = $rawContent; // Use raw string as fallback
                }
            } else {
                Log::warning('No content found in message');
            }
            
            // Verify we have the required fields
            if (is_array($content)) {
                $requiredFields = ['ringkasan_kronologi', 'analisis_hukum', 'langkah_penyidikan', 'tingkat_urgensi'];
                $missingFields = [];
                
                foreach ($requiredFields as $field) {
                    if (!isset($content[$field])) {
                        $missingFields[] = $field;
                    }
                }
                
                if (!empty($missingFields)) {
                    Log::warning('Missing required fields in response', ['missing' => $missingFields]);
                } else {
                    Log::info('All required fields present in response');
                }
                
                // Debug the entire content structure
                Log::info('Content structure', ['keys' => array_keys($content)]);
            } else {
                Log::warning('Content is not an array, cannot check fields');
            }
            
            // Return a standardized response format
            $result = [
                'success' => true,
                'data' => $content,
                'raw_response' => $response
            ];
            
            Log::info('Returning final result', ['result' => $result]);
            return $result;
                
        } catch (\Exception $e) {
            // Log the error and return a formatted error response
            Log::error('DeepSeek API Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);
            
            return [
                'success' => false,
                'message' => 'Error processing request: ' . $e->getMessage(),
                'error' => $e->getMessage(),
                'code' => $e->getCode()
            ];
        }
    }
}