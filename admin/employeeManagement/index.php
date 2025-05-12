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
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
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

            .edit {

                font-size: 11px;

            }
        }

        /* ---------------------- smaller table css --------------------------- */

        @media (max-width: 650px) {
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
        }

        @media (max-width: 430px) {




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

        .employee-images img:not(#empuid-logo, .empuid-logo) {
            width: 100%;
            /*border-radius: 8px;*/
            /*box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);*/
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
    </style>

    <style>
        /* Form Group Styles */
        .form-group {
            margin-bottom: 1.5rem;
            display: grid;
            gap: 0.5rem;
        }

        @media (min-width: 768px) {
            .form-group {
                grid-template-columns: 200px 1fr;
                align-items: center;
            }
        }

        /* Form Labels */
        .form-group label {
            font-weight: 500;
            color: #333;
            font-size: 0.95rem;
        }

        /* Form Inputs */
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #3C91E6;
            outline: none;
            box-shadow: 0 0 0 2px rgba(60, 145, 230, 0.1);
        }

        /* Preview Container */
        .preview-container {
            margin-top: 0.5rem;
        }

        .preview-image {
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .preview-image:hover {
            transform: scale(1.05);
        }

        /* Form Buttons */
        .form-buttons {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }

        .btn-primary {
            background: #3C91E6;
            color: white;
        }

        .btn-primary:hover {
            background: #2d7ac7;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
        }

        /* Close Button */
        .close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            opacity: 0.7;
            transition: opacity 0.2s;
        }

        .close:hover {
            opacity: 1;
        }

        /* Responsive Adjustments */
        @media (max-width: 576px) {
            .modal-content {
                padding: 15px;
                margin: 10px;
                width: auto;
            }

            .form-buttons {
                justify-content: center;
            }

            .btn {
                width: 100%;
            }
        }
        
        
#content main .box-info li:nth-child(1) .bx {
	background: #CFE8FF;
	color: #46abcc;
}

