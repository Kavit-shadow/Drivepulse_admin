<?php

$configMailPath = "./configMail/configMail.json";
$json_data = file_get_contents($configMailPath);
$config_data = json_decode($json_data, true);



use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require 'vendor/autoload.php';
function sendMail($id, $email, $name, $VN, $timeslot, $config_data)
{

    $mail = new PHPMailer(true);
    $mailFileName = $name;
    $mailFileName .= "'s booking reciept";

    // Get customer details from database using phone number
    include("./config.php");
    $sql = "SELECT * FROM cust_details WHERE phone='$id'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_assoc($result);

    $cust_uid = $row['cust_uid'];

    try {

        $mail->SMTPDebug = SMTP::DEBUG_SERVER;
        $mail->isSMTP();
        // $mail->Host = 'smtp.gmail.com';
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = $config_data[0]['config-mail']['email'];
        $mail->Password = $config_data[0]['config-mail']['password'];
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;


        $mail->setFrom($config_data[0]['config-mail']['email'], $config_data[0]['config-mail-data']['mail-company-name']);
        $mail->addAddress($email, $name);
        // $mail->addAddress('ellen@example.com');               //Name is optional
        // $mail->addReplyTo('info@example.com', 'Information');
        // $mail->addCC('cc@example.com');
        // $mail->addBCC('bcc@example.com');


        //Attachments
        // $mail->addAttachment('/var/tmp/file.tar.gz');        
        $mail->addAttachment('addmission_pdf/test.pdf', $mailFileName); //Optional name

        //Content
        $mail->isHTML(true); //Set email format to HTML
        $mail->Subject = $config_data[0]['config-mail-data']['mail-subject'];


        $Mailparagraphs = "";

        if (isset($config_data[0]['config-mail-data']['mail-paragraph']) && !empty($config_data[0]['config-mail-data']['mail-paragraph'])) {

            foreach ($config_data[0]['config-mail-data']['mail-paragraph'] as $index => $paragraph) {
                $Mailparagraphs .= $paragraph;
            }
        }
        include('configWeb.php');
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
    </style>
</head>
<body>
    <div class="container">
             ' . $mailDevMsg . '
        <div class="header">
            <img src="'.htmlspecialchars($config_data[0]['config-mail-data']['mail-header-logo']).'" alt="Header Image">
        </div>
        <div class="section">
            <h1>' . htmlspecialchars($config_data[0]['config-mail-data']['mail-heading']) . '</h1>
            <p>' . htmlspecialchars($config_data[0]['config-mail-data']['mail-greetings']) . ' <b>' . htmlspecialchars($name) . '</p>

            '.$Mailparagraphs .'
             <div class="customer-id">
                <p>Your Customer ID: <strong>'.htmlspecialchars($cust_uid).'</strong></p>
                <p style="font-size: 0.9em; color: #666;">Please keep this ID for future reference and attendance tracking.</p>
            </div>
           
           
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
            <p>Contact us: <a href="mailto:info@example.com">patelmotordrivingschool1985@gmail.com</a> | ' . htmlspecialchars($config_data[0]['config-mail-data']['mail-contact-number']) . '</p>
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
        // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
        $mail->SMTPDebug = 0;
        $mail->send();
        // echo 'Message has been sent';
    } catch (Exception $e) {
        return $e->getMessage();
        // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

require_once('lib/dompdf/autoload.inc.php');

require "mailGeneratedPDF.php";

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('chroot', realpath(''));
$pdf = new Dompdf($options);


ob_start();

include('previewPDF.php');

$htmlCode = ob_get_clean();


$pdf->loadHtml($htmlCode);

$pdf->setPaper('A4', 'portrait');

$pdf->render();

// // Save the PDF to the "ADMISSION_PDF" folder
// $savePath = 'addmission_pdf/test.pdf';
// file_put_contents($savePath, $pdf->output());

// Output the PDF for preview
// header('Content-Type: application/pdf');
// header('Content-Disposition: inline; filename="test.pdf"');
// echo $pdf->output();

$pdfDataUri = 'data:application/pdf;base64,' . base64_encode($pdf->output());



mailReceipt($phone, $email, $name);
$error[] = sendMail($phone, $email, $name, $VN, $timeslot, $config_data);
error_reporting(0);

// Suppress PHP warnings and notices
ini_set('display_errors', 0);
// include('./includes/authenticationAdminOrStaff.php');
// authenticationAdminOrStaff();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/3db79b918b.js" crossorigin="anonymous"></script>

    <!-- My CSS -->

    <link rel="stylesheet" href="css/adminDashboard.css">
    <link rel="stylesheet" href="css/sideBarFooter.css">
    <style>
        /* Style the container */
        .container {
            margin-top: 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            height: auto;
            width: 100%;
        }

        /* Style the chart container */
        .chart-container {
            width: 100%;
            max-width: auto;
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;

        }

        .barchart {
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: start;
            align-items: start;
            gap: 30px;
            flex-wrap: wrap;
        }

        .piechart {
            padding: 20px;
            display: flex;
            flex-direction: row;
            justify-content: start;
            align-items: start;
            flex-wrap: wrap;
        }

        /* Style the button container */
        .barchart .button-container {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            width: 100%;

        }

        /* Style the button */
        button {
            padding: 10px 20px;
            background-color: #46abcc;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1397c2;
        }

        /* Style the select element */
        select[name="select-year"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 200px;
            /* Adjust the width as needed */
        }

        /* Style the selected option */
        select[name="select-year"] option[selected] {
            background-color: #3498db;
            color: #fff;
        }

        /* Style the options when the select is open */
        select[name="select-year"]:focus {
            outline: none;
            /* Remove the default focus outline */
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.7);
        }

        /* Style the options in the dropdown */
        select[name="select-year"] option {
            padding: 5px;
            font-size: 14px;
        }

        /* Style the hover effect on options */
        select[name="select-year"] option:hover {
            background-color: #f2f2f2;
            cursor: pointer;
        }

        /* Style the apply button */
        input[type="submit"][name="apply-button"] {
            padding: 10px 20px;
            /* Adjust padding as needed */
            background-color: #46abcc;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        /* Style the button on hover */
        input[type="submit"][name="apply-button"]:hover {
            background-color: #1397c2;
        }

        @media (max-width: 768px) {



            #sidebar {
                width: 200px;
            }

            .side-menu .text {

                font-size: 12px;
                font-weight: 700;

            }

            #content {
                width: calc(100% - 60px);
                left: 200px;
            }

            #content nav .nav-link {
                display: none;
            }

            #sidebar .brand .text h4 {
                font-size: 17px;
            }

            #content main .head-title .left .breadcrumb {
                font-size: 13px;
            }

            .view {

                font-size: 11px;

            }

            #content main .table-data .order table td {
                font-size: 10px;
            }


        }


        .error-msg {
            margin: 10px 0px;
            display: block;
            background: red;
            color: #fff;
            border-radius: 5px;
            font-size: 20px;
            padding: 10px;
            text-align: center;
        }
    </style>
        <style>
        .whatsapp-form {
            max-width: 600px;
            width: 100%;
            margin: 2rem auto;
            padding: 1.5rem;
            background: #fff;
            border-radius: 0.8rem;

        }

        .whatsapp-form .form-group {
            margin-bottom: 1.2rem;
        }

        .whatsapp-form input[type="tel"],
        .whatsapp-form textarea,
        .whatsapp-form select {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid #ddd;
            border-radius: 0.5rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .whatsapp-form input[type="tel"]:focus,
        .whatsapp-form textarea:focus,
        .whatsapp-form select:focus {
            border-color: #25D366;
            box-shadow: 0 0 0 2px rgba(37, 211, 102, 0.2);
            outline: none;
        }

        .whatsapp-form textarea {
            min-height: 120px;
            resize: vertical;
        }

        .whatsapp-form small {
            display: block;
            margin-top: 0.4rem;
            color: #666;
            font-size: 0.8rem;
        }

        .whatsapp-form button {
            width: 100%;
            padding: 0.8rem;
            background-color: #25D366;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .whatsapp-form button:hover {
            background-color: #128C7E;
        }

        .whatsapp-form select {
            width: auto;
            min-width: 80px;
            background-color: #fff;
        }

        /* Responsive Design */
        @media screen and (max-width: 768px) {
            .whatsapp-form {
                margin: 1rem;
                padding: 1rem;
            }

            .whatsapp-form input[type="tel"],
            .whatsapp-form textarea,
            .whatsapp-form select {
                font-size: 0.9rem;
                padding: 0.7rem;
            }

            .whatsapp-form button {
                padding: 0.7rem;
                font-size: 0.9rem;
            }
        }

        @media screen and (max-width: 480px) {
            .whatsapp-form {
                margin: 0.5rem;
                padding: 0.8rem;
            }

            .whatsapp-form .form-group {
                margin-bottom: 0.8rem;
            }

            .whatsapp-form small {
                font-size: 0.75rem;
            }
        }

        /* Modal styling */
        dialog {
            padding: 0;
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.15);
            max-width: 95%;
            width: 1200px;
            margin: 20px auto;
            position: fixed;
            left: 50%;
            top: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Poppins', sans-serif;
            max-height: 90vh;
            overflow-y: auto;
            -webkit-overflow-scrolling: touch;
            /* Smooth scrolling on iOS */
        }

        dialog::backdrop {
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(3px);
        }

        .modal-content {
            position: relative;
            padding: 24px 16px;
            background: #fff;
            border-radius: 12px;
        }

        .close {
            position: absolute;
            top: 12px;
            right: 12px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: all 0.2s;
            padding: 8px;
            border-radius: 50%;
            z-index: 10;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            line-height: 1;
        }

        .close:hover {
            color: #333;
            background: #f1f5f9;
        }
    </style>


    <!-- Add SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" type="image/png" href="assets/logo.png" />
    <title>Generated PDF</title>
