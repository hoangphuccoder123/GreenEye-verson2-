<?php
    session_start();
    $isLoggedIn = isset($_SESSION["user_id"]);
    require 'db.php';
    ?>

    <!DOCTYPE html>
    <html lang="vi">
    <head>
        <meta charset="UTF-8">
        <title>Báo cáo điểm rác - Green Eye</title>
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />        <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
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
            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Roboto', 'Poppins', sans-serif;
            }

            body {
                background-color: var(--gray);
                display: flex;
                flex-direction: column;
                align-items: center;
                min-height: 100vh;
            }

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

            header h1 {
                margin: 0;
                font-size: 24px;
                display: flex;
                align-items: center;
                gap: 10px;
                color: white;
            }

            .user-info {
                position: relative;
            }

            .avatar {
                width: 40px;
                height: 40px;
                border-radius: 50%;
                background: white;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 20px;
                color: var(--primary-dark);
                transition: all 0.3s ease;
                box-shadow: var(--shadow);
                cursor: pointer;
            }
            .avatar:hover {
                transform: scale(1.1);
                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            }

            .user-menu {
                position: absolute;
                top: 120%;
                right: 0;
                background: white;
                border-radius: 16px;
                min-width: 220px;
                box-shadow: 0 8px 32px rgba(46,204,113,0.18);
                display: none;
                overflow: hidden;
                z-index: 1000;
                animation: popupShow 0.3s;
            }

            .user-menu.active {
                display: block;
            }

            .menu-header {
                padding: 18px 18px 10px 18px;
                background: linear-gradient(135deg, #43e97b, #38f9d7);
                color: #fff;
                border-bottom: 1px solid #e0f5e9;
                text-align: left;
            }

            .menu-header .username {
                font-size: 1.1rem;
                font-weight: 600;
                margin-bottom: 4px;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .menu-header .user-role {
                font-size: 0.98rem;
                font-weight: 400;
                opacity: 0.95;
                display: flex;
                align-items: center;
                gap: 6px;
            }

            .menu-items {
                padding: 10px 0;
            }

            .menu-item {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 14px 20px;
                color: #333;
                text-decoration: none;
                font-size: 1rem;
                transition: background 0.2s, color 0.2s;
                cursor: pointer;
            }

            .menu-item:hover {
                background: #e8fdf4;
                color: #27ae60;
            }

            @keyframes popupShow {
                0% { opacity: 0; transform: translateY(-10px) scale(0.95); }
                100% { opacity: 1; transform: translateY(0) scale(1); }
            }

            .map-container {
                width: 100%;
                max-width: 1200px;
                margin: 20px auto;
                padding: 20px;
                background: var(--white);
                border-radius: 15px;
                box-shadow: var(--shadow);
                position: relative;
                display: flex;
                gap: 20px;
                transition: all 0.3s ease;
            }

            .map-container:hover {
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                transform: translateY(-2px);
            }

            #map {
                flex: 1;
                height: 600px;
                border-radius: 12px;
                overflow: hidden;
                border: 2px solid var(--primary-color);
            }

            .control-panel {
                width: 300px;
                background: var(--white);
                padding: 20px;
                border-radius: 15px;
                box-shadow: var(--shadow);
                transition: all 0.3s ease;
            }

            .control-panel:hover {
                transform: translateX(-5px);
            }

            .form-block {
                background: var(--white);
                border-radius: 12px;
                padding: 14px 16px;
                margin-bottom: 12px;
                box-shadow: var(--shadow);
                border: 1px solid rgba(0,0,0,0.1);
            }

            input[type="text"],
            input[type="file"] {
                width: 100%;
                padding: 10px;
                border: 1px solid #ddd;
                border-radius: 8px;
                margin-bottom: 15px;
            }

            input[type="text"]:focus {
                border-color: var(--primary-color);
                outline: none;
                box-shadow: 0 0 0 2px rgba(39, 174, 96, 0.2);
            }

            button {
                background: var(--primary-color);
                color: white;
                border: none;
                padding: 12px 20px;
                border-radius: var(--button-radius);
                cursor: pointer;
                font-weight: 500;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                gap: 8px;
                justify-content: center;
                width: 100%;
                margin-top: 10px;
            }

            button:hover {
                background: var(--primary-dark);
                transform: translateY(-2px);
            }

            h3 {
                color: var(--primary-dark);
                margin-bottom: 15px;
                display: flex;
                align-items: center;
                gap: 8px;
            }
            img {
                max-width: 150px;
                border-radius: 8px;
                margin: 10px 0;
            }

            .form-group {
                margin-bottom: 10px;
            }

            .form-group label {
                display: block;
                margin-bottom: 8px;
                color: var(--primary-dark);
                font-weight: 500;
            }

            .location-btn {
                background: var(--profile-color);
                margin-bottom: 10px;
            }

            .location-btn:hover {
                background: var(--profile-dark);
            }

            .submit-btn {
                background: var(--report-color);
            }

            .submit-btn:hover {
                background: var(--report-dark);
            }

            .floating-rank-btn {
                position: fixed;
                left: 32px;
                bottom: 32px;
                width: 62px;
                height: 62px;
                background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
                color: #fff;
                border-radius: 50%;
                box-shadow: 0 6px 24px rgba(46,204,113,0.18);
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 2.1rem;
                z-index: 2000;
                transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
                border: none;
                cursor: pointer;
                text-decoration: none;
                outline: none;
                opacity: 0.95;
            }
            .floating-rank-btn:hover {
                background: linear-gradient(135deg, #38f9d7 0%, #43e97b 100%);
                box-shadow: 0 10px 32px #43e97b55;
                transform: scale(1.08);
                color: #ffd700;
            }
            @media (max-width: 600px) {
                .floating-rank-btn {
                    left: 12px;
                    bottom: 12px;
                    width: 48px;
                    height: 48px;
                    font-size: 1.4rem;
                }
            }
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
                    <button type="submit" class="submit-btn">
                        <i class="fas fa-paper-plane"></i> Gửi báo cáo
                    </button>
                    <div id="loadingMsg" style="display: none; color: #27ae60; margin-top: 10px;">
                        <strong>⏳ Đang xử lý và phân tích ảnh qua AI... Vui lòng đợi (có thể mất tới 5 phút)</strong>
                    </div>
                </form>
            </div>
            <?php else: ?>
            <p><strong>Vui lòng đăng nhập để thêm điểm rác.</strong></p>
            <?php endif; ?>
            <h3 style="margin-top:30px"><i class="fas fa-list"></i> Danh sách điểm bạn đã đăng</h3>
            <?php if ($isLoggedIn): ?>
            <?php        
            // Function to check if columns exist in table
            function checkColumnExists($conn, $table, $column) {
                $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
                return $result->num_rows > 0;
            }

            // Check if AI columns exist
            $has_ai_analysis = checkColumnExists($conn, 'trash_points', 'ai_analysis');
            $has_ai_verified = checkColumnExists($conn, 'trash_points', 'ai_verified');

            $user_id = $_SESSION["user_id"];
            
            // Build SQL query based on available columns
            $select_fields = "*";
            $sql = "SELECT $select_fields FROM trash_points WHERE user_id = ?";
            
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            while ($row = $result->fetch_assoc()):
                // Set default values for missing columns
                if (!isset($row['ai_analysis'])) {
                    $row['ai_analysis'] = null;
                }
                if (!isset($row['ai_verified'])) {
                    $row['ai_verified'] = false;
                }
                
                $aiData = $row['ai_analysis'] ? json_decode($row['ai_analysis'], true) : null;
            ?>
            <div class="form-block">
                <form action="update_point.php" method="post" enctype="multipart/form-data" class="updatePointForm">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    Vĩ độ: <input type="text" name="lat" value="<?= $row['latitude'] ?>" required>                    Kinh độ: <input type="text" name="lng" value="<?= $row['longitude'] ?>" required><br><br>
                    Mô tả: <input type="text" name="description" value="<?= htmlspecialchars($row['description']) ?>"><br><br>
                    <?php if ($row['image_url']): ?>
                    Ảnh hiện tại:<br>
                    <img src="<?= $row['image_url'] ?>"><br>
                    <?php endif; ?>
                    Cập nhật ảnh: <input type="file" name="image"><br><br>
                    <button type="submit" class="updateBtn">Cập nhật</button>
                    <div class="updateLoadingMsg" style="display: none; color: #27ae60; margin-top: 10px;">
                        <strong>⏳ Đang xử lý và phân tích ảnh qua AI... Vui lòng đợi (có thể mất tới 5 phút)</strong>
                    </div>
                </form>
            </div>
            <?php endwhile; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
    const map = L.map('map').setView([10.762622, 106.660172], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);    // Hiển thị điểm rác của người dùng
    fetch('get_points.php')
        .then(res => res.json())        .then(data => {
            data.forEach(point => {                let popup = `<div style="font-family: 'Roboto', sans-serif;">
                    <h4 style="margin: 0 0 10px 0; color: #2c3e50; border-bottom: 2px solid #2ecc71; padding-bottom: 5px;">
                        ${point.description}
                    </h4>`;
                
                // Thêm thông tin AI nếu có
                if (point.ai_verified && point.ai_data) {
                    const aiData = point.ai_data;
                    popup += `<div style="background: linear-gradient(135deg, #e8f5e8, #d4edda); padding: 8px; margin: 8px 0; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">`;
                    popup += `<strong style="color: #28a745; font-size: 12px;">🤖 Phân tích AI:</strong> `;
                    if (aiData.image_type === 'trash') {
                        const pollutionTexts = {
                            1: ['Rác rất ít', '#28a745'],
                            2: ['Rác ít', '#ffc107'],
                            3: ['Rác trung bình', '#fd7e14'],
                            4: ['Rác nghiêm trọng', '#dc3545']
                        };
                        const [text, color] = pollutionTexts[aiData.pollution_level] || ['Xác nhận rác', '#28a745'];
                        popup += `<span style="color: ${color}; font-weight: 500;">${text}</span>`;
                        if (aiData.pollution_level >= 3) {
                            popup += ` <span style="color: #dc3545;">⚠️</span>`;
                        }
                    } else {
                        popup += '<span style="color: #6c757d;">Không xác định được</span>';
                    }
                    popup += `</div>`;
                } else if (!point.ai_verified) {
                    popup += `<div style="background: linear-gradient(135deg, #fff3cd, #ffeeba); padding: 8px; margin: 8px 0; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.05);">
                        <span style="color: #856404; font-size: 12px;"></span>
                    </div>`;
                }
                
                if (point.image_url) {
                    popup += `<div style="margin-top: 10px;">
                        <img src="${point.image_url}" style="width: 100%; max-width: 200px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                    </div>`;
                }
                popup += '</div>';
                
                L.marker([point.latitude, point.longitude]).addTo(map).bindPopup(popup);
            });
        });

    // Lấy vị trí hiện tại và điền vào form
    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    document.getElementById('lat').value = lat;
                    document.getElementById('lng').value = lng;

                    map.setView([lat, lng], 16);
                    L.marker([lat, lng]).addTo(map).bindPopup("📍 Vị trí của bạn").openPopup();
                },
                function(error) {
                    alert("Không thể lấy vị trí: " + error.message);
                }
            );
        } else {
            alert("Trình duyệt không hỗ trợ định vị vị trí.");
        }
    }    // Cho chọn điểm trên bản đồ (nếu muốn)
    <?php if ($isLoggedIn): ?>
    map.on('click', function(e) {
        document.getElementById('lat').value = e.latlng.lat;
        document.getElementById('lng').value = e.latlng.lng;
    });    // Xử lý form submission với loading indicator
    document.getElementById('addPointForm').addEventListener('submit', function(e) {
        const fileInput = document.querySelector('input[type="file"]');
        const submitBtn = document.getElementById('submitBtn');
        const loadingMsg = document.getElementById('loadingMsg');
        
        // Nếu có file được chọn, hiển thị loading message
        if (fileInput.files.length > 0) {
            submitBtn.disabled = true;
            submitBtn.textContent = 'Đang xử lý...';
            loadingMsg.style.display = 'block';
            
            // Set timeout cảnh báo nếu quá lâu
            setTimeout(function() {
                if (submitBtn.disabled) {
                    alert('Quá trình xử lý đang mất nhiều thời gian hơn dự kiến. Vui lòng tiếp tục đợi...');
                }
            }, 60000); // 1 phút
        }
    });

    // Xử lý các form update
    document.querySelectorAll('.updatePointForm').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            const fileInput = this.querySelector('input[type="file"]');
            const submitBtn = this.querySelector('.updateBtn');
            const loadingMsg = this.querySelector('.updateLoadingMsg');
            
            // Nếu có file được chọn, hiển thị loading message
            if (fileInput.files.length > 0) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Đang xử lý...';
                loadingMsg.style.display = 'block';
                
                // Set timeout cảnh báo nếu quá lâu
                setTimeout(function() {
                    if (submitBtn.disabled) {
                        alert('Quá trình xử lý đang mất nhiều thời gian hơn dự kiến. Vui lòng tiếp tục đợi...');
                    }
                }, 60000); // 1 phút
            }
        });
    });
    <?php endif; ?>

    function toggleUserMenu() {
        const menu = document.getElementById('userMenu');
        menu.classList.toggle('active');
        // Đóng popup khi click ra ngoài
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
            <!-- Cột 2: Hạng 2 -->
            <rect x="8" y="28" width="16" height="28" rx="4" fill="#b0c4de"/>
            <text x="16" y="50" text-anchor="middle" font-size="16" fill="#fff" font-weight="bold" dy=".3em">2</text>
            <!-- Cột 1: Hạng 1 (cao nhất) -->
            <rect x="24" y="12" width="16" height="44" rx="4" fill="#ffd700"/>
            <text x="32" y="38" text-anchor="middle" font-size="16" fill="#fff" font-weight="bold" dy=".3em">1</text>
            <!-- Cột 3: Hạng 3 -->
            <rect x="40" y="36" width="16" height="20" rx="4" fill="#cd7f32"/>
            <text x="48" y="50" text-anchor="middle" font-size="16" fill="#fff" font-weight="bold" dy=".3em">3</text>
        </svg>
    </a>
    <style>
    .floating-rank-btn {
        position: fixed;
        left: 32px;
        bottom: 32px;
        width: 62px;
        height: 62px;
        background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        color: #fff;
        border-radius: 50%;
        box-shadow: 0 6px 24px rgba(46,204,113,0.18);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.1rem;
        z-index: 2000;
        transition: background 0.2s, box-shadow 0.2s, transform 0.15s;
        border: none;
        cursor: pointer;
        text-decoration: none;
        outline: none;
        opacity: 0.95;
    }
    .floating-rank-btn:hover {
        background: linear-gradient(135deg, #38f9d7 0%, #43e97b 100%);
        box-shadow: 0 10px 32px #43e97b55;
        transform: scale(1.08);
        color: #ffd700;
    }
    @media (max-width: 600px) {
        .floating-rank-btn {
            left: 12px;
            bottom: 12px;
            width: 48px;
            height: 48px;
            font-size: 1.4rem;
        }
        .floating-rank-btn svg {
            width: 24px;
            height: 24px;
        }
    }
    .floating-rank-btn svg {
        display: block;
    }
    </style>
    
    </body>
    </html>
