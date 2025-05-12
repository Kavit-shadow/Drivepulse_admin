<?php
require '../../../vendor/phpmailer/phpmailer/src/PHPMailer.php';
require '../../../vendor/phpmailer/phpmailer/src/SMTP.php';
require '../../../vendor/phpmailer/phpmailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Load email configuration
$configMailPath = "../../../configMail/configMail.json";
$json_data = file_get_contents($configMailPath);
$config_data = json_decode($json_data, true);

date_default_timezone_set('Asia/Kolkata');
function uploadImageToPostImages($imageFilePath) {
    $ch = curl_init();
    $postData = [
        'image' => new CURLFile($imageFilePath, mime_content_type($imageFilePath), basename($imageFilePath)),
        'key' => '4b304a153e63c467f693e831c14671d4' // Free API key for imgbb
    ];

    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.imgbb.com/1/upload',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_DNS_CACHE_TIMEOUT => 600
    ]);

    $response = curl_exec($ch);
    $timestamp = date('Y-m-d H:i:s');
    
    if (curl_errno($ch)) {
        $error = "cURL Error [$timestamp]: " . curl_error($ch);
        file_put_contents('./logs/image_curl_error.log', $error . PHP_EOL, FILE_APPEND);
        curl_close($ch);
        return null;
    }

    $responseLog = "cURL Response [$timestamp]: " . $response;
    file_put_contents('./logs/image_curl_response.log', $responseLog . PHP_EOL, FILE_APPEND);
    curl_close($ch);

    $responseData = json_decode($response, true);
    return $responseData['data']['url'] ?? null;
}

// Database connection
// $conn = mysqli_connect("localhost", "root", "", "billing");
include('../../../config.php');

if ($conn->connect_error) {
    die(json_encode(['error' => "Connection failed: " . $conn->connect_error]));
}

// Test mode flag
$isTestMode = true;

if ($isTestMode) {
    // Test email recipients
    $emailRecipients = [
        'bobby200543676@gmail.com',
        'rockgangsteryt@gmail.com',
        'bobbyok2005@gmail.com'
    ];

    // Test email content
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $attachmentType = $_POST['attachmentType'] === 'null' ? 'none' : $_POST['attachmentType'];
} else {
    // Get email recipients from database
    $sql = "SELECT email FROM cust_details";
    $result = $conn->query($sql);
    $emailRecipients = [];

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $emailRecipients[] = $row["email"];
        }
    } else {
        die(json_encode(['error' => 'No email addresses found in the database.']));
    }

    $conn->close();

    // Get email content from POST
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $attachmentType = $_POST['attachmentType'] === 'null' ? 'none' : $_POST['attachmentType'];
}

$batchSize = 50; // Number of emails to send per batch
$currentIndex = isset($_POST['currentIndex']) ? intval($_POST['currentIndex']) : 0;

