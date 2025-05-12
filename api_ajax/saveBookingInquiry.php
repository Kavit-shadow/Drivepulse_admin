<?php
// Add CORS headers
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS'); // Added OPTIONS
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Max-Age: 86400'); // Cache preflight for 24 hours

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200); // Return 200 OK for preflight
    exit();
}

header('Content-Type: application/json');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

include("../config.php");


// Get config data
$configMailPath = "../configMail/configMail.json";
$json_data = file_get_contents($configMailPath);
$config_data = json_decode($json_data, true);

// Function to save to JSON file
function createBookingTable($conn) {
    $sql = "CREATE TABLE IF NOT EXISTS booking_inquiries (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100),
        email VARCHAR(100),
        phone VARCHAR(20),
        package_name VARCHAR(100),
        package_price VARCHAR(20),
        package_features TEXT,
        vehicle_type VARCHAR(50),
        vehicle_name VARCHAR(50),
        duration VARCHAR(20),
        time_slot VARCHAR(50),
        booking_inquiry_date DATE,
        distance VARCHAR(50),
        session_duration VARCHAR(50),
        is_read BOOLEAN DEFAULT FALSE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    if (!$conn->query($sql)) {
        throw new Exception("Error creating table: " . $conn->error);
    }
}

function saveToDatabase($bookingData, $conn) {
    createBookingTable($conn);
    
    // Extract all required data
    $name = $bookingData['userDetails']['name'];
    $email = $bookingData['userDetails']['email'];
    $phone = $bookingData['userDetails']['phone'];
    $package_name = $bookingData['package']['name'];
    $package_price = str_replace('₹', '', $bookingData['package']['price']); // Remove ₹ symbol
    $package_features = json_encode($bookingData['package']['features']);
    $vehicle_type = $bookingData['package']['vehicleType'];
    $vehicle_name = $bookingData['vehicle']['name'];
    $duration = $bookingData['package']['duration'];
    $time_slot = $bookingData['timeSlot']['time'];
    $booking_inquiry_date = date('Y-m-d', strtotime($bookingData['bookingDate']));
    
    // Extract distance and session duration from package features
    $distance = '';
    $session_duration = '';
    foreach ($bookingData['package']['features'] as $feature) {
        if (strpos($feature, 'KM') !== false) {
            $distance = $feature;
        }
        if (strpos($feature, 'Minutes') !== false) {
            $session_duration = $feature;
        }
    }
    
    $sql = "INSERT INTO booking_inquiries (
        name, email, phone, package_name, package_price, package_features,
        vehicle_type, vehicle_name, duration, time_slot, booking_inquiry_date,
        distance, session_duration
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "ssssdssssssss",
        $name, $email, $phone, $package_name, $package_price, $package_features,
        $vehicle_type, $vehicle_name, $duration, $time_slot, $booking_inquiry_date,
        $distance, $session_duration
    );
    
    if (!$stmt->execute()) {
        throw new Exception("Error saving to database: " . $stmt->error);
    }
    
    return $stmt->insert_id;
}

// function markAsRead($id, $conn) {
//     $sql = "UPDATE booking_inquiries SET is_read = TRUE WHERE id = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $id);
//     return $stmt->execute();
// }

