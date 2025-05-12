<?php

include('../../includes/authentication.php');
authenticationAdmin('../../');


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



// if (isset($_POST['edit'])) {


//     $id = $_POST['id'];
//     $name = $_POST['name'];
//     $email = $_POST['email'];
//     $phone = $_POST['phone'];




//     $conn = mysqli_connect("localhost", "root", "", "billing");

//     // Personal Details
//     $name = $_POST['name'];
//     $email = $_POST['email'];
//     $phone = $_POST['phone'];
//     $address = $_POST['address'];

//     // Booking Details
//     $totalA = $_POST['totalamount'];
//     $paidA = $_POST['paidamount'];
//     $dueA = $totalA - $paidA;
//     $days = $_POST['days'];
//     $timeSlot = $_POST['time-slot'];
//     $vehicle = $_POST['vehicle'];
//     $boolLicence = $_POST['newlicence'];
//     $trainername = $_POST['trainername'];
//     $trainerphone = $_POST['trainerphone'];
//     $formfiller = $_POST['formfiller'];

//     $update = "UPDATE `cust_details` SET `name`='$name',`email`='$email',`phone`='$phone',`address`='$address',`totalamount`='$totalA',`paidamount`='$paidA',`dueamount`='$dueA',`days`='$days',`timeslot`='$timeSlot',`vehicle`='$vehicle',`newlicence`='$boolLicence',`trainername`='$trainername',`trainerphone`='$trainerphone',`formfiller`='$formfiller' WHERE `id`='$id' ";
//     $result = mysqli_query($conn, $update);
//     if (!$result) {

//         die("Error updating row: " . mysqli_error($conn));

