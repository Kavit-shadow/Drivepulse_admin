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
        'upload' => new CURLFile($imageFilePath, mime_content_type($imageFilePath), basename($imageFilePath)),
        'api_key' => 'free' // Using free API
    ];

    curl_setopt_array($ch, [
        CURLOPT_URL => 'https://api.postimages.org/',
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => $postData,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false
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
    return $responseData['url'] ?? null;
}

// Test email recipients
$emailRecipients = [
    'bobby200543676@gmail.com',
    'rockgangsteryt@gmail.com',
    'bobbyok2005@gmail.com'
];

// Prepare email content
$subject = "Test Email";
$message = "This is a test email.";
$attachmentType = 'html';


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
<style>
@page { size: A4 portrait; margin: 0; }
body { font-family: Arial, sans-serif; font-size: 14px; line-height: 1.6; margin: 0; padding: 0; background-color: #eee; }
span { background-color: #f0f0f0; padding: 2px 5px; }
.header { text-align: center; margin: 20px 0; }
.header img.logo { max-width: 70px; height: auto; margin-right: 10px; }
.header img.name-logo { max-width: 180px; height: auto; }
</style>
</head>
<body style=\"text-align: center; align-items: center; background-color: rgb(240, 242, 252);\">
<div class=\"header\">
    <img class=\"name-logo\" src=\"https://cdn.discordapp.com/attachments/839435729993072660/1136988864150507551/name2.png\" alt=\"Company name logo\">
</div>
<h2>%s</h2><br>
%s
<br><br>
<hr style=\"min-width: 80dvw; min-width: 80vw;\">
<h5>
    Address: <span><a href=\"https://goo.gl/maps/WvKi1kqaamyBR8y68\">CLICK HERE</a></span><br>
    Phone: <span><a href=\"tel:+919725603403\">+91 9725603403</a></span><br>
    Website: https://pmds.co.in/<br>
    E-Mail: patelmotordrivingschool1985@gmail.com
</h5>
</body>
</html>";

$responses = [];

foreach ($emailRecipients as $recipient) {
    $mail = initMailer($config_data);
    
    try {
        $mail->addAddress($recipient);
        $mail->Subject = $subject;
        $mail->isHTML(true);
        
        if ($attachmentType === 'image') {
            $imageHtml = $imageUrl ? "<img src=\"{$imageUrl}\" class=\"background-image\" style=\"max-width: 100%; height: auto;\">" : "";
            $mail->Body = sprintf($htmlTemplate, $message, $imageHtml);
        } elseif ($attachmentType === 'html' && isset($_FILES['attachment'])) {
            $mail->Body = file_get_contents($_FILES['attachment']['tmp_name']);
        } else {
            $mail->Body = sprintf($htmlTemplate, $message, "");
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
            'message' => $e->getMessage()
        ];
    }
}

// Log responses
$logFile = './logs/email_log.log';
$logData = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];
$logData = array_merge($logData, $responses);
file_put_contents($logFile, json_encode($logData, JSON_PRETTY_PRINT));

// Return response
$response = [
    'totalRecipients' => count($emailRecipients),
    'batchResults' => $responses,
    'complete' => true
];

echo json_encode($response);
?>