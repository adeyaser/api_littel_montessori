#!/usr/bin/env php
<?php
/**
 * QUICK PERFORMANCE CHECK SCRIPT
 * Run: php bin/check-performance.php
 * 
 * Mengecek kecepatan API dan mengidentifikasi bottleneck
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

$base_url = 'http://localhost/little_home_api';
$test_token = 'YOUR_JWT_TOKEN_HERE';

echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║         API PERFORMANCE TESTING - Little Home Mobile API       ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

// Test configurations
$tests = [
    [
        'name' => 'Login Endpoint',
        'method' => 'POST',
        'url' => $base_url . '/walimurid/login',
        'data' => [
            'email' => 'joko@example.com',
            'password' => 'password123'
        ],
        'auth' => false
    ],
    [
        'name' => 'Dashboard',
        'method' => 'GET',
        'url' => $base_url . '/walimurid/dashboard?id_anak=1',
        'auth' => true
    ],
    [
        'name' => 'Posting (Page 1)',
        'method' => 'GET',
        'url' => $base_url . '/walimurid/posting?id_anak=1&page=1',
        'auth' => true
    ],
    [
        'name' => 'Pencatatan',
        'method' => 'GET',
        'url' => $base_url . '/walimurid/pencatatan?id_anak=1',
        'auth' => true
    ],
    [
        'name' => 'Testimoni',
        'method' => 'GET',
        'url' => $base_url . '/walimurid/testimoni',
        'auth' => true
    ],
    [
        'name' => 'Tagihan',
        'method' => 'GET',
        'url' => $base_url . '/walimurid/tagihan?id_anak=1',
        'auth' => true
    ]
];

$overall_results = [];

foreach ($tests as $test) {
    echo "\n📍 Testing: {$test['name']}\n";
    echo "─────────────────────────────────────────────────────────────\n";
    
    for ($i = 1; $i <= 3; $i++) {
        $start = microtime(true);
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $test['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $test['method']);
        
        // Add headers
        $headers = ['Content-Type: application/json'];
        if ($test['auth']) {
            $headers[] = 'Authorization: Bearer ' . $test_token;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        
        // Add POST data if needed
        if ($test['method'] === 'POST' && isset($test['data'])) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($test['data']));
        }
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $content_length = curl_getinfo($ch, CURLINFO_CONTENT_LENGTH_DOWNLOAD);
        curl_close($ch);
        
        $elapsed = (microtime(true) - $start) * 1000; // Convert to milliseconds
        
        $response_data = json_decode($response, true);
        $is_success = $http_code >= 200 && $http_code < 300;
        
        $status_icon = $is_success ? '✅' : '❌';
        $time_color = $elapsed < 200 ? '🟢' : ($elapsed < 500 ? '🟡' : '🔴');
        
        echo "{$status_icon} Run {$i}: {$time_color} {$elapsed}ms | HTTP {$http_code}";
        
        if ($content_length > 0) {
            $size_kb = $content_length / 1024;
            echo " | Size: {$size_kb}KB";
        }
        
        echo "\n";
        
        $overall_results[$test['name']][] = [
            'time' => $elapsed,
            'status' => $http_code,
            'size' => $content_length
        ];
    }
}

// Summary
echo "\n╔════════════════════════════════════════════════════════════════╗\n";
echo "║                        SUMMARY RESULTS                          ║\n";
echo "╚════════════════════════════════════════════════════════════════╝\n\n";

printf("%-20s | %-10s | %-10s | %-10s\n", "Endpoint", "Avg Time", "Min", "Max");
echo "─────────────────────────────────────────────────────────────────\n";

foreach ($overall_results as $endpoint => $results) {
    $times = array_column($results, 'time');
    $avg = array_sum($times) / count($times);
    $min = min($times);
    $max = max($times);
    
    $status = $avg < 200 ? '✅' : ($avg < 500 ? '⚠️' : '❌');
    
    printf("%s %-16s | %7.0fms  | %7.0fms | %7.0fms\n", 
        $status, 
        substr($endpoint, 0, 16), 
        $avg, 
        $min, 
        $max
    );
}

echo "\n📝 RECOMMENDATIONS:\n";
echo "  • 🟢 <200ms   = EXCELLENT\n";
echo "  • 🟡 200-500ms = ACCEPTABLE\n";
echo "  • 🔴 >500ms   = NEEDS OPTIMIZATION\n";

echo "\n💡 For detailed optimization guide, read PERFORMANCE_AUDIT_REPORT.md\n\n";
?>
