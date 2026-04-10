# 🎯 CONSTANTS QUICK REFERENCE CARD

## 📌 QUICK LOOKUP

### 🌐 URL Constants
```php
API_BASE_URL              'http://localhost/little_home_api' (dev) → https://api.littlehome.id (prod)
UPLOAD_BASE_URL           'http://localhost/little_home_api/uploads' → https://uploads.littlehome.id
API_DOCS_SWAGGER          API_BASE_URL . '/docs/'
OPENAPI_SPEC              API_BASE_URL . '/docs/api.yaml'
```

### 🔐 Security Constants
```php
JWT_SECRET_KEY            Secret key untuk JWT encoding
JWT_EXPIRATION_DAYS       Default: 7 days
CORS_ALLOW_ORIGIN         '*' (dev) → specific domain (prod)
CORS_ALLOW_HEADERS        'Origin, X-Requested-With, ...'
CORS_ALLOW_METHODS        'GET, POST, OPTIONS, PUT, DELETE'
ENABLE_CORS               TRUE/FALSE
```

### 📄 Upload Constants
```php
UPLOAD_DIR_TESTIMONI      './uploads/testimoni/'
UPLOAD_DIR_POST_CONTENT   './uploads/posts/'
UPLOAD_DIR_DOCUMENTS      './uploads/documents/'
UPLOAD_DIR_PROFILE        './uploads/profile/'

MAX_UPLOAD_SIZE_IMAGE     2048 (KB)
MAX_UPLOAD_SIZE_DOCUMENT  5120 (KB)

ALLOWED_IMAGE_TYPES       'gif|jpg|jpeg|png|webp'
ALLOWED_VIDEO_TYPES       'mp4|avi|mov|mkv'
```

### 📊 Pagination Constants
```php
DEFAULT_PAGE              1
DEFAULT_LIMIT_POSTS       3
DEFAULT_LIMIT_RECORDS     10
DEFAULT_LIMIT_INVOICES    15
DEFAULT_LIMIT_TESTIMONIALS 10
MAX_LIMIT_PER_PAGE        50
```

### ⚙️ Feature Constants
```php
ENABLE_QUERY_CACHING      TRUE (staging/prod) | FALSE (dev)
CACHE_TTL_SECONDS         3600 (1 hour)
ENABLE_COMPRESSION        TRUE (staging/prod) | FALSE (dev)
DB_DEBUG                  TRUE (dev) | FALSE (prod)
DB_CACHE_ON               FALSE (dev) | TRUE (prod)
```

---

## 🔧 HOW TO USE

### In Controllers
```php
// Upload file handling
$file_url = UPLOAD_BASE_URL . '/' . $filename;

// JWT configuration
private $jwt_secret_key = JWT_SECRET_KEY;
$payload['exp'] = time() + (60 * 60 * 24 * JWT_EXPIRATION_DAYS);

// CORS headers
header('Access-Control-Allow-Origin: ' . CORS_ALLOW_ORIGIN);
```

### In Views
```php
// Image URLs
<img src="<?= UPLOAD_BASE_URL . '/profile/' . $user['img']; ?>" />

// API Documentation link
<a href="<?= API_DOCS_SWAGGER; ?>">API Docs</a>
```

### In Queries
```php
// Use pagination constant
$per_page = DEFAULT_LIMIT_POSTS;
$limit = $this->get('limit') ?: DEFAULT_LIMIT_RECORDS;
```

---

## 🌍 ENVIRONMENT SETUP

### Local (Development)
```bash
# No setup needed - everything works with defaults
# Open http://localhost/little_home_api
```

### Staging
```bash
# 1. Update index.php
define('ENVIRONMENT', 'staging');

# 2. Or set environment variable
export CI_ENVIRONMENT=staging

# 3. Optional - set custom URLs
export JWT_SECRET_KEY='staging_key'
```

### Production
```bash
# 1. Update index.php
define('ENVIRONMENT', 'production');

# 2. Set environment variables (REQUIRED!)
export JWT_SECRET_KEY='your_production_secret'
export API_BASE_URL='https://api.littlehome.id'
export UPLOAD_BASE_URL='https://uploads.littlehome.id'
export CORS_ALLOW_ORIGIN='https://littlehome.id'
```

---

## ✅ VERIFICATION