</head>

<body>


    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-car'></i>

            <span class="text">
                <h6><span>
                        <?php echo ucfirst((isset($_GET['who'])) ? $_GET['who'] : ""); ?>
                    </span></h6>
                <h4>Welcome <span>
                        <?php echo (string) ($_GET['who'] == "admin") ? $_SESSION['admin_name'] : (($_GET['who'] == "staff") ? $_SESSION['staff_name'] : "error"); ?>
                    </span></h4>
            </span>
        </a>
        <ul class="side-menu top">
            <li class="active">
                <a href="<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/admissionForm.php">
                    <i class='bx bxs-book-bookmark'></i>
                    <span class="text">Book Admission</span>
                </a>
            </li>
            <li style="<?php echo (isset($_GET['who'])) ? (($_GET['who'] == "staff") ? "display:none;" : "") : (($_GET['who'] == "staff") ? "" : ""); ?>">
                <a href="<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/analytics">
                    <i class='bx bxs-bar-chart-alt-2'></i>
                    <span class="text">Analytics</span>
                </a>
            </li>
            <li class="search">
                <a href="<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/search.php">
                    <i class='bx bx-search-alt-2'></i>
                    <span class="text">Search</span>
                </a>
            </li>
            <li>
                <a href="<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/sortData/">
                    <!-- <i class='bx bxs-bar-chart-alt-2'></i> -->
                    <i class='bx bxs-data'></i>
                    <span class="text">Customers Data</span>
                </a>
            </li>
            <li class="time-table">
                <a href="<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/timetable/">
                    <i class='bx bx-table'></i>
                    <span class="text">Time Table</span>
                </a>
            </li>
            <li>
                <a href="<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/dueCustomers/">
                    <i class='bx bxs-user'></i>
                    <span class="text">Pending Payment</span>
                </a>
            </li>
            <li style="<?php echo (isset($_GET['who'])) ? (($_GET['who'] == "staff") ? "display:none;" : "") : (($_GET['who'] == "staff") ? "" : ""); ?>">
                <a href="<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/mailSender">
                    <i class='bx bx-mail-send'></i>
                    <span class="text">Mail System</span>
                </a>
            </li>
            <li style="<?php echo (isset($_GET['who'])) ? (($_GET['who'] == "staff") ? "display:none;" : "") : (($_GET['who'] == "staff") ? "" : ""); ?>">
                <a href="<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/manageUsers/">
                    <i class='bx bxs-group'></i>
                    <span class="text">Manage Users</span>
                </a>
            </li>
            <li style="<?php echo (isset($_GET['who'])) ? (($_GET['who'] == "staff") ? "display:none;" : "") : (($_GET['who'] == "staff") ? "" : ""); ?>">
                <a href="<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/createVehicle/">
                    <i class='bx bxs-car'></i>
                    <span class="text">Add New Vehicle</span>
                </a>
            </li>
            <li style="<?php echo (isset($_GET['who'])) ? (($_GET['who'] == "staff") ? "display:none;" : "") : (($_GET['who'] == "staff") ? "" : ""); ?>">
                <a href="<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/employeeManagement/">
                    <i class='bx bxs-id-card'></i>
                    <span class="text">Employee Management</span>
                </a>
            </li>


        </ul>
        <ul class="side-menu">
            <!-- <li>
                <a href="#">
                    <i class='bx bxs-cog'></i>
                    <span class="text">Settings</span>
                </a>
            </li> -->
            <li>
                <a href="logout.php" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>

        
  
        </div>
    </section>
    <!-- SIDEBAR -->



    <!-- CONTENT -->
    <section id="content">
        <!-- NAVBAR -->
        <nav>
            <i class='bx bx-menu'></i>
            <!-- <a href="#" class="nav-link">Categories</a> -->
            <!-- <form action="" method="get">
                <div class="form-input">
                    <input type="search" name="search-query" placeholder="Search...">
                    <button type="submit" class="search-btn"><i class='bx bx-search'></i></button>
                </div>
            </form> -->
            <!-- <input type="checkbox" id="switch-mode" hidden>
            <label for="switch-mode" class="switch-mode"></label>
            <a href="#" class="notification">
                <i class='bx bxs-bell'></i>
                <span class="num">8</span>
            </a> -->
            <span class="text">
                <h3>Patel Motor Driving School</h3>
                <h5>Dashboard</h5>
            </span>
            <a href="" class="profile">
                <img src="assets/logoBlack.png">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>

            <?php
            if (isset($error)) {
                foreach ($error as $error) {
                    echo '<span class="error-msg" id="error-msg" >' . $error . '</span>';
                }
            }
            ?>
            <div class="head-title">
                <div class="left">
                    <h1>Generated PDF Viewer</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="">Generated PDF</a>
                        </li>
                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>


            <button type="button" class="whatsapp-btn" id="openWhatsAppBtn" style="
                background-color: #25D366;
                color: white;
                border: none;
                padding: 9px 11px;
                font-size: 15px;
                font-weight: 500;
                cursor: pointer;
                display: flex;
                align-items: center;
                gap: 8px;
                transition: background-color 0.3s ease;
                margin-top: 35px;
            "><i class='bx bxl-whatsapp'></i>Share via WhatsApp</button>

