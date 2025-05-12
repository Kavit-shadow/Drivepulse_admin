<?php


include('../includes/authenticationStaff.php');
authenticationStaff('../');

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
        $start_date = $startedAT;
        $end_date = $EndsTemp;

        $start = new DateTime($start_date);
        $end = new DateTime($end_date);
        $interval = new DateInterval('P1D');
        $period = new DatePeriod($start, $interval, $end);

        $count = 0;
        foreach ($period as $date) {
            if ($date->format('N') == 7) {
                $count++;
            }
        }


        $Ends_On = date('Y-m-d', strtotime($EndsTemp . ' +' . $count . ' days'));


        if (date('N', strtotime($Ends_On)) == 7) {

            $Ends_On = date('Y-m-d', strtotime($Ends_On . ' +1 day'));
        }
        // echo $count . "</br>";

    } else {
        $Ends_On = date('Y-m-d', strtotime($currentDate . ' +' . $days . ' days'));
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
        $distance = $_POST['distance'];

        $CarDetails = "Four Wheeler  ";
        $CarDetails .= $carName;

        $TandD = $distance . "Km ";

        $vehicleDetails = $CarDetails . "/ " . $TandD;
    } elseif ($vehicle == '2wheeler') {

        $bikeName = "Two Wheeler";
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

                $checkIfAny = "SELECT * FROM `pre_book_queue` WHERE timeslot = '$timeSlot' AND vehicle = '$carName'";
                $resultCheckIfAny = mysqli_query($conn, $checkIfAny);
                if (mysqli_num_rows($resultCheckIfAny) > 0) {
                    while ($row = $resultCheckIfAny->fetch_assoc()) {
                        if ($row['status'] = 'active') {
                            $priority = (int) $row['priority'] + 1;
                        }
                    }
                }

                $insertPreBook = "INSERT INTO `pre_book_queue`(`priority`, `timeslot`, `name`, `phone`, `vehicle`, `trainer`, `start_date`, `end_date`, `status`) VALUES ('$priority','$timeSlot','$name','$phone','$carName','$Tname','$startedAT','$Ends_On','active')";

                $resultPreBookInsert = mysqli_query($conn, $insertPreBook);

                if (!isset($result)) {
                    $error[] = "Error inserting data in Pre Book Queue: " . mysqli_error($conn);
                }
            } else {

                if ($carName == "i10") {

                    $CheckStatus = "SELECT * FROM `car_one` WHERE timeslots = '$timeSlot'";
                    $result = mysqli_query($conn, $CheckStatus);
                    $row = mysqli_fetch_assoc($result);
                    if ($row['status'] != "empty") {

                        $error[] = $timeSlot . '  Time Slot is already Taken in ' . $carName . '!';
                    } else {

                        $updata = "UPDATE `car_one` SET name='$name', phone='$phone', vehicle='$vehicleDetails', trainer='$Tname', start_date='$startedAT', end_date='$Ends_On', status='active' WHERE timeslots = '$timeSlot'";
                        mysqli_query($conn, $updata);
                        if (!isset($result)) {
                            $error[] = "Error inserting data in Time Table: " . mysqli_error($conn);
                        }
                    }
                }
                if ($carName == "Liva") {

                    $CheckStatus = "SELECT * FROM `car_two` WHERE timeslots = '$timeSlot'";
                    $result = mysqli_query($conn, $CheckStatus);
                    $row = mysqli_fetch_assoc($result);
                    if ($row['status'] != "empty") {

                        $error[] = $timeSlot . '  Time Slot is already Taken in ' . $carName . '!';
                    } else {

                        $updata = "UPDATE `car_two` SET name='$name', phone='$phone', vehicle='$vehicleDetails', trainer='$Tname', start_date='$startedAT', end_date='$Ends_On', status='active' WHERE timeslots = '$timeSlot'";
                        mysqli_query($conn, $updata);
                        if (!isset($result)) {
                            $error[] = "Error inserting data in Time Table: " . mysqli_error($conn);
                        }
                    }
                }
            }

            if (!isset($error)) {


                $days += 1;
                $insert = "INSERT INTO `cust_details`(`name`, `email`, `phone`, `address`, `totalamount`, `paidamount`, `dueamount`, `days`, `timeslot`, `vehicle`, `newlicence`, `trainername`, `trainerphone`, `date`, `time`, `endedAT`,  `startedAT`, `formfiller`) VALUES('$name','$email','$phone','$address', '$totalA','$paidA','$dueA','$days', '$timeSlot', '$vehicleDetails','$add_ons', '$Tname', '$Tnum','$currentDate','$currentTime','$Ends_On','$startedAT','" . $_SESSION['staff_name'] . "')";


                $result = mysqli_query($conn, $insert);


                if (isset($result)) {

                    logActivity('staff_logs', $_SESSION['staff_name'], array("What" => (isset($_POST['preBook'])) ? "Pre Booked Admission, Priority: $priority" : "Booked Admission", array("customer_details" => array("name" => $name, "phone" => $phone, "vehicle" => $vehicleDetails, "timeSlot" => $timeSlot, "addmission_date" => $currentDate, "days" => $days, "started_at" => $startedAT, "ended_at" => $Ends_On, "formfiller" => $_SESSION['staff_name']))));


                    if ($vehicle == '4wheeler') {



                        header('location:../previewPDF.php?id=' . $phone . '&email=' . $email . '&name=' . $name . '&VN=' . $CarDetails . '&TT=' . $TandD . '&who=staff');
                    } elseif ($vehicle == '2wheeler') {


                        header('location:../previewPDF.php?id=' . $phone . '&email=' . $email . '&name=' . $name . '&VN=' . $bikeName . '&TT=' . $bikeTime . '&who=staff');
                    }
                } else {
                    $error[] = "Error inserting data: " . mysqli_error($conn);
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
                            <label>Full Name</label>
                            <input type="text" name="name" placeholder="Enter Name" required>
                        </div>

                        <div class="input-field">
                            <label style="display: flex; justify-content: space-between; ">Email <span id="emailError" style="color: red;"></span></label>

                            <input type="text" id="email" name="email" placeholder="Enter Email" required>

                        </div>

                        <div class="input-field">
                            <label style="display: flex; justify-content: space-between; ">Mobile Number<span id="phoneError" style="color: red;"></span></label>
                            <input type="number" id="phone" name="phone" placeholder="Enter Mobile number" required>
                        </div>


                        <div class="input-field">
                            <label>Address</label>
                            <input type="text" name="address" placeholder="Enter Address" required>
                        </div>
                    </div>
                </div>
                <div class="details ID">

                    <span class="title">Booking Details</span>

                    <div class="fields">
                        <div class="input-field">
                            <label style="display: flex; justify-content: space-between; " >Total Amount<span id="TA-error" style="color: red;"></span></label>
                            <input type="number" name="totalA" placeholder="Enter Total Amount" required autocomplete="off" id="TA">
                        </div>

                        <div class="input-field">
                            <label style="display: flex; justify-content: space-between; " >Paid Amount<span id="PA-error" style="color: red;"></span></label>
                            <input type="number" name="paidA" placeholder="Enter Paid Amount" required autocomplete="off" id="PA">
                        </div>

                        <div class="input-field">
                            <label>Days</label>
                            <input type="text" name="days" placeholder="Enter Days" required autocomplete="off">
                        </div>

                        <div class="input-field" id="timeSlotsContainer">
                            <label>‎ </label>
                            <input type="text" id="tempcarinput" placeholder="Select a vehicle to select timeslot" disabled>
                        </div>

                        <div class="input-field">
                            <label>Select vehicle</label>
                            <select name="vehicle" onchange="showFields(this)" id="selectVehicle" required>
                                <option disabled selected>Select Vehicle</option>
                                <option value="4wheeler">Four Wheeler</option>
                                <option value="2wheeler">Two Wheeler</option>
                            </select>
                        </div>

                        <div class="input-field">
                            <label id="startingFormHead">Select Training Starting From</label>
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
                            <label>Sundays</label>
                            <select name="ARSundays" required>
                                <option value="remove" selected>Dont Count</option>
                                <option value="add">Count</option>
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

                            <div class="input-field">
                                <label>Car Name</label>
                                <!-- <input type="text" name="carName" id="carName"> -->
                                <select name="carName" id="carName" required>
                                    <option disabled selected>Select Car</option>
                                    <option value="i10">Hyundai i10</option>
                                    <option value="Liva">Toyota Liva</option>
                                </select>
                            </div>

                            <div class="input-field">
                                <label>Distance [ Kilometers ]</label>
                                <input type="text" name="distance" id="distance" placeholder="Enter Distance">
                            </div>

                        </div>

                        <div id="bikeFields" style="display: none;">
                            <div class="input-field">
                                <label>Time [ mins ]</label>
                                <input type="text" name="bikeTime" id="bikeTime" placeholder="Enter Duration">
                            </div>
                        </div>




                    </div>




                </div>

                <div class="trainer-details">
                    <span class="title">Trainer Details</span>
                    <div class="fields" style="justify-content:flex-start; gap: 40px;">

                        <div class="input-field">
                            <label>Trainer Name</label>
                            <input type="name" autocomplete="off" name="Tname" placeholder="Enter Name" required>
                        </div>

                        <div class="input-field">
                            <label>Trainer Number</label>
                            <input type="number" autocomplete="off" name="Tnumber" placeholder="Enter Number" required>
                        </div>
                    </div>
                    <!-- <button class="nextBtn"> -->
                    <!-- <span class="btnText"> -->
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
                    <!-- <i class="uil uil-navigator"></i> -->
                    <!-- </button> -->
                </div>

        </form>

    </div>



    <script>
        function showFields(select) {
            var carFields = document.getElementById("carFields");
            var bikeFields = document.getElementById("bikeFields");

            if (select.value === "4wheeler") {
                carFields.style.display = "flex";
                carFields.style.gap = "50px";
                carFields.style.width = "100%"
                bikeFields.style.display = "none";
            } else if (select.value === "2wheeler") {
                carFields.style.display = "none";
                bikeFields.style.display = "flex";
                bikeFields.style.gap = "50px";
                bikeFields.style.width = "100%"
            }
        }
    </script>
    <script>
        function updateSelected(dropdown, selectedValue) {
            dropdown.val(selectedValue);
        }

        function loadCarContent(car) {
            $.ajax({
                url: '../cars/' + car + '.php',
                type: 'GET',
                success: function(data) {
                    $('#timeSlotsContainer').html(data);
                },
                error: function() {
                    $('#timeSlotsContainer').html('Error loading car content.');
                }
            });
        }

        function loadBikeContent(bike) {
            $.ajax({
                url: `../cars/${bike}.php`,
                type: 'GET',
                success: function(data) {
                    $('#timeSlotsContainer').html(data);
                },
                error: function() {
                    $('#timeSlotsContainer').html('Error loading bike content.');
                }
            });
        }
        const twowheeler = document.getElementById('selectVehicle');
        const carNameSelect = document.getElementById('carName');
        const preBookCheckBox = document.getElementById("preBook");

        twowheeler.addEventListener('change', function() {

            const cartemp1 = document.getElementById('carName');

            if (twowheeler.value === '4wheeler') {
                if (preBookCheckBox.checked) {
                    cartemp1.value = 'i10';
                    loadCarContent('preBook/i10');
                } else {
                    cartemp1.value = 'i10';
                    loadCarContent('i10');
                }
            } else if (twowheeler.value === '2wheeler') {
                const selectedBike = this.value;

                if (preBookCheckBox.checked) {
                    if (selectedBike) {
                        loadBikeContent('preBook/bike');
                    }
                } else {
                    if (selectedBike) {
                        loadBikeContent('bike');
                    }
                }
            }
        });


        carNameSelect.addEventListener('change', function() {
            const selectedCar = this.value;
            if (preBookCheckBox.checked) {
                if (selectedCar) {
                    loadCarContent(`preBook/${selectedCar}`);
                }
            } else {
                if (selectedCar) {
                    loadCarContent(selectedCar);
                }
            }
        });
    </script>

    <script>
        // Get references to the select and input elements
        const startingFormSelect = document.getElementById('startingForm');
        const customDateField = document.getElementById('customDateField');
        const startingFormHead = document.getElementById('startingFormHead');
        const startingFormBack = document.getElementById('startingFormBack');
        // Add an event listener to the select element
        startingFormSelect.addEventListener('change', function() {
            if (startingFormSelect.value === 'customDate') {
                startingFormHead.innerHTML = "Select Date<i class='bx bx-arrow-back' id='startingFormBack' style='float: right;font-size: 13px;font-weight: 600;border-radius: 50%;padding: 2px;' onclick='startingFormBackF()' title='Back to Select Menu'></i>";
                customDateField.required = true;
                customDateField.style.display = 'block';
                startingFormSelect.style.display = "none";
            } else {

                startingFormSelect.style.display = "block";
                customDateField.style.display = 'none';
            }
        });

        function startingFormBackF() {
            customDateField.required = false;
            startingFormHead.innerHTML = "Select Training Starting From";
            startingFormSelect.value = 'Select Training Starting From';
            startingFormSelect.style.display = "block";
            customDateField.style.display = 'none';
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