### Check what environment you're in:
```php
<?php
echo 'Environment: ' . ENVIRONMENT;
echo 'API URL: ' . API_BASE_URL;
echo 'JWT Key: ' . JWT_SECRET_KEY;
echo 'CORS Origin: ' . CORS_ALLOW_ORIGIN;
?>
```

### Test in browser:
```
http://localhost/little_home_api/test.php  // Create test file
```

---

## 🔒 SECURITY CHECKLIST

### Development ✅
- [x] JWT_SECRET_KEY = dev key (OK to hardcode)
- [x] CORS_ALLOW_ORIGIN = '*' (OK for local)
- [x] DB_DEBUG = TRUE (OK for local)

### Staging ✅
- [x] JWT_SECRET_KEY = from environment
- [x] CORS_ALLOW_ORIGIN = staging domain
- [x] DB_DEBUG = FALSE

### Production ✅
- [x] JWT_SECRET_KEY = from secure vault
- [x] CORS_ALLOW_ORIGIN = production domain only
- [x] DB_DEBUG = FALSE
- [x] All URLs are HTTPS

---

## 🚨 COMMON MISTAKES

❌ Hardcoding URLs in code:
```php
// DON'T DO THIS
$url = 'https://galerilittlehomemontessori.my.id/uploads/' . $file;
```

✅ Use constants:
```php
// DO THIS
$url = UPLOAD_BASE_URL . '/' . $file;
```

---

❌ Committing secrets to Git:
```php
// DON'T DO THIS
define('JWT_SECRET_KEY', 'real_secret_key_123');
```

✅ Use environment variables:
```php
// DO THIS
JWT_SECRET_KEY = getenv('JWT_SECRET_KEY');
```

---

❌ Same config for all environments:
```php
// DON'T DO THIS
header('Access-Control-Allow-Origin: *'); // Never in production!
```

✅ Environment-specific config:
```php
// DO THIS
header('Access-Control-Allow-Origin: ' . CORS_ALLOW_ORIGIN);
// Automatically * (dev) or specific domain (prod)
```

---

## 📝 WHERE TO FIND CONSTANTS

### Main Definition:
```
application/config/constants.php
  └─ includes ─→ application/config/constants.env.php
```

### Environment-Specific:
```
application/config/constants.env.php
├─ if ($environment === 'development')
├─ elseif ($environment === 'staging')
└─ else (production)
```

### Used In:
```
application/controllers/Walimurid.php (and others)
application/views/*.php
Any other controller/model/view file
```

---

## 🆘 TROUBLESHOOTING

### Constants not defined?
```bash
# Check if constants.env.php is being included
grep "require_once.*constants.env" application/config/constants.php
```

### Wrong URL in browser?
```bash
# Check what environment you're in
php -r "define('ENVIRONMENT', 'YOUR_ENV'); 
        require 'application/config/constants.php'; 
        echo API_BASE_URL;"
```

### CORS errors?
```bash
# Check CORS constant value
php -r "define('ENVIRONMENT', 'development'); 
        require 'application/config/constants.php'; 
        echo CORS_ALLOW_ORIGIN;"
```

### JWT errors?
```bash
# Verify JWT key is being read
php -r "define('ENVIRONMENT', 'production'); 
        putenv('JWT_SECRET_KEY=test'); 
        require 'application/config/constants.php'; 
        echo JWT_SECRET_KEY;"
```

---

## 📚 MORE INFO

See detailed guide: [CONSTANTS_AND_CONFIG_GUIDE.md](CONSTANTS_AND_CONFIG_GUIDE.md)

---

## 🎯 REMEMBER

| Action | File | Location |
|--------|------|----------|
| View constants | `constants.php` | `application/config/` |
| Edit constants | `constants.env.php` | `application/config/` |
| Use constants | Any PHP file | `CONSTANT_NAME` |
| Set environment | `index.php` OR env var | Root or system |

---

**Quick Copy-Paste Reference:**

```php
// Upload file URL
UPLOAD_BASE_URL . '/' . $filename

// JWT secret
JWT_SECRET_KEY

// CORS origin
CORS_ALLOW_ORIGIN

// Pagination
DEFAULT_LIMIT_POSTS

// API base
API_BASE_URL

// Check environment
ENVIRONMENT
```

---

**Last Updated:** April 10, 2026 | **Version:** 1.0