<dialog id="whatsappDialog">
                <div class="modal-content">
                    <button class="close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; border: none; background: none;">&times;</button>
                    <h2 style="margin-top: 0;">Share via WhatsApp</h2>

                    <div class="modal-table-container">
                        <form method="POST" id="whatsappForm" class="whatsapp-form" enctype="multipart/form-data">
                            <div class="form-group">
                                <div style="display: flex; gap: 10px;">
                                    <select id="countryCode" style="width: 80px;">
                                        <option value="+91" selected>+91</option>
                                        <option value="+1">+1</option>
                                        <option value="+44">+44</option>
                                        <option value="+81">+81</option>
                                        <option value="+86">+86</option>
                                    </select>
                                    <input type="tel" name="phone" id="phoneInput" value="<?php echo $_GET['id']; ?>" placeholder="Enter phone number" required>
                                </div>
                                <small>Format: Phone Number without country code (e.g., 9876543210)</small>
                            </div>
                            <div class="form-group">
                                <textarea name="message" id="messageInput" placeholder="Type your message here..." readonly>*Dear <?php echo $_GET['name']; ?>*,
<?php
include('./config.php');
$id = $_GET['id'];
$sql = "SELECT * FROM cust_details WHERE phone = '$id'";
$result = mysqli_query($conn, $sql);
$row = mysqli_fetch_assoc($result);


