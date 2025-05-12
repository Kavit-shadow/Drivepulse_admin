<?php

include('../../includes/authentication.php');
authenticationAdmin('../../');

date_default_timezone_set('Asia/Kolkata');
// $conn = mysqli_connect("localhost", "root", "", "billing");
include('../../config.php');


?>


<?php
if (isset($_POST['submit'])) {
    $errors = array();

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $username = strtolower(mysqli_real_escape_string($conn, $_POST['username']));
    $pass = md5($_POST['password']);
    $cpass = md5($_POST['cpassword']);
    $emp_id = isset($_POST['emp_id']) ? mysqli_real_escape_string($conn, $_POST['emp_id']) : '';

    if (!isset($_POST['permissions'])) {
        $errors[] = 'Permissions field is required.';
    } else {
        $permissions = mysqli_real_escape_string($conn, $_POST['permissions']);
        
        // Check if username already exists
        $stmt = $conn->prepare("SELECT * FROM users_db WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $errors[] = 'User already exists!';
        } else if ($pass != $cpass) {
            $errors[] = 'Password mismatch!';
        } else {
            // Insert new user
            $stmt = $conn->prepare("INSERT INTO users_db (name, username, password, permissions, emp_uid, time) VALUES (?, ?, ?, ?, ?, current_timestamp())");
            $stmt->bind_param("sssss", $name, $username, $pass, $permissions, $emp_id);

            if ($stmt->execute()) {
                $errors[] = 'User Created!';
                echo '<script>
                    setTimeout(function () {
                        window.location.href = "../manageUsers/";
                    }, 2000);
                </script>';
            } else {
                $errors[] = 'Error creating user: ' . $stmt->error;
            }
            $stmt->close();
        }
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
        .error-msg {
            margin: 10px 0;
            display: block;
            background: #46abcc;
            color: #fff;
            border-radius: 5px;
            font-size: 20px;
            padding: 10px;
        }

        .createUserBox {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 90vh;
            margin: 0;
            margin-top: 30px;
        }

        .form-container {
            background-color: #f9f9f9;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
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
    </style>

    <style>
        .create-user-form {
            width: 800px;
            margin: 0;
   
            padding: 20px;
        }

        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
            text-align: left;
        }

        .form-input,
        .form-select {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 6px;
            font-size: 14px;
            transition: border-color 0.3s ease;
            text-align: left;
        }

        .form-input:focus,
        .form-select:focus {
            border-color: #3C91E6;
            outline: none;
            box-shadow: 0 0 0 2px rgba(60, 145, 230, 0.1);
        }

        .readonly-input {
            width: 100%;
            padding: 12px;
            background-color: #f5f5f5;
            border: 1px solid #ddd;
            border-radius: 6px;
            color: #666;
            cursor: not-allowed;
            margin-bottom: 20px;
            text-align: left;
        }

        .submit-btn {
            width: 100%;
            padding: 12px;
            background-color: #3C91E6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .submit-btn:hover {
            background-color: #2d7bc0;
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%23333' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            padding-right: 35px;
        }

        ::placeholder {
            color: #999;
        }

        /* Responsive styles */
        @media screen and (max-width: 900px) {
            .create-user-form {
                width: 600px;
                padding: 15px;
            }
        }

        @media screen and (max-width: 600px) {
            .create-user-form {
                width: 100%;
                padding: 10px;
            }

            .form-input,
            .form-select,
            .readonly-input {
                padding: 10px;
                font-size: 13px;
            }

            .form-group label {
                font-size: 14px;
            }

            .submit-btn {
                padding: 10px;
                font-size: 14px;
            }
        }

        @media screen and (max-width: 400px) {
            .form-group {
                margin-bottom: 15px;
            }

            .form-input,
            .form-select,
            .readonly-input {
                padding: 8px;
                font-size: 12px;
            }

            .form-group label {
                font-size: 13px;
                margin-bottom: 5px;
            }

            .submit-btn {
                padding: 8px;
                font-size: 13px;
            }
        }
   
    </style>

    <!-- Add SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" type="image/png" href="../../assets/logo.png" />
    <title>Create Users</title>
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
                    <h1>Create User</h1>
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
                            <a class="active" href="./createUser">Create Users</a>
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
                    <h2>Create User</h2>
                    <?php
                    if (isset($errors)) {
                        foreach ($errors as $errors) {
                            echo '<span class="error-msg">' . $errors . '</span>';
                        }
                    }
                    ?>

                    <span id="error-msg" class="error-msg" style='display:none'></span>

                    <form method="post" class="create-user-form">
                        <?php if (isset($_GET['emp_id'])): ?>
                            <input type="text" name="emp_id" value="<?php echo htmlspecialchars($_GET['emp_id']); ?>" readonly class="readonly-input">
                        <?php endif; ?>
                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" id="name" required placeholder="Enter your name" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" name="username" id="username" required placeholder="Enter your username" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" required placeholder="Enter your password" class="form-input">
                        </div>

                        <div class="form-group">
                            <label for="cpassword">Confirm Password</label>
                            <input type="password" name="cpassword" id="cpassword" required placeholder="Confirm your password" class="form-input">
                        </div>

                        <div class="form-group">
                            <select name='permissions' required class="form-select">
                                <option disabled selected value='false'>Select Permissions</option>
                                <option value='admin'>Admin</option>
                                <option value='staff'>Staff</option>
                                <option value='trainer'>Trainer</option>
                                <option value='user'>User</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="emp_id">Create Account for Employee</label>
                            <select name="emp_id" id="emp_id" class="form-select">
                                <option value="">None</option>
                            </select>
                        </div>


                        <button type="submit" name="submit" class="submit-btn">Create Now</button>
                    </form>

                </div>
            </div>

        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Add an event listener to the form submission
            document.querySelector(".form-btn").addEventListener("click", function (e) {
                e.preventDefault(); // Prevent the form from submitting
                const name = document.querySelector("input[name='name']").value;
                const username = document.querySelector("input[name='username']").value;
                const password = document.querySelector("input[name='password']").value;
                const cpassword = document.querySelector("input[name='cpassword']").value;
                const permissions = document.querySelector("select[name='permissions']").value;

                if (name === "" || username === "" || password === "" || cpassword === "" || permissions === "Select Permissions") {
                    Swal.fire({
                        icon: "error",
                        title: "Empty Fields",
                        text: "Please fill in all required fields.",
                    });
                } else {
                    const confirmPassword = document.querySelector("input[name='cpassword']").value;
                    if (password !== confirmPassword) {
                        Swal.fire({
                            icon: "error",
                            title: "Password Mismatch",
                            text: "Please make sure your passwords match.",
                        });
                    } else {
                        // Form submission logic here
                        Swal.fire({
                            icon: "success",
                            title: "Registration Successful",
                            text: "You are now registered!",
                        }).then(() => {
                            // If the user confirms, submit the form
                            document.getElementById("registration-form").submit();
                        });
                    }
                }
            });
        });
    </script> -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $.ajax({
                url: '../../api_ajax/get_employees.php',
                method: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.employees.length > 0) {
                        response.employees.forEach(employee => {
                            $('#emp_id').append(`
                                <option value="${employee.emp_uid}">
                                    ${employee.name} (${employee.emp_uid})
                                </option>
                            `);
                        });

                        // Store employees data for later use
                        const employees = response.employees;

                        // Add change event handler for emp_id select
                        $('#emp_id').on('change', function() {
                            const selectedEmpId = $(this).val();
                            const selectedEmployee = employees.find(emp => emp.emp_uid === selectedEmpId);
                            
                            if (selectedEmployee) {
                                // Set permissions based on employee role
                                const role = selectedEmployee.role.toLowerCase();
                                if (role === 'none') {
                                    $('select[name="permissions"]').parent().hide();
                                } else {
                                    $('select[name="permissions"]').parent().show();
                                    $('select[name="permissions"]').val(role);
                                }
                            }
                        });

                        // If emp_id is in URL, select that employee
                        const urlParams = new URLSearchParams(window.location.search);
                        const empId = urlParams.get('emp_id');
                        if (empId) {
                            $('#emp_id').val(empId).trigger('change');
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching employees:', error);
                }
            });
        });
    </script>
    <script>
        $(document).ready(function() {
            $('#username').keyup(function() {
                var username = $(this).val();
                $.ajax({
                    url: '../../api_ajax/check_username.php',
                    method: 'POST',
                    data: {
                        'username': username
                    },
                    success: function(data) {

                        if (data.exists) {
                            $('#error-msg').text(data.message);
                            $('#error-msg').css('display', 'block');
                        } else {
                            $('#error-msg').text('');
                            $('#error-msg').css('display', 'none');
                        }

                    },
                    error: function(xhr, status, error) {
                        console.error('Error:', error);
                    }
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