//     }
//     // } else {
//     //     header('location:' . $_GET['route']);
//     // }
// }


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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- My CSS -->
    <link rel="stylesheet" href="../../css/adminDashboard.css">
    <link rel="stylesheet" href="../../css/sideBarFooter.css">
    <style>
        .profile-container {
            max-width: 900px;
            margin: 50px auto;
            background-color: #f9f9f9;
            border-radius: 7px;
            padding: 20px;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;

        }

        .profile-image {
            flex: 0 0 120px;
            text-align: center;
            margin-right: 3rem;
        }

        .profile-image img {
            width: 120px;
            height: 120px;
            border-radius: 50%;
        }

        .profile-details {
            flex: 1;
        }

        .profile-details p {
            margin: 5px 0;
        }

        .profile-details label {
            font-weight: bold;
        }

        .profile-buttons {
            margin-top: 4rem;
            display: flex;
            justify-content: center;
        }

        .profile-buttons input[type="button"] {
            padding: 1rem 5rem;
            margin-left: 10px;
            border: none;
            border-radius: 3px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            text-align: center;
        }

        .profile-buttons .edit-btn {

            background-color: #4CAF50;
            color: #fff;

        }

        .profile-buttons .pdf-btn {

            background-color: #2196F3;
            color: #fff;
            text-align: center;
        }

        .profile-buttons .close-btn {

            background-color: #f44336;
            color: #fff;

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

            .profile-buttons {
                margin-top: 3rem;
                width: 100%;
                justify-content: center;
                align-items: center;
            }
        }


        .profile-details form input {
            padding: .5rem;
            font-weight: 400;
            margin-left: 1rem;
        }

        form {
            display: flex;
            flex-direction: column;
            max-width: 500px;
            /* Set max width for the form */
            margin: 0 auto;
        }

        p {
            display: flex;
            justify-content: space-between;
            margin-bottom: 15px;
            align-items: center;
        }

        label {
            width: 150px;
            font-weight: bold;
        }

        input[type="text"],
        select {
            width: 100%;
            padding: 5px;
            margin-left: 10px;
            flex-grow: 1;
            /* Makes the input field grow */
        }

        select[disabled],
        input[disabled] {
            background-color: #f5f5f5;
            /* Optional: make disabled inputs look different */
        }

        input[type="text"]:disabled {
            color: #888;
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

        <?php
        // $connect = mysqli_connect("localhost", "root", "", "billing");
        $backRoute = '';
        if (isset($_GET['id'])) {

            try {
                $backRoute = $_GET['route'];
            } catch (Exception $e) {
                $backRoute = "../";
            }
            $id = $_GET['id'];
            $query = "SELECT * FROM `cust_details` WHERE phone = $id";
            $result = mysqli_query($conn, $query);
            $row = mysqli_fetch_assoc($result);
        ?>

            <main>
                <div class="head-title">
                    <div class="left">
                        <h1>Edit or Remove Customer</h1>
                        <ul class="breadcrumb">
                            <li>
                                <a class="active" style=" color: #aaaaaa;" href="../">Dashboard</a>
                            </li>
                            <li><i class='bx bx-chevron-right'></i></li>
                            <li>
                                <a class="active" style=" color: #aaaaaa;" href="<?php echo $backRoute; ?>">View Details</a>
                            </li>
                            <li><i class='bx bx-chevron-right'></i></li>
                            <li>
                                <a class="active" href="./">Edit or Remove Customer</a>
                            </li>
                        </ul>
                    </div>
                    <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
                </div>








                <div class="profile-container">
                    <div class="profile-image">
                        <!-- <img src="../../api_ajax/generate_image.php?name=<?php // echo $row["name"]; 
                                                                                ?>" alt="Profile Image"> -->
                        <img src="../../assets/Default_Profile.png" alt="Profile Image">
                    </div>
                    <div class="profile-details" style="padding-right: 50px;">

                        <form method="post" id="delete-form" style="display: none;">
                            <input type="hidden" name="DELid" value="<?php echo $row["id"]; ?>">

                            <input type="text" name="DELname" value="<?php echo $row["name"]; ?>">

                            <input type="text" name="DELemail" value="<?php echo $row["email"]; ?>">

                            <input type="text" name="DELphone" value="<?php echo $row["phone"]; ?>">

                            <input type="hidden" name="additionalData" id="additionalData">
                        </form>

                        <form method="post" id="edit-form">

                            <input type="hidden" name="id" value="<?php echo $row["id"]; ?>">

                            <p><label>Name:</label>
                                <input type="text" name="name" value="<?php echo $row["name"]; ?>">
                            </p>

                            <p><label>Email:</label>
                                <input type="text" name="email" value="<?php echo $row["email"]; ?>">
                            </p>

                            <p><label>Phone:</label>
                                <input type="text" name="phone" value="<?php echo $row["phone"]; ?>">
                            </p>

                            <p><label>Address:</label>
                                <input type="text" name="address" value="<?php echo $row["address"]; ?>">
                            </p>
                            
                             <p><label>Payment Method:</label>
                                <select name="payment_method">
                                    <option value="cash" <?php echo ($row["payment_method"] == "cash") ? "selected" : ""; ?>>Cash</option>
                                    <option value="bank" <?php echo ($row["payment_method"] == "bank") ? "selected" : ""; ?>>Bank</option>
                                </select>
                            </p>

                            <p><label>Total Amount:</label>
                                <input type="text" name="totalamount" value="<?php echo $row["totalamount"]; ?>">
                            </p>

                            <p><label>Paid Amount:</label>
                                <input type="text" name="paidamount" value="<?php echo $row["paidamount"]; ?>">
                            </p>

                            <p><label>Due Amount:</label>
                                <input type="text" name="dueamount" value="<?php echo $row["dueamount"]; ?>" readonly disabled>
                            </p>

                            <p><label>Days:</label>
                                <input type="text" name="days" value="<?php echo $row["days"]; ?>">
                            </p>

                            <p><label>Time-Slot:</label>
                                <select name="time-slot" readonly disabled>
                                    <option disabled selected>Select Time Slot</option>

                                    <?php
                                    $timeSlots = array(
                                        "7:00am to 7:30am",
                                        "7:30am to 8:00am",
                                        "8:00am to 8:30am",
                                        "8:30am to 9:00am",
                                        "9:00am to 9:30am",
                                        "9:30am to 10:00am",
                                        "10:00am to 10:30am",
                                        "10:30am to 11:00am",
                                        "11:00am to 11:30am",
                                        "11:30am to 12:00pm",
                                        "12:00pm to 12:30pm",
                                        "12:30pm to 1:00pm",
                                        "1:00pm to 1:30pm",
                                        "1:30pm to 2:00pm",
                                        "2:00pm to 2:30pm",
                                        "2:30pm to 3:00pm",
                                        "3:00pm to 3:30pm",
                                        "3:30pm to 4:00pm",
                                        "4:00pm to 4:30pm",
                                        "4:30pm to 5:00pm",
                                        "5:00pm to 5:30pm",
                                        "5:30pm to 6:00pm",
                                        "6:00pm to 6:30pm",
                                        "6:30pm to 7:00pm",
                                        "7:00pm to 7:30pm",
                                        "7:30pm to 8:00pm"
                                    );

                                    $timeSlotsTWO = array(
                                        "7:00am to 7:45am",
                                        "7:45am to 8:30am",
                                        "8:30am to 9:15am",
                                        "9:15am to 10:00am",
                                        "10:00am to 10:45am",
                                        "10:45am to 11:30am",
                                        "11:30am to 12:15pm",
                                        "12:15pm to 1:00pm",
                                        "1:00pm to 1:45pm",
                                        "1:45pm to 2:30pm",
                                        "2:30pm to 3:15pm",
                                        "3:15pm to 4:00pm",
                                        "4:00pm to 4:45pm",
                                        "4:45pm to 5:30pm",
                                        "5:30pm to 6:15pm",
                                        "6:15pm to 7:00pm",
                                        "7:00pm to 7:45pm",
                                        "7:45pm to 8:30pm"
                                    );
                                    $selectedTimeSlot = $row["timeslot"];


                                    if (in_array($selectedTimeSlot, $timeSlots)) {
                                        foreach ($timeSlots as $timeSlot) {
                                            $selected = ($timeSlot === $selectedTimeSlot) ? "selected" : "";
                                            echo "<option value='$timeSlot' $selected>$timeSlot</option>";
                                        }
                                    } elseif (in_array($selectedTimeSlot, $timeSlotsTWO)) {
                                        foreach ($timeSlotsTWO as $timeSlot) {
                                            $selected = ($timeSlot === $selectedTimeSlot) ? "selected" : "";
                                            echo "<option value='$timeSlot' $selected>$timeSlot</option>";
                                        }
                                    } else {
                                        echo "$timeToCheck is not found in any array.";
                                    }


                                    ?>
                                </select>
                            </p>

                            <p><label>Vehicle:</label>
                                <input type="text" name="vehicle" value="<?php echo $row["vehicle"]; ?>" readonly disabled>
                            </p>

                            <p><label>New Licence:</label>
                                <select name="newlicence">
                                    <?php
                                    $boolLicence = array("Applied" => "Applied", "Not Applied" => "Not Applied");
                                    $select = $row["newlicence"];
                                    foreach ($boolLicence as $Licence) {
                                        $selected = ($Licence == $select) ? "selected" : "";
                                        echo "<option value='$Licence' $selected>$Licence</option>";
                                    }
                                    ?>
                                </select>
                            </p>

                            <p><label>Trainer Name:</label>
                                <select name="trainername" id="trainername_select" onchange="handleTrainerSelect(this)">
                                    <option value="" selected disabled>Select Trainer</option>
                                    <option value="custom">Custom Entry</option>
                                    <?php
                                    $current_trainer = $row["trainername"];
                                    if ($current_trainer) {
                                        echo "<option value='$current_trainer' selected>$current_trainer</option>";
                                    }
                                    ?>
                                </select>
                                <input type="text" name="trainername_custom" id="trainername_custom" value="<?php echo $row["trainername"]; ?>" style="display:none;">
                            </p>


                            <p><label>Trainer Phone:</label>
                                <input type="text" name="trainerphone" id="trainerphone_input" value="<?php echo $row["trainerphone"]; ?>" disabled readonly>
                            </p>
                            <script>
                                $(document).ready(function() {
                                    // Show loader
                                    $('#trainername_select').html(`
                                        <option value="">Loading trainers...</option>
                                    `);
                                    $('#trainername_select').prop('disabled', true);

                                    $.ajax({
                                        url: '../../api_ajax/get_employees.php',
                                        method: 'GET',
                                        dataType: 'json',
                                        success: function(response) {
                                            $('#trainername_select').empty();
                                            $('#trainername_select').append(`
                                                <option value="" selected disabled>Select Trainer</option>
                                                <option value="custom">Custom Entry</option>
                                            `);

                                            let currentTrainerFound = false;
                                            const currentTrainer = '<?php echo $row["trainername"]; ?>';
                                            const currentPhone = '<?php echo $row["trainerphone"]; ?>';

                                            if (response.success && response.employees.length > 0) {
                                                response.employees.forEach(employee => {
                                                    if (employee.name === currentTrainer) {
                                                        currentTrainerFound = true;
                                                        $('#trainername_select').append(`
                                                            <option value="${employee.name}" data-phone="${employee.phone}" selected>
                                                                ${employee.name}
                                                            </option>
                                                        `);
                                                        $('#trainerphone_input').val(employee.phone);
                                                    } else {
                                                        $('#trainername_select').append(`
                                                            <option value="${employee.name}" data-phone="${employee.phone}">
                                                                ${employee.name}
                                                            </option>
                                                        `);
                                                    }
                                                });
                                            }

                                            // If current trainer not found in employees list, treat as custom
                                            if (!currentTrainerFound && currentTrainer) {
                                                $('#trainername_select').val('custom');
                                                $('#trainername_custom').show().val(currentTrainer);
                                                $('#trainerphone_input').val(currentPhone);
                                                $('#trainerphone_input').prop('disabled', false)
                                                                      .prop('readonly', false)
                                                                      .prop('required', true)
                                                                      .prop('inputmode', 'numeric');
                                            }

                                            $('#trainername_select').prop('disabled', false);
                                        },
                                        error: function(xhr, status, error) {
                                            console.error('Error fetching trainers:', error);
                                            $('#trainername_select').html(`
                                                <option value="">Error loading trainers</option>
                                            `);
                                            $('#trainername_select').prop('disabled', true);
                                        }
                                    });

                                    // Add change handler for trainer select
                                    $('#trainername_select').on('change', function() {
                                        const selectedOption = $(this).find('option:selected');
                                        const phone = selectedOption.data('phone');
                                        const originalPhone = '<?php echo $row["trainerphone"]; ?>';
                                        
                                        if ($(this).val() === 'custom') {
                                            $('#trainerphone_input').val(originalPhone);
                                        } else if (phone) {
                                            $('#trainerphone_input').val(phone);
                                            $('#trainerphone_input').prop('disabled', true);
                                            $('#trainerphone_input').prop('readonly', true);

                                        }
                                    });
                                });

                                function handleTrainerSelect(select) {
                                    var customInput = document.getElementById('trainername_custom');
                                    var originalPhone = '<?php echo $row["trainerphone"]; ?>';
                                    
                                    if (select.value === 'custom') {
                                        customInput.style.display = 'block';
                                        customInput.focus();
                                        // Restore original phone number for custom entry
                                        $('#trainerphone_input').val(originalPhone);
                                        $('#trainerphone_input').prop('disabled', false);
                                        $('#trainerphone_input').prop('readonly', false);
                                        $('#trainerphone_input').prop('required', true);
                                        $('#trainerphone_input').prop('inputmode', 'numeric');
                                    } else {
                                        customInput.style.display = 'none';
                                        // Update phone when selecting non-custom trainer
                                        const selectedOption = $(select).find('option:selected');
                                        const phone = selectedOption.data('phone');
                                        if (phone) {
                                            $('#trainerphone_input').val(phone);
                                        }
                                    }
                                }
                            </script>

                            <p><label>Admission Date:</label>
                                <input type="text" name="date" value="<?php echo $row["date"]; ?>" readonly disabled>
                            </p>

                            <p><label>Admission Time:</label>
                                <input type="text" name="time" value="<?php echo $row["time"]; ?>" readonly disabled>
                            </p>

                            <p><label>Training Started On:</label>
                                <input type="text" name="startedAT" value="<?php echo $row["startedAT"]; ?>" readonly disabled>
                            </p>

                            <p><label>Training Ended On:</label>
                                <input type="text" name="endedAT" value="<?php echo $row["endedAT"]; ?>" readonly disabled>
                            </p>

                            <p><label>Form Filler:</label>
                                <input type="text" name="formfiller" value="<?php echo $row["formfiller"]; ?>">
                            </p>
                        </form>

                    </div>
                    <div class="profile-buttons" style="
                        display: flex;
                        justify-content: flex-start;
                        flex-direction: column;
                        flex-wrap: wrap;
                        gap: 20px;">

                        <input type="button" class="edit-btn" name="edit" value="Save" readonly>
                        <input type="button" class="close-btn delete-btn" name="Delete" value="Delete" readonly>

                        <!-- <button type="button" class="edit-btn">Save</button>
                        <button type="button" class="close-btn delete-btn" >Delete</button> -->


                    </div>


                    </form>




                </div>
            <?php
        }
            ?>



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
    <script src="../../js/sweetalert.js"></script>
    <script src="../../js/toggleSideBar.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/hideSideBar.js"></script>

</body>

</html>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


<script>
    document.addEventListener("DOMContentLoaded", function() {

        document.querySelector(".delete-btn").addEventListener("click", function(e) {
            // Show a SweetAlert confirmation dialog
            Swal.fire({
                title: "Confirm Removal",
                text: `Are you sure you want to remove this customer?`,
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Yes, remove it",
                cancelButtonText: "Cancel",
            }).then((result) => {
                if (result.isConfirmed) {
                    // If the user confirms, submit the form
                    document.getElementById("additionalData").value = "some value";
                    document.getElementById("delete-form").submit();
                }
            });
        });

    });


    $(document).ready(function() {
        // Add an event listener to the edit button
        $(".edit-btn").on("click", function(e) {
            e.preventDefault(); // Prevent the default form submission

            // Show a SweetAlert confirmation dialog
            Swal.fire({
                title: "Confirm Edit",
                text: "Are you sure you want to edit this profile?",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#4CAF50",
                cancelButtonColor: "#f44336",
                confirmButtonText: "Yes, edit it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    // Collect form data
                    const formData = {
                        id: $("input[name='id']").val(),
                        name: $("input[name='name']").val(),
                        email: $("input[name='email']").val(),
                        phone: $("input[name='phone']").val(),
                        address: $("input[name='address']").val(),
                        totalamount: $("input[name='totalamount']").val(),
                        paidamount: $("input[name='paidamount']").val(),
                        days: $("input[name='days']").val(),
                        timeslot: $("select[name='time-slot']").val(),
                        vehicle: $("input[name='vehicle']").val(),
                        newlicence: $("select[name='newlicence']").val(),
                        trainername: $("select[name='trainername']").val() === 'custom' ? $("input[name='trainername_custom']").val() : $("select[name='trainername']").val(),
                        trainerphone: $("input[name='trainerphone']").val(),
                        formfiller: $("input[name='formfiller']").val(),
                        payment_method: $("select[name='payment_method']").val()
                    };

                    console.log(JSON.stringify(formData));


                    // Send an AJAX POST request to your API endpoint
                    $.ajax({
                        url: "./edit.php", // Replace with your API endpoint
                        type: "POST",
                        data: JSON.stringify(formData), // Convert form data to JSON
                        contentType: "application/json; charset=utf-8",
                        success: function(response) {
                            // Handle success response from the API
                            Swal.fire({
                                title: "Success",
                                text: "Profile updated successfully!",
                                icon: "success"
                            }).then(() => {
                                // Optional: Reload the page or redirect after success
                                const currentUrl = new URL(window.location.href);
                                currentUrl.searchParams.set('id', response.phone);
                                window.location.href = currentUrl.toString();
                            });
                        },
                        error: function(xhr, status, error) {
                            // Handle error response from the API
                            console.log(xhr.responseJSON);
                            Swal.fire({
                                title: "Error",
                                text: "Something went wrong! Please try again. " + xhr.responseJSON.message,
                                icon: "error"
                            });
                        }
                    });
                }
            });
        });
    });