// Initialize PHPMailer with common settings
function initMailer($config_data) {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->SMTPDebug = SMTP::DEBUG_OFF;
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = $config_data[0]['config-mail']['email'];
    $mail->Password = $config_data[0]['config-mail']['password'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom($config_data[0]['config-mail']['email'], $config_data[0]['config-mail-data']['mail-company-name']);
    return $mail;
}

// Process image if attachment type is image
$imageUrl = null;
if ($attachmentType === 'image' && isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
    $uploadDirectory = '../../../addmission_pdf/';
    $mailImageName = 'mailImage.png';
    $destinationPath = $uploadDirectory . $mailImageName;
    move_uploaded_file($_FILES['attachment']['tmp_name'], $destinationPath);
    
    $imageUrl = uploadImageToPostImages($destinationPath);
}

// Prepare HTML template
$htmlTemplate = "<!DOCTYPE html>
<html>
<head>
    <meta charset=\"UTF-8\">
    <style>
        body {
            font-family: \"Segoe UI\", Tahoma, Geneva, Verdana, sans-serif;
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
            background-image: url(\"https://i.postimg.cc/2yVpQWN3/mailbackground.png\");
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
        .section h1 {
            color: #007bff;
            font-size: 1.5em;
            margin-top: 0;
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
            padding: 10px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 0.85em;
            color: #777;
            text-align: center;
        }
        .customer-id {
            margin: 20px 0;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            text-align: center;
        }
        .customer-id strong {
            color: #0056b3;
            font-size: 1.2em;
            font-weight: bold;
        }
        .customer-id p {
            margin: 5px 0;
        }

        .background-image {
            display: block;
            margin: 20px auto;
            max-width: 100%;
            height: auto;
        }

    </style>
</head>
<body>
    <div class=\"container\">
        <div class=\"header\">
            <img src=\"".htmlspecialchars($config_data[0]['config-mail-data']['mail-header-logo'])."\" alt=\"Header Image\">
        </div>
        
        <div class=\"section\">
            <h1>".htmlspecialchars($subject)."</h1>
            <p>".$message."</p>
            %s

        </div>
      
        <div class=\"footer\">
            <p>Best regards,</p>
            <p>Patel Motor Driving School</p>
            <p>Contact us: <a href=\"mailto:patelmotordrivingschool1985@gmail.com\">patelmotordrivingschool1985@gmail.com</a> | +91 9725603403</p>
            <div class=\"social-media\">
                <a href=\"https://maps.app.goo.gl/VU13ZPf6ukhgV1w19\" target=\"_blank\">
                    <img src=\"https://i.postimg.cc/YqLZtgNq/pngwing-com.png\" alt=\"google maps\">
                </a>
                <a href=\"https://www.facebook.com/PatelMotorDrivingSchool/\" target=\"_blank\">
                    <img src=\"https://i.postimg.cc/2jTqwbgG/pngwing-com-1.png\" alt=\"facebook\">
                </a>
                <a href=\"https://www.instagram.com/patelmotordrivingschool\" target=\"_blank\">
                    <img src=\"https://i.postimg.cc/Dw2mbqvn/pngwing-com-2.png\" alt=\"LinkedIn\">
                </a>
            </div>
        </div>
    </div>
</body>
</html>";

$responses = [];
$endIndex = min($currentIndex + $batchSize, count($emailRecipients));

for ($i = $currentIndex; $i < $endIndex; $i++) {
    $recipient = $emailRecipients[$i];
    $mail = initMailer($config_data);
    
    try {
        $mail->addAddress($recipient);
        $mail->Subject = $subject;
        $mail->isHTML(true);

        if ($attachmentType === 'image') {
            $imageHtml = $imageUrl ? "<img src=\"{$imageUrl}\" class=\"background-image\" style=\"max-width: 100%; height: auto;\">" : "";
            $mail->Body = str_replace('%s', $imageHtml, $htmlTemplate);
        } elseif ($attachmentType === 'html' && isset($_FILES['attachment'])) {
            $mail->Body = file_get_contents($_FILES['attachment']['tmp_name']);
        } else {
            $mail->Body = str_replace('%s', '', $htmlTemplate);
        }
        
        $success = $mail->send();
        $responses[] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'recipient' => $recipient,
            'status' => $success ? 'success' : 'error',
            'message' => $success ? "Email sent successfully" : "Failed to send email"
        ];
        
    } catch (Exception $e) {
        $responses[] = [
            'timestamp' => date('Y-m-d H:i:s'),
            'recipient' => $recipient,
            'status' => 'error',
            'message' => strip_tags($e->getMessage()) // Strip HTML tags from error message
        ];
    }
}

// Log responses
$logFile = './logs/email_log.log';
$logData = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];
$logData = array_merge($logData, $responses);
file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT));

header('Content-Type: application/json');

// Return response
$response = [
    'currentIndex' => $endIndex,
    'totalRecipients' => count($emailRecipients),
    'batchResults' => $responses,
    'complete' => $endIndex >= count($emailRecipients),
    'imageUrl' => $imageUrl,
    'success' => true // Added success flag
];

echo json_encode($response);
?>