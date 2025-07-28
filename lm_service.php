<?php
require_once 'config_timeout.php';

class LMStudioService {
    private $apiUrl;
    private $model;
    
    public function __construct($apiUrl = 'http://ai.vnpthaiphong.vn:1234/v1/chat/completions', $model = 'google/gemma-3-4b') {
        $this->apiUrl = $apiUrl;
        $this->model = $model;
    }
    
    /**
     * Phân tích ảnh qua LMStudio
     * @param string $imageBase64 - Ảnh dạng base64
     * @return array|null - Kết quả phân tích hoặc null nếu lỗi
     */
    public function analyzeImage($imageBase64) {
        try {
            $imageUrl = "data:image/jpeg;base64," . $imageBase64;
            
            $payload = [
                'model' => $this->model,
                'temperature' => 0.3,
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => 'You are an environmental expert specialized in analyzing urban waste images. Given the uploaded image, respond in the following JSON format only: { "image_type": "trash" | "irrelevant", "pollution_level": 1 | 2 | 3 | 4 | null } Rules: - If the image contains garbage (household waste, illegal dumping, open trash sites, etc.), set "image_type" to "trash". - Otherwise, if the image is irrelevant (selfie, spam, clean landscape, meme, etc.), set "image_type" to "irrelevant". Pollution level (only if "image_type" is "trash"): 1: Very minimal trash. 2: Small amount of trash in a localized area. 3: Medium amount of trash with moderate environmental impact. 4: Large or severe trash pollution with major environmental impact. If image_type is "irrelevant", pollution_level must be null. Return only the JSON. No explanations or extra text. No markdown or code blocks.'
                    ],
                    [
                        'role' => 'user',
                        'content' => [
                            [
                                'type' => 'text',
                                'text' => 'Please analyze this image.'
                            ],
                            [
                                'type' => 'image_url',
                                'image_url' => [
                                    'url' => $imageUrl
                                ]
                            ]
                        ]
                    ]
                ]
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->apiUrl);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json'
            ]);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, API_TIMEOUT); // Timeout từ config
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, API_CONNECT_TIMEOUT); // Connection timeout từ config
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $error = curl_error($ch);
            curl_close($ch);
            
            if ($error) {
                error_log("LMStudio CURL Error: " . $error);
                return null;
            }
            
            if ($httpCode !== 200) {
                error_log("LMStudio HTTP Error: " . $httpCode . " - " . $response);
                return null;
            }
            
            $result = json_decode($response, true);
            if (!$result || !isset($result['choices'][0]['message']['content'])) {
                error_log("LMStudio Invalid Response: " . $response);
                return null;
            }
            
            // Parse JSON response từ LMStudio
            $analysisJson = trim($result['choices'][0]['message']['content']);
            
            // Xử lý trường hợp AI trả về với markdown code block
            if (strpos($analysisJson, '```json') !== false) {
                $analysisJson = preg_replace('/```json\s*/', '', $analysisJson);
                $analysisJson = preg_replace('/\s*```/', '', $analysisJson);
                $analysisJson = trim($analysisJson);
            }
            
            $analysis = json_decode($analysisJson, true);
            
            if (!$analysis || !isset($analysis['image_type'])) {
                error_log("LMStudio Invalid Analysis JSON: " . $analysisJson);
                return null;
            }
            
            return $analysis;
            
        } catch (Exception $e) {
            error_log("LMStudio Exception: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Kiểm tra xem LMStudio có hoạt động không
     * @return bool
     */
    public function isHealthy() {
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, str_replace('/v1/chat/completions', '/health', $this->apiUrl));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);
            
            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            return $httpCode === 200;
        } catch (Exception $e) {
            return false;
        }
    }
}
?>
