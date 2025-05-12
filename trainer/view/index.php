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

        .sidebar-footer {
            height: 80vh;
            height: 80dvh;
            height: 57%;
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
                <h6><span>Trainer</span></h6>
                <h4>Welcome <span>
                        <?php echo $_SESSION['trainer_name'] ?>
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
            <li>
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
                    <h1>View Details</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./">View Details</a>
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
                <div class="profile-container">
                    <div class="profile-image">
                        <!-- <img src="../../api_ajax/generate_image.php?name=<?php // echo $row["name"]; 
                                                                                ?>" alt="Profile Image"> -->
                        <img src="../../assets/Default_Profile.png" alt="Profile Image">
                    </div>
                    <div class="profile-details" style="
    padding-right: 50px;
">
                        <p style="font-size: 16px; margin: 10px 0; padding: 8px 0; border-bottom: 1px solid #ddd;">
                            <label style="font-weight: 600; color: #333; display: inline-block; width: 120px;">Customer UID:</label>
                            <span style="color: #666;"><?php echo $row["cust_uid"]; ?></span>
                        </p>
                        <p><label>Name:</label>
                            <?php echo $row["name"]; ?>
                        </p>

                        <p><label>Email:</label>
                            <?php echo $row["email"]; ?>
                        </p>
                        <p><label>Phone:</label>
                            <?php echo $row["phone"]; ?>
                        </p>
                        <p><label>Address:</label>
                            <?php echo $row["address"]; ?>
                        </p>
                        <p><label>Days:</label>
                            <?php echo $row["days"]; ?>
                        </p>
                        <p><label>Time-Slot:</label>
                            <?php echo $row["timeslot"]; ?>
                        </p>

                        <p><label>Vehicle:</label>
                            <?php echo $row["vehicle"]; ?>
                        </p>
                        <p><label>New Licence:</label>
                            <?php echo $row["newlicence"]; ?>
                        </p>
                        <p><label>Trainer Name:</label>
                            <?php echo $row["trainername"]; ?>
                        </p>
                        <p><label>Trainer Phone:</label>
                            <?php echo $row["trainerphone"]; ?>
                        </p>
                        <p><label>Admission Date:</label>
                            <?php echo $row["date"]; ?>
                        </p>

                        <p><label>Admission Time:</label>
                            <?php echo date('h:i A', strtotime($row["time"])); ?>
                        </p>
                        <p><label>Training Started On:</label>
                            <?php echo $row["startedAT"]; ?>
                        </p>
                        <p><label>Training Ended On:</label>
                            <?php echo $row["endedAT"]; ?>
                        </p>
                        <p><label>Form Filler:</label>
                            <?php echo $row["formfiller"]; ?>
                        </p>
                    </div>
                    <div class="profile-buttons" style="
    display: flex;
    justify-content: flex-start;
    flex-direction: column;
    flex-wrap: wrap;
    gap: 20px;
">
                        <button class="force-attendance-btn" style="background: #e74c3c; color: #fff; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" data-id="<?php echo $row['id']; ?>" data-cust-uid="<?php echo $row['cust_uid']; ?>" data-acc-id="<?php echo $_SESSION['trainer_ID']; ?>" data-acc-name="<?php echo $_SESSION['trainer_name']; ?>">
                            <i class='bx bx-time'></i>
                            Force Attendance
                        </button>
                        <button class="attendance-btn" style="background: #46abcc; color: #fff; border: none; padding: 12px 24px; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 500; transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; box-shadow: 0 2px 4px rgba(0,0,0,0.1);" data-id="<?php echo $row['id']; ?>" data-cust-uid="<?php echo $row['cust_uid']; ?>">View Attendance</button>
                        <a href="<?php echo $backRoute; ?>" class="close-btn">Close</a>
                    </div>
                </div>
            <?php
            }
            ?>

            <dialog id="attendanceModal">
                <div class="modal-content" style="padding: 20px;">
                    <button class="close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; border: none; background: none;">&times;</button>
                    <h2 style="margin-top: 0;">Attendance Details</h2>

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
                            for (let i = 0; i < Math.min(26, allDates.length || 26); i++) {
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
    <script src="../../js/sweetalert.js"></script>
    <script src="../../js/toggleSideBar.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/hideSideBar.js"></script>

</body>

</html>