function saveToJson($bookingData)
{
    $jsonFile = '../storage/json/bookingInquirys.json';
    $bookings = [];

    if (file_exists($jsonFile)) {
        $bookings = json_decode(file_get_contents($jsonFile), true) ?? [];
    }

    $bookings[] = $bookingData;
    return file_put_contents($jsonFile, json_encode($bookings, JSON_PRETTY_PRINT));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get POST data
        $jsonData = file_get_contents('php://input');
        $bookingData = json_decode($jsonData, true);

        if (!$bookingData) {
            throw new Exception('Invalid booking data: ' . json_last_error_msg());
        }

        if ($conn->connect_error) {
            throw new Exception("Database connection failed: " . $conn->connect_error);
        }

        // Save to both database and JSON
        $dbSaved = saveToDatabase($bookingData, $conn);
        $jsonSaved = saveToJson($bookingData);

        if (!$jsonSaved) {
            throw new Exception('Failed to save booking data to JSON. Error: ' . error_get_last()['message']);
        }

        $conn->close();

        
        // Send confirmation email
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF; // Disable debug output
            $mail->isSMTP();
            $mail->Host = 'smtp.hostinger.com';
            $mail->SMTPAuth = true;
            $mail->Username = $config_data[0]['config-mail']['email'];
            $mail->Password = $config_data[0]['config-mail']['password'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = 465;

            // Recipients
            $mail->setFrom($config_data[0]['config-mail']['email'], 'Patel Motor Driving School');
            
            // Add customer as recipient
            $mail->addAddress($bookingData['userDetails']['email'], $bookingData['userDetails']['name']);

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Booking Inquiry Confirmation';

            // Format package features as HTML list
            $featuresList = '';
            foreach ($bookingData['package']['features'] as $feature) {
                $featuresList .= "<li>$feature</li>";
            }

            // Email body for customer
            $emailBody = '
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
            padding: 15px;
            border-radius: 8px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .booking-details {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        }
        .booking-item {
            margin: 15px 0;
            padding: 10px;
            background: #f8f9f9;
            border-radius: 8px;
        }
        .package-features {
            list-style-type: none;
            padding-left: 0;
        }
        .package-features li {
            padding: 5px 0;
            color: #555;
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="' . htmlspecialchars($config_data[0]['config-mail-data']['mail-header-logo']) . '" alt="Header Image">
        </div>
        <div class="section">
            <h1>Booking Inquiry Confirmation</h1>
            <p>Dear ' . htmlspecialchars($bookingData['userDetails']['name']) . ',</p>
            <p>Thank you for choosing Patel Motor Driving School. We\'ve received your booking inquiry with the following details:</p>
            
            <div class="booking-details">
                <div class="booking-item">
                    <h3>Package Details:</h3>
                    <p><strong>Name:</strong> ' . htmlspecialchars($bookingData['package']['name']) . '</p>
                    <p><strong>Vehicle Type:</strong> ' . htmlspecialchars($bookingData['package']['vehicleType']) . '</p>
                    <p><strong>Price:</strong> ' . htmlspecialchars($bookingData['package']['price']) . '</p>
                    <p><strong>Duration:</strong> ' . htmlspecialchars($bookingData['package']['duration']) . '</p>
                    <h4>Features:</h4>
                    <ul class="package-features">
                        ' . $featuresList . '
                    </ul>
                </div>
                
                <div class="booking-item">
                    <h3>Vehicle:</h3>
                    <p>' . htmlspecialchars($bookingData['vehicle']['name']) . '</p>
                </div>
                
                <div class="booking-item">
                    <h3>Time Slot:</h3>
                    <p>' . htmlspecialchars($bookingData['timeSlot']['time']) . '</p>
                </div>
                
                <div class="booking-item">
                    <h3>Booking Inquiry Date:</h3>
                    <p>' . date('F j, Y', strtotime($bookingData['bookingDate'])) . '</p>
                </div>
            </div>

            <p>Our team will contact you shortly at ' . htmlspecialchars($bookingData['userDetails']['phone']) . ' to confirm your booking.</p>
        </div>
        
       <div class="footer">
            <p>Best regards,</p>
            <p>' . htmlspecialchars($config_data[0]['config-mail-data']['mail-company-name']) . '</p>
            <p>Contact us: <a href="mailto:info@example.com">patelmotordrivingschool1985@gmail.com</a> | ' . htmlspecialchars($config_data[0]['config-mail-data']['mail-contact-number']) . '</p>
            <div class="social-media">
                <a href="https://maps.app.goo.gl/VU13ZPf6ukhgV1w19" target="_blank">
                    <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-google-maps.png" alt="google maps">
                </a>
                <a href="https://www.facebook.com/PatelMotorDrivingSchool/" target="_blank">
                    <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-facebook.png" alt="facebook">
                </a>
                <a href="https://www.instagram.com/patelmotordrivingschool" target="_blank">
                    <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-instagram.png" alt="instagram">
                </a>
                
            </div>

        </div>
    </div>
</body>
</html>';

            $mail->Body = $emailBody;
            $mail->send();

            // Create new PHPMailer instance for admin notification
            $adminMail = new PHPMailer(true);
            
            // Server settings for admin mail
            $adminMail->SMTPDebug = SMTP::DEBUG_OFF; // Disable debug output
            $adminMail->isSMTP();
            $adminMail->Host = 'smtp.hostinger.com';
            $adminMail->SMTPAuth = true;
            $adminMail->Username = $config_data[0]['config-mail']['email'];
            $adminMail->Password = $config_data[0]['config-mail']['password'];
            $adminMail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $adminMail->Port = 465;

            // Set admin recipient
            $adminMail->setFrom($config_data[0]['config-mail']['email'], 'Patel Motor Driving School');
            // Add multiple admin email addresses
            $adminMail->addAddress('patelmotordrivingschool1985@gmail.com', 'System Admin');
            $adminMail->addAddress('hemal.babaraj30@gmail.com', 'Hemal Patel'); 
            // $adminMail->addAddress('patelmotordrivingschool3@gmail.com', 'System Admin 3');

            // Admin email content
            $adminMail->isHTML(true);
            $adminMail->Subject = 'New Booking Inquiry Received';

            // Admin email body
            $adminEmailBody = '
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
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        .alert {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
            text-align: center;
        }
        .alert h2 {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
        }
        .details {
            margin: 20px 0;
        }
        .section-title {
            color: #4a5568;
            font-size: 18px;
            font-weight: 600;
            margin: 25px 0 15px;
            padding-bottom: 8px;
            border-bottom: 2px solid #e2e8f0;
        }
        .info-card {
            background-color: #f8fafc;
            padding: 20px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            margin-bottom: 20px;
        }
        .info-item {
            display: flex;
            margin-bottom: 12px;
        }
        .info-label {
            font-weight: 600;
            min-width: 140px;
            color: #4a5568;
        }
        .info-value {
            color: #2d3748;
            flex: 1;
        }
        .action-required {
            background-color: #fed7d7;
            color: #c53030;
            padding: 15px;
            border-radius: 8px;
            margin-top: 25px;
            font-weight: 600;
            text-align: center;
        }
        .action-buttons {
            display: flex;
            justify-content: center;
            gap: .8rem;
            margin-top: 20px;
        }
        .action-button {
            padding: 12px 25px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            transition: all 0.2s ease;
        }
        .call-button {
            background-color: #48bb78;
            color: white !important;
            border: 1px solid #48bb78;
        }
        .call-button:hover {
            background-color: #38a169;
            border-color: #38a169;
        }
        .whatsapp-button {
            background-color: #25d366;
            color: white !important;
            border: 1px solid #25d366;
        }
        .whatsapp-button:hover {
            background-color: #128c7e;
            border-color: #128c7e;
        }
        .action-button img {
            width: 20px;
            height: 20px;
            object-fit: contain;
        }
        ul {
            list-style-type: disc;
            margin: 0;
            padding-left: 20px;
        }
        li {
            color: #2d3748;
            margin-bottom: 4px;
            line-height: 1.4;
        }
        li:last-child {
            margin-bottom: 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="alert">
            <h2>New Booking Inquiry Alert</h2>
            <p>A new booking inquiry has been received from ' . htmlspecialchars($bookingData['userDetails']['name']) . '</p>
        </div>
        
        <div class="details">
            <h3 class="section-title">Customer Information</h3>
            <div class="info-card">
                <div class="info-item">
                    <span class="info-label">Name:</span>
                    <span class="info-value">' . htmlspecialchars($bookingData['userDetails']['name']) . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Email:</span>
                    <span class="info-value">' . htmlspecialchars($bookingData['userDetails']['email']) . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Phone:</span>
                    <span class="info-value">' . htmlspecialchars($bookingData['userDetails']['phone']) . '</span>
                </div>
            </div>
            
            <h3 class="section-title">Booking Details</h3>
            <div class="info-card">
                <div class="info-item">
                    <span class="info-label">Package:</span>
                    <span class="info-value">' . htmlspecialchars($bookingData['package']['name']) . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Vehicle Type:</span>
                    <span class="info-value">' . htmlspecialchars($bookingData['package']['vehicleType']) . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Price:</span>
                    <span class="info-value">' . htmlspecialchars($bookingData['package']['price']) . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Duration:</span>
                    <span class="info-value">' . htmlspecialchars($bookingData['package']['duration']) . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Features:</span>
                    <span class="info-value">
                        <ul style="margin: 0; padding-left: 20px;">
                            ' . implode('', array_map(function($feature) {
                                return '<li>' . htmlspecialchars($feature) . '</li>';
                            }, $bookingData['package']['features'])) . '
                        </ul>
                    </span>
                </div>
                <div class="info-item">
                    <span class="info-label">Vehicle:</span>
                    <span class="info-value">' . htmlspecialchars($bookingData['vehicle']['name']) . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Time Slot:</span>
                    <span class="info-value">' . htmlspecialchars($bookingData['timeSlot']['time']) . '</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Booking Date:</span>
                    <span class="info-value">' . date('F j, Y', strtotime($bookingData['bookingDate'])) . '</span>
                </div>
            </div>
        </div>
        
        <div class="action-required">
            Action Required: Please contact the customer to confirm their booking
            <div class="action-buttons">
                <a href="tel:' . htmlspecialchars($bookingData['userDetails']['phone']) . '" class="action-button call-button">
                    <img src="https://i.postimg.cc/YS0D9pDj/image.png" alt="Call">
                    Call Now
                </a>
                <a href="https://wa.me/91' . htmlspecialchars($bookingData['userDetails']['phone']) . '" target="_blank" class="action-button whatsapp-button">
                    <img src="https://i.postimg.cc/RC2mxYFc/image.png" alt="WhatsApp">
                    WhatsApp
                </a>
            </div>
        </div>
    </div>
</body>
</html>';

            $adminMail->Body = $adminEmailBody;
            $adminMail->send();

            echo json_encode([
                'status' => 'success', 
                'message' => 'Booking saved successfully'
            ]);
        } catch (Exception $e) {
            error_log("Email sending failed: {$mail->ErrorInfo}");
            echo json_encode([
                'status' => 'success', 
                'message' => 'Booking saved successfully but email failed',
                'error' => $e->getMessage() 
            ]);
        }
    } catch (Exception $e) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error', 
            'message' => $e->getMessage()
        ]);
    }
} else {
    http_response_code(405);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Method not allowed'
    ]);
}
