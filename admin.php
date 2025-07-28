<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập hay chưa
if (!isset($_SESSION["user_id"])) {
    die("<h2>Bạn cần đăng nhập trước khi truy cập trang này.</h2><a href='login.html'>Đăng nhập</a>");
}

// Kiểm tra vai trò admin hoặc quản trị viên (cho phép cả 'admin' và 'quan_tri_vien')
if (!in_array($_SESSION["user_role"], ['admin', 'quan_tri_vien'])) {
    die("<h2>❌ Bạn không có quyền truy cập trang này.</h2>");
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Trang Quản Trị Viên</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #27ae60;
            --primary-dark: #219150;
            --primary-light: #a5f1c1;
            --white: #fff;
            --gray: #f5f7fa;
            --shadow: 0 4px 15px rgba(46, 204, 113, 0.10);
        }
        * { box-sizing: border-box; font-family: 'Roboto', 'Poppins', sans-serif; }
        body {
            background: var(--gray);
            margin: 0;
            min-height: 100vh;
        }
        .admin-container {
            max-width: 1400px;
            margin: 40px auto;
            background: var(--white);
            border-radius: 18px;
            box-shadow: var(--shadow);
            padding: 36px 48px 40px 48px;
            border: 2px solid var(--primary-color);
        }
        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 24px;
        }
        .admin-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-dark);
        }
        .logout-btn {
            background: none;
            border: 1.5px solid var(--primary-color);
            color: var(--primary-dark);
            padding: 8px 22px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, color 0.2s;
        }
        .logout-btn:hover {
            background: var(--primary-color);
            color: #fff;
        }
        .admin-content {
            display: flex;
            gap: 48px;
            flex-wrap: wrap;
        }
        .admin-map-block {
            flex: 2.5;
            background: #eafbe7;
            border-radius: 40px;
            padding: 40px 32px;
            min-width: 600px;
            min-height: 520px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .admin-map-block .logo {
            font-size: 1.3rem;
            color: var(--primary-dark);
            margin-bottom: 18px;
            font-weight: 600;
        }
        #map {
            width: 100%;
            height: 520px;
            border-radius: 32px;
            border: 2px solid var(--primary-color);
        }
        .admin-panel-block {
            flex: 1.2;
            background: #eafbe7;
            border-radius: 40px;
            padding: 40px 32px 24px 32px;
            min-width: 340px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .admin-panel-block .panel-btn {
            width: 100%;
            background: #fff;
            border: 1.5px solid var(--primary-color);
            color: var(--primary-dark);
            border-radius: 10px;
            padding: 14px 0;
            font-size: 1.08rem;
            font-weight: 500;
            margin-bottom: 18px;
            cursor: pointer;
            transition: background 0.2s, color 0.2s, box-shadow 0.2s;
            box-shadow: 0 2px 8px 0 rgba(34,197,94,0.07);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        .admin-panel-block .panel-btn:hover {
            background: var(--primary-color);
            color: #fff;
        }
        .admin-panel-block .panel-btn:last-child { margin-bottom: 0; }
        .admin-panel-block .panel-desc {
            font-size: 0.98rem;
            color: #333;
            margin-top: 18px;
            text-align: left;
        }
        @media (max-width: 1200px) {
            .admin-container { padding: 18px 4px; }
            .admin-content { gap: 18px; }
            .admin-map-block, .admin-panel-block { min-width: unset; padding: 18px 8px; }
            #map { height: 340px; }
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <div class="admin-title"><i class="fas fa-user-shield"></i> Trang chủ của tài khoản quản trị viên</div>
            <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Đăng xuất</a>
        </div>
        <div class="admin-content">
            <div class="admin-map-block">
                <div class="logo"><i class="fas fa-leaf"></i> Green Eye</div>
                <div id="map"></div>
            </div>
            <div class="admin-panel-block">

                <a href="sukien.php" class="panel-btn"><i class="fas fa-calendar-plus"></i> Tạo sự kiện dọn rác</a>
                <a href="duyet_anh.php" class="panel-btn"><i class="fas fa-sync-alt"></i> Xác minh điểm rác</a>
                <a href="nguoidung_admin.php" class="panel-btn"><i class="fas fa-users-cog"></i> Quản lý tài khoản người dùng</a>
            </div>
        </div>
    </div>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        const map = L.map('map').setView([10.762622, 106.660172], 13);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
        fetch('get_points.php')
            .then(res => res.json())
            .then(data => {
                data.forEach(point => {
                    let markerOptions = {};
                    let popup = `<div style=\"font-family: 'Roboto', sans-serif;\"><h4 style=\"margin: 0 0 10px 0; color: #2c3e50; border-bottom: 2px solid #2ecc71; padding-bottom: 5px;\">${point.description}</h4>`;
                    if (point.ai_verified && point.ai_data) {
                        const aiData = point.ai_data;
                        popup += `<div style=\"background: linear-gradient(135deg, #e8f5e8, #d4edda); padding: 8px; margin: 8px 0; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);\">`;
                        popup += `<strong style=\"color: #28a745; font-size: 12px;\">🤖 Phân tích AI:</strong> `;
                        if (aiData.image_type === 'trash') {
                            const pollutionTexts = {
                                1: ['Rác rất ít', '#28a745'],
                                2: ['Rác ít', '#ffc107'],
                                3: ['Rác trung bình', '#fd7e14'],
                                4: ['Rác nghiêm trọng', '#dc3545']
                            };
                            const [text, color] = pollutionTexts[aiData.pollution_level] || ['Xác nhận rác', '#28a745'];
                            popup += `<span style=\"color: ${color}; font-weight: 500;\">${text}</span>`;
                            if (aiData.pollution_level >= 3) {
                                popup += ` <span style=\"color: #dc3545;\">⚠️</span>`;
                            }
                        } else {
                            popup += '<span style=\"color: #6c757d;\">Không xác định được</span>';
                        }
                        popup += `</div>`;
                    } else if (!point.ai_verified) {
                        popup += `<div style=\"background: linear-gradient(135deg, #fff3cd, #ffeeba); padding: 8px; margin: 8px 0; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);\">`;
                        popup += `<span style=\"color: #856404; font-size: 12px;\">📸 Chưa phân tích AI</span>`;
                        popup += `</div>`;
                    }
                    if (point.image_url) {
                        popup += `<div style=\"margin-top: 10px;\"><img src=\"${point.image_url}\" style=\"width: 100%; max-width: 200px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);\"></div>`;
                    }
                    popup += '</div>';
                    L.marker([point.latitude, point.longitude], markerOptions).addTo(map).bindPopup(popup);
                });
            });
    </script>
</body>
</html>
