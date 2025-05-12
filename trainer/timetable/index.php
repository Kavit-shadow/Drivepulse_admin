<?php

include('../../includes/authenticationTrainer.php');
authenticationTrainer('../../');
date_default_timezone_set('Asia/Kolkata');
// $conn = mysqli_connect("localhost", "root", "", "billing");
include('../../config.php');

$msg = [];

if (isset($_POST['submitCar'])) {

    header("location:?car=" . $_POST['car'] . "");
}

function convertDMY($dateString)
{
    if ($dateString === "0000-00-00") {
        echo "00-00-00";
    } else {

        $date = new DateTime($dateString);
        $formattedDate = $date->format("d-m-y");
        echo $formattedDate;
    }
}



function logActivity($logType, $who, $activity)
{
    date_default_timezone_set('Asia/Kolkata');

    $logFolder = '../../logs/' . $logType;

    if (!file_exists($logFolder)) {
        mkdir($logFolder, 0755, true);
    }

    $logFile = $logFolder . '/logs.json';

    // Read existing log entries from the file, or create an empty array if the file doesn't exist
    $existingLogs = file_exists($logFile) ? json_decode(file_get_contents($logFile), true) : [];

    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'who' => $who,
        'activity' => $activity,
    ];

    // Append the new log entry to the existing logs array
    // $existingLogs[] = $logEntry;
    array_unshift($existingLogs, $logEntry);
    // Save the updated logs array back to the file
    file_put_contents($logFile, json_encode($existingLogs, JSON_PRETTY_PRINT));
}


function updateAllVehicleTable($conn, $table, $name)
{


    date_default_timezone_set('Asia/Kolkata');
    $current_timestamp_by_mktime = mktime(date("m"), date("d"), date("Y"));
    $currentDate = date("Y-m-d", $current_timestamp_by_mktime);

    $check = "SELECT * FROM $table WHERE status = 'active'";
    $result = mysqli_query($conn, $check);
    // echo $currentDate."<br>";
    if (!$result) {
        die("Error executing query: " . mysqli_error($conn));
    }

    if (mysqli_num_rows($result) > 0) {
        // Loop through each row
        while ($row = mysqli_fetch_assoc($result)) {
            $endDate = date('Y-m-d', strtotime($row['end_date'] . ' +2 days'));

            $cust_phone = $row['phone'];
            $cust_details_query = "SELECT * FROM cust_details WHERE phone = '$cust_phone'";
            $cust_details_result = mysqli_query($conn, $cust_details_query);
            $cust_details = mysqli_fetch_assoc($cust_details_result);
            $cust_days = $cust_details['days'];
            $cust_id = $cust_details['id'];
            $cust_uid = $cust_details['cust_uid'];
            
            // Get attendance count for this customer
            $count_sql = "SELECT COUNT(*) as attendance_count FROM customer_attendance WHERE cust_id = ? OR cust_uid = ?";
            $count_stmt = $conn->prepare($count_sql);
            $count_stmt->bind_param("ss", $cust_id, $cust_uid);
            $count_stmt->execute();
            $count_result = $count_stmt->get_result();
            $count_row = $count_result->fetch_assoc();
            $attendance_count = $count_row['attendance_count'];
            $count_stmt->close();

            if ($attendance_count >= $cust_days) {
                $id = $row['id'];
                $update = "UPDATE $table SET name='', phone='', vehicle='', trainer='', start_date='', end_date='', status='empty' WHERE id = '$id'";
                $updateResult = mysqli_query($conn, $update);

                if (!$updateResult) {
                    die("Error updating row: " . mysqli_error($conn));
                } else {
                    logActivity('admin_logs', "System", "Name: " . $row['name'] . " Phone: " . $row['phone'] . " Training Has been Ended in $name car TimeSlot was : " . $row['timeslots'] . " (Attendance Completed)");
                    $msg[] = "Rows updated successfully. $name"; 
                }
            }
            if($row['end_date'] == $currentDate && $attendance_count < $cust_days){
                logActivity('admin_logs', "System", "Alert: " . $row['name'] . " (Ph: " . $row['phone'] . ") training ends today. System will remove from " . $name . " timeslot " . $row['timeslots'] . " in 2 days if attendance incomplete (" . $attendance_count . "/" . $cust_days . " days completed)");
            }
            
            // echo $endDate."<br>";

            if ($endDate < $currentDate) {
                $id = $row['id'];

                $update = "UPDATE $table SET name='', phone='', vehicle='', trainer='',  start_date='', end_date='', status='empty' WHERE id = '$id'";
                $updateResult = mysqli_query($conn, $update);

                if (!$updateResult) {
                    die("Error updating row: " . mysqli_error($conn));
                } else {
                    logActivity('admin_logs', "System", "Name: " . $row['name'] . " Phone: " . $row['phone'] . " Training Has been Ended in $name car TimeSlot was : " . $row['timeslots'] . " (End Date Passed)");
                    $msg[] = "Rows updated successfully. $name";
                    // echo "<script>alert('Rows updated successfully in i10')</script>";
                }
            }
        }
    } else {
        $msg[] = "No rows to update. $name";
        // echo "<script>alert('No rows to update in i10')</script>";

    }
}




