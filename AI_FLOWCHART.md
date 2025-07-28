# 🔄 Sơ Đồ Luồng AI - Green Eye

```
┌─────────────────┐
│   👤 User       │
│  Upload Image   │
│   + Location    │
│  + Description  │
└─────┬───────────┘
      │
      ▼
┌─────────────────┐
│  📁 add_point   │
│     .php        │
│                 │
│ ✓ Save image    │
│ ✓ Convert base64│
└─────┬───────────┘
      │
      ▼
┌─────────────────┐
│ 🤖 LMStudio     │
│   Service.php   │
│                 │
│ • Prepare payload│
│ • Send to AI    │
│ • Parse response│
└─────┬───────────┘
      │
      ▼
┌─────────────────┐
│ 🌐 AI Server    │
│ vnpthaiphong.vn │
│     :1234       │
│                 │
│ Model: gemma-3  │
└─────┬───────────┘
      │
      ▼ 
┌─────────────────┐
│ 📋 AI Response  │
│                 │
│ {               │
│  "image_type":  │
│    "trash",     │
│  "pollution_    │
│    level": 2    │
│ }               │
└─────┬───────────┘
      │
      ▼
┌─────────────────┐
│ ⚖️ Validation   │
│                 │
│ image_type =    │
│ "trash"? ──┐    │
│            │    │
│     YES ────┘    │
│            │    │
│     NO ─────X    │ (Reject & Delete)
└─────┬───────────┘
      │
      ▼
┌─────────────────┐
│ 💾 Database     │
│                 │
│ • trash_points  │
│ • ai_analysis   │
│ • ai_verified   │
│ • image_url     │
└─────┬───────────┘
      │
      ▼
┌─────────────────┐
│ 📱 Frontend     │
│   Display       │
│                 │
│ • List view     │
│ • Map markers   │
│ • AI badges     │
└─────────────────┘
```

## 🎯 Decision Points

```
AI Analysis Result
        │
        ▼
┌──────────────────┐
│ image_type?      │
└─────┬─────┬──────┘
      │     │
"trash"│     │"irrelevant"
      │     │
      ▼     ▼
   ✅ SAVE   ❌ REJECT
      │        │
      ▼        ▼
┌─────────┐ ┌─────────┐
│pollution│ │ Delete  │
│ level?  │ │ image   │
└┬─┬─┬─┬──┘ │ & show  │
 │ │ │ │    │ error   │
 1 2 3 4    └─────────┘
 │ │ │ │
 ▼ ▼ ▼ ▼
🟢🟡🟠🔴
```

## 📊 Data Flow

```
User Input           AI Processing         Database Storage
    │                     │                      │
    ▼                     ▼                      ▼
┌─────────┐          ┌─────────┐           ┌─────────┐
│ Image   │ ────────▶│ Base64  │ ─────────▶│ BLOB    │
│ File    │          │ Encode  │           │ Storage │
└─────────┘          └─────────┘           └─────────┘
    │                     │                      │
    ▼                     ▼                      ▼
┌─────────┐          ┌─────────┐           ┌─────────┐
│ GPS     │ ────────▶│ Validate│ ─────────▶│ lat/lng │
│ Coords  │          │ Format  │           │ DECIMAL │
└─────────┘          └─────────┘           └─────────┘
    │                     │                      │
    ▼                     ▼                      ▼
┌─────────┐          ┌─────────┐           ┌─────────┐
│ Text    │ ────────▶│ Sanitize│ ─────────▶│ VARCHAR │
│ Desc    │          │ & Escape│           │ (255)   │
└─────────┘          └─────────┘           └─────────┘
```

