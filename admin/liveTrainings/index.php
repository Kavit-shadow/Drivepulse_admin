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

    <style>
        .order {
            width: 100%;
        }

        .order .head {
            background-color: #f1f1f1;
            padding: 10px;
        }

        .order h3 {
            margin: 0;
        }

        .order .table-container {
            max-height: 400px;
            overflow-y: auto;
            border: 1px solid #ccc;
            margin-top: 10px;
        }

        .order table {
            width: 100%;
            border-collapse: collapse;
        }

        .order th,
        .order td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ccc;
        }

        .order tbody tr:last-child td {
            border-bottom: none;
        }

        .view {
            font-size: 14px;
            padding: 6px 16px;
            color: var(--light);
            border-radius: 20px;
            font-weight: 200;
            background: var(--blue);
            text-decoration: none;
            transition: all 250ms;
        }

        .view:hover {
            background: #206e88;
        }

        .order td[colspan="6"] {
            text-align: center;
        }



        .edit {
            font-size: 14px;
            padding: 6px 16px;
            color: #fff;
            border-radius: 20px;
            font-weight: 200;
            background: green;
            text-decoration: none;
        }

        .delete {
            font-size: 14px;
            padding: 6px 16px;
            color: #fff;
            border-radius: 20px;
            font-weight: 200;
            background: red;
            text-decoration: none;
        }

        .edit:hover {
            background: darkgreen;
        }

        .delete:hover {
            background: darkred;
        }

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

        #CU {
            display: inline-block;
            padding: 10px 20px;
            background-color: #46abcc;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s;
        }


        #RU {
            display: inline-block;
            padding: 10px 20px;
            background-color: #ff2626;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s;
        }


        #EU {
            display: inline-block;
            padding: 10px 20px;
            background-color: #00d813;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        #CU:hover {
            background: #176781;
        }

        #RU:hover {
            background: #c60f0f;
        }

        #EU:hover {
            background: #099a16;
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
                width: 50%;
            }

            th {
                display: none;
            }

            td {

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
                gap: 5.2rem;
                display: flex;

            }

            .delete {
                width: 90px;
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
    </style>

    <style>
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
        .order {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-top: 30px;
        }

        .order .head {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 2px solid #eee;
        }

        .order .head h3 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2b2b2b;
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: 1rem;
        }

        .status.live {
            display: inline-flex;
            align-items: center;
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
            background: rgba(255, 0, 0, 0.1);
            color: #ff0000;
            transition: all 0.2s ease;
        }

        .status.live:hover {
            background: rgba(255, 0, 0, 0.15);
        }

        .table-container {
            margin-top: 20px;
            max-height: 500px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #888 #f5f5f5;
        }

        .table-container::-webkit-scrollbar {
            width: 8px;
        }

        .table-container::-webkit-scrollbar-track {
            background: #f5f5f5;
            border-radius: 4px;
        }

        .table-container::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
        }

        th,
        td {
            padding: 15px 20px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f8f9fa;
            font-weight: 600;
            color: #2b2b2b;
            position: sticky;
            top: 0;
            z-index: 1;
        }

        tbody tr {
            transition: all 0.2s ease;
        }

        tbody tr:hover {
            background-color: #f8f9fa;
        }

        .view {
            display: inline-block;
            padding: 8px 16px;
            background: #007bff;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            font-size: 0.9rem;
            transition: all 0.2s ease;
        }

        .view:hover {
            background: #0056b3;
            transform: translateY(-1px);
        }

        @media screen and (max-width: 1024px) {
            .order {
                padding: 15px;
            }

            th,
            td {
                padding: 12px 15px;
            }
        }

        @media screen and (max-width: 768px) {
            .order .head h3 {
                font-size: 1.2rem;
            }

            table {
                border: 0;
            }

            thead {
                display: none;
            }

            tr {
                margin-bottom: 15px;
                display: block;
                border-radius: 8px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                text-align: right;
                padding: 12px 15px;
                border-bottom: 1px solid #eee;
            }

            td:last-child {
                border-bottom: 0;
            }

            td::before {
                content: attr(data-label);
                font-weight: 600;
                margin-right: 15px;
                color: #2b2b2b;
            }

            .status.live {
                margin-left: auto;
            }

            .view {
                width: 100%;
                text-align: center;
            }
        }

        @media screen and (max-width: 480px) {
            .order {
                padding: 10px;
            }

            .order .head h3 {
                font-size: 1.1rem;
            }

            td {
                padding: 10px;
                font-size: 0.9rem;
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
    <title>Live Trainings</title>
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
                <a href="./">
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

            <li class="active">
                <a href="./">
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
                    <h1>Live Trainings</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./">Live Trainings</a>
                        </li>

                    </ul>
                </div>
                 <button class="btn-clean" type="button" id="clearTimeOutWarnings">
                    <i class='bx bx-refresh'></i>
                    <span class="text">Clean Time Out Warnings</span>
                </button>
                <style>
                    .btn-clean {
                        background-color: #007bff;
                        color: #fff;
                        padding: 10px 20px;
                        border-radius: 20px;
                        text-decoration: none;
                        display: inline-flex;
                        align-items: center;
                        justify-content: center;
                        font-size: 16px;
                        /* Base font size */
                        border: none;
                        transition: background-color 0.3s ease, transform 0.2s ease;
                    }

                    .btn-clean:hover {
                        background-color: #0056b3;
                        /* Darker shade on hover */
                        transform: scale(1.05);
                        /* Slight zoom effect */
                    }

                    .btn-clean:active {
                        background-color: #003f88;
                        /* Even darker shade when active */
                        transform: scale(0.98);
                        /* Pressed effect */
                    }

                    .btn-clean i {
                        font-size: 24px;
                        margin-right: 8px;
                    }

                    /* Responsive design adjustments */
                    @media (max-width: 768px) {
                        .btn-clean {
                            padding: 8px 16px;
                            font-size: 14px;
                        }

                        .btn-clean i {
                            font-size: 20px;
                        }
                    }

                    @media (max-width: 480px) {
                        .btn-clean {
                            padding: 6px 12px;
                            font-size: 12px;
                        }

                        .btn-clean i {
                            font-size: 18px;
                        }
                    }
                </style>
            </div>

            <div class="order">
                <div class="head">
                    <h3>Live Training Sessions <i class='bx bx-broadcast bx-burst' style="color: #ff0000;"></i></h3>
                </div>

                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>Status</th>
                                <th>Customer Name</th>
                                <th>Phone</th>
                                <th>Vehicle</th>
                                <th>Instructor</th>
                                <th>Time Slot</th>
                                <th>Start Time</th>
                                <th>Duration</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="live-trainings-data">
                            <!-- Data will be loaded here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>





        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->



    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
     <script>
        $(document).ready(function() {
            $("#clearTimeOutWarnings").click(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST", // Changed from PUT to POST as per PHP context
                    url: "../../api_ajax/clearTimeOutWarnings.php",
                    contentType: "application/json",
                    success: function(response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: response.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'An error occurred while processing your request.'
                        });
                    }
                });
            });
        });
    </script>
    <script>
        function calculateDuration(startTime) {
            const start = new Date(startTime);
            const now = new Date();
            const durationInMinutes = Math.floor((now - start) / (1000 * 60));
            console.log(startTime);
            
            if (isNaN(durationInMinutes) || durationInMinutes <= 0) {
                return 'Just started';
            }
            return durationInMinutes + ' mins';
        }

        function fetchLiveTrainings() {
            $.ajax({
                url: '../../api_ajax/fetch_live_trainings.php',
                method: 'GET',
                success: function(response) {
                    let data;
                    try {
                        // Handle if response is already parsed
                        data = typeof response === 'string' ? JSON.parse(response) : response;
                    } catch (e) {
                        console.error('Error parsing response:', e);
                        $('#live-trainings-data').html('<tr><td colspan="9" style="text-align:center">Error parsing data</td></tr>');
                        return;
                    }

                    let html = '';

                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(training => {
                            if (!training) return;
                            
                            // Calculate duration in JS instead of using API value
                            const duration = calculateDuration(training.start_time_2);
                            
                            // Skip if duration is more than 50 minutes
                            if (duration !== 'Just started') {
                                const durationNum = parseInt(duration);
                                if (durationNum > 50) return;
                            }

                            html += `
                                <tr>
                                    <td data-label="Status">
                                        <span class="status live">
                                            <i class='bx bx-pulse bx-flashing' style="color: #ff0000;"></i> LIVE
                                        </span>
                                    </td>
                                    <td data-label="Customer Name">${training.customer_name || ''}</td>
                                    <td data-label="Phone">${training.phone || ''}</td>
                                    <td data-label="Vehicle">${training.vehicle || ''}</td>
                                    <td data-label="Instructor">${training.instructor || ''}</td>
                                    <td data-label="Time Slot">${training.time_slot || ''}</td>
                                    <td data-label="Start Time">${training.start_time || ''}</td>
                                    <td data-label="Duration">${duration}</td>
                                    <td data-label="Action" style="display: flex; gap: 10px;">
                                        <a href='../view?id=${training.cust_id || ''}&phone=${training.phone || ''}&route=../liveTrainings/' class='view'>
                                            View Details
                                        </a>
                                    </td>
                                </tr>
                            `;
                        });
                    } else {
                        html = '<tr><td colspan="9" style="text-align:center">No live training sessions found</td></tr>';
                    }

                    if (!html) {
                        html = '<tr><td colspan="9" style="text-align:center">No active training sessions under 50 minutes found</td></tr>';
                    }

                    $('#live-trainings-data').html(html);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching live trainings:', error);
                    $('#live-trainings-data').html('<tr><td colspan="9" style="text-align:center">Error loading data</td></tr>');
                }
            });
        }

        // Ensure DOM is loaded before running
        $(document).ready(function() {
            // Fetch data initially
            fetchLiveTrainings();

            // Refresh data every 30 seconds
            setInterval(fetchLiveTrainings, 30000);
        });


    </script>


    <script src="../../js/sweetalert.js"></script>
    <script src="../../js/toggleSideBar.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/hideSideBar.js"></script>

</body>

</html>