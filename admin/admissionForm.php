<?php


include('../includes/authentication.php');
authenticationAdmin('../');

function logActivity($logType, $who, $activity)
{
    date_default_timezone_set('Asia/Kolkata');

    $logFolder = '../logs/' . $logType;

    if (!file_exists($logFolder)) {
        mkdir($logFolder, 0755, true);
    }

    $logFile = $logFolder . '/admission_logs.json';

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




if (isset($_POST['book-admission'])) {

    // varaibles

    if (empty($_POST['time-slot'])) {
        $error[] = "Select Time Slot!";
    } else {

        // Generate and validate unique cust_uid
        function generateCustUID($length = 6)
        {
            return substr(str_shuffle('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ'), 0, $length);
        }

        // Get unique customer ID
        $cust_uid = null;
        $stmt = mysqli_prepare($conn, "SELECT cust_uid FROM cust_details WHERE cust_uid = ?");
        mysqli_stmt_bind_param($stmt, "s", $cust_uid);

        do {
            $cust_uid = generateCustUID();
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
        } while (mysqli_stmt_num_rows($stmt) > 0);


        // Personal Details
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $address = $_POST['address'];


        // Booking Details
        $totalA = $_POST['totalA'];
        $paidA = $_POST['paidA'];
        $dueA = $totalA - $paidA;
        $tempS = $_POST['startingForm'];
        $days = $_POST['days'];
        $timeSlot = $_POST['time-slot'];


        $paymentMethod = $_POST['payment_method'];



        $Tname = $_POST['Tname'];
        $Tnum = $_POST['Tnumber'];


        // time and date End date

        date_default_timezone_set('Asia/Kolkata');

        $current_timestamp_by_mktime = mktime(date("m"), date("d"), date("Y"));
        $currentDate = date("Y-m-d", $current_timestamp_by_mktime);
        $currentMakeTime = strtotime("now");
        $currentTime = date("h:i:sa", $currentMakeTime);
        $startedAT = "";


        if ($tempS === "customDate") {

            $startedAT = $_POST["customDate"];
        } else {

            $startedAT = date('Y-m-d', strtotime((int) $currentDate . ' +' . $_POST['startingForm'] . ' days'));
        }



        $days -= 1;
        $EndsTemp = date('Y-m-d', strtotime($startedAT . ' +' . $days . ' days'));

        if ($_POST['ARSundays'] == 'remove') {
            // Calculate end date excluding Sundays
            $start_date = new DateTime($startedAT);
            $end_date = clone $start_date;
            $days_to_add = $days;

            while ($days_to_add > 0) {
                $end_date->modify('+1 day');
                // Skip counting Sundays
                if ($end_date->format('N') != 7) {
                    $days_to_add--;
                }
            }

            // If end date falls on Sunday, move to Monday
            if ($end_date->format('N') == 7) {
                $end_date->modify('+1 day');
            }

            $Ends_On = $end_date->format('Y-m-d');
        } else {
            // Include Sundays in calculation
            $Ends_On = date('Y-m-d', strtotime($startedAT . ' +' . $days . ' days'));
        }





        // Check if add ons

        if (isset($_POST['add-ons'])) {

            $add_ons = "Applied";
        } else {
            $add_ons = "Not Applied";
        }



        $vehicle = $_POST['vehicle'];

        if ($vehicle == '4wheeler') {
            $carName = $_POST['carName'];
            $veh_Name = mysqli_fetch_array(mysqli_query($conn, "SELECT vehicle_name FROM vehicles WHERE data_base_table = '$carName'"));
            $distance = $_POST['distance'];

            $CarDetails = "Four Wheeler ";
            $CarDetails .= $veh_Name[0];

            $TandD = $distance . "Km ";

            $vehicleDetails = $CarDetails . "/ " . $TandD;
        } elseif ($vehicle == '2wheeler') {

            $carName = $_POST['carName'];
            $veh_Name = mysqli_fetch_array(mysqli_query($conn, "SELECT vehicle_name FROM vehicles WHERE data_base_table = '$carName'"));

            $bikeName = "Two Wheeler";
            // $bikeName .= $veh_Name[0];
            $bikeTime = $_POST['bikeTime'] . " mins ";

            $vehicleDetails = $bikeName . "/ " . $bikeTime;
        }


        if (empty($_POST['time-slot'])) {
            $error[] = "Select Time Slot!";
        } else {



            $CheckNum = " SELECT * FROM `cust_details` WHERE phone = $phone";
            $result = mysqli_query($conn, $CheckNum);



            if (mysqli_num_rows($result) > 0) {

                $error[] = 'Phone Number already exist!';
            } else {

                if (isset($_POST['preBook'])) {

                    $priority = 1;

                    $checkIfAny = "SELECT * FROM `pre_book_queue` WHERE timeslot = '$timeSlot' AND vehicle = '" . $veh_Name[0] . "'";
                    $resultCheckIfAny = mysqli_query($conn, $checkIfAny);
                    if (mysqli_num_rows($resultCheckIfAny) > 0) {
                        while ($row = $resultCheckIfAny->fetch_assoc()) {
                            if ($row['status'] = 'active') {
                                $priority = (int) $row['priority'] + 1;
                            }
                        }
                    }

                    $insertPreBook = "INSERT INTO `pre_book_queue`(`priority`, `timeslot`, `name`, `phone`, `vehicle`, `trainer`, `start_date`, `end_date`, `status`) VALUES ('$priority','$timeSlot','$name','$phone','" . $veh_Name[0] . "','$Tname','$startedAT','$Ends_On','active')";

                    $resultPreBookInsert = mysqli_query($conn, $insertPreBook);

                    if (!isset($result)) {
                        $error[] = "Error inserting data in Pre Book Queue: " . mysqli_error($conn);
                    }
                } elseif ($vehicle == '2wheeler') {
                    $TwoWheeler = true;


                    $r = mysqli_query($conn, "SELECT data_base_table FROM vehicles");
                    $vehicle_name_assoc_array = mysqli_fetch_all($r, MYSQLI_ASSOC);

                    $vehicle_name_array = array_map(function ($item) {
                        return $item['data_base_table'];
                    }, $vehicle_name_assoc_array);


                    if (in_array($carName, $vehicle_name_array)) {

                        // $vehicleAll_INFO = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM vehicles WHERE data_base_table = '$carName'"));


                        $CheckStatus = "SELECT * FROM `$carName` WHERE timeslots = '$timeSlot'";
                        $result = mysqli_query($conn, $CheckStatus);
                        $row = mysqli_fetch_assoc($result);
                        if ($row['status'] != "empty") {

                            $error[] = $timeSlot . '  Time Slot is already Taken in ' . $veh_Name[0] . '!';
                        } else {

                            $updata = "UPDATE `$carName` SET name='$name', phone='$phone', vehicle='$vehicleDetails', trainer='$Tname', start_date='$startedAT', end_date='$Ends_On', status='active' WHERE timeslots = '$timeSlot'";
                            mysqli_query($conn, $updata);
                            if (!isset($result)) {
                                $error[] = "Error inserting data in Time Table: " . mysqli_error($conn);
                            }
                        }
                    } else {
                        $error[] = "Error inserting data in Pre Book Queue: Cant Find Car in DB!";
                    }


                } else {

                    // if ($carName == "i10") {
                    $r = mysqli_query($conn, "SELECT data_base_table FROM vehicles");
                    $vehicle_name_assoc_array = mysqli_fetch_all($r, MYSQLI_ASSOC);

                    $vehicle_name_array = array_map(function ($item) {
                        return $item['data_base_table'];
                    }, $vehicle_name_assoc_array);


                    if (in_array($carName, $vehicle_name_array)) {

                        // $vehicleAll_INFO = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM vehicles WHERE data_base_table = '$carName'"));


                        $CheckStatus = "SELECT * FROM `$carName` WHERE timeslots = '$timeSlot'";
                        $result = mysqli_query($conn, $CheckStatus);
                        $row = mysqli_fetch_assoc($result);
                        if ($row['status'] != "empty") {

                            $error[] = $timeSlot . '  Time Slot is already Taken in ' . $veh_Name[0] . '!';
                        } else {

                            $updata = "UPDATE `$carName` SET name='$name', phone='$phone', vehicle='$vehicleDetails', trainer='$Tname', start_date='$startedAT', end_date='$Ends_On', status='active' WHERE timeslots = '$timeSlot'";
                            mysqli_query($conn, $updata);
                            if (!isset($result)) {
                                $error[] = "Error inserting data in Time Table: " . mysqli_error($conn);
                            }
                        }
                    } else {
                        $error[] = "Error inserting data in Pre Book Queue: Cant Find Car in DB!";
                    }
                }

                if (!isset($error) || $TwoWheeler) {


                    $days += 1;
                    // $insert = "INSERT INTO `cust_details`(`name`, `email`, `phone`, `address`, `totalamount`, `paidamount`, `dueamount`, `days`, `timeslot`, `vehicle`, `newlicence`, `trainername`, `trainerphone`, `date`, `time`, `endedAT`,  `startedAT`, `formfiller`,`cust_uid`) VALUES('$name','$email','$phone','$address', '$totalA','$paidA','$dueA','$days', '$timeSlot', '$vehicleDetails','$add_ons', '$Tname', '$Tnum','$currentDate','$currentTime','$Ends_On','$startedAT','" . $_SESSION['admin_name'] . "','$cust_uid')";


                    // $result = mysqli_query($conn, $insert);


                    // ------------ new code ---------

                    // Escape all input variables to prevent SQL syntax issues
                    $name = mysqli_real_escape_string($conn, $name);
                    $email = mysqli_real_escape_string($conn, $email);
                    $phone = mysqli_real_escape_string($conn, $phone);
                    $address = mysqli_real_escape_string($conn, $address);
                    $totalA = mysqli_real_escape_string($conn, $totalA);
                    $paidA = mysqli_real_escape_string($conn, $paidA);
                    $dueA = mysqli_real_escape_string($conn, $dueA);
                    $days = mysqli_real_escape_string($conn, $days);
                    $timeSlot = mysqli_real_escape_string($conn, $timeSlot);
                    $vehicleDetails = mysqli_real_escape_string($conn, trim($vehicleDetails));
                    $add_ons = mysqli_real_escape_string($conn, $add_ons);
                    $Tname = mysqli_real_escape_string($conn, $Tname);
                    $Tnum = mysqli_real_escape_string($conn, $Tnum);
                    $currentDate = mysqli_real_escape_string($conn, $currentDate);
                    $currentTime = mysqli_real_escape_string($conn, $currentTime);
                    $Ends_On = mysqli_real_escape_string($conn, $Ends_On);
                    $startedAT = mysqli_real_escape_string($conn, $startedAT);
                    $cust_uid = mysqli_real_escape_string($conn, $cust_uid);
                    $formfiller = mysqli_real_escape_string($conn, $_SESSION['admin_name']);
                    $paymentMethod = mysqli_real_escape_string($conn, $paymentMethod);

                    // Construct the SQL query
                    $insert = "INSERT INTO `cust_details`(`name`, `email`, `phone`, `address`, `totalamount`, `paidamount`, `dueamount`, `days`, `timeslot`, `vehicle`, `newlicence`, `trainername`, `trainerphone`, `date`, `time`, `endedAT`, `startedAT`, `formfiller`, `cust_uid`, `payment_method`) VALUES('$name', '$email', '$phone', '$address', '$totalA', '$paidA', '$dueA', '$days', '$timeSlot', '$vehicleDetails', '$add_ons', '$Tname', '$Tnum', '$currentDate', '$currentTime', '$Ends_On', '$startedAT', '$formfiller', '$cust_uid', '$paymentMethod')";

                    // Execute the query
                    $result = mysqli_query($conn, $insert);

                    if (!$result) {
                        // Output error message for debugging
                        die("Error in query: " . mysqli_error($conn));
                    }

                    // ------------ new code ---------





                    if (isset($result)) {

                        logActivity('admin_logs', $_SESSION['admin_name'], array("What" => (isset($_POST['preBook'])) ? "Pre Booked Admission, Priority: $priority" : "Booked Admission", array("customer_details" => array("name" => $name, "phone" => $phone, "vehicle" => $vehicleDetails, "timeSlot" => $timeSlot, "addmission_date" => $currentDate, "days" => $days, "started_at" => $startedAT, "ended_at" => $Ends_On, "formfiller" => $_SESSION['admin_name']))));


                        if ($vehicle == '4wheeler') {



                            header('location:../previewPDF.php?id=' . $phone . '&email=' . $email . '&name=' . $name . '&VN=' . $CarDetails . '&TT=' . $TandD . '&who=admin');
                        } elseif ($vehicle == '2wheeler') {


                            header('location:../previewPDF.php?id=' . $phone . '&email=' . $email . '&name=' . $name . '&VN=' . $bikeName . '&TT=' . $bikeTime . '&who=admin');
                        }
                    } else {
                        $error[] = "Error inserting data: " . mysqli_error($conn);
                    }
                }
            }
        }
    }
}
?>



<!DOCTYPE html>

<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!----======== CSS ======== -->
    <link rel="stylesheet" href="../css/admissionForm.css">

    <!----===== Iconscout CSS ===== -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="shortcut icon" type="image/png" href="../assets/logo.png" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .container {
            height: 85dvh;
            height: 85vh;
        }


        .container form {
            overflow-y: scroll;
            height: 95%;

        }

        #tempcarinput::placeholder {
            color: red;
            font-weight: 500;
        }

        #startingFormBack {
            transition: all .2s;
        }

        #startingFormBack:hover {
            background: black;
            color: #fff;
        }

        .container {
            width: 90%;
        }

        @media (max-width: 1060px) {
            .container form {
                overflow-y: scroll;
            }

            .container form::-webkit-scrollbar {
                display: none;
            }

            form .fields .input-field {
                width: calc(100% / 2 - 15px);
            }
        }

        @media (max-width: 769px) {

            .container {
                height: 90dvh;
                height: 90vh;
            }

            .container form {
                overflow-y: scroll;
                height: 95%;

            }

            .container form::-webkit-scrollbar {
                display: none;
            }

            form .fields .input-field {
                width: calc(100% / 2 - 15px);
            }
        }


        @media (max-width: 550px) {
            form .fields .input-field {
                width: 100%;
            }
        }


        @media (max-width: 465px) {
            .btn-s {
                justify-content: start;
                font-size: 10px;
            }
        }

        @media (max-width: 400px) {
            .btn-s {
                width: 100px;
                font-size: 9px;
            }

            .btn-s a {
                font-weight: 700;
            }

            .container {
                height: 85dvh;
                height: 85vh;
            }

            .container form {
                overflow-y: scroll;
                height: 90%;

            }




        }
    </style>
    <link rel="stylesheet" href="../css/navbar.css">
    <title>Admission Form</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@200;300;400;500;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            margin: 0px;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: flex-start;
            background: #4070f4;
            flex-direction: column;
        }

        .container {
            overflow-y: scroll;
            position: relative;
            max-width: 1360px;
            height: 650px;
            width: 90%;
            border-radius: 6px;
            padding: 30px;
            margin: 0 15px;
            background-color: #fff;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);

        }

        .container header {
            position: relative;
            font-size: 20px;
            font-weight: 600;
            color: #333;
        }

        .container header::before {
            content: "";
            position: absolute;
            left: 0;
            bottom: -2px;
            height: 3px;
            width: 27px;
            border-radius: 8px;
            background-color: #4070f4;
        }

        .container form {
            position: relative;
            margin-top: 16px;
            min-height: 550px;
            background-color: #fff;
            overflow-y: scroll;
        }

        .container form .form {
            position: absolute;
            background-color: #fff;
            transition: 0.3s ease;
        }

        .container form .form.second {
            opacity: 0;
            pointer-events: none;
            transform: translateX(100%);
        }

        form.secActive .form.second {
            opacity: 1;
            pointer-events: auto;
            transform: translateX(0);
        }

        form.secActive .form.first {
            opacity: 0;
            pointer-events: none;
            transform: translateX(-100%);
        }

        .container form .title {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            font-weight: 500;
            margin: 6px 0;
            color: #333;
        }

        .container form .fields {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        form .fields .input-field,
        form .fields #carFields .input-field,
        form .fields #bikeFields .input-field {
            display: flex;
            width: calc(70% / 3 - 15px);
            flex-direction: column;
            margin: 4px 0;
        }

        .input-field label {
            font-size: 12px;
            font-weight: 500;
            color: #2e2e2e;
        }

        .input-field input,
        select {
            outline: none;
            font-size: 14px;
            font-weight: 400;
            color: #333;
            border-radius: 5px;
            border: 1px solid #aaa;
            padding: 0 15px;
            height: 42px;
            margin: 8px 0;
        }

        .input-field input :focus,
        .input-field select:focus {
            box-shadow: 0 3px 6px rgba(0, 0, 0, 0.13);
        }

        .input-field select,
        .input-field input[type="date"] {
            color: #707070;
        }

        .input-field input[type="date"]:valid {
            color: #333;
        }

        .container .form .trainer-details .nextBtn,
        .backBtn {
            height: 45px;
            max-width: 200px;
            width: 100%;
            border: none;
            outline: none;
            color: #fff;
            border-radius: 5px;
            margin: 25px 0;
            background-color: #4070f4;
            cursor: pointer;
        }

        .container form .btnText {
            font-size: 14px;
            font-weight: 400;
        }

        form button:hover {
            background-color: #265df2;
        }

        form button i,
        form .backBtn i {
            margin: 0 6px;
        }

        form .backBtn i {
            transform: rotate(180deg);
        }

        form .buttons {
            display: flex;
            align-items: center;
        }

        form .buttons button,
        .backBtn {
            margin-right: 14px;
        }

        @media (max-width: 1200px) {
            .container {
                max-width: 95%;
            }

            form .fields .input-field {
                width: calc(50% - 15px);
            }
        }

        @media (max-width: 992px) {
            .container {
                height: 700px;
            }

            form .fields .input-field {
                width: calc(50% - 15px);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            form .fields .input-field {
                width: 100%;
            }

            .container form {
                min-height: 450px;
            }
        }

        @media (max-width: 576px) {
            .container {
                margin: 0 10px;
                height: 600px;
            }

            .container header {
                font-size: 18px;
            }

            .input-field input,
            select {
                font-size: 13px;
            }
        }

        @media (max-width: 480px) {
            .container {
                width: 95%;
                padding: 15px;
            }

            .container form .btnText {
                font-size: 12px;
            }

            .container .form .trainer-details .nextBtn,
            .backBtn {
                max-width: 150px;
            }
        }

        @media (max-width: 550px) {
            form .fields .input-field {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            #carFields .input-field {
                width: 100%;
            }

            #carName {
                width: 100%;
            }
        }

        @media (max-width: 480px) {

            #carFields .input-field select,
            #carFields .input-field input {
                font-size: 13px;
            }
        }

        :root {
            --primary-color: #1e293b;
            /* Dark Blue-Gray */
            --secondary-color: #334155;
            /* Slate Gray */
            --accent-color: #64748b;
            /* Steel Blue */
            --background-color: #0f172a;
            /* Midnight Blue */
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            /* Deeper shadow for darker theme */

            /* Additional background colors */
            --background-light: #1c2a39;
            /* Slightly lighter background for contrast */
            --background-dark: #0d1117;
            /* Ultra dark for headers/footers */
            --background-muted: #252f3f;
            /* Muted tone for sections */
            --background-accent: #16222e;
            /* Subtle accent background */
        }


        /* Navbar container */
        #content {
            position: sticky;
            top: 0;
            z-index: 1000;
            padding: 0;
            background-color: white;
            box-shadow: var(--card-shadow);
        }

        nav {
            padding: 1rem;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            /* border-radius: 0 0 1rem 1rem; */
        }

        .navbar-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .btn-s {
            display: flex;
            gap: 1rem;
        }

        .home-link {
            color: white;
            text-decoration: none;
            padding: 0.5rem 1rem;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
        }

        .home-link:hover {
            background-color: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
        }

        .text h3 {
            color: white;
            margin: 0;
            font-size: 1.5rem;
            font-weight: 600;
        }


        @media (max-width: 768px) {
            .navbar-container {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
            }

            .btn-s {
                width: 100%;
                justify-content: center;
            }

        }

        @media (max-width: 480px) {
            .home-link {
                padding: 0.5rem;
                font-size: 0.875rem;
            }

            .text h3 {
                font-size: 1.25rem;
            }

            .profile {
                display: none;
            }
        }
    </style>
