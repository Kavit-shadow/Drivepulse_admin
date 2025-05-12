<?php

include('../../includes/authentication.php');
authenticationAdmin('../../');

date_default_timezone_set('Asia/Kolkata');
// $conn = mysqli_connect("localhost", "root", "", "billing");
include('../../config.php');

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
    <link rel="stylesheet" href="../../css/adminDashboard.css">
    <link rel="stylesheet" href="../../css/sideBarFooter.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        #content main {

            height: 100vh;
        }

        /* Style for the order container */

        #content main .table-data {
            height: 80%;
        }

        .order {
            width: 100%;
            padding: 10px;
            background-color: #f1f1f1;
            text-align: center;
            border-radius: 4px;

        }

        /* Style for the table title */

        .order .head {
            background-color: #f1f1f1;
            padding: 10px;
        }

        .order h3 {
            margin: 0;
            font-size: 20px;
        }

        /* Style for the table container */
        .order .table-container {
            max-height: 400px;
            overflow-y: auto;
            margin-top: 10px;
        }

        /* Style for the table */
        .order table {
            width: 100%;
            border-collapse: collapse;
        }

        /* Style for table headers and cells */
        .order th,
        .order td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #ccc;
        }

        /* Style for the last row's cells */
        .order tbody tr:last-child td {
            border-bottom: none;
        }

        /* Style for action buttons */
        .view,
        .edit,
        .delete {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
            font-size: 14px;
            padding: 6px 16px;
            color: #fff;
            border-radius: 20px;
            font-weight: 200;
            text-decoration: none;
            transition: background-color 250ms ease;
            margin: 4px;
            cursor: pointer;
        }

        /* Style for the "View" button */
        .view {
            background: var(--blue);
            /* Use your custom color variable */
        }

        /* Hover style for action buttons */
        .view:hover,
        .edit:hover,
        .delete:hover {
            background: #206e88;
            /* Darker color on hover */
        }

        /* Style for the message block */
        .msg {
            text-align: center;
            margin: 10px 0;
            display: block;
            background: #46abcc;
            color: #fff;
            border-radius: 5px;
            font-size: 20px;
            padding: 10px;
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
        }




        /* ---------------------- smaller table css --------------------------- */

        @media (max-width: 650px) {

            #content main .table-data {
                height: 70dvh;
                height: 70vh;
            }

            .order td {
                border: none;
            }

            .order tr {
                border-bottom: 1px solid #ccc;
            }

            .view {
                width: 80%;
            }

            th {
                display: none;
            }

            td {
                text-align: start;
                display: grid;
                grid-template-columns: 15ch auto;
                padding: 0.5rem 1rem;
            }

            td:first-child {
                padding-top: 2rem;
            }

            td:last-child {
                padding-bottom: 2rem;
            }

            td::before {
                content: attr(data-cell) ": ";
                font-weight: 700;
                text-transform: capitalize;
            }

            #content main .table-data .order table td {
                font-size: 15px;
            }

            #action-cell {
                gap: 4rem;
                display: flex;
                align-items: center;

            }

            #action-cell-1,
            #action-cell-2 {
                gap: 2.2rem;
            }


        }

        @media (max-width: 430px) {


            #content main .table-data {
                height: 65dvh;
                height: 65vh;
            }


            #content main .table-data .head {
                min-width: 200px;
            }

            #content main .table-data .order table {
                min-width: 200px;
            }

            #content main .table-data .order table {
                width: 100%;
            }


            #content main .table-data .head h3 {
                margin: 0;
                font-size: 20px;
            }

            #content main .table-data .order table td {
                font-size: 13px;

            }

            #action-cell {
                gap: 4rem;


            }

            #action-cell a {
                width: 25%;
            }
        }

        /* ---------------------- smaller table css --------------------------- */


        /* Modal styling */
        .modal {
            display: none;
            /* Hidden by default */
            position: absolute;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            left: 0;
            top: 0;
            width: 100%;
            height: 100vh;
            overflow: hidden;
            background-color: rgba(0, 0, 0, 0.2);
            /* Black background with opacity */
        }

        /* Modal content */
        .modal-content {
            background-color: #fff;
            margin: 10% auto;
            /* Center the modal */
            padding: 20px;
            border-radius: 10px;
            width: clamp(80%, 80%, 85%);
            height: 75%;
            overflow-y: scroll;
            /* Prevent it from becoming too wide on large screens */
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            /* Add a slight shadow */
        }

  

        /* Close button */
        .close-btn {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close-btn:hover,
        .close-btn:focus {
            color: black;
        }

        /* Responsive styling */
        @media only screen and (max-width: 600px) {
            .modal-content {
                width: 90%;
                /* Full width for smaller devices */
                margin: 20% auto;
                /* Adjust the margin for mobile */
            }
        }

        @media only screen and (min-width: 600px) and (max-width: 1024px) {
            .modal-content {
                width: 70%;
                /* Wider on tablets */
            }
        }

        .allmsgcontent {
            display: flex;
            min-width: 90%;
            height: fit-content;
            flex-direction: column;
            gap: .5rem;
        }

        .send_header {
            display: flex;
            max-width: 90%;
            min-width: 90%;
            height: fit-content;
            padding: 1rem;
            align-items: center;
            justify-content: space-between;
        }

        .btns {
            display: flex;
            gap: .5rem;
            flex-direction: row;
        }

        .btn {
            padding: .7rem;
            border: none;
            background: #46abcc;
            color: #fff;
            border-radius: .4rem;
            font-size: .9rem;
        }

        .btn:hover {
            background: #419bb9;
        }

        .cust_info {
            display: flex;
            gap: .5rem;
            flex-direction: row;
        }

        /* Google Fonts */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap');

        /* *{
	font-family: 'Poppins';
	margin: 0;
	padding: 0;
	box-sizing: border-box;
	scroll-behavior: smooth;
	scroll-padding-top: 2rem;
}

body{
	color: #0e2045;
	background: #fff;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
} */

        .msg_container{
            margin-top: 40px;
        }

        .msg_container .container {
            flex: 1;
            position: relative;
        }

        /* 
        img {
            width: 100%;
        }

        a {
            text-decoration: none;
            color: #0e2045;
        } */

        .container {
            max-width: 1024px;
            margin: auto;
            width: 100%;
        }

        .tools-area .btn {
            background-color: #eaeaea;
            border: none;
            padding: 8px 20px 8px 20px;
            border-radius: 50px;
            cursor: pointer;
        }

        .tools-area .btn:hover {
            box-shadow: 0 5px 30px 0 rgba(0, 0, 0, .05);
            transition: .3s ease;
        }

        .tools-area .btn:active {
            scale: 95%;
        }

        .tools-btn {
            background-color: #fff;
            border: none;
            padding: 8px 20px 8px 20px;
            border-radius: 6px;
            cursor: pointer;
            margin: 2px;
        }

        .tools-btn:hover {
            background-color: #eaeaea;
        }

        .tools-btn:active:hover {
            background-color: #e1e0e0;
        }

        .tools-area {
            background-color: #f8f9fa;
            width: 100%;
            padding: 20px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .txt-area .text {
            width: 100%;
            height: 400px;
            resize: none;
            border-bottom-left-radius: 8px;
            border-bottom-right-radius: 8px;
            border: 3px solid #eaeaea;
            border-top-color: #f8f9fa;
            background-color: #fff;
            outline: none;
            padding: 10px 12px 10px 12px;
        }



        @media (max-width: 1000px) {
            .container {
                margin: 0 auto;
                width: 90%;
            }
        }
        
           .swal2-container {
            z-index: 999999999 !important;
        }

        .swal2-popup {
            z-index: 999999999 !important;
        }

        .swal2-backdrop-show {
            z-index: 999999999 !important;
        }
    
    </style>

    <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />
    <title>Pending Payment</title>
</head>

<body>


    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-car'></i>

            <span class="text">
                <h6><span>Admin</span></h6>
                <h4>Welcome <span>
                        <?php echo $_SESSION['admin_name'] ?>
                    </span></h4>
            </span>
        </a>
        <ul class="side-menu top">
            <li>
                <a href="../">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../admissionForm.php">
                    <i class='bx bxs-book-bookmark'></i>
                    <span class="text">Book Admission</span>
                </a>
            </li>
            <li>
                <a href="../analytics">
                    <i class='bx bxs-bar-chart-alt-2'></i>
                    <span class="text">Analytics</span>
                </a>
            </li>
           
            <li class="search">
                <a href="../search.php">
                    <i class='bx bx-search-alt-2'></i>
                    <span class="text">Search</span>
                </a>
            </li>
            <li>
                <a href="../sortData/">
                    <!-- <i class='bx bxs-bar-chart-alt-2'></i> -->
                    <i class='bx bxs-data'></i>
                    <span class="text">Customers Data</span>
                </a>
            </li>
            <li class="time-table">
                <a href="../timetable/">
                    <i class='bx bx-table'></i>
                    <span class="text">Time Table</span>
                </a>
            </li>
            <li class="active">
                <a href="../dueCustomers/">
                    <i class='bx bxs-user'></i>
                    <span class="text">Pending Payment</span>
                </a>
            </li>
            <li>
                <a href="../mailSender/">
                    <i class='bx bx-mail-send'></i>
                    <span class="text">Mail System</span>
                </a>
            </li>
            <li>
                <a href="../manageUsers/">
                    <i class='bx bxs-group'></i>
                    <span class="text">Manage Users</span>
                </a>
            </li>
            <li>
                <a href="../createVehicle/">
                    <i class='bx bxs-car'></i>
                    <span class="text">Add New Vehicle</span>
                </a>
            </li>
            <li>
                <a href="../employeeManagement/">
                    <i class='bx bxs-id-card'></i>
                    <span class="text">Employee Management</span>
                </a>
            </li>
            <li>
                <a href="../liveTrainings/">
                    <i class='bx bxs-videos'></i>
                    <span class="text">Live Trainings</span>
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
                <a href="../../logout.php" class="logout">
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
                <h3><?php
                    include("../../configWeb.php");
                    echo $WebAppTitle;
                    ?></h3>
                <h5>Dashboard</h5>
            </span>
            <a href="../" class="profile">
                <img src="../../assets/logoBlack.png">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Pending Payment</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./">Pending Payment</a>
                        </li>

                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Customers</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Due Amount</th>
                                <th>Vehicle</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            date_default_timezone_set('Asia/Kolkata');
                            $current_timestamp_by_mktime = mktime(date("m"), date("d"), date("Y"));
                            $currentDate = date("Y-m-d", $current_timestamp_by_mktime);

                            $query = "SELECT * FROM `cust_details` WHERE `dueamount` > 0 ORDER BY `date` ASC";



                            $result = mysqli_query($conn, $query);
                            if (mysqli_num_rows($result) > 0) {

                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr><td data-cell='Name' >" . $row["name"] . "</td>
                                    <td data-cell='Phone' >" . $row["phone"] . "</td>
                                    <td data-cell='Total Amount' >" . $row["totalamount"] . "</td>
                                    <td data-cell='Paid Amount' >" . $row["paidamount"] . "</td>
                                    <td data-cell='Due Amount' >" . $row["dueamount"] . "</td>
                                    <td data-cell='Vehicle' >" . $row["vehicle"] . "</td>
                                    <td data-cell='Action 1' id='action-cell-1' >" . " <a class='view' href='updatePayment.php?id=" . $row["id"] . "&phone=" . $row["phone"] . "#updateBox' > Update Payment</a>" . "</td>
                                    <td data-cell='Action 2' id='action-cell-2' >" . "<a class='view' href='../view?id=" . $row["id"] . "&phone=" . $row["phone"] . "&route=" . urlencode("../dueCustomers") . "'>View Details</a></td>
                                    <td data-cell='Action 1' id='action-cell-1' > <button style='background: #09b309;border: none;padding: .5rem;'type='button'  class='sendMSG view' data-name='" . $row["name"] . "' data-phone='" . $row["phone"] . "' data-email='" . $row["email"] . "' data-pending='" . $row["dueamount"] . "'>Send Payment Reminder</button></td>";
                                }
                            } else {
                                $msg = array("No Pending Payment ");
                            }
                            ?>
                        </tbody>
                    </table>

                    <?php
                    if (isset($msg)) {
                        foreach ($msg as $msg) {
                            echo '<span class="msg">' . $msg . '</span>';
                        };
                    };
                    ?>

                </div>



            </div>

            <div class="modal" id="myModal">
                <div class="modal-content">
                    <span class="close-btn" id="closeModal">&times;</span>
                    <div class="send_header" style="display: flex; flex-direction: column; align-items: flex-start; padding: 10px;">
                        <div class="cust_info" style="margin-bottom: 10px;">
                            <span style="display: block; margin-bottom: 5px;"><b>Phone:</b> <span id="dis_phone"></span></span>
                            <span style="display: block; margin-bottom: 5px;"><b>Email:</b> <span id="dis_email"></span></span>
                            <span style="display:none;"><b>am:</b> <span id="dis_am"></span></span>
                        </div>
                        <div class="btns" style="display: flex; flex-direction: row; gap: 10px;">
                            <button type="button" class="btn" id="send_email" style="flex: 1; white-space: nowrap;">Send email</button>
                            <button type="button" class="btn" id="share_btn" style="flex: 1; white-space: nowrap;">Share</button>
                        </div>
                    </div>

                    <div class="msg_container">
                        <div class="container">
                            <div class="tools-area">
                                <button id="bold" class="tools-btn"><i class="fa-solid fa-bold"></i></button>
                                <button id="italic" class="tools-btn"><i class="fa-solid fa-italic"></i></button>
                                <button id="underline" class="tools-btn"><i class="fa-solid fa-underline"></i></button>
                                <button id="strikethrough" class="tools-btn"><i class="fa-solid fa-strikethrough"></i></button>
                                <button id="justifyRight" class="tools-btn"><i class="fa-solid fa-align-right"></i></button>
                                <button id="justifyLeft" class="tools-btn"><i class="fa-solid fa-align-left"></i></button>
                                <button id="justifyFull" class="tools-btn"><i class="fa-solid fa-align-justify"></i></button>
                                <button id="justifyCenter" class="tools-btn"><i class="fa-solid fa-align-center"></i></button>
                                <button id="unorderedList" class="tools-btn"><i class="fa-solid fa-list-ul"></i></button>
                                <button id="increaseFontSize" class="tools-btn"><i class="fa-solid fa-plus"></i></button>
                                <button id="decreaseFontSize" class="tools-btn"><i class="fa-solid fa-minus"></i></button>
                                <button id="resetBtn" class="tools-btn"><i class="fa-solid fa-rotate-right"></i></button>
                            </div>
                            <div class="txt-area">
                                <div contentEditable class="text" id="text" placeholder="start typing your text to play with..." rows="20"></div>
                                <div id="qr-container" style="text-align: center; margin-top: 10px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        <!-- MAIN -->

    </section>
    <!-- CONTENT -->
    <script src="js/sweetalert.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        const shareBtn = document.getElementById('share_btn');

        shareBtn.addEventListener('click', async () => {
            try {
                // Get DOM elements and values
                const phoneNumber = document.querySelector("#dis_phone").innerText;
                const amount = document.querySelector("#dis_am").innerText;
                const name = document.querySelector("#text").innerText.split("Dear ")[1].split(",")[0];

                const payeeName = "Patel Motor Driving School";
                const upiId = "9725603403-1@okbizaxis";

                // Create UPI URL with parameters
                const upiURL = "upi://pay?" + new URLSearchParams({
                    pn: payeeName,
                    pa: upiId,
                    am: amount,
                    cu: 'INR',
                    tn: 'Payment for service'
                }).toString();

                // Load header logo
                const logoImage = new Image();
                logoImage.crossOrigin = "anonymous";
                logoImage.src = 'https://i.postimg.cc/BQvDYtCZ/PMDS-text-B.png';
                await new Promise((resolve, reject) => {
                    logoImage.onload = resolve;
                    logoImage.onerror = () => reject(new Error('Failed to load logo image'));
                });

                // Fetch QR code with error handling
                const qrCodeURL = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" + encodeURIComponent(upiURL);

                const response = await fetch(qrCodeURL);
                if (!response.ok) {
                    throw new Error('Failed to fetch QR code image.');
                }

                // Create and load QR image
                const qrBlob = await response.blob();
                const qrImage = new Image();
                qrImage.src = URL.createObjectURL(qrBlob);

                await new Promise((resolve, reject) => {
                    qrImage.onload = resolve;
                    qrImage.onerror = () => reject(new Error('Failed to load QR code image'));
                });

                // Create canvas with proper pixel ratio
                const pixelRatio = window.devicePixelRatio || 1;
                const canvas = document.createElement('canvas');
                const displayWidth = 350;
                const displayHeight = 650;

                canvas.width = displayWidth * pixelRatio;
                canvas.height = displayHeight * pixelRatio;
                canvas.style.width = `${displayWidth}px`;
                canvas.style.height = `${displayHeight}px`;

                const ctx = canvas.getContext('2d');
                ctx.scale(pixelRatio, pixelRatio);

                // Ensure clean canvas state
                ctx.clearRect(0, 0, displayWidth, displayHeight);

                // Draw background with gradient
                const gradient = ctx.createLinearGradient(0, 0, 0, displayHeight);
                gradient.addColorStop(0, '#ffffff');
                gradient.addColorStop(1, '#f0f0f0');
                ctx.fillStyle = gradient;
                ctx.fillRect(0, 0, displayWidth, displayHeight);

                // Add subtle border
                ctx.strokeStyle = '#e0e0e0';
                ctx.lineWidth = 2;
                ctx.strokeRect(5, 5, displayWidth - 10, displayHeight - 10);

                // Draw header logo
                const logoWidth = 300;
                const logoHeight = (logoWidth * logoImage.height) / logoImage.width;
                const logoY = 20;
                ctx.drawImage(
                    logoImage,
                    (displayWidth - logoWidth) / 2,
                    logoY,
                    logoWidth,
                    logoHeight
                );

                // Draw customer details with shadow
                const contentStartY = logoY + logoHeight + 30;
                ctx.shadowColor = 'rgba(0, 0, 0, 0.1)';
                ctx.shadowBlur = 2;
                ctx.font = '16px Arial, sans-serif';
                ctx.fillStyle = '#333333';
                ctx.textAlign = 'center';
                ctx.fillText(`Customer Name: ${name}`, displayWidth / 2, contentStartY);
                ctx.fillText(`Phone Number: ${phoneNumber}`, displayWidth / 2, contentStartY + 30);
                ctx.shadowBlur = 0;

                // Draw QR code with white background
                const qrY = contentStartY + 50;
                ctx.fillStyle = '#ffffff';
                ctx.fillRect((displayWidth - 310) / 2, qrY, 310, 310);
                ctx.drawImage(qrImage, (displayWidth - 300) / 2, qrY + 5, 300, 300);

                // Draw payment details
                const detailsY = qrY + 320;
                ctx.font = '16px Arial, sans-serif';
                ctx.fillStyle = '#333333';
                ctx.fillText(`UPI ID: ${upiId}`, displayWidth / 2, detailsY + 20);

                // Draw amount with highlight
                ctx.font = 'bold 16px Arial, sans-serif';
                const amountText = `Pending Payment: ₹${amount}`;
                ctx.fillStyle = '#007BFF';
                ctx.fillText(amountText, displayWidth / 2, detailsY + 50);

                // Draw underline
                const textWidth = ctx.measureText(amountText).width;
                ctx.beginPath();
                ctx.moveTo(displayWidth / 2 - textWidth / 2, detailsY + 53);
                ctx.lineTo(displayWidth / 2 + textWidth / 2, detailsY + 53);
                ctx.strokeStyle = '#007BFF';
                ctx.lineWidth = 1;
                ctx.stroke();

                // Draw payee name and instructions
                ctx.fillStyle = '#333333';
                ctx.fillText(`To: ${payeeName}`, displayWidth / 2, detailsY + 80);
                ctx.font = '16px Arial, sans-serif';
                ctx.fillStyle = '#007BFF';
                ctx.fillText('Scan the QR code to complete your payment', displayWidth / 2, detailsY + 110);

                // Function to try different sharing methods
                const tryShare = async (mimeType, quality) => {
                    try {
                        const blob = await new Promise((resolve) => canvas.toBlob(resolve, mimeType, quality));
                        const file = new File([blob], `payment-reminder.${mimeType.split('/')[1]}`, {
                            type: mimeType
                        });

                        const shareData = {
                            text: `Dear ${name},\n\nThis is a payment reminder of Rs. ${amount}.\n\nRegards,\nPatel Motor Driving School`,
                            files: [file],
                            title: 'Payment Reminder'
                        };

                        if (navigator.canShare && navigator.canShare(shareData)) {
                            await navigator.share(shareData);
                            return true;
                        }
                        return false;
                    } catch (error) {
                        console.error('Share attempt failed:', error);
                        return false;
                    }
                };

                // Try sharing with different methods
                if (!(await tryShare('image/png', 1.0)) &&
                    !(await tryShare('image/jpeg', 0.9))) {

                    // If file sharing fails, try downloading
                    try {
                        const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/png', 1.0));
                        const url = URL.createObjectURL(blob);
                        const link = document.createElement('a');
                        link.href = url;
                        link.download = 'payment-reminder.png';
                        document.body.appendChild(link);
                        link.click();
                        document.body.removeChild(link);
                        URL.revokeObjectURL(url);

                        // Also copy text to clipboard
                        const fallbackText = `Dear ${name},\n\nThis is a payment reminder of Rs. ${amount}.\n\nRegards,\nPatel Motor Driving School`;
                        await navigator.clipboard.writeText(fallbackText);
                        alert('Image downloaded and text copied to clipboard. You can now share it manually.');
                    } catch (downloadError) {
                        console.error('Download failed:', downloadError);
                        throw new Error('Failed to download image');
                    }
                }

            } catch (err) {
                console.error('Error:', err);

                // Ultimate fallback - just copy text
                try {
                    const fallbackText = `Dear ${name},\n\nThis is a payment reminder of Rs. ${amount}.\n\nRegards,\nPatel Motor Driving School`;
                    await navigator.clipboard.writeText(fallbackText);
                    alert('Sharing failed. Text copied to clipboard. You can share it manually.');
                } catch (clipboardErr) {
                    alert('Sharing failed. Please try taking a screenshot or copying the details manually.');
                    console.error('Clipboard error:', clipboardErr);
                }
            } finally {
                // Cleanup any remaining object URLs
                if (qrImage) {
                    URL.revokeObjectURL(qrImage.src);
                }
            }
        });
    </script>



    </script>
    <script>
        const boldButton = document.getElementById("bold");
        const underlineButton = document.getElementById("underline");
        const italicButton = document.getElementById("italic");
        const strikethroughButton = document.getElementById("strikethrough");
        const justifyRightButton = document.getElementById("justifyRight");
        const justifyLeftButton = document.getElementById("justifyLeft");
        const justifyCenterButton = document.getElementById("justifyCenter");
        const justifyFullButton = document.getElementById("justifyFull");
        const unorderedListButton = document.getElementById("unorderedList");
        const increaseFontSizeButton = document.getElementById("increaseFontSize");
        const decreaseFontSizeButton = document.getElementById("decreaseFontSize");
        const copyButton = document.getElementById("copyBtn");
        const resetButton = document.getElementById("resetBtn");








        const modal = document.getElementById('myModal');
        const btns = document.querySelectorAll('.sendMSG');
        const closeBtn = document.getElementById('closeModal');
        const sendEmailBtn = document.getElementById('send_email');
        const text = document.getElementById('text');

        btns.forEach(btn => {
            btn.addEventListener('click', () => {
                modal.style.display = 'block';

                const phoneNumber = btn.dataset.phone;
                const email = btn.dataset.email;
                const name = btn.dataset.name;
                const pending = btn.dataset.pending;

                document.querySelector("#dis_phone").innerText = phoneNumber;
                document.querySelector("#dis_email").innerText = email;
                document.querySelector("#dis_am").innerText = pending;


                text.innerHTML = `
                <div style="text-align: left;">
                    <span style="font-family: var(--poppins);">Dear <b>${name}</b>,</span>
                </div>
                <div style="text-align: left;"><br></div>
                <div style="text-align: left;">
                    <span style="font-family: var(--poppins);">This is a reminder that your payment is still pending. Please arrange for payment at your earliest convenience.</span>
                </div>
                <div style="text-align: left;"><br></div>
                <div style="text-align: left;">
                    <span style="font-family: var(--poppins);"><u>Pending Payment: <b>₹${pending}</b></u></span>
                </div>
                <div style="text-align: left;"><br></div>
                <div style="text-align: left;">
                    <span style="font-family: var(--poppins);">For your convenience, you can pay using the QR code below.</span>
                </div>
            `;
            });
        });

        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        window.addEventListener('click', (event) => {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });

        sendEmailBtn.addEventListener('click', (e) => {
            e.preventDefault(); // Prevent default form submission

            // Get data from the modal
            const email = document.querySelector("#dis_email").innerText;
            const pending = document.querySelector("#text").innerText; // Adjust based on your content
            const emailContent = text.innerHTML;
            const amount = parseInt(document.querySelector("#dis_am").innerText);

            console.log(emailContent);

            // Confirm sending email
            Swal.fire({
                title: 'Confirm Send',
                text: `Are you sure you want to send the email to ${email}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, send it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('body').css('cursor', 'wait'); // Show loading cursor

                    $.ajax({
                        url: '../../api_ajax/send_payment_email.php',
                        type: 'POST',
                        data: {
                            email: email,
                            content: emailContent,
                            pending: pending,
                            amount: amount
                        },
                        dataType: 'json', // Expect JSON response
                        success: function(response) {
                            $('body').css('cursor', 'auto'); // Restore cursor

                            console.log('Response received:', response); // Log the raw response

                            Swal.fire({
                                title: response.status === 'success' ? 'Success' : 'Error',
                                text: response.message,
                                icon: response.status === 'success' ? 'success' : 'error',
                                confirmButtonText: 'OK'
                            });
                        },
                        error: function(xhr, status, error) {
                            $('body').css('cursor', 'auto');
                            console.error('AJAX Error:', status, error);
                            Swal.fire({
                                title: 'success',
                                text: "Message has been sent",
                                icon: 'success',
                                confirmButtonText: 'OK'
                            });
                        }
                    });
                }
            });
        });




        boldButton.addEventListener("click", function() {
            document.execCommand("bold", false, null);
        });

        italicButton.addEventListener("click", function() {
            document.execCommand("italic", false, null);
        });

        underlineButton.addEventListener("click", function() {
            document.execCommand("underline", false, null);
        });

        strikethroughButton.addEventListener("click", function() {
            document.execCommand("strikethrough", false, null);
        });

        justifyRightButton.addEventListener("click", function() {
            document.execCommand("justifyRight", false, null);
        });

        justifyLeftButton.addEventListener("click", function() {
            document.execCommand("justifyLeft", false, null);
        });

        justifyCenterButton.addEventListener("click", function() {
            document.execCommand("justifyCenter", false, null);
        });

        justifyFullButton.addEventListener("click", function() {
            document.execCommand("justifyFull", false, null);
        });

        unorderedListButton.addEventListener("click", function() {
            document.execCommand("insertUnorderedList", false, null);
        });

        increaseFontSizeButton.addEventListener("click", function() {
            const currentSize = parseInt(getComputedStyle(text).fontSize, 10);
            text.style.fontSize = (currentSize + 1) + "px";
        });

        decreaseFontSizeButton.addEventListener("click", function() {
            const currentSize = parseInt(getComputedStyle(text).fontSize, 10);
            text.style.fontSize = (currentSize - 1) + "px";
        });

        copyButton.addEventListener("click", function() {
            const allText = text.innerText;
            if (allText) {
                navigator.clipboard.writeText(allText);
            }
        });

        resetButton.addEventListener("click", function() {
            text.innerHTML = "";
        });

        function sendWhatapp() {
            console.log("asdas");

            const message = text.innerText;
            const encodedMessage = encodeURIComponent(message);

            const whatsappLink = `https://wa.me/${btn.dataset.phone}?text=${encodedMessage}`;

            window.open(whatsappLink, '_blank');
        }
    </script>
    <script src="../../js/toggleSideBar.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/hideSideBar.js"></script>




</body>

</html>