if($row) {
    $string = $row['vehicle'];
    $parts = explode("/", $string);

    // Split first part into vehicle type and name
    $firstPart = trim($parts[0]);
    $words = explode(" ", $firstPart);

    // Find position of "Wheeler" to separate vehicle type from name
    $wheelerPos = array_search('Wheeler', $words);

    if ($wheelerPos !== false) {
      // Join words up to and including "Wheeler" for var1
      $var1 = implode(" ", array_slice($words, 0, $wheelerPos + 1));
      // Join remaining words for var2
      $var2 = implode(" ", array_slice($words, $wheelerPos + 1));
    } else {
      // Fallback if "Wheeler" not found
      $var1 = $words[0];
      $var2 = implode(" ", array_slice($words, 1));
    }

    $firstPart = $var1; // Will contain "Four Wheeler"
    $VN = $var2; // Will contain "Toyota Liva"
?>


*Customer ID (Use this ID for attendance):* ```<?php echo $row['cust_uid']; ?>```

*Training Details:*

- Vehicle: <?php echo $firstPart . " " . $VN; ?>

- Time Slot: <?php echo $row['timeslot']; ?>

- Trainer Name: <?php echo $row['trainername']; }?>


Thank you for choosing our driving school! 🚗

If you have any questions, feel free to contact us. 📞

Please remember to use your Customer ID for attendance tracking.

We look forward to helping you master your driving skills! 🎯

