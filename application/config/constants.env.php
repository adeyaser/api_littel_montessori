<?php
/**
 * ENVIRONMENT-SPECIFIC CONFIGURATION
 * File: application/config/constants.env.php
 * 
 * Mengatur konstanta berdasarkan environment
 * Environment ditentukan di: index.php dengan ENVIRONMENT variable
 */

defined('BASEPATH') OR exit('No direct script access allowed');

// Tentukan environment dari sistem atau default ke 'development'
$environment = !empty($_ENV['CI_ENVIRONMENT']) ? $_ENV['CI_ENVIRONMENT'] : ENVIRONMENT;

/**
 * ===============================================
 * DEVELOPMENT ENVIRONMENT
 * ===============================================
 */
if ($environment === 'development') {
    // Base URLs
    defined('API_BASE_URL') OR define('API_BASE_URL', 'http://localhost/little_home_api');
    defined('API_BASE_URL_LOCAL') OR define('API_BASE_URL_LOCAL', 'http://localhost/little_home_api');
    defined('UPLOAD_BASE_URL') OR define('UPLOAD_BASE_URL', 'http://localhost/little_home_api/uploads');
    defined('UPLOAD_BASE_URL_LOCAL') OR define('UPLOAD_BASE_URL_LOCAL', 'http://localhost/little_home_api/uploads');
    
    // JWT
    defined('JWT_SECRET_KEY') OR define('JWT_SECRET_KEY', 'dev_secret_key_change_in_production');
    defined('JWT_EXPIRATION_DAYS') OR define('JWT_EXPIRATION_DAYS', 7);
    
    // CORS
    defined('CORS_ALLOW_ORIGIN') OR define('CORS_ALLOW_ORIGIN', '*');
    defined('ENABLE_CORS') OR define('ENABLE_CORS', TRUE);
    
    // Database
    defined('DB_DEBUG') OR define('DB_DEBUG', TRUE);
    defined('DB_CACHE_ON') OR define('DB_CACHE_ON', FALSE);
    
    // Features
    defined('ENABLE_QUERY_CACHING') OR define('ENABLE_QUERY_CACHING', FALSE);
    defined('ENABLE_COMPRESSION') OR define('ENABLE_COMPRESSION', FALSE);
}

/**
 * ===============================================
 * STAGING ENVIRONMENT  
 * ===============================================
 */
elseif ($environment === 'staging') {
    // Base URLs
    defined('API_BASE_URL') OR define('API_BASE_URL', 'https://staging-api.littlehome.id');
    defined('API_BASE_URL_LOCAL') OR define('API_BASE_URL_LOCAL', 'https://staging-api.littlehome.id');
    defined('UPLOAD_BASE_URL') OR define('UPLOAD_BASE_URL', 'https://staging-uploads.littlehome.id');
    defined('UPLOAD_BASE_URL_LOCAL') OR define('UPLOAD_BASE_URL_LOCAL', 'https://staging-uploads.littlehome.id');
    
    // JWT
    defined('JWT_SECRET_KEY') OR define('JWT_SECRET_KEY', getenv('JWT_SECRET_KEY') ?: 'staging_secret_key_from_env');
    defined('JWT_EXPIRATION_DAYS') OR define('JWT_EXPIRATION_DAYS', 7);
    
    // CORS
    defined('CORS_ALLOW_ORIGIN') OR define('CORS_ALLOW_ORIGIN', 'https://staging.littlehome.id');
    defined('ENABLE_CORS') OR define('ENABLE_CORS', TRUE);
    
    // Database
    defined('DB_DEBUG') OR define('DB_DEBUG', FALSE);
    defined('DB_CACHE_ON') OR define('DB_CACHE_ON', TRUE);
    
    // Features
    defined('ENABLE_QUERY_CACHING') OR define('ENABLE_QUERY_CACHING', TRUE);
    defined('ENABLE_COMPRESSION') OR define('ENABLE_COMPRESSION', TRUE);
}

/**
 * ===============================================
 * PRODUCTION ENVIRONMENT
 * ===============================================
 */
