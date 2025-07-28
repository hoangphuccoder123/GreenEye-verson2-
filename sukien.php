<?php
session_start();
require 'db.php';

$isLoggedIn = isset($_SESSION["user_id"]);
$isAdmin = $isLoggedIn && isset($_SESSION["user_role"]) && $_SESSION["user_role"] === 'admin';
$user_id = $_SESSION["user_id"] ?? null;

// Xử lý tạo sự kiện (chỉ admin)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "tao_su_kien" && $isAdmin) {
    $ten = $_POST["ten"];
    $thoigian = $_POST["thoigian"];
    $soluong = $_POST["soluong"];
    $diadiem = $_POST["diadiem"];
    $mota = $_POST["mota"];
    $stmt = $conn->prepare("INSERT INTO su_kien (ten, thoigian, soluong, diadiem, mota) VALUES (?, ?, ?, ?, ?)");
    if ($stmt) {
        $stmt->bind_param("ssiss", $ten, $thoigian, $soluong, $diadiem, $mota);
        $stmt->execute();
        $stmt->close();
    }
}

// Xử lý người dùng tham gia sự kiện
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"]) && $_POST["action"] === "tham_gia" && $isLoggedIn) {
    $su_kien_id = $_POST["su_kien_id"];
    $stmt = $conn->prepare("INSERT IGNORE INTO tham_gia_su_kien (user_id, su_kien_id) VALUES (?, ?)");
    if ($stmt) {
        $stmt->bind_param("ii", $user_id, $su_kien_id);
        $stmt->execute();
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Sự kiện cộng đồng</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', 'Poppins', sans-serif;
            background: #f4fef9;
            margin: 0;
            padding: 0;
        }
        .event-header {
            background: linear-gradient(135deg, #43e97b, #38f9d7);
            color: #fff;
            padding: 32px 0 24px 0;
            text-align: center;
            border-bottom: 4px solid #27ae60;
            margin-bottom: 32px;
        }
        .event-header h2 {
            font-size: 2.1rem;
            margin: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            font-weight: 700;
        }
        .event-header p {
            margin: 8px 0 0 0;
            font-size: 1.1rem;
            color: #eafff6;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 16px 32px 16px;
        }
        .event-form {
            background: #e8fdf4;
            border: 1.5px solid #97e1c5;
            padding: 28px 24px 20px 24px;
            margin-bottom: 32px;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(46,204,113,0.07);
        }
        .event-form h3 {
            margin-top: 0;
            color: #219150;
            font-size: 1.2rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .event-form label {
            font-weight: 500;
            color: #219150;
            margin-top: 10px;
            display: block;
        }
        .event-form input, .event-form textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #b2e5c7;
            border-radius: 8px;
            margin-top: 6px;
            margin-bottom: 16px;
            font-size: 1rem;
            background: #fff;
        }
        .event-form button[type="submit"] {
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 10px 22px;
            font-size: 1rem;
            cursor: pointer;
            box-shadow: 0 2px 8px 0 rgba(34,197,94,0.10);
            transition: background 0.2s, transform 0.2s;
        }
        .event-form button[type="submit"]:hover {
            background: linear-gradient(90deg, #219150 0%, #43e97b 100%);
            transform: translateY(-2px) scale(1.03);
        }
        .event-list {
            background: #fff;
            padding: 28px 24px 18px 24px;
            border-radius: 14px;
            box-shadow: 0 4px 16px rgba(46,204,113,0.07);
        }
        .event-list h3 {
            color: #219150;
            margin-top: 0;
            margin-bottom: 18px;
            font-size: 1.15rem;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .event-list ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .event-list li {
            margin-bottom: 32px;
            padding-bottom: 18px;
            border-bottom: 1px solid #e0f5e9;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(46,204,113,0.07);
            transition: box-shadow 0.25s, transform 0.25s, border 0.25s;
            position: relative;
            background: rgba(255,255,255,0.95);
        }
        .event-list li:hover {
            box-shadow: 0 8px 32px rgba(46,204,113,0.18);
            border: 2px solid #43e97b;
            transform: translateY(-4px) scale(1.02);
        }
        .event-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #27ae60;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .event-meta {
            color: #219150;
            font-size: 0.98rem;
            margin: 2px 0 8px 0;
        }
        .event-participants {
            color: #555;
            font-size: 0.97rem;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .event-list button {
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            padding: 8px 18px;
            font-size: 1rem;
            cursor: pointer;
            margin-top: 8px;
            box-shadow: 0 2px 8px 0 rgba(34,197,94,0.10);
            transition: background 0.2s, transform 0.2s;
        }
        .event-list button:hover {
            background: linear-gradient(90deg, #219150 0%, #43e97b 100%);
            transform: translateY(-2px) scale(1.03);
        }
        @media (max-width: 600px) {
            .container { padding: 0 2px 18px 2px; }
            .event-form, .event-list { padding: 12px 4px; }
        }
    </style>
</head>
<body>
<div class="event-header">
    <h2><i class="fas fa-calendar-alt"></i> Sự kiện cộng đồng</h2>
    <?php if (
        $isLoggedIn): ?>
        <p>Xin chào, <strong><?= htmlspecialchars($_SESSION["user_name"]) ?></strong> <?php if ($isAdmin) echo "(quản trị viên)"; ?></p>
    <?php else: ?>
        <p><a href="login.html" style="color:#fff; text-decoration:underline;">Đăng nhập</a> để tham gia sự kiện.</p>
    <?php endif; ?>
</div>
<div class="container">
<?php if ($isAdmin): ?>
<div class="event-form">
    <h3><i class="fas fa-plus-circle"></i> Tạo sự kiện mới</h3>
    <form method="post">
        <input type="hidden" name="action" value="tao_su_kien">
        <label>Tên sự kiện:</label>
        <input type="text" name="ten" required>
        <label>Thời gian bắt đầu:</label>
        <input type="datetime-local" name="thoigian" required>
        <label>Số lượng người cần tham gia:</label>
        <input type="number" name="soluong" required min="1">
        <label>Địa điểm tổ chức:</label>
        <input type="text" name="diadiem" required placeholder="Công viên, khu dân cư, v.v.">
        <label>Mô tả sự kiện:</label>
        <textarea name="mota" required placeholder="Mô tả ngắn gọn mục tiêu, nội dung sự kiện..."></textarea>
        <button type="submit"><i class="fas fa-plus"></i> Tạo sự kiện</button>
    </form>
</div>
<?php endif; ?>
<div class="event-list">
    <h3><i class="fas fa-list"></i> Danh sách sự kiện</h3>
    <ul>
<?php
$sql = "SELECT * FROM su_kien ORDER BY thoigian ASC";
$result = $conn->query($sql);
if ($result && $result->num_rows > 0):
    while ($event = $result->fetch_assoc()):
        $su_kien_id = $event['id'];
        $daThamGia = false;
        if ($isLoggedIn) {
            $stmt = $conn->prepare("SELECT 1 FROM tham_gia_su_kien WHERE user_id = ? AND su_kien_id = ?");
            $stmt->bind_param("ii", $user_id, $su_kien_id);
            $stmt->execute();
            $stmt->store_result();
            $daThamGia = $stmt->num_rows > 0;
            $stmt->close();
        }
        $countQuery = $conn->prepare("SELECT COUNT(*) AS total FROM tham_gia_su_kien WHERE su_kien_id = ?");
        $countQuery->bind_param("i", $su_kien_id);
        $countQuery->execute();
        $countResult = $countQuery->get_result()->fetch_assoc();
        $so_nguoi_tham_gia = $countResult['total'] ?? 0;
        $countQuery->close();
?>
        <li>
            <span class="event-title"><i class="fa-solid fa-seedling"></i> <?= htmlspecialchars($event["ten"]) ?></span><br>
            <span class="event-meta"><i class="fa-regular fa-clock"></i> <?= htmlspecialchars($event["thoigian"]) ?></span><br>
            <span class="event-meta"><i class="fa-solid fa-location-dot"></i> <?= htmlspecialchars($event["diadiem"]) ?></span><br>
            <span class="event-meta"><i class="fa-solid fa-align-left"></i> <?= htmlspecialchars($event["mota"]) ?></span><br>
            <span class="event-participants"><i class="fa-solid fa-users"></i> Cần: <?= $event["soluong"] ?> | Đã có: <?= $so_nguoi_tham_gia ?></span><br>
            <?php if ($isLoggedIn && !$isAdmin && !$daThamGia): ?>
                <form method="post" style="margin-top:10px;">
                    <input type="hidden" name="action" value="tham_gia">
                    <input type="hidden" name="su_kien_id" value="<?= $su_kien_id ?>">
                    <button type="submit"><i class="fa-solid fa-check"></i> Tham gia</button>
                </form>
            <?php elseif ($isLoggedIn && !$isAdmin && $daThamGia): ?>
                <em>✅ Bạn đã tham gia sự kiện này.</em>
            <?php endif; ?>
        </li>
<?php endwhile; else: ?>
    <li>Chưa có sự kiện nào được tạo.</li>
<?php endif; ?>
    </ul>
</div>
</div>
</body>
</html>
