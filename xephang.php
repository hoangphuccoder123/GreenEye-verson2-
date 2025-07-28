<?php
session_start();
require 'db.php';

$isLoggedIn = isset($_SESSION["user_id"]);
$current_user_id = $isLoggedIn ? $_SESSION["user_id"] : null;
$current_user_name = null;
$current_user_points = 0;
$isAdmin = false;

// Truy vấn thông tin người dùng đang đăng nhập
if ($isLoggedIn) {
    $stmt = $conn->prepare("SELECT name, social_points, role FROM users WHERE id = ?");
    $stmt->bind_param("i", $current_user_id);
    $stmt->execute();
    $stmt->bind_result($current_user_name, $current_user_points, $role);
    $stmt->fetch();
    $stmt->close();

    $isAdmin = ($role === 'admin');
}

// Lấy danh sách người dùng không phải admin để xếp hạng
$sql = "SELECT id, name, social_points FROM users WHERE role != 'admin' ORDER BY social_points DESC, id ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Bảng xếp hạng điểm - Green Eye</title>
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(120deg, #e0ffe8 0%, #f7f9fb 100%);
            min-height: 100vh;
            padding: 20px;
        }

        h1 {
            color: #2ecc71;
            text-align: center;
            font-size: 2.3rem;
            letter-spacing: 1px;
            margin-bottom: 10px;
            text-shadow: 0 2px 8px #b2f7c7;
        }

        .my-score {
            background: rgba(46,204,113,0.08);
            padding: 18px 24px;
            margin: 24px auto 10px auto;
            max-width: 600px;
            border-left: 7px solid #2ecc71;
            border-radius: 10px;
            font-size: 1.13rem;
            box-shadow: 0 3px 12px rgba(46,204,113,0.08);
            color: #1a5d3a;
            animation: fadeIn 1s;
        }

        table.leaderboard {
            width: 100%;
            max-width: 700px;
            margin: 24px auto 0 auto;
            border-collapse: separate;
            border-spacing: 0;
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(46,204,113,0.10);
            overflow: hidden;
            animation: fadeInUp 1.2s;
        }

        table.leaderboard th, table.leaderboard td {
            padding: 16px 18px;
            border-bottom: 1px solid #f1f1f1;
            text-align: left;
            font-size: 1.08rem;
        }

        table.leaderboard th {
            background: linear-gradient(90deg, #2ecc71 80%, #27ae60 100%);
            color: #fff;
            font-size: 1.13rem;
            letter-spacing: 0.5px;
        }

        table.leaderboard tr.highlight {
            background: #eafaf1 !important;
            font-weight: bold;
            color: #27ae60;
            box-shadow: 0 0 0 2px #2ecc7133;
        }

        table.leaderboard tr:hover {
            background: #f2fff6;
            transition: background 0.2s;
        }

        .cup {
            font-size: 1.5em;
            vertical-align: middle;
            margin-right: 2px;
        }
        .cup.gold { filter: drop-shadow(0 0 4px #ffd70088); }
        .cup.silver { filter: drop-shadow(0 0 4px #b0c4de88); }
        .cup.bronze { filter: drop-shadow(0 0 4px #cd7f3288); }

        .back-btn {
            display: block;
            width: 160px;
            margin: 36px auto 0 auto;
            padding: 12px;
            background: linear-gradient(90deg, #27ae60 80%, #2ecc71 100%);
            color: white;
            text-align: center;
            text-decoration: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 500;
            box-shadow: 0 2px 8px #b2f7c7;
            transition: background 0.2s, box-shadow 0.2s;
        }

        .back-btn:hover {
            background: #219150;
            box-shadow: 0 4px 16px #b2f7c7;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(60px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<h1>🏆 Bảng xếp hạng người dùng</h1>

<?php if ($isLoggedIn && !$isAdmin): ?>
    <div class="my-score">
        <strong>🎯 Điểm của bạn:</strong><br>
        Tên: <strong><?= htmlspecialchars($current_user_name) ?></strong><br>
        Điểm xã hội: <strong><?= $current_user_points ?></strong>
    </div>
<?php endif; ?>

<table class="leaderboard">
    <tr>
        <th>Hạng</th>
        <th>Tên người dùng</th>
        <th>Điểm xã hội</th>
    </tr>

    <?php
    $rank = 1;
    $cup_icons = [
        1 => '<span class="cup gold" title="Hạng 1">🥇</span>',
        2 => '<span class="cup silver" title="Hạng 2">🥈</span>',
        3 => '<span class="cup bronze" title="Hạng 3">🥉</span>'
    ];
    while ($row = $result->fetch_assoc()) {
        $highlight = ($row['id'] == $current_user_id && !$isAdmin) ? "class='highlight'" : "";
        echo "<tr $highlight>";
        echo "<td>";
        if (isset($cup_icons[$rank])) {
            echo $cup_icons[$rank];
        } else {
            echo $rank;
        }
        echo "</td>";
        echo "<td style='display:flex;align-items:center;gap:8px;'>";
        // Avatar ngẫu nhiên (chữ cái đầu)
        $avatar_bg = ['#2ecc71','#27ae60','#16a085','#1abc9c','#f39c12','#e67e22','#e74c3c','#9b59b6','#2980b9'];
        $bg = $avatar_bg[$rank % count($avatar_bg)];
        $initial = mb_strtoupper(mb_substr($row['name'],0,1,'UTF-8'));
        echo "<span style='width:32px;height:32px;display:inline-flex;align-items:center;justify-content:center;background:$bg;color:#fff;border-radius:50%;font-weight:bold;font-size:1.1em;'>$initial</span>";
        echo htmlspecialchars($row['name']);
        echo "</td>";
        echo "<td>" . intval($row['social_points']) . "</td>";
        echo "</tr>";
        $rank++;
    }
    ?>
</table>

<a href="index.php" class="back-btn">← Quay về</a>

</body>
</html>
