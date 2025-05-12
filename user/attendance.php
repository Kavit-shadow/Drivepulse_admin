<?php
include "../configWeb.php";
include "includes/functions.php";

// Set cache control headers to prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");

// Add additional security headers to prevent caching
header("X-Frame-Options: DENY");
header("X-Content-Type-Options: nosniff");
header("Referrer-Policy: no-referrer");

// Handle logout
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // Start session if not already started
    if (session_status() == PHP_SESSION_NONE) {
       session_start();
    }
    
    // Clear session
    $_SESSION = array();
    session_destroy();
    
    // Complete cookie deletion - ensure all possible cookie variations are removed
    setcookie('pmds_user_token', '', time() - 3600, '/');
    setcookie('pmds_user_token', '', time() - 3600, '/', '', true, true);
    setcookie('pmds_user_token', '', time() - 3600, '/', $_SERVER['HTTP_HOST']);
    setcookie('pmds_user_token', '', time() - 3600, '/', $_SERVER['HTTP_HOST'], true, true);
    unset($_COOKIE['pmds_user_token']);
    
    // Set cache control headers to prevent caching
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header("Expires: 0");
    
    // Redirect to home page with cache-busting parameter
    header('Location: ./?logout_success=' . time());
    exit();
 }

// Check for authentication cookie
if (!isset($_COOKIE['pmds_user_token']) || isCookieExpired($_COOKIE['pmds_user_token'])) {
    // If cookie is expired, remove it completely
    if (isset($_COOKIE['pmds_user_token'])) {
        setcookie('pmds_user_token', '', time() - 3600, '/');
        setcookie('pmds_user_token', '', time() - 3600, '/', '', true, true);
        unset($_COOKIE['pmds_user_token']);
    }
    header('Location: ./?nocache=' . time());
    exit();
}

// If we get here, the cookie is valid and not expired
session_start();
$userData = json_decode(base64_decode($_COOKIE['pmds_user_token']), true);

// Additional security check - verify session data matches cookie data
if (!isset($_SESSION['pmds_user']) || !isset($userData['cust_uid']) || 
    !isset($_SESSION['pmds_user']['cust_uid']) || 
    $_SESSION['pmds_user']['cust_uid'] !== $userData['cust_uid']) {
    // Session/cookie mismatch - force logout
    $_SESSION = array();
    session_destroy();
    setcookie('pmds_user_token', '', time() - 3600, '/');
    setcookie('pmds_user_token', '', time() - 3600, '/', '', true, true);
    header('Location: ./?auth_error=' . time());
    exit();
}

if (!isset($_SESSION['pmds_user'])) {

    // Redirect to home page
    header('Location: ./');
    exit();
}

?>

