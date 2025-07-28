<?php
/**
 * Cấu hình Timeout cho ứng dụng Green Eye
 * Tăng các giá trị này nếu API AI xử lý chậm
 */

// Timeout cho script PHP (giây)
define('PHP_EXECUTION_TIMEOUT', 300); // 5 phút

// Timeout cho cURL request tới LMStudio API (giây)  
define('API_TIMEOUT', 300); // 5 phút
define('API_CONNECT_TIMEOUT', 30); // 30 giây để kết nối

// Thời gian cảnh báo cho người dùng (milliseconds)
define('USER_WARNING_TIMEOUT', 60000); // 1 phút

// Cấu hình upload file
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('UPLOAD_TIMEOUT', 120); // 2 phút cho upload

?>
