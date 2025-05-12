<?php

include('../../includes/authentication.php');
authenticationAdmin('../../');


date_default_timezone_set('Asia/Kolkata');


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

        select {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .dropdown {
            display: flex;
            flex-direction: row;
            align-items: center;
            float: right;
            gap: 10px;
            margin: 10px 0px;
        }

        /* #content main .table-data .order table tr td:first-child {
            display: inline-block;
        } */




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


        .btns {
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
            gap: 10px;

        }

        td .btn {
            text-align: center;
            display: inline-block;
            padding: .5rem 1rem;
            background-color: #46abcc;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            flex-grow: 3;
            border: none;
            font-size: 1rem;
        }
    </style>
    <!-- Add SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />
    <title>Pre Book Queue</title>
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
                <a href="./">
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
            <a href="index.php" class="profile">
                <img src="../../assets/logoBlack.png">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Pre Book Queue</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Home</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./">Queue</a>
                        </li>
                    </ul>
                </div>

            </div>

            <div class="dropdown">
                <label style="font-weight:700;">Filter : </label>
                <select id="Car">
                    <option value="ALL" selected>All</option>

                </select>

                <select id="TimeSlot" name="time-slot">

                    <option value="ALL" selected>All</option>

                    <option value="7:00am to 7:30am">7:00am to 7:30am</option>
                    <option value="7:30am to 8:00am">7:30am to 8:00am</option>
                    <option value="8:00am to 8:30am">8:00am to 8:30am</option>
                    <option value="8:30am to 9:00am">8:30am to 9:00am</option>
                    <option value="9:00am to 9:30am">9:00am to 9:30am</option>
                    <option value="9:30am to 10:00am">9:30am to 10:00am</option>
                    <option value="10:00am to 10:30am">10:00am to 10:30am</option>
                    <option value="10:30am to 11:00am">10:30am to 11:00am</option>
                    <option value="11:00am to 11:30am">11:00am to 11:30am</option>
                    <option value="11:30am to 12:00pm">11:30am to 12:00pm</option>
                    <option value="12:00pm to 12:30pm">12:00pm to 12:30pm</option>
                    <option value="12:30pm to 1:00pm">12:30pm to 1:00pm</option>
                    <option value="1:00pm to 1:30pm">1:00pm to 1:30pm</option>
                    <option value="1:30pm to 2:00pm">1:30pm to 2:00pm</option>
                    <option value="2:00pm to 2:30pm">2:00pm to 2:30pm</option>
                    <option value="2:30pm to 3:00pm">2:30pm to 3:00pm</option>
                    <option value="3:00pm to 3:30pm">3:00pm to 3:30pm</option>
                    <option value="3:30pm to 4:00pm">3:30pm to 4:00pm</option>
                    <option value="4:00pm to 4:30pm">4:00pm to 4:30pm</option>
                    <option value="4:30pm to 5:00pm">4:30pm to 5:00pm</option>
                    <option value="5:00pm to 5:30pm">5:00pm to 5:30pm</option>
                    <option value="5:30pm to 6:00pm">5:30pm to 6:00pm</option>
                    <option value="6:00pm to 6:30pm">6:00pm to 6:30pm</option>
                    <option value="6:30pm to 7:00pm">6:30pm to 7:00pm</option>
                    <option value="7:00pm to 7:30pm">7:00pm to 7:30pm</option>
                    <option value="7:30pm to 8:00pm">7:30pm to 8:00pm</option>
                </select>

            </div>

            <div class="table-data">
                <div class="order">
                    <table>
                        <thead>
                            <tr>
                                <th>Priority</th>
                                <th>Timeslot</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Vehicle</th>
                                <th>Trainer</th>
                                <th>Start Date</th>
                                <th>End Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>

                    <?php
                    // if (isset($msg2)) {
                    //     foreach ($msg2 as $msg2) {
                    //         echo '<span class="msg">' . $msg2 . '</span>';
                    //     };
                    // };
                    ?>

                </div>



        </main>
        <!-- MAIN -->
    </section>



    <script src="../../js/sweetalert.js"></script>
    <script src="../../js/toggleSideBar.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/hideSideBar.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function() {

            function loadData(car, timeSlot) {
                console.log(car + "  " + timeSlot);

                $.ajax({
                    url: '../../api_ajax/fetch_preBook.php',
                    type: 'GET',
                    data: {
                        'car': car,
                        'timeSlot': timeSlot
                    },
                    success: function(response) {
                        console.log(response);
                        $('.table-data tbody').html(response);
                    },
                    error: function(xhr, status, error) {
                        console.error('Request failed:', error);
                    }
                });
            }

            $('#Car, #TimeSlot').change(function() {
                var car = $('#Car').val();
                var timeSlot = $('#TimeSlot').val();
                loadData(car, timeSlot);
            });

            var defaultCar = $('#Car').val();
            var defaultTimeSlot = $('#TimeSlot').val();
            loadData(defaultCar, defaultTimeSlot);

            // Use event delegation for dynamically generated buttons
            $(document).on('click', '.moveToTT', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you really want to move this customer to time table? This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, move it!',
                    cancelButtonText: 'No, cancel'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: './move.php',
                            method: 'POST',
                            data: {
                                id: id,
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Customer Moved',
                                        text: response.message
                                    }).then((result) => {
                                        if (result.isConfirmed || result.isDismissed) {
                                            // Reload or update table as necessary
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

            $(document).on('click', '.removeFromPreBook', function() {
                var id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: 'Do you really want to Remove this customer From Pre Book? This action cannot be undone!',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, remove it!',
                    cancelButtonText: 'No, cancel'
                }).then((result) => {
                    if (result.isConfirmed) {

                        $.ajax({
                            url: './remove.php',
                            method: 'POST',
                            data: {
                                id: id,
                            },
                            success: function(response) {
                                if (response.status === 'success') {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Customer Removed',
                                        text: response.message
                                    }).then((result) => {
                                        if (result.isConfirmed || result.isDismissed) {
                                            // Reload or update table as necessary
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

</body>

</html>