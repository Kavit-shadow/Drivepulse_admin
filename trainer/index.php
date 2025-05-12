<?php

include('../includes/authenticationTrainer.php');
authenticationTrainer('../');
date_default_timezone_set('Asia/Kolkata');




function logActivity($logType, $who, $activity)
{
    date_default_timezone_set('Asia/Kolkata');

    $logFolder = '../logs/' . $logType;

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
            if ($row['end_date'] == $currentDate && $attendance_count < $cust_days) {
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

    <link rel="shortcut icon" type="image/png" href="../assets/logo.png" />
    <link rel="stylesheet" href="../css/sideBarFooter.css">
    <style>
        .container {
            min-height: 20vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 15px;
            gap: 20px;
        }

        .container .form-container {
            width: 100%;
            max-width: 600px;
        }

        .container .form-container input {
            width: 100%;
            height: 45px;
            padding: 10px 15px;
            border: 2px solid #525151;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }

        .container .form-container input:focus {
            border-color: #46abcc;
            box-shadow: 0 0 5px rgba(70, 171, 204, 0.3);
            outline: none;
        }

        #search_result {
            width: 100%;
            max-width: 100%;
            max-height: 70vh;
            overflow-y: auto;
            margin: 0 auto;
            padding: 0 10px;
        }

        #search_result h3 {
            color: #dc3545;
            margin: 8px 0;
            font-size: 16px;
            text-align: center;
        }

        .search-table {
            width: 100%;
            overflow-x: auto;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 3px 8px rgba(0, 0, 0, 0.15);
            margin-bottom: 15px;
        }

        .search-table table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        .search-table table thead th {
            background-color: #f1f3f5;
            color: #2c3e50;
            font-weight: 600;
            padding: 15px 12px;
            text-align: left;
            border-bottom: 2px solid #e9ecef;
            white-space: nowrap;
            font-size: 14px;
        }

        .search-table table tbody td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
            line-height: 1.4;
        }

        .search-table table tbody tr:last-child td {
            border-bottom: none;
        }

        .search-table table tbody tr:hover {
            background-color: #f8f9fa;
            transition: background-color 0.2s ease;
        }

        .view-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #46abcc;
            color: #fff;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s ease;
            text-align: center;
            min-width: 80px;
            box-shadow: 0 2px 4px rgba(70, 171, 204, 0.2);
        }

        .view-btn:hover {
            background-color: #3590ad;
        }

        @media screen and (max-width: 768px) {
            .container {
                padding: 10px;
            }

            .container .form-container {
                max-width: 100%;
            }

            .search-table {
                font-size: 14px;
            }

            .search-table table thead th,
            .search-table table tbody td {
                padding: 8px 10px;
            }

            .view-btn {
                padding: 5px 10px;
                font-size: 12px;
            }
        }

        @media screen and (max-width: 480px) {
            .container .form-container input {
                height: 40px;
                font-size: 14px;
            }

            .search-table {
                font-size: 13px;
            }

            .search-table table thead th,
            .search-table table tbody td {
                padding: 6px 8px;
            }
        }
    </style>
    <style>
        .search-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: #525151;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }

        .search-btn:hover {
            background: #333;
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

        .search-container {
            padding: 0;
            max-width: 100%;
            margin: 0 auto;
        }

        .search-box {
            position: relative;
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .search-box input {
            width: 100%;
            padding: 12px 40px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .search-box input:focus {
            border-color: #525151;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            outline: none;
        }

        .search-icon {
            position: absolute;
            left: 12px;
            color: #666;
        }

        .clear-icon {
            position: absolute;
            right: 12px;
            color: #666;
            cursor: pointer;
            padding: 5px;
        }

        .clear-icon:hover {
            color: #000;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #666;
        }

        @media only screen and (max-width: 600px) {
            .dialog-content {
                width: 95%;
                margin: 20px auto;
            }

            .search-box input {
                padding: 10px 35px;
                font-size: 14px;
            }
        }
    </style>


    <style>
        /* Table Responsive Styles */
        .table-data {
            width: 100%;
            overflow-x: auto;
        }

        .table-data table {
            width: 100%;
            border-collapse: collapse;
            white-space: nowrap;
        }

        .table-data table th,
        .table-data table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table-data table th {
            background-color: #f5f5f5;
            font-weight: 600;
        }

        .table-data table tr:hover {
            background-color: #f9f9f9;
        }

        .table-data .view {
            padding: 6px 12px;
            background: #3C91E6;
            color: #fff;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .table-data .view:hover {
            background: #2d7ac7;
        }

        /* Mobile Responsive Styles */
        @media (max-width: 650px) {
            .table-data table td {
                border: none;
                display: grid;
                grid-template-columns: 15ch auto;
                padding: 0.5rem 1rem;
                font-size: 15px;
            }

            .table-data table tr {
                border-bottom: 1px solid #ccc;
            }

            .table-data table th {
                display: none;
            }

            .table-data table td:first-child {
                padding-top: 2rem;
            }

            .table-data table td:last-child {
                padding-bottom: 2rem;
            }

            .table-data table td::before {
                content: attr(data-cell) ": ";
                font-weight: 700;
                text-transform: capitalize;
            }

            .table-data .view {
                width: 50%;
            }

            #action-cell {
                gap: 5.2rem;
                display: flex;
            }
        }

        /* Small Mobile Devices */
        @media (max-width: 430px) {
            #content main .table-data .head {
                min-width: 200px;
            }

            #content main .table-data .order table {
                min-width: 200px;
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

    <link rel="stylesheet" href="../css/adminDashboard.css">
    <title>Trainer Dashboard</title>
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
            <li class="active">
                <a href="./">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="./todaySchedule">
                    <i class='bx bx-calendar'></i>
                    <span class="text">Today's Schedule</span>
                </a>
            </li>
            <li>
                <a href="./timetable">
                    <i class='bx bx-table'></i>
                    <span class="text">Time Table</span>
                </a>
            </li>
            <li>
                <a href="./myStudents">
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
                <a href="../logout.php" class="logout">
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
                    include("../configWeb.php");
                    echo $WebAppTitle;
                    ?></h3>
                <h5>Dashboard</h5>
            </span>
            <a href="./'" class="profile">
                <img src="../assets/logoBlack.png">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Home</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" href="./">Dashboard</a>
                        </li>

                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>
            <?php
            if (isset($_GET['query'])) {
                $Squery = $_GET['query'];
            }
            ?>

            <ul class="box-info">
                <li>
                    <i class='bx bxs-car' id="active-customers" style="cursor: pointer;"></i>
                    <span class="text">
                        <h3>
                            <?php
                            $DBtables = mysqli_fetch_all(mysqli_query($conn, "SELECT data_base_table FROM vehicles"), MYSQLI_ASSOC);
                            $queries = [];

                            // Loop through each table and create a SELECT statement for each
                            foreach ($DBtables as $table) {
                                $tableName = $table['data_base_table'];
                                $queries[] = "SELECT COUNT(*) AS active_count FROM $tableName WHERE status = 'active'";
                            }

                            // Combine all SELECT statements with UNION ALL
                            $sql = "SELECT SUM(active_count) AS total_active_users FROM (" . implode(" UNION ALL ", $queries) . ") AS combined_tables";

                            // Execute the query
                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                // Output data
                                $row = $result->fetch_assoc();
                                $totalActiveUsers = $row["total_active_users"];
                                echo "" . $totalActiveUsers;
                            } else {
                                echo "No active users found.";
                            }
                            ?>
                        </h3>
                        <p>Active Learners</p>
                    </span>
                </li>
                <li>
                    <i class='bx bxs-user-detail'></i>
                    <span class="text">
                        <h3>
                            <?php
                            $trainer_id = $_SESSION['trainer_ID'];
                            $query = "SELECT COUNT(*) as total FROM cust_details 
                                     WHERE (trainername = (SELECT name FROM employees WHERE emp_uid = (SELECT emp_uid FROM users_db WHERE id = ?))
                                     OR trainerphone = (SELECT phone FROM employees WHERE emp_uid = (SELECT emp_uid FROM users_db WHERE id = ?)))";

                            $stmt = mysqli_prepare($conn, $query);
                            mysqli_stmt_bind_param($stmt, "ii", $trainer_id, $trainer_id);
                            mysqli_stmt_execute($stmt);
                            $result = mysqli_stmt_get_result($stmt);
                            $row = mysqli_fetch_assoc($result);
                            echo $row['total'];
                            mysqli_stmt_close($stmt);
                            ?>
                        </h3>
                        <p>Total Students Trained</p>
                    </span>
                </li>
                <li>
                    <i class='bx bx-search' id="openSearchDialog" style="background: #afb1ffba; color: #6366d9ba; cursor: pointer; transition: all 0.3s ease; padding: 10px; border-radius: 8px;"></i>
                    <span class="text">

                        <h3>Search</h3>
                        <p>Customer</p>

                    </span>
                </li>
            </ul>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Active Learners</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Vehicle</th>
                                <th>Time Slot</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Days Left</th>
                                <!--<th>Trainer</th>-->
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                     <?php
                        // Get all vehicles first
                        $vehicles = mysqli_query($conn, "SELECT vehicle_name, data_base_table FROM vehicles ORDER BY vehicle_name");
                        
                        if ($vehicles && mysqli_num_rows($vehicles) > 0) {
                            while ($vehicle = mysqli_fetch_assoc($vehicles)) {
                                $tableName = $vehicle['data_base_table'];
                                $vehicleName = $vehicle['vehicle_name'];
                                
                                // Query for each vehicle's active customers
                                // $sql = "SELECT 
                                //     CONVERT(t.name USING utf8mb4) AS name,
                                //     CONVERT(t.vehicle USING utf8mb4) AS vehicle,
                                //     t.id,
                                //     CONVERT(t.phone USING utf8mb4) AS phone,
                                //     CONVERT(t.timeslots USING utf8mb4) AS timeslots,
                                //     (SELECT id FROM cust_details cd 
                                //      WHERE CONVERT(cd.phone USING utf8mb4) = CONVERT(t.phone USING utf8mb4)) as cust_id,
                                //     CONVERT(t.status USING utf8mb4) AS status,
                                //     t.start_date,
                                //     t.end_date
                                // FROM $tableName t
                                // WHERE t.status = 'active'";
                                
                                    // Query for each vehicle's active customers
                                $sql = "SELECT 
                                    CONVERT(t.name USING utf8mb4) AS name,
                                    CONVERT(t.vehicle USING utf8mb4) AS vehicle,
                                    t.id,
                                    CONVERT(t.phone USING utf8mb4) AS phone,
                                    CONVERT(t.timeslots USING utf8mb4) AS timeslots,
                                    cd.id AS cust_id,
                                    cd.cust_uid,
                                    CONVERT(t.status USING utf8mb4) AS status,
                                    t.start_date,
                                    t.end_date
                                FROM $tableName t
                                LEFT JOIN cust_details cd ON CONVERT(cd.phone USING utf8mb4) = CONVERT(t.phone USING utf8mb4)
                                WHERE t.status = 'active'";

                                $result = $conn->query($sql);

                                if ($result && $result->num_rows > 0) {
                                    // Print vehicle name as header
                                    echo "<tr><td colspan='8' data-cell='#' style='background-color: #f0f0f0; font-weight: bold; text-align: left; padding: 10px;'>$vehicleName</td></tr>";
                                    
                                    while ($row = $result->fetch_assoc()) {
                                        // Escape output to prevent XSS
                                        $name = htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8');
                                        $phone = htmlspecialchars($row['phone'], ENT_QUOTES, 'UTF-8');
                                        $vehicle = htmlspecialchars($row['vehicle'], ENT_QUOTES, 'UTF-8');
                                        $id = htmlspecialchars($row['cust_id'], ENT_QUOTES, 'UTF-8');
                                        $timeslot = htmlspecialchars($row['timeslots'], ENT_QUOTES, 'UTF-8');
                                        $start_date = date('d M Y', strtotime(htmlspecialchars($row['start_date'], ENT_QUOTES, 'UTF-8')));
                                        $end_date_str = date('d M Y', strtotime(htmlspecialchars($row['end_date'], ENT_QUOTES, 'UTF-8')));

                                        // Calculate days left
                                        $end_date = new DateTime($row['end_date']);
                                        $today = new DateTime();
                                        $days_left = $today->diff($end_date)->days + 1;

                                        echo "<tr>
                                            <td data-cell='Name'>" . $name . "</td>
                                            <td data-cell='Phone'><a href='tel:" . $phone . "'>" . $phone . "</a></td>
                                            <td data-cell='Vehicle'>" . $vehicle . "</td>
                                            <td data-cell='Time Slot'>" . $timeslot . "</td>
                                            <td data-cell='Start Date'>" . $start_date . "</td>
                                            <td data-cell='End Date'>" . $end_date_str . "</td>
                                            <td data-cell='Days Left'>" . $days_left . "</td>
                                            <td data-cell='Action' style='display: flex;gap: 10px;flex-wrap:wrap; flex-direction: column;'>
                                                <a href='view?id=" . $id . "&phone=" . $phone . "&route=../' class='view'>
                                                    View Details
                                                </a>
                                                  <button class='force-attendance-btn' 
                                                    style='background-color: red; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 14px; transition: background-color 0.2s;'
                                                    data-id='" . $row['cust_id'] . "'
                                                    data-cust-uid='" . $row['cust_uid'] . "'
                                                    data-acc-id='" . $_SESSION['trainer_ID'] . "'
                                                    data-acc-name='" . $_SESSION['trainer_name'] . "'>
                                                    Mark Attendance
                                                </button>
                                            </td>
                                        </tr>";
                                    }
                                }
                            }
                        } else {
                            echo "<tr><td colspan='8' style='text-align:center'>No active learners found</td></tr>";
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>









            <!-- Search Dialog -->
            <dialog id="searchDialog">
                <div class="modal-content" style="padding: 20px;">
                    <button class="close" style="font-size: 28px; font-weight: bold; cursor: pointer; color: #aaa; border: none; background: none;">&times;</button>
                    <h2 style="margin-top: 0;">Search Records</h2>
                    <div class="search-container">
                        <div class="search-box">
                            <i class='bx bx-search search-icon'></i>
                            <input type='text' id='live_search' autocomplete='off'
                                placeholder='Search by name, phone, date or customer UID...'
                                value='<?php echo isset($Squery) ? $Squery : ""; ?>'>
                            <i class='bx bx-x clear-icon' id='clear-search'></i>
                        </div>
                    </div>
                    <div id="search_result"></div>
                </div>
            </dialog>

        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    
    
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Handle force attendance button click
        document.querySelectorAll('.force-attendance-btn').forEach(button => {
            button.addEventListener('click', function() {
                const custUid = this.dataset.custUid;
                const accId = this.dataset.accId;
                const id = this.dataset.id;
                const accName = this.dataset.accName;

                console.log(custUid, accId, id, accName);

                // Close the dialog when showing SweetAlert
                // document.getElementById('myModal').close();

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
                            url: '../api_ajax/forceAttendance.php',
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
        $(document).ready(function() {
            var timeout = null;
            var $searchInput = $("#live_search");
            var $clearBtn = $("#clear-search");
            var $dialog = document.getElementById('searchDialog');
            var $openBtn = $("#openSearchDialog");
            var $closeBtn = $(".close");

            // Open dialog
            $openBtn.on("click", function() {
                $dialog.showModal();
            });

            // Close dialog
            $closeBtn.on("click", function() {
                $dialog.close();
            });

            // Close on click outside
            $(window).on("click", function(e) {
                if ($(e.target).is($dialog)) {
                    $dialog.close();
                }
            });

            // Clear search
            $clearBtn.on("click", function() {
                $searchInput.val('');
                $("#search_result").html("");
                $(this).hide();
            });

            // Show/hide clear button based on input
            $searchInput.on("input", function() {
                clearTimeout(timeout);
                var input = $(this).val();

                if (input.length > 0) {
                    $clearBtn.show();
                } else {
                    $clearBtn.hide();
                }

                if (input != "") {
                    timeout = setTimeout(function() {
                        $.ajax({
                            url: "live_search.php",
                            method: "POST",
                            data: {
                                input: input
                            },
                            beforeSend: function() {
                                $("#search_result").html("<div class='loading'>Searching...</div>");
                            },
                            success: function(data) {
                                $("#search_result").html(data);
                            }
                        });
                    }, 500);
                } else {
                    $("#search_result").html("");
                }
            });

            // Initially hide clear button if input is empty
            if ($searchInput.val() === '') {
                $clearBtn.hide();
            }
        });

        // Pagination handling
        $(document).on('click', '.pagination-link', function(e) {
            e.preventDefault();
            $('.pagination-link').removeClass('active');
            $(this).addClass('active');
        });
    </script>

<script>
        // Add styles for the notification
        const orientationStyle = document.createElement('style');
        orientationStyle.textContent = `
    .orientation-notice {
        position: fixed;
        bottom: -300px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, rgba(0,0,0,0.95), rgba(20,20,20,0.95));
        color: white;
        padding: 15px 25px; /* Reduced padding */
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px; /* Reduced gap */
        z-index: 9999;
        box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: 'Poppins', sans-serif;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
        font-size: 14px; /* Added smaller font size */
    }

    .orientation-notice.show {
        bottom: 30px;
        animation: bounce 1.2s cubic-bezier(0.36, 0, 0.66, 1);
    }

    .close-btn {
        position: absolute;
        top: 8px; /* Adjusted position */
        right: 8px;
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 4px; /* Reduced padding */
        font-size: 16px; /* Reduced font size */
        opacity: 0.7;
        transition: opacity 0.3s;
    }

    .close-btn:hover {
        opacity: 1;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateX(-50%) translateY(0);
        }
        40% {
            transform: translateX(-50%) translateY(-12px); /* Reduced bounce height */
        }
        60% {
            transform: translateX(-50%) translateY(-6px);
        }
    }

    .rotate-icon {
        width: 28px; /* Reduced icon size */
        height: 28px;
        animation: rotate 2.5s infinite cubic-bezier(0.4, 0, 0.2, 1);
        filter: drop-shadow(0 0 8px rgba(255,255,255,0.3));
    }

    @keyframes rotate {
        0% { transform: rotate(0deg) scale(1); }
        50% { transform: rotate(90deg) scale(1.1); }
        100% { transform: rotate(0deg) scale(1); }
    }
`;
        document.head.appendChild(orientationStyle);

        // Create and add the notification element
        const notice = document.createElement('div');
        notice.className = 'orientation-notice';
        notice.innerHTML = `
    <svg class="rotate-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
        <path d="M16.48 2.52c3.27 1.55 5.61 4.72 5.97 8.48h1.5C23.44 4.84 18.29 0 12 0l-.66.03 3.81 3.81 1.33-1.32zm-6.25-.77c-.59-.59-1.54-.59-2.12 0L1.75 8.11c-.59.59-.59 1.54 0 2.12l12.02 12.02c.59.59 1.54.59 2.12 0l6.36-6.36c.59-.59.59-1.54 0-2.12L10.23 1.75zm4.6 19.44L2.81 9.17l6.36-6.36 12.02 12.02-6.36 6.36zm-7.31.29C4.25 19.94 1.91 16.76 1.55 13H.05C.56 19.16 5.71 24 12 24l.66-.03-3.81-3.81-1.33 1.32z"/>
    </svg>
    <span>Please rotate your device to landscape mode for better experience</span>
    <button class="close-btn">&times;</button>
`;
        document.body.appendChild(notice);

        // Check if notification was already shown and when
        const getLastShownTime = () => {
            return parseInt(localStorage.getItem('orientationNoticeLastShown') || '0');
        };

        // Show notification only on mobile devices in portrait mode
        function checkOrientation() {
            const currentTime = Date.now();
            const lastShownTime = getLastShownTime();
            const fiveMinutes = 5 * 60 * 1000; // 5 minutes in milliseconds
            //  const fiveMinutes = 10 * 1000; // 5 minutes in milliseconds

            if (window.innerWidth < 768 && window.innerHeight > window.innerWidth) {
                // Show if never shown or if 5 minutes have passed since last shown
                if (!lastShownTime || (currentTime - lastShownTime) >= fiveMinutes) {
                    notice.classList.add('show');
                    localStorage.setItem('orientationNoticeLastShown', currentTime.toString());
                }
            }
        }

        // Close button handler
        const closeBtn = notice.querySelector('.close-btn');
        closeBtn.addEventListener('click', () => {
            notice.classList.remove('show');
        });

        // Check on load and orientation change
        window.addEventListener('load', checkOrientation);
        window.addEventListener('orientationchange', checkOrientation);
    </script>

    <script src="../js/toggleSideBar.js"></script>
    <script src="../js/script.js"></script>
    <script src="../js/hideSideBar.js"></script>

</body>

</html>