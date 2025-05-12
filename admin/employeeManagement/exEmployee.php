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
            -webkit-overflow-scrolling: touch; /* Smooth scrolling on iOS */
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
            .restore-btn, .view-btn {
                padding: 6px 10px !important;
                font-size: 12px !important;
                display: block !important;
                width: 100% !important;
                margin: 5px 0 !important;
            }

            .restore-btn i, .view-btn i {
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
    <!-- Add SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />
    <title>Ex-Employee's</title>
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
            <li class="active">
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
                    <h1>Employee Management</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="./">Employee Management</a>
                        </li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./exEmployee">Ex-Employee's</a>
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
                        <h3>Ex-Employees</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joining Date</th>
                                <th>Leaving Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT id, name, phone, email, role, joining_date, leaving_date, photo, photo_type FROM employees WHERE is_ex_employee = 1";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                while ($row = $result->fetch_assoc()) {
                                    $photo = $row["photo"] ? "data:" . $row["photo_type"] . ";base64," . base64_encode($row["photo"]) : "../../assets/Default_Profile.png";
                                    echo "<tr>
                                            <td data-cell='Name'>
                                                <img src='" . $photo . "' alt='Profile' style='width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; vertical-align: middle;'>
                                                " . $row["name"] . "
                                            </td>
                                            <td data-cell='Phone'>" . $row["phone"] . "</td>
                                            <td data-cell='Email'>" . $row["email"] . "</td>
                                            <td data-cell='Role'>" . $row["role"] . "</td>
                                            <td data-cell='Joining Date'>" . $row["joining_date"] . "</td>
                                            <td data-cell='Leaving Date'>" . $row["leaving_date"] . "</td>
                                            <td data-cell='Action'>
                                                <button class='restore-btn' data-id='" . $row["id"] . "' data-name='" . $row["name"] . "' style='background:#339ddd; padding: 8px 15px; border: none; border-radius: 4px; color: white; font-size: 14px; cursor: pointer; margin-right: 5px; transition: all 0.3s ease;'><i class='bx bx-history' style='vertical-align: middle; margin-right: 5px;'></i> Restore</button>
                                                <button class='view-btn' data-id='" . $row["id"] . "' style='background:#4CAF50; padding: 8px 15px; border: none; border-radius: 4px; color: white; font-size: 14px; cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1);'><i class='bx bxs-show' style='vertical-align: middle; margin-right: 5px;'></i> View</button>
                                            </td>
                                        </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='7' style='text-align:center'>No ex-employees found</td></tr>";
                            }
                            $conn->close();
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>



        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <dialog id="employeeModal">
        <div class="modal-content" style="padding: 20px;">
            <button class="close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; border: none; background: none;">&times;</button>
            <h2 style="margin-top: 0;">Employee Details</h2>
            <div class="modal-table-container">
                <div class="employee-images" style="display: flex; flex-direction: column; gap: 20px;">
                    <div>
                        <h4>Profile Photo</h4>
                        <img class="modalProfilePhoto" src="" alt="Profile Photo" style="width: 200px; height: 200px; object-fit: cover; border-radius: 8px;">
                    </div>
                    <div>
                        <h4>Aadhar Card</h4>
                        <img id="modalAadharImage" src="" alt="Aadhar Card" style="width: 200px; height: auto; object-fit: contain; border-radius: 8px;">
                    </div>
                    <div id="empuid-card" style="background: #fff; padding: 20px; border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); text-align: center;">
                        <div style="display:flex; flex-direction:column; align-items:center; justify-content:center;">
                        <img id="empuid-logo" src="https://i.postimg.cc/BQvDYtCZ/PMDS-text-B.png" alt="Company Logo" style="width: 180px; height: auto; margin-bottom: 15px;">
                        <img class="modalProfilePhoto" src="" alt="Profile Photo" style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;">
                        </div>
                        <h4 style="color: #333; font-size: 18px; margin-bottom: 15px;">Employee ID Card</h4>
                        <div style="border: 2px solid #e2e8f0; padding: 15px; border-radius: 8px;">
                            <div style="display: flex; justify-content: center; gap: 20px; margin-bottom: 15px;">
                               
                                <div>
                                    <span style="display: block; color: #666; font-size: 14px; margin-bottom: 5px;">Scan for Attendance</span>
                                    <img id="modalQRCode" src="" alt="QR Code" style="width: 180px; height: 180px; object-fit: contain; margin: 0 auto;">
                                </div>
                            </div>
                            <div style="font-size: 13px; color: #666; margin-bottom: 15px;">
                                <p style="margin: 0;">Name: <strong><span class="modalName"></span></strong></p>
                            </div>
                        </div>
                    </div>
                 

                </div>

                <div class="employee-info" style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px;">
                    <div class="info-item">
                        <strong>Employee UID:</strong>
                        <span id="modalId"></span>
                    </div>
                    <div class="info-item">
                        <strong>Name:</strong>
                        <span class="modalName"></span>
                    </div>
                    <div class="info-item">
                        <strong>Phone:</strong>
                        <span id="modalPhone"></span>
                    </div>
                    <div class="info-item">
                        <strong>Email:</strong>
                        <span id="modalEmail"></span>
                    </div>
                    <div class="info-item">
                        <strong>Aadhar Number:</strong>
                        <span id="modalAadhar"></span>
                    </div>
                    <div class="info-item">
                        <strong>Date of Birth:</strong>
                        <span id="modalDob"></span>
                    </div>
                    <div class="info-item">
                        <strong>Gender:</strong>
                        <span id="modalGender"></span>
                    </div>
                    <div class="info-item">
                        <strong>Role:</strong>
                        <span id="modalRole"></span>
                    </div>
                    <div class="info-item">
                        <strong>Joining Date:</strong>
                        <span id="modalJoiningDate"></span>
                    </div>
                    <div class="info-item">
                        <strong>Leaving Date:</strong>
                        <span id="modalLeavingDate"></span>
                    </div>
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <strong>Address:</strong>
                        <span id="modalAddress"></span>
                    </div>

                    <hr style="grid-column: 1 / -1; margin: 10px 0; border: none; border-top: 1px solid #e2e8f0;">

                    <div class="info-item">
                        <strong>Created At:</strong>
                        <span id="modalCreatedAt"></span>
                    </div>
                    <div class="info-item">
                        <strong>Updated At:</strong>
                        <span id="modalUpdatedAt"></span>
                    </div>
             

                   

                </div>

            </div>

        </div>
    </dialog>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // View button click handler
        $(document).on('click', '.view-btn', function() {
            const employeeId = $(this).data('id');
            $.ajax({
                url: '../../api_ajax/get_employee_details.php',
                method: 'GET',
                data: {
                    id: employeeId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        const employee = response.employee;

                        // Populate modal with employee details
                        $('#modalId').text(employee.emp_uid);
                        $('.modalName').text(employee.name);
                        $('#modalPhone').text(employee.phone);
                        $('#modalEmail').text(employee.email);
                        $('#modalAadhar').text(employee.aadhar);
                        $('#modalDob').text(employee.dob);
                        $('#modalGender').text(employee.gender);
                        $('#modalRole').text(employee.role);
                        $('#modalJoiningDate').text(employee.joining_date);
                        $('#modalAddress').text(employee.address);
                        $('#modalCreatedAt').text(employee.created_at);
                        $('#modalUpdatedAt').text(employee.updated_at);
                        $('#modalLeavingDate').text(employee.leaving_date);
                        // Set images
                        $('.modalProfilePhoto').attr('src', employee.photo || '../../assets/Default_Profile.png');
                        $('#modalAadharImage').attr('src', employee.aadhar_image || '../../assets/default_aadhaar_card.jpg');
                        $('#modalQRCode').attr('src', employee.emp_att_qr || '../../assets/default_qr.jpg');

                        // Show modal using the showModal() method

                    

                        document.getElementById('employeeModal').showModal();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to fetch employee details',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch employee details. Please try again later.',
                        confirmButtonColor: '#3085d6'
                    });
                    console.error('Error:', error);
                }
            });
        });

        // Close modal when clicking the close button or outside the modal
        $('.close, #employeeModal').click(function(event) {
            if (event.target == document.getElementById('employeeModal') || $(event.target).hasClass('close')) {
                document.getElementById('employeeModal').close();
            }
        });

        // Function to download QR code
        function downloadQR() {
            const qrImage = document.getElementById('modalQRCode');
            const link = document.createElement('a');
            link.href = qrImage.src;
            link.download = 'employee_qr.png';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        // Handle restore button click
        $('.restore-btn').click(function() {
            const employeeId = $(this).data('id');
            const employeeName = $(this).data('name');

            Swal.fire({
                title: 'Restore Employee?',
                text: `Are you sure you want to restore ${employeeName} as an active employee?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#339ddd',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, restore!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../../api_ajax/restore_employee.php',
                        type: 'POST',
                        data: {
                            employee_id: employeeId
                        },
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: `${employeeName} has been restored successfully.`,
                                    confirmButtonColor: '#339ddd'
                                }).then(() => {
                                    // Reload the page to reflect changes
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to restore employee',
                                    confirmButtonColor: '#339ddd'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to restore employee. Please try again later.',
                                confirmButtonColor: '#339ddd'
                            });
                            console.error('Error:', error);
                        }
                    });
                }
            });
        });
    </script>

    <script src="../../js/sweetalert.js"></script>
    <script src="../../js/toggleSideBar.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/hideSideBar.js"></script>

</body>

</html>