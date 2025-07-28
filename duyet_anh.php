<?php
require_once 'db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $sql = "UPDATE trash_points SET verified = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Duyệt ảnh rác - GreenEye</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gradient-to-br from-green-50 via-green-100 to-green-200 min-h-screen p-6">
  <div class="max-w-6xl mx-auto">
    <h1 class="text-3xl font-bold mb-6 text-green-700 flex items-center gap-3 drop-shadow-lg animate-fade-in-down">
      <i class="fas fa-check-circle text-green-500 animate-bounce"></i> Duyệt ảnh rác - GreenEye
    </h1>

    <?php
    $sql = "SELECT tp.id, tp.latitude, tp.longitude, tp.image_url, tp.description, u.name AS user_name, tp.created_at
            FROM trash_points tp
            JOIN users u ON tp.user_id = u.id
            WHERE tp.verified = 0
            ORDER BY tp.created_at DESC";
    $result = $conn->query($sql);
    if ($result->num_rows === 0) {
        echo "<p class='text-gray-600 italic text-lg text-center mt-16 animate-fade-in'>Không có ảnh cần duyệt.</p>";
    } else {
        echo "<div class='grid md:grid-cols-2 lg:grid-cols-3 gap-8 animate-fade-in-up'>";
        while ($row = $result->fetch_assoc()) {
            echo "<div class='bg-white shadow-2xl rounded-2xl overflow-hidden border-2 border-green-100 hover:shadow-green-200 transition-shadow duration-300 group relative'>";
            echo "<div class='relative'>";
            echo "<img src='{$row['image_url']}' class='w-full h-56 object-cover group-hover:scale-105 transition-transform duration-300'>";
            echo "<span class='absolute top-2 right-2 bg-green-600 text-white text-xs px-3 py-1 rounded-full shadow-md animate-pulse'><i class='fas fa-image'></i> Ảnh mới</span>";
            echo "</div>";
            echo "<div class='p-5 flex flex-col gap-2'>";
            echo "<p class='text-green-700 font-bold mb-1 flex items-center gap-2'><i class='fas fa-user'></i> {$row['user_name']}</p>";
            echo "<p class='text-xs text-gray-500 mb-1 flex items-center gap-2'><i class='fas fa-clock'></i> {$row['created_at']}</p>";
            echo "<p class='text-xs text-gray-500 mb-1 flex items-center gap-2'><i class='fas fa-map-marker-alt'></i> <span class='font-semibold'>{$row['latitude']}, {$row['longitude']}</span></p>";
            echo "<p class='text-sm text-gray-800 mb-2 flex items-center gap-2'><i class='fas fa-align-left'></i> <span>" . htmlspecialchars($row['description']) . "</span></p>";
            echo "<form method='POST' action='' onsubmit='return confirm(\"Bạn có chắc muốn duyệt ảnh này?\")' class='mt-2'>";
            echo "<input type='hidden' name='id' value='{$row['id']}'>";
            echo "<button type='submit' class='bg-gradient-to-r from-green-500 to-green-600 text-white px-4 py-2 rounded-lg shadow hover:from-green-600 hover:to-green-700 hover:scale-105 transition-all duration-200 w-full font-semibold flex items-center justify-center gap-2'><i class='fas fa-check'></i> Duyệt</button>";
            echo "</form>";
            echo "</div>";
            echo "<div class='absolute left-0 top-0 w-full h-full bg-green-50 opacity-0 group-hover:opacity-30 transition-opacity duration-300 pointer-events-none'></div>";
            echo "</div>";
        }
        echo "</div>";
    }
    ?>
  </div>
  <style>
    @keyframes fade-in-down { from { opacity: 0; transform: translateY(-30px);} to { opacity: 1; transform: translateY(0);} }
    @keyframes fade-in-up { from { opacity: 0; transform: translateY(30px);} to { opacity: 1; transform: translateY(0);} }
    .animate-fade-in-down { animation: fade-in-down 0.7s cubic-bezier(.4,0,.2,1) both; }
    .animate-fade-in-up { animation: fade-in-up 0.7s cubic-bezier(.4,0,.2,1) both; }
  </style>
</body>
</html>
