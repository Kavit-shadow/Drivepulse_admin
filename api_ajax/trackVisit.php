<?php
// Add CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

include '../config.php';

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Create table if it doesn't exist
$createTable = "CREATE TABLE IF NOT EXISTS visit_tracking (
    id INT AUTO_INCREMENT PRIMARY KEY,
    visit_data JSON,
    timestamp DATETIME,
    ip_address TEXT,
    page_url VARCHAR(2048),
    user_agent VARCHAR(1024),
    referrer VARCHAR(2048),
    city VARCHAR(100),
    region VARCHAR(100), 
    country VARCHAR(100),
    postal VARCHAR(20),
    latitude DECIMAL(10,8),
    longitude DECIMAL(11,8),
    timezone VARCHAR(100),
    asn VARCHAR(100),
    isp VARCHAR(255),
    session_id VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($createTable)) {
    die(json_encode([
        'status' => 'error',
        'message' => 'Failed to create table: ' . $conn->error
    ]));
}

// Get the raw POST data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate required data
        if (!$data) {
            throw new Exception('Invalid JSON data');
        }

        // Prepare visit data
        $visitData = json_encode($data);
        $timestamp = date('Y-m-d H:i:s', strtotime($data['timestamp']));
        $ip = json_encode($data['ip'] ?? '');
        $url = $data['url'] ?? '';
        $userAgent = $data['browser']['userAgent'] ?? '';
        $referrer = $data['referrer'] ?? '';
        $city = $data['location']['city'] ?? '';
        $region = $data['location']['region'] ?? '';
        $country = $data['location']['country'] ?? '';
        $postal = $data['location']['postal'] ?? '';
        $latitude = $data['location']['latitude'] ?? null;
        $longitude = $data['location']['longitude'] ?? null;
        $timezone = $data['location']['timezone'] ?? '';
        $asn = $data['network']['asn'] ?? '';
        $isp = $data['network']['isp'] ?? '';
        $sessionId = $data['session']['id'] ?? '';
        
        // Insert into database
        $sql = "INSERT INTO visit_tracking (
            visit_data,
            timestamp,
            ip_address,
            page_url,
            user_agent,
            referrer,
            city,
            region,
            country,
            postal,
            latitude,
            longitude,
            timezone,
            asn,
            isp,
            session_id
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssddssss",
            $visitData,
            $timestamp,
            $ip,
            $url,
            $userAgent,
            $referrer,
            $city,
            $region, 
            $country,
            $postal,
            $latitude,
            $longitude,
            $timezone,
            $asn,
            $isp,
            $sessionId
        );

        if (!$stmt->execute()) {
            throw new Exception('Failed to save visit data: ' . $stmt->error);
        }

        $response = [
            'status' => 'success',
            'message' => 'Visit tracked successfully',
            'timestamp' => $timestamp
        ];

        echo json_encode($response);

    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    } finally {
        if (isset($stmt)) {
            $stmt->close();
        }
        if (isset($conn)) {
            $conn->close();
        }
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error',
        'message' => 'Method not allowed'
    ]);
}
?>