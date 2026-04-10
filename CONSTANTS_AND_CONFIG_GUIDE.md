# 🔧 CONSTANTS & CONFIGURATION GUIDE

## 📋 Overview

Semua URL links, JWT settings, dan konfigurasi API sekarang diatur melalui **constants** di `application/config/`. Ini memudahkan maintenance dan switching antara environment yang berbeda.

---

## 📁 FILES YANG DIGUNAKAN

### 1. **application/config/constants.php** (Main)
File utama yang sudah ada di CodeIgniter. Berisi definisi konstanta umum.

**Update:** Sekarang include file `constants.env.php` untuk environment-specific settings.

### 2. **application/config/constants.env.php** (NEW)
File baru yang menyimpan environment-specific constants.

```
development  → localhost settings
staging      → staging server settings  
production   → production server settings (from env vars)
```

---

## 🔑 KONSTANTA YANG TERSEDIA

### URL Constants
```php
// Base API URLs
API_BASE_URL              // Production: https://api.littlehome.id
API_BASE_URL_LOCAL        // Fallback URL
UPLOAD_BASE_URL           // Upload server URL
UPLOAD_BASE_URL_LOCAL     // Fallback upload URL

// Documentation URLs
API_DOCS_SWAGGER          // Swagger UI: /docs/
API_DOCS_POSTMAN          // Postman collection
OPENAPI_SPEC              // OpenAPI YAML file

// Health check
API_HEALTH_CHECK          // Health check endpoint
API_STATUS                // Status endpoint
```

### JWT Constants
```php
JWT_SECRET_KEY            // Secret key for token encoding
JWT_EXPIRATION_DAYS       // Token expiration in days
```

### CORS Constants
```php
CORS_ALLOW_ORIGIN         // Allowed origins (*, domain, atau list)
CORS_ALLOW_HEADERS        // Allowed headers
CORS_ALLOW_METHODS        // Allowed HTTP methods
ENABLE_CORS               // Enable/disable CORS
```

### Upload Constants
```php
// Directories
UPLOAD_DIR_TESTIMONI      // testimonial uploads
UPLOAD_DIR_POST_CONTENT   // post content uploads
UPLOAD_DIR_DOCUMENTS      // document uploads
UPLOAD_DIR_PROFILE        // profile picture uploads

// Size limits (in KB)
MAX_UPLOAD_SIZE_IMAGE     // default: 2048 (2MB)
MAX_UPLOAD_SIZE_DOCUMENT  // default: 5120 (5MB)

// Allowed types
ALLOWED_IMAGE_TYPES       // gif|jpg|jpeg|png|webp
ALLOWED_VIDEO_TYPES       // mp4|avi|mov|mkv
ALLOWED_DOCUMENT_TYPES    // pdf|doc|docx|xls|xlsx
```

### Pagination Constants
```php
DEFAULT_PAGE              // Default page number: 1
DEFAULT_LIMIT_POSTS       // Default posts per page: 3
DEFAULT_LIMIT_RECORDS     // Default records per page: 10
DEFAULT_LIMIT_INVOICES    // Default invoices per page: 15
DEFAULT_LIMIT_TESTIMONIALS // Default testimonials per page: 10
MAX_LIMIT_PER_PAGE        // Max items per page: 50
```

### Caching Constants
```php
ENABLE_QUERY_CACHING      // Enable query result caching
CACHE_TTL_SECONDS         // Cache time-to-live: 3600 (1 hour)
ENABLE_COMPRESSION        // Enable GZIP compression
```

### Debug Constants
```php
DB_DEBUG                  // Database debug mode
DB_CACHE_ON               // Query caching enabled
```

---

## 🌍 ENVIRONMENT-SPECIFIC CONFIGURATION

### Development (localhost)
```php
// File: application/config/constants.env.php
// Environment: development

API_BASE_URL              = 'http://localhost/little_home_api'
UPLOAD_BASE_URL           = 'http://localhost/little_home_api/uploads'
JWT_SECRET_KEY            = 'dev_secret_key_change_in_production'
CORS_ALLOW_ORIGIN         = '*'
ENABLE_QUERY_CACHING      = FALSE
ENABLE_COMPRESSION        = FALSE
DB_DEBUG                  = TRUE
```

