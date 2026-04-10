<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Load environment-specific configuration
require_once APPPATH . 'config/constants.env.php';

/*
|--------------------------------------------------------------------------
| Display Debug backtrace
|--------------------------------------------------------------------------
|
| If set to TRUE, a backtrace will be displayed along with php errors. If
| error_reporting is disabled, the backtrace will not display, regardless
| of this setting
|
*/
defined('SHOW_DEBUG_BACKTRACE') OR define('SHOW_DEBUG_BACKTRACE', TRUE);

/*
|--------------------------------------------------------------------------
| File and Directory Modes
|--------------------------------------------------------------------------
|
| These prefs are used when checking and setting modes when working
| with the file system.  The defaults are fine on servers with proper
| security, but you may wish (or even need) to change the values in
| certain environments (Apache running a separate process for each
| user, PHP under CGI with Apache suEXEC, etc.).  Octal values should
| always be used to set the mode correctly.
|
*/
defined('FILE_READ_MODE')  OR define('FILE_READ_MODE', 0644);
defined('FILE_WRITE_MODE') OR define('FILE_WRITE_MODE', 0666);
defined('DIR_READ_MODE')   OR define('DIR_READ_MODE', 0755);
defined('DIR_WRITE_MODE')  OR define('DIR_WRITE_MODE', 0755);

/*
|--------------------------------------------------------------------------
| File Stream Modes
|--------------------------------------------------------------------------
|
| These modes are used when working with fopen()/popen()
|
*/
defined('FOPEN_READ')                           OR define('FOPEN_READ', 'rb');
defined('FOPEN_READ_WRITE')                     OR define('FOPEN_READ_WRITE', 'r+b');
defined('FOPEN_WRITE_CREATE_DESTRUCTIVE')       OR define('FOPEN_WRITE_CREATE_DESTRUCTIVE', 'wb'); // truncates existing file data, use with care
defined('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE')  OR define('FOPEN_READ_WRITE_CREATE_DESTRUCTIVE', 'w+b'); // truncates existing file data, use with care
defined('FOPEN_WRITE_CREATE')                   OR define('FOPEN_WRITE_CREATE', 'ab');
defined('FOPEN_READ_WRITE_CREATE')              OR define('FOPEN_READ_WRITE_CREATE', 'a+b');
defined('FOPEN_WRITE_CREATE_STRICT')            OR define('FOPEN_WRITE_CREATE_STRICT', 'xb');
defined('FOPEN_READ_WRITE_CREATE_STRICT')       OR define('FOPEN_READ_WRITE_CREATE_STRICT', 'x+b');

/*
|--------------------------------------------------------------------------
| Exit Status Codes
|--------------------------------------------------------------------------
|
| Used to indicate the conditions under which the script is exit()ing.
| While there is no universal standard for error codes, there are some
| broad conventions.  Three such conventions are mentioned below, for
| those who wish to make use of them.  The CodeIgniter defaults were
| chosen for the least overlap with these conventions, while still
| leaving room for others to be defined in future versions and user
| applications.
|
| The three main conventions used for determining exit status codes
| are as follows:
|
|    Standard C/C++ Library (stdlibc):
|       http://www.gnu.org/software/libc/manual/html_node/Exit-Status.html
|       (This link also contains other GNU-specific conventions)
|    BSD sysexits.h:
|       http://www.gsp.com/cgi-bin/man.cgi?section=3&topic=sysexits
|    Bash scripting:
|       http://tldp.org/LDP/abs/html/exitcodes.html
|
*/
defined('EXIT_SUCCESS')        OR define('EXIT_SUCCESS', 0); // no errors
defined('EXIT_ERROR')          OR define('EXIT_ERROR', 1); // generic error
defined('EXIT_CONFIG')         OR define('EXIT_CONFIG', 3); // configuration error
defined('EXIT_UNKNOWN_FILE')   OR define('EXIT_UNKNOWN_FILE', 4); // file not found
defined('EXIT_UNKNOWN_CLASS')  OR define('EXIT_UNKNOWN_CLASS', 5); // unknown class
defined('EXIT_UNKNOWN_METHOD') OR define('EXIT_UNKNOWN_METHOD', 6); // unknown class member
defined('EXIT_USER_INPUT')     OR define('EXIT_USER_INPUT', 7); // invalid user input
defined('EXIT_DATABASE')       OR define('EXIT_DATABASE', 8); // database error
defined('EXIT__AUTO_MIN')      OR define('EXIT__AUTO_MIN', 9); // lowest automatically-assigned error code
defined('EXIT__AUTO_MAX')      OR define('EXIT__AUTO_MAX', 125); // highest automatically-assigned error code

/*
|--------------------------------------------------------------------------
| API BASE URL & UPLOAD PATHS
|--------------------------------------------------------------------------
| Konstanta untuk alamat link API dan path upload
| Gunakan konstanta ini di seluruh aplikasi untuk consistency
|
*/

// Base URL untuk API endpoints
defined('API_BASE_URL')              OR define('API_BASE_URL', 'https://littlehome-api.example.com');
defined('API_BASE_URL_LOCAL')        OR define('API_BASE_URL_LOCAL', 'http://localhost/little_home_api');

