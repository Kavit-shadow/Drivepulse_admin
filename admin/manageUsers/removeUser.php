<?php

include('../../includes/authentication.php');
authenticationAdmin('../../');

date_default_timezone_set('Asia/Kolkata');
// $conn = mysqli_connect("localhost", "root", "", "billing");
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
            background-color: #ff2626;
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
            background: #c60f0f;
        }

        #EU:hover {
            background: #099a16;
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


        /* ---------------------- smaller table css --------------------------- */

        @media (max-width: 650px) {
            #content main .table-data {
                height: 70dvh;
                height: 70vh;
            }

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

            .delete {
                width: 90px;
            }
        }

        @media (max-width: 430px) {

            #content main .table-data {
                height: 65dvh;
                height: 65vh;
            }



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
    <title>Remove Users</title>
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
                <a href="../mailSender/">
                    <i class='bx bx-mail-send'></i>
                    <span class="text">Mail System</span>
                </a>
            </li>
            <li class="active">
                <a href="./">
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
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Remove User</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="./">Manage Users</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>
                        <li>
                            <a class="active" href="./removeUser">Remove Users</a>
                        </li>
                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>


            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Users</h3>
                        <!-- <i class='bx bx-plus'></i>
                        <i class='bx bx-filter'></i> -->
                    </div>
                    <!-- <div class="display-table"> -->
                    <table>

                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Permissions</th>
                                <th>Created at</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT id, name, username, password, permissions, time FROM users_db";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0) {
                                // output data of each row
                                while ($row = $result->fetch_assoc()) {
                                    echo "<tr>
                                            <td data-cell='Name' >" . $row["name"] . "</td>
                                            <td data-cell='Username' >" . $row["username"] . "</td>
                                            <td data-cell='Permissions' >" . $row["permissions"] . "</td>
                                            <td data-cell='Created at' >" . $row["time"] . "</td>
                                            <td data-cell='Action' >
                                                <a class='delete' data-id='" . $row["id"] . "' data-name='" . $row["name"] . "'>Remove</a>
                                            </td>
                                        </tr>";
                                }
                                echo "</tbody>";
                                echo "</table>";
                            } else {
                                echo "0 results";
                            }
                            $conn->close();
                            ?>
                </div>
            </div>



        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Add an event listener to the "Remove" links
            const deleteLinks = document.querySelectorAll(".delete");
            deleteLinks.forEach((link) => {
                link.addEventListener("click", function() {
                    const userId = this.getAttribute("data-id");
                    const userName = this.getAttribute("data-name");

                    Swal.fire({
                        title: "Confirm Removal",
                        text: `Are you sure you want to remove user "${userName}"?`,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonText: "Yes, remove it",
                        cancelButtonText: "Cancel",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Perform user removal via AJAX
                            $.ajax({
                                type: "POST",
                                url: "./remove_user.php", // Replace with the URL to your PHP script for user removal
                                data: {
                                    id: userId
                                },
                                success: function(response) {
                                    if (response.success) {
                                        Swal.fire("Removed!", `User "${userName}" has been removed.`, "success");
                                        // Update the table or reload the page as needed
                                        // For example, remove the row from the table:
                                        link.closest("tr").remove();
                                    } else {
                                        Swal.fire("Error", "Failed to remove user.", "error");
                                    }
                                },
                                error: function() {
                                    Swal.fire("Error", "An error occurred while removing the user.", "error");
                                },
                            });
                        }
                    });
                });
            });
        });
    </script>
    <script src="../../js/sweetalert.js"></script>
    <script src="../../js/toggleSideBar.js"></script>
    <script src="../../js/script.js"></script>
    <script src="../../js/hideSideBar.js"></script>

</body>

</html>