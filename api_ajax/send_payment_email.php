<?php




use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Decode Base64 PDF and create a temporary file
function createTempFileFromBase64($base64String, $fileName) {
    // Decode Base64 string
    $pdfData = base64_decode($base64String);

    // Create a temporary file
    $tempFilePath = tempnam(sys_get_temp_dir(), 'pdf');
    file_put_contents($tempFilePath, $pdfData);

    return $tempFilePath;
}

function sendMail( $email, $emailContent, $am, $config_data)
{
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);
    function generateUPIQRCode($payeeName, $upiId, $amount = '', $note = '') {
        // Construct UPI URL
        $upiURL = "upi://pay?";
        $params = array(
            'pn' => urlencode($payeeName),
            'pa' => $upiId,
            'cu' => 'INR'
        );
    
        if (!empty($amount)) {
            $params['am'] = $amount;
        }
        if (!empty($note)) {
            $params['tn'] = urlencode($note);
        }
    
        $upiURL .= http_build_query($params);
    
        // Use QR code API that's more reliable than Google Charts
        $qrCodeAPI = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($upiURL);
    
        return $qrCodeAPI;
    }

    function generateUPILink($payeeName, $upiId, $amount = '', $note = '') {
        // Construct UPI URL
        $upiURL = "upi://pay?";
        $params = array(
            'pn' => urlencode($payeeName),
            'pa' => $upiId,
            'cu' => 'INR'
        );
    
        if (!empty($amount)) {
            $params['am'] = $amount;
        }
        if (!empty($note)) {
            $params['tn'] = urlencode($note);
        }
    
        $upiURL .= http_build_query($params);
        
        return $upiURL;
    }
    
    $payeeName = "Patel Motor Driving School";
    $upiId = "9725603403-1@okbizaxis"; 
    $amount = (int)$am;
    $note = "Payment for service";
    
    $qrCodeURL = generateUPIQRCode($payeeName, $upiId, $amount, $note);
    $upiLink = generateUPILink($payeeName, $upiId, $amount, $note);


    try {
        // Server settings
        $mail->SMTPDebug = 0; // Set to SMTP::DEBUG_SERVER for debugging
        $mail->isSMTP();
        // $mail->Host = 'smtp.gmail.com';
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = $config_data[0]['config-mail']['email']; // Your SMTP username
        $mail->Password = $config_data[0]['config-mail']['password']; // Your SMTP password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465; // TCP port to connect to

        // Recipients
        $mail->setFrom($config_data[0]['config-mail']['email'], $config_data[0]['config-mail-data']['mail-company-name']);
        $mail->addAddress($email);


        // $tempFilePath = createTempFileFromBase64($pdfBase64, 'attachment.pdf');

        // // Attach the file
        // $mail->addAttachment($tempFilePath, $mailFileName);

        

    

        // Content
        $mail->isHTML(true); // Set email format to HTML
        $mail->Subject = "Reminder: Payment Pending";

        // Compose the email body
        $Mailparagraphs = '';
        if (isset($config_data[0]['config-mail-data']['mail-paragraph']) && !empty($config_data[0]['config-mail-data']['mail-paragraph'])) {
            foreach ($config_data[0]['config-mail-data']['mail-paragraph'] as $paragraph) {
                $Mailparagraphs .= "<section>". $paragraph ."</section>";
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
        
                .payment-container {
            text-align: center;
            margin: 20px auto;
            background-color: #4285f4;
            padding: 30px;
            border-radius: 12px;
            max-width: 400px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .payment-inner {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
        }

        .payment-logo {
            height: 40px;
            margin-bottom: 10px;
        }

        .payment-title {
            color: #202124;
            font-family: "Google Sans", Arial, sans-serif;
            margin: 10px 0;
            font-size: 20px;
        }

        .payment-phone {
            color: #5f6368;
            margin: 5px 0;
            font-size: 14px;
        }

        .payment-qr {
            max-width: 230px;
            margin: 15px 0;
        }

        .payment-upi {
            color: #5f6368;
            font-size: 14px;
            margin-bottom: 15px;
        }
        .payment-methods {
            border-top: 1px solid #e8eaed;
            padding-top: 15px;
            margin-top: 15px;
            text-align: center;
        }

        .payment-icons {
            height: 35px;
            width: 35px;
            margin: 3px;
            display: inline-block;
            vertical-align: middle;
            border-radius: 5px;
        }
        .payment-icons:nth-child(1) {
            height: 40px;
            width: 40px;
            object-fit: contain;
        }
        .payment-icons:nth-child(4) {
            height: 45px;
            width: 45px;
            object-fit: contain;
        }
    </style>
</head>
<body>
    <div class="container">
             ' . $mailDevMsg . '
        <div class="header">
            <img src="'.htmlspecialchars($config_data[0]['config-mail-data']['mail-header-logo']).'" alt="Header Image">
        </div>
        <div class="section">
            <h1>Payment Pending</h1>
            '.$emailContent.'
        </div>
        <div class="payment-section">
            <p>If your payment is pending, please scan the QR code below to complete the payment:</p>
             <div class="payment-container" style="overflow: hidden;">
                <div class="payment-inner">
                    <h2 class="payment-title">Patel Motor Driving School</h2>
                    <p class="payment-phone">+91 97256 03403</p>
                    <img src="'.$qrCodeURL.'" alt="UPI Payment QR Code" class="payment-qr" style="max-width: 100%; height: auto;">
                    <p class="payment-upi">9725603403-1@okbizaxis</p>
                    <div class="payment-methods">
                        <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-gpay.png" alt="Google Pay" class="payment-icons">
                        <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-phonepe.png" class="payment-icons">
                        <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-paytm.png" alt="Paytm" class="payment-icons">
                        <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-bhim.png" alt="BHIM UPI" class="payment-icons">
                        <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-upi.png" alt="UPI" class="payment-icons">
                    </div>
                </div>
            </div>
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
</html>

';

        // Send the email
        $mail->send();

        $response = array();

        $response = [
            'status' => 'success',
            'message' => 'Message has been sent'
        ];
    } catch (Exception $e) {
        // If there's an error sending the email
        $response = [
            'status' => 'error',
            'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"
        ];
    }

    echo json_encode($response);
}
$configMailPath = "../configMail/configMail.json";
$json_data = file_get_contents($configMailPath);
$config_data = json_decode($json_data, true);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = $_POST['email'];
    $emailContent = $_POST['content'];
    $am = $_POST['amount'];

    // echo $email;
    // echo $name;

   sendMail($email, $emailContent, $am, $config_data);
   
}



?>
