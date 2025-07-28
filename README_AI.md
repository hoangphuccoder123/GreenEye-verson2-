# Green Eye - Hệ thống Báo cáo Điểm rác với AI

## 🚀 Tính năng mới: Tích hợp LMStudio AI

Hệ thống đã được nâng cấp với tính năng phân tích ảnh thông minh sử dụng LMStudio để tự động xác định và đánh giá mức độ ô nhiễm của điểm rác.

## 📋 Cài đặt và Cấu hình

### 1. Cập nhật Database
Chạy file `database_update.sql` để thêm các cột mới:
```sql
-- Thêm các cột cho AI analysis
ALTER TABLE trash_points ADD COLUMN IF NOT EXISTS image_url VARCHAR(255) NULL;
ALTER TABLE trash_points ADD COLUMN IF NOT EXISTS ai_analysis TEXT NULL;
ALTER TABLE trash_points ADD COLUMN IF NOT EXISTS ai_verified BOOLEAN DEFAULT FALSE;
```

### 2. Cài đặt LMStudio
1. Tải và cài đặt LMStudio từ: https://lmstudio.ai/
2. Tải model `google/gemma-3-4b` trong LMStudio
3. Khởi động Local Server trên port 1234
4. Đảm bảo server chạy tại: `http://ai.vnpthaiphong.vn:1234`

### 3. Tạo thư mục uploads
```bash
mkdir uploads
chmod 755 uploads
```

## 🤖 Cách thức hoạt động của AI

### Phân tích ảnh tự động
- Khi người dùng upload ảnh, hệ thống sẽ gửi ảnh đến LMStudio
- AI sẽ phân tích và trả về kết quả dạng JSON:
```json
{
  "image_type": "trash",
  "pollution_level": 3
}
```

### Xử lý kết quả
- **image_type: "trash"**: Ảnh chứa rác thải → Cho phép lưu
- **image_type: "irrelevant"**: Ảnh không liên quan → Từ chối lưu
- **pollution_level**: Mức độ ô nhiễm từ 1-4
  - 1: Rất nhẹ
  - 2: Nhẹ  
  - 3: Trung bình
  - 4: Nghiêm trọng

### Fallback Logic
- Nếu LMStudio không khả dụng → Lưu theo logic cũ (không AI)
- Nếu có lỗi kết nối → Tiếp tục lưu bình thường
- Đảm bảo hệ thống luôn hoạt động ổn định

## 📊 Dashboard AI (Dành cho Admin)

Truy cập `/ai_dashboard.php` để xem:
- Thống kê tổng quan về AI analysis
- Tỷ lệ ảnh được xác nhận/từ chối
- Phân bố mức độ ô nhiễm
- Danh sách điểm rác gần đây có AI analysis
- Trạng thái kết nối LMStudio

## 🔧 Test và Debug

### Test kết nối LMStudio
Truy cập `/test_lmstudio.php` để:
- Kiểm tra health của LMStudio
- Test phân tích ảnh mẫu
- Xem thông tin cấu hình

### Log lỗi
Kiểm tra error logs để debug:
```bash
tail -f /var/log/apache2/error.log
# hoặc
tail -f /var/log/nginx/error.log
```

## 🎯 Tính năng AI hiện có

### 1. Phân tích ảnh thông minh
- Tự động nhận diện ảnh có chứa rác thải
- Đánh giá mức độ ô nhiễm
- Từ chối ảnh spam, selfie, meme

### 2. Hiển thị thông tin AI
- Badge AI trên bản đồ
- Thông tin phân tích trong danh sách điểm rác
- Màu sắc phân biệt mức độ ô nhiễm

### 3. Dashboard quản lý
- Thống kê hiệu suất AI
- Theo dõi tỷ lệ chính xác
- Phân tích xu hướng ô nhiễm

## ⚙️ Cấu hình LMStudio

### File cấu hình: `lm_service.php`
```php
// Thay đổi URL và model nếu cần
$lmService = new LMStudioService(
    'http://ai.vnpthaiphong.vn:1234/v1/chat/completions',
    'google/gemma-3-4b'
);
```

### Timeout Settings
- Connection timeout: 10 giây
- Request timeout: 30 giây
- Health check timeout: 5 giây

## 🔒 Bảo mật

### Validation ảnh
- Kiểm tra file extension
- Validate MIME type
- Giới hạn kích thước file
- Scan ảnh qua AI trước khi lưu

### API Security
- Local API calls (127.0.0.1)
- Timeout protection
- Error handling
- Fallback mechanisms

## 🚀 Hướng phát triển

### Tính năng tiếp theo
- [ ] Batch analysis cho ảnh cũ
- [ ] API public cho mobile app
- [ ] Machine learning feedback loop
- [ ] Advanced image preprocessing
- [ ] Multi-model support

### Cải thiện AI
- [ ] Fine-tuning model cho rác Việt Nam
- [ ] Object detection cho loại rác cụ thể
- [ ] Sentiment analysis cho mô tả
- [ ] Auto-tagging và categorization

## 📞 Hỗ trợ

### Lỗi thường gặp
1. **LMStudio không kết nối được**
   - Kiểm tra LMStudio đang chạy
   - Verify port 1234 đang mở
   - Check firewall settings

2. **AI analysis không hoạt động**
   - Kiểm tra model đã load chưa
   - Verify API response format
   - Check error logs

3. **Upload ảnh lỗi**
   - Kiểm tra quyền thư mục uploads
   - Verify file size limits
   - Check image format support

### Debug Commands
```bash
# Test LMStudio API
curl http://ai.vnpthaiphong.vn:1234/health

# Check uploads permission
ls -la uploads/

# Monitor logs
tail -f error.log
```

---

## 📝 Changelog

### v2.0 - AI Integration
- ✅ Tích hợp LMStudio AI
- ✅ Automatic image analysis
- ✅ AI Dashboard cho admin
- ✅ Fallback logic
- ✅ Health monitoring
- ✅ Pollution level detection
