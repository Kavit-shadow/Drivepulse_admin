<?php

include('../../../includes/authentication.php');
authenticationAdmin('../../../');
date_default_timezone_set('Asia/Kolkata');
// $conn = mysqli_connect("localhost", "root", "", "billing");
include('../../../config.php');

function convertDMY($dateString)
{
    if ($dateString === "0000-00-00") {
        echo "00-00-00";
    } else {

        $date = new DateTime($dateString);
        $formattedDate = $date->format("d-m-Y");
        echo $formattedDate;
    }
}



?>



<?php
$configMailPath = "../../../configMail/configMail.json";
$json_data = file_get_contents($configMailPath);
$config_data = json_decode($json_data, true);
$successCredentials = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_credentials'])) {
    $config_data[0]['config-mail']['email'] = $_POST['email'];
    $config_data[0]['config-mail']['password'] = $_POST['password'];

    file_put_contents($configMailPath, json_encode($config_data, JSON_PRETTY_PRINT));

    $successCredentials = true;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_body'])) {

    $paragraphs = array();


    for ($i = 1; isset($_POST["paragraph$i"]); $i++) {

        $paragraphs[] = '<p>' . $_POST["paragraph$i"] . '</p>';
    }


    $config_data[0]['config-mail-data']['mail-title'] = $_POST['mail_title'];
    $config_data[0]['config-mail-data']['mail-header-logo'] = $_POST['mail_header_logo'];
    $config_data[0]['config-mail-data']['mail-subject'] = $_POST['mail_subject'];
    $config_data[0]['config-mail-data']['mail-greetings'] = $_POST['mail_greetings'];
    $config_data[0]['config-mail-data']['mail-heading'] = $_POST['mail_heading'];
    $config_data[0]['config-mail-data']['mail-contact-number'] = $_POST['mail_contact_number'];
    $config_data[0]['config-mail-data']['mail-company-name'] = $_POST['mail_company_name'];
    $config_data[0]['config-mail-data']['mail-paragraph'] =  $paragraphs;

    file_put_contents($configMailPath, json_encode($config_data, JSON_PRETTY_PRINT));

    $successCredentials = true;
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <script src="../../../js/sweetalert.js"></script>


    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>

    <!-- Boxicons -->
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://kit.fontawesome.com/3db79b918b.js" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- My CSS -->
    <link rel="stylesheet" href="../../../css/adminDashboard.css">
    <link rel="stylesheet" href="../../../css/sideBarFooter.css">
    <style>
        #addPBtn {
            background-color: #4CAF50;
            /* Green */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }

        #removePBtn {
            background-color: #f44336;
            /* Green */
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 4px;
        }

        #removePBtn:hover {
            background-color: #d32f2f;
            /* Darker Red */
        }

        #addPBtn:hover {
            background-color: #45a049;
            /* Darker Green */
        }



        .container {
            display: flex;
            justify-content: space-evenly;
            align-items: start;
            flex-wrap: wrap;
            max-width: 90%;
            margin: 20px auto;
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 8px;

        }

        .container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            margin-top: 20px;
        }

        form label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        form input[type="text"] {
            width: 100%;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        .mail-credentials {
            display: flex;
            flex-direction: column;
            gap: 20px;
            width: 40%;
            background-color: #e0e0e0;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .mail-body {
            width: 40%;
            background-color: #f0f0f0;
            padding: 15px;
            border-radius: 5px;
        }

        .input-group {
            margin-bottom: 20px;
        }

        .input-group label {
            font-weight: bold;
        }

        .input-group input[type="text"] {
            margin-top: 5px;
        }

        .input-group+.input-group {
            margin-top: 10px;
        }

        .input-group:first-child {
            margin-top: 0;
        }


        textarea {
            resize: vertical;
            height: 90px;
        }

        /* Media query for responsiveness */
        @media (max-width: 768px) {


            .container {
                margin: 10px;
                padding: 10px 5px;
                width: 100%;
                align-items: center;
                gap: 20px;
                flex-direction: column;
            }

            .mail-credentials,
            .mail-body {
                width: 90%;
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

            #content main .head-title .left .breadcrumb {
                font-size: 13px;
            }
        }


        .tutorial {
            max-width: 800px;
            margin: 15px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .h11 {
            text-align: center;
            color: #333;
        }

        section {
            margin-bottom: 20px;
        }

        .h44 {
            margin-bottom: 10px;
            color: #666;
        }

        p {
            font-size: 13px;
            line-height: 1.6;
            color: #333;
        }

        a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }

        a:hover {
            color: #0056b3;
        }

        ul {
            list-style-type: disc;

        }

        li {
            line-height: 1.6;
        }

        @media (max-width: 600px) {
            .tutorial {
                padding: 15px;
            }
        }

        button {
            padding: 10px 20px;
            background-color: #46abcc;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #1397c2;
        }

        #paragraphContainer {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
    </style>

    <!-- Add SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" type="image/png" href="../../../assets/logo.png" />
    <title>Mail Credentials</title>
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
                <a href="../../">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../../admissionForm.php">
                    <i class='bx bxs-book-bookmark'></i>
                    <span class="text">Book Admission</span>
                </a>
            </li>
            <li>
                <a href="../../analytics">
                    <i class='bx bxs-bar-chart-alt-2'></i>
                    <span class="text">Analytics</span>
                </a>
            </li>
            
            <li class="search">
                <a href="../../search.php">
                    <i class='bx bx-search-alt-2'></i>
                    <span class="text">Search</span>
                </a>
            </li>
            <li>
                <a href="../../sortData/">
                    <!-- <i class='bx bxs-bar-chart-alt-2'></i> -->
                    <i class='bx bxs-data'></i>
                    <span class="text">Customers Data</span>
                </a>
            </li>
            <li class="time-table">
                <a href="../../timetable/">
                    <i class='bx bx-table'></i>
                    <span class="text">Time Table</span>
                </a>
            </li>
            <li>
                <a href="../../dueCustomers/">
                    <i class='bx bxs-user'></i>
                    <span class="text">Pending Payment</span>
                </a>
            </li>
            <li class="active">
                <a href="../">
                    <i class='bx bx-mail-send'></i>
                    <span class="text">Mail System</span>
                </a>
            </li>
            <li>
                <a href="../../manageUsers/">
                    <i class='bx bxs-group'></i>
                    <span class="text">Manage Users</span>
                </a>
            </li>
            <li>
                <a href="../../createVehicle/">
                    <i class='bx bxs-car'></i>
                    <span class="text">Add New Vehicle</span>
                </a>
            </li>
            <li>
                <a href="../../employeeManagement/">
                    <i class='bx bxs-id-card'></i>
                    <span class="text">Employee Management</span>
                </a>
            </li>
            <li>
                <a href="../../liveTrainings/">
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
                <a href="../../../logout.php" class="logout">
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
                    include("../../../configWeb.php");
                    echo $WebAppTitle;
                    ?></h3>
                <h5>Dashboard</h5>
            </span>
            <a href="index.php" class="profile">
                <img src="../../../assets/logoBlack.png">
            </a>
        </nav>
        <!-- NAVBAR -->



        <!-- MAIN -->
        <main>
            <div class="head-title" style="margin-bottom: 40px;">
                <div class="left">
                    <h1>Mail Credentials</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../../">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Mail System</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./">Mail Credentials</a>
                        </li>
                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>
            <div class="container">



                <div class="mail-body">
                    <h3>Mail Receipt Body</h3>
                    <form id="mailBodyForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                        <input type="hidden" name="submit_body" value="submit_body">

                        <div class="input-group">
                            <label>Mail Subject:</label>
                            <input type="text" name="mail_subject" value="<?php echo $config_data[0]['config-mail-data']['mail-subject']; ?>">
                        </div>
                        <div class="input-group">
                            <label>Mail Title:</label>
                            <input type="text" name="mail_title" value="<?php echo $config_data[0]['config-mail-data']['mail-title']; ?>">
                        </div>
                        <div class="input-group">
                            <label>Mail Heading:</label>
                            <input type="text" name="mail_heading" value="<?php echo $config_data[0]['config-mail-data']['mail-heading']; ?>">
                        </div>
                        <div class="input-group">
                            <label>Mail Header Logo Link:</label>
                            <input type="text" name="mail_header_logo" value="<?php echo $config_data[0]['config-mail-data']['mail-header-logo']; ?>">
                        </div>

                        <div class="input-group">
                            <label>Mail Greetings:</label>
                            <input type="text" name="mail_greetings" value="<?php echo $config_data[0]['config-mail-data']['mail-greetings']; ?>">
                        </div>
                        <div class="input-group">
                            <label>Mail Company Name:</label>
                            <input type="text" name="mail_company_name" value="<?php echo $config_data[0]['config-mail-data']['mail-company-name']; ?>">
                        </div>
                        <div class="input-group">
                            <label>Mail Contact Number:</label>
                            <input type="text" name="mail_contact_number" value="<?php echo $config_data[0]['config-mail-data']['mail-contact-number']; ?>">
                        </div>
                        <div class="input-group">
                            <label>Mail Paragraphs:</label>
                            <div id="paragraphContainer">
                                <!-- Paragraph inputs will be added here -->
                                <?php

                                if (isset($config_data[0]['config-mail-data']['mail-paragraph']) && !empty($config_data[0]['config-mail-data']['mail-paragraph'])) {

                                    foreach ($config_data[0]['config-mail-data']['mail-paragraph'] as $index => $paragraph) {
                                        $stripped_paragraph = strip_tags($paragraph);
                                        echo '<textarea name="paragraph' . $index + 1 . '" placeholder="Enter paragraph ' . $index + 1 . '">' . htmlspecialchars($stripped_paragraph) . '</textarea>';
                                    }
                                }
                                ?>
                            </div>
                        </div>


                        <button type="button" onclick="addParagraphInput()" id="addPBtn">Add Paragraph Input</button>
                        <button type="button" onclick="removeParagraphInput()" id="removePBtn">Remove Last Paragraph Input</button>




                        <button type="button" id="saveMailBodyChanges">Save Changes</button>
                    </form>
                </div>

                <div class="mail-credentials">
                    <div>
                        <h3>Mail Credentials</h3>
                        <form id="mailCredentialsForm" method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
                            <input type="hidden" name="submit_credentials" value="submit_credentials">
                            <div class="input-group">
                                <label>Email:</label>
                                <input type="text" name="email" value="<?php echo $config_data[0]['config-mail']['email']; ?>">
                            </div>
                            <div class="input-group">
                                <label>App Password:</label>
                                <input type="text" name="password" value="<?php echo $config_data[0]['config-mail']['password']; ?>">
                            </div>
                            <button type="button" id="saveMailCredentials">Save Changes</button>
                        </form>
                    </div>
                    <div>
                        <article class="tutorial">
                            <h1 class="h11">How to Get an App Password for Your Google Account</h1>
                            </br>
                            </br>
                            <section>
                                <h4 class="h44">Step 1: Access Your Google Account Settings</h4>
                                <p>Open your web browser and go to <a href="https://myaccount.google.com/">Google Account settings</a>. Sign
                                    in to your Google Account if you haven't already.</p>
                            </section>
                            <section>
                                <h4 class="h44">Step 2: Navigate to Security Settings</h4>
                                <p>In the Google Account dashboard, locate and click on the "Security" option.</p>
                            </section>
                            <section>
                                <h4 class="h44">Step 3: Enable 2-Step Verification</h4>
                                <p>Under the "Signing in to Google" section, find and select "2-Step Verification." If prompted, enter your
                                    Google Account password to proceed.</p>
                            </section>
                            <section>
                                <h4 class="h44">Step 4: Access App Passwords</h4>
                                <p>Scroll down to the bottom of the page until you find the "App passwords" section. Click on the "App
                                    passwords" option.</p>
                            </section>
                            <section>
                                <h4 class="h44">Step 5: Generate an App Password</h4>
                                <p>You will be asked to enter a name for the app or device you're generating the password for. This helps you
                                    identify it later. Enter a descriptive name and click on "Generate."</p>
                            </section>
                            <section>
                                <h4 class="h44">Step 6: Use the App Password</h4>
                                <p>Follow on-screen instructions to input the app password where needed. Then, click "Done" to finish.</p>
                            </section>
                            <!-- <section>
                                <h2>Step 7: Additional Considerations</h2>
                                <ul>
                                    <li>If you can't find the option to add an app password, it may be because your Google Account has 2-Step
                                        Verification set up only for security keys, or you're logged into a work, school, or another
                                        organization account, or your Google Account has Advanced Protection enabled.</li>
                                    <li>Remember that you'll typically need to enter an app password only once per app or device.</li>
                                </ul>
                            </section> -->
                        </article>
                    </div>
                </div>
            </div>

        </main>
        <!-- MAIN -->
    </section>



    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Your JavaScript code here
            document.getElementById('saveMailBodyChanges').addEventListener('click', function() {

                event.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to save these changes for mail body?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, save it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('mailBodyForm').submit();
                    }
                });
            });

            document.getElementById('saveMailCredentials').addEventListener('click', function() {

                event.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You want to save these changes for mail credentials?",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, save it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('mailCredentialsForm').submit();
                    }
                });
            });
        });

        var paragraphContainer = document.getElementById('paragraphContainer');
        let paragraphCount = paragraphContainer.children.length;

        console.log(paragraphCount);


        function addParagraphInput() {
            if (paragraphCount < 3) {
                const paragraphContainer = document.getElementById('paragraphContainer');
                const newParagraphInput = document.createElement('textarea');
                newParagraphInput.name = `paragraph${paragraphCount + 1}`;
                newParagraphInput.placeholder = `Enter Paragraph ${paragraphCount + 1}`;
                paragraphContainer.appendChild(newParagraphInput);
                paragraphCount++;

            } else {
                alert('You can only add up to 3 paragraphs.');
            }
        }

        function removeParagraphInput() {
            if (paragraphCount > 0) {
                const paragraphContainer = document.getElementById('paragraphContainer');
                var textareaToRemove = paragraphContainer.querySelector(`textarea[name="paragraph${paragraphCount}"]`);
                if (textareaToRemove) {
                    textareaToRemove.remove();
                    paragraphCount--;
                }


            }
        }
    </script>



    <script src="../../../js/toggleSideBar.js"></script>
    <script src="../../../js/script.js"></script>
    <script src="../../../js/hideSideBar.js"></script>



    <?php
    if ($successCredentials) {
        $sweetAlertScript = "<script>
            Swal.fire({
                title: 'Success!',
                text: 'Changes saved successfully!',
                icon: 'success',
                showConfirmButton: false,
                timer: 1500
            });
            setTimeout(function() {
                window.location.href = './';
            }, 1000);
            </script>";

        echo $sweetAlertScript;
    }
    ?>

</body>

</html>