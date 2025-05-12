<?php

include('../includes/authentication.php');
authenticationAdmin('../');


date_default_timezone_set('Asia/Kolkata');
if (isset($_GET['search-query'])) {

    $Squery = $_GET['search-query'];
    header('location:search.php?query=' . $Squery);
}



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
            
            if ($cust_days != null && $cust_days != '' && $attendance_count >= $cust_days) {
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
    <link rel="stylesheet" href="../css/adminDashboard.css">
    <link rel="stylesheet" href="../css/sideBarFooter.css">
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
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            align-items: center;
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





        @media (max-width: 940px) {
            #content main .table-data .order table td {
                font-size: 12px;
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

            .view {

                font-size: 11px;

            }

            #content main .table-data .order table td {
                font-size: 10px;
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
            max-width: 90%;
            width: 1500px;
            margin: auto;
            position: fixed;
            left: 50%;
            transform: translateX(-50%);
            font-family: 'Poppins', sans-serif;
        }



        dialog::backdrop {
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(2px);
        }

        .modal-content {
            position: relative;
            padding: 24px;
            background: #fff;
            border-radius: 12px;
        }

        .close {
            position: absolute;
            top: 16px;
            right: 16px;
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #666;
            transition: color 0.2s;
        }

        .close:hover {
            color: #333;
        }

        .modal-table-container {
            margin-top: 20px;
            overflow-x: auto;
            border-radius: 12px;
        }

        /* Modern table styling */
        .modal-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .modal-table thead {
            background: #f1f5f9;
        }

        .modal-table th {
            font-weight: 600;
            color: #334155;
            text-transform: uppercase;
            font-size: 0.875rem;
            letter-spacing: 0.05em;
            padding: 1rem 1.5rem;
            border-bottom: 2px solid #e2e8f0;
            text-align: left;
        }

        .modal-table td {
            padding: 1rem 1.5rem;
            color: #475569;
            border-bottom: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .modal-table tbody tr {
            transition: all 0.2s ease;
        }

        .modal-table tbody tr:hover {
            background: #f8fafc;
            transform: scale(1.01);
        }

        /* Modern view button */
        .view {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: #3b82f6;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            transition: all 0.2s ease;
            box-shadow: 0 2px 4px rgba(59, 130, 246, 0.1);
        }

        .view:hover {
            background: #2563eb;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(59, 130, 246, 0.2);
        }

        /* Enhanced responsive styles */
        @media (max-width: 768px) {
            .modal-table {
                box-shadow: none;
            }

            .modal-table thead {
                display: none;
            }

            .modal-table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border-radius: 12px;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
                background: #fff;
            }

            .modal-table td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 1rem;
                text-align: right;
                border-bottom: 1px solid #f1f5f9;
            }

            .modal-table td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #334155;
                margin-right: 1rem;
            }

            .modal-table td:last-child {
                border-bottom: none;
            }

            .view {
                width: 100%;
                padding: 0.75rem;
            }

            .modal-table tbody tr:hover {
                transform: none;
            }
        }

        @media (max-width: 480px) {
            .modal-table td {
                flex-direction: column;
                align-items: flex-start;
                padding: 0.75rem;
            }

            .modal-table td::before {
                margin-bottom: 0.25rem;
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



    <link rel="shortcut icon" type="image/png" href="../assets/logo.png" />
    <title>Dashboard</title>
</head>

<body>


    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-car'></i>

            <span class="text">
                <h6><span>Admin</span></h6>
                <h5>Welcome <span>
                        <?php echo $_SESSION['admin_name'] ?>
                    </span></h5>
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
                <a href="admissionForm.php">
                    <i class='bx bxs-book-bookmark'></i>
                    <span class="text">Book Admission</span>
                </a>
            </li>
            <li>
                <a href="analytics/">
                    <i class='bx bxs-bar-chart-alt-2'></i>
                    <span class="text">Analytics</span>
                </a>
            </li>
           
            <li class="search">
                <a href="search.php">
                    <i class='bx bx-search-alt-2'></i>
                    <span class="text">Search</span>
                </a>
            </li>
            <li>
                <a href="sortData/">
                    <!-- <i class='bx bxs-bar-chart-alt-2'></i> -->
                    <i class='bx bxs-data'></i>
                    <span class="text">Customers Data</span>
                </a>
            </li>
            <li class="time-table">
                <a href="timetable/">
                    <i class='bx bx-table'></i>
                    <span class="text">Time Table</span>
                </a>
            </li>

            <li>
                <a href="./dueCustomers/">
                    <i class='bx bxs-user'></i>
                    <span class="text">Pending Payment</span>
                </a>
            </li>
            <li>
                <a href="mailSender/">
                    <i class='bx bx-mail-send'></i>
                    <span class="text">Mail System</span>
                </a>
            </li>

            <li>
                <a href="manageUsers/">
                    <i class='bx bxs-group'></i>
                    <span class="text">Manage Users</span>
                </a>
            </li>
            <li>
                <a href="createVehicle/">
                    <i class='bx bxs-car'></i>
                    <span class="text">Add New Vehicle</span>
                </a>
            </li>
            <li>
                <a href="employeeManagement/">
                    <i class='bx bxs-id-card'></i>
                    <span class="text">Employee Management</span>
                </a>
            </li>
            <li>
                <a href="liveTrainings/">
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
                <a href="../logout.php" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
        
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
            <label for="switch-mode" class="switch-mode"></label> -->
            <!-- <a href="#" class="notification">
                <i class='bx bxs-bell'></i>
                <span class="num">8</span>
            </a> -->
            <span class="text">
                <h2><?php
                    include("../configWeb.php");
                    echo $WebAppTitle;
                    ?></h2>
               
            </span>
            <a href="./" class="profile">
                <img src="../assets/logoBlack.png">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Dashboard</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="./">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./">Home</a>
                        </li>
                    </ul>
                </div>
                <div style="display: flex;
                    width: 670px;
                    <?php

                    if ($CreditsBool) {
                        echo "width: 850px;";
                    }

                    ?>flex-direction: row; align-content: center; justify-content: flex-start; gap: 20px; align-items: center; flex-wrap: wrap;">
                    <a href="Excel/?export=true" class="btn-download">
                        <i class='bx bxs-cloud-download'></i>
                        <span class="text">Data to Excel</span>
                    </a>
                    <a href="preBook/" class="btn-download" style="background: #efbe19;">
                        <i class="fa-solid fa-circle-info"></i>
                        <span class="text">Pre-Book Queue</span>
                    </a>
                    <a href="viewLogs/" class="btn-download" style="background: grey;">
                        <i class="fa-solid fa-file-circle-question"></i>
                        <span class="text">Logs</span>
                    </a>
                    <a href="backup/" class="btn-download" style="background: #28a745;">
                        <i class="fa-solid fa-database"></i>
                        <span class="text">Backup Data</span>
                    </a>
                    <?php

                    if ($CreditsBool) {
                        $CreditsTag = ' <a href="../credits/?who=admin" class="btn-download" style="background: #207df5;">
                        <i class="fa-brands fa-dev"></i>
                        <span class="text">Credits</span>
                    </a>';
                        echo $CreditsTag;
                    }


                    ?>

                </div>
            </div>


            <?php
            if (isset($msg)) {
                foreach ($msg as $msg) {
                    echo '<span class="msg">' . $msg . '</span>';
                };
            };

            ?>

            <div class="box-info-head" style="
            padding: 24px;
            background: #f9f9f9;
            border-radius: 20px;
            display: flex;
            align-items: center;
            gap: 24px;
            margin-top: 36px;">

                <i class='bx bxs-calendar' style="
            width: 30px;
            height: 30px;
            border-radius: 10px;
            font-size: 28px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: red;
            
            "></i>
                <span class="text">
                    <h3>
                        <?php
                        $currentMonthYear = date("F Y");

                        echo $currentMonthYear . " ";
                        ?>
                        Statistics
                    </h3>
                    <p></p>
                </span>
            </div>
            <ul class="box-info">
                 <li>
                    <i class='bx bxs-phone' style="cursor: pointer;" onclick="window.location.href='./inquiries/'"></i>
                    <span class="text">
                        <h3>
                            <?php
                            // SQL query to retrieve total unread booking inquiries
                            $sql = "SELECT COUNT(*) AS total_inquiries FROM `booking_inquiries` WHERE is_read = 0";

                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $totalInquiries = $row["total_inquiries"];
                                echo $totalInquiries;
                            } else {
                                echo "0";
                            }
                            ?>
                        </h3>
                        <p>New Inquiries</p>
                    </span>
                </li>
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
                    <i class='bx bxs-group'></i>
                    <span class="text">
                        <h3>
                            <?php

                            // Get the current year and month
                            $currentYear = date("Y");
                            $currentMonth = date("m");

                            // SQL query to retrieve total paidamount for the current month
                            $sql = "SELECT COUNT(*) AS total_new_customers FROM `cust_details` WHERE YEAR(`date`) = $currentYear AND MONTH(`date`) = $currentMonth";

                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $totalNewCustomers = $row["total_new_customers"];
                                echo $totalNewCustomers;
                            } else {
                                echo "No new customers recorded for this month.";
                            }


                            ?>
                        </h3>
                        <p>Total Admissions</p>
                    </span>
                </li>
                <li>
                    <i class='bx bx-rupee'></i>
                    <span class="text">
                        <h3>
                            <?php


                            // Get the current year and month
                            $currentYear = date("Y");
                            $currentMonth = date("m");

                            // SQL query to retrieve total paidamount for the current month
                            $sql = "SELECT SUM(paidamount) AS total_paidamount FROM `cust_details` WHERE YEAR(`date`) = $currentYear AND MONTH(`date`) = $currentMonth";

                            $result = $conn->query($sql);

                            if ($result->num_rows > 0) {
                                $row = $result->fetch_assoc();
                                $totalPaidAmount =  $row["total_paidamount"] == NULL ? 0 : $row["total_paidamount"];

                                echo "₹" . number_format($totalPaidAmount, 2);
                            } else {
                                echo "No payments recorded for this month.";
                            }

                            ?>
                        </h3>
                        <p>Total Sales</p>
                    </span>
                </li>
            </ul>


            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Today's Admissions</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Total Amount</th>
                                <th>Vehicle</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            date_default_timezone_set('Asia/Kolkata');
                            $current_timestamp_by_mktime = mktime(date("m"), date("d"), date("Y"));
                            $currentDate = date("Y-m-d", $current_timestamp_by_mktime);

                            $query = "SELECT * FROM `cust_details` WHERE date = '$currentDate'";

                            $result = mysqli_query($conn, $query);
                            if (mysqli_num_rows($result) > 0) {

                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr><td data-cell='Name' >" . $row["name"] . "
                                    </td><td data-cell='Phone' ><a href='tel:" . $row["phone"] . "'>" . $row["phone"] . "</a></td>
                                    <td data-cell='Total Amount' >" . $row["totalamount"] . "</td><td data-cell='Vehicle' >" . $row["vehicle"] . "</td><td data-cell='Date' >"
                                        . $row["date"] . "</td>" . "<td data-cell='Action' id='action-cell' >" .
                                        "<a class='view' href='view?id=" . $row["id"] . "&phone=" . $row["phone"] . "&date=" . $row["date"] . "&route=../'>View Details</a>";
                                }
                            } else {
                                $msg2[] = "No Admissions Today ☹️ ";
                            }
                            ?>
                        </tbody>
                    </table>

                    <?php
                    if (isset($msg2)) {
                        foreach ($msg2 as $msg2) {
                            echo '<span class="msg">' . $msg2 . '</span>';
                        };
                    };
                    ?>

                </div>

        </main>
        <!-- MAIN -->

    </section>
    <!-- CONTENT -->

    <dialog id="myModal">
        <div class="modal-content" style="padding: 20px;">
            <button class="close" onclick="document.getElementById('myModal').close();">&times;</button>
            <h2 style="margin-top: 0;">Active Learners</h2>
            <div class="modal-table-container">
                <table class="modal-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Vehicle</th>
                            <th>Time Slot</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Days Left</th>
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
                                    echo "<tr><td colspan='8' style='background-color: #f0f0f0; font-weight: bold; text-align: left; padding: 10px; text-align:center;'>$vehicleName</td></tr>";
                                    
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
                                            <td data-label='Name'>" . $name . "</td>
                                            <td data-label='Phone'><a href='tel:" . $phone . "'>" . $phone . "</a></td>
                                            <td data-label='Vehicle'>" . $vehicle . "</td>
                                            <td data-label='Time Slot'>" . $timeslot . "</td>
                                            <td data-label='Start Date'>" . $start_date . "</td>
                                            <td data-label='End Date'>" . $end_date_str . "</td>
                                            <td data-label='Days Left'>" . $days_left . "</td>
                                            <td data-label='Action' style='display: flex; gap: 10px;'>
                                                <a href='view?id=" . $id . "&phone=" . $phone . "&route=../' class='view'>
                                                    View Details
                                                </a>
                                               <button class='force-attendance-btn' 
                                                    style='background-color: #46abcc; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; font-size: 14px; transition: background-color 0.2s;'
                                                    data-id='" . $row['cust_id'] . "'
                                                    data-cust-uid='" . $row['cust_uid'] . "'
                                                    data-acc-id='" . $_SESSION['admin_ID'] . "'
                                                    data-acc-name='" . $_SESSION['admin_name'] . "'>
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
    </dialog>
    
    
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

                // console.log(custUid, accId, id, accName);

                // Close the dialog when showing SweetAlert
                document.getElementById('myModal').close();

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
        document.getElementById('active-customers').addEventListener('click', function() {
            document.getElementById('myModal').showModal();
        });
        document.getElementById('myModal').addEventListener('click', function(event) {
            if (event.target === this) {
                this.close();
            }
        });
    </script>
    <script>
        function checkInternetConnection() {
            if (!navigator.onLine) {
                alert("Please connect to the internet.");
            } else {
                //   console.log("You are online.");
            }
        }


        window.onload = checkInternetConnection;


        window.addEventListener('online', checkInternetConnection);


        window.addEventListener('offline', function() {
            alert("You are offline. Please connect to the internet.");
        });
        <?php
        // Auto Backup
        include('../autoBackup.php');
        autoBackup('../');
        ?>
      
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