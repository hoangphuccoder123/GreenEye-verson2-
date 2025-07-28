<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];

$sql = "SELECT name, phone, date_of_birth, created_at, points, role FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Hồ sơ người dùng</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins:400,600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background: url('Environmental Protection Earth Green Simple Fresh Powerpoint Background For Free Download.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .profile-container {
            background: rgba(255,255,255,0.7);
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.18);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border-radius: 20px;
            padding: 40px 32px 32px 32px;
            max-width: 420px;
            width: 100%;
            margin: 32px 0;
            border: 1.5px solid rgba(34,197,94,0.18);
            position: relative;
        }
        .profile-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .profile-header h2 {
            color: #219150;
            font-weight: 700;
            margin: 0 0 8px 0;
            letter-spacing: 1px;
        }
        .profile-avatar {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px auto;
            font-size: 2.5rem;
            color: #fff;
            box-shadow: 0 2px 12px 0 rgba(34,197,94,0.15);
        }
        .profile-info {
            margin-bottom: 18px;
        }
        .profile-info .info-row {
            display: flex;
            align-items: center;
            margin-bottom: 14px;
        }
        .profile-info .info-icon {
            color: #219150;
            margin-right: 12px;
            font-size: 1.15rem;
            min-width: 22px;
            text-align: center;
        }
        .profile-info .info-label {
            font-weight: 600;
            color: #219150;
            min-width: 120px;
        }
        .profile-info .info-value {
            color: #222;
            font-weight: 500;
        }
        .profile-actions {
            text-align: center;
            margin-top: 18px;
        }
        .profile-actions .button {
            display: inline-block;
            padding: 10px 28px;
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            color: #fff;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            text-decoration: none;
            box-shadow: 0 2px 8px 0 rgba(34,197,94,0.10);
            transition: background 0.2s, transform 0.2s;
        }
        .profile-actions .button:hover {
            background: linear-gradient(90deg, #219150 0%, #43e97b 100%);
            transform: translateY(-2px) scale(1.03);
        }
        @media (max-width: 600px) {
            .profile-container {
                padding: 24px 8px 16px 8px;
            }
            .profile-header h2 {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>
<div class="profile-container">
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fa-solid fa-user"></i>
        </div>
        <h2>Hồ sơ của bạn</h2>
    </div>
    <div class="profile-info">
        <div class="info-row"><span class="info-icon"><i class="fa-solid fa-signature"></i></span><span class="info-label">Họ tên:</span> <span class="info-value"><?= htmlspecialchars($user['name']) ?></span></div>
        <div class="info-row"><span class="info-icon"><i class="fa-solid fa-phone"></i></span><span class="info-label">Số điện thoại:</span> <span class="info-value"><?= htmlspecialchars($user['phone']) ?></span></div>
        <div class="info-row"><span class="info-icon"><i class="fa-solid fa-cake-candles"></i></span><span class="info-label">Ngày sinh:</span> <span class="info-value"><?= $user['date_of_birth'] ?></span></div>
        <div class="info-row"><span class="info-icon"><i class="fa-solid fa-calendar-plus"></i></span><span class="info-label">Ngày tạo TK:</span> <span class="info-value"><?= $user['created_at'] ?></span></div>
        <div class="info-row"><span class="info-icon"><i class="fa-solid fa-leaf"></i></span><span class="info-label">Điểm tích lũy:</span> <span class="info-value"><?= $user['points'] ?></span></div>
        <div class="info-row"><span class="info-icon"><i class="fa-solid fa-user-shield"></i></span><span class="info-label">Vai trò:</span> <span class="info-value"><?= ucfirst($user['role']) ?></span></div>
    </div>
    <div class="profile-actions">
        <a class="button" href="edit_profile.php"><i class="fa-solid fa-pen-to-square"></i> Cập nhật hồ sơ</a>
    </div>
</div>
</body>
</html>