else {
    // Base URLs - HARUS SET DI PRODUCTION!
    defined('API_BASE_URL') OR define('API_BASE_URL', getenv('API_BASE_URL') ?: 'https://api.littlehome.id');
    defined('API_BASE_URL_LOCAL') OR define('API_BASE_URL_LOCAL', getenv('API_BASE_URL') ?: 'https://api.littlehome.id');
    defined('UPLOAD_BASE_URL') OR define('UPLOAD_BASE_URL', getenv('UPLOAD_BASE_URL') ?: 'https://uploads.littlehome.id');
    defined('UPLOAD_BASE_URL_LOCAL') OR define('UPLOAD_BASE_URL_LOCAL', getenv('UPLOAD_BASE_URL') ?: 'https://uploads.littlehome.id');
    
    // JWT - MUST BE SET FROM ENVIRONMENT VARIABLES!
    defined('JWT_SECRET_KEY') OR define('JWT_SECRET_KEY', getenv('JWT_SECRET_KEY') ?: exit('ERROR: JWT_SECRET_KEY not set in environment'));
    defined('JWT_EXPIRATION_DAYS') OR define('JWT_EXPIRATION_DAYS', getenv('JWT_EXPIRATION_DAYS') ?: 7);
    
    // CORS - Restrict to specific domains
    defined('CORS_ALLOW_ORIGIN') OR define('CORS_ALLOW_ORIGIN', getenv('CORS_ALLOW_ORIGIN') ?: 'https://littlehome.id');
    defined('ENABLE_CORS') OR define('ENABLE_CORS', TRUE);
    
    // Database
    defined('DB_DEBUG') OR define('DB_DEBUG', FALSE);
    defined('DB_CACHE_ON') OR define('DB_CACHE_ON', TRUE);
    
    // Features
    defined('ENABLE_QUERY_CACHING') OR define('ENABLE_QUERY_CACHING', TRUE);
    defined('ENABLE_COMPRESSION') OR define('ENABLE_COMPRESSION', TRUE);
}

/**
 * ===============================================
 * FALLBACK CONSTANTS (If not defined above)
 * ===============================================
 */

// CORS defaults
defined('CORS_ALLOW_HEADERS') OR define('CORS_ALLOW_HEADERS', 'Origin, X-Requested-With, Content-Type, Accept, Authorization, Access-Control-Request-Method');
defined('CORS_ALLOW_METHODS') OR define('CORS_ALLOW_METHODS', 'GET, POST, OPTIONS, PUT, DELETE');

// Pagination defaults
defined('DEFAULT_PAGE') OR define('DEFAULT_PAGE', 1);
defined('DEFAULT_LIMIT_POSTS') OR define('DEFAULT_LIMIT_POSTS', 3);
defined('DEFAULT_LIMIT_RECORDS') OR define('DEFAULT_LIMIT_RECORDS', 10);
defined('DEFAULT_LIMIT_INVOICES') OR define('DEFAULT_LIMIT_INVOICES', 15);
defined('DEFAULT_LIMIT_TESTIMONIALS') OR define('DEFAULT_LIMIT_TESTIMONIALS', 10);
defined('MAX_LIMIT_PER_PAGE') OR define('MAX_LIMIT_PER_PAGE', 50);

// Upload settings
defined('UPLOAD_DIR_TESTIMONI') OR define('UPLOAD_DIR_TESTIMONI', './uploads/testimoni/');
defined('UPLOAD_DIR_POST_CONTENT') OR define('UPLOAD_DIR_POST_CONTENT', './uploads/posts/');
defined('UPLOAD_DIR_DOCUMENTS') OR define('UPLOAD_DIR_DOCUMENTS', './uploads/documents/');
defined('UPLOAD_DIR_PROFILE') OR define('UPLOAD_DIR_PROFILE', './uploads/profile/');

// File upload sizes (in KB)
defined('MAX_UPLOAD_SIZE_IMAGE') OR define('MAX_UPLOAD_SIZE_IMAGE', 2048);
defined('MAX_UPLOAD_SIZE_DOCUMENT') OR define('MAX_UPLOAD_SIZE_DOCUMENT', 5120);
defined('ALLOWED_IMAGE_TYPES') OR define('ALLOWED_IMAGE_TYPES', 'gif|jpg|jpeg|png|webp');
defined('ALLOWED_VIDEO_TYPES') OR define('ALLOWED_VIDEO_TYPES', 'mp4|avi|mov|mkv');

// Caching
defined('CACHE_TTL_SECONDS') OR define('CACHE_TTL_SECONDS', 3600);

// Documentation URLs
defined('API_DOCS_SWAGGER') OR define('API_DOCS_SWAGGER', API_BASE_URL . '/docs/');
defined('API_DOCS_POSTMAN') OR define('API_DOCS_POSTMAN', API_BASE_URL . '/docs/Little_Home_API_Postman.json');
defined('OPENAPI_SPEC') OR define('OPENAPI_SPEC', API_BASE_URL . '/docs/api.yaml');

// Health check & Status endpoints
defined('API_HEALTH_CHECK') OR define('API_HEALTH_CHECK', API_BASE_URL . '/walimurid/health');
defined('API_STATUS') OR define('API_STATUS', API_BASE_URL . '/walimurid/status');
