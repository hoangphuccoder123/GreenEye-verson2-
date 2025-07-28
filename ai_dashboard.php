<?php
session_start();
require 'db.php';
require 'lm_service.php';

// Function to check if columns exist in table
function checkColumnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}

// Check if AI columns exist
$has_ai_analysis = checkColumnExists($conn, 'trash_points', 'ai_analysis');
$has_ai_verified = checkColumnExists($conn, 'trash_points', 'ai_verified');

// Kiểm tra quyền admin (cần role = 'quan_tri_vien')
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== 'quan_tri_vien') {
    die("Bạn không có quyền truy cập trang này.");
}

$lmService = new LMStudioService();
$isLMStudioHealthy = $lmService->isHealthy();

// Thống kê AI analysis
$stats = [];

// Tổng số điểm rác
$result = $conn->query("SELECT COUNT(*) as total FROM trash_points");
$stats['total'] = $result->fetch_assoc()['total'];

// Chỉ thực hiện thống kê AI nếu có columns cần thiết
if ($has_ai_verified && $has_ai_analysis) {
    // Số điểm đã được AI phân tích
    $result = $conn->query("SELECT COUNT(*) as ai_analyzed FROM trash_points WHERE ai_verified = 1");
    $stats['ai_analyzed'] = $result->fetch_assoc()['ai_analyzed'];

    // Số điểm AI xác nhận là rác
    $result = $conn->query("SELECT COUNT(*) as ai_confirmed_trash FROM trash_points WHERE ai_verified = 1 AND JSON_EXTRACT(ai_analysis, '$.image_type') = 'trash'");
    $stats['ai_confirmed_trash'] = $result->fetch_assoc()['ai_confirmed_trash'];

    // Số điểm AI từ chối (irrelevant)
    $result = $conn->query("SELECT COUNT(*) as ai_rejected FROM trash_points WHERE ai_verified = 1 AND JSON_EXTRACT(ai_analysis, '$.image_type') = 'irrelevant'");
    $stats['ai_rejected'] = $result->fetch_assoc()['ai_rejected'];

    // Phân bố mức độ ô nhiễm
    $pollutionLevels = [];
    for ($i = 1; $i <= 4; $i++) {
        $result = $conn->query("SELECT COUNT(*) as count FROM trash_points WHERE ai_verified = 1 AND JSON_EXTRACT(ai_analysis, '$.pollution_level') = $i");
        $pollutionLevels[$i] = $result->fetch_assoc()['count'];
    }

    // Lấy danh sách điểm rác gần đây có AI analysis
    $recentAnalyzed = $conn->query("
        SELECT tp.*, u.name as user_name 
        FROM trash_points tp 
        JOIN users u ON tp.user_id = u.id 
        WHERE tp.ai_verified = 1 
        ORDER BY tp.id DESC 
        LIMIT 10
    ");
} else {
    // Nếu không có AI columns, set default values
    $stats['ai_analyzed'] = 0;
    $stats['ai_confirmed_trash'] = 0;
    $stats['ai_rejected'] = 0;
    $pollutionLevels = [1 => 0, 2 => 0, 3 => 0, 4 => 0];
    $recentAnalyzed = false;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Admin - AI Analysis Dashboard</title>
    <style>
        body { font-family: sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; }
        .header { background: #2c3e50; color: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .stat-number { font-size: 2.5em; font-weight: bold; color: #3498db; }
        .stat-label { color: #666; margin-top: 5px; }
        .status-indicator { display: inline-block; padding: 5px 15px; border-radius: 20px; font-size: 0.9em; }
        .status-healthy { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
        .table { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .table table { width: 100%; border-collapse: collapse; }
        .table th, .table td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        .table th { background: #f8f9fa; font-weight: bold; }
        .pollution-level { padding: 3px 8px; border-radius: 4px; font-size: 0.85em; }
        .level-1 { background: #d1ecf1; color: #0c5460; }
        .level-2 { background: #fff3cd; color: #856404; }
        .level-3 { background: #f8d7da; color: #721c24; }
        .level-4 { background: #d1b3e0; color: #5a2d6b; }
        .image-preview { max-width: 80px; max-height: 60px; border-radius: 4px; }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1>🤖 AI Analysis Dashboard</h1>
        <p>Quản lý và theo dõi hiệu suất phân tích AI cho hệ thống báo cáo điểm rác</p>
        <div>
            LMStudio Status: 
            <span class="status-indicator <?= $isLMStudioHealthy ? 'status-healthy' : 'status-error' ?>">
                <?= $isLMStudioHealthy ? '✅ Hoạt động' : '❌ Không kết nối được' ?>
            </span>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-number"><?= $stats['total'] ?></div>
            <div class="stat-label">Tổng điểm rác</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $stats['ai_analyzed'] ?></div>
            <div class="stat-label">Đã phân tích AI</div>
            <div style="font-size: 0.9em; color: #666; margin-top: 5px;">
                <?= $stats['total'] > 0 ? round($stats['ai_analyzed'] / $stats['total'] * 100, 1) : 0 ?>% tổng số
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $stats['ai_confirmed_trash'] ?></div>
            <div class="stat-label">AI xác nhận là rác</div>
            <div style="font-size: 0.9em; color: #666; margin-top: 5px;">
                <?= $stats['ai_analyzed'] > 0 ? round($stats['ai_confirmed_trash'] / $stats['ai_analyzed'] * 100, 1) : 0 ?>% đã phân tích
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-number"><?= $stats['ai_rejected'] ?></div>
            <div class="stat-label">AI từ chối (không phải rác)</div>
            <div style="font-size: 0.9em; color: #666; margin-top: 5px;">
                <?= $stats['ai_analyzed'] > 0 ? round($stats['ai_rejected'] / $stats['ai_analyzed'] * 100, 1) : 0 ?>% đã phân tích
            </div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <h3>Phân bố mức độ ô nhiễm</h3>
            <?php 
            $levelLabels = [
                1 => 'Rất nhẹ',
                2 => 'Nhẹ', 
                3 => 'Trung bình',
                4 => 'Nghiêm trọng'
            ];
            ?>
            <?php foreach ($pollutionLevels as $level => $count): ?>
                <div style="margin: 8px 0;">
                    <span class="pollution-level level-<?= $level ?>">
                        Mức <?= $level ?> - <?= $levelLabels[$level] ?>
                    </span>
                    <strong><?= $count ?></strong> điểm
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="table">
        <h3>Điểm rác gần đây đã được AI phân tích</h3>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Người báo cáo</th>
                    <th>Mô tả</th>
                    <th>Vị trí</th>
                    <th>Ảnh</th>
                    <th>AI Analysis</th>
                    <th>Mức độ ô nhiễm</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($recentAnalyzed && $recentAnalyzed->num_rows > 0): ?>
                    <?php while ($row = $recentAnalyzed->fetch_assoc()): 
                        $aiData = json_decode($row['ai_analysis'], true);
                    ?>
                        <tr>
                            <td><?= $row['id'] ?></td>
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td><?= round($row['latitude'], 6) ?>, <?= round($row['longitude'], 6) ?></td>
                            <td>
                                <?php if ($row['image_url']): ?>
                                <img src="<?= $row['image_url'] ?>" class="image-preview" alt="Trash point">
                            <?php else: ?>
                                <em>Không có ảnh</em>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($aiData['image_type'] === 'trash'): ?>
                                <span style="color: green;">✅ Xác nhận rác</span>
                            <?php else: ?>
                                <span style="color: red;">❌ Không phải rác</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($aiData['image_type'] === 'trash' && isset($aiData['pollution_level'])): ?>
                                <span class="pollution-level level-<?= $aiData['pollution_level'] ?>">
                                    Mức <?= $aiData['pollution_level'] ?>
                                </span>
                            <?php else: ?>
                                <em>N/A</em>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">
                            <em>Chưa có dữ liệu AI analysis hoặc thiếu cấu trúc bảng</em>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div style="margin-top: 30px; text-align: center;">
        <a href="index.php" style="padding: 10px 20px; background: #3498db; color: white; text-decoration: none; border-radius: 5px;">
            ← Quay lại trang chủ
        </a>
    </div>
</div>

</body>
</html>