function enterBook($connection, $timeslot, $vehicle, $name, $phone, $carTable)
{
    $query = "SELECT * FROM pre_book_queue WHERE name = '$name' AND phone = '$phone' AND timeslot = '$timeslot' AND vehicle = '$vehicle' AND priority = '1' ORDER BY id ASC LIMIT 1";
    $result = mysqli_query($connection, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $id = $row['id'];


        $selectCust = "SELECT * FROM `cust_details` WHERE name = '" . $row['name'] . "' AND phone = '" . $row['phone'] . "'";
        $r = mysqli_query($connection, $selectCust);
        $vehDetails = mysqli_fetch_assoc($r);


        $update = "UPDATE `$carTable` SET name='" . $row['name'] . "', phone='" . $row['phone'] . "', vehicle='" . $vehDetails['vehicle'] . "', trainer='" . $row['trainer'] . "',  start_date='" . $row['start_date'] . "', end_date='" . $row['end_date'] . "', status='active' WHERE timeslots = '$timeslot'";


        $del = "DELETE FROM `pre_book_queue` WHERE id = $id";
        $delResult = mysqli_query($connection, $del);
        $updateResult = mysqli_query($connection, $update);


        if (isset($updateResult) && isset($delResult)) {
            // Update the priorities of the remaining items with the same timeslot
            $updateQuery = "UPDATE pre_book_queue SET priority = priority - 1 WHERE timeslot = '$timeslot' AND vehicle = '$vehicle' AND priority > 1";
            mysqli_query($connection, $updateQuery);

            return $row;
        } else {
            echo "Error: " . mysqli_error($connection);
            return null;
        }
    } else {
        return null;
    }
}

function carPreBookCheck($conn, $checkPreBookQueue, $carTable, $preg)
{

    $resultPreBookQueue = mysqli_query($conn, $checkPreBookQueue);

    if (mysqli_num_rows($resultPreBookQueue) > 0) {
        while ($row = mysqli_fetch_assoc($resultPreBookQueue)) {

            $CheckEmpty = "SELECT * FROM `$carTable` WHERE timeslots = '" . $row['timeslot'] . "'";
            $data = mysqli_fetch_assoc(mysqli_query($conn, $CheckEmpty));

            $text = $row['vehicle'];

            if (preg_match('/\b' . $preg . '\b/', $text, $matches)) {
                $i10String = $matches[0];

                if ($data['status'] === "empty") {
                    $log = enterBook($conn, $row['timeslot'], $row['vehicle'], $row['name'], $row['phone'], $carTable);
                    if ($log != null) {
                        logActivity('admin_logs', "System", "Name: " . $log['name'] . " Phone: " . $log['phone'] . " Has been added to " . $preg . " Timetable TimeSlot is : " . $log['timeslot']);
                    }
                }
            }
        }
    }
}



// Checking Pre Book & Updateing TimeTables 
function callAgain($conn)
{
    $checkPreBookQueue = "SELECT * FROM pre_book_queue";
    $tables = mysqli_fetch_all(mysqli_query($conn, "SELECT data_base_table FROM vehicles"));

    // print_r($tables);

    foreach ($tables as $table) {
        $vehicleNAME = mysqli_fetch_array(mysqli_query($conn, "SELECT vehicle_name  FROM vehicles WHERE data_base_table = '" . $table[0] . "'"));
        carPreBookCheck($conn, $checkPreBookQueue, $table[0], $vehicleNAME[0]);
    }
    foreach ($tables as $table) {
        $vehicleNAME = mysqli_fetch_array(mysqli_query($conn, "SELECT vehicle_name  FROM vehicles WHERE data_base_table = '" . $table[0] . "'"));
        updateAllVehicleTable($conn, $table[0], $vehicleNAME[0]);
    }
}
for ($i = 0; $i < 4; $i++) {
    callAgain($conn);
}




