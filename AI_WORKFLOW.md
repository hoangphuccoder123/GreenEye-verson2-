# Luồng Quy Trình Tích Hợp AI - Green Eye

## 📋 Tổng Quan
Hệ thống Green Eye tích hợp AI để tự động phân tích và xác thực các điểm rác được báo cáo bởi người dùng.

---

## 🔄 Luồng Quy Trình Chi Tiết

### 1. 👤 Người Dùng Báo Cáo
```
Người dùng → Tải lên ảnh + Vị trí + Mô tả
              ↓
         add_point.php
```

### 2. 📤 Xử Lý Upload
```
add_point.php:
├── Kiểm tra đăng nhập
├── Lưu ảnh vào thư mục uploads/
├── Chuyển ảnh sang base64
└── Gọi LMStudioService
```

### 3. 🤖 Phân Tích AI
```
LMStudioService.php:
├── Chuẩn bị payload gửi tới AI
│   ├── model: "google/gemma-3-4b"
│   ├── temperature: 0.3
│   └── messages: [system_prompt + image]
│
├── Gửi request tới AI Server
│   └── POST http://ai.vnpthaiphong.vn:1234/v1/chat/completions
│
└── Nhận response từ AI
```

### 4. 📊 Response Từ AI
```json
{
    "id": "chatcmpl-xxx",
    "object": "chat.completion", 
    "created": 1750325668,
    "model": "google/gemma-3-4b",
    "choices": [
        {
            "index": 0,
            "message": {
                "role": "assistant",
                "content": "```json\n{\n  \"image_type\": \"trash\",\n  \"pollution_level\": 2\n}\n```"
            }
        }
    ]
}
```

### 5. 🔍 Xử Lý Kết Quả AI
```
LMStudioService.php:
├── Parse JSON từ choices[0].message.content
├── Xử lý markdown (```json```) nếu có
└── Trả về: {"image_type": "trash", "pollution_level": 2}
```

### 6. ✅ Validation & Lưu Database
```
add_point.php:
├── Kiểm tra image_type
│   ├── Nếu "irrelevant" → Từ chối + Xóa ảnh
│   └── Nếu "trash" → Tiếp tục
│
├── Cập nhật description với thông tin AI
│   └── "[AI: Rác thải ít ở khu vực nhỏ]"
│
└── Lưu vào database:
    ├── ai_analysis: JSON string
    ├── ai_verified: true
    └── Các thông tin khác
```

### 7. 📱 Hiển Thị Cho Người Dùng
```
Frontend (index.php):
├── Danh sách điểm rác
│   └── Hiển thị badge AI với mức độ ô nhiễm
│
└── Bản đồ
    ├── Marker với popup thông tin AI
    └── Icon tùy theo mức độ nghiêm trọng
```

---

## 🎯 Chi Tiết Từng Bước

### Bước 1: System Prompt
```
"You are an environmental expert specialized in analyzing urban waste images. 
Given the uploaded image, respond in the following JSON format only:
{ 
  "image_type": "trash" | "irrelevant", 
  "pollution_level": 1 | 2 | 3 | 4 | null 
}

Rules:
- If image contains garbage → "image_type": "trash"
- Otherwise → "image_type": "irrelevant"

Pollution levels (only if trash):
1: Very minimal trash
2: Small amount of trash in localized area  
3: Medium amount with moderate environmental impact
4: Large/severe pollution with major environmental impact

Return only JSON. No explanations."
```

### Bước 2: Phân Loại Kết Quả
| image_type | pollution_level | Hành động |
|------------|----------------|-----------|
| "irrelevant" | null | ❌ Từ chối báo cáo |
| "trash" | 1 | ✅ Lưu - Mức độ rất nhẹ |
| "trash" | 2 | ✅ Lưu - Mức độ nhẹ |
| "trash" | 3 | ✅ Lưu - Mức độ trung bình ⚠️ |
| "trash" | 4 | ✅ Lưu - Mức độ nghiêm trọng ⚠️ |

### Bước 3: Hiển Thị UI
```
🤖 Phân tích AI: Đã xác nhận là điểm rác (Mức độ nhẹ)
   ↑                    ↑                    ↑
Badge AI           Trạng thái         Mức độ chi tiết
```

---

## 📁 Files Liên Quan

### Core Files
- `lm_service.php` - Service giao tiếp với AI
- `add_point.php` - Xử lý upload và phân tích
- `get_points.php` - API lấy dữ liệu điểm rác
- `index.php` - Frontend hiển thị

### Config Files  
- `config_timeout.php` - Cấu hình timeout
- `db.php` - Kết nối database

### Database Schema
```sql
ALTER TABLE trash_points ADD COLUMN ai_analysis TEXT;
ALTER TABLE trash_points ADD COLUMN ai_verified BOOLEAN DEFAULT FALSE;
ALTER TABLE trash_points ADD COLUMN image_url VARCHAR(255);
```

---

## ⚡ Xử Lý Lỗi

### Lỗi AI Service
```
1. Connection timeout → Lưu báo cáo không có AI
2. Invalid JSON response → Log lỗi + Lưu không có AI  
3. AI server down → Fallback lưu thủ công
```

### Lỗi Upload
```
1. File quá lớn → Thông báo lỗi
2. Format không hỗ trợ → Thông báo lỗi
3. Lỗi lưu file → Báo cáo không có ảnh
```

---

## 🚀 Performance

### Timeout Settings
- API Request: 300 seconds (5 phút)
- Connection: 30 seconds
- PHP Execution: 300 seconds

### Caching
- Không cache kết quả AI (mỗi ảnh unique)
- Cache static assets (CSS, JS)

---

## 🔒 Bảo Mật

### Input Validation
- Kiểm tra file type trước upload
- Validate coordinates
- Sanitize user input

### AI Response
- Parse JSON an toàn
- Validate structure trước lưu
- Log suspicious responses

---

## 📈 Monitoring

### Logs
```
error_log("LMStudio CURL Error: " . $error);
error_log("LMStudio HTTP Error: " . $httpCode);  
error_log("LMStudio Invalid Response: " . $response);
```

### Metrics
- Tỷ lệ thành công AI analysis
- Thời gian response trung bình
- Phân bố pollution levels
