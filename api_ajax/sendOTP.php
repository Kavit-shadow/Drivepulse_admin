<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// // Add CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');


$configMailPath = "../configMail/configMail.json";
$json_data = file_get_contents($configMailPath);
$config_data = json_decode($json_data, true);

include '../config.php';

function saveVisitLog($email, $name, $phone, $userData) {
    global $conn;
    
    $sql = "INSERT INTO VisitLogs (email, name, phone, user_data) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $email, $name, $phone, $userData);
    
    if (!$stmt->execute()) {
        throw new Exception('Failed to save visit log');
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = $_POST['email'] ?? '';
        $name = $_POST['name'] ?? '';
        $phone = $_POST['phone'] ?? '';
        $userData = $_POST['userData'] ?? '';

        // Save visit log
        saveVisitLog($email, $name, $phone, $userData);
        


        if (empty($email)) {
            throw new Exception('Email is required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Invalid email format');
        }
        // Generate 4 digit OTP
        $otp = str_pad(random_int(0, 9999), 4, '0', STR_PAD_LEFT);

        // Store OTP data
        $otpData = [
            'email' => $email,
            'otp' => $otp,
            'expires' => time() + (10 * 60) // 10 minutes from now
        ];

        $jsonFile = '../storage/json/otps.json';

        // Read existing OTPs
        $otps = [];
        if (file_exists($jsonFile)) {
            $otps = json_decode(file_get_contents($jsonFile), true) ?? [];
        }

        // Remove expired OTPs
        $otps = array_filter($otps, function ($item) {
            return $item['expires'] > time();
        });

        // Add new OTP
        $otps[] = $otpData;

        // Save to JSON file
        file_put_contents($jsonFile, json_encode($otps));

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        $mail->isSMTP();
        // $mail->Host = 'smtp.gmail.com';
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = $config_data[0]['config-mail']['email'];
        $mail->Password = $config_data[0]['config-mail']['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // Changed from STARTTLS to SMTPS
        $mail->Port = 465;

        $mail->setFrom($config_data[0]['config-mail']['email'], 'DrivePulse');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'Your OTP for Verification';

        include('../configWeb.php');
        $mailDevMsg = $DevMode ? '<div class="note">
            <p>This is a test email sent by the development team. Please do not reply.</p>
        </div>' : '';

        $mail->Body = '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: auto;
            padding: 20px;
            border-radius: 8px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: rgba(255, 255, 255, 0.9);
            background-image: url("https://patelmotordrivingschool.com/storage/images/pmds-assets/pmds-mail-background-1.png");
            background-size: cover;
            background-position: center;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .header img {
            max-width: 200px; 
            height: auto;
            border-radius: 8px;
        }
        .section {
            margin-bottom: 20px;
            padding: 20px;
            border-radius: 12px;
            background-color: #f9f9f9;
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.1);
            border: 1px solid #e0e0e0;
        }
        .section h1 {
            color: #2c5282;
            font-size: 1.8em;
            margin-top: 0;
            text-align: center;
            margin-bottom: 20px;
        }
        .otp-display {
            background: #edf2f7;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 20px 0;
            border: 2px dashed #4a5568;
        }
        .otp-number {
            font-size: 2.5em;
            color: #2d3748;
            font-weight: bold;
            letter-spacing: 4px;
        }
        .expiry-timer {
            color: #e53e3e;
            font-size: 1.1em;
            margin-top: 10px;
            font-weight: 600;
        }
        .footer {
            margin-top: 20px;
            padding: 15px;
            border-top: 1px solid #ddd;
            font-size: 0.9em;
            color: #666;
            text-align: center;
            background-color: #f9f9f9;
            border-radius: 0 0 8px 8px;
        }
        .footer p {
            margin: 0;
        }
        .social-media {
            margin-top: 10px;
        }
        .social-media a {
            margin: 0 8px;
            display: inline-block;
            transition: transform 0.2s;
        }
        .social-media a:hover {
            transform: scale(1.1);
        }
        .social-media img {
            width: 28px;
            height: 28px;
            vertical-align: middle;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 15px;
            font-size: 16px;
            color: #ffffff;
            background-color: #007bff;
            border-radius: 5px;
            text-decoration: none;
            text-align: center;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .note {
            margin-top: 20px;
            padding: 10px;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.85em;
            color: #555;
            text-align: center;
        }
        .payment-section {
            margin-top: 20px;
            padding: 15px;
            background-color: #e9f4f9;
            border: 1px solid #d1e3e5;
            border-radius: 8px;
            text-align: center;
        }
        .payment-section img {
            max-width: 230px;
            height: auto;
        }
        .disclaimer {
            margin-top: 20px;
            padding: 15px;
            background-color: #fff5f5;
            border: 1px solid #fed7d7;
            border-radius: 8px;
            font-size: 0.9em;
            color: #744210;
            text-align: center;
        }
        .welcome-message {
            text-align: center;
            color: #2d3748;
            font-size: 1.1em;
            margin-bottom: 15px;
        }
        .security-notice {
            margin-top: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-size: 0.9em;
            color: #777;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
                 ' . $mailDevMsg . '
        <div class="header">
            <img src="' . htmlspecialchars($config_data[0]['config-mail-data']['mail-header-logo']) . '" alt="Header Image">
        </div>
        <div class="section">
            <div class="welcome-message">
                <p>Thank you for choosing Patel Motor Driving School. To ensure your security, we\'ve generated a one-time password (OTP) for you.</p>
            </div>
            <div class="otp-display">
                <p>Your OTP is:</p>
                <div class="otp-number">' . $otp . '</div>
            </div>
            <p style="text-align: center; color: #4a5568;">Please enter this code to complete your verification process.</p>
            <div class="disclaimer">
                <p>This code expires in 10 minutes.</p>
            </div>
            <div class="security-notice">
                <p>If you did not request an OTP from Patel Motor Driving School, you can safely ignore this email. Someone else might have entered your email address by mistake.</p>
            </div>
        </div>
        <div class="footer">
            <p>Best regards,</p>
            <p>' . htmlspecialchars($config_data[0]['config-mail-data']['mail-company-name']) . '</p>
            <p>Contact us: <a href="mailto:drivepulse@gmail.com">DrivePulse@gmail.com</a> | ' . htmlspecialchars($config_data[0]['config-mail-data']['mail-contact-number']) . '</p>
            <div class="social-media">
                <a href="#" target="_blank">
                    <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-google-maps.png" alt="google maps">
                </a>
                <a href="#" target="_blank">
                    <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-facebook.png" alt="facebook">
                </a>
                <a href="#" target="_blank">
                    <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-instagram.png" alt="instagram">
                </a>
                
            </div>

        </div>
    </div>
</body>
</html>';
        $mail->AltBody = "Your OTP is: {$otp}\nThis OTP will expire in 10 minutes.";

        $mail->send();

        echo json_encode(['status' => 'success', 'message' => 'OTP sent successfully']);
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
}
