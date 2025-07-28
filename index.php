<?php
session_start();
$isLoggedIn = isset($_SESSION["user_id"]);
require 'db.php';

// Lấy toàn bộ điểm rác để hiển thị trên bản đồ
$allPoints = [];
$sql = "SELECT latitude, longitude, image_url, description, verified FROM trash_points";
$result = $conn->query($sql);
while ($row = $result->fetch_assoc()) {
    $allPoints[] = $row;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Báo cáo điểm rác - Green Eye</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #27ae60;
            --primary-dark: #219150;
            --primary-light: #a5f1c1;
            --report-color: #43e97b;
            --report-dark: #27ae60;
            --profile-color: #43e97b;
            --profile-dark: #27ae60;
            --history-color: #43e97b;
            --history-dark: #27ae60;
            --white: #ffffff;
            --gray: #f5f7fa;
            --shadow: 0 4px 15px rgba(46, 204, 113, 0.10);
            --button-radius: 25px;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Roboto', 'Poppins', sans-serif; }
        body { background-color: var(--gray); display: flex; flex-direction: column; align-items: center; min-height: 100vh; }
        header {
            width: 100%;
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            padding: 20px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border: 3px solid var(--primary-color);
            border-bottom: none;
        }
        header h1 { margin: 0; font-size: 24px; display: flex; align-items: center; gap: 10px; color: white; }
        .user-info { position: relative; }
        .avatar {
            width: 40px; height: 40px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; font-size: 20px; color: var(--primary-dark); transition: all 0.3s ease; box-shadow: var(--shadow); cursor: pointer;
        }
        .avatar:hover { transform: scale(1.1); box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15); }
        .user-menu { position: absolute; top: 120%; right: 0; background: white; border-radius: 16px; min-width: 220px; box-shadow: 0 8px 32px rgba(46,204,113,0.18); display: none; overflow: hidden; z-index: 1000; animation: popupShow 0.3s; }
        .user-menu.active { display: block; }
        .menu-header { padding: 18px 18px 10px 18px; background: linear-gradient(135deg, #43e97b, #38f9d7); color: #fff; border-bottom: 1px solid #e0f5e9; text-align: left; }
        .menu-header .username { font-size: 1.1rem; font-weight: 600; margin-bottom: 4px; display: flex; align-items: center; gap: 6px; }
        .menu-header .user-role { font-size: 0.98rem; font-weight: 400; opacity: 0.95; display: flex; align-items: center; gap: 6px; }
        .menu-items { padding: 10px 0; }
        .menu-item { display: flex; align-items: center; gap: 10px; padding: 14px 20px; color: #333; text-decoration: none; font-size: 1rem; transition: background 0.2s, color 0.2s; cursor: pointer; }
        .menu-item:hover { background: #e8fdf4; color: #27ae60; }
        @keyframes popupShow { 0% { opacity: 0; transform: translateY(-10px) scale(0.95); } 100% { opacity: 1; transform: translateY(0) scale(1); } }
        .map-container { width: 100%; max-width: 1200px; margin: 20px auto; padding: 20px; background: var(--white); border-radius: 15px; box-shadow: var(--shadow); position: relative; display: flex; gap: 20px; transition: all 0.3s ease; }
        .map-container:hover { box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); transform: translateY(-2px); }
        #map { flex: 1; height: 600px; border-radius: 12px; overflow: hidden; border: 2px solid var(--primary-color); }
        .control-panel { width: 300px; background: var(--white); padding: 20px; border-radius: 15px; box-shadow: var(--shadow); transition: all 0.3s ease; }
        .control-panel:hover { transform: translateX(-5px); }
        .form-block { background: var(--white); border-radius: 12px; padding: 14px 16px; margin-bottom: 12px; box-shadow: var(--shadow); border: 1px solid rgba(0,0,0,0.1); }
        input[type="text"], input[type="file"] { width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 8px; margin-bottom: 15px; }
        input[type="text"]:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 0 2px rgba(39, 174, 96, 0.2); }
        button { background: var(--primary-color); color: white; border: none; padding: 12px 20px; border-radius: var(--button-radius); cursor: pointer; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; gap: 8px; justify-content: center; width: 100%; margin-top: 10px; }
        button:hover { background: var(--primary-dark); transform: translateY(-2px); }
        h3 { color: var(--primary-dark); margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
        img { max-width: 150px; border-radius: 8px; margin: 10px 0; }
        .form-group { margin-bottom: 10px; }
        .form-group label { display: block; margin-bottom: 8px; color: var(--primary-dark); font-weight: 500; }
        .location-btn { background: var(--profile-color); margin-bottom: 10px; }
        .location-btn:hover { background: var(--profile-dark); }
        .submit-btn { background: var(--report-color); }
        .submit-btn:hover { background: var(--report-dark); }
        .floating-rank-btn { position: fixed; left: 32px; bottom: 32px; width: 62px; height: 62px; background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); color: #fff; border-radius: 50%; box-shadow: 0 6px 24px rgba(46,204,113,0.18); display: flex; align-items: center; justify-content: center; font-size: 2.1rem; z-index: 2000; transition: background 0.2s, box-shadow 0.2s, transform 0.15s; border: none; cursor: pointer; text-decoration: none; outline: none; opacity: 0.95; }
        .floating-rank-btn:hover { background: linear-gradient(135deg, #38f9d7 0%, #43e97b 100%); box-shadow: 0 10px 32px #43e97b55; transform: scale(1.08); color: #ffd700; }
        @media (max-width: 600px) { .floating-rank-btn { left: 12px; bottom: 12px; width: 48px; height: 48px; font-size: 1.4rem; } }
    </style>
</head>
<body>
<header>
    <div style="display: flex; align-items: center; gap: 15px;">
        <h1 style="margin-right: 24px;"><i class="fas fa-leaf"></i> Green Eye - Báo cáo điểm rác</h1>
        <a href="dangky_admin.php" style="background: linear-gradient(90deg, #38f9d7 0%, #43e97b 100%); color: #fff; padding: 8px 18px; border-radius: 8px; font-weight: 600; text-decoration: none; margin-right: 8px; box-shadow: 0 2px 8px 0 rgba(34,197,94,0.10); transition: background 0.2s, transform 0.2s; display: flex; align-items: center; gap: 6px;"><i class="fas fa-user-plus"></i> Đăng ký Admin</a>
        <a href="admin.php" style="background: linear-gradient(90deg, #43e97b 0%, #38f9d7 100%); color: #fff; padding: 8px 18px; border-radius: 8px; font-weight: 600; text-decoration: none; box-shadow: 0 2px 8px 0 rgba(34,197,94,0.10); transition: background 0.2s, transform 0.2s; display: flex; align-items: center; gap: 6px;"><i class="fas fa-user-shield"></i> Quản trị</a>
        <a href="sukien.php" style="background: linear-gradient(90deg, #f7971e 0%, #ffd200 100%); color: #222; padding: 8px 18px; border-radius: 8px; font-weight: 600; text-decoration: none; margin-right: 8px; box-shadow: 0 2px 8px 0 rgba(247,151,30,0.10); transition: background 0.2s, transform 0.2s; display: flex; align-items: center; gap: 6px;"><i class="fas fa-calendar-alt"></i> Sự kiện</a>
    </div>
    <div style="display: flex; align-items: center; gap: 15px;">
        <?php if ($isLoggedIn): ?>
        <div class="user-info">
            <div class="avatar" onclick="toggleUserMenu()">
                <?= strtoupper(substr($_SESSION["user_name"], 0, 1)) ?>
            </div>
            <div class="user-menu" id="userMenu">
                <div class="menu-header">
                    <div class="username">👤 <?= htmlspecialchars($_SESSION["user_name"]) ?></div>
                    <div class="user-role">🔰 Vai trò: <strong style="color:#27ae60; text-transform:capitalize;">
                        <?php
                        $role = $_SESSION["user_role"] ?? 'nguoi_dung';
                        if ($role === 'quan_tri_vien' || $role === 'admin') echo 'Quản trị viên';
                        else if ($role === 'cong_tac_vien') echo 'Cộng tác viên';
                        else echo 'Người dùng';
                        ?>
                    </strong></div>
                </div>
                <div class="menu-items">
                    <a href="logout.php" class="menu-item">
                        <i class="fas fa-sign-out-alt"></i>
                        Đăng xuất
                    </a>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="auth-links">
            <a href="login.html"><i class="fas fa-sign-in-alt"></i> Đăng nhập</a> |
            <a href="register.html"><i class="fas fa-user-plus"></i> Đăng ký</a>
        </div>
        <?php endif; ?>
    </div>
</header>
<div class="map-container">
    <div id="map"></div>
    <div class="control-panel">
        <?php if ($isLoggedIn): ?>
        <div class="form-block">
            <h3><i class="fas fa-plus-circle"></i> Thêm điểm rác mới</h3>
            <form action="add_point.php" method="post" enctype="multipart/form-data" id="addPointForm">
                <div class="form-group">
                    <label><i class="fas fa-map-marker-alt"></i> Vị trí</label>
                    <input type="text" name="lat" id="lat" placeholder="Vĩ độ" required>
                    <input type="text" name="lng" id="lng" placeholder="Kinh độ" required>
                    <button type="button" onclick="getCurrentLocation()" class="location-btn">
                        <i class="fas fa-location-arrow"></i> Lấy vị trí của tôi
                    </button>
                </div>
                <div class="form-group">
                    <label><i class="fas fa-camera"></i> Ảnh (tùy chọn)</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                <button type="submit" class="submit-btn" id="submitBtn">
                    <i class="fas fa-paper-plane"></i> Gửi báo cáo
                </button>
                <div id="loadingMsg" style="display: none; color: #27ae60; margin-top: 10px;">
                    <strong>⏳ Đang xử lý và phân tích ảnh qua AI... Vui lòng đợi (có thể mất tới 5 phút)</strong>
                </div>
            </form>
        </div>
        <div class="form-block">
            <h3 style="margin-top:18px"><i class="fas fa-history"></i> Lịch sử báo cáo của bạn</h3>
            <?php
            $user_id = $_SESSION["user_id"];
            $stmt = $conn->prepare("SELECT id, latitude, longitude, description, image_url FROM trash_points WHERE user_id = ? ORDER BY id DESC");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0):
                while ($row = $result->fetch_assoc()):
            ?>
            <form action="update_point.php" method="post" enctype="multipart/form-data" class="updatePointForm" style="margin-bottom:18px; border-bottom:1px solid #e0f5e9; padding-bottom:12px;">
                <input type="hidden" name="id" value="<?= $row['id'] ?>">
                <div class="form-group">
                    <label>Vĩ độ</label>
                    <input type="text" name="lat" value="<?= htmlspecialchars($row['latitude']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Kinh độ</label>
                    <input type="text" name="lng" value="<?= htmlspecialchars($row['longitude']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Mô tả</label>
                    <input type="text" name="description" value="<?= htmlspecialchars($row['description']) ?>">
                </div>
                <?php if ($row['image_url']): ?>
                <div class="form-group">
                    <label>Ảnh hiện tại</label><br>
                    <img src="<?= $row['image_url'] ?>" style="max-width:100px; border-radius:6px; box-shadow:0 2px 8px rgba(0,0,0,0.08); margin-bottom:6px;">
                </div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Cập nhật ảnh mới (nếu muốn)</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                <button type="submit" class="submit-btn updateBtn"><i class="fas fa-save"></i> Cập nhật</button>
                <div class="updateLoadingMsg" style="display: none; color: #27ae60; margin-top: 8px;">
                    <strong>⏳ Đang xử lý và phân tích ảnh qua AI... Vui lòng đợi (có thể mất tới 5 phút)</strong>
                </div>
            </form>
            <?php endwhile; else: ?>
            <div style="color:#888; text-align:center;">Bạn chưa có báo cáo nào.</div>
            <?php endif; ?>
        </div>
        <?php else: ?>
        <p><strong>Vui lòng đăng nhập để thêm điểm rác.</strong></p>
        <?php endif; ?>
    </div>
</div>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script src="https://kit.fontawesome.com/2b8e1e2e7a.js" crossorigin="anonymous"></script>
<script>
const points = <?= json_encode($allPoints) ?>;
const map = L.map('map').setView([10.762622, 106.660172], 13);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
const redIcon = new L.Icon({ iconUrl: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png', iconSize: [32, 32], iconAnchor: [16, 32], popupAnchor: [0, -32] });
const blueIcon = new L.Icon({ iconUrl: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png', iconSize: [32, 32], iconAnchor: [16, 32], popupAnchor: [0, -32] });
const markerMap = {};
points.forEach(point => {
    const lat = parseFloat(point.latitude);
    const lng = parseFloat(point.longitude);
    const icon = point.verified == 1 ? blueIcon : redIcon;
    const marker = L.marker([lat, lng], { icon }).addTo(map);
    // Popup styled giống giaodien.php
    let popup = `<div style="font-family: 'Roboto', sans-serif; min-width:220px;">
        <h4 style="margin: 0 0 10px 0; color: #2c3e50; border-bottom: 2px solid #2ecc71; padding-bottom: 5px; font-size:1.1rem;">
            ${point.description || 'Không có mô tả'}
        </h4>`;
    popup += `<div style='margin-bottom:6px;'><strong>Tọa độ:</strong> ${lat}, ${lng}</div>`;
    popup += `<div style='margin-bottom:6px;'><strong>Tình trạng:</strong> ${point.verified == 1 ? '<span style=\'color:#27ae60\'>✅ Đã kiểm duyệt</span>' : '<span style=\'color:#fd7e14\'>⚠️ Chưa kiểm duyệt</span>'}</div>`;
    if (point.image_url) {
        popup += `<div style='margin-top: 10px;'><img src="${point.image_url}" style="width: 100%; max-width: 200px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);"></div>`;
    }
    popup += '</div>';
    marker.bindPopup(popup, {autoPan: true, closeButton: true, className: 'custom-popup'});
    markerMap[`${lat},${lng}`] = marker;
    // Hiệu ứng hover: mở popup khi hover
    marker.on('mouseover', function(e) { this.openPopup(); });
    marker.on('mouseout', function(e) { this.closePopup(); });
});
function focusOnMap(lat, lng) {
    const key = `${lat},${lng}`;
    const marker = markerMap[key];
    if (marker) {
        map.setView([lat, lng], 17);
        marker.openPopup();
    } else {
        alert("Không tìm thấy điểm này trên bản đồ.");
    }
}
function getCurrentLocation() {
    if (!navigator.geolocation) {
        alert("Trình duyệt không hỗ trợ định vị.");
        return;
    }
    navigator.geolocation.getCurrentPosition(
        function (position) {
            document.getElementById('lat').value = position.coords.latitude;
            document.getElementById('lng').value = position.coords.longitude;
            map.setView([position.coords.latitude, position.coords.longitude], 16);
            L.marker([position.coords.latitude, position.coords.longitude]).addTo(map).bindPopup("📍 Vị trí của bạn").openPopup();
        },
        function () {
            alert("Không thể lấy vị trí.");
        }
    );
}
<?php if ($isLoggedIn): ?>
map.on('click', function(e) {
    document.getElementById('lat').value = e.latlng.lat;
    document.getElementById('lng').value = e.latlng.lng;
});
document.getElementById('addPointForm').addEventListener('submit', function(e) {
    const fileInput = document.querySelector('input[type="file"]');
    const submitBtn = document.getElementById('submitBtn');
    const loadingMsg = document.getElementById('loadingMsg');
    if (fileInput.files.length > 0) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Đang xử lý...';
        loadingMsg.style.display = 'block';
        setTimeout(function() {
            if (submitBtn.disabled) {
                alert('Quá trình xử lý đang mất nhiều thời gian hơn dự kiến. Vui lòng tiếp tục đợi...');
            }
        }, 60000);
    }
});
<?php endif; ?>
function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    menu.classList.toggle('active');
    if (menu.classList.contains('active')) {
        document.addEventListener('mousedown', closeMenuOnClickOutside);
    } else {
        document.removeEventListener('mousedown', closeMenuOnClickOutside);
    }
}
function closeMenuOnClickOutside(e) {
    const menu = document.getElementById('userMenu');
    const avatar = document.querySelector('.avatar');
    if (!menu.contains(e.target) && !avatar.contains(e.target)) {
        menu.classList.remove('active');
        document.removeEventListener('mousedown', closeMenuOnClickOutside);
    }
}
</script>
<a href="xephang.php" class="floating-rank-btn" title="Bảng xếp hạng">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="32" height="32" fill="currentColor">
        <rect x="8" y="28" width="16" height="28" rx="4" fill="#b0c4de"/>
        <text x="16" y="50" text-anchor="middle" font-size="16" fill="#fff" font-weight="bold" dy=".3em">2</text>
        <rect x="24" y="12" width="16" height="44" rx="4" fill="#ffd700"/>
        <text x="32" y="38" text-anchor="middle" font-size="16" fill="#fff" font-weight="bold" dy=".3em">1</text>
        <rect x="40" y="36" width="16" height="20" rx="4" fill="#cd7f32"/>
        <text x="48" y="50" text-anchor="middle" font-size="16" fill="#fff" font-weight="bold" dy=".3em">3</text>
    </svg>
</a>
</body>
</html>
