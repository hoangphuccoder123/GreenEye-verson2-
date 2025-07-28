# Hướng dẫn khắc phục lỗi Database & Cấu hình Timeout - Green Eye

## Mô tả lỗi Database

Các lỗi bạn gặp phải do thiếu các cột trong bảng `trash_points` của database:

1. `Warning: Undefined array key "ai_verified"` - Thiếu cột `ai_verified`
2. `Fatal error: Unknown column 'ai_analysis' in 'field list'` - Thiếu cột `ai_analysis`

## Cấu hình Timeout

Ứng dụng đã được cấu hình với timeout **5 phút** cho việc xử lý AI:

- **Server timeout**: 5 phút (300 giây)
- **API timeout**: 5 phút cho LMStudio API
- **User warning**: Cảnh báo sau 1 phút nếu xử lý chậm
- **Connection timeout**: 30 giây để kết nối

### Thay đổi timeout (nếu cần)

Chỉnh sửa file `config_timeout.php`:
```php
define('PHP_EXECUTION_TIMEOUT', 600); // 10 phút
define('API_TIMEOUT', 600); // 10 phút cho API
```

## Cách khắc phục

### Phương án 1: Sử dụng script tự động (Khuyến nghị)

1. Mở trình duyệt và truy cập: `http://localhost/e2/setup_database.php`
2. Script sẽ tự động kiểm tra và thêm các cột thiếu
3. Sau khi hoàn tất, trở lại trang chủ

### Phương án 2: Thực hiện thủ công qua phpMyAdmin

1. Mở phpMyAdmin
2. Chọn database `greeneye`
3. Chọn bảng `trash_points`
4. Chạy các câu lệnh SQL sau:

```sql
-- Thêm cột image_url nếu chưa có
ALTER TABLE trash_points ADD COLUMN image_url VARCHAR(255) NULL;

-- Thêm cột ai_analysis để lưu kết quả phân tích AI
ALTER TABLE trash_points ADD COLUMN ai_analysis TEXT NULL;

-- Thêm cột ai_verified để đánh dấu đã được AI phân tích
ALTER TABLE trash_points ADD COLUMN ai_verified BOOLEAN DEFAULT FALSE;

-- Thêm index cho tối ưu query
CREATE INDEX idx_user_ai_verified ON trash_points(user_id, ai_verified);
CREATE INDEX idx_ai_verified ON trash_points(ai_verified);
```

### Phương án 3: Chạy từ file SQL

1. Import file `database_update.sql` vào phpMyAdmin
2. Hoặc chạy từ command line:
```bash
mysql -u root -p greeneye < database_update.sql
```

## Kiểm tra sau khi khắc phục

1. Truy cập trang chủ `index.php` - không còn warning
2. Upload ảnh mới - không còn fatal error
3. Trang AI Dashboard hoạt động bình thường

## Cấu trúc bảng sau khi cập nhật

Bảng `trash_points` sẽ có các cột:
- `id` (INT, PRIMARY KEY, AUTO_INCREMENT)
- `user_id` (INT)
- `latitude` (DECIMAL)
- `longitude` (DECIMAL) 
- `description` (TEXT)
- `image_url` (VARCHAR(255), NULL) - **Mới thêm**
- `ai_analysis` (TEXT, NULL) - **Mới thêm**
- `ai_verified` (BOOLEAN, DEFAULT FALSE) - **Mới thêm**
- `created_at` (TIMESTAMP, DEFAULT CURRENT_TIMESTAMP)

## Ghi chú

- Các file PHP đã được cập nhật để xử lý trường hợp thiếu cột
- Nếu vẫn gặp lỗi, hãy chạy `setup_database.php` để kiểm tra chi tiết
- Ứng dụng sẽ hoạt động bình thường ngay cả khi thiếu một số cột AI (chỉ không có tính năng AI)