### Staging
```php
// Environment: staging

API_BASE_URL              = 'https://staging-api.littlehome.id'
UPLOAD_BASE_URL           = 'https://staging-uploads.littlehome.id'
JWT_SECRET_KEY            = getenv('JWT_SECRET_KEY')
CORS_ALLOW_ORIGIN         = 'https://staging.littlehome.id'
ENABLE_QUERY_CACHING      = TRUE
ENABLE_COMPRESSION        = TRUE
DB_DEBUG                  = FALSE
```

### Production
```php
// Environment: production

API_BASE_URL              = getenv('API_BASE_URL')
UPLOAD_BASE_URL           = getenv('UPLOAD_BASE_URL')
JWT_SECRET_KEY            = getenv('JWT_SECRET_KEY') // REQUIRED!
CORS_ALLOW_ORIGIN         = getenv('CORS_ALLOW_ORIGIN')
ENABLE_QUERY_CACHING      = TRUE
ENABLE_COMPRESSION        = TRUE
DB_DEBUG                  = FALSE
```

---

## 🚀 SETUP UNTUK BERBAGAI ENVIRONMENT

### 1. LOCAL DEVELOPMENT (NO CHANGES NEEDED!)

Default configuration sudah pas untuk development:
```bash
# Cukup jalankan
http://localhost/little_home_api
```

Konstanta akan otomatis menggunakan nilai development.

---

### 2. STAGING DEPLOYMENT

1. **Update `index.php`** untuk set environment ke staging:
```php
// In index.php (around line 56)
define('ENVIRONMENT', 'staging');
```

2. Jika gunakan custom URLs, set environment variables:
```bash
# Bash/Linux
export JWT_SECRET_KEY='your_staging_secret_from_vault'
export API_BASE_URL='https://staging-api.littlehome.id'
export UPLOAD_BASE_URL='https://staging-uploads.littlehome.id'

# Windows PowerShell
$env:JWT_SECRET_KEY = 'your_staging_secret'
$env:API_BASE_URL = 'https://staging-api.littlehome.id'
```

---

### 3. PRODUCTION DEPLOYMENT

**CRITICAL:** Semua sensitive data harus dari environment variables atau secure config management!

1. **Set environment ke production:**
```php
// In index.php
define('ENVIRONMENT', 'production');
```

2. **Set environment variables** (Jangan hardcoded di kode!):

**Option A: .env file** (gunakan library seperti `phpdotenv`)
```ini
CI_ENVIRONMENT=production
JWT_SECRET_KEY=your_production_secret_key_from_vault
API_BASE_URL=https://littlehome-api.example.com
UPLOAD_BASE_URL=https://galerilittlehomemontessori.my.id/uploads
CORS_ALLOW_ORIGIN=https://littlehome.example.com
JWT_EXPIRATION_DAYS=7
```

**Option B: Docker/Kubernetes environment variables**
```bash
docker run -e JWT_SECRET_KEY="xxx" -e API_BASE_URL="yyy" ...
```

**Option C: Server configuration** (Docker, systemd, etc.)
```bash
# In systemd service file
Environment="JWT_SECRET_KEY=xxx"
Environment="CORS_ALLOW_ORIGIN=https://littlehome.id"
```

3. **Verify production settings:**
```bash
php -r "define('ENVIRONMENT', 'production'); 
        require 'application/config/constants.env.php'; 
        echo 'API_BASE_URL: ' . API_BASE_URL;"
```

---

## 💻 USAGE IN CODE

### How to Use Constants

**Before (Hardcoded):**
```php
$url = 'https://galerilittlehomemontessori.my.id/uploads/' . $file;
```

**After (Using Constants):**
```php
$url = UPLOAD_BASE_URL . '/' . $file;
```

### In Walimurid Controller

**JWT Secret:**
```php
private $jwt_secret_key = JWT_SECRET_KEY;
```

**Upload URL:**
```php
foreach ($post_content as &$content) {
    if (!empty($content['file_url'])) {
        $content['file_url'] = UPLOAD_BASE_URL . '/' . $content['file_url'];
    }
}
```

**CORS Headers:**
```php
if (ENABLE_CORS) {
    header('Access-Control-Allow-Origin: ' . CORS_ALLOW_ORIGIN);
    header('Access-Control-Allow-Headers: ' . CORS_ALLOW_HEADERS);
    header('Access-Control-Allow-Methods: ' . CORS_ALLOW_METHODS);
}
```

### In Client/JavaScript

