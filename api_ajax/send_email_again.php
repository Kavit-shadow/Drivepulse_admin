<?php




use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Decode Base64 PDF and create a temporary file
function createTempFileFromBase64($base64String, $fileName)
{
    // Decode Base64 string
    $pdfData = base64_decode($base64String);

    // Create a temporary file
    $tempFilePath = tempnam(sys_get_temp_dir(), 'pdf');
    file_put_contents($tempFilePath, $pdfData);

    return $tempFilePath;
}

function sendMail($email, $name, $config_data, $pdfBase64)
{
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);


    $mailFileName = $name . "'s booking receipt";

    try {
        // Server settings
        $mail->SMTPDebug = 0; // Set to SMTP::DEBUG_SERVER for debugging
        $mail->isSMTP();
        //$mail->Host = 'smtp.gmail.com';
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = $config_data[0]['config-mail']['email']; // Your SMTP username
        $mail->Password = $config_data[0]['config-mail']['password']; // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465; // TCP port to connect to

        // Recipients
        $mail->setFrom($config_data[0]['config-mail']['email'], $config_data[0]['config-mail-data']['mail-company-name']);
        $mail->addAddress($email, $name);


        // $tempFilePath = createTempFileFromBase64($pdfBase64, 'attachment.pdf');

        // // Attach the file
        // $mail->addAttachment($tempFilePath, $mailFileName);


        list($mimeType, $base64Data) = explode(',', $pdfBase64, 2);



        $pdfBinaryData = base64_decode($base64Data, true);

        // Attach the PDF directly from the decoded Base64 string
        $mail->addStringAttachment($pdfBinaryData, $mailFileName, 'base64', 'application/pdf');


        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = $config_data[0]['config-mail-data']['mail-subject'];

        // Compose the email body
        $Mailparagraphs = '';
        if (isset($config_data[0]['config-mail-data']['mail-paragraph']) && !empty($config_data[0]['config-mail-data']['mail-paragraph'])) {
            foreach ($config_data[0]['config-mail-data']['mail-paragraph'] as $paragraph) {
                $Mailparagraphs .= "<section>" . $paragraph . "</section>";
            }
        }

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
            max-width: 230px; /* Adjust as needed */
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
    </style>
</head>
<body>
    <div class="container">
             ' . $mailDevMsg . '
        <div class="header">
            <img src="' . htmlspecialchars($config_data[0]['config-mail-data']['mail-header-logo']) . '" alt="Header Image">
        </div>
        <div class="section">
            <h1>' . htmlspecialchars($config_data[0]['config-mail-data']['mail-heading']) . '</h1>
            <p>' . htmlspecialchars($config_data[0]['config-mail-data']['mail-greetings']) . ' <b>' . htmlspecialchars($name) . '</p>

            ' . $Mailparagraphs . '

            
            <div class="disclaimer">
                <p>This is a computer-generated receipt. No signature is needed.</p>
            </div>
        </div>
        <div class="payment-section">
            <h2>Pending Payment?</h2>
            <p>If your payment is pending, please scan the QR code below to complete the payment:</p>
            <img src="http://demo-drivepulse.eternalbytes.in/assets/QR.jpg" alt="Payment QR Code">
            <p>If you have already paid, please disregard this message.</p>
        </div>
        <div class="footer">
            <p>Best regards,</p>
            <p>' . htmlspecialchars($config_data[0]['config-mail-data']['mail-company-name']) . '</p>
            <p>Contact us: <a href="mailto:info@example.com">DrivePulse@gmail.com</a> | ' . htmlspecialchars($config_data[0]['config-mail-data']['mail-contact-number']) . '</p>
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
</html>

';

        // Send the email
        $mail->send();
        return [
            'status' => 'success',
            'message' => 'Message has been sent'
        ];
    } catch (Exception $e) {
        return [
            'status' => 'error',
            'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"
        ];
    }
}
$configMailPath = "../configMail/configMail.json";
$json_data = file_get_contents($configMailPath);
$config_data = json_decode($json_data, true);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $name = $_POST['name'];
    $pdfbase64 = $_POST['pdfbase64'];

    // echo $email;
    // echo $name;

    $response = sendMail($email, $name, $config_data, $pdfbase64);
    echo json_encode($response);
}