#content main .box-info li:nth-child(2) .bx {
	background: #FFF2C6;
	color: #FFCE26;
}


    </style>
    <!-- Add SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />
    <title>Employee Management</title>
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
            <li class="active">
                <a href="./">
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
                            <a class="active" href="./">Employee Management</a>
                        </li>
                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>



            <ul class="box-info">
                <li>
                    <i class='bx bxs-user-plus'></i>
                    <span class="text">
                        <h3>

                        </h3>
                        <a id="CU" href="addEmp">Add Employee</a>
                    </span>
                </li>

                <li>
                    <i class='bx bxs-user-x' style="background: #ffd0d0;
    color: #d80000;"></i>
                    <span class="text">
                        <h3>

                        </h3>
                        <a id="RU" href="exEmployee">Ex-Employee's</a>
                    </span>
                </li>
            </ul>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Employee Information</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th></th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Joining Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="employee-table-body">
                            <!-- Employee data will be populated here via JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>


        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <!-- Employee Details Modal -->
    <dialog id="employeeModal">
        <div class="modal-content" style="padding: 20px;">
            <button class="close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; border: none; background: none;">&times;</button>
            <h2 style="margin-top: 0;">Employee Details</h2>
            <div class="modal-table-container">
                <div class="employee-images" style="display: flex; flex-direction: column; gap: 20px;">
                    <div  style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <h4>Profile Photo</h4>
                        <img class="modalProfilePhoto" src="" alt="Profile Photo" style="width: 200px; height: 200px; object-fit: cover; border-radius: 8px;">
                    </div>
                    <div  style="display: flex; flex-direction: column; justify-content: center; align-items: center;">
                        <h4>Identity Proof</h4>
                        <img id="modalAadharImage" src="" alt="Aadhar Card" style="width: 200px; height: auto; object-fit: contain; border-radius: 8px;">
                    </div>
                    <div id="empuid-card" style="background: linear-gradient(135deg, #ffffff, #f8f9fa); 
                        width: 54mm;
                        height: 86mm;
                        padding: 6px 6px 4px;
                        border-radius: 8px;
                        box-shadow: 0 10px 25px rgba(0,0,0,0.08);
                        text-align: center;
                        position: relative;
                        overflow: hidden;
                        margin: 0 auto;
                        display: flex;
                        flex-direction: column;
                        justify-content: space-between;
                        box-sizing: border-box;
                        border: 1px solid rgba(0,0,0,0.05);">
                        <!-- Enhanced Decorative Elements -->
                        <div style="position: absolute; top: -40px; right: -40px; width: 120px; height: 120px; background: linear-gradient(135deg, rgba(13, 110, 253, 0.08), transparent); border-radius: 100%; z-index: 0; transform: rotate(-15deg);"></div>
                        <div style="position: absolute; top: -20px; right: -20px; width: 80px; height: 80px; background: linear-gradient(135deg, rgba(13, 110, 253, 0.05), transparent); border-radius: 100%; z-index: 0; transform: rotate(15deg);"></div>
                        
                        <div style="position: absolute; bottom: -40px; left: -40px; width: 120px; height: 120px; background: linear-gradient(315deg, rgba(13, 110, 253, 0.08), transparent); border-radius: 100%; z-index: 0; transform: rotate(15deg);"></div>
                        <div style="position: absolute; bottom: -20px; left: -20px; width: 80px; height: 80px; background: linear-gradient(315deg, rgba(13, 110, 253, 0.05), transparent); border-radius: 100%; z-index: 0; transform: rotate(-15deg);"></div>
                        
                         <!-- Additional Corner Accents -->
                        <!-- <div style="position: absolute; top: 10px; left: 10px; width: 20px; height: 20px; border-top: 2px solid rgba(13, 110, 253, 0.1); border-left: 2px solid rgba(13, 110, 253, 0.1); 
                        z-index: 1;"></div>
                        <div style="position: absolute; top: 10px; right: 10px; width: 20px; height: 20px; border-top: 2px solid rgba(13, 110, 253, 0.1); border-right: 2px solid rgba(13, 110, 253, 0.1); 
                        z-index: 1;"></div>
                        <div style="position: absolute; bottom: 10px; left: 10px; width: 20px; height: 20px; border-bottom: 2px solid rgba(13, 110, 253, 0.1); border-left: 2px solid rgba(13, 110, 253, 0.
                        1); z-index: 1;"></div>
                        <div style="position: absolute; bottom: 10px; right: 10px; width: 20px; height: 20px; border-bottom: 2px solid rgba(13, 110, 253, 0.1); border-right: 2px solid rgba(13, 110, 253, 0.
                        1); z-index: 1;"></div>  -->


                        <!-- Subtle Background Pattern -->
                        <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background-image: radial-gradient(circle at 50% 50%, rgba(13, 110, 253, 0.03) 1px, transparent 1px); background-size: 20px 20px; z-index: 0; opacity: 0.5;"></div>
                        
                        <!-- Top Section: Logo + Photo + Title -->
                        <!--<div style="flex: 0 0 auto;">-->
                             <!--Header with logo -->
                        <!--    <div style="position: relative; z-index: 1; display: flex; align-items: center; justify-content: center; margin-bottom: 2px;">-->
                        <!--        <img id="empuid-logo" src="https://i.postimg.cc/BQvDYtCZ/PMDS-text-B.png" alt="Company Logo" style="width: 85px; height: auto;">-->
                        <!--    </div>-->
                            
                             <!--Profile photo with border -->
                        <!--    <div style="position: relative; z-index: 1; width: 60px; height: 60px; margin: 0 auto 2px; border-radius: 50%; padding: 2px; background: linear-gradient(145deg, #0d6efd, #0dcaf0); box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);">-->
                        <!--        <img class="modalProfilePhoto" src="" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 1px solid white;">-->
                        <!--    </div>-->
                            
                             <!--ID Card title -->
                        <!--    <div style="position: relative; z-index: 1; margin-bottom: 2px;">-->
                        <!--        <h3 style="color: #212529; font-size: 12px; font-weight: 600; margin: 0 0 2px; letter-spacing: 0.5px;">Employee ID Card</h3>-->
                        <!--        <div style="width: 25px; height: 2px; background: linear-gradient(to right, #0d6efd, #0dcaf0); margin: 0 auto;"></div>-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        
                          <!-- Top Section: Logo + Photo + Title -->
                        <div style="flex: 0 0 auto;">
                             <!--Header with logo -->
                            <div style="position: relative; z-index: 1; display: flex; align-items: center; justify-content: center; margin-bottom: 2px;">
                                <img id="empuid-logo" src="../../assets/name2.png" alt="Company Logo" style="width: 115px; height: auto; margin-bottom: 0px;">
                            </div>

                              <!--ID Card title -->
                             <div style="position: relative; z-index: 1; margin-bottom: 9px;">
                                <div style="background: linear-gradient(45deg, #0d6efd, #0dcaf0); color: white; font-size: 8px; font-weight: 600; padding: 2px 8px; border-radius: 12px; display: inline-block; text-transform: uppercase; letter-spacing: 0.5px; box-shadow: 0 1px 3px rgba(13, 110, 253, 0.15); border: 1px solid rgba(255,255,255,0.1);">
                                    <img class="empuid-logo" src="../../favicon.ico" style="width: 9px; height: 9px; margin-right: 3px; vertical-align: middle; border-radius: 50%;">
                                    Employee ID Card
                                </div>
                            </div>
                            
                             <!--Profile photo with border -->
                            <div style="position: relative; z-index: 1; width: 60px; height: 60px; margin: 0 auto 8px; border-radius: 50%; padding: 2px; background: linear-gradient(145deg, #0d6efd, #0dcaf0); box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);">
                                <img class="modalProfilePhoto" src="" alt="Profile Photo" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 1px solid white;">
                            </div>
                            
                           
                        </div>
                        
                        <!-- Middle Section: Employee Details + QR -->
                        <div style="flex: 1 1 auto; display: flex; flex-direction: column; justify-content: center; margin: 2px 0;">
                            <!-- Employee details -->
                            <div style="position: relative; z-index: 1; background: linear-gradient(to bottom, rgba(255, 255, 255, 0.95), rgba(248, 249, 250, 0.95)); border: 1px solid rgba(233, 236, 239, 0.8); padding: 5px; border-radius: 8px; margin-bottom: 0; box-shadow: 0 2px 8px rgba(0,0,0,0.04);">
                                <!-- Employee Name and ID with icons -->
                                <div style="margin-bottom: 4px; position: relative;">
                                    <div style="display: flex; align-items: center; justify-content: center; margin-bottom: 1px;">
                                        <i class='bx bxs-user' style="color: #0d6efd; font-size: 11px; margin-right: 3px;"></i>
                                        <h4 style="font-size: 12px; color: #212529; margin: 0; font-weight: 600; letter-spacing: 0.2px;" class="modalName"></h4>
                                    </div>
                                    <div style="display: flex; align-items: center; justify-content: center;">
                                        <i class='bx bxs-id-card' style="color: #0d6efd; font-size: 9px; margin-right: 3px;"></i>
                                        <p style="margin: 0; color: #495057; font-size: 9px;">ID: <strong id="modalID" style="color: #0d6efd; letter-spacing: 0.5px;"></strong></p>
                                    </div>
                                </div>
                                
                                <!-- Divider -->
                                <div style="width: 40px; height: 1px; background: linear-gradient(to right, transparent, rgba(13, 110, 253, 0.2), transparent); margin: 0 auto 4px;"></div>
                                
                                <!-- QR Code section with improved styling -->
                                <!--<div style="background: linear-gradient(to bottom, #ffffff, #f8f9fa); padding: 4px; border-radius: 7px; display: flex; flex-direction: column; align-items: center; border: 1px solid rgba(233, 236, 239, 0.6);">-->
                                <div style="background: linear-gradient(to bottom, #ffffff, #f8f9fa); padding: 4px; border-radius: 7px; display: flex; flex-direction: column; align-items: center;">
                                    <!-- QR Label with icon -->
                                    <div style="display: flex; align-items: center; margin-bottom: 2px;">
                                        <i class='bx bx-scan' style="color: #0d6efd; font-size: 10px; margin-right: 3px;"></i>
                                        <span style="color: #495057; font-size: 8px; font-weight: 500; letter-spacing: 0.3px;">Scan for Attendance</span>
                                    </div>
                                    <!-- QR Code with enhanced container -->
                                    <div style="background: white; padding: 3px; border-radius: 6px; box-shadow: 0 2px 6px rgba(0,0,0,0.03); border: 1px dashed rgba(13, 110, 253, 0.2);">
                                        <img id="modalQRCode" src="" alt="QR Code" style="width: 70px; height: 70px; object-fit: contain; margin: 0 auto; display: block;">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Footer with additional info -->
                        <div style="flex: 0 0 auto; position: relative; z-index: 1; padding-top: 2px; margin-bottom: 2px;">
                            <!-- Divider Line -->
                            <div style="width: 100%; height: 1px; background: linear-gradient(to right, transparent, rgba(13, 110, 253, 0.1), transparent); margin-bottom: 2px;"></div>
                            
                            <!-- Contact Info - Simplified to fit better -->
                            <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 1px;">
                                <!-- Return Info -->
                                <div style="display: flex; align-items: center; gap: 1px;">
                                    <i class='bx bx-map' style="color: #0d6efd; font-size: 7px;"></i>
                                    <span style="font-size: 6px; color: #495057; letter-spacing: 0.1px;">Return to office if found</span>
                                </div>
                                <!-- Divider Dot -->
                                <span style="width: 2px; height: 2px; background: #0d6efd; border-radius: 50%; opacity: 0.5;"></span>
                                <!-- Contact -->
                                <div style="display: flex; align-items: center; gap: 1px;">
                                    <i class='bx bx-phone' style="color: #0d6efd; font-size: 7px;"></i>
                                    <span style="font-size: 6px; color: #495057; letter-spacing: 0.1px;">+91 70166 55237</span>
                                </div>
                            </div>
                            
                            <!-- Company Website -->
                            <div style="display: flex; align-items: center; justify-content: center; gap: 1px;">
                                <i class='bx bx-globe' style="color: #0d6efd; font-size: 7px;"></i>
                                <span style="font-size: 6px; color: #495057; letter-spacing: 0.1px;">www.patelmotordrivingschool.com</span>
                            </div>
                        </div>
                    </div>
                    <button id="downloadButton" style="width: 100%; padding: 14px; background: linear-gradient(to right, #0d6efd, #0b5ed7); color: white; border: none; border-radius: 10px; cursor: pointer; font-size: 1rem; font-weight: 600; transition: all 0.3s; display: flex; align-items: center; justify-content: center; gap: 10px; margin-top: 20px; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);">
                        <i class='bx bx-download'></i>Download ID Card
                    </button>
                    <div style="text-align: center; color: #6c757d; font-size: 12px; display: flex; align-items: center; justify-content: center; gap: 5px;">
                        <!-- <i class='bx bx-ruler' style="font-size: 14px;"></i> -->
                        <span>Standard ID Card Size: 54mm × 86mm (ISO/IEC 7810 ID-1)</span>
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
                    <div class="info-item">
                        <strong>Rejoin Date:</strong>
                        <span id="modalRejoinDate"></span>
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
                    <hr style="grid-column: 1 / -1; margin: 10px 0; border: none; border-top: 1px solid #e2e8f0;">

                    <div class="info-item" style="grid-column: 1 / -1;">

                        <div class="access-status" id="accessStatusContainer">
                            <!-- Content will be populated by AJAX -->
                            <div class="loading">Checking access status...</div>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </dialog>


    <dialog id="editEmployeeModal">
        <div class="modal-content" style="padding: 20px;">
            <button class="close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; border: none; background: none;">&times;</button>
            <h2 style="margin-top: 0;">Edit Employee</h2>
            <form id="editEmployeeForm">
                <input type="hidden" id="editEmployeeId">
                <div class="form-group">
                    <label for="editName">Name:</label>
                    <input type="text" id="editName" name="name">
                </div>
                <div class="form-group">
                    <label for="editPhone">Phone:</label>
                    <input type="tel" id="editPhone" name="phone">
                </div>
                <div class="form-group">
                    <label for="editEmail">Email:</label>
                    <input type="email" id="editEmail" name="email">
                </div>
                <div class="form-group">
                    <label for="editAadhar">Aadhar Number:</label>
                    <input type="text" id="editAadhar" name="aadhar">
                </div>
                <div class="form-group">
                    <label for="editDob">Date of Birth:</label>
                    <input type="date" id="editDob" name="dob">
                </div>
                <div class="form-group">
                    <label for="editGender">Gender:</label>
                    <select id="editGender" name="gender">
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editRole">Role:</label>
                    <select id="editRole" name="role">
                        <option value="admin">admin</option>
                        <option value="staff">staff</option>
                        <option value="trainer">trainer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="editJoiningDate">Joining Date:</label>
                    <input type="date" id="editJoiningDate" name="joining_date">
                </div>
                <div class="form-group">
                    <label for="editAddress">Address:</label>
                    <textarea id="editAddress" name="address"></textarea>
                </div>
                <div class="form-group">
                    <label for="editPhoto">Profile Photo:</label>
                    <div class="preview-container">
                        <a href="#" id="editPhotoPreview" data-fancybox="profile-photo">
                            <img src="" alt="Profile Photo Preview" style="max-width: 150px; display: none;" class="preview-image">
                        </a>
                    </div>
                    <input type="file" id="editPhoto" name="photo" accept="image/*" onchange="previewImage(this, '#editPhotoPreview')">
                </div>
                <div class="form-group">
                    <label for="editAadharImage">Identity Proof Image:</label>
                    <div class="preview-container">
                        <a href="#" id="editAadharPreview" data-fancybox="aadhar-image">
                            <img src="" alt="Aadhar Image Preview" style="max-width: 150px; display: none;" class="preview-image">
                        </a>
                    </div>
                    <input type="file" id="editAadharImage" name="aadhar_image" accept="image/*" onchange="previewImage(this, '#editAadharPreview')">
                </div>

                <div class="form-buttons" style="margin-top: 20px;">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                </div>
            </form>
        </div>
    </dialog>




    <script src="../../js/sweetalert.js"></script>
    <script src="../../js/toggleSideBar.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/hideSideBar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
    <script>
        function previewImage(input, previewSelector) {
            const preview = document.querySelector(`${previewSelector} img`);
            const link = document.querySelector(previewSelector);

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                    link.href = e.target.result;
                    preview.style.display = 'block';
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        Fancybox.bind("[data-fancybox]", {
            // Your custom options
        });
    </script>

    <script>
        // Edit Employee Form Submission
        $('#editEmployeeForm').on('submit', function(e) {
            e.preventDefault();

            const formData = new FormData();
            formData.append('id', $('#editEmployeeId').val());
            formData.append('name', $('#editName').val());
            formData.append('phone', $('#editPhone').val());
            formData.append('email', $('#editEmail').val());
            formData.append('aadhar', $('#editAadhar').val());
            formData.append('dob', $('#editDob').val());
            formData.append('gender', $('#editGender').val());
            formData.append('role', $('#editRole').val());
            formData.append('joining_date', $('#editJoiningDate').val());
            formData.append('address', $('#editAddress').val());

            // Add photo file if selected
            const photoFile = $('#editPhoto')[0].files[0];
            if (photoFile) {
                formData.append('photo', photoFile);
            }

            // Add aadhar image file if selected  
            const aadharFile = $('#editAadharImage')[0].files[0];
            if (aadharFile) {
                formData.append('aadhar_image', aadharFile);
            }

            $.ajax({
                url: '../../api_ajax/update_employee.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        document.getElementById('editEmployeeModal').close();
                        // Clear image previews
                        $('#editPhotoPreview img').attr('src', '').hide();
                        $('#editAadharPreview img').attr('src', '').hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Employee updated successfully',
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            document.getElementById('editEmployeeModal').close();
                            fetchEmployees();
                        });
                    } else {
                        document.getElementById('editEmployeeModal').close();
                        // Clear image previews
                        $('#editPhotoPreview img').attr('src', '').hide();
                        $('#editAadharPreview img').attr('src', '').hide();
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to update employee',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    document.getElementById('editEmployeeModal').close();
                    // Clear image previews
                    $('#editPhotoPreview img').attr('src', '').hide();
                    $('#editAadharPreview img').attr('src', '').hide();
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to update employee. Please try again.',
                        confirmButtonColor: '#3085d6'
                    });
                    console.error('Error:', error);
                }
            });
        });

        // Close modal handlers
        $('.close, .close-modal').on('click', function() {
            // Clear image previews
            $('#editPhotoPreview img').attr('src', '').hide();
            $('#editAadharPreview img').attr('src', '').hide();
            // Close modal
            document.getElementById('editEmployeeModal').close();
        });

        // Edit button click handler
        $(document).on('click', '.edit-btn', function() {
            const employeeId = $(this).data('id');

            // Fetch employee details
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

                        // Populate form fields
                        $('#editEmployeeId').val(employee.id);
                        $('#editName').val(employee.name);
                        $('#editPhone').val(employee.phone);
                        $('#editEmail').val(employee.email);
                        $('#editAadhar').val(employee.aadhar);
                        $('#editDob').val(employee.dob);
                        $('#editGender').val(employee.gender);
                        // Set the role dropdown value and ensure it exists in options
                        $('#editRole').val((employee.role || '').toLowerCase());

                        $('#editJoiningDate').val(employee.joining_date);
                        $('#editAddress').val(employee.address);

                        // Show current images in preview
                        if (employee.photo) {
                            $('#editPhotoPreview img').attr('src', employee.photo).show();
                            $('#editPhotoPreview').attr('href', employee.photo);
                        }

                        if (employee.aadhar_image) {
                            $('#editAadharPreview img').attr('src', employee.aadhar_image).show();
                            $('#editAadharPreview').attr('href', employee.aadhar_image);
                        }

                        // Show modal
                        document.getElementById('editEmployeeModal').showModal();
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
                        text: 'Failed to fetch employee details. Please try again.',
                        confirmButtonColor: '#3085d6'
                    });
                    console.error('Error:', error);
                }
            });
        });
    </script>

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
                        $('#modalId').text(employee.emp_uid || 'N/A');
                        $('#modalID').text(employee.emp_uid || 'N/A');
                        $('.modalName').text(employee.name || 'N/A')
                        $('#modalPhone').text(employee.phone || 'N/A');
                        $('#modalEmail').text(employee.email || 'N/A');
                        $('#modalAadhar').text(employee.aadhar || 'N/A');
                        $('#modalDob').text(employee.dob || 'N/A');
                        $('#modalGender').text(employee.gender || 'N/A');
                        $('#modalRole').text(employee.role || 'N/A');
                        $('#modalJoiningDate').text(employee.joining_date || 'N/A');
                        $('#modalAddress').text(employee.address || 'N/A');
                        $('#modalCreatedAt').text(employee.created_at || 'N/A');
                        $('#modalUpdatedAt').text(employee.updated_at || 'N/A');
                        $('#modalLeavingDate').text(employee.leaving_date || 'N/A');
                        $('#modalRejoinDate').text(employee.rejoin_date || 'N/A');

                        // Set images
                        $('.modalProfilePhoto').attr('src', employee.photo || '../../assets/Default_Profile.png');
                        $('#modalAadharImage').attr('src', employee.aadhar_image || '../../assets/default_aadhaar_card.jpg');
                        // --- START REPLACEMENT ---
                        const plainEmpUid = employee.emp_uid; // Get the plain emp_uid from the fetched data
                        if (plainEmpUid) {
                            // Construct the URL to the QR generator script, passing the plain emp_uid as data
                            const qrGeneratorUrl = `../../api_ajax/generate_qr.php?data=${encodeURIComponent(plainEmpUid)}&size=8&margin=1`; // Adjust size/margin if needed
                            $('#modalQRCode').attr('src', qrGeneratorUrl); // Set the image source to the generator script
                            $('#modalQRCode').attr('alt', 'Employee Attendance QR Code'); // Update alt text
                            // Optionally update the download button if needed (depends on its implementation)
                            // Assuming downloadButton uses the image src directly:
                            $('#downloadButton').off('click').on('click', function() { downloadQrImage($('#modalQRCode').attr('src'), plainEmpUid); });

                        } else {
                            // Fallback if emp_uid is missing for some reason
                            $('#modalQRCode').attr('src', '../../assets/default-qr.jpg');
                            $('#modalQRCode').attr('alt', 'Employee UID missing');
                            $('#downloadButton').off('click'); // Disable download if no UID
                        }
                        // --- END REPLACEMENT ---

                        // Also, you might need a helper function for download if the original relied on a simple filename
                        function downloadQrImage(qrUrl, empUid) {
                            const link = document.createElement('a');
                            // Use fetch to get the blob if direct download fails cross-origin or due to headers
                            fetch(qrUrl)
                            .then(response => response.blob())
                            .then(blob => {
                                const blobUrl = URL.createObjectURL(blob);
                                link.href = blobUrl;
                                link.download = `employee_qr_${empUid}.png`;
                                document.body.appendChild(link);
                                link.click();
                                document.body.removeChild(link);
                                URL.revokeObjectURL(blobUrl); // Clean up blob URL
                            })
                            .catch(error => console.error('Error fetching QR for download:', error));
                        }

                        // Show modal using the showModal() method

                        checkSoftwareAccess(employee.emp_uid);

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

        // Handle ex-employee button click
        $(document).on('click', '.ex-emp-btn', function() {
            const employeeId = $(this).data('id');

            Swal.fire({
                title: 'Mark as Ex-Employee?',
                text: "This will mark the employee as inactive. Continue?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, mark as ex-employee'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '../../api_ajax/mark_ex_employee.php',
                        method: 'POST',
                        data: {
                            id: employeeId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success',
                                    text: 'Employee marked as inactive successfully',
                                    confirmButtonColor: '#3085d6'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: response.message || 'Failed to mark employee as inactive',
                                    confirmButtonColor: '#3085d6'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to process request. Please try again later.',
                                confirmButtonColor: '#3085d6'
                            });
                            console.error('Error:', error);
                        }
                    });
                }
            });
        });
    </script>
    <script>
        function checkSoftwareAccess(empId) {
            $.ajax({
                url: '../../api_ajax/checkSoftwareAccess.php',
                type: 'POST',
                data: {
                    emp_id: empId
                },
                success: function(response) {
                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                    const container = $('#accessStatusContainer');
                    container.empty(); // Clear existing content

                    if (data.hasAccess) {
                        container.html(`
                            <div class="access-granted">
                                <div class="access-status">
                                    <i class="fas fa-check-circle"></i>
                                    <strong>Software Access Granted</strong>
                                </div>
                                <div class="access-details">
                                    <p><strong>Username:</strong> ${data.username}</p>
                                    <p><strong>Name:</strong> ${data.name}</p>
                                    <p><strong>Permissions:</strong> ${data.permissions}</p>
                                    <p><strong>Account Created:</strong> ${data.time}</p>
                                </div>
                            </div>
                        `);
                    } else {
                        container.html(`
                            <div class="access-denied">
                                <span class="access-status">
                                    <i class="fas fa-times-circle"></i>
                                    <strong>No Software Access</strong>
                                </span>
                                <button onclick="createSoftwareAccount('${empId}')" 
                                    class="btn btn-success create-access-btn">
                                    <i class="fas fa-user-plus"></i>
                                    Create Access
                                </button>
                            </div>
                        `);
                    }
                },
                error: function(xhr, status, error) {
                    $('#accessStatusContainer').html(`
                        <div class="access-error">  
                            <span class="access-status">
                                <i class="fas fa-exclamation-circle"></i>
                                <strong>Error checking access status</strong>
                            </span>
                            <p class="error-message">${error}</p>
                        </div>
                    `);
                }
            });
        }

        function createSoftwareAccount(empId) {
            // Redirect to the create user page
            window.location.href = '../manageUsers/createUser?emp_id=' + empId;
        }
    </script>

    <script>
        $(document).ready(function() {
            fetchEmployees();
        });

        function fetchEmployees() {
            // Show loader
            const tableBody = $('#employee-table-body');
            tableBody.html(`
                <tr>
                    <td colspan="7" style="text-align: center;">
                        <div class="loading-container">
                            <div class="loading-spinner"></div>
                            <div class="loading-text">
                                <p>Loading employees...</p>
                                <p class="loading-subtext">Please wait while we fetch the data</p>
                            </div>
                            <style>
                                .loading-container {
                                    padding: 1rem;
                                    width: 100%;
                                    max-width: 300px;
                                    margin: 0 auto;
                                }
                                .loading-spinner {
                                    width: 40px;
                                    height: 40px;
                                    border: 4px solid #f3f3f3;
                                    border-top: 4px solid #3C91E6;
                                    border-radius: 50%;
                                    margin: 0 auto;
                                    animation: spin 1s linear infinite;
                                }
                                .loading-text {
                                    margin-top: 0.8rem;
                                }
                                .loading-text p {
                                    margin: 0;
                                    color: #3C91E6;
                                    font-size: 1rem;
                                    font-weight: 500;
                                }
                                .loading-subtext {
                                    font-size: 0.8rem !important;
                                    color: #666 !important;
                                    margin-top: 0.3rem !important;
                                }
                                @media screen and (max-width: 480px) {
                                    .loading-container {
                                        padding: 0.8rem;
                                    }
                                    .loading-spinner {
                                        width: 35px;
                                        height: 35px;
                                        border-width: 3px;
                                    }
                                    .loading-text p {
                                        font-size: 0.9rem;
                                    }
                                    .loading-subtext {
                                        font-size: 0.75rem !important;
                                    }
                                }
                                @keyframes spin {
                                    0% { transform: rotate(0deg); }
                                    100% { transform: rotate(360deg); }
                                }
                            </style>
                        </div>
                    </td>
                </tr>
            `);

            $.ajax({
                url: '../../api_ajax/get_employees.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    tableBody.empty();
                    console.log(response);
                    if (response.success && response.employees.length > 0) {
                        response.employees.forEach(employee => {
                            const row = `
                        <tr>
                            <td data-cell="Photo">
                                <img src="${employee.photo || '../../assets/Default_Profile.png'}" alt="${employee.name}" 
                                     style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover;">
                            </td>
                            <td data-cell="Name">${employee.name}</td>
                            <td data-cell="Phone">${employee.phone}</td>
                            <td data-cell="Email">${employee.email}</td>
                            <td data-cell="Role">
                                <span style="color: black;">${employee.role}</span>
                            </td>
                            <td data-cell="Joining Date">${employee.joining_date}</td>
                            <td data-cell="Action">
                                <div class="action-buttons">
                                    <i class='bx bxs-edit edit-btn' data-id='${employee.id}' 
                                       style="cursor: pointer; color: #3C91E6; font-size: 1.2rem; margin-right: 8px;"></i>
                                    <i class='bx bxs-show view-btn' data-id='${employee.id}'
                                       style="cursor: pointer; color: #3C91E6; font-size: 1.2rem; margin-right: 8px;"></i>
                                    <i class='bx bx-user-x ex-emp-btn' data-id='${employee.id}'
                                       style="cursor: pointer; color: #ff2626; font-size: 1.2rem;"></i>
                                </div>
                            </td>
                            
                        </tr>
                    `;
                            tableBody.append(row);
                        });


                    } else {
                        tableBody.append('<tr><td colspan="7" style="text-align: center;">No employees found</td></tr>');
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to fetch employee data. Please try again later.',
                        confirmButtonColor: '#3085d6'
                    });
                    console.error('Error:', error);
                }
            });
        }



        function deleteEmployee(employeeId) {
            $.ajax({
                url: '../../api_ajax/delete_employee.php',
                method: 'POST',
                data: {
                    id: employeeId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Employee deleted successfully',
                            confirmButtonColor: '#3085d6'
                        }).then(() => {
                            fetchEmployees(); // Refresh the table
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message || 'Failed to delete employee',
                            confirmButtonColor: '#3085d6'
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to delete employee. Please try again later.',
                        confirmButtonColor: '#3085d6'
                    });
                    console.error('Error:', error);
                }
            });
        }
    </script>

    <script>
        const captureElement = document.getElementById('empuid-card');
        const downloadButton = document.getElementById('downloadButton');

        if (captureElement && downloadButton) {
            downloadButton.addEventListener('click', async () => {
                // Show loading state
                downloadButton.disabled = true;
                downloadButton.innerHTML = '<i class="bx bx-loader-alt bx-spin"></i>Generating High Quality Card...';

                try {
                    // Wait for all images to load
                    const images = captureElement.getElementsByTagName('img');
                    await Promise.all([...images].map(img => {
                        if (img.complete) return Promise.resolve();
                        return new Promise(resolve => {
                            img.onload = resolve;
                            img.onerror = resolve;
                        });
                    }));

                    // Capture with high quality settings
                    const canvas = await html2canvas(captureElement, {
                        scale: 4, // Increase resolution
                        useCORS: true,
                        allowTaint: true,
                        backgroundColor: null,
                        imageTimeout: 0,
                        logging: false,
                        removeContainer: true,
                        onclone: function(clonedDoc) {
                            // Ensure styles are copied
                            const styles = document.getElementsByTagName('style');
                            for (let style of styles) {
                                clonedDoc.head.appendChild(style.cloneNode(true));
                            }
                        }
                    });

                    // Convert to high quality PNG
                    const image = canvas.toDataURL('image/png', 1.0);
                    
                    // Create download link with employee name
                    const employeeName = document.querySelector('.modalName').textContent.trim();
                    const sanitizedName = employeeName.replace(/[^a-z0-9]/gi, '_').toLowerCase();
                    const timestamp = new Date().toISOString().slice(0,10);
                    const fileName = `id_card_${sanitizedName}_${timestamp}.png`;
                    
                    const link = document.createElement('a');
                    link.download = fileName;
                    link.href = image;
                    link.click();
                } catch (error) {
                    console.error('Error capturing ID card:', error);
                    alert('Failed to generate ID card. Please try again.');
                } finally {
                    // Restore button state
                    downloadButton.disabled = false;
                    downloadButton.innerHTML = '<i class="bx bx-download"></i>Download ID Card';
                }
            });
        }
    </script>
</body>

</html>