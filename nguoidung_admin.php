<?php
require_once 'db.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trang quản lý người dùng - GreenEye</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gradient-to-br from-green-100 via-green-50 to-blue-100 min-h-screen font-sans">
  <div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-green-700 flex items-center gap-3">
      <i class="fas fa-users-cog text-green-500"></i> Trang Quản Lý Người Dùng - GreenEye
    </h1>

    <!-- Thông tin người dùng -->
    <div class="bg-white rounded-2xl shadow-xl p-6 mb-8 border border-green-200 animate-fade-in">
      <h2 class="text-2xl font-semibold mb-4 text-green-700 flex items-center gap-2"><i class="fas fa-user"></i> Danh sách người dùng</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto rounded-xl overflow-hidden">
          <thead>
            <tr class="bg-gradient-to-r from-green-200 to-green-100 text-green-900">
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Tên</th>
              <th class="px-4 py-2">Email</th>
              <th class="px-4 py-2">Điểm phân loại</th>
              <th class="px-4 py-2">Điểm xã hội</th>
              <th class="px-4 py-2">Vai trò</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $user_sql = "SELECT id, name, email, points, social_points, role FROM users";
            $user_result = $conn->query($user_sql);
            if (!$user_result) {
                die("Lỗi truy vấn người dùng: " . $conn->error);
            }
            while ($row = $user_result->fetch_assoc()) {
                echo "<tr class='hover:bg-green-50 transition'>";
                echo "<td class='border px-4 py-2 text-center'>{$row['id']}</td>";
                echo "<td class='border px-4 py-2 font-medium flex items-center gap-2'>";
                $initial = mb_strtoupper(mb_substr($row['name'],0,1,'UTF-8'));
                echo "<span class='w-8 h-8 rounded-full bg-green-200 text-green-700 flex items-center justify-center font-bold text-lg shadow'>{$initial}</span>";
                echo htmlspecialchars($row['name']);
                echo "</td>";
                echo "<td class='border px-4 py-2'>{$row['email']}</td>";
                echo "<td class='border px-4 py-2 text-center text-green-600 font-semibold'>{$row['points']}</td>";
                echo "<td class='border px-4 py-2 text-center text-yellow-600 font-semibold'>{$row['social_points']}</td>";
                $roleIcon = $row['role'] === 'admin' ? '<i class=\'fas fa-user-shield text-blue-600\'></i>' : ($row['role'] === 'cong_tac_vien' ? '<i class=\'fas fa-user-friends text-green-600\'></i>' : '<i class=\'fas fa-user text-gray-500\'></i>');
                echo "<td class='border px-4 py-2 text-center'>{$roleIcon} <span class='ml-1'>".ucwords(str_replace('_',' ',$row['role']))."</span></td>";
                echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Báo cáo bãi rác -->
    <div class="bg-white rounded-2xl shadow-xl p-6 border border-green-200 animate-fade-in">
      <h2 class="text-2xl font-semibold mb-4 text-green-700 flex items-center gap-2"><i class="fas fa-dumpster"></i> Danh sách báo cáo bãi rác</h2>
      <div class="overflow-x-auto">
        <table class="min-w-full table-auto rounded-xl overflow-hidden">
          <thead>
            <tr class="bg-gradient-to-r from-green-200 to-green-100 text-green-900">
              <th class="px-4 py-2">ID</th>
              <th class="px-4 py-2">Người gửi</th>
              <th class="px-4 py-2">Thời gian</th>
              <th class="px-4 py-2">Tọa độ</th>
              <th class="px-4 py-2">Ảnh</th>
              <th class="px-4 py-2">Mô tả</th>
            </tr>
          </thead>
          <tbody>
            <?php
            $report_sql = "SELECT tp.id, u.name AS user_name, tp.created_at, tp.latitude, tp.longitude, tp.image_url, tp.description 
                           FROM trash_points tp 
                           JOIN users u ON tp.user_id = u.id 
                           ORDER BY tp.created_at DESC";
            $report_result = $conn->query($report_sql);
            if (!$report_result) {
                die("Lỗi truy vấn báo cáo: " . $conn->error);
            }
            while ($row = $report_result->fetch_assoc()) {
                echo "<tr class='hover:bg-green-50 transition'>";
                echo "<td class='border px-4 py-2 text-center'>{$row['id']}</td>";
                echo "<td class='border px-4 py-2 font-medium'>".htmlspecialchars($row['user_name'])."</td>";
                echo "<td class='border px-4 py-2 text-center'>{$row['created_at']}</td>";
                echo "<td class='border px-4 py-2 text-center'><span class='inline-block bg-green-100 text-green-700 px-2 py-1 rounded'>".number_format($row['latitude'],5).", ".number_format($row['longitude'],5)."</span></td>";
                if ($row['image_url']) {
                  echo "<td class='border px-4 py-2 text-center'><a href='{$row['image_url']}' target='_blank'><img src='{$row['image_url']}' alt='Ảnh' class='w-16 h-16 object-cover rounded shadow hover:scale-110 transition'></a></td>";
                } else {
                  echo "<td class='border px-4 py-2 text-center text-gray-400 italic'>Không có</td>";
                }
                echo "<td class='border px-4 py-2'>".htmlspecialchars($row['description'])."</td>";
                echo "</tr>";
            }
            ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <style>
    @keyframes fade-in { from { opacity: 0; transform: translateY(30px);} to { opacity: 1; transform: none;}}
    .animate-fade-in { animation: fade-in 0.8s; }
  </style>
</body>
</html>
