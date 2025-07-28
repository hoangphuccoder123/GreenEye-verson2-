<?php
require_once 'db.php';
session_start();

// ✅ Kiểm tra quyền admin nếu cần
// if ($_SESSION['role'] !== 'admin') {
//     die("Bạn không có quyền truy cập trang này");
// }

// ✅ Duyệt hoặc từ chối
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $action = $_GET['action'];

    // Lấy thông tin đơn
    $query = "SELECT * FROM admin_applications WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $app = $result->fetch_assoc();

    if ($app && $app['status'] === 'pending') {
        if ($action === 'approve') {
            // ✅ Chèn vào bảng users với role = 'admin'
            $defaultPhone = '';
            $points = 0;

            $insert = $conn->prepare("
                INSERT INTO users (name, email, password, phone, date_of_birth, points, role)
                VALUES (?, ?, ?, ?, ?, ?, 'admin')
            ");
            $insert->bind_param("sssssi", 
                $app['username'], 
                $app['email'], 
                $app['password'], 
                $defaultPhone, 
                $app['dob'], 
                $points
            );
            $insert->execute();

            // ✅ Cập nhật đơn đã duyệt
            $update = $conn->prepare("UPDATE admin_applications SET status = 'approved' WHERE id = ?");
            $update->bind_param("i", $id);
            $update->execute();

            echo "<p style='color: green;'>✅ Đã duyệt đơn và thêm người dùng làm quản trị viên.</p>";
        } elseif ($action === 'reject') {
            $update = $conn->prepare("UPDATE admin_applications SET status = 'rejected' WHERE id = ?");
            $update->bind_param("i", $id);
            $update->execute();

            echo "<p style='color: red;'>❌ Đã từ chối đơn.</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠️ Đơn này không tồn tại hoặc đã được xử lý.</p>";
    }
}

// ✅ Lấy danh sách đơn chờ duyệt
$sql = "SELECT * FROM admin_applications WHERE status = 'pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Duyệt đơn quản trị viên</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(120deg, #e0ffe8 0%, #f7f9fb 100%);
            min-height: 100vh;
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 1100px;
            margin: 40px auto;
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 8px 32px rgba(46,204,113,0.13);
            padding: 36px 32px 32px 32px;
        }
        h2 {
            color: #27ae60;
            text-align: center;
            margin-bottom: 32px;
            font-size: 2.1rem;
            letter-spacing: 1px;
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 18px rgba(46,204,113,0.08);
        }
        th, td {
            padding: 16px 12px;
            border-bottom: 1px solid #f1f1f1;
            text-align: left;
            font-size: 1.05rem;
        }
        th {
            background: linear-gradient(90deg, #2ecc71 80%, #27ae60 100%);
            color: #fff;
            font-size: 1.13rem;
            letter-spacing: 0.5px;
        }
        tr:last-child td {
            border-bottom: none;
        }
        tr:hover {
            background: #f2fff6;
            transition: background 0.2s;
        }
        .action-btn {
            display: inline-block;
            margin: 4px 0;
            padding: 8px 18px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            text-decoration: none;
            color: #fff;
            transition: background 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px #b2f7c7;
        }
        .action-approve {
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
        }
        .action-approve:hover {
            background: #27ae60;
        }
        .action-reject {
            background: linear-gradient(90deg, #ff5858 0%, #f09819 100%);
        }
        .action-reject:hover {
            background: #e74c3c;
        }
        .status-msg {
            text-align: center;
            font-size: 1.1rem;
            margin-bottom: 18px;
            padding: 12px 0;
            border-radius: 8px;
        }
        .status-success { background: #eafaf1; color: #27ae60; border-left: 5px solid #27ae60; }
        .status-error { background: #fff3cd; color: #e67e22; border-left: 5px solid #e67e22; }
        .status-fail { background: #ffeaea; color: #e74c3c; border-left: 5px solid #e74c3c; }
        @media (max-width: 900px) {
            .container { padding: 12px 2vw; }
            th, td { padding: 10px 6px; font-size: 0.98rem; }
        }
        @media (max-width: 600px) {
            .container { padding: 4px 0.5vw; }
            table, th, td { font-size: 0.93rem; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2><i class="fas fa-user-shield"></i> Danh sách đơn xin làm quản trị viên</h2>
        <?php if (isset($update) && $update && $action === 'approve'): ?>
            <div class="status-msg status-success">✅ Đã duyệt đơn và thêm người dùng làm quản trị viên.</div>
        <?php elseif (isset($update) && $update && $action === 'reject'): ?>
            <div class="status-msg status-fail">❌ Đã từ chối đơn.</div>
        <?php elseif (isset($app) && (!$app || $app['status'] !== 'pending')): ?>
            <div class="status-msg status-error">⚠️ Đơn này không tồn tại hoặc đã được xử lý.</div>
        <?php endif; ?>
        <?php if ($result->num_rows === 0): ?>
            <div class="status-msg status-error">Không có đơn nào đang chờ duyệt.</div>
        <?php else: ?>
        <table>
            <tr>
                <th>ID</th>
                <th><i class="fas fa-user"></i> Tên đăng nhập</th>
                <th><i class="fas fa-envelope"></i> Email</th>
                <th><i class="fas fa-calendar"></i> Ngày sinh</th>
                <th><i class="fas fa-building"></i> Tổ chức</th>
                <th><i class="fas fa-hands-helping"></i> Hoạt động xã hội</th>
                <th><i class="fas fa-comment-dots"></i> Lý do</th>
                <th>Hành động</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['email']) ?></td>
                <td><?= $row['dob'] ?></td>
                <td><?= htmlspecialchars($row['organization']) ?></td>
                <td><?= nl2br(htmlspecialchars($row['activities'])) ?></td>
                <td><?= nl2br(htmlspecialchars($row['reason'])) ?></td>
                <td>
                    <a href="?action=approve&id=<?= $row['id'] ?>" class="action-btn action-approve"><i class="fas fa-check"></i> Duyệt</a><br>
                    <a href="?action=reject&id=<?= $row['id'] ?>" class="action-btn action-reject"><i class="fas fa-times"></i> Từ chối</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </table>
        <?php endif; ?>
    </div>
</body>
</html>