include("../../config.php");

// Fetch the data from the database
$query = "SELECT * FROM vehicles";
$result = mysqli_query($conn, $query);

// Check if the query was successful
if (!$result) {
    die("Database query failed: " . mysqli_error($conn));
}

// Generate the options for the select element
$options = "<option disabled selected>Select Car To View Time-Table</option>";

while ($row = mysqli_fetch_assoc($result)) {
    $tableName = htmlspecialchars($row['data_base_table']); // Sanitize output
    $displayName = htmlspecialchars($row['vehicle_name']); // You can customize the display name if needed
    $options .= "<option value='$tableName'>$displayName</option>";
}

mysqli_close($conn);


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
        body {
            scroll-behavior: smooth;
        }

        .title {
            text-align: center;
            padding: 13px;
            background: #46abcc;
            color: rgb(255, 255, 255);
            font-size: 20px;
            font-weight: 700;
            border-radius: 10px;
            /* box-shadow: 7px 7px 2px 1px rgba(0, 0, 0, 0.2); */
        }

        table {
            width: 100%;
            max-width: 1500px;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table th,
        table td {
            font-size: 15px;
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        table tr:hover {
            background-color: #f5f5f5;
        }

        h1 {
            text-align: center;
        }

        form {
            max-width: 100%;
            margin: 30px 0px 5px 0px;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: bold;
        }

        select {
            width: 100%;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        button[type="submit"] {
            background-color: #4CAF50;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        button[type="submit"]:hover {
            background-color: #45a049;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {

            table th,
            table td {
                font-size: 14px;
            }

            h1 {
                font-size: 24px;
            }

            form {
                max-width: 100%;
                padding: 10px;
            }

            select {
                padding: 6px;
            }

            button[type="submit"] {
                padding: 8px 16px;
                font-size: 14px;
            }



            .plusDay:hover {
                background-color: #45a049;
            }




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




        #car {
            padding: 20px;
            font-size: 17px;
            font-weight: bold;
            text-align: center;
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

        .btns {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 10px;

        }

        .btn {
            text-align: center;
            display: inline-block;
            padding: 5px 10px;
            background-color: #46abcc;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            flex-grow: 3;
            border: none;
        }

        #SD,
        #ED,
        #TS {
            font-size: 13px;
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

        #showTimeTableBtn {
            margin-top: 20px;
            background: #46abcc;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            transition: background 0.2s;
            
        }

        #showTimeTableBtn:hover {
            background: #3d92ae;
        }

        #showTimeTableBtn i {
            font-size: 18px;
        }

        @media screen and (max-width: 768px) {
            #showTimeTableBtn {
                width: 100%;
                justify-content: center;
                font-size: 14px;
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
    <title>Time Table</title>
</head>

<body>


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
             <li>
                <a href="../todaySchedule">
                    <i class='bx bx-calendar'></i>
                    <span class="text">Today's Schedule</span>
                </a>
            </li>
            <li class="active time-table">
                <a href="./">
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

        <script>
            // TOGGLE SIDEBAR
            const menuBar = document.querySelector('#content nav .bx.bx-menu');
            const sidebar = document.getElementById('sidebar');
            const sideBarFooter = document.getElementById("dev-footer");

            menuBar.addEventListener('click', function() {

                sidebar.classList.toggle('hide');
                sideBarFooter.classList.toggle('hide-footer')

            })

            function checkWidthAndAddClass() {
                const element = document.getElementById('sidebar');
                const sideBarFooter = document.getElementById("dev-footer");
                if (window.innerWidth <= 768) {
                    element.classList.add('hide');
                    sideBarFooter.classList.add('hide-footer')

                } else {
                    element.classList.remove('hide');
                    sideBarFooter.classList.remove('hide-footer')
                }
            }


            checkWidthAndAddClass();

            window.addEventListener('resize', checkWidthAndAddClass);
        </script>

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Time Table</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./">Time Table</a>
                        </li>
                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>

            <?php
            if (isset($msg)) {
                foreach ($msg as $msg) {
                    echo '<span class="msg">' . $msg . '</span>';
                };
            };
            ?>

            <div class="time_table_box">
                <button id="showTimeTableBtn" class="btn-download" style="margin-bottom: 20px;">
                    <i class='bx bx-calendar'></i>
                    <span class="text">Show Simple Timetable</span>
                </button>

                <div style="margin: 20px 0; max-width: 400px; width: 100%;">
                    <label for="car" style="display: block; margin-bottom: 8px; font-size: 16px; font-weight: 500; color: #333;">Select Car:</label>
                    <select id="car" name="car" style="width: 100%; padding: 12px; border: 2px solid #ddd; border-radius: 8px; font-size: 15px; color: #444; background-color: #fff; cursor: pointer; transition: all 0.3s ease; outline: none; -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url('data:image/svg+xml;utf8,<svg fill=\'%23444\' height=\'24\' viewBox=\'0 0 24 24\' width=\'24\' xmlns=\'http://www.w3.org/2000/svg\'><path d=\'M7 10l5 5 5-5z\'/></svg>'); background-repeat: no-repeat; background-position: right 10px center;">
                        <?php echo $options; ?>
                    </select>
                </div>

                <script>
                    // Handle car selection and URL updates
                    const carSelect = document.getElementById('car');
                    
                    carSelect.addEventListener('change', () => {
                        if (carSelect.value) {
                            window.location.href = `?car=${carSelect.value}`;
                        }
                    });

                    // Set initial selection from URL
                    const carParam = new URLSearchParams(window.location.search).get('car');
                    if (carParam) {
                        carSelect.value = carParam;
                    }
                </script>



                <dialog id="timeTableModal">
                    <div class="modal-content" style="padding: 20px;">
                        <button class="close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; border: none; background: none;">&times;</button>
                        <h2 style="margin-top: 0;">Car Timetable</h2>
                        
                        <div class="modal-table-container">
                            <div id="timeTableContent">
                                <!-- Timetable content will be loaded here -->
                            </div>
                          
                        </div>
                    </div>
                </dialog>
                <!-- Add html2canvas and jsPDF libraries -->
                <script src="https://html2canvas.hertzen.com/dist/html2canvas.min.js"></script>
                <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
                <script>
                    // Show timetable button click handler
                    document.getElementById('showTimeTableBtn').addEventListener('click', function() {
                        const urlParams = new URLSearchParams(window.location.search);
                        const carId = urlParams.get('car');
                        console.log(carId);

                        if (carId) {
                            // Load timetable content
                            $.ajax({
                                url: '../../api_ajax/get_car_timetable.php',
                                method: 'GET',
                                data: {
                                    car: carId
                                },
                                success: function(response) {
                                    document.getElementById('timeTableContent').innerHTML = response;
                                    document.getElementById('timeTableModal').showModal();
                                },
                                error: function(xhr, status, error) {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Failed to load timetable. Please try again.',
                                        confirmButtonColor: '#3085d6'
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No Car Selected',
                                text: 'Please select a car first to view its timetable.',
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    });

                    // Close modal when clicking close button or outside
                    document.querySelector('.close').addEventListener('click', function() {
                        document.getElementById('timeTableModal').close();
                    });

                    document.getElementById('timeTableModal').addEventListener('click', function(event) {
                        if (event.target === this) {
                            this.close();
                        }
                    });

                    // Download as PDF
                    document.getElementById('downloadPDF').addEventListener('click', function() {
                        const { jsPDF } = window.jspdf;
                        const content = document.getElementById('timeTableContent');
                        
                        // Get car name from URL for filename
                        const urlParams = new URLSearchParams(window.location.search);
                        const carId = urlParams.get('car');
                        
                        // Get current date for filename
                        const today = new Date();
                        const date = today.getFullYear() + '-' + (today.getMonth()+1) + '-' + today.getDate();
                        
                        // Set options to capture full content
                        const options = {
                            scale: 2, // Increase scale for better quality
                            useCORS: true,
                            scrollX: 0,
                            scrollY: 0,
                            width: content.offsetWidth,
                            height: content.offsetHeight
                        };
                        
                        html2canvas(content, options).then(canvas => {
                            const imgData = canvas.toDataURL('image/png');
                            // Use landscape orientation and A4 size
                            const pdf = new jsPDF({
                                orientation: 'portrait',
                                unit: 'mm',
                                format: 'a4'
                            });
                            const pageWidth = pdf.internal.pageSize.getWidth();
                            const pageHeight = pdf.internal.pageSize.getHeight();
                            
                            // Calculate image dimensions while maintaining aspect ratio
                            const imgWidth = pageWidth - 20; // 10mm margins on each side
                            const imgHeight = (canvas.height * imgWidth) / canvas.width;
                            
                            // Add title
                            pdf.setFontSize(16);
                            pdf.text('Timetable Report', pageWidth/2, 15, {align: 'center'});
                            
                            // Add car name and date
                            pdf.setFontSize(12);
                            pdf.text(`Car: ${carId}`, 10, 25);
                            pdf.text(`Generated on: ${date}`, pageWidth-10, 25, {align: 'right'});
                            
                            // Add table image
                            const xPos = 10; // Left margin
                            const yPos = 35; // Top margin after headers
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
                            
                            pdf.save(`timetable_${carId}_${date}.pdf`);
                        });
                    });

                    // Download as Image
                    document.getElementById('downloadImage').addEventListener('click', function() {
                        const content = document.getElementById('timeTableContent');
                        html2canvas(content).then(canvas => {
                            const link = document.createElement('a');
                            link.download = 'timetable.png';
                            link.href = canvas.toDataURL('image/png');
                            link.click();
                        });
                    });

                    // Print table
                    document.getElementById('printTable').addEventListener('click', function() {
                        const content = document.getElementById('timeTableContent');
                        const printContent = content.innerHTML;
                        
                        // Create an iframe
                        const iframe = document.createElement('iframe');
                        iframe.style.display = 'none';
                        document.body.appendChild(iframe);
                        
                        // Write content to iframe
                        const doc = iframe.contentWindow.document;
                        doc.write('<html><head><title>Timetable Report</title>');
                        doc.write('<style>table{width:100%;border-collapse:collapse;margin-top:20px}th,td{padding:10px;border:1px solid #ddd}th{background-color:#f4f4f4}</style>');
                        doc.write('</head><body>');
                        doc.write(printContent);
                        doc.write('</body></html>');
                        doc.close();
                        
                        // Print iframe content
                        iframe.contentWindow.focus();
                        iframe.contentWindow.print();
                        
                        // Remove iframe after printing
                        setTimeout(() => {
                            document.body.removeChild(iframe);
                        }, 1000);
                    });
                </script>



                <?php
                include('carTable.php');
                ?>



            </div>


        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            $('.addDayBtn').on('click', function() {

                var id = $(this).data('id');
                var car = $(this).data('car');

                $.ajax({
                    url: './addDayAPI.php',
                    method: 'GET',
                    data: {
                        id: id,
                        car: car
                    },
                    success: function(response) {

                        var result = JSON.parse(response);

                        if (result.status === 'success') {

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Date updated successfully. New end date: ' + result.updated_date
                            }).then((result) => {

                                if (result.isConfirmed || result.isDismissed) {

                                    window.location.reload();
                                }
                            });


                        } else {

                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: result.message
                            });
                        }
                    },
                    error: function(xhr, status, error) {

                        Swal.fire({
                            icon: 'error',
                            title: 'AJAX Error',
                            text: 'Error: ' + status + ' - ' + error
                        });
                    }
                });
            });





            $('.subDayBtn').on('click', function() {

                var id = $(this).data('id');
                var car = $(this).data('car');


                $.ajax({
                    url: './subDayAPI.php',
                    method: 'GET',
                    data: {
                        id: id,
                        car: car
                    },
                    success: function(response) {

                        if (response.status === 'success') {

                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'Date updated successfully. New end date: ' + response.updated_date
                            }).then((result) => {
                                if (result.isConfirmed || result.isDismissed) {

                                    window.location.reload();
                                }
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
                            title: 'AJAX Error',
                            text: 'Error: ' + status + ' - ' + error
                        });
                    }
                });
            });



            $('.removeCust').on('click', function() {
                var id = $(this).data('id');
                var car = $(this).data('car');

                Swal.fire({
                    title: 'Are you sure?',
                    text: `Do you really want to remove this customer? This action cannot be undone!`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'No, cancel'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: './removeAPI.php',
                            method: 'GET',
                            data: {
                                id: id,
                                car: car
                            },
                            success: function(response) {
                                console.log(response.status);
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Customer Removed',
                                        text: 'Customer has been successfully removed. The name is: ' + response.name
                                    }).then((result) => {
                                        if (result.isConfirmed || result.isDismissed) {

                                            window.location.reload();
                                        }
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
                                    title: 'AJAX Error',
                                    text: 'Error: ' + status + ' - ' + error
                                });
                            }
                        });
                    }
                });
            });

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
                    text: 'Are you sure you want to mark attendance for this customer ' + custUid + '?',
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