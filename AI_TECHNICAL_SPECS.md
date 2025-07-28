# 🔧 Technical Specifications - AI Integration

## 📋 System Overview
**Project**: Green Eye - Waste Reporting System  
**AI Integration**: LMStudio with Google Gemma-3-4B  
**Purpose**: Automatic trash detection and pollution level assessment

---

## 🎯 Core Components

### 1. AI Service Configuration
```php
// lm_service.php
class LMStudioService {
    private $apiUrl = 'http://ai.vnpthaiphong.vn:1234/v1/chat/completions';
    private $model = 'google/gemma-3-4b';
    private $temperature = 0.3;
}
```

### 2. Request Payload Structure
```json
{
    "model": "google/gemma-3-4b",
    "temperature": 0.3,
    "messages": [
        {
            "role": "system", 
            "content": "Environmental expert prompt..."
        },
        {
            "role": "user",
            "content": [
                {"type": "text", "text": "Please analyze this image."},
                {"type": "image_url", "image_url": {"url": "data:image/jpeg;base64,..."}}
            ]
        }
    ]
}
```

### 3. Expected AI Response
```json
{
    "id": "chatcmpl-xxx",
    "object": "chat.completion",
    "created": 1750325668,
    "model": "google/gemma-3-4b",
    "choices": [{
        "index": 0,
        "message": {
            "role": "assistant",
            "content": "```json\n{\"image_type\":\"trash\",\"pollution_level\":2}\n```"
        }
    }]
}
```

---

## 📊 Data Models

### Database Schema
```sql
-- Add AI columns to existing trash_points table
ALTER TABLE trash_points ADD COLUMN ai_analysis TEXT;
ALTER TABLE trash_points ADD COLUMN ai_verified BOOLEAN DEFAULT FALSE;
ALTER TABLE trash_points ADD COLUMN image_url VARCHAR(255);

-- Complete table structure
CREATE TABLE trash_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    latitude DECIMAL(10, 8) NOT NULL,
    longitude DECIMAL(11, 8) NOT NULL,
    description TEXT,
    image_url VARCHAR(255),
    ai_analysis TEXT,
    ai_verified BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### AI Analysis JSON Structure
```json
{
    "image_type": "trash|irrelevant",
    "pollution_level": 1|2|3|4|null
}
```

---

## ⚙️ Configuration Settings

### Timeout Configuration (config_timeout.php)
```php
define('API_TIMEOUT', 300);           // 5 minutes
define('API_CONNECT_TIMEOUT', 30);    // 30 seconds  
define('PHP_EXECUTION_TIMEOUT', 300); // 5 minutes
```

### File Upload Settings
```php
// Supported formats
$allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

// Upload directory
$upload_dir = 'uploads/';

// Max file size (handled by PHP settings)
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '10M');
```

---

## 🚦 Validation Rules

### Image Type Validation
| Value | Action | Description |
|-------|--------|-------------|
| `"trash"` | ✅ Accept | Valid waste report |
| `"irrelevant"` | ❌ Reject | Not a waste image |

### Pollution Level Mapping
| Level | Label | Color | Icon | Urgent |
|-------|-------|-------|------|--------|
| 1 | Rất nhẹ | `#28a745` | 🟢 | No |
| 2 | Nhẹ | `#ffc107` | 🟡 | No |
| 3 | Trung bình | `#fd7e14` | 🟠 | Yes |
| 4 | Nghiêm trọng | `#dc3545` | 🔴 | Yes |

---

## 🔄 API Endpoints

### Internal APIs
- `add_point.php` - Process new waste reports with AI
- `get_points.php` - Retrieve waste points with AI data
- `update_point.php` - Update existing points with new AI analysis

### External Dependencies
- `http://ai.vnpthaiphong.vn:1234/v1/chat/completions` - LMStudio AI API
- `http://ai.vnpthaiphong.vn:1234/health` - Health check endpoint

---

## 🛡️ Error Handling

### API Errors
```php
// Connection errors
if ($error) {
    error_log("LMStudio CURL Error: " . $error);
    return null; // Continue without AI
}

// HTTP errors  
if ($httpCode !== 200) {
    error_log("LMStudio HTTP Error: " . $httpCode);
    return null; // Continue without AI
}

// Invalid response
if (!$result || !isset($result['choices'][0]['message']['content'])) {
    error_log("LMStudio Invalid Response: " . $response);
    return null; // Continue without AI
}
```

### Fallback Behavior
- If AI fails → Save report without AI verification
- If image invalid → Reject entire report
- If JSON parsing fails → Log error, continue without AI

---

## 📈 Performance Characteristics

### Response Times
- **Upload + Save**: ~1-2 seconds
- **AI Analysis**: ~30-60 seconds  
- **Total Process**: ~32-62 seconds

### Resource Usage
- **Memory**: Base64 encoding increases memory usage 1.33x
- **Storage**: Original images stored in `uploads/` directory
- **Bandwidth**: Full image sent to AI service

### Scalability Considerations
- AI service is external bottleneck
- Database grows with each image upload
- No caching of AI results (each analysis unique)

---

## 🔍 Monitoring & Logging

### Log Locations
```php
error_log("LMStudio CURL Error: " . $error);        // PHP error log
error_log("LMStudio HTTP Error: " . $httpCode);     // PHP error log  
error_log("LMStudio Invalid Response: " . $response); // PHP error log
```

### Key Metrics to Monitor
- AI service uptime and response time
- Success rate of AI analysis
- Distribution of pollution levels
- User report acceptance rate
- Error frequencies by type

---

## 🔒 Security Considerations

### Input Validation
- File type checking before upload
- GPS coordinate validation
- SQL injection prevention (prepared statements)
- XSS prevention (htmlspecialchars)

### Data Privacy
- Images stored locally on server
- No personal data sent to AI service
- User authentication required for all operations

### API Security
- No authentication for LMStudio (internal network)
- Rate limiting handled by external service
- Timeout protection against hanging requests

---

## 🚀 Deployment Requirements

### Server Requirements
- **PHP**: 7.4+ with cURL extension
- **MySQL**: 5.7+ or MariaDB 10.2+
- **Disk Space**: Sufficient for image storage
- **Network**: Access to ai.vnpthaiphong.vn:1234

### Environment Variables
```php
// Database connection
$servername = "localhost";
$username = "db_user";  
$password = "db_password";
$dbname = "green_eye_db";

// AI Service
$ai_api_url = "http://ai.vnpthaiphong.vn:1234/v1/chat/completions";
```

### File Permissions
```bash
chmod 755 uploads/     # Upload directory
chmod 644 *.php        # PHP files
chmod 600 db.php       # Database config
```
