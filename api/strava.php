<?php
header('Access-Control-Allow-Origin: http://localhost:5173');  // Your Vite dev server
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Credentials: true');

define('AUTHORIZED', true);
require_once 'config.php';

// CORS headers
$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
if (in_array($origin, $ALLOWED_ORIGINS)) {
    header("Access-Control-Allow-Origin: $origin");
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
    header("Access-Control-Allow-Headers: Content-Type");
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Error handling
function sendError($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['error' => $message]);
    exit;
}

// Token management
function refreshAccessToken() {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://www.strava.com/oauth/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query([
            'client_id' => STRAVA_CLIENT_ID,
            'client_secret' => STRAVA_CLIENT_SECRET,
            'refresh_token' => STRAVA_REFRESH_TOKEN,
            'grant_type' => 'refresh_token'
        ])
    ]);

    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);

    if ($error) {
        sendError('Failed to refresh token', 500);
    }

    return json_decode($response, true);
}

// Route handling
$route = $_GET['route'] ?? '';

switch ($route) {
    case 'activities':
        // Get fresh access token
        $tokenData = refreshAccessToken();
        $accessToken = $tokenData['access_token'];


    // Get query parameters from the request
        $after = $_GET['after'] ?? '';
        $before = $_GET['before'] ?? '';
        $perPage = $_GET['per_page'] ?? '100';

        // Build the URL with query parameters
        $url = STRAVA_API_BASE . '/athlete/activities?' . http_build_query([
            'after' => $after,
            'before' => $before,
            'per_page' => $perPage
        ]);

        // Make request to Strava
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $accessToken
            ]
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        curl_close($ch);

        if ($error) {
            sendError('Failed to fetch activities', 500);
        }

        header('Content-Type: application/json');
        echo $response;
        break;

    default:
        sendError('Invalid endpoint', 404);
}