</head>

<body>


    <section id="content" style="width: 100%; position: sticky; margin-bottom: 10px; ">
        <nav>
            <div class="btn-s">
                <a href="./" class="home-link" style="margin-right: 20px;">
                    Back
                </a>
                <a href="timetable" class="home-link">
                    TimeSlots
                </a>
            </div>
            <span class="text">
                <h3><?php
                    include("../configWeb.php");
                    echo $WebAppTitle;
                    ?></h3>

            </span>
            <a href="./" class="profile">
                <img src="../assets/logoBlack.png">
            </a>
        </nav>

    </section>

    <div class="error-container">
        <?php
        if (isset($error)) {
            foreach ($error as $error) {
                echo '<span class="error-msg" style="
          margin: 10px 0px;
          display: block;
          background: red;
          color:#fff;
          border-radius: 5px;
          font-size: 20px;
          padding:10px;">' . $error . '</span>';
            };
        };
        ?>
        <span id="error-msg-phone" class="error-msg" style="
          margin: 10px 0px;
          background: red;
          display: none;
          color:#fff;
          border-radius: 5px;
          font-size: 20px;
          padding:10px;"></span>
    </div>


    <div class="container">
        <header>Admission Form</header>

        <form method="post">

            <style>
                .payment-field {
                    width: calc(70% / 3 - 15px);
                    margin: 4px 0;
                }

                .payment-label {
                    font-size: 12px;
                    font-weight: 500;
                    color: #2e2e2e;
                }

                .required {
                    color: red;
                }

                .payment-select {
                    outline: none;
                    font-size: 14px;
                    font-weight: 400;
                    color: #333;
                    border-radius: 5px;
                    border: 1px solid #aaa;
                    padding: 0 15px;
                    height: 42px;
                    margin: 8px 0;
                    width: 100%;
                }

                /* Responsive styles */
                @media screen and (max-width: 768px) {
                    .payment-field {
                        width: 100%;
                        margin: 8px 0;
                    }

                    .payment-label {
                        font-size: 14px;
                    }

                    .payment-select {
                        height: 48px;
                        font-size: 16px;
                    }
                }

                @media screen and (min-width: 769px) and (max-width: 1024px) {
                    .payment-field {
                        width: calc(50% - 15px);
                    }
                }
            </style>

            <div class="input-field payment-field">
                <label class="payment-label">
                    Payment Method <span class="required">*</span>
                </label>
                <select name="payment_method" required class="payment-select">
                    <option value="cash" selected>Cash</option>
                    <option value="bank">Bank</option>
                </select>
            </div>

            <span style="
                display: flex;
                flex-wrap: wrap;
                align-content: center;
                justify-content: flex-start;
                align-items: center;
                margin-right: 30px;
                flex-direction: row-reverse;
            ">
                <label for="preBook" style="
                            align-items: center;
                            display: flex;
                            gap: 1rem;
                            font-weight: 600;
                        ">Pre-Book<input type="checkbox" name="preBook" value="ture" id="preBook"></label></span>


            <div class="form first">
                <div class="details personal">
                    <span class="title">Personal Details</span>

                    <div class="fields">
                        <div class="input-field">
                            <label>Full Name <span style="color: red;">*</span></label>
                            <input type="text" id="customer-Name" name="name" placeholder="Enter Name" required>
                        </div>

                        <div class="input-field">
                            <label style="display: flex; justify-content: space-between; ">Email <span style="color: red;">*</span><span id="emailError" style="color: red;"></span></label>

                            <input type="text" id="email" name="email" placeholder="Enter Email" required>

                        </div>

                        <div class="input-field">
                            <label style="display: flex; justify-content: space-between; ">Mobile Number <span style="color: red;">*</span><span id="phoneError" style="color: red;"></span></label>
                            <input type="number" id="phone" name="phone" placeholder="Enter Mobile number" required>
                        </div>


                        <div class="input-field">
                            <label>Address <span style="color: red;">*</span></label>
                            <input type="text" name="address" placeholder="Enter Address" required>
                        </div>
                    </div>
                </div>
                <div class="details ID">

                    <span class="title">Booking Details</span>

                    <div class="fields">
                        <div class="input-field">
                            <label style="display: flex; justify-content: space-between; ">Total Amount <span style="color: red;">*</span><span id="TA-error" style="color: red;"></span></label>
                            <input type="number" name="totalA" placeholder="Enter Total Amount" required autocomplete="off" id="TA">
                        </div>

                        <div class="input-field">
                            <label style="display: flex; justify-content: space-between; ">Paid Amount <span style="color: red;">*</span><span id="PA-error" style="color: red;"></span></label>
                            <input type="number" name="paidA" placeholder="Enter Paid Amount" required autocomplete="off" id="PA">
                        </div>

                        <div class="input-field">
                            <label>Days <span style="color: red;">*</span></label>
                            <input type="number" name="days" id="customer-Days" placeholder="Enter Days" required autocomplete="off">
                        </div>

                        <div class="input-field" id="timeSlotsContainer">
                            <label>‎ </label>
                            <input type="text" id="tempcarinput" placeholder="Select a vehicle to select timeslot" disabled>
                        </div>

                        <div class="input-field">
                            <label>Select vehicle <span style="color: red;">*</span></label>
                            <select name="vehicle" onchange="showFields(this)" id="selectVehicle" required>
                                <option disabled selected>Select Vehicle</option>
                                <option value="4wheeler">Four Wheeler</option>
                                <option value="2wheeler">Two Wheeler</option>
                            </select>
                        </div>

                        <div class="input-field">
                            <label id="startingFormHead">Select Training Starting From <span style="color: red;">*</span></label>
                            <select name="startingForm" id="startingForm" required>
                                <option disabled value="Select Training Starting From">Select Training Starting From
                                </option>
                                <option value="0" selected>Same Day</option>
                                <option value="1">Tomorrow</option>
                                <option value="2">Overmorrow</option>
                                <option value="customDate">Custom Date</option>
                            </select>


                            <input type="date" name="customDate" id="customDateField" style="display: none;">

                        </div>

                        <div class="input-field">
                            <label>Sundays <span style="color: red;">*</span></label>
                            <select name="ARSundays" required>
                                <option value="remove" selected>(Training will not be conducted on Sundays)</option>
                                <option value="add">(Training will be conducted on Sundays)</option>
                            </select>
                        </div>

                        <div class="box">

                            <label style="
                            font-size: 12px;
                            margin-left: 4px;
                            margin-bottom: 10px; color: #080800;">
                                Add Ons</label>

                            <div class="boxborder" style="
                    border: 1px solid rgb(129, 129, 129);
                    padding: 6.5px 5px 6.5px 19px;
                    border-radius: 4px;
                    width: 285px;">
                                <div class="input-field" style="justify-content: flex-start; gap: 11px; align-items: center; flex-direction: row; width: 146px;  max-height: 26px; align-items: center; margin: 0px;">
                                    <label style="color: rgb(143, 143, 143);">New Licence</label>

                                    <input type="checkbox" name="add-ons" value="LNew" style="width: 20px;">
                                </div>
                            </div>

                        </div>

                        <div id="carFields" style="display: none;">

                            <div class="input-field" id="form-CarName">
                                <label>Car Name <span style="color: red;">*</span></label>
                                <!-- <input type="text" name="carName" id="carName"> -->
                                <select name="carName" id="carName" required>

                                </select>
                            </div>

                            <div class="input-field" id="form-Distance">
                                <label>Distance [ Kilometers ] <span style="color: red;">*</span></label>
                                <input type="text" name="distance" id="distance" placeholder="Enter Distance">
                            </div>



                        </div>

                        <div id="bikeFields" style="display: none;">
                            <div class="input-field">
                                <label>Time [ mins ] <span style="color: red;">*</span></label>
                                <input type="text" name="bikeTime" id="bikeTime" placeholder="Enter Duration">
                            </div>
                        </div>




                    </div>




                </div>

                <div class="trainer-details">
                    <span class="title">Trainer Details</span>
                    <div class="fields" style="justify-content:flex-start; gap: 40px;">

                        <div class="input-field">
                            <label>Trainer Name <span style="color: red;">*</span></label>
                            <select name="Tname" id="trainerSelect" required>
                                <option value="" disabled selected>Select Trainer</option>
                                <?php
                                include("../config.php");

                                $query = "SELECT id, emp_uid, name, phone FROM employees WHERE is_ex_employee='0' AND role='trainer'";
                                $result = mysqli_query($conn, $query);

                                if (!$result) {
                                    die("Database query failed: " . mysqli_error($conn));
                                }

                                while ($row = mysqli_fetch_assoc($result)) {
                                    $emp_uid = htmlspecialchars($row['emp_uid']);
                                    $name = htmlspecialchars($row['name']);
                                    $phone = htmlspecialchars($row['phone']);
                                    echo "<option value='$name' data-name='$name' data-emp-uid='$emp_uid' data-phone='$phone'>$name</option>";
                                }

                                mysqli_close($conn);
                                ?>
                            </select>
                        </div>

                        <div class="input-field">
                            <label>Trainer Number <span style="color: red;">*</span></label>
                            <input type="number" autocomplete="off" name="Tnumber" id="trainerPhone" placeholder="Enter Number" required>
                        </div>
                    </div>
                </div>
                <input class="nextBtn" type="submit" name="book-admission" class="enter" value="Book Admission" style="    height: 45px;
                        max-width: 200px;
                        width: 100%;
                        border: none;
                        outline: none;
                        color: #fff;
                        border-radius: 5px;
                        margin: 25px 0;
                        background-color: #4070f4;
                        cursor: pointer;
                        ">
            </div>
            <script>
                document.getElementById('trainerSelect').addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    document.getElementById('trainerPhone').value = selectedOption.dataset.phone;
                });
            </script>
            <script>
                // Get URL parameters
                const urlParams = new URLSearchParams(window.location.search);
                const iiid = urlParams.get('id');
                const iname = urlParams.get('name');
                const iphone = urlParams.get('phone');
                const iemail = urlParams.get('email');
                const itotalA = urlParams.get('totalA');
                const idays = urlParams.get('days');
                const idistance = urlParams.get('distance');


                // Set input values if parameters exist
                if (iiid) {
                    if (iname) document.getElementById('customer-Name').value = decodeURIComponent(iname);
                    if (iphone) document.getElementById('phone').value = decodeURIComponent(iphone);
                    if (iemail) document.getElementById('email').value = decodeURIComponent(iemail);
                    if (itotalA) document.getElementById('TA').value = decodeURIComponent(itotalA);
                    if (idays) document.getElementById('customer-Days').value = decodeURIComponent(idays);
                    if (idistance) document.getElementById('distance').value = decodeURIComponent(idistance);


                }
            </script>

        </form>

    </div>



    <script>
        // Show/hide vehicle fields based on selection
        function showFields(select) {
            const carFields = document.getElementById("carFields");
            const bikeFields = document.getElementById("bikeFields");
            const formDistance = document.getElementById("form-Distance");

            if (select.value === "4wheeler") {
                carFields.style.display = "flex";
                bikeFields.style.display = "none";
                formDistance.style.display = "block";
            } else if (select.value === "2wheeler") {
                carFields.style.display = "flex";
                bikeFields.style.display = "flex";
                formDistance.style.display = "none";
            } else {
                carFields.style.display = "none";
                bikeFields.style.display = "none";
            }

            const visibleFields = [carFields, bikeFields].filter(field => field.style.display === "flex");
            visibleFields.forEach(field => {
                field.style.gap = "10px";
                field.style.width = "100%";
                field.style.flexDirection = "column";
                field.style.alignItems = "flex-start";
                field.style.justifyContent = "flex-start";
                field.style.flexWrap = "wrap";
                
                if (window.innerWidth < 768) {
                    document.getElementById("form-CarName").style.width = "100%";
                } else {
                    field.style.flexDirection = "row";
                    field.style.gap = "50px";
                }
            });
        }

        // Load vehicle timeslots content
        function loadVehicleContent(type, vehicle, tableName = '', vehicleName = '') {
            const isPreBooked = document.getElementById("preBook").checked;
            const baseUrl = isPreBooked ? '../cars/preBook/' : '../cars/';
            // console.log(type);

            const endpoint = type === 'car' ? 'getCarsTimeSlots' : 'getBikeTimeSlots';

            const url = `${baseUrl}${endpoint}.php`;
          
            const data = type === 'car' ? {
                table_name: tableName,
                vehicle_name: vehicleName
            } : {
                table_name: tableName,
                vehicle_name: vehicleName
            };

            // console.log(data);

        
            $.ajax({
                url: url,
                type: 'GET',
                data: data,
                success: function(response) {
                    $('#timeSlotsContainer').html(response);

                },
                error: function() {
                    // $('#timeSlotsContainer').html('Error: Select Car First');
                    console.log('Error: Select Car First');
                    $('#timeSlotsContainer').html('<input type="text" id="tempcarinput" placeholder="Select a vehicle to select timeslot" disabled="">');
                }
            });
        }

        // Event handlers for vehicle selection
        const vehicleSelect = document.getElementById('selectVehicle');
        const carNameSelect = document.getElementById('carName');

        vehicleSelect.addEventListener('change', function() {
            const isCar = this.value === '4wheeler';
            if (isCar) {
                // Load car vehicles into carNameSelect
                $.ajax({
                    url: '../api_ajax/loadVehicleSelect.php',
                    type: 'POST',
                    data: {
                        category: '4-wheel'
                    },
                    success: function(response) {
                       carNameSelect.innerHTML = response;
                    //    carNameSelect.selectedIndex = 1;
                        const selectedCar = carNameSelect.value;
                        const vehicleName = carNameSelect.options[carNameSelect.selectedIndex].textContent;
                        loadVehicleContent('car', 'getCarsTimeSlots', selectedCar, vehicleName);
                    },
                    error: function() {
                        console.error('Error loading vehicles');
                    }
                });
            } else {
                // Load bike vehicles into carNameSelect
                $.ajax({
                    url: '../api_ajax/loadVehicleSelect.php',
                    type: 'POST',
                    data: {
                        category: '2-wheel'
                    },
                    success: function(response) {
                        carNameSelect.innerHTML = response;
                        const selectedBike = carNameSelect.value;
                        // carNameSelect.selectedIndex = 1;
                        const vehicleName = carNameSelect.options[carNameSelect.selectedIndex].textContent;
                        loadVehicleContent('bike', 'getBikeTimeSlots', selectedBike, vehicleName);
                    },
                    error: function() {
                        console.error('Error loading vehicles');
                    }
                });
            }
        });

        carNameSelect.addEventListener('change', function() {
            const selectedVehicle = this.value;
            const vehicleName = this.options[this.selectedIndex].textContent;
            const vehicleType = vehicleSelect.value === '4wheeler' ? 'car' : 'bike';
            const timeSlotType = vehicleType === 'car' ? 'getCarsTimeSlots' : 'getBikeTimeSlots';
            loadVehicleContent(vehicleType, timeSlotType, selectedVehicle, vehicleName);
        });


        // Starting form date selection handling
        const startingFormSelect = document.getElementById('startingForm');
        const customDateField = document.getElementById('customDateField');
        const startingFormHead = document.getElementById('startingFormHead');

        function toggleDateFields(showCustom) {
            startingFormHead.innerHTML = showCustom ?
                "Select Date<i class='bx bx-arrow-back' style='float: right;font-size: 13px;font-weight: 600;border-radius: 50%;padding: 2px;' onclick='startingFormBackF()' title='Back to Select Menu'></i>" :
                "Select Training Starting From";

            customDateField.required = showCustom;
            customDateField.style.display = showCustom ? 'block' : 'none';
            startingFormSelect.style.display = showCustom ? 'none' : 'block';
        }

        startingFormSelect.addEventListener('change', function() {
            toggleDateFields(this.value === 'customDate');
        });

        function startingFormBackF() {
            startingFormSelect.value = 'Select Training Starting From';
            toggleDateFields(false);
        }
    </script>




    <script>
        document.getElementById("email").addEventListener("input", function() {
            var email = document.getElementById("email").value;
            var emailError = document.getElementById("emailError");

            if (!validateEmail(email)) {
                emailError.textContent = "*Invalid email address";
            } else {
                emailError.textContent = "";
            }
        });

        function validateEmail(email) {

            var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return re.test(email);
        }

        document.getElementById("phone").addEventListener("input", function() {
            var phone = document.getElementById("phone").value;
            var phoneError = document.getElementById("phoneError");

            if (!validatePhoneNumber(phone)) {
                phoneError.textContent = "*10 digits required";
            } else {
                phoneError.textContent = "";
            }
        });







        document.getElementById("TA").addEventListener("input", function() {
            var TA = Number(document.getElementById("TA").value);
            var PA = Number(document.getElementById("PA").value);
            var TA_error = document.getElementById("TA-error");

            if (TA < PA) {
                TA_error.textContent = "*is Less Then Paid Amount";
            } else {
                TA_error.textContent = "";
            }
        });

        document.getElementById("PA").addEventListener("input", function() {
            var TA = Number(document.getElementById("TA").value);
            var PA = Number(document.getElementById("PA").value);
            var PA_error = document.getElementById("PA-error");

            if (PA > TA) {
                PA_error.textContent = "*Is Greater Then Total Amount";
            } else {
                PA_error.textContent = "";
            }
        });






        function validatePhoneNumber(phone) {

            var re = /^\d{10}$/;
            return re.test(phone);
        }
    </script>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#phone').keyup(function() {
                var phone = $(this).val();
                $.ajax({
                    url: '../api_ajax/check_phone.php',
                    method: 'GET',
                    data: {
                        'phone': phone
                    },
                    dataType: 'json', // Ensure that the response is parsed as JSON
                    success: function(data) {
                        // Log the data received from the server for debugging
                        console.log(data);

                        // Check if the 'exists' field exists in the response
                        if ('exists' in data) {
                            if (data.exists) {
                                $('#error-msg-phone').text(data.message);
                                $('#error-msg-phone').css('display', 'block');
                            } else {
                                $('#error-msg-phone').text('');
                                $('#error-msg-phone').css('display', 'none');
                            }
                        } else {
                            console.error('Invalid response from server. Missing "exists" field.');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
                });
            });
        });
    </script>

    <!-- <script>
   function getTimeSlot() {
    // Listen for change event on the time slot select box
    $('select[name="time-slot"]').change(function() {
        // Get the selected time slot
        var selectedTimeSlot = $(this).val();

        // Log the selected time slot to the console
        console.log('Selected Time Slot:', selectedTimeSlot);
    });
}

$('#preBook').change(function() {
    if ($(this).is(':checked')) {
        // If "Pre-Book" checkbox is checked, wait for the user to select a time slot
        getTimeSlot();
    } else {
        // If "Pre-Book" checkbox is unchecked, remove the change event listener
        $('select[name="time-slot"]').off('change');
    }
});

    </script> -->



    <!-- <script>
        $(document).ready(function() {
            $('#email').keyup(function() {
                var email = $(this).val();

                $.ajax({
                    type: 'GET',
                    url: '../api_ajax/check_email_exist.php',
                    data: {
                        email: email
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.isValid) {
                            $('#error-msg-phone').text('Email is valid');
                            $('#error-msg-phone').css('display', 'block');
                        } else {
                            $('#error-msg-phone').text('Email is not valid');
                            $('#error-msg-phone').css('display', 'block');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Request failed:', error);
                    }
                });
            });
        });
    </script> -->

</body>

</html>