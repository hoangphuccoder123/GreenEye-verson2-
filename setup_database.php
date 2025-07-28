<?php
/**
 * Database Setup Script
 * Run this script once to ensure all necessary columns exist in the trash_points table
 */

require 'db.php';

function addColumnIfNotExists($conn, $table, $column, $definition) {
    // Check if column exists
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    if ($result->num_rows === 0) {
        // Column doesn't exist, add it
        $sql = "ALTER TABLE $table ADD COLUMN $column $definition";
        if ($conn->query($sql)) {
            echo "✅ Đã thêm cột '$column' vào bảng '$table'<br>";
        } else {
            echo "❌ Lỗi khi thêm cột '$column': " . $conn->error . "<br>";
        }
    } else {
        echo "ℹ️ Cột '$column' đã tồn tại trong bảng '$table'<br>";
    }
}

function addIndexIfNotExists($conn, $table, $indexName, $columns) {
    // Check if index exists
    $result = $conn->query("SHOW INDEX FROM $table WHERE Key_name = '$indexName'");
    if ($result->num_rows === 0) {
        // Index doesn't exist, add it
        $sql = "CREATE INDEX $indexName ON $table ($columns)";
        if ($conn->query($sql)) {
            echo "✅ Đã tạo index '$indexName' cho bảng '$table'<br>";
        } else {
            echo "❌ Lỗi khi tạo index '$indexName': " . $conn->error . "<br>";
        }
    } else {
        echo "ℹ️ Index '$indexName' đã tồn tại trong bảng '$table'<br>";
    }
}

echo "<h2>🔧 Thiết lập cơ sở dữ liệu - Green Eye AI</h2>";
echo "<hr>";

// Check if trash_points table exists
$result = $conn->query("SHOW TABLES LIKE 'trash_points'");
if ($result->num_rows === 0) {
    echo "❌ Bảng 'trash_points' không tồn tại. Vui lòng tạo bảng cơ bản trước.<br>";
    exit();
}

echo "<h3>Kiểm tra và thêm các cột cần thiết:</h3>";

// Add image_url column if not exists
addColumnIfNotExists($conn, 'trash_points', 'image_url', 'VARCHAR(255) NULL');

// Add ai_analysis column if not exists
addColumnIfNotExists($conn, 'trash_points', 'ai_analysis', 'TEXT NULL');

// Add ai_verified column if not exists
addColumnIfNotExists($conn, 'trash_points', 'ai_verified', 'BOOLEAN DEFAULT FALSE');

echo "<h3>Kiểm tra và tạo các index:</h3>";

// Add indexes for optimization
addIndexIfNotExists($conn, 'trash_points', 'idx_user_ai_verified', 'user_id, ai_verified');
addIndexIfNotExists($conn, 'trash_points', 'idx_ai_verified', 'ai_verified');

echo "<hr>";
echo "<h3>📊 Cấu trúc bảng hiện tại:</h3>";

// Show current table structure
$result = $conn->query("DESCRIBE trash_points");
if ($result) {
    echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
    echo "<tr><th>Tên cột</th><th>Kiểu dữ liệu</th><th>Null</th><th>Key</th><th>Mặc định</th><th>Extra</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['Field'] . "</td>";
        echo "<td>" . $row['Type'] . "</td>";
        echo "<td>" . $row['Null'] . "</td>";
        echo "<td>" . $row['Key'] . "</td>";
        echo "<td>" . $row['Default'] . "</td>";
        echo "<td>" . $row['Extra'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}

echo "<hr>";
echo "<h3>✅ Thiết lập hoàn tất!</h3>";
echo "<p>Bây giờ bạn có thể sử dụng ứng dụng mà không gặp lỗi về cột thiếu.</p>";
echo "<p><a href='index.php'>← Quay lại trang chủ</a></p>";

$conn->close();
?>
