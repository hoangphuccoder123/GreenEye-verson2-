<?php
require 'lm_service.php';

echo "<h2>Test LMStudio Connection</h2>";

$lmService = new LMStudioService();

// Test 1: Kiểm tra health
echo "<h3>1. Health Check</h3>";
$isHealthy = $lmService->isHealthy();
echo "Status: " . ($isHealthy ? "✅ LMStudio đang hoạt động" : "❌ LMStudio không kết nối được") . "<br><br>";

// Test 2: Test với ảnh mẫu (nếu có)
echo "<h3>2. Image Analysis Test</h3>";
if (isset($_FILES['test_image']) && $_FILES['test_image']['error'] === UPLOAD_ERR_OK) {
    $imageData = file_get_contents($_FILES['test_image']['tmp_name']);
    $imageBase64 = base64_encode($imageData);
    
    echo "Đang phân tích ảnh...<br>";
    $startTime = microtime(true);
    
    $result = $lmService->analyzeImage($imageBase64);
    
    $endTime = microtime(true);
    $processingTime = round(($endTime - $startTime) * 1000, 2);
    
    echo "Thời gian xử lý: {$processingTime}ms<br>";
    
    if ($result) {
        echo "✅ Kết quả phân tích:<br>";
        echo "<pre>" . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    } else {
        echo "❌ Không thể phân tích ảnh";
    }
} else {
    echo "Tải lên ảnh để test:<br>";
    echo '<form method="post" enctype="multipart/form-data">';
    echo '<input type="file" name="test_image" accept="image/*" required>';
    echo '<button type="submit">Test Phân Tích Ảnh</button>';
    echo '</form>';
}

echo "<br><h3>3. Configuration Info</h3>";
echo "API URL: http://ai.vnpthaiphong.vn:1234/v1/chat/completions<br>";
echo "Model: google/gemma-3-4b<br>";
echo "Temperature: 0.3<br>";
echo "Timeout: 30s<br>";

echo "<br><a href='index.php'>← Quay lại trang chủ</a>";
?>
