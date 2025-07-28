<?php
session_start();
require 'db.php';

// ✅ Chặn truy cập nếu không phải quản trị viên
if (!isset($_SESSION["user_id"]) || $_SESSION["user_role"] !== 'quan_tri_vien') {
    header("Location: login.html");
    exit();
}

// ✅ Xử lý yêu cầu xóa điểm rác (nếu có)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_id"])) {
    $delete_id = intval($_POST["delete_id"]);
    $stmt = $conn->prepare("DELETE FROM trash_points WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Quản lý điểm rác đã xử lý</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f4f4f4; }
        .btn-delete {
            background: #dc3545;
            color: white;
            border: none;
            padding: 6px 12px;
            cursor: pointer;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h2>📋 Quản lý điểm rác đã được AI xác thực</h2>

    <p><a href="index.php">← Quay lại trang chính</a></p>

    <?php
    // ✅ Lấy tất cả điểm rác đã được AI xác thực
    $sql = "SELECT * FROM trash_points WHERE ai_verified = 1";
    $result = $conn->query($sql);

    if ($result->num_rows === 0): ?>
        <p>Không có điểm rác nào đã được AI xác thực.</p>
    <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Vị trí</th>
                <th>Mô tả</th>
                <th>Ảnh</th>
                <th>Hành động</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= $row['latitude'] ?>, <?= $row['longitude'] ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td>
                    <?php if ($row['image_url']): ?>
                        <img src="<?= $row['image_url'] ?>" width="100">
                    <?php else: ?>
                        Không có ảnh
                    <?php endif; ?>
                </td>
                <td>
                    <form method="post" onsubmit="return confirm('Bạn có chắc muốn xóa điểm này?');">
                        <input type="hidden" name="delete_id" value="<?= $row['id'] ?>">
                        <button type="submit" class="btn-delete">🗑️ Xóa</button>
                    </form>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>
</body>
</html>