<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attendance - DrivePulse</title>
    <!-- Add cache control meta tags -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="icon" type="image/png" href="https://patelmotordrivingschool.com/storage/images/icons/icon-logo-512x512.png">
    <script>
        // Add page reload prevention for back button navigation
        window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
                // Page is loaded from cache (back/forward button)
                window.location.reload(true);
            }
        });
        
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
    <style>
        .toggle-btn-active {
            transform: translateX(0);
            background-color: #f3f4f6;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .menu-overlay {
            animation: fadeIn 0.3s ease-in-out;
        }

        /* Add smooth drag transition */
        .sidebar-drag {
            transition: transform 0.05s ease-out !important;
        }

        /* Dark mode switch styles */
        .theme-switch {
            width: 48px;
            height: 24px;
            position: relative;
        }

        .theme-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #e5e7eb;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: #3b82f6;
        }

        input:checked+.slider:before {
            transform: translateX(24px);
        }

        .dark .slider {
            background-color: #4b5563;
        }

        .dark input:checked+.slider {
            background-color: #60a5fa;
        }

        .attendance-table th {
            position: sticky;
            top: 0;
            background-color: #f8fafc;
            z-index: 10;
        }

        .dark .attendance-table th {
            background-color: #1e293b;
        }

        .table-container {
            position: relative;
            overflow-x: auto;
            overflow-y: auto;
            max-height: calc(100vh - 400px);
            -webkit-overflow-scrolling: touch;
            scroll-behavior: smooth;
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }

        .table-container::-webkit-scrollbar {
            width: 6px;
            height: 6px;
        }

        .table-container::-webkit-scrollbar-track {
            background: transparent;
        }

        .table-container::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }

        .dark .table-container::-webkit-scrollbar-thumb {
            background-color: rgba(75, 85, 99, 0.5);
        }

        .attendance-table {
            border-collapse: separate;
            border-spacing: 0;
        }

        .attendance-table th {
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .attendance-table td {
            white-space: nowrap;
        }

        @media (max-width: 640px) {
            .table-container {
                max-height: calc(100vh - 450px);
            }
        }
    </style>

    <?php include './includes/modal.php'; ?>
</head>

<body class="bg-gray-50 dark:bg-gray-900 min-h-screen">
    <!-- Overlay for mobile menu -->
    <div id="menuOverlay" class="lg:hidden fixed inset-0 bg-gray-900/50 dark:bg-black/50 z-30 hidden menu-overlay"></div>

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 transform -translate-x-full lg:translate-x-0 z-40 w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 transition-transform duration-300 ease-in-out">
        <div class="flex flex-col h-full">
            <!-- Logo -->
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <!-- Logo Image -->
                        <div class="flex-shrink-0">
                            <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-logo-512x512.png" alt="PMDS Logo" class="h-10 w-auto rounded-full" onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\' class=\'text-gray-900\'%3E%3Ccircle cx=\'12\' cy=\'12\' r=\'10\'%3E%3C/circle%3E%3Cpath d=\'M8 14s1.5 2 4 2 4-2 4-2\'%3E%3C/path%3E%3Cline x1=\'9\' y1=\'9\' x2=\'9.01\' y2=\'9\'%3E%3C/line%3E%3Cline x1=\'15\' y1=\'9\' x2=\'15.01\' y2=\'9\'%3E%3C/line%3E%3C/svg%3E'; this.classList.add('dark:invert');">
                        </div>
                        <!-- Company Name -->
                        <div class="flex flex-col">
                            <?php
                            $parts = explode(" ", $WebAppTitle, 2);
                            $part1 = $parts[0] . " " . explode(" ", $parts[1])[0]; // "Patel Motor"
                            $part2 = explode(" ", $parts[1])[1] . " " . explode(" ", $parts[1])[2]; // "Driving School"
                            ?>
                            <h1 class="text-base font-bold text-gray-800 dark:text-white leading-tight">
                                <?php echo $part1; ?>
                            </h1>
                            <span class="text-sm font-medium text-gray-600 dark:text-gray-300">
                                <?php echo $part2; ?>
                            </span>
                        </div>
                    </div>
                    <!-- Close button for mobile -->
                    <button id="sidebarClose" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 p-4 space-y-2">
                <a href="./" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span>Dashboard</span>
                </a>
                <a href="./profile" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                    <span>Profile</span>
                </a>
                <a href="./scanner" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <span>Scan For Attendance</span>
                </a>
                <a href="./attendance" class="flex items-center space-x-3 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>View Attendance</span>
                </a>
                <a href="./receipt?id=<?php echo $userData['phone']; ?>&email=<?php echo $userData['email']; ?>&name=<?php echo $userData['name']; ?>&cust_uid=<?php echo $userData['cust_uid']; ?>" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
               <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
               </svg>
               <span>View Receipt</span>
            </a>

                <!-- Divider -->
                <div class="border-t border-gray-200 dark:border-gray-700 my-2"></div>

                <!-- Theme Toggle -->
                <div class="px-3 py-2">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600 dark:text-gray-400">Dark Mode</span>
                        <label class="theme-switch">
                            <input type="checkbox" id="themeToggle">
                            <span class="slider"></span>
                        </label>
                    </div>
                </div>

                <!-- Logout -->
                <a href="javascript:void(0)" onclick="confirmLogout()" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                    </svg>
                    <span>Logout</span>
                </a>
            </nav>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-64">
        <!-- Header -->
        <header class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 fixed top-0 right-0 left-0 lg:left-64 z-30">
            <div class="px-4 py-3">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <!-- Sidebar Toggle Button (Mobile) -->
                        <button id="sidebarToggle" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:focus:ring-gray-700">
                            <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Attendance Records</h2>
                    </div>
                    <div class="flex items-center space-x-4">
                       <div class="relative">
                     <button id="notificationButton" class="relative p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <svg class="w-6 h-6 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                           <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                        </svg>
                        <!-- <span class="absolute top-0 right-0 h-2 w-2 rounded-full bg-red-500"></span> -->
                     </button>
                     <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-[280px] sm:w-[320px] md:w-[380px] lg:w-[420px] max-w-[95vw] bg-white dark:bg-gray-800 rounded-lg shadow-lg border border-gray-200 dark:border-gray-700 transform origin-top-right transition-all duration-200 ease-out">
                        <div class="p-3 sm:p-4">
                           <div class="flex items-center justify-between mb-3 sm:mb-4">
                              <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Notifications</h3>
                              <button class="text-xs text-blue-600 dark:text-blue-400 hover:underline">Mark all as read</button>
                           </div>
                           <div class="text-center text-gray-600 dark:text-gray-400 py-6 sm:py-8">
                              <svg class="w-12 h-12 sm:w-16 sm:h-16 mx-auto text-gray-400 dark:text-gray-600 mb-3 sm:mb-4 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                 <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                              </svg>
                              <p class="text-sm sm:text-base font-medium">Coming Soon!</p>
                              <p class="text-xs sm:text-sm mt-2">We're working hard to bring you real-time notifications.</p>
                              <button class="mt-4 px-4 py-2 bg-blue-600 text-white rounded-lg text-sm hover:bg-blue-700 transition-colors">Enable Notifications</button>
                           </div>
                        </div>
                     </div>
                  </div>
                  <script>
                     const notificationButton = document.getElementById('notificationButton');
                     const notificationDropdown = document.getElementById('notificationDropdown');
                     
                     // Toggle dropdown with animation
                     notificationButton.addEventListener('click', (e) => {
                        e.stopPropagation();
                        if(notificationDropdown.classList.contains('hidden')) {
                           notificationDropdown.classList.remove('hidden');
                           requestAnimationFrame(() => {
                              notificationDropdown.classList.add('scale-100', 'opacity-100');
                              notificationDropdown.classList.remove('scale-95', 'opacity-0');
                           });
                        } else {
                           closeDropdown();
                        }
                     });

                     // Close dropdown with animation
                     function closeDropdown() {
                        notificationDropdown.classList.add('scale-95', 'opacity-0');
                        notificationDropdown.classList.remove('scale-100', 'opacity-100');
                        setTimeout(() => {
                           notificationDropdown.classList.add('hidden');
                        }, 200);
                     }

                     // Close when clicking outside
                     document.addEventListener('click', (e) => {
                        if (!notificationButton.contains(e.target) && !notificationDropdown.contains(e.target)) {
                           closeDropdown();
                        }
                     });

                     // Close on escape key
                     document.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape' && !notificationDropdown.classList.contains('hidden')) {
                           closeDropdown();
                        }
                     });

                     // Responsive handling
                     const resizeObserver = new ResizeObserver(entries => {
                        const vw = entries[0].contentRect.width;
                        notificationDropdown.style.maxWidth = Math.min(vw - 20, 420) + 'px';
                     });

                     resizeObserver.observe(document.body);
                  </script>
                        <button class="flex items-center space-x-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center overflow-hidden">
                                <?php
                                if (isset($userData['cust_uid'])) {
                                    $pfp_path_header = '../storage/uploads/customer_documents/' . $userData['cust_uid'] . '/pfp.png';
                                    if (file_exists($pfp_path_header)) {
                                        echo '<img src="' . $pfp_path_header . '?v=' . time() . '" alt="Profile" class="w-full h-full object-cover">';
                                    } else {
                                        echo '<span class="text-sm font-bold text-blue-600 dark:text-blue-300">' . strtoupper(substr($userData['name'] ?? '', 0, 2)) . '</span>';
                                    }
                                } else {
                                    echo '<span class="text-sm font-bold text-blue-600 dark:text-blue-300">--</span>';
                                }
                                ?>
                            </div>
                        </button>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="p-6 mt-16 min-h-screen">
            <!-- Summary Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Present Days</p>
                            <h3 class="text-2xl font-bold text-green-600 dark:text-green-400" id="presentCount">-</h3>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900/20 rounded-full">
                            <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Absent Days</p>
                            <h3 class="text-2xl font-bold text-red-600 dark:text-red-400" id="absentCount">-</h3>
                        </div>
                        <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-full">
                            <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Attendance Rate</p>
                            <h3 class="text-2xl font-bold text-blue-600 dark:text-blue-400" id="attendanceRate">-</h3>
                        </div>
                        <div class="p-3 bg-blue-100 dark:bg-blue-900/20 rounded-full">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white">Attendance History</h3>
                </div>
                <!-- Add responsive wrapper -->
                <div class="min-w-full overflow-hidden">
                    <div class="table-container">
                        <table class="w-full attendance-table min-w-[800px]">
                            <thead>
                                <tr class="text-left bg-gray-50 dark:bg-gray-700">
                                    <th class="sticky left-0 z-10 bg-gray-50 dark:bg-gray-700 px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">#</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Customer ID</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Name</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Date</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Time In</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Time Out</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Attendance Time</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Vehicle</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Trainer</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Employee ID</th>
                                    <th class="px-4 py-3 text-sm font-medium text-gray-600 dark:text-gray-300 whitespace-nowrap">Note</th>
                                </tr>
                            </thead>
                            <tbody id="attendanceTableBody">
                                <!-- Table content will be loaded dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Add scroll indicator for mobile -->
                <div class="lg:hidden px-4 py-2 text-sm text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center justify-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        <span>Scroll horizontally to view more</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </main>

        <?php include './includes/footer.php'; ?>
    </div>

    <script>
        // Utility function to escape HTML
        function escapeHtml(unsafe) {
            return unsafe ?
                unsafe
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;") :
                '';
        }

        // Error handling function
        function showError(message) {
            const tbody = document.getElementById('attendanceTableBody');
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="12" class="px-4 py-8 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="p-3 bg-red-100 dark:bg-red-900/20 rounded-full">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <p class="text-gray-600 dark:text-gray-400">${escapeHtml(message)}</p>
                            </div>
                        </td>
                    </tr>
                `;
            }

            // Reset summary cards
            document.getElementById('presentCount').textContent = '-';
            document.getElementById('absentCount').textContent = '-';
            document.getElementById('attendanceRate').textContent = '-';
        }

        // Loading state function
        function showLoading() {
            const tbody = document.getElementById('attendanceTableBody');
            if (tbody) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="12" class="px-4 py-8 text-center">
                            <div class="flex flex-col items-center justify-center space-y-3">
                                <div class="animate-spin rounded-full h-8 w-8 border-4 border-gray-200 dark:border-gray-600 border-t-blue-600 dark:border-t-blue-400"></div>
                                <p class="text-gray-600 dark:text-gray-400">Loading attendance data...</p>
                            </div>
                        </td>
                    </tr>
                `;
            }
        }

        // Function to load attendance data
        function loadAttendanceData() {
            const userId = '<?php echo $_SESSION["pmds_user"]["user_id"]; ?>';

            if (!userId) {
                showError('No user ID found in session');
                return;
            }

            showLoading();

            $.ajax({
                url: '../api_ajax/get_attendance.php',
                method: 'POST',
                data: {
                    id: userId
                },
                success: function(response) {
                    try {
                        const data = typeof response === 'string' ? JSON.parse(response) : response;

                        if (!Array.isArray(data)) {
                            throw new Error('Invalid data format');
                        }

                        // Sort data by date in ascending order
                        data.sort((a, b) => {
                            const dateA = new Date(a.date);
                            const dateB = new Date(b.date);
                            return dateA - dateB;
                        });

                        const tbody = document.getElementById('attendanceTableBody');
                        if (!tbody) {
                            throw new Error('Table body element not found');
                        }

                        tbody.innerHTML = '';

                        // Get all dates between first and last attendance
                        const allDates = [];
                        if (data.length > 0) {
                            const startDate = new Date(data[0].date);
                            const endDate = new Date(data[data.length - 1].date);

                            for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                                allDates.push(new Date(d));
                            }
                        }

                        // Update summary cards
                        const presentCount = data.length;
                        const absentCount = allDates.length - presentCount;
                        const attendancePercentage = allDates.length > 0 ?
                            ((presentCount / allDates.length) * 100).toFixed(1) :
                            0;

                        document.getElementById('presentCount').textContent = presentCount;
                        document.getElementById('absentCount').textContent = absentCount;
                        document.getElementById('attendanceRate').textContent = `${attendancePercentage}%`;

                        let dataIndex = 0;
                        // Fill table rows
                        for (let i = 0; i < Math.min(20, allDates.length || 20); i++) {
                            const tr = document.createElement('tr');
                            tr.className = i % 2 === 0 ? 'bg-gray-50 dark:bg-gray-800' : 'bg-white dark:bg-gray-900';

                            if (allDates.length > 0) {
                                const currentDate = allDates[i].toISOString().split('T')[0];
                                const attendanceData = data[dataIndex];

                                if (attendanceData && attendanceData.date === currentDate) {
                                    // Present day
                                    tr.innerHTML = `
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${i + 1}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${escapeHtml(attendanceData.cust_uid || '')}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${escapeHtml(attendanceData.customer_name || '')}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${escapeHtml(attendanceData.date || '')} (${['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][allDates[i].getDay()]})</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${escapeHtml(attendanceData.time_in ? new Date(attendanceData.time_in).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) : '')}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${escapeHtml(attendanceData.time_out ? new Date(attendanceData.time_out).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) : '')}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${escapeHtml(attendanceData.attendance_time ? new Date(attendanceData.attendance_time).toLocaleString('en-US', { hour: 'numeric', minute: 'numeric', hour12: true }) : '')}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${escapeHtml(attendanceData.vehicle_name || '')}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${escapeHtml(attendanceData.trainer_name || '')}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${escapeHtml(attendanceData.employee_uid || '')}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${escapeHtml(attendanceData.note || '')}</td>
                                    `;
                                    dataIndex++;
                                } else {
                                    // Absent day
                                    tr.innerHTML = `
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${i + 1}</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">-</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">-</td>
                                        <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400">${currentDate} (${['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][allDates[i].getDay()]}) (Absent)</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">-</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">-</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">-</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">-</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">-</td>
                                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">-</td>
                                        <td class="px-4 py-3 text-sm text-red-600 dark:text-red-400">Absent</td>
                                    `;
                                }
                            } else {
                                // No attendance data
                                tr.innerHTML = `
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200">${i + 1}</td>
                                    <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-200" colspan="11">No attendance records found</td>
                                `;
                            }
                            tbody.appendChild(tr);
                        }
                    } catch (err) {
                        console.error('Error processing data:', err);
                        showError('Error processing attendance data');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error:', error);
                    showError('Failed to load attendance data');
                }
            });
        }

        // Load attendance data when the page loads
        document.addEventListener('DOMContentLoaded', loadAttendanceData);

        // Theme Management
        const themeToggle = document.getElementById('themeToggle');
        const html = document.documentElement;

        // Check for saved theme preference, otherwise use system preference
        const getThemePreference = () => {
            if (typeof localStorage !== 'undefined') {
                const storedTheme = localStorage.getItem('theme');
                if (storedTheme) {
                    return storedTheme;
                }
                return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            }
            return 'light';
        };

        // Apply theme
        const setTheme = (theme) => {
            if (theme === 'dark') {
                html.classList.add('dark');
                themeToggle.checked = true;
            } else {
                html.classList.remove('dark');
                themeToggle.checked = false;
            }
            localStorage.setItem('theme', theme);
        };

        // Initialize theme
        setTheme(getThemePreference());

        // Handle theme toggle
        themeToggle.addEventListener('change', (e) => {
            setTheme(e.target.checked ? 'dark' : 'light');
        });

        // Watch for system theme changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            if (!localStorage.getItem('theme')) {
                setTheme(e.matches ? 'dark' : 'light');
            }
        });

        // Improved Sidebar Toggle Functionality
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebarClose = document.getElementById('sidebarClose');
        const menuOverlay = document.getElementById('menuOverlay');
        const themeToggleContainer = document.querySelector('.theme-switch');

        // Swipe gesture variables
        let touchStartX = 0;
        let touchEndX = 0;
        let isDragging = false;
        let currentTranslateX = 0;
        let startTranslateX = 0;
        const SWIPE_THRESHOLD = 50; // Minimum distance for swipe
        const SIDEBAR_WIDTH = 256; // w-64 = 16rem = 256px

        function toggleMenu(show) {
            sidebar.classList.remove('sidebar-drag');
            if (show) {
                sidebar.classList.remove('-translate-x-full');
                menuOverlay.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            } else {
                sidebar.classList.add('-translate-x-full');
                menuOverlay.classList.add('hidden');
                document.body.style.overflow = '';
            }
        }

        // Touch event handlers
        function handleTouchStart(e) {
            if (window.innerWidth >= 1024) return; // Only handle touch on mobile

            // Don't initiate drag if touching the theme toggle or other interactive elements
            if (e.target.closest('.theme-switch') ||
                e.target.closest('a') ||
                e.target.closest('button') ||
                e.target.closest('.table-container')) { // Ignore touches in table container
                return;
            }

            touchStartX = e.touches[0].clientX;
            isDragging = true;
            startTranslateX = sidebar.classList.contains('-translate-x-full') ? -SIDEBAR_WIDTH : 0;

            sidebar.classList.add('sidebar-drag');

            // Prevent default only if touch starts near the edge
            if (touchStartX < 30 || !sidebar.classList.contains('-translate-x-full')) {
                e.preventDefault();
            }
        }

        function handleTouchMove(e) {
            // Don't handle touch move if it started in the table container
            if (!isDragging ||
                window.innerWidth >= 1024 ||
                e.target.closest('.table-container')) {
                return;
            }

            touchEndX = e.touches[0].clientX;
            const diffX = touchEndX - touchStartX;
            currentTranslateX = Math.min(0, Math.max(-SIDEBAR_WIDTH, startTranslateX + diffX));

            // Only handle swipes starting from left edge or when sidebar is open
            if (touchStartX < 30 || !sidebar.classList.contains('-translate-x-full')) {
                sidebar.style.transform = `translateX(${currentTranslateX}px)`;

                // Show/hide overlay based on drag position
                if (currentTranslateX > -SIDEBAR_WIDTH) {
                    menuOverlay.classList.remove('hidden');
                } else {
                    menuOverlay.classList.add('hidden');
                }

                e.preventDefault();
            }
        }

        function handleTouchEnd() {
            if (!isDragging || window.innerWidth >= 1024) return;

            isDragging = false;
            sidebar.classList.remove('sidebar-drag');

            const diffX = touchEndX - touchStartX;

            // Determine whether to open or close based on swipe distance
            if (Math.abs(diffX) > SWIPE_THRESHOLD) {
                if (diffX > 0 && startTranslateX === -SIDEBAR_WIDTH) {
                    // Swipe right when closed
                    toggleMenu(true);
                } else if (diffX < 0 && startTranslateX === 0) {
                    // Swipe left when open
                    toggleMenu(false);
                } else {
                    // Reset to starting position
                    toggleMenu(startTranslateX === 0);
                }
            } else {
                // Reset to starting position if swipe wasn't long enough
                toggleMenu(startTranslateX === 0);
            }

            // Reset variables
            sidebar.style.transform = '';
            touchStartX = 0;
            touchEndX = 0;
            currentTranslateX = 0;
        }

        // Add touch event listeners
        document.addEventListener('touchstart', handleTouchStart, {
            passive: false
        });
        document.addEventListener('touchmove', handleTouchMove, {
            passive: false
        });
        document.addEventListener('touchend', handleTouchEnd);

        // Existing click event listeners
        sidebarToggle.addEventListener('click', (e) => {
            e.stopPropagation();
            const isHidden = sidebar.classList.contains('-translate-x-full');
            toggleMenu(isHidden);
        });

        sidebarClose.addEventListener('click', (e) => {
            e.stopPropagation();
            toggleMenu(false);
        });

        menuOverlay.addEventListener('click', () => {
            toggleMenu(false);
        });

        // Modified click handler to prevent sidebar closure when clicking interactive elements
        document.addEventListener('click', (e) => {
            if (window.innerWidth < 1024) {
                // Don't close if clicking inside sidebar on interactive elements
                if (e.target.closest('.theme-switch') || e.target.closest('a') || e.target.closest('button')) {
                    return;
                }

                // Close only if clicking outside sidebar and not on toggle button
                if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                    toggleMenu(false);
                }
            }
        });

        // Handle escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                toggleMenu(false);
            }
        });

        // Handle resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 1024) {
                menuOverlay.classList.add('hidden');
                document.body.style.overflow = '';
                sidebar.style.transform = '';
                isDragging = false;
            }
        });

        function confirmLogout() {
            const logoutModal = document.getElementById('logoutModal');
            const loadingModal = document.getElementById('loadingModal');
            const cancelBtn = document.getElementById('cancelLogout');
            const confirmBtn = document.getElementById('confirmLogout');

            // Show logout confirmation modal
            logoutModal.classList.remove('hidden');

            // Handle cancel
            cancelBtn.onclick = () => {
                logoutModal.classList.add('hidden');
            };

            // Handle confirm
            confirmBtn.onclick = () => {
                // Hide confirmation modal
                logoutModal.classList.add('hidden');
                // Show loading modal
                loadingModal.classList.remove('hidden');
                // Force browser to make a fresh request by adding random parameters
                const cacheBuster = new Date().getTime() + Math.random().toString(36).substring(2, 15);
                window.location.href = './?action=logout&nocache=' + cacheBuster;
            };

            // Close on backdrop click
            logoutModal.querySelector('.backdrop-blur-sm').onclick = () => {
                logoutModal.classList.add('hidden');
            };

            // Close on escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && !logoutModal.classList.contains('hidden')) {
                    logoutModal.classList.add('hidden');
                }
            });
        }
    </script>
</body>

</html>