*Drive Pulse*</textarea>
                            </div>
                            <div class="dialog-buttons">
                                <button type="submit" data-clicked="false">Open WhatsApp number</button>
                            </div>
                        </form>
                    </div>
                </div>
            </dialog>

            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script>
                $(document).ready(function() {
                    // Open modal when button is clicked
                    $('#openWhatsAppBtn').click(function() {
                        // Check if WhatsApp is installed
                        if (!navigator.userAgent.match(/WhatsApp/i)) {
                            Swal.fire({
                                title: 'Open WhatsApp First',
                                text: 'Please open WhatsApp on your device before proceeding.',
                                icon: 'warning',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // // Open WhatsApp in background
                                    // const whatsappWindow = window.open(`whatsapp://`, '_blank');
                                    // if (whatsappWindow) {
                                    //     whatsappWindow.blur();
                                    //     window.focus();
                                    // }
                                    document.getElementById('whatsappDialog').showModal();
                                }
                            });
                        } else {
                            document.getElementById('whatsappDialog').showModal();
                        }
                    });

                    // Close modal when clicking close button or outside
                    $('.close, #whatsappDialog').click(function(event) {
                        if (event.target == document.getElementById('whatsappDialog') || $(event.target).hasClass('close')) {
                            document.getElementById('whatsappDialog').close();
                        }
                    });
                });
            </script>

            <iframe src="<?php echo $pdfDataUri; ?>" style="width: 100%;height: 1200px;margin-top: 35px;border: none;border-radius: 10px;" title="Generated PDF" allowfullscreen loading="lazy"></iframe>

            <script>
                document.getElementById('whatsappForm').addEventListener('submit', async function(e) {
                    e.preventDefault();

                    const countryCode = document.getElementById('countryCode').value;
                    const message = document.getElementById('messageInput').value;
                    const phone = countryCode + document.getElementById('phoneInput').value.replace(/[\D\s]/g, '');
                    const pdfDataUri = '<?php echo $pdfDataUri; ?>';

                    if (message.trim() !== '' && phone.trim() !== '') {
                        const encodedMessage = encodeURIComponent(message);

                        try {
                            // First click - only send message
                            if (!this.hasAttribute('data-clicked')) {
                                // Set attribute to track first click
                                this.setAttribute('data-clicked', 'true');
                                
                                // Update button text
                                document.querySelector('#whatsappForm button[type="submit"]').textContent = 'Send Message';
                                
                                // Send WhatsApp message
                                window.location.href = `whatsapp://send?phone=${phone}&text=${encodedMessage}`;
                                
                                return;
                            }

                            // Second click - download PDF and show instructions
                            const response = await fetch(pdfDataUri);
                            const blob = await response.blob();

                            // Create download link
                            const a = document.createElement('a');
                            a.href = window.URL.createObjectURL(blob);
                            a.download = '<?php echo $_GET['name']; ?>-<?php echo $_GET['id']; ?>-Booking-receipt.pdf';

                            // Trigger download

                            document.body.appendChild(a);
                            a.click();
                            document.body.removeChild(a);
                            window.location.href = `whatsapp://send?phone=${phone}&text=${encodedMessage}`;


                            // Show instructions for PDF
                            Swal.fire({
                                title: 'PDF Downloaded',
                                text: 'The PDF has been saved to your Downloads folder. Please open WhatsApp and select the PDF from your Downloads folder when sharing the document.',
                                icon: 'success',
                                confirmButtonText: 'OK'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // // Create a new tab for document sharing
                                    // const shareUrl = `whatsapp://document?phone=${phone}`;
                                    // window.open(shareUrl, '_blank');
                                    
                                    // Reset click state for next time
                                    this.removeAttribute('data-clicked');
                                }
                            });

                        } catch (err) {
                            Swal.fire({
                                title: 'Error',
                                text: 'Error processing the PDF file. Please try again.',
                                icon: 'error',
                                confirmButtonText: 'OK'
                            });
                            console.error(err);
                        }
                    } else {
                        Swal.fire({
                            title: 'Missing Information',
                            text: 'Please enter both phone number and message',
                            icon: 'warning',
                            confirmButtonText: 'OK'
                        });
                    }
                });
            </script>


        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script>
        window.addEventListener('DOMContentLoaded', function() {
            var errorMessage = document.getElementById('error-msg');

            if (errorMessage.textContent.trim() === '') {
                errorMessage.style.display = 'none';
            }
        });
    </script>
    <script src="js/sweetalert.js"></script>
    <script src="js/toggleSideBar.js"></script>
    <script src="js/script.js"></script>
    <script src="js/hideSideBar.js"></script>

</body>

</html>