```javascript
// Get API base URL from response or config
const API_BASE = 'https://littlehome-api.example.com'; // Or get from config endpoint

fetch(`${API_BASE}/walimurid/posting?id_anak=1&page=1`, {
    headers: {
        'Authorization': `Bearer ${token}`,
        'Accept-Encoding': 'gzip, deflate'
    }
});
```

---

## 🔐 SECURITY BEST PRACTICES

### 1. JWT Secret Key
❌ **NEVER hardcode in production:**
```php
JWT_SECRET_KEY = 'fixed_secret_123'; // BAD!
```

✅ **Always use environment variables:**
```php
JWT_SECRET_KEY = getenv('JWT_SECRET_KEY');
```

### 2. CORS Configuration
❌ **Development only:**
```php
CORS_ALLOW_ORIGIN = '*'; // Allow all origins
```

✅ **Production strict:**
```php
CORS_ALLOW_ORIGIN = 'https://littlehome.id'; // Specific domain
```

### 3. Debug Mode
❌ **Production:**
```php
DB_DEBUG = TRUE; // Show database errors
```

✅ **Production:**
```php
DB_DEBUG = FALSE; // Hide sensitive info
```

### 4. Manage Secrets

**Use a secrets manager:**
- HashiCorp Vault
- AWS Secrets Manager
- Azure Key Vault
- Google Cloud Secret Manager

```php
// Example with environment variable
JWT_SECRET_KEY = getenv('JWT_SECRET_KEY') 
                 ?: exit('ERROR: JWT_SECRET_KEY is required');
```

---

## 📝 CHECKLIST UNTUK DEPLOYMENT

### Before Production
- [ ] Update environment to 'production' in index.php
- [ ] Set all required environment variables
- [ ] Verify JWT_SECRET_KEY is NOT hardcoded
- [ ] Verify CORS_ALLOW_ORIGIN is specific domain
- [ ] Verify DB_DEBUG = FALSE
- [ ] Verify UPLOAD_BASE_URL points to correct CDN
- [ ] Verify API_BASE_URL is HTTPS

### Production Health Check
```bash
# Test that constants are loaded correctly
curl https://api.example.com/walimurid/health

# Should return:
{
    "status": true,
    "api_url": "https://api.example.com"
}
```

---

## 🐛 TROUBLESHOOTING

### Issue: Constants not defined
**Solution:** Ensure `constants.env.php` is being included:
```php
// In constants.php
require_once APPPATH . 'config/constants.env.php';
```

### Issue: Wrong URL in production
**Solution:** Check environment variables are set:
```bash
# Linux
echo $API_BASE_URL
echo $JWT_SECRET_KEY

# Windows
echo %API_BASE_URL%
echo %JWT_SECRET_KEY%
```

### Issue: CORS errors
**Solution:** Set correct origin for your domain:
```php
// In constants.env.php for production
CORS_ALLOW_ORIGIN = 'https://yourdomain.com'
```

---

## 📚 EXAMPLE: Adding New Constants

If you need to add a new constant for a new feature:

1. **Add to constants.php** (default/fallback):
```php
defined('FEATURE_NEW_API_ENDPOINT') OR define('FEATURE_NEW_API_ENDPOINT', FALSE);
```

2. **Override in constants.env.php** (environment-specific):
```php
// Development
if ($environment === 'development') {
    defined('FEATURE_NEW_API_ENDPOINT') OR define('FEATURE_NEW_API_ENDPOINT', TRUE);
}

// Production
else {
    defined('FEATURE_NEW_API_ENDPOINT') OR define('FEATURE_NEW_API_ENDPOINT', getenv('FEATURE_NEW_API_ENDPOINT') === 'true');
}
```

3. **Use in code:**
```php
if (FEATURE_NEW_API_ENDPOINT) {
    // Enable new feature
}
```

---

## 🎯 SUMMARY

| | Before | After |
|---|--------|-------|
| **URL Management** | Hardcoded strings | Constants |
| **Environment Switching** | Manual find/replace | Automatic via ENVIRONMENT |
| **Security** | Secrets in code | Environment variables |
| **Maintenance** | Error-prone | Centralized & consistent |
| **Mobile Integration** | URL hardcoded in app | Can be configured per environment |

---

## 📞 NEXT STEPS

1. **Review** `application/config/constants.env.php`
2. **Test** locally - no changes needed
3. **Deploy to staging** - set environment='staging'
4. **Deploy to production** - set environment='production' + env vars
5. **Verify** all URLs are correct

---

**Last Updated:** April 10, 2026
**Status:** Implemented & Ready

