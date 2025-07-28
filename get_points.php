<?php
session_start();
if (!isset($_SESSION["user_id"])) {
    echo json_encode([]);
    exit();
}
require 'db.php';

// Function to check if columns exist in table
function checkColumnExists($conn, $table, $column) {
    $result = $conn->query("SHOW COLUMNS FROM $table LIKE '$column'");
    return $result->num_rows > 0;
}

// Check if AI columns exist
$has_ai_analysis = checkColumnExists($conn, 'trash_points', 'ai_analysis');
$has_ai_verified = checkColumnExists($conn, 'trash_points', 'ai_verified');
$has_image_url = checkColumnExists($conn, 'trash_points', 'image_url');

$user_id = $_SESSION["user_id"];

// Build SQL query based on available columns
$select_fields = "id, latitude, longitude, description";
if ($has_image_url) {
    $select_fields .= ", image_url";
}
if ($has_ai_analysis) {
    $select_fields .= ", ai_analysis";
}
if ($has_ai_verified) {
    $select_fields .= ", ai_verified";
}

$sql = "SELECT $select_fields FROM trash_points WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$points = [];
while ($row = $result->fetch_assoc()) {
    // Set default values for missing columns
    if (!$has_image_url) {
        $row['image_url'] = null;
    }
    if (!$has_ai_verified) {
        $row['ai_verified'] = false;
    }
    if (!$has_ai_analysis) {
        $row['ai_analysis'] = null;
    }    // Parse AI analysis nếu có
    if ($row['ai_analysis']) {
        $row['ai_data'] = json_decode($row['ai_analysis'], true);
    }
    $points[] = $row;
}

echo json_encode($points);
