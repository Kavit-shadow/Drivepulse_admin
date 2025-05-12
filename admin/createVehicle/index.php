<?php

include('../../includes/authentication.php');
authenticationAdmin('../../');

date_default_timezone_set('Asia/Kolkata');
// $conn = mysqli_connect("localhost", "root", "", "billing");
include('../../config.php');

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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">



    <!-- My CSS -->
    <link rel="stylesheet" href="../../css/adminDashboard.css">
    <link rel="stylesheet" href="../../css/sideBarFooter.css">
    <style>
        /* Style the container */
        .trash {
            font-size: 1.5rem;
            cursor: pointer;
        }

        .trash:hover {
            color: red;
        }



        .container {
            margin-top: 70px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            height: auto;
            width: 100%;
        }

        /* Style the chart container */
        .chart-container {
            width: 100%;
            max-width: auto;
            margin-bottom: 20px;
            background-color: #fff;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 5px;

        }

        .barchart {
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: start;
            align-items: start;
            gap: 30px;
            flex-wrap: wrap;
        }

        .piechart {
            padding: 20px;
            display: flex;
            flex-direction: row;
            justify-content: start;
            align-items: start;
            flex-wrap: wrap;
        }

        /* Style the button container */
        .barchart .button-container {
            display: flex;
            gap: 20px;
            justify-content: space-between;
            align-items: center;
            text-align: center;
            width: 100%;

        }

        /* Style the button */
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

        /* Style the select element */
        select[name="select-year"] {
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 200px;
            /* Adjust the width as needed */
        }

        /* Style the selected option */
        select[name="select-year"] option[selected] {
            background-color: #3498db;
            color: #fff;
        }

        /* Style the options when the select is open */
        select[name="select-year"]:focus {
            outline: none;
            /* Remove the default focus outline */
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.7);
        }

        /* Style the options in the dropdown */
        select[name="select-year"] option {
            padding: 5px;
            font-size: 14px;
        }

        /* Style the hover effect on options */
        select[name="select-year"] option:hover {
            background-color: #f2f2f2;
            cursor: pointer;
        }

        /* Style the apply button */
        input[type="submit"][name="apply-button"] {
            padding: 10px 20px;
            /* Adjust padding as needed */
            background-color: #46abcc;
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }

        /* Style the button on hover */
        input[type="submit"][name="apply-button"]:hover {
            background-color: #1397c2;
        }

        #CU {
            display: inline-block;
            padding: 10px 20px;
            background-color: #46abcc;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s;
        }


        #RU {
            display: inline-block;
            padding: 10px 20px;
            background-color: #5f5f5f;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s;
        }


        #EU {
            display: inline-block;
            padding: 10px 20px;
            background-color: #00d813;
            color: #fff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-weight: bold;
            transition: background-color 0.3s;
        }

        #CU:hover {
            background: #176781;
        }

        #RU:hover {
            background: #313131;
        }

        #EU:hover {
            background: #099a16;
        }


        .createUserBox {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 40vh;
            margin: 0;

        }

        .form-container {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            width: 100%;
        }

        .form-container form {
            margin-top: .5rem;
        }

        .form-container input,
        select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 3px;
            font-size: 16px;
        }

        .form-btn {
            background-color: #46abcc;
            color: #fff;
            border: none;
            border-radius: 3px;
            padding: 12px 20px;
            font-size: 18px;
            cursor: pointer;
            transition: all .3s;

        }

        .form-btn:hover {
            background-color: #007da5;
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

            #content main .head-title .left .breadcrumb {
                font-size: 13px;
            }
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

    <!-- Add SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />
    <title>Add New Vehicle</title>
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
            <li class="active">
                <a href="./">
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
                    <h1>Add New Vehicle</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./">Add New Vehicle</a>
                        </li>
                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>



            <div class="createUserBox">
                <div class="form-container" id="create-form">
                    <h2>Add New Vehicle</h2>

                    <span id="error-msg" class="error-msg" style='display:none'></span>

                    <form method="post" id="add-vehicle-form">
                        <label for="name" style="float: left;">Vehicle Name</label>
                        <input type="text" name="vehicle-name" required placeholder="Enter your vehicle name">

                        <label for="category" style="float: left;">Vehicle Category</label>
                        <select name="category" required>
                            <option value="" selected disabled>Select Category</option>
                            <option value="4-wheel">4-Wheel</option>
                            <option value="2-wheel">2-Wheel</option>
                        </select>

                  

                        <input type="submit" name="submit" value="Add Now" class="form-btn">
                    </form>
                </div>
            </div>


            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Vehicles</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>Sr No</th>
                                <th>Vehicle Name</th>
                                <th>Created At</th>
                                <th>Category</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="vehicle-table-body">
                            <!-- Rows will be populated here by JavaScript -->
                        </tbody>
                    </table>
                    <span id="msg" class="msg" style="display:none;"></span>
                </div>
            </div>




        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


    <script>
        $(document).ready(function() {
            fetchVehicles();

            $('#add-vehicle-form').submit(function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You are about to add a new vehicle.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, add it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        addVehicle();
                    }
                });
            });
        });

        function fetchVehicles() {
            $.ajax({
                url: '../../api_ajax/get_vehicles.php',
                method: 'GET',
                dataType: 'json',
                success: function(vehicles) {
                    const tableBody = $('#vehicle-table-body');
                    tableBody.empty();

                    if (vehicles.length > 0) {
                        vehicles.forEach((vehicle, index) => {
                            const row = $(`
                                <tr>
                                    <td data-cell='Sr No'>${index + 1}</td>
                                    <td data-cell='Vehicle Name'>${vehicle.vehicle_name}</td>
                                    <td data-cell='Created At'>${vehicle.created_at}</td>
                                    <td data-cell='Category'>${vehicle.category}</td>
                                    <td data-cell='Action'><i class='bx bxs-trash trash' data-id='${vehicle.id}' ></i></td>
                                </tr>
                            `);
                            tableBody.append(row);
                        });

                        addDeleteEventListeners();
                    } else {
                        $('#msg').text('No Cars Added');
                    }
                },
                error: function() {
                    $('#msg').text('Error fetching data');
                }
            });
        }

        function addVehicle() {
            const formData = new FormData($('#add-vehicle-form')[0]);

            $.ajax({
                url: '../../api_ajax/add_vehicle.php',
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#vehicle-table-body').empty();
                            fetchVehicles();
                            $('#add-vehicle-form')[0].reset();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Request error',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }

        function addDeleteEventListeners() {
            $('.trash').click(function() {
                const id = $(this).data('id');

                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteVehicle(id);
                    }
                });
            });
        }


        function deleteVehicle(id) {
            $.ajax({
                url: '../../api_ajax/delete_vehicle.php',
                method: 'POST',
                data: {
                    id: id
                },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            title: 'Success',
                            text: response.message,
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            $('#vehicle-table-body').empty();
                            fetchVehicles();
                        });
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: response.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error',
                        text: 'Request error',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    </script>

    <script src="../../js/sweetalert.js"></script>
    <script src="../../js/hideSideBar.js"></script>
    <script src="../../js/toggleSideBar.js"></script>
    <script src="../../js/script.js"></script>

</body>

</html>