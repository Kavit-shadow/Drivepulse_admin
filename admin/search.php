<?php

include('../includes/authentication.php');
authenticationAdmin('../');
date_default_timezone_set('Asia/Kolkata');


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
            min-height: 25vh;
            display: flex;
            flex-direction: row;
            align-items: center;
            justify-content: center;
            padding: 20px;
            padding-bottom: 60px;
            gap: 30px;
        }

        .container .form-container {
            max-width: 800px;
            width: 100%;
            padding: 0 15px;
        }

        .container .form-container input {
            padding: 15px;
            width: 100%;
            height: 50px;
            border-radius: 5px;
            border: 3px solid #525151;
            font-size: 16px;
        }

        #search_result {
            overflow-x: auto;
            color: black;
            width: 100%;
            height: 50vh;
            text-align: center;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0 15px;
        }

        #search_result h3 {
            color: red;
        }

        .search-table {
            width: 100%;
            overflow-x: auto;
        }

        .search-table table {
            min-width: 600px;
            width: 100%;
        }

        .search-table table thead th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 12px;
            white-space: nowrap;
        }

        .search-table table tbody td {
            padding: 12px;
            white-space: nowrap;
        }

        .search-table table tbody td a.view,
        .full-data a {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            padding: 8px 16px;
            color: #fff;
            border-radius: 8px;
            background-color: #46abcc;
            text-decoration: none;
            margin: 2px;
            transition: background-color 0.3s ease;
        }

        .full-data a:hover,
        .search-table table tbody td a.view:hover {
            background-color: #206e88;
        }

        @media only screen and (max-width: 992px) {
            .container {
                flex-direction: column;
                gap: 20px;
            }

            #sidebar {
                width: 200px;
            }

            #content {
                width: calc(100% - 60px);
                left: 200px;
            }

            .side-menu .text {
                font-size: 14px;
            }
        }

        @media only screen and (max-width: 768px) {
            .container .form-container input {
                height: 45px;
                padding: 12px;
                font-size: 14px;
            }

            .search-table {
                font-size: 13px;
            }

            .search-table table tbody td a.view,
            .full-data a {
                font-size: 12px;
                padding: 6px 12px;
            }

            #sidebar .brand .text h4 {
                font-size: 16px;
            }

            #sidebar .brand .text h6 {
                font-size: 14px;
            }
        }

        @media only screen and (max-width: 480px) {
            .container .form-container input {
                height: 40px;
                padding: 10px;
                font-size: 13px;
            }

            .search-table {
                font-size: 12px;
            }

            .search-table table th,
            .search-table table td {
                padding: 8px;
            }

            .search-table table tbody td a.view,
            .full-data a {
                font-size: 11px;
                padding: 5px 10px;
            }
        }
    </style>
    <link rel="stylesheet" href="../css/adminDashboard.css">
    <title>Search</title>
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
           
            <li class="active search">
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
                <a href="dueCustomers/">
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
                    <h1>Search</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="./">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./search.php">Search</a>
                        </li>
                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>

            <?php
            if (isset($_GET['q'])) {
                $Squery = $_GET['q'];
                echo "  <div class='container' >
        <div class='form-container'>
            <input type='text' id='live_search' autocomplete='off' placeholder='Search by name, phone, date, or customer UID...' value='$Squery' style='
            width: 50vw;'>
        </div>
     ";
            } else {
                echo "  <div class='container' >
        <div class='form-container'>
            <input type='text' id='live_search' autocomplete='off' placeholder='Search by name, phone, date, or customer UID...' style='
            width: 50vw;'>
        </div>
        
     ";
            }
            ?>
            <div class="full-data">
                <a href="sortData/">View DataBase</a>
            </div>
            </div>
            <!-- <div class="container">
        <div class="form-container">
            <input type="text" id="live_search" autocomplete="off" placeholder="Search">
        </div>
    </div> -->
            <div id="search_result"></div>


        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script>
     $(document).ready(function() {
            var timeout = null;

            // Function to check if the URL has the 'q' parameter
            function getUrlParameter(name) {
                var urlParams = new URLSearchParams(window.location.search);
                return urlParams.has(name) ? urlParams.get(name) : null;
            }

            // Get the value of 'q' parameter if it exists in the URL
            var queryParam = getUrlParameter('q');

            // If 'q' is found in the URL, perform the AJAX request
            if (queryParam) {
                $("#live_search").val(queryParam); // Set the input field with the 'q' parameter value

                $.ajax({
                    url: "live_search.php", // URL for live search
                    method: "POST",
                    data: {
                        input: queryParam
                    }, // Send the 'q' parameter as input
                    success: function(data) {
                        $("#search_result").html(data); // Display search result
                    }
                });
            }

            // Trigger the search only if the input has value
            $("#live_search").on("input", function() {
                var input = $(this).val(); // Get input value

                // Proceed only if input has a value
                if (input.trim() !== "") {
                    clearTimeout(timeout);

                    timeout = setTimeout(function() {
                        $.ajax({
                            url: "live_search.php", // URL for live search
                            method: "POST",
                            data: {
                                input: input
                            }, // Send the input data
                            success: function(data) {
                                $("#search_result").html(data); // Display search result
                            }
                        });
                    }, 500); // Delay to prevent too many requests (500 ms)
                } else {
                    $("#search_result").html(""); // Clear search results if input is empty
                }
            });
        });



        // Get the pagination links
        const paginationLinks = document.querySelectorAll(".pagination-link");

        // Add click event listeners to the pagination links
        paginationLinks.forEach(link => link.addEventListener("click", handlePageNavigation));

        // Event handler function
        function handlePageNavigation(e) {
            e.preventDefault();

            // Remove the "active" class from all pagination links
            paginationLinks.forEach(link => link.classList.remove("active"));

            // Add the "active" class to the clicked link
            this.classList.add("active");

            // Add logic to navigate to the corresponding page
        }
    </script>
    <script src="../js/toggleSideBar.js"></script>
    <script src="../js/script.js"></script>
    <script src="../js/hideSideBar.js"></script>

</body>

</html>


