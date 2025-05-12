<?php

include('../../includes/authentication.php');
authenticationAdmin('../../');
date_default_timezone_set('Asia/Kolkata');




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
    <style>
        .profile-container {
            max-width: 900px;
            width: 95%;
            margin: 20px auto;
            background-color: #f9f9f9;
            border-radius: 7px;
            padding: 15px;
            display: flex;
            flex-direction: row;

        }

        .profile-image {
            flex: 0 0 100px;
            text-align: center;
            margin-right: 15px;
        }

        .profile-image img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 20px;

        }

        .profile-details {
            flex: 1;
            min-width: 0;
        }

        .profile-details p {
            margin: 8px 0;
            padding: 5px 0;
            word-wrap: break-word;
        }

        .profile-details label {
            font-weight: 600;
            color: #333;
            display: inline-block;
            min-width: 100px;
            margin-right: 10px;
        }

        .profile-buttons {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 150px;
        }

        .profile-buttons a {
            padding: 12px;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            width: 100%;
        }

        .profile-buttons .pdf-btn {
            background-color: #2196F3;
            color: #fff;
        }

        .profile-buttons .edit-btn {
            background-color: #4CAF50;
            color: #fff;
        }

        .profile-buttons .close-btn {
            background-color: #f44336;
            color: #fff;
        }

        .profile-buttons a:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        @media screen and (max-width: 600px) {
            .profile-container {
                flex-direction: column;
                align-items: center;
                padding: 12px;
            }

            .profile-image {
                margin: 0 0 15px 0;
            }

            .profile-details {
                width: 100%;
            }

            .profile-details p {
                font-size: 14px;
            }

            .profile-details label {
                min-width: 90px;
                font-size: 14px;
            }

            .profile-buttons {
                justify-content: center;
            }

            .profile-buttons a {
                padding: 7px 14px;
                font-size: 13px;
            }
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

            #sidebar .brand .text h6 {
                font-size: 15px;
            }

            #content main .head-title .left .breadcrumb {
                font-size: 13px;
            }
        }
    </style>

    <!-- Add this custom CSS right after the first <style> tag -->
    <style>
        /* Modern Profile Container Styles */
        .modern-profile-container {
            max-width: 1200px;
            width: 95%;
            margin: 24px auto;
            background-color: #f9f9f9;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            padding: 24px;
        }

        .modern-profile-grid {
            display: grid;
            grid-template-columns: auto 1fr auto;
            gap: 32px;
            align-items: start;
        }

        .modern-profile-image {
            width: 120px;
            aspect-ratio: 1;
            border-radius: 12px;
            overflow: hidden;
            /* box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1); */
        }

        .modern-profile-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .modern-profile-details {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 16px;
            align-content: start;
        }

        .modern-profile-field {
            background-color: #f8fafc;
            padding: 12px 16px;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .modern-profile-field:hover {
            background-color: #f1f5f9;
            transform: translateY(-1px);
        }

        .modern-profile-field label {
            display: block;
            font-size: 0.875rem;
            font-weight: 500;
            color: #64748b;
            margin-bottom: 4px;
        }

        .modern-profile-field span {
            display: block;
            font-size: 1rem;
            color: #1e293b;
            font-weight: 500;
            word-break: break-word;
        }

        .modern-profile-field a {
            color: #2563eb;
            text-decoration: none;
        }

        .modern-profile-field a:hover {
            text-decoration: underline;
        }

        .modern-profile-actions {
            display: flex;
            flex-direction: column;
            gap: 12px;
            min-width: 200px;
        }

        .modern-profile-actions > * {
            width: 100%;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.2s ease;
            border: none;
            cursor: pointer;
            text-decoration: none;
        }

        .modern-btn-primary {
            background-color: #2563eb;
            color: white;
        }

        .modern-btn-primary:hover {
            background-color: #1d4ed8;
        }

        .modern-btn-success {
            background-color: #059669;
            color: white;
        }

        .modern-btn-success:hover {
            background-color: #047857;
        }

        .modern-btn-danger {
            background-color: #dc2626;
            color: white;
        }

        .modern-btn-danger:hover {
            background-color: #b91c1c;
        }

        .modern-btn-purple {
            background-color: #7c3aed;
            color: white;
        }

        .modern-btn-purple:hover {
            background-color: #6d28d9;
        }

        .modern-btn-neutral {
            background-color: #475569;
            color: white;
        }

        .modern-btn-neutral:hover {
            background-color: #334155;
        }

        @media (max-width: 1024px) {
            .modern-profile-grid {
                grid-template-columns: auto 1fr;
            }

            .modern-profile-actions {
                grid-column: 1 / -1;
                flex-direction: row;
                flex-wrap: wrap;
            }

            .modern-profile-actions > * {
                flex: 1;
                min-width: 160px;
            }
        }

        @media (max-width: 768px) {
            .modern-profile-container {
                padding: 16px;
                margin: 16px auto;
            }

            .modern-profile-grid {
                grid-template-columns: 1fr;
                gap: 24px;
            }

            .modern-profile-image {
                width: 100px;
                margin: 0 auto;
            }

            .modern-profile-details {
                grid-template-columns: 1fr;
            }

            .modern-profile-field {
                padding: 10px 14px;
            }
        }

        @media (max-width: 480px) {
            .modern-profile-container {
                width: 98%;
                padding: 12px;
                margin: 12px auto;
            }

            .modern-profile-actions {
                flex-direction: column;
            }

            .modern-profile-actions > * {
                width: 100%;
            }
        }
    </style>

<style>
        /* Modal styling */
        dialog {
            padding: 0;
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 30px rgba(0, 0, 0, 0.15);
            max-width: 95%;
            width: 1500px;
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
        }

        .close:hover {
            color: #333;
            background: #f1f5f9;
        }

        .modal-table-container {
            display: flex;
            gap: 24px;
            margin-top: 20px;
            flex-direction: column;
        }

        .employee-images {
            width: 100%;
            max-width: 400px;
            margin: 0 auto;
        }

        .employee-images h4 {
            margin: 0 0 10px 0;
            color: #333;
            font-size: 1rem;
            font-weight: 600;
            text-align: center;
        }

        .employee-images img:not(#empuid-logo) {
            width: 100%;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            margin-bottom: 20px;
            max-height: 300px;
            object-fit: contain;
        }

        .employee-images button {
            width: 100%;
            padding: 12px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
        }

        .employee-info {
            background: #f8fafc;
            padding: 20px 16px;
            border-radius: 8px;
            display: grid;
            grid-template-columns: 1fr;
            gap: 16px;
        }

        .info-item strong {
            color: #334155;
            margin-right: 8px;
            font-size: 0.9rem;
            display: block;
            margin-bottom: 4px;
        }

        .info-item span {
            color: #64748b;
            font-size: 0.95rem;
            word-break: break-word;
        }

        /* Tablet breakpoint */
        @media (min-width: 768px) {
            .modal-content {
                padding: 28px;
            }

            .employee-info {
                grid-template-columns: repeat(2, 1fr);
                padding: 24px;
            }
        }

        /* Desktop breakpoint */
        @media (min-width: 1024px) {
            .modal-table-container {
                flex-direction: row;
            }

            .employee-images {
                flex: 0 0 300px;
            }

            .employee-info {
                flex: 1;
            }
        }

        /* Software Access Status Styles */
        .access-granted,
        .access-denied,
        .access-error {
            padding: 12px;
            border-radius: 6px;
            margin: 8px 0;
            font-size: 0.9rem;
        }

        .access-granted {
            background-color: #e8f5e9;
            border: 1px solid #4caf50;
            color: #2e7d32;
        }

        .access-denied {
            background-color: #ffebee;
            border: 1px solid #ef5350;
            color: #c62828;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 10px;
        }

        .access-error {
            background-color: #fff3e0;
            border: 1px solid #ff9800;
            color: #e65100;
        }

        .access-granted i,
        .access-denied i,
        .access-error i {
            margin-right: 8px;
            font-size: 1.1em;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 20px;
            height: 20px;
        }

        .access-details {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid rgba(76, 175, 80, 0.3);
            font-size: 0.85rem;
        }

        .create-access-btn {
            padding: 8px 12px;
            border-radius: 4px;
            border: none;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
            transition: background-color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.85rem;
            white-space: nowrap;
        }

        .error-message {
            margin-top: 6px;
            font-size: 0.85rem;
        }

        .access-status {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {

            .restore-btn,
            .view-btn {
                padding: 6px 10px !important;
                font-size: 12px !important;
                display: block !important;
                width: 100% !important;
                margin: 5px 0 !important;
            }

            .restore-btn i,
            .view-btn i {
                font-size: 14px !important;
            }

            td[data-cell='Action'] {
                display: flex !important;
                flex-direction: column !important;
                gap: 5px !important;
                padding: 10px 5px !important;
            }

            table td {
                padding: 10px 5px !important;
                font-size: 13px !important;
            }

            table th {
                padding: 10px 5px !important;
                font-size: 14px !important;
            }

            .table-data .order table {
                min-width: 500px !important;
            }

            .table-data {
                overflow-x: auto !important;
            }
        }

        @media (max-width: 480px) {
            .table-data .order table {
                min-width: 400px !important;
            }

            table td {
                font-size: 12px !important;
            }

            table th {
                font-size: 13px !important;
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

    <!-- Add SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />
    <title>View Details</title>
</head>

<body id="root">


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
            <li class="active">
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
            <li>
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
                    <h1>View Details</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./?id=<?php echo $_GET['id']; ?>&route=<?php echo $_GET['route']; ?>">View Details</a>
                        </li>
                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>



            <?php
            // $connect = mysqli_connect("localhost", "root", "", "billing");
            include('../../config.php');
            $connect = $conn;
            $backRoute = '';
            if (isset($_GET['id'])) {

                try {
                    $backRoute = $_GET['route'];
                } catch (Exception $e) {
                    $backRoute = "../";
                }
                $id = $_GET['id'];
                $query = "SELECT * FROM `cust_details` WHERE id = '$id'";
                $result = mysqli_query($connect, $query);
                $row = mysqli_fetch_assoc($result);
            ?>
                <div class="modern-profile-container">
                    <div class="modern-profile-grid">
                        <div class="modern-profile-image">
                        <?php
                                if (isset($row['cust_uid'])) {
                                    $pfp_path_header = '../../storage/uploads/customer_documents/' . $row['cust_uid'] . '/pfp.png';
                                    if (file_exists($pfp_path_header)) {
                                        echo '<img src="' . $pfp_path_header . '?v=' . time() . '" alt="Profile" >';
                                    } else {
                                        echo '<img src="../../assets/Default_Profile.png" alt="Profile Image">';
                                    }
                                } else {
                                    echo '<img src="../../assets/Default_Profile.png" alt="Profile Image">';
                                }
                                ?>
                           
                        </div>
                        
                        <div class="modern-profile-details">
                            <div class="modern-profile-field">
                                <label>Customer UID</label>
                                <span><?php echo $row["cust_uid"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Name</label>
                                <span><?php echo $row["name"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Email</label>
                                <span><?php echo $row["email"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Phone</label>
                                <span><a href="tel:<?php echo $row["phone"]; ?>"><?php echo $row["phone"]; ?></a></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Address</label>
                                <span><?php echo $row["address"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Payment Method</label>
                                <span><?php echo $row["payment_method"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Total Amount</label>
                                <span><?php echo $row["totalamount"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Paid Amount</label>
                                <span><?php echo $row["paidamount"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Due Amount</label>
                                <span><?php echo $row["dueamount"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Days</label>
                                <span><?php echo $row["days"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Time-Slot</label>
                                <span><?php echo $row["timeslot"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Vehicle</label>
                                <span><?php echo $row["vehicle"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>New Licence</label>
                                <span><?php echo $row["newlicence"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Trainer Name</label>
                                <span><?php echo $row["trainername"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Trainer Phone</label>
                                <span><?php echo $row["trainerphone"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Admission Date</label>
                                <span><?php echo $row["date"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Admission Time</label>
                                <span><?php echo $row["time"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Training Started On</label>
                                <span><?php echo $row["startedAT"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Training Ended On</label>
                                <span><?php echo $row["endedAT"]; ?></span>
                            </div>
                            <div class="modern-profile-field">
                                <label>Form Filler</label>
                                <span><?php echo $row["formfiller"]; ?></span>
                            </div>
                        </div>

                        <div class="modern-profile-actions">
                            <a href="<?php echo "../../viewGeneratedPDF.php?id=" . $row['phone'] . "&email=" . $row['email'] . "&name=" . $row['name'] .  "&route=" . urlencode("admin/view?id=" . $row['id'] . "&phone=" . $row['phone'] . "&route=$backRoute") . "&who=admin"; ?>" class="modern-btn-primary" id="pdfBtn">
                                <i class='bx bxs-file-pdf'></i>
                                Booking Receipt PDF
                            </a>
                            <a href="<?php echo "../editUserData?id=" . $row['phone'] . "&email=" . $row['email'] . "&name=" . $row['name'] . "&route=" . urlencode("../view/?id=" . $row['id'] . "&phone=" . $row['phone'] . "&route=" . $backRoute); ?>" class="modern-btn-success">
                                <i class='bx bxs-edit'></i>
                                Edit
                            </a>
                            <button class="modern-btn-danger force-attendance-btn" data-id="<?php echo $row['id']; ?>" data-cust-uid="<?php echo $row['cust_uid']; ?>" data-acc-id="<?php echo $_SESSION['admin_ID']; ?>" data-acc-name="<?php echo $_SESSION['admin_name']; ?>">
                                <i class='bx bx-time'></i>
                                Force Attendance
                            </button>
                            <button class="modern-btn-primary attendance-btn" data-id="<?php echo $row['id']; ?>" data-cust-uid="<?php echo $row['cust_uid']; ?>">
                                <i class='bx bx-calendar'></i>
                                View Attendance
                            </button>
                            <button class="modern-btn-purple qr-code-btn" data-cust-uid="<?php echo $row['cust_uid']; ?>" data-email="<?php echo $row['email']; ?>" data-cust-name="<?php echo $row['name']; ?>" data-cust-phone="<?php echo $row['phone']; ?>" data-user-id="<?php echo $row['id']; ?>">
                                <i class='bx bx-qr'></i>
                                Generate Login QR
                            </button>
                            <button class="modern-btn-success documents-btn" data-cust-uid="<?php echo $row['cust_uid']; ?>">
                                <i class='bx bx-folder-open'></i>
                                View Documents
                            </button>
                            <a href="<?php echo $backRoute; ?>" class="modern-btn-neutral">
                                <i class='bx bx-x'></i>
                                Close
                            </a>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>

            <dialog id="attendanceModal">
                <div class="modal-content" style="padding: 20px;">
                    <button class="close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; border: none; background: none;">&times;</button>
                    <h2 style="margin-top: 0;">Attendance Details</h2>
                    <div style="margin-top: 20px; display: flex; flex-wrap: wrap; gap: 10px; justify-content: flex-end;">
                        <button id="downloadPDF" class="btn-download" style="background: #46abcc; color: #fff; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 14px; transition: background 0.2s; flex: 1 1 auto; justify-content: center; min-width: 140px; max-width: 200px;">
                            <i class='bx bxs-file-pdf'></i>
                            Download PDF
                        </button>
                        <button id="downloadImage" class="btn-download" style="background: #46abcc; color: #fff; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 14px; transition: background 0.2s; flex: 1 1 auto; justify-content: center; min-width: 140px; max-width: 200px;">
                            <i class='bx bxs-image'></i>
                            Download Image
                        </button>
                        <button id="printTable" class="btn-download" style="background: #46abcc; color: #fff; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; display: flex; align-items: center; gap: 6px; font-size: 14px; transition: background 0.2s; flex: 1 1 auto; justify-content: center; min-width: 140px; max-width: 200px;">
                            <i class='bx bx-printer'></i>
                            Print
                        </button>
                    </div>
                    <div class="modal-table-container">
                        <div id="attendanceContent">
                            <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
                                <thead>
                                    <tr>
                                        <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Sr No.</th>
                                        <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Customer UID</th>
                                        <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Customer Name</th>
                                        <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Date</th>
                                        <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Time In</th>
                                        <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Time Out</th>
                                        <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Attendance Time</th>
                                        <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Vehicle Name</th>
                                        <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Trainer Name</th>
                                        <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Employee UID</th>
                                        <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Total Time</th>
                                        <th style="padding: 12px; border: 1px solid #ddd; background: #f4f4f4;">Note</th>
                                    </tr>
                                </thead>
                                <tbody id="attendanceTableBody">
                             
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </dialog>

            <!-- Add QR Code Dialog -->
            <dialog id="qrCodeModal">
                <div class="modal-content" style="padding: 20px; text-align: center;">
                    <button class="close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; border: none; background: none;">&times;</button>
                    <h2 style="margin-top: 0;">Customer Login QR Code</h2>
                    <div id="qrCodeContainer" style="margin: 20px 0;">
                        <!-- QR code will be inserted here -->
                    </div>
                    <p style="margin-bottom: 20px;">Scan this QR code to login to your account</p>
                </div>
            </dialog>

            <!-- Documents Modal -->
            <dialog id="documentsModal">
                <div class="modal-content" style="padding: 20px; max-width: 1200px;">
                    <button class="close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; border: none; background: none;">&times;</button>
                    <h2 style="margin-top: 0;">Customer Documents</h2>
                    <div style="margin-top: 20px;">
                        <div id="documentsContainer" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                            <!-- Documents will be loaded here -->
                            <div class="loading-indicator" style="grid-column: 1/-1; text-align: center; padding: 40px;">
                                <i class='bx bx-loader-alt bx-spin' style="font-size: 48px; color: #3498db;"></i>
                                <p style="margin-top: 16px; color: #666;">Loading documents...</p>
                            </div>
                        </div>
                        <div id="noDocumentsMessage" style="display: none; text-align: center; padding: 40px; background: #f8f9fa; border-radius: 8px; margin-top: 20px;">
                            <i class='bx bx-file' style="font-size: 48px; color: #aaa;"></i>
                            <p style="margin-top: 16px; color: #666;">No documents found for this customer.</p>
                        </div>
                    </div>
                </div>
            </dialog>

        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
    <script>
        document.getElementById('pdfBtn').onclick = function() {
            let root = document.getElementById('root');
            root.setAttribute("style", "cursor: wait");
        }
    </script>

    <!-- Add html2canvas and jsPDF libraries -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Download as PDF
        document.getElementById('downloadPDF').addEventListener('click', function() {
            const {
                jsPDF
            } = window.jspdf;
            const content = document.getElementById('attendanceContent');

            // Get current date for filename
            const today = new Date();
            const date = today.getFullYear() + '-' + (today.getMonth() + 1) + '-' + today.getDate();

            // Set options to capture full content
            const options = {
                scale: 2,
                useCORS: true,
                scrollX: 0,
                scrollY: 0,
                width: content.offsetWidth,
                height: content.offsetHeight
            };

            html2canvas(content, options).then(canvas => {
                const imgData = canvas.toDataURL('image/png');
                const pdf = new jsPDF({
                    orientation: 'portrait',
                    unit: 'mm',
                    format: 'a4'
                });
                const pageWidth = pdf.internal.pageSize.getWidth();
                const pageHeight = pdf.internal.pageSize.getHeight();

                // Calculate image dimensions while maintaining aspect ratio
                const imgWidth = pageWidth - 20;
                const imgHeight = (canvas.height * imgWidth) / canvas.width;

                // Add title
                pdf.setFontSize(16);
                pdf.text('Attendance Report', pageWidth / 2, 15, {
                    align: 'center'
                });

                // Add date
                pdf.setFontSize(12);
                pdf.text(`Generated on: ${date}`, pageWidth - 10, 25, {
                    align: 'right'
                });

                // Add table image
                const xPos = 10;
                const yPos = 35;
                pdf.addImage(imgData, 'PNG', xPos, yPos, imgWidth, imgHeight);

                // Add new pages if content overflows
                if (yPos + imgHeight > pageHeight) {
                    let heightLeft = imgHeight;
                    let position = -pageHeight + yPos;

                    while (heightLeft > 0) {
                        pdf.addPage();
                        pdf.addImage(imgData, 'PNG', xPos, position, imgWidth, imgHeight);
                        heightLeft -= (pageHeight - yPos);
                        position -= (pageHeight - yPos);
                    }
                }

                pdf.save(`attendance_report_${date}.pdf`);
            });
        });

        // Download as Image
        document.getElementById('downloadImage').addEventListener('click', function() {
            const content = document.getElementById('attendanceContent');
            html2canvas(content).then(canvas => {
                const link = document.createElement('a');
                link.download = 'attendance_report.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        });

        // Print table
        document.getElementById('printTable').addEventListener('click', function() {
            const content = document.getElementById('attendanceContent');
            const printContent = content.innerHTML;

            const iframe = document.createElement('iframe');
            iframe.style.display = 'none';
            document.body.appendChild(iframe);

            const doc = iframe.contentWindow.document;
            doc.write('<html><head><title>Attendance Report</title>');
            doc.write('<style>table{width:100%;border-collapse:collapse;margin-top:20px}th,td{padding:10px;border:1px solid #ddd}th{background-color:#f4f4f4}</style>');
            doc.write('</head><body>');
            doc.write(printContent);
            doc.write('</body></html>');
            doc.close();

            iframe.contentWindow.focus();
            iframe.contentWindow.print();

            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 1000);
        });

        // Show attendance modal when button is clicked
        document.addEventListener('DOMContentLoaded', function() {
            // Modal open/close handlers
            const attendanceBtn = document.querySelector('.attendance-btn');
            const attendanceModal = document.getElementById('attendanceModal');
            const closeBtn = document.querySelector('#attendanceModal .close');

            if (attendanceBtn) {
                attendanceBtn.addEventListener('click', function() {
                    attendanceModal.showModal();
                    loadAttendanceData();
                });
            }

            if (closeBtn) {
                closeBtn.addEventListener('click', function() {
                    attendanceModal.close();
                });
            }

            if (attendanceModal) {
                attendanceModal.addEventListener('click', function(event) {
                    if (event.target === this) {
                        this.close();
                    }
                });
            }

            // Function to load attendance data
            function loadAttendanceData() {
                const urlParams = new URLSearchParams(window.location.search);
                const id = urlParams.get('id');


                if (!id) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No ID provided',
                        confirmButtonColor: '#3085d6'
                    });
                    return;
                }

                $.ajax({
                    url: '../../api_ajax/get_attendance.php',
                    method: 'POST',
                    data: {
                        id: id
                    },
                    success: function(response) {
                        try {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;

                            if (!Array.isArray(data)) {
                                throw new Error('Invalid data format');
                            }

                            // Sort data by date in ascending order (oldest first)
                            data.sort((a, b) => {
                                const dateA = new Date(a.date);
                                const dateB = new Date(b.date);
                                return dateA - dateB;
                            });

                            const tbody = document.getElementById('attendanceTableBody');
                            if (!tbody) {
                                throw new Error('Table body element not found');
                            }

                            tbody.innerHTML = '';

                            // Get all dates between first and last attendance
                            const allDates = [];
                            if (data.length > 0) {
                                const startDate = new Date(data[0].date);
                                const endDate = new Date(data[data.length - 1].date);

                                for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                                    allDates.push(new Date(d));
                                }
                            }

                            // Add summary row
                            const summaryRow = document.createElement('tr');
                            const presentCount = data.length;
                            const absentCount = allDates.length - presentCount;
                            const attendancePercentage = allDates.length > 0 
                                ? ((presentCount / allDates.length) * 100).toFixed(1) 
                                : 0;

                            summaryRow.innerHTML = `
                                <td colspan="12" style="padding: 16px; border: 1px solid #ddd; background-color: #f8f9fa;">
                                    <div style="display: flex; justify-content: space-around; align-items: center;">
                                        <div style="text-align: center;">
                                            <div style="font-weight: bold; font-size: 1.1em; color: #28a745;">Present</div>
                                            <div style="font-size: 1.2em;">${presentCount} days</div>
                                        </div>
                                        <div style="text-align: center;">
                                            <div style="font-weight: bold; font-size: 1.1em; color: #dc3545;">Absent</div>
                                            <div style="font-size: 1.2em;">${absentCount} days</div>
                                        </div>
                                        <div style="text-align: center;">
                                            <div style="font-weight: bold; font-size: 1.1em; color: #007bff;">Attendance Rate</div>
                                            <div style="font-size: 1.2em;">${attendancePercentage}%</div>
                                        </div>
                                    </div>
                                </td>
                            `;
                            tbody.appendChild(summaryRow);


                            let dataIndex = 0;
                            // Fill up to 20 rows
                            for (let i = 0; i < Math.min(20, allDates.length || 20); i++) {
                                const tr = document.createElement('tr');

                                if (allDates.length > 0) {
                                    // Check if there's attendance data for this date
                                    const currentDate = allDates[i].toISOString().split('T')[0];
                                    const attendanceData = data[dataIndex];

                                    if (attendanceData && attendanceData.date === currentDate) {
                                        // Data exists for this date
                                        tr.innerHTML = `
                                            <td style="padding: 12px; border: 1px solid #ddd;">${i + 1}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.cust_uid || '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.customer_name || '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.date || '')} (${allDates[i].getDay() === 1 ? 'Mon' : allDates[i].getDay() === 2 ? 'Tue' : allDates[i].getDay() === 3 ? 'Wed' : allDates[i].getDay() === 4 ? 'Thu' : allDates[i].getDay() === 5 ? 'Fri' : allDates[i].getDay() === 6 ? 'Sat' : 'Sun'})</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.time_in ? new Date(attendanceData.time_in).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) : '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.time_out ? new Date(attendanceData.time_out).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) : '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.attendance_time ? new Date(attendanceData.attendance_time).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) : '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.vehicle_name || '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.trainer_name || '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.employee_uid || '')}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${
                                                attendanceData.time_in && attendanceData.time_out ? 
                                                (() => {
                                                    const timeIn = new Date(attendanceData.time_in);
                                                    const timeOut = new Date(attendanceData.time_out);
                                                    const diff = timeOut - timeIn;
                                                    const hours = Math.floor(diff / (1000 * 60 * 60));
                                                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                                                    return `${hours}h ${minutes}m`;
                                                })() : '-'
                                            }</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">${escapeHtml(attendanceData.note || '')}</td>
                                        `;
                                        dataIndex++;
                                    } else {
                                        // No attendance on this date
                                        tr.innerHTML = `
                                            <td style="padding: 12px; border: 1px solid #ddd;">${i + 1}</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd; background-color: #ffe6e6;">${currentDate} (${allDates[i].getDay() === 1 ? 'Mon' : allDates[i].getDay() === 2 ? 'Tue' : allDates[i].getDay() === 3 ? 'Wed' : allDates[i].getDay() === 4 ? 'Thu' : allDates[i].getDay() === 5 ? 'Fri' : allDates[i].getDay() === 6 ? 'Sat' : 'Sun'}) (Absent)</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">-</td>
                                            <td style="padding: 12px; border: 1px solid #ddd;">Absent</td>
                                        `;
                                    }
                                } else {
                                    // Empty row when no attendance data exists
                                    tr.innerHTML = `
                                        <td style="padding: 12px; border: 1px solid #ddd;">${i + 1}</td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                        <td style="padding: 12px; border: 1px solid #ddd;"> </td>
                                    `;
                                }
                                tbody.appendChild(tr);
                            }
                        } catch (err) {
                            console.error('Error processing data:', err);
                            showError('Error processing attendance data');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        showError('Failed to load attendance data');
                    }
                });

            }

            // Helper function to escape HTML and prevent XSS
            function escapeHtml(unsafe) {
                if (unsafe == null) return '';
                return unsafe
                    .toString()
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // Helper function to show error messages
            function showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message,
                    confirmButtonColor: '#3085d6'
                });
            }
        });
    </script>

    <script>
        // Handle force attendance button click
        document.querySelectorAll('.force-attendance-btn').forEach(button => {
            button.addEventListener('click', function() {
                const custUid = this.dataset.custUid;
                const accId = this.dataset.accId;
                const id = this.dataset.id;
                const accName = this.dataset.accName;

                Swal.fire({
                    title: 'Force Attendance',
                    text: 'Are you sure you want to mark attendance for this customer?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, mark attendance'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Make AJAX call to mark attendance
                        $.ajax({
                            url: '../../api_ajax/forceAttendance.php',
                            type: 'POST',
                            data: {
                                cust_uid: custUid,
                                acc_id: accId,
                                cust_id: id,
                                acc_name: accName
                            },
                            success: function(response) {
                                try {
                                    const data = JSON.parse(response);
                                    if (data.success) {
                                        Swal.fire(
                                            'Success!',
                                            'Attendance has been marked.',
                                            'success'
                                        );
                                    } else {
                                        Swal.fire(
                                            'Error!',
                                            data.message || 'Failed to mark attendance',
                                            'error'
                                        );
                                    }
                                } catch (e) {
                                    Swal.fire(
                                        'Error!',
                                        'Invalid server response',
                                        'error'
                                    );
                                }
                            },
                            error: function() {
                                Swal.fire(
                                    'Error!',
                                    'Failed to communicate with server',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });
        });
    </script>

    <script>
        // Add this to your existing JavaScript code
        document.addEventListener('DOMContentLoaded', function() {
            // QR Code Modal functionality
            const qrCodeBtn = document.querySelector('.qr-code-btn');
            const qrCodeModal = document.getElementById('qrCodeModal');
            const qrCloseBtn = qrCodeModal.querySelector('.close');

            if (qrCodeBtn) {
                qrCodeBtn.addEventListener('click', function() {
                  /*  const baseUrl = 'http://app.patelmotordrivingschool.com/user/auth/login';
                    const params = new URLSearchParams({
                        user_id: this.dataset.userId,
                        cust_uid: this.dataset.custUid,
                        email: this.dataset.email,
                        name: this.dataset.custName,
                        phone: this.dataset.custPhone
                    });
                    
                    const loginUrl = `${baseUrl}?${params.toString()}`;
                
                    console.log(loginUrl);*/
                    const customerUID = this.dataset.custUid; // Get only the cust_uid
                    console.log("Data for QR:", customerUID); // Log the UID
                    const qrCodeUrl = `../../api_ajax/generate_qr.php?data=${encodeURIComponent(customerUID)}&size=10&margin=2`;
                    
                    const qrCodeContainer = document.getElementById('qrCodeContainer');
                    qrCodeContainer.innerHTML = `
                        <img src="${qrCodeUrl}" alt="Login QR Code" style="max-width: 300px; height: auto; margin: 20px auto; display: block; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                        <p style="margin: 15px 0; font-size: 14px; color: #666; text-align: center;">
                            Scan this QR code to open the login page with your details pre-filled
                        </p>
                        <div style="margin-top: 10px; text-align: center;">
                           
                        </div>
                    `;
                    
                    qrCodeModal.showModal();
                });
            }

            if (qrCloseBtn) {
                qrCloseBtn.addEventListener('click', function() {
                    qrCodeModal.close();
                });
            }

            // Close modal when clicking outside
            qrCodeModal.addEventListener('click', function(event) {
                if (event.target === this) {
                    this.close();
                }
            });
        });

        // Function to download QR code
        function downloadQRCode(qrCodeUrl) {
            const link = document.createElement('a');
            link.href = qrCodeUrl;
            link.download = 'login-qr-code.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>

    <script>
        // Documents Modal functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Documents Modal functionality
            const documentsBtn = document.querySelector('.documents-btn');
            const documentsModal = document.getElementById('documentsModal');
            const documentsCloseBtn = documentsModal.querySelector('.close');
            const documentsContainer = document.getElementById('documentsContainer');
            const noDocumentsMessage = document.getElementById('noDocumentsMessage');

            if (documentsBtn) {
                documentsBtn.addEventListener('click', function() {
                    const custUid = this.dataset.custUid;
                    documentsModal.showModal();
                    loadCustomerDocuments(custUid);
                });
            }

            if (documentsCloseBtn) {
                documentsCloseBtn.addEventListener('click', function() {
                    documentsModal.close();
                });
            }

            // Close modal when clicking outside
            documentsModal.addEventListener('click', function(event) {
                if (event.target === this) {
                    this.close();
                }
            });

            // Function to load customer documents
            function loadCustomerDocuments(custUid) {
                const documentsPath = `../../storage/uploads/customer_documents/${custUid}/`;
                
                // Show loading indicator
                documentsContainer.innerHTML = `
                    <div class="loading-indicator" style="grid-column: 1/-1; text-align: center; padding: 40px;">
                        <i class='bx bx-loader-alt bx-spin' style="font-size: 48px; color: #3498db;"></i>
                        <p style="margin-top: 16px; color: #666;">Loading documents...</p>
                    </div>
                `;
                
                // Make AJAX call to scan directory
                $.ajax({
                    url: '../../api_ajax/scan_customer_documents.php',
                    type: 'POST',
                    data: {
                        cust_uid: custUid
                    },
                    dataType: 'json',
                    xhrFields: {
                        withCredentials: true
                    },
                    success: function(data) {
                        console.log('Success response:', data);
                        
                        if (data.success && data.files && data.files.length > 0) {
                            // Display documents
                            documentsContainer.innerHTML = '';
                            noDocumentsMessage.style.display = 'none';
                            
                            data.files.forEach(file => {
                                const fileExtension = file.name.split('.').pop().toLowerCase();
                                let fileIcon = 'bx-file';
                                let fileColor = '#3498db';
                                
                                // Set icon based on file type
                                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                                    fileIcon = 'bx-image';
                                    fileColor = '#2ecc71';
                                } else if (['pdf'].includes(fileExtension)) {
                                    fileIcon = 'bxs-file-pdf';
                                    fileColor = '#e74c3c';
                                } else if (['doc', 'docx'].includes(fileExtension)) {
                                    fileIcon = 'bxs-file-doc';
                                    fileColor = '#3498db';
                                } else if (['xls', 'xlsx'].includes(fileExtension)) {
                                    fileIcon = 'bxs-file-doc';
                                    fileColor = '#27ae60';
                                }
                                
                                const fileCard = document.createElement('div');
                                fileCard.className = 'document-card';
                                fileCard.style.cssText = `
                                    background-color: #fff;
                                    border-radius: 8px;
                                    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
                                    padding: 16px;
                                    display: flex;
                                    flex-direction: column;
                                    align-items: center;
                                    transition: transform 0.2s, box-shadow 0.2s;
                                    cursor: pointer;
                                `;
                                
                                // For image files, show a thumbnail
                                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                                    fileCard.innerHTML = `
                                        <div style="width: 100%; height: 150px; overflow: hidden; border-radius: 6px; margin-bottom: 12px; background-color: #f1f5f9;">
                                            <img src="${documentsPath}${file.name}" alt="${file.name}" style="width: 100%; height: 100%; object-fit: cover;">
                                        </div>
                                        <div style="width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 14px; color: #333; text-align: center;">
                                            ${file.name}
                                        </div>
                                        <div style="display: flex; gap: 8px; margin-top: 12px;">
                                            <a href="${documentsPath}${file.name}" target="_blank" class="view-btn" style="background-color: #3498db; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                                                <i class='bx bx-show'></i> View
                                            </a>
                                            <a href="${documentsPath}${file.name}" download="${file.name}" class="download-btn" style="background-color: #2ecc71; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                                                <i class='bx bx-download'></i> Download
                                            </a>
                                        </div>
                                    `;
                                } else {
                                    fileCard.innerHTML = `
                                        <div style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px;">
                                            <i class='bx ${fileIcon}' style="font-size: 48px; color: ${fileColor};"></i>
                                        </div>
                                        <div style="width: 100%; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-size: 14px; color: #333; text-align: center;">
                                            ${file.name}
                                        </div>
                                        <div style="display: flex; gap: 8px; margin-top: 12px;">
                                            <a href="${documentsPath}${file.name}" target="_blank" class="view-btn" style="background-color: #3498db; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                                                <i class='bx bx-show'></i> View
                                            </a>
                                            <a href="${documentsPath}${file.name}" download="${file.name}" class="download-btn" style="background-color: #2ecc71; color: white; padding: 6px 12px; border-radius: 4px; text-decoration: none; font-size: 12px; display: flex; align-items: center; gap: 4px;">
                                                <i class='bx bx-download'></i> Download
                                            </a>
                                        </div>
                                    `;
                                }
                                
                                fileCard.addEventListener('mouseover', function() {
                                    this.style.transform = 'translateY(-5px)';
                                    this.style.boxShadow = '0 8px 16px rgba(0,0,0,0.1)';
                                });
                                
                                fileCard.addEventListener('mouseout', function() {
                                    this.style.transform = 'translateY(0)';
                                    this.style.boxShadow = '0 2px 8px rgba(0,0,0,0.1)';
                                });
                                
                                documentsContainer.appendChild(fileCard);
                            });
                        } else {
                            // No documents found or error
                            documentsContainer.innerHTML = '';
                            
                            if (data.success) {
                                // No documents found
                                noDocumentsMessage.style.display = 'block';
                            } else {
                                // Error message
                                documentsContainer.innerHTML = `
                                    <div style="grid-column: 1/-1; text-align: center; padding: 40px; background: #fff3e0; border-radius: 8px;">
                                        <i class='bx bx-error-circle' style="font-size: 48px; color: #e67e22;"></i>
                                        <p style="margin-top: 16px; color: #666;">${data.message || 'Error loading documents. Please try again.'}</p>
                                        <div style="margin-top: 16px; font-size: 12px; color: #999; text-align: left; background: #f8f9fa; padding: 12px; border-radius: 4px; max-width: 600px; margin-left: auto; margin-right: auto;">
                                            <p style="margin: 0 0 8px 0; font-weight: bold;">Debug Information:</p>
                                            <pre style="margin: 0; white-space: pre-wrap; word-break: break-all;">${JSON.stringify(data, null, 2)}</pre>
                                        </div>
                                    </div>
                                `;
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        console.log('XHR:', xhr);
                        
                        let errorMessage = 'Failed to load documents. Server error.';
                        let responseText = '';
                        
                        try {
                            if (xhr.responseText) {
                                responseText = xhr.responseText;
                                const response = JSON.parse(xhr.responseText);
                                if (response.message) {
                                    errorMessage = response.message;
                                }
                            }
                        } catch (e) {
                            console.error('Error parsing response:', e);
                        }
                        
                        documentsContainer.innerHTML = `
                            <div style="grid-column: 1/-1; text-align: center; padding: 40px; background: #ffebee; border-radius: 8px;">
                                <i class='bx bx-error-circle' style="font-size: 48px; color: #e74c3c;"></i>
                                <p style="margin-top: 16px; color: #666;">${errorMessage}</p>
                                <div style="margin-top: 16px; font-size: 12px; color: #999; text-align: left; background: #f8f9fa; padding: 12px; border-radius: 4px; max-width: 600px; margin-left: auto; margin-right: auto;">
                                    <p style="margin: 0 0 8px 0; font-weight: bold;">Debug Information:</p>
                                    <p style="margin: 0 0 4px 0;"><strong>Status:</strong> ${status}</p>
                                    <p style="margin: 0 0 4px 0;"><strong>Error:</strong> ${error}</p>
                                    <p style="margin: 0 0 4px 0;"><strong>Response:</strong></p>
                                    <pre style="margin: 0; white-space: pre-wrap; word-break: break-all;">${responseText}</pre>
                                </div>
                            </div>
                        `;
                    }
                });
            }
        });
    </script>

    <script src="../../js/sweetalert.js"></script>
    <script src="../../js/toggleSideBar.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/hideSideBar.js"></script>

</body>

</html>