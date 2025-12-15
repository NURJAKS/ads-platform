<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Http;

echo "--- STARTING SYSTEM VERIFICATION ---\n";

// 1. Check Database
try {
    DB::connection()->getPdo();
    echo "✅ Database (PostgreSQL): CONNECTED\n";
} catch (\Exception $e) {
    echo "❌ Database (PostgreSQL): FAILED - " . $e->getMessage() . "\n";
}

// 2. Check Redis
try {
    Redis::connection()->ping();
    echo "✅ Redis: CONNECTED\n";
} catch (\Exception $e) {
    echo "❌ Redis: FAILED - " . $e->getMessage() . "\n";
}

// 3. Check Go Image Service (via internal network or mapped port)
// In Docker, it is 'go-image-service:8080' or localhost:8080 if mapped.
// Since this runs inside the laravel container (or host?), we should try host interaction.
// Assuming this runs via 'php artisan' on host, we hit localhost:8080. 
// If inside container, we hit 'go-image-service:8080'.
// Let's try localhost first as we are running from 'php' command on host (likely, or mapped).
try {
    $response = Http::get('http://localhost:8080/'); // Assuming root returns something or 404
    // If we get response (even 404), it's reachable.
    echo "✅ Go Image Service: REACHABLE (Status: " . $response->status() . ")\n";
} catch (\Exception $e) {
    echo "⚠️ Go Image Service: Unreachable via localhost:8080. (Might be okay if internal only)\n";
}

// 4. Check Internal API (Login)
$response = Http::post('http://localhost:8000/api/v1/auth/login', [
    'email' => 'admin@example.com',
    'password' => 'password'
]);

if ($response->successful()) {
    echo "✅ API Login: SUCCESS\n";
} else {
    echo "⚠️ API Login: Failed (" . $response->status() . ") - Might be invalid credentials or seeded data missing.\n";
}


echo "--- VERIFICATION COMPLETE ---\n";
