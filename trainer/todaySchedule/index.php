<?php

include('../../includes/authenticationTrainer.php');
authenticationTrainer('../../');
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
    </style>
    <style>
        @media (max-width: 768px) {
            .profile-container {
                flex-direction: column;
                padding: 15px;
            }

            .profile-image {
                width: 150px;
                height: 150px;
                margin: 0 auto 20px;
            }

            .profile-image img {
                width: 100%;
                height: 100%;
            }

            .profile-details {
                padding-right: 0 !important;
                width: 100%;
            }

            .profile-details p {
                font-size: 14px;
                margin: 8px 0;
            }

            .profile-details label {
                width: 110px;
                font-size: 14px;
            }

            .profile-buttons {
                width: 100%;
                margin-top: 20px;
            }

            .attendance-btn {
                padding: 10px 20px !important;
                font-size: 13px !important;
            }
        }

        @media (max-width: 480px) {
            .profile-container {
                padding: 10px;
            }

            .profile-image {
                width: 120px;
                height: 120px;
            }

            .profile-details p {
                font-size: 13px;
                margin: 6px 0;
            }

            .profile-details label {
                width: 100px;
                font-size: 13px;
            }

            .attendance-btn {
                padding: 8px 16px !important;
                font-size: 12px !important;
            }

            .modal-content {
                padding: 15px !important;
            }

            .modal-table-container {
                overflow-x: auto;
            }

            #attendanceContent table {
                font-size: 12px;
            }

            #attendanceContent th,
            #attendanceContent td {
                padding: 8px !important;
            }
        }
    </style>


    <style>
        .search-box {
            display: flex;
            align-items: center;
            background: #fff;
            border-radius: 5px;
            padding: 5px 10px;
            margin-left: 10px;
        }

        .search-box input {
            border: none;
            outline: none;
            padding: 5px;
            width: 200px;
        }

        .search-box i {
            color: #888;
        }

        .view-btn {
            background: #3C91E6;
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .view-btn:hover {
            background: #2d7ac7;
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-weight: 500;
        }

        .status.active {
            background: #d3f3d3;
            color: #1b911b;
        }

        .status.pending {
            background: #fff2d0;
            color: #a68817;
        }

        .status.completed {
            background: #e9ecef;
            color: #6c757d;
        }

        #content main .table-data {
            height: 70vh !important;


        }

        @media screen and (max-width: 768px) {
            #content main .table-data .order table {
                min-width: 700px;
            }

            .table-container {
                overflow-x: auto;
            }

            .view-btn {
                padding: 4px 8px;
                font-size: 12px;
            }

            table td {
                padding: 10px 5px;
                font-size: 13px;
            }

            table th {
                padding: 10px 5px;
                font-size: 14px;
            }

            .head {
                flex-direction: column;
                gap: 10px;
            }

            .head h3 {
                font-size: 18px;
            }
        }

        @media screen and (max-width: 480px) {
            #content main .table-data .order table {
                min-width: 500px;
            }

            table td {
                font-size: 12px;
            }

            table th {
                font-size: 13px;
            }

            .head h3 {
                font-size: 16px;
            }
        }

        .sidebar-footer {
            height: 80vh;
            height: 80dvh;
            height: 57%;
        }
    </style>




    <!-- Add SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />
    <title>Today's Schedule</title>
</head>