</script>





<?php
// $conn = mysqli_connect("localhost", "root", "", "billing");


function checkIfExistInTables($conn, $table, $phone, $name): bool
{
    $stmt = mysqli_prepare($conn, "SELECT * FROM `$table` WHERE phone = ? AND name = ?");
    mysqli_stmt_bind_param($stmt, "ss", $phone, $name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result) > 0;
}

function getCustID($conn, $phone, $name, $table)
{
    $stmt = mysqli_prepare($conn, "SELECT id FROM `$table` WHERE phone = ? AND name = ?");
    mysqli_stmt_bind_param($stmt, "ss", $phone, $name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        return $row['id'];
    }
    return null;
}

function updateTimeTable($conn, $id, $table): bool
{
    $stmt = mysqli_prepare($conn, "UPDATE `$table` SET name='', phone='', vehicle='', trainer='', start_date='', end_date='', status='empty' WHERE id = ?");
    mysqli_stmt_bind_param($stmt, "i", $id);
    return mysqli_stmt_execute($stmt);
}

function checkPreBook($conn, $phone, $name): bool
{
    $stmt = mysqli_prepare($conn, "SELECT * FROM `pre_book_queue` WHERE phone = ? OR name = ?");
    mysqli_stmt_bind_param($stmt, "ss", $phone, $name);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    return mysqli_num_rows($result) > 0;
}

