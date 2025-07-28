<?php
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $dob = $_POST['dob'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $organization = $_POST['organization'];
    $activities = $_POST['activities'];
    $reason = $_POST['reason'];

    $sql = "INSERT INTO admin_applications (username, email, dob, password, organization, activities, reason)
            VALUES (?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $username, $email, $dob, $password, $organization, $activities, $reason);

    if ($stmt->execute()) {
        echo "✅ Yêu cầu của bạn đã được gửi! Vui lòng chờ xét duyệt.";
    } else {
        echo "❌ Lỗi khi gửi yêu cầu: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng ký xin làm quản trị viên</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: url('Environmental Protection Earth Green Simple Fresh Powerpoint Background For Free Download.jpg') no-repeat center center fixed;
            background-size: cover;
            min-height: 100vh;
            margin: 0;
            position: relative;
            font-family: 'Poppins', sans-serif;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 0;
        }
        .container {
            position: relative;
            z-index: 1;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .admin-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(8px);
            border-radius: 1.2rem;
            box-shadow: 0 8px 32px rgba(39,174,96,0.13);
            padding: 2.5rem 2.2rem 2rem 2.2rem;
            max-width: 700px;
            width: 100%;
            margin: 2rem 0;
        }
        @media (max-width: 900px) {
            .admin-card { max-width: 98vw; }
        }
        .admin-header {
            text-align: center;
            margin-bottom: 1.8rem;
        }
        .admin-header .logo {
            font-size: 2.5rem;
            color: #27ae60;
            margin-bottom: 0.5rem;
        }
        .admin-header h2 {
            font-size: 1.5rem;
            color: #219150;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }
        .admin-header p {
            color: #555;
            font-size: 1rem;
        }
        form label {
            color: #219150;
            font-weight: 500;
            margin-bottom: 0.2rem;
            display: block;
        }
        form input, form textarea {
            width: 100%;
            padding: 0.35rem 0.6rem;
            border: 1px solid #b2e5c7;
            border-radius: 0.5rem;
            margin-bottom: 0.45rem;
            font-size: 0.93rem;
            background: #f8fffa;
            transition: border 0.2s;
        }
        form input:focus, form textarea:focus {
            border: 1.5px solid #27ae60;
            outline: none;
        }
        form textarea {
            resize: vertical;
            min-height: 70px;
        }
        .submit-btn {
            width: 100%;
            padding: 0.9rem;
            background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%);
            color: #fff;
            border: none;
            border-radius: 0.6rem;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 2px 8px 0 rgba(34,197,94,0.10);
            transition: background 0.2s, transform 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .submit-btn:hover {
            background: linear-gradient(90deg, #219150 0%, #43e97b 100%);
            transform: translateY(-2px) scale(1.03);
        }
        @media (max-width: 600px) {
            .admin-card { padding: 1.2rem 0.5rem; }
        }
        .success-msg, .error-msg {
            text-align: center;
            font-size: 1.1rem;
            margin-bottom: 1.2rem;
            padding: 0.8rem 1rem;
            border-radius: 0.7rem;
            font-weight: 500;
        }
        .success-msg { background: #e8fdf4; color: #219150; border: 1.5px solid #97e1c5; }
        .error-msg { background: #ffe0e0; color: #e74c3c; border: 1.5px solid #ffb3b3; }
    </style>
</head>
<body>
<div class="container">
    <div class="admin-card">
        <div class="admin-header">
            <div class="logo"><i class="fas fa-user-shield"></i></div>
            <h2>Đăng ký xin quyền quản trị viên</h2>
            <p>Điền thông tin chi tiết để gửi yêu cầu xét duyệt quyền quản trị viên cho hệ thống Green Eye.</p>
        </div>
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if ($stmt && $stmt->execute()) {
                echo '<div class="success-msg"><i class="fas fa-check-circle"></i> Yêu cầu của bạn đã được gửi! Vui lòng chờ xét duyệt.</div>';
            } else if ($stmt) {
                echo '<div class="error-msg"><i class="fas fa-times-circle"></i> Lỗi khi gửi yêu cầu: ' . htmlspecialchars($stmt->error) . '</div>';
            }
        }
        ?>
        <form method="POST" action="">
            <label><i class="fas fa-user"></i> Tên đăng nhập:</label>
            <input type="text" name="username" required>

            <label><i class="fas fa-envelope"></i> Email:</label>
            <input type="email" name="email" required>

            <label><i class="fas fa-calendar"></i> Ngày sinh:</label>
            <input type="date" name="dob" required>

            <label><i class="fas fa-lock"></i> Mật khẩu:</label>
            <input type="password" name="password" required>

            <label><i class="fas fa-building"></i> Tổ chức đang hoạt động (nếu có):</label>
            <input type="text" name="organization">

            <label><i class="fas fa-hands-helping"></i> Các hoạt động xã hội từng tham gia (nếu có):</label>
            <textarea name="activities" rows="4"></textarea>

            <label><i class="fas fa-question-circle"></i> Lý do bạn muốn trở thành quản trị viên:</label>
            <textarea name="reason" rows="4" required></textarea>

            <button type="submit" class="submit-btn"><i class="fas fa-paper-plane"></i> Gửi yêu cầu</button>
        </form>
    </div>
</div>
</body>
</html>
