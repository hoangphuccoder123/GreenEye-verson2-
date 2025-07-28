<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone    = $_POST['phone'];
    $dob      = $_POST['dob'];
    $role     = $_POST['role'];

    // Chặn không cho tự gửi role là "quan_tri_vien"
    if ($role !== 'nguoi_dung' && $role !== 'cong_tac_vien') {
        die("Không hợp lệ!");
    }

    $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, date_of_birth, role) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $email, $password, $phone, $dob, $role);

    if ($stmt->execute()) {
        // Đăng ký thành công, chuyển hướng sang trang đăng nhập
        header('Location: login.html');
        exit();
    } else {
        echo "Lỗi: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
