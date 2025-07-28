<?php
require_once 'config_timeout.php';
set_time_limit(PHP_EXECUTION_TIMEOUT);
ini_set('max_execution_time', PHP_EXECUTION_TIMEOUT);

session_start();
if (!isset($_SESSION["user_id"])) {
    die("Bạn cần đăng nhập.");
}

require 'db.php';
require 'lm_service.php';

// Kiểm tra các cột có tồn tại hay không
function checkColumnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result && $result->num_rows > 0;
}

$has_ai_analysis = checkColumnExists($conn, 'trash_points', 'ai_analysis');
$has_ai_verified = checkColumnExists($conn, 'trash_points', 'ai_verified');
$has_image_url   = checkColumnExists($conn, 'trash_points', 'image_url');
$has_verified    = checkColumnExists($conn, 'trash_points', 'verified');
$has_image_hash  = checkColumnExists($conn, 'trash_points', 'image_hash');

$user_id    = $_SESSION["user_id"];
$lat        = $_POST["lat"] ?? null;
$lng        = $_POST["lng"] ?? null;
$description = $_POST["description"] ?? "";

if (!$lat || !$lng) {
    die("Thiếu tọa độ lat hoặc lng trong yêu cầu.");
}

$image_url   = null;
$image_hash  = null;
$ai_analysis = null;
$ai_verified = false;
$verified    = false;

if (isset($_FILES["image"]) && $_FILES["image"]["error"] === UPLOAD_ERR_OK) {
    if (!is_dir("uploads")) {
        mkdir("uploads", 0755, true);
    }

    $ext = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $filename = "uploads/" . uniqid() . "." . $ext;

    if (move_uploaded_file($_FILES["image"]["tmp_name"], $filename)) {
        $image_url = $filename;

        // Tính hash ảnh
        $imageData = file_get_contents($filename);
        $image_hash = md5($imageData);

        // 🔍 Kiểm tra ảnh đã đăng chưa
        if ($has_image_hash) {
            $checkStmt = $conn->prepare("SELECT id FROM trash_points WHERE user_id = ? AND image_hash = ?");
            $checkStmt->bind_param("is", $user_id, $image_hash);
            $checkStmt->execute();
            $checkStmt->store_result();
            if ($checkStmt->num_rows > 0) {
                unlink($filename);
                echo "<script>alert('Bạn đã đăng ảnh này trước đó.'); window.location.href = 'index.php';</script>";
                exit();
            }
            $checkStmt->close();
        }

        // 🧠 Gọi AI phân tích ảnh
        $lmService = new LMStudioService();
        try {
            $imageBase64 = base64_encode($imageData);
            $analysis = $lmService->analyzeImage($imageBase64);
            if ($analysis) {
                $ai_analysis = json_encode($analysis);
                $ai_verified = true;

                if ($analysis['image_type'] === 'irrelevant') {
                    unlink($filename);
                    echo "<script>alert('Ảnh không hợp lệ. Vui lòng chọn ảnh có chứa rác.'); window.location.href = 'index.php';</script>";
                    exit();
                }

                if ($analysis['image_type'] === 'trash' && isset($analysis['pollution_level'])) {
                    $pollutionTexts = [
                        1 => "Rác thải rất ít",
                        2 => "Rác thải ít ở khu vực nhỏ",
                        3 => "Rác thải vừa phải với tác động môi trường trung bình",
                        4 => "Rác thải nhiều với tác động môi trường nghiêm trọng"
                    ];
                    $aiDescription = "[AI: " . $pollutionTexts[$analysis['pollution_level']] . "]";
                    $description  .= " " . $aiDescription;
                }
            }
        } catch (Exception $e) {
            error_log("LMStudio Error: " . $e->getMessage());
        }
    }
}

// 📝 Chèn dữ liệu vào bảng trash_points
$query = "INSERT INTO trash_points (user_id, latitude, longitude, description";
$params = "idds";
$values = [$user_id, $lat, $lng, $description];

if ($image_url !== null) {
    $query .= ", image_url";
    $params .= "s";
    $values[] = $image_url;
}
if ($has_image_hash && $image_hash !== null) {
    $query .= ", image_hash";
    $params .= "s";
    $values[] = $image_hash;
}
if ($has_ai_analysis && $ai_analysis !== null) {
    $query .= ", ai_analysis";
    $params .= "s";
    $values[] = $ai_analysis;
}
if ($has_ai_verified) {
    $query .= ", ai_verified";
    $params .= "b";
    $values[] = $ai_verified;
}
if ($has_verified) {
    $query .= ", verified";
    $params .= "b";
    $values[] = $verified;
}

$query .= ") VALUES (" . rtrim(str_repeat("?, ", count($values)), ", ") . ")";
$stmt = $conn->prepare($query);
$stmt->bind_param($params, ...$values);
$stmt->execute();
$stmt->close();

// ✅ Cộng điểm
$diem_cong = 10;
$stmt = $conn->prepare("UPDATE users SET social_points = social_points + ? WHERE id = ?");
$stmt->bind_param("ii", $diem_cong, $user_id);
$stmt->execute();
$stmt->close();

$conn->close();
header("Location: index.php");
exit();
?>