<body id="root">


    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-car'></i>

            <span class="text">
                <h6><span>Trainer</span></h6>
                <h4>Welcome <span>
                        <?php echo $_SESSION['trainer_name'] ?>
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
            <li class="active">
                <a href="../todaySchedule">
                    <i class='bx bx-calendar'></i>
                    <span class="text">Today's Schedule</span>
                </a>
            </li>
            <li>
                <a href="../timetable">
                    <i class='bx bx-table'></i>
                    <span class="text">Time Table</span>
                </a>
            </li>
            <li >
                <a href="../myStudents">
                    <i class='bx bx-user'></i>
                    <span class="text">My Students</span>
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
                    <h1>Today's Schedule</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./">Today's Schedule</a>
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
                        <h3>Today's Schedule</h3>
                    </div>

                    <div class="timeslot-groups">
                        <?php
                        include("../../config.php");
                        
                        // Define timeslots
                        $timeslots = [
                            "7:00am to 7:30am", "7:30am to 8:00am", "8:00am to 8:30am",
                            "8:30am to 9:00am", "9:00am to 9:30am", "9:30am to 10:00am",
                            "10:00am to 10:30am", "10:30am to 11:00am", "11:00am to 11:30am",
                            "11:30am to 12:00pm", "12:00pm to 12:30pm", "12:30pm to 1:00pm",
                            "1:00pm to 1:30pm", "1:30pm to 2:00pm", "2:00pm to 2:30pm",
                            "2:30pm to 3:00pm", "3:00pm to 3:30pm", "3:30pm to 4:00pm",
                            "4:00pm to 4:30pm", "4:30pm to 5:00pm", "5:00pm to 5:30pm",
                            "5:30pm to 6:00pm", "6:00pm to 6:30pm", "6:30pm to 7:00pm",
                            "7:00pm to 7:30pm", "7:30pm to 8:00pm"
                        ];

                        // Get all vehicle database tables
                        $DBtables = mysqli_fetch_all(mysqli_query($conn, "SELECT data_base_table, vehicle_name FROM vehicles"), MYSQLI_ASSOC);
                        
                        // Initialize array to store all customers grouped by timeslot
                        $grouped_customers = array();
                        
                        // Get current date in Y-m-d format
                        $today = date('Y-m-d');
                        $trainer_id = $_SESSION['trainer_ID'];
                        
                        // Fetch trainer details
                        $trainer_query = "SELECT name, phone FROM employees WHERE emp_uid = (SELECT emp_uid FROM users_db WHERE id = ?)";
                        // $trainer_query = "SELECT name, phone FROM employees WHERE emp_uid = 'DR2Y57'";
                        $stmt = mysqli_prepare($conn, $trainer_query);
                        mysqli_stmt_bind_param($stmt, "i", $trainer_id);
                        mysqli_stmt_execute($stmt);
                        $trainer_result = mysqli_stmt_get_result($stmt);
                        $trainer_details = mysqli_fetch_assoc($trainer_result);
                        
                        // Loop through each vehicle table
                        foreach ($DBtables as $table) {
                            $table_name = $table['data_base_table'];
                            $vehicle_name = $table['vehicle_name'];
                            
                            // Query to get today's bookings for the current trainer
                            $query = "SELECT v.name, v.phone, v.timeslots, v.start_date, v.end_date, v.status,
                                           cd.id as customer_id
                                    FROM $table_name v
                                    LEFT JOIN cust_details cd 
                                        ON cd.phone = v.phone 
                                        AND cd.name = v.name
                                    WHERE v.trainer = ? AND v.status != 'empty'";
                                    
                            $stmt = mysqli_prepare($conn, $query);
                            mysqli_stmt_bind_param($stmt, "s", $trainer_details['name']);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            
                            // Group customers by timeslot
                            while ($row = mysqli_fetch_assoc($result)) {
                                if (!isset($grouped_customers[$row['timeslots']])) {
                                    $grouped_customers[$row['timeslots']] = array();
                                }
                                $row['vehicle'] = $vehicle_name;
                                $grouped_customers[$row['timeslots']][] = $row;
                            }
                        }

                        // Display timeslots and their customers
                        foreach ($timeslots as $timeslot) {
                            $hasCustomers = isset($grouped_customers[$timeslot]) && !empty($grouped_customers[$timeslot]);
                            
                            if ($hasCustomers) {
                                echo "<div class='timeslot-group'>";
                                echo "<div class='timeslot-header' onclick='toggleTimeslot(this)'>";
                                echo "<div class='header-content'>";
                                echo "<h4>$timeslot</h4>";
                                echo "<span class='customer-count'>" . count($grouped_customers[$timeslot]) . " customers</span>";
                                echo "</div>";
                                echo "<i class='bx bx-chevron-down toggle-icon'></i>";
                                echo "</div>";
                                
                                echo "<div class='customer-table-container collapsed'>";
                                echo "<table class='customer-table'>";
                                echo "<thead>";
                                echo "<tr>";
                                echo "<th>Name</th>";
                                echo "<th>Contact Number</th>";
                                echo "<th>Vehicle</th>";
                                echo "<th>Started At</th>";
                                echo "<th>Ended At</th>";
                                echo "<th>Action</th>";
                                echo "</tr>";
                                echo "</thead>";
                                echo "<tbody>";
                                
                                foreach ($grouped_customers[$timeslot] as $customer) {
                                    
                                    echo "<tr>";
                                    echo "<td data-label='Name'>" . htmlspecialchars($customer['name']) . "</td>";
                                    echo "<td data-label='Contact Number'>" . htmlspecialchars($customer['phone']) . "</td>";
                                    echo "<td data-label='Vehicle'>" . htmlspecialchars($customer['vehicle']) . "</td>";
                                    echo "<td data-label='Start Date'>" . htmlspecialchars($customer['start_date']) . "</td>";
                                    echo "<td data-label='End Date'>" . htmlspecialchars($customer['end_date']) . "</td>";
                                    echo "<td data-label='Action'><button class='view-btn' onclick='window.location.href=\"../view?id=" . urlencode($customer['customer_id']) ."&name=" . urlencode($customer['name']) ."&phone=" . urlencode($customer['phone']) ."&route=../todaySchedule\"'><i class='bx bx-show'></i> View</button></td>";
                                    echo "</tr>";
                                }
                                
                                echo "</tbody>";
                                echo "</table>";
                                echo "</div>";
                                echo "</div>";
                            } else {
                                echo "<div class='timeslot-group empty'>";
                                echo "<div class='timeslot-header'>";
                                echo "<div class='header-content'>";
                                echo "<h4>$timeslot</h4>";
                                echo "<span class='customer-count'>No customers</span>";
                                echo "</div>";
                                echo "</div>";
                                echo "</div>";
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>

            <style>
                .timeslot-groups {
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                    padding: 20px;
                }

                .timeslot-group {
                    background: #fff;
                    border-radius: 8px;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                    overflow: hidden;
                    transition: transform 0.2s ease, box-shadow 0.2s ease;
                }

                .timeslot-group:not(.empty):hover {
                    transform: translateY(-2px);
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                .timeslot-group.empty {
                    background: #f8f9fa;
                    padding: 10px;
                    opacity: 0.7;
                }

                .timeslot-header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 15px 20px;
                    background: #f8f9fa;
                    border-bottom: 1px solid #e9ecef;
                    cursor: pointer;
                    user-select: none;
                    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                }

                .timeslot-header:hover {
                    background: #e9ecef;
                }

                .timeslot-header.active {
                    background: #e9ecef;
                    border-bottom-color: #dee2e6;
                }

                .header-content {
                    display: flex;
                    align-items: center;
                    gap: 15px;
                    flex: 1;
                }

                .toggle-icon {
                    font-size: 1.5rem;
                    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                    color: #495057;
                }

                .timeslot-header.active .toggle-icon {
                    transform: rotate(-180deg);
                    color: #2b2b2b;
                }

                .timeslot-header h4 {
                    margin: 0;
                    color: #2b2b2b;
                    font-size: 1.1rem;
                    transition: color 0.3s ease;
                }

                .timeslot-header.active h4 {
                    color: #000;
                }

                .customer-count {
                    background: #e9ecef;
                    padding: 4px 8px;
                    border-radius: 4px;
                    font-size: 0.9rem;
                    color: #495057;
                    transition: all 0.3s ease;
                }

                .timeslot-header.active .customer-count {
                    background: #dee2e6;
                    color: #212529;
                }

                .customer-table-container {
                    padding: 15px;
                    overflow: hidden;
                    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
                    max-height: 2000px; /* Increased to handle more content */
                    opacity: 1;
                    transform-origin: top;
                    transform: scaleY(1);
                }

                .customer-table-container.collapsed {
                    max-height: 0;
                    padding-top: 0;
                    padding-bottom: 0;
                    opacity: 0;
                    transform: scaleY(0);
                }

                .customer-table {
                    width: 100%;
                    border-collapse: collapse;
                    opacity: 1;
                    transition: opacity 0.3s ease;
                }

                .collapsed .customer-table {
                    opacity: 0;
                }

                .customer-table th,
                .customer-table td {
                    padding: 12px;
                    text-align: left;
                    border-bottom: 1px solid #e9ecef;
                }

                .customer-table th {
                    background: #f8f9fa;
                    font-weight: 600;
                    color: #495057;
                }

                .customer-table tr:last-child td {
                    border-bottom: none;
                }

                .view-btn {
                    background: #3C91E6;
                    color: #fff;
                    padding: 6px 12px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                    display: inline-flex;
                    align-items: center;
                    gap: 5px;
                    font-size: 0.9rem;
                }

                .view-btn:hover {
                    background: #2d7ac7;
                }

                @media screen and (max-width: 768px) {
                    .timeslot-groups {
                        padding: 10px;
                        gap: 15px;
                    }

                    .timeslot-header {
                        padding: 12px 15px;
                    }

                    .timeslot-header h4 {
                        font-size: 0.95rem;
                    }

                    .customer-count {
                        font-size: 0.8rem;
                        padding: 3px 6px;
                    }

                    .customer-table-container {
                        padding: 10px;
                    }

                    /* Make table scrollable horizontally */
                    .customer-table {
                        min-width: 600px;
                    }

                    .customer-table th,
                    .customer-table td {
                        padding: 8px;
                        font-size: 0.85rem;
                    }

                    .view-btn {
                        padding: 4px 8px;
                        font-size: 0.8rem;
                    }
                }

                @media screen and (max-width: 480px) {
                    .timeslot-groups {
                        padding: 8px;
                        gap: 12px;
                    }

                    .timeslot-header {
                        padding: 10px;
                        flex-direction: column;
                        gap: 5px;
                        align-items: flex-start;
                    }

                    .timeslot-header h4 {
                        font-size: 0.9rem;
                    }

                    .customer-count {
                        font-size: 0.75rem;
                    }

                    .customer-table-container {
                        padding: 8px;
                    }

                    /* Stack table cells for very small screens */
                    .customer-table {
                        min-width: unset;
                        width: 100%;
                    }

                    .customer-table thead {
                        display: none;
                    }

                    .customer-table tr {
                        display: block;
                        border: 1px solid #e9ecef;
                        border-radius: 4px;
                        margin-bottom: 10px;
                        padding: 8px;
                        background: #fff;
                    }

                    .customer-table td {
                        display: flex;
                        padding: 6px 0;
                        border: none;
                        font-size: 0.8rem;
                        align-items: center;
                    }

                    .customer-table td::before {
                        content: attr(data-label);
                        font-weight: 600;
                        width: 120px;
                        min-width: 120px;
                        color: #495057;
                    }

                    .view-btn {
                        width: 100%;
                        justify-content: center;
                        margin-top: 5px;
                    }
                }
            </style>

            <script>
                function toggleTimeslot(header) {
                    const container = header.nextElementSibling;
                    const allHeaders = document.querySelectorAll('.timeslot-header');
                    const allContainers = document.querySelectorAll('.customer-table-container');
                    
                    // If clicking on an already active header, just close it
                    if (header.classList.contains('active')) {
                        header.classList.remove('active');
                        container.classList.add('collapsed');
                        return;
                    }

                    // Close all other open timeslots
                    allHeaders.forEach(h => h.classList.remove('active'));
                    allContainers.forEach(c => c.classList.add('collapsed'));

                    // Open the clicked timeslot
                    setTimeout(() => {
                        header.classList.add('active');
                        container.classList.remove('collapsed');
                    }, 50);
                }

                // Open the first timeslot with customers by default
                document.addEventListener('DOMContentLoaded', function() {
                    const firstTimeslot = document.querySelector('.timeslot-group:not(.empty) .timeslot-header');
                    if (firstTimeslot) {
                        setTimeout(() => {
                            firstTimeslot.click();
                        }, 300); // Delay to allow page to settle
                    }
                });

                // Optional: Add smooth scroll when opening a timeslot
                function scrollToHeader(header) {
                    const headerRect = header.getBoundingClientRect();
                    const absoluteHeaderTop = headerRect.top + window.pageYOffset;
                    const middle = window.innerHeight / 3;
                    
                    window.scrollTo({
                        top: absoluteHeaderTop - middle,
                        behavior: 'smooth'
                    });
                }
            </script>

        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->


    <!-- Add html2canvas and jsPDF libraries -->
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script src="../../js/sweetalert.js"></script>
    <script src="../../js/toggleSideBar.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/hideSideBar.js"></script>

</body>

</html>