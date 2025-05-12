<?php

// Add CORS headers if needed
// Add CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = $_POST['email'] ?? '';
        $submittedOtp = $_POST['otp'] ?? '';

        if (empty($email) || empty($submittedOtp)) {
            throw new Exception('Email and OTP are required');
        }

        // Read OTPs from JSON file
        $jsonFile = '../storage/json/otps.json';
        if (!file_exists($jsonFile)) {
            throw new Exception('OTP verification system error');
        }

        $otps = json_decode(file_get_contents($jsonFile), true) ?? [];

        // Remove expired OTPs
        $otps = array_filter($otps, function ($item) {
            return $item['expires'] > time();
        });

        // Find matching OTP for email
        $validOtp = false;
        foreach ($otps as $key => $otpData) {
            if ($otpData['email'] === $email && $otpData['otp'] === $submittedOtp) {
                if ($otpData['expires'] > time()) {
                    $validOtp = true;
                    // Remove used OTP
                    unset($otps[$key]);
                }
                break;
            }
        }

        // Save updated OTPs back to file
        file_put_contents($jsonFile, json_encode($otps));

        if ($validOtp) {
            echo json_encode(['status' => 'success', 'message' => 'OTP verified successfully']);
        } else {
            throw new Exception('Invalid or expired OTP');
        }

    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}

?>
