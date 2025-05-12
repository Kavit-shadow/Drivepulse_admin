<?php

include('../includes/authenticationAdminOrStaff.php');
authenticationAdminOrStaff();


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

    <link rel="stylesheet" href="../css/adminDashboard.css">
    <style>
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #F9F9F9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }


        .repo-info-container {
            max-width: 800px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 6px;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .repo-header {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }

        .owner-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .repo-header h1 {
            font-size: 24px;
            color: #0366d6;
            margin: 0;
        }

        .description {
            color: #586069;
            margin-top: 10px;
        }

        .url {
            margin-top: 10px;
            color: #586069;
        }

        .url a {
            color: #0366d6;
            text-decoration: none;
        }

        .stats {
            display: flex;
            margin-top: 15px;
        }

        .stat {
            display: flex;
            align-items: center;
            margin-right: 20px;
            color: #586069;
        }

        .stat img {
            width: 20px;
            height: 20px;
            margin-right: 5px;
        }

        .stats p {
            margin: 0;
        }

        .git-repo-icons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .git-repo-screenshots {
            margin-top: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            grid-gap: 10px;
        }

        .git-repo-screenshots img {
            max-width: 100%;
            border-radius: 6px;
        }

        .contributors {
            margin-top: 20px;
            list-style: none;
            padding-left: 0;
        }

        .contributors li {
            color: #586069;
            margin-bottom: 5px;
        }

        .contributors li:first-child {
            font-weight: bold;
        }
    </style>

    <!-- Add SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="shortcut icon" type="image/png" href="../assets/logo.png" />
    <title>Credits</title>
</head>

<body>


    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-car'></i>

            <span class="text">
                <h6><span><?php echo ucfirst((isset($_GET['who'])) ? $_GET['who'] : ""); ?></span></h6>
                <h4>Welcome <span>
                        <?php echo (string)($_GET['who'] == "admin") ? $_SESSION['admin_name'] : (($_GET['who'] == "staff") ? $_SESSION['staff_name'] : "error"); ?>
                    </span></h4>
            </span>
        </a>
        <ul class="side-menu top">
            <li class="active">
                <a href="../<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/">
                    <i class='bx bxs-dashboard'></i>
                    <span class="text">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="../<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/admissionForm.php">
                    <i class='bx bxs-book-bookmark'></i>
                    <span class="text">Book Admission</span>
                </a>
            </li>
            <li style="<?php echo (isset($_GET['who'])) ? (($_GET['who'] == "staff") ? "display:none;" : "") : (($_GET['who'] == "staff") ? "" : ""); ?>">
                <a href="../<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/analytics">
                    <i class='bx bxs-bar-chart-alt-2'></i>
                    <span class="text">Analytics</span>
                </a>
            </li>
            <li class="search">
                <a href="../<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/search.php">
                    <i class='bx bx-search-alt-2'></i>
                    <span class="text">Search</span>
                </a>
            </li>
            <li>
                <a href="../<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/sortData/">
                    <!-- <i class='bx bxs-bar-chart-alt-2'></i> -->
                    <i class='bx bxs-data'></i>
                    <span class="text">Customers Data</span>
                </a>
            </li>
            <li class="time-table">
                <a href="../<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/timetable/">
                    <i class='bx bx-table'></i>
                    <span class="text">Time Table</span>
                </a>
            </li>
            <li>
                <a href="../<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/dueCustomers/">
                    <i class='bx bxs-user'></i>
                    <span class="text">Pending Payment</span>
                </a>
            </li>
            <li style="<?php echo (isset($_GET['who'])) ? (($_GET['who'] == "staff") ? "display:none;" : "") : (($_GET['who'] == "staff") ? "" : ""); ?>">
                <a href="../<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/mailSender">
                    <i class='bx bx-mail-send'></i>
                    <span class="text">Mail System</span>
                </a>
            </li>
            <li style="<?php echo (isset($_GET['who'])) ? (($_GET['who'] == "staff") ? "display:none;" : "") : (($_GET['who'] == "staff") ? "" : ""); ?>">
                <a href="../<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>/manageUsers/">
                    <i class='bx bxs-group'></i>
                    <span class="text">Manage Users</span>
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
                <a href="logout.php" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
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
            <a href="" class="profile">
                <img src="../assets/logoBlack.png">
            </a>
        </nav>
        <!-- NAVBAR -->

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Credits</h1>
                    <ul class="breadcrumb">
                        <li>
                            <a class="active" style=" color: #aaaaaa;" href="../<?php echo (isset($_GET['who'])) ? $_GET['who'] : ""; ?>">Dashboard</a>
                        </li>
                        <li><i class='bx bx-chevron-right'></i></li>

                        <li>
                            <a class="active" href="./">Credits</a>
                        </li>
                    </ul>
                </div>
                <!-- <a href="Excel/?export=true" class="btn-download">
                    <i class='bx bxs-cloud-download'></i>
                    <span class="text">Data to Excel</span>
                </a> -->
            </div>

            <div class="container">
                <h1>Credits</h1>
                <p>This website is Develop by DrivePulse Team.</p>
                <div class="iframe-container">
                  
                    <div id="repo-info"></div>


                </div>
            </div>
        </main>
        <!-- MAIN -->
    </section>
    <!-- CONTENT -->
    <script>
        const username = 'Ayushx309';
        const repoName = 'DrivePulse';


        const repoUrl = `../api_ajax/json/credits_repo.json`;
        const readmeUrl = `../api_ajax/json/credits_repo_readme.json`;
 



        fetch(repoUrl)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(repoData => {

                fetch(readmeUrl)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(readmeData => {

                        const readmeContent = atob(readmeData.content);


                        const repoSize = repoData.size;


                        const screenshots = readmeContent.match(/!\[(.*?)\]\((.*?)\)/g);


                        const repoInfoHTML = `
    <div class="repo-info-container">
        <h3><i class='bx bxl-github'></i>  GitHub Repo</h3>
        </br>
        <div class="repo-header">
            <img class="owner-avatar" src="${repoData.owner.avatar_url}" alt="Owner Avatar">
            <h1><a href='${repoData.html_url}' >${repoData.full_name}</a></h1>
        </div>
        <div class="repo-body">
            ${repoData.description ? `<p class="description">${repoData.description}</p>` : ''}
            <p class="size">Size: ${repoSize} KB</p>
            <div class="screenshots">
                <div class="git-repo-icons">
                    ${screenshots ? screenshots.slice(0, 4).map(screenshot => `<img src="${screenshot.match(/\((.*?)\)/)[1]}" alt="Screenshot">`).join('') : ''}
                </div>
                </br>
                <div class="git-repo-screenshots">
                    ${screenshots ? screenshots.slice(5, -1).map(screenshot => `<img src="${screenshot.match(/\((.*?)\)/)[1]}" alt="Screenshot">`).join('') : ''}
                </div>
                </br>
                <div class="git-repo-icons">
                    ${screenshots ? `<img src="${screenshots[screenshots.length - 1].match(/\((.*?)\)/)[1]}" alt="Screenshot">` : ''}
                </div>
            </div>
            <p class="url">URL: <a href="${repoData.html_url}" target="_blank">${repoData.html_url}</a></p>
            <div class="stats">
                <div class="stat">
                    <i class='bx bxs-star'></i>
                    <p>${repoData.stargazers_count}</p>
                </div>
                <div class="stat">
                    <i class='bx bx-git-repo-forked'></i>
                    <p>${repoData.forks_count}</p>
                </div>
            </div>
        </div>
        <h2>Contributors:</h2>
        <ul class="contributors">
            ${repoData.contributors_url ? `<li>Loading contributors...</li>` : `<li>No contributors found</li>`}
        </ul>
    </div>
`;

                        document.getElementById('repo-info').innerHTML = repoInfoHTML;

                        if (repoData.contributors_url) {
                            fetch(repoData.contributors_url)
                                .then(response => response.json())
                                .then(contributorsData => {
                                    const contributorsList = contributorsData.map(contributor => `<li>${contributor.login}</li>`).join('');
                                    document.querySelector('#repo-info .contributors').innerHTML = contributorsList;
                                })
                                .catch(error => {
                                    console.error('Error fetching contributors:', error);
                                });
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching README content:', error);
                    });
            })
            .catch(error => {
                console.error('Error fetching repository information:', error);
            });
    </script>
    <script src="../js/sweetalert.js"></script>
    <script src="../js/script.js"></script>

</body>
 <!-- "contributors_url": "https://api.github.com/repos/Ayushx309/DrivePulse/contributors", -->
</html>