function delFromPreBook($conn, $phone, $name): bool
{
    $stmt = mysqli_prepare($conn, "DELETE FROM `pre_book_queue` WHERE phone = ? OR name = ?");
    mysqli_stmt_bind_param($stmt, "ss", $phone, $name);
    return mysqli_stmt_execute($stmt);
}

function delFromCustTable($conn, $Custid, $phone, $name): bool
{
    $stmt = mysqli_prepare($conn, "DELETE FROM `cust_details` WHERE id = ? AND phone = ? AND name = ?");
    mysqli_stmt_bind_param($stmt, "iss", $Custid, $phone, $name);
    return mysqli_stmt_execute($stmt);
}

function delCustFromEveryWhere($conn, $name, $phone, $Custid): bool
{
    // First check and delete from pre_book_queue if exists
    if (checkPreBook($conn, $phone, $name)) {
        delFromPreBook($conn, $phone, $name);
    }

    $result = mysqli_query($conn, "SELECT data_base_table FROM vehicles");
    if (!$result) {
        return false;
    }

    $tables = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $tables[] = $row['data_base_table'];
    }

    foreach ($tables as $table) {
        if (checkIfExistInTables($conn, $table, $phone, $name)) {
            $id = getCustID($conn, $phone, $name, $table);
            if ($id) {
                updateTimeTable($conn, $id, $table);
            }
        }
    }

    return delFromCustTable($conn, $Custid, $phone, $name);
}

if (isset($_POST['DELid'])) {
    $id = $_POST['DELid'];
    $name = $_POST['DELname'];
    $phone = $_POST['DELphone'];

    $stmt = mysqli_prepare($conn, "SELECT vehicle FROM `cust_details` WHERE id = ? AND name = ? AND phone = ?");
    mysqli_stmt_bind_param($stmt, "iss", $id, $name, $phone);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        $success = false;

        if (strpos($row['vehicle'], 'Two Wheeler') === 0) {
            $success = delFromCustTable($conn, $id, $phone, $name);
        } else {
            $success = delCustFromEveryWhere($conn, $name, $phone, $id);
        }

        if ($success) {
            logActivity('admin_logs', $_SESSION['admin_name'], "Customer $name has been removed from database | Details:- Name: $name Phone: $phone");

            echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Removed!', 
                    text: 'Customer $name has been removed',
                    showConfirmButton: false,
                    timer: 2000
                });
                setTimeout(function() {
                    window.location.href = '../';
                }, 2000);
            </script>";
            exit();
        }
    }

    echo '<script>Swal.fire("Error", "An error occurred while processing the request", "error");</script>';
}
?>


</body>

</html>