// Base URL untuk upload/media files
defined('UPLOAD_BASE_URL')           OR define('UPLOAD_BASE_URL', 'https://galerilittlehomemontessori.my.id/uploads');
defined('UPLOAD_BASE_URL_LOCAL')     OR define('UPLOAD_BASE_URL_LOCAL', 'http://localhost/little_home_api/uploads');

// Upload directories (relative to base path)
defined('UPLOAD_DIR_TESTIMONI')      OR define('UPLOAD_DIR_TESTIMONI', './uploads/testimoni/');
defined('UPLOAD_DIR_POST_CONTENT')   OR define('UPLOAD_DIR_POST_CONTENT', './uploads/posts/');
defined('UPLOAD_DIR_DOCUMENTS')      OR define('UPLOAD_DIR_DOCUMENTS', './uploads/documents/');
defined('UPLOAD_DIR_PROFILE')        OR define('UPLOAD_DIR_PROFILE', './uploads/profile/');

// Documentation URLs
defined('API_DOCS_SWAGGER')          OR define('API_DOCS_SWAGGER', API_BASE_URL . '/docs/');
defined('API_DOCS_POSTMAN')          OR define('API_DOCS_POSTMAN', API_BASE_URL . '/docs/Little_Home_API_Postman.json');

// OpenAPI Spec
defined('OPENAPI_SPEC')              OR define('OPENAPI_SPEC', API_BASE_URL . '/docs/api.yaml');

// Health check & Status endpoints
defined('API_HEALTH_CHECK')          OR define('API_HEALTH_CHECK', API_BASE_URL . '/walimurid/health');
defined('API_STATUS')                OR define('API_STATUS', API_BASE_URL . '/walimurid/status');

/*
|--------------------------------------------------------------------------
| CORS & SECURITY CONSTANTS
|--------------------------------------------------------------------------
*/
defined('CORS_ALLOW_ORIGIN')         OR define('CORS_ALLOW_ORIGIN', '*');
defined('CORS_ALLOW_METHODS')        OR define('CORS_ALLOW_METHODS', 'GET, POST, OPTIONS, PUT, DELETE');
defined('CORS_ALLOW_HEADERS')        OR define('CORS_ALLOW_HEADERS', 'Origin, X-Requested-With, Content-Type, Accept, Authorization, Access-Control-Request-Method');
defined('JWT_SECRET_KEY')            OR define('JWT_SECRET_KEY', 'rahasia_little_home_super_aman_123');
defined('JWT_EXPIRATION_DAYS')       OR define('JWT_EXPIRATION_DAYS', 7);

/*
|--------------------------------------------------------------------------
| PAGINATION CONSTANTS
|--------------------------------------------------------------------------
*/
defined('DEFAULT_PAGE')              OR define('DEFAULT_PAGE', 1);
defined('DEFAULT_LIMIT_POSTS')       OR define('DEFAULT_LIMIT_POSTS', 3);
defined('DEFAULT_LIMIT_RECORDS')     OR define('DEFAULT_LIMIT_RECORDS', 10);
defined('DEFAULT_LIMIT_INVOICES')    OR define('DEFAULT_LIMIT_INVOICES', 15);
defined('DEFAULT_LIMIT_TESTIMONIALS') OR define('DEFAULT_LIMIT_TESTIMONIALS', 10);
defined('MAX_LIMIT_PER_PAGE')        OR define('MAX_LIMIT_PER_PAGE', 50);

/*
|--------------------------------------------------------------------------
| FILE UPLOAD CONSTANTS
|--------------------------------------------------------------------------
*/
defined('MAX_UPLOAD_SIZE_IMAGE')     OR define('MAX_UPLOAD_SIZE_IMAGE', 2048); // 2MB in KB
defined('MAX_UPLOAD_SIZE_DOCUMENT')  OR define('MAX_UPLOAD_SIZE_DOCUMENT', 5120); // 5MB in KB
defined('ALLOWED_IMAGE_TYPES')       OR define('ALLOWED_IMAGE_TYPES', 'gif|jpg|jpeg|png|webp');
defined('ALLOWED_VIDEO_TYPES')       OR define('ALLOWED_VIDEO_TYPES', 'mp4|avi|mov|mkv');
defined('ALLOWED_DOCUMENT_TYPES')    OR define('ALLOWED_DOCUMENT_TYPES', 'pdf|doc|docx|xls|xlsx');

/*
|--------------------------------------------------------------------------
| FEATURE FLAGS & CONFIGURATIONS
|--------------------------------------------------------------------------
*/
defined('ENABLE_QUERY_CACHING')      OR define('ENABLE_QUERY_CACHING', TRUE);
defined('CACHE_TTL_SECONDS')         OR define('CACHE_TTL_SECONDS', 3600); // 1 hour
defined('ENABLE_COMPRESSION')        OR define('ENABLE_COMPRESSION', TRUE);
defined('ENABLE_CORS')               OR define('ENABLE_CORS', TRUE);
defined('REQUIRE_JWT_AUTH')          OR define('REQUIRE_JWT_AUTH', TRUE);
