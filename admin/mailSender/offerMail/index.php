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



<!DOCTYPE html>
<html lang="en">

<head>
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
        /* Style for the main container */
        .container {
            margin: 20px;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 5px 5px 0px 0px;
            max-width: 100%;
            margin: 0 auto;
            font-family: Arial, sans-serif;
        }

        /* Style for form elements */
        form {
            margin-top: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="text"],
        textarea,
        select {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.1);
            font-size: 16px;
        }

        select {
            height: 40px;
        }

        input[type="file"] {
            padding: 5px;
        }

        input[type="submit"] {
            background-color: #46abcc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all .3s;
        }

        input[type="submit"]:hover {
            background-color: #206e88;
        }

        /* Style for list items */
        #successfulEmails li {
            margin-bottom: 10px;
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            list-style: none;
        }

        /* Style for in-process status */
        #inprocessEmails li.in-process {
            background-color: #f0ad4e;

            /* Yellow background for in-process */
            color: #fff;
            /* White text color */
        }

        /* Style for success status */
        #successfulEmails li.success {
            background-color: #5bc0de;
            /* Blue background for success */
            color: #fff;
            /* White text color */
        }

        /* Style for error status */
        #successfulEmails li.error {
            background-color: #d9534f;
            /* Red background for error */
            color: #fff;
            /* White text color */
        }

        #drop-area {
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            border: 2px dashed #ccc;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            margin: 20px 0px;
        }

        #drop-area.highlight {
            background-color: #f0f0f0;
        }


        input[type="file"] {
            width: 100%;
            background-color: #46abcc;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: all .3s;
        }

        input[type="file"]:hover {
            background-color: #206e88;
        }

        #optional {
            font-size: 12px;
            color: #ccc;
            float: right;
        }


        /* Media query for responsiveness */
        @media (max-width: 768px) {
            .container {
                width: 100%;
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
    </style>
    <style>
        .container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
            max-width: 800px;
        }

        .container h2 {
            color: #333;
            margin-bottom: 25px;
            font-size: 24px;
        }

        #emailForm div {
            margin-bottom: 20px;
        }

        #emailForm label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
        }

        #emailForm input[type="text"],
        #emailForm textarea,
        #emailForm select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        #emailForm input[type="text"]:focus,
        #emailForm textarea:focus,
        #emailForm select:focus {
            outline: none;
            border-color: #3C91E6;
        }

        #emailForm textarea {
            resize: vertical;
            min-height: 100px;
        }

        #optional {
            color: #888;
            font-size: 12px;
            margin-left: 5px;
        }

        #drop-area {
            border: 2px dashed #ddd;
            border-radius: 4px;
            padding: 20px;
            text-align: center;
            margin: 10px 0;
            background: #f9f9f9;
            transition: all 0.3s ease;
            position: relative;
            min-height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        #drop-area.drag-over {
            background: #e3f2fd;
            border-color: #3C91E6;
        }

        #drop-area p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        #drop-area input[type="file"] {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0;
            cursor: pointer;
        }

        .preview-area {
            margin-top: 15px;
            max-width: 100%;
        }

        .preview-area img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #emailForm input[type="submit"] {
            background: #3C91E6;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
            width: auto;
            min-width: 150px;
        }

        #emailForm input[type="submit"]:hover {
            background: #2d7bc0;
        }

        #inprocessEmails .in-process,
        #successfulEmails li {
            background: #f8f9fa;
            padding: 12px 15px;
            border: 1px solid #e9ecef;
            border-radius: 4px;
            margin-bottom: 8px;
            font-size: 14px;
            color: #495057;
            transition: transform 0.2s ease;
        }

        #inprocessEmails .in-process:hover,
        #successfulEmails li:hover {
            transform: translateX(5px);
        }

        .msg_container {
            width: 100%;
            margin: 1rem 0;
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .msg_container .container {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            padding: 1rem;
        }

        .msg_container .tools-area {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.75rem;
            border-bottom: 1px solid #eee;
            background: #f8f9fa;
            border-radius: 8px 8px 0 0;
        }

        .msg_container .tools-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            border: none;
            border-radius: 4px;
            background: transparent;
            color: #495057;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .msg_container .tools-btn:hover {
            background: #e9ecef;
            color: #228be6;
        }

        .msg_container .tools-btn i {
            font-size: 1rem;
        }

        .msg_container .txt-area {
            position: relative;
            min-height: 200px;
        }

        .msg_container .text {
            width: 100%;
            min-height: 200px;
            padding: 1rem;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            font-family: inherit;
            font-size: 1rem;
            line-height: 1.5;
            color: #495057;
            background: #fff;
            resize: vertical;
            overflow-y: auto;
        }

        .msg_container .text:empty:before {
            content: attr(placeholder);
            color: #adb5bd;
        }

        .msg_container .text:focus {
            outline: none;
            border-color: #228be6;
            box-shadow: 0 0 0 3px rgba(34, 139, 230, 0.1);
        }

        @media (max-width: 768px) {
            .msg_container .tools-area {
                padding: 0.5rem;
            }

            .msg_container .tools-btn {
                width: 32px;
                height: 32px;
            }

            .msg_container .text {
                min-height: 150px;
                padding: 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .msg_container .tools-btn {
                width: 28px;
                height: 28px;
            }

            .msg_container .tools-btn i {
                font-size: 0.875rem;
            }
        }
    </style>

    <!-- Add SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" type="image/png" href="../../../assets/logo.png" />
    <title>Offer Email Sender</title>
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
                    <h1>Offer Email Sender</h1>
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
                            <a class="active" href="./">offer Mail</a>
                        </li>
                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>


            <div class="container">
                <h2 style="color: red;">TEST MODE ON</h2>
                <h2>Email Sender</h2>
                <form id="emailForm" enctype="multipart/form-data">
                    <div>
                        <label for="subject">Subject:</label>
                        <input type="text" id="subject" name="subject" required>
                    </div>
                    <div>
                        <label for="message">Message:</label>
                        <div class="msg_container">
                            <div class="container">
                                <div class="tools-area">
                                    <button id="bold" class="tools-btn" type="button"><i class="fa-solid fa-bold"></i></button>
                                    <button id="italic" class="tools-btn" type="button"><i class="fa-solid fa-italic"></i></button>
                                    <button id="underline" class="tools-btn" type="button"><i class="fa-solid fa-underline"></i></button>
                                    <button id="strikethrough" class="tools-btn" type="button"><i class="fa-solid fa-strikethrough"></i></button>
                                    <button id="justifyRight" class="tools-btn" type="button"><i class="fa-solid fa-align-right"></i></button>
                                    <button id="justifyLeft" class="tools-btn" type="button"><i class="fa-solid fa-align-left"></i></button>
                                    <button id="justifyFull" class="tools-btn" type="button"><i class="fa-solid fa-align-justify"></i></button>
                                    <button id="justifyCenter" class="tools-btn" type="button"><i class="fa-solid fa-align-center"></i></button>
                                    <button id="unorderedList" class="tools-btn" type="button"><i class="fa-solid fa-list-ul"></i></button>
                                    <button id="increaseFontSize" class="tools-btn" type="button"><i class="fa-solid fa-plus"></i></button>
                                    <button id="decreaseFontSize" class="tools-btn" type="button"><i class="fa-solid fa-minus"></i></button>
                                    <button id="resetBtn" class="tools-btn" type="button"><i class="fa-solid fa-rotate-right"></i></button>
                                </div>
                                <div class="txt-area">
                                    <div contentEditable class="text" id="message" name="message" placeholder="start typing your text to play with..." rows="20"></div>
                                    <div id="qr-container" style="text-align: center; margin-top: 10px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <label for="attachment">Mail Body:<span id="optional">(optional)</span></label>
                    <div id="drop-area">
                        <p>Drag and drop files here or click to select</p>
                        <input type="file" id="attachment" name="attachment" class="file-input">
                        <div id="preview" class="preview-area"></div>
                    </div>
                    <div class="form-group">
                        <label for="attachmentType" class="form-label">Mail Body Type: <span class="optional-text">(optional)</span></label>
                        <select id="attachmentType" name="attachmentType" class="form-select">
                            <option value="null" selected disabled>Select File Type</option>
                            <option value="none">None</option>
                            <option value="image">Image</option>
                            <option value="html">HTML File</option>
                        </select>
                    </div>
                    <div>
                        <input type="submit" value="Send Email">
                    </div>
                </form>
            </div>
            <div class="container">
                <h2>Successful Emails</h2>
                <ul id="inprocessEmails">
                    <li class="in-process" style="display:none"></li>
                </ul>
                <ul id="successfulEmails"></ul>
            </div>



        </main>
        <!-- MAIN -->
    </section>
    <script>
        const boldButton = document.getElementById("bold");
        const underlineButton = document.getElementById("underline");
        const italicButton = document.getElementById("italic");
        const strikethroughButton = document.getElementById("strikethrough");
        const justifyRightButton = document.getElementById("justifyRight");
        const justifyLeftButton = document.getElementById("justifyLeft");
        const justifyCenterButton = document.getElementById("justifyCenter");
        const justifyFullButton = document.getElementById("justifyFull");
        const unorderedListButton = document.getElementById("unorderedList");
        const increaseFontSizeButton = document.getElementById("increaseFontSize");
        const decreaseFontSizeButton = document.getElementById("decreaseFontSize");
        const resetButton = document.getElementById("resetBtn");
        const text = document.getElementById("message");

        // Add null checks before adding event listeners
        if (boldButton) {
            boldButton.addEventListener("click", function() {
                document.execCommand("bold", false, null);
            });
        }

        if (italicButton) {
            italicButton.addEventListener("click", function() {
                document.execCommand("italic", false, null);
            });
        }

        if (underlineButton) {
            underlineButton.addEventListener("click", function() {
                document.execCommand("underline", false, null);
            });
        }

        if (strikethroughButton) {
            strikethroughButton.addEventListener("click", function() {
                document.execCommand("strikethrough", false, null);
            });
        }

        if (justifyRightButton) {
            justifyRightButton.addEventListener("click", function() {
                document.execCommand("justifyRight", false, null);
            });
        }

        if (justifyLeftButton) {
            justifyLeftButton.addEventListener("click", function() {
                document.execCommand("justifyLeft", false, null);
            });
        }

        if (justifyCenterButton) {
            justifyCenterButton.addEventListener("click", function() {
                document.execCommand("justifyCenter", false, null);
            });
        }

        if (justifyFullButton) {
            justifyFullButton.addEventListener("click", function() {
                document.execCommand("justifyFull", false, null);
            });
        }

        if (unorderedListButton) {
            unorderedListButton.addEventListener("click", function() {
                document.execCommand("insertUnorderedList", false, null);
            });
        }

        if (increaseFontSizeButton && text) {
            increaseFontSizeButton.addEventListener("click", function() {
                const currentSize = parseInt(getComputedStyle(text).fontSize, 10);
                text.style.fontSize = (currentSize + 1) + "px";
            });
        }

        if (decreaseFontSizeButton && text) {
            decreaseFontSizeButton.addEventListener("click", function() {
                const currentSize = parseInt(getComputedStyle(text).fontSize, 10);
                text.style.fontSize = (currentSize - 1) + "px";
            });
        }

        if (resetButton && text) {
            resetButton.addEventListener("click", function() {
                text.innerHTML = "";
            });
        }
    </script>
    <script>
        const dropArea = document.getElementById('drop-area');
        const fileInput = document.getElementById('attachment');
        const preview = document.getElementById('preview');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight(e) {
            dropArea.classList.add('highlight');
        }

        function unhighlight(e) {
            dropArea.classList.remove('highlight');
        }

        dropArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;
            fileInput.files = files;
            updatePreview(files[0]);
        }

        fileInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                updatePreview(this.files[0]);
            }
        });

        function updatePreview(file) {
            preview.innerHTML = `Selected file: ${file.name}`;
        }
    </script>


    <script>
        document.getElementById('emailForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            // Get total email count first
            const countResponse = await fetch('get_email_count.php');
            const countData = await countResponse.json();
            const totalEmails = countData.count;

            // Calculate estimated time (assuming ~5 seconds per email)
            const estimatedSeconds = Math.ceil(totalEmails * 5);
            const estimatedMinutes = Math.ceil(estimatedSeconds / 60);

            // Show confirmation with estimated time
            const confirmResult = await Swal.fire({
                title: 'Confirm Email Send',
                html: `You are about to send emails to ${totalEmails} recipients.<br>
                      Estimated time: ${estimatedMinutes} minute${estimatedMinutes > 1 ? 's' : ''}<br>
                      Do you want to continue?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, send emails',
                cancelButtonText: 'Cancel'
            });

            if (!confirmResult.isConfirmed) {
                return;
            }

            const formData = new FormData();
            formData.append('subject', document.getElementById('subject').value);
            formData.append('message', document.getElementById('message').innerHTML);

            const attachmentFile = document.getElementById('attachment').files[0];
            if (attachmentFile) {
                formData.append('attachment', attachmentFile);
            }
            formData.append('attachmentType', document.getElementById('attachmentType').value);

            const successfulEmails = document.getElementById('successfulEmails');
            const inprocessEmails = document.getElementById('inprocessEmails');
            const inProcessItem = document.querySelector('.in-process');

            // Add loader element
            const loader = document.createElement('div');
            loader.className = 'loader';
            loader.innerHTML = `
                <div class="spinner"></div>
                <style>
                    .loader {
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        margin: 20px 0;
                    }
                    .spinner {
                        width: 50px;
                        height: 50px;
                        border: 5px solid #f3f3f3;
                        border-top: 5px solid #3498db;
                        border-radius: 50%;
                        animation: spin 1s linear infinite;
                    }
                    @keyframes spin {
                        0% { transform: rotate(0deg); }
                        100% { transform: rotate(360deg); }
                    }
                </style>
            `;

            let currentIndex = 0;
            const startTime = Date.now();
            let remainingTime = estimatedSeconds;

            // Create countdown timer function
            function updateCountdown() {
                const elapsedTime = Math.floor((Date.now() - startTime) / 1000);
                const remainingSeconds = Math.max(0, estimatedSeconds - elapsedTime);
                const minutes = Math.floor(remainingSeconds / 60);
                const seconds = remainingSeconds % 60;
                return `${minutes}:${seconds.toString().padStart(2, '0')}`;
            }

            // Start countdown timer
            const countdownInterval = setInterval(() => {
                if (currentIndex < totalEmails) {
                    const percentComplete = Math.round((currentIndex / totalEmails) * 100);
                    inProcessItem.textContent = `Processing emails... ${currentIndex}/${totalEmails} (${percentComplete}%) - Est. ${updateCountdown()} remaining`;
                }
            }, 1000);

            while (true) {
                try {
                    inProcessItem.style.display = 'block';
                    const percentComplete = Math.round((currentIndex / totalEmails) * 100);

                    // Show loader before fetch
                    inProcessItem.appendChild(loader);

                    formData.set('currentIndex', currentIndex);
                    console.log(formData);
                    const response = await fetch('send_email.php', {
                        method: 'POST',
                        body: formData
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();

                    // Add successful emails to the list
                    result.batchResults.forEach(email => {
                        const li = document.createElement('li');
                        li.textContent = `${email.recipient} - ${email.status === 'success' ? 'Sent successfully' : 'Failed: ' + email.message}`;
                        li.className = email.status === 'success' ? 'success' : 'error';
                        successfulEmails.appendChild(li);
                    });

                    if (result.complete) {
                        // Clear countdown interval
                        clearInterval(countdownInterval);

                        // Remove loader and hide process item
                        loader.remove();
                        inProcessItem.style.display = 'none';
                        const totalTime = Math.round((Date.now() - startTime) / 1000);
                        Swal.fire({
                            title: 'Success!',
                            html: `All emails have been sent (${result.totalRecipients} total)<br>
                                  Total time taken: ${Math.ceil(totalTime/60)} minute${Math.ceil(totalTime/60) > 1 ? 's' : ''}<br>
                                  Image URL: ${result.imageUrl}`,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        });
                        break;
                    }

                    currentIndex = result.currentIndex;

                } catch (error) {
                    // Clear countdown interval on error
                    clearInterval(countdownInterval);

                    console.error('Error:', error);
                    // Remove loader on error
                    console.log(error);
                    loader.remove();
                    inProcessItem.style.display = 'none';
                    Swal.fire({
                        title: 'Error!',
                        text: `Failed to send emails: ${error.message}`,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                    break;
                }
            }
        });





        // Handle attachment type selection
        document.getElementById('attachmentType').addEventListener('change', function() {
            const fileInput = document.getElementById('attachment');
            const dropArea = document.getElementById('drop-area');
            const preview = document.getElementById('preview');

            if (this.value === 'none') {
                dropArea.style.display = 'none';
                fileInput.value = '';
                preview.innerHTML = '';
            } else {
                dropArea.style.display = 'block';

                // Update accepted file types
                if (this.value === 'image') {
                    fileInput.accept = 'image/*';
                } else if (this.value === 'html') {
                    fileInput.accept = '.html,.htm';
                }
            }
        });
    </script>



    <script src="../../../js/sweetalert.js"></script>
    <script src="../../../js/toggleSideBar.js"></script>
    <script src="../../../js/script.js"></script>
    <script src="../../../js/hideSideBar.js"></script>



</body>

</html>