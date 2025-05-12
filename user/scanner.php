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
if (
    !isset($_SESSION['pmds_user']) || !isset($userData['cust_uid']) ||
    !isset($_SESSION['pmds_user']['cust_uid']) ||
    $_SESSION['pmds_user']['cust_uid'] !== $userData['cust_uid']
) {
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
    <title>Patel Motor Driving School - Scan For Attendance</title>
    <link rel="manifest" href="./manifest.json">
    <meta name="theme-color" content="#3b82f6">
    <link rel="apple-touch-icon" href="https://patelmotordrivingschool.com/storage/images/icons/icon-logo-512x512.png">
    <!-- Camera permission meta tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <!-- Prevent caching -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <script src="https://cdn.tailwindcss.com"></script>
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

        /* Video container responsive styles */
        @media (max-width: 768px) {
            #video {
                aspect-ratio: 4/5;
                max-height: 70vh;
                width: 100%;
            }
        }

        @media (min-width: 769px) {
            #video {
                aspect-ratio: 16/9;
                max-height: 60vh;
            }
        }

        /* Custom SweetAlert2 Responsive Styles */
        .swal2-popup {
            font-size: 0.9rem !important;
            border-radius: 1rem !important;
            padding: 1.5rem !important;
            width: auto !important;
            max-width: 90% !important;
            margin: 1rem !important;
        }

        /* Styles for the wide permission instructions dialog */
        .swal-wide-popup {
            max-width: 600px !important;
            width: 90% !important;
        }

        .swal-wide-popup .swal2-html-container {
            text-align: left !important;
            overflow-y: auto !important;
            max-height: 70vh !important;
        }

        @media (max-width: 640px) {
            .swal2-popup {
                padding: 1rem !important;
                font-size: 0.85rem !important;
                margin: 0.5rem !important;
            }

            .swal2-title {
                font-size: 1.25rem !important;
                margin-bottom: 0.5rem !important;
            }

            .swal2-html-container {
                font-size: 0.9rem !important;
                margin: 0.5rem 0 !important;
            }

            .swal2-input {
                height: 2.5rem !important;
                font-size: 0.9rem !important;
            }

            .swal2-actions {
                margin-top: 1rem !important;
                gap: 0.5rem !important;
                flex-wrap: wrap !important;
            }

            .swal2-actions button {
                font-size: 0.85rem !important;
                padding: 0.5rem 1rem !important;
                margin: 0 !important;
                flex: 1 1 auto !important;
            }
        }

        /* Toast Notifications Responsive Styles */
        .swal2-toast {
            max-width: calc(100% - 2rem) !important;
            margin: 1rem !important;
        }

        @media (max-width: 640px) {
            .swal2-toast {
                margin: 0.5rem !important;
                padding: 0.5rem 1rem !important;
            }

            .swal2-toast .swal2-title {
                font-size: 0.875rem !important;
                margin: 0 !important;
            }
        }

        /* Dark mode styles */
        .dark .swal2-popup {
            background-color: #1f2937 !important;
            color: #fff !important;
        }

        .dark .swal2-title,
        .dark .swal2-html-container {
            color: #fff !important;
        }

        .dark .swal2-input {
            background-color: #374151 !important;
            color: #fff !important;
            border-color: #4b5563 !important;
        }

        .dark .swal2-input:focus {
            border-color: #60a5fa !important;
            box-shadow: 0 0 0 2px rgba(96, 165, 250, 0.25) !important;
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
                <a href="./scanner" class="flex items-center space-x-3 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
                    </svg>
                    <span>Scan For Attendance</span>
                </a>
                <a href="./attendance" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
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
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Scan For Attendance</h2>
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
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

            </div>
            <div class="flex flex-col items-center justify-center h-full p-4 md:p-8 lg:p-12">
                <h2 class="text-xl sm:text-2xl md:text-3xl lg:text-4xl font-semibold mb-4 text-gray-800 dark:text-white text-center">Welcome! Please Scan Your QR Code</h2>
                <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 text-center mb-6">Quickly record your attendance by scanning your employee QR code</p>
                <select id="cameraSelect" class="mb-4 p-2 border rounded-lg shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full max-w-xs hidden">
                    <option value="">Select Camera</option>
                </select>

                <!-- Camera access button - always visible -->
                <button id="startCameraBtn" class="mb-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-md transition-colors flex items-center justify-center w-full max-w-xs mx-auto">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Start Camera
                </button>

                <!--<video id="video" class="border rounded-lg shadow-lg mb-4 w-full max-w-lg"></video>-->
                
                
                
                  <!-- Video container with overlay -->
                <div class="relative w-full mb-4 max-w-lg">
                    <video id="video" class="rounded-lg shadow-lg  w-full max-w-lg"></video>
                    <!-- Scanning overlay -->
                    <div class="absolute inset-0 rounded-lg overflow-hidden pointer-events-none flex items-center justify-center">
                        <!-- Corner markers with animated borders -->
                        <div class="absolute top-0 left-0 w-20 h-20">
                            <div class="absolute top-0 left-0 w-full h-full border-t-4 border-l-4 border-blue-500 rounded-tl-lg animate-corner-pulse"></div>
                            <div class="absolute top-0 left-0 w-full h-full border-t-4 border-l-4 border-blue-400 rounded-tl-lg animate-corner-glow"></div>
                        </div>
                        <div class="absolute top-0 right-0 w-20 h-20">
                            <div class="absolute top-0 right-0 w-full h-full border-t-4 border-r-4 border-blue-500 rounded-tr-lg animate-corner-pulse"></div>
                            <div class="absolute top-0 right-0 w-full h-full border-t-4 border-r-4 border-blue-400 rounded-tr-lg animate-corner-glow"></div>
                        </div>
                        <div class="absolute bottom-0 left-0 w-20 h-20">
                            <div class="absolute bottom-0 left-0 w-full h-full border-b-4 border-l-4 border-blue-500 rounded-bl-lg animate-corner-pulse"></div>
                            <div class="absolute bottom-0 left-0 w-full h-full border-b-4 border-l-4 border-blue-400 rounded-bl-lg animate-corner-glow"></div>
                        </div>
                        <div class="absolute bottom-0 right-0 w-20 h-20">
                            <div class="absolute bottom-0 right-0 w-full h-full border-b-4 border-r-4 border-blue-500 rounded-br-lg animate-corner-pulse"></div>
                            <div class="absolute bottom-0 right-0 w-full h-full border-b-4 border-r-4 border-blue-400 rounded-br-lg animate-corner-glow"></div>
                        </div>
                        
                        <!-- Multi-layer scanning animation -->
                        <!--<div class="absolute inset-x-0  h-40 bg-gradient-to-b from-transparent via-blue-500/10 to-transparent animate-scan"></div>-->
                        <div class="absolute inset-x-0 h-1 bg-gradient-to-r from-transparent via-blue-500 to-transparent animate-scan opacity-80 shadow-glow blur-sm"></div>
                        <div class="absolute inset-x-0 h-[2px] bg-gradient-to-r from-transparent via-white to-transparent animate-scan opacity-50" style="filter: blur(1px);"></div>
                        
                        <!-- Scan area highlight -->
                        <div class="absolute inset-0 bg-gradient-to-b from-blue-500/5 via-transparent to-blue-500/5 animate-pulse-subtle"></div>
                        
                        <!-- Center target indicator with multiple layers -->
                        <div class="relative w-52 h-52">
                            <div class="absolute inset-0 border-2 border-dashed border-blue-400 rounded-lg opacity-60 animate-pulse-slow"></div>

                            <!-- Scanner corners with highlight effect -->
                            <div class="absolute inset-0">
                                <div class="absolute top-0 left-0 w-4 h-4 border-t-2 border-l-2 border-blue-500 animate-highlight-corners"></div>
                                <div class="absolute top-0 right-0 w-4 h-4 border-t-2 border-r-2 border-blue-500 animate-highlight-corners" style="animation-delay: 0.5s"></div>
                                <div class="absolute bottom-0 left-0 w-4 h-4 border-b-2 border-l-2 border-blue-500 animate-highlight-corners" style="animation-delay: 1s"></div>
                                <div class="absolute bottom-0 right-0 w-4 h-4 border-b-2 border-r-2 border-blue-500 animate-highlight-corners" style="animation-delay: 1.5s"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <style>
                    @keyframes scan {
                        0% {
                            top: 0%;
                            opacity: 0;
                        }
                        10% {
                            opacity: 1;
                        }
                        90% {
                            opacity: 1;
                        }
                        100% {
                            top: 100%;
                            opacity: 0;
                        }
                    }
                    @keyframes highlight-corners {
                        0%, 100% {
                            opacity: 0.5;
                            filter: brightness(1);
                        }
                        50% {
                            opacity: 1;
                            filter: brightness(1.5) drop-shadow(0 0 3px rgba(59, 130, 246, 0.5));
                        }
                    }
                    @keyframes pulse-subtle {
                        0%, 100% {
                            opacity: 0.1;
                        }
                        50% {
                            opacity: 0.2;
                        }
                    }
                    @keyframes pulse-slow {
                        0%, 100% {
                            transform: scale(1);
                            opacity: 0.4;
                        }
                        50% {
                            transform: scale(1.01);
                            opacity: 0.6;
                        }
                    }
                    .animate-scan {
                        animation: scan 3s cubic-bezier(0.4, 0, 0.2, 1) infinite;
                    }
                    .animate-highlight-corners {
                        animation: highlight-corners 2s ease-in-out infinite;
                    }
                    .animate-pulse-subtle {
                        animation: pulse-subtle 3s ease-in-out infinite;
                    }
                    .animate-pulse-slow {
                        animation: pulse-slow 3s ease-in-out infinite;
                    }
                    .animate-target-rotate {
                        animation: target-rotate 12s linear infinite;
                    }
                    .shadow-glow {
                        box-shadow: 0 0 20px 3px rgba(59, 130, 246, 0.3);
                    }
                    @keyframes corner-pulse {
                        0%, 100% {
                            opacity: 0.6;
                            transform: scale(1);
                        }
                        50% {
                            opacity: 1;
                            transform: scale(1.05);
                        }
                    }
                    @keyframes corner-glow {
                        0%, 100% {
                            opacity: 0;
                        }
                        50% {
                            opacity: 0.5;
                        }
                    }
                    @keyframes target-rotate {
                        0% {
                            transform: rotate(0deg);
                        }
                        100% {
                            transform: rotate(360deg);
                        }
                    }
                    .animate-scan {
                        animation: scan 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
                    }
                    .animate-scan-reverse {
                        animation: scan-reverse 2.5s cubic-bezier(0.4, 0, 0.2, 1) infinite;
                    }
                    .animate-corner-pulse {
                        animation: corner-pulse 2s ease-in-out infinite;
                    }
                    .animate-corner-glow {
                        animation: corner-glow 2s ease-in-out infinite;
                    }
                    .animate-target-rotate {
                        animation: target-rotate 8s linear infinite;
                    }
                    .shadow-glow {
                        box-shadow: 0 0 15px 2px rgba(59, 130, 246, 0.5);
                    }
                    @keyframes pulse {
                        0%, 100% {
                            transform: scale(1);
                            opacity: 0.5;
                        }
                        50% {
                            transform: scale(1.02);
                            opacity: 0.7;
                        }
                    }
                    .animate-pulse {
                        animation: pulse 2s ease-in-out infinite;
                    }
                </style>
                
                
                
                <div id="loading" class="mt-4 text-lg font-medium hidden">Loading camera...</div>
                <div id="result" class="mt-4 text-lg md:text-xl font-medium text-green-600 transition-opacity duration-300 w-full max-w-lg text-center break-words" style="display: none;"></div>
                <div id="error" class="mt-4 text-lg md:text-xl font-medium text-red-600 transition-opacity duration-300 w-full max-w-lg text-center break-words" style="display: none;"></div>

                <!-- Manual camera permission button -->
                <button id="requestCameraBtn" class="mt-4 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-md transition-colors hidden flex items-center">
                    <svg class="inline-block w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    Enable Camera Access
                </button>

                <!-- Camera permission help text -->
                <p id="cameraPermissionHelp" class="mt-2 text-sm text-gray-600 dark:text-gray-400 hidden">
                    Click the button above to enable camera access. If you've denied permission, you'll need to update your browser settings.
                </p>

                <!-- Manual camera permission button -->
                <button id="showManualEntryBtn" class="mt-2 text-sm text-blue-600 dark:text-blue-400 hover:underline">
                    Having camera issues? Click here
                </button>

                <p class="mt-4 text-sm text-gray-600 dark:text-gray-300 text-center">Point your camera at a Employee QR code to scan it.</p>

                <!-- Manual entry section -->
                <div id="manualEntrySection" class="mt-6 p-4 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 w-full max-w-lg hidden">
                    <h3 class="text-lg font-medium text-gray-800 dark:text-white mb-3">Having Camera Issues?</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-300 mb-4">If you're experiencing persistent camera problems, you can manually enter the employee ID:</p>

                    <div class="flex flex-col space-y-3">
                        <input type="text" id="manualEmployeeId" placeholder="Enter Employee ID" class="p-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 w-full">
                        <button id="submitManualId" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg shadow-md transition-colors">
                            Submit Attendance
                        </button>
                    </div>
                </div>

                <!-- Info Block for Clock Out Reminder (hidden by default) -->
                <div id="clockOutReminder" class="mt-6 p-4 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800 w-full max-w-lg hidden">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-blue-800 dark:text-blue-300">Important Reminder</h3>
                            <div class="mt-1 text-sm text-blue-700 dark:text-blue-200">
                                Attendance recorded successfully! Don't forget to clock out at the end of your training session to properly record your attendance.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <?php include './includes/footer.php'; ?>
    </div>

    <!-- Add jQuery library -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <script src="https://unpkg.com/jsqr/dist/jsQR.js"></script> -->
    <script src="./lib/jsQR.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Add MD5 library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/blueimp-md5/2.19.0/js/md5.min.js"></script>
    <script>
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
            if (e.target.closest('.theme-switch') || e.target.closest('a') || e.target.closest('button')) {
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
            if (!isDragging || window.innerWidth >= 1024) return;

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

        // Ensure jsQR is loaded before using it
        if (typeof jsQR === 'undefined') {
            console.error('jsQR library failed to load.');
        } else {
            const video = document.getElementById('video');
            const resultDiv = document.getElementById('result');
            const errorDiv = document.getElementById('error');
            const cameraSelect = document.getElementById('cameraSelect');
            const loadingDiv = document.getElementById('loading');
            let defaultDeviceId = '';
            let isAttendanceSent = false; // Flag to prevent multiple attendance submissions
            let scannerInitialized = false;

            // Initialize camera on page load or when user clicks a button
            document.addEventListener('DOMContentLoaded', function() {
                // Don't auto-initialize on page load - wait for user interaction

                // Add event listener for start camera button
                document.getElementById('startCameraBtn').addEventListener('click', function() {
                    if (!scannerInitialized) {
                        initializeScanner();
                    } else {
                        // If already initialized but not working, try again with different approach
                        scannerInitialized = false;
                        initializeScanner();
                    }
                });

                // Add event listener for manual camera access button
                document.getElementById('requestCameraBtn').addEventListener('click', function() {
                    this.classList.add('hidden');
                    showCameraPermissionInstructions();
                });
            });

            // Function to show camera permission instructions
            function showCameraPermissionInstructions() {
                // Detect browser and OS for specific instructions
                const isChrome = /Chrome/.test(navigator.userAgent) && !/Edge|Edg/.test(navigator.userAgent);
                const isFirefox = /Firefox/.test(navigator.userAgent);
                const isSafari = /Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent);
                const isEdge = /Edge|Edg/.test(navigator.userAgent);
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
                const isAndroid = /Android/.test(navigator.userAgent);

                // Create browser-specific instructions
                let permissionInstructions = '';

                if (isChrome) {
                    permissionInstructions = `
                      <div class="text-left mt-4">
                          <p class="font-medium">To enable camera access in Chrome:</p>
                          <ol class="list-decimal pl-5 space-y-1 mt-2">
                              <li>Click the lock/info icon in the address bar</li>
                              <li>Select "Site settings"</li>
                              <li>Change Camera permission from "Block" to "Allow"</li>
                              <li>Refresh this page</li>
                          </ol>
                      </div>
                  `;
                } else if (isFirefox) {
                    permissionInstructions = `
                      <div class="text-left mt-4">
                          <p class="font-medium">To enable camera access in Firefox:</p>
                          <ol class="list-decimal pl-5 space-y-1 mt-2">
                              <li>Click the lock/info icon in the address bar</li>
                              <li>Click "Connection secure" or "Connection not secure"</li>
                              <li>Click "More Information" and then "Permissions"</li>
                              <li>Find "Use the Camera" and remove the current setting</li>
                              <li>Refresh this page</li>
                          </ol>
                      </div>
                  `;
                } else if (isSafari) {
                    permissionInstructions = `
                      <div class="text-left mt-4">
                          <p class="font-medium">To enable camera access in Safari:</p>
                          <ol class="list-decimal pl-5 space-y-1 mt-2">
                              <li>Open Safari Preferences (click Safari in the menu bar, then Preferences)</li>
                              <li>Go to the "Websites" tab</li>
                              <li>Select "Camera" from the left sidebar</li>
                              <li>Find this website and change permission to "Allow"</li>
                              <li>Refresh this page</li>
                          </ol>
                      </div>
                  `;
                } else if (isEdge) {
                    permissionInstructions = `
                      <div class="text-left mt-4">
                          <p class="font-medium">To enable camera access in Edge:</p>
                          <ol class="list-decimal pl-5 space-y-1 mt-2">
                              <li>Click the lock/info icon in the address bar</li>
                              <li>Click "Site permissions"</li>
                              <li>Change Camera permission to "Allow"</li>
                              <li>Refresh this page</li>
                          </ol>
                      </div>
                  `;
                }

                // Add mobile-specific instructions
                if (isIOS) {
                    permissionInstructions += `
                      <div class="text-left mt-4">
                          <p class="font-medium">On iOS devices:</p>
                          <ol class="list-decimal pl-5 space-y-1 mt-2">
                              <li>Open Settings app</li>
                              <li>Scroll down and find your browser (Safari, Chrome, etc.)</li>
                              <li>Tap on the browser and find "Camera"</li>
                              <li>Make sure it's set to "Allow"</li>
                              <li>If using PWA, go to Settings > Safari > Advanced > Website Data</li>
                              <li>Find this website and tap "Remove" to reset permissions</li>
                          </ol>
                      </div>
                      
                      <div class="text-left mt-4">
                          <p class="font-medium">For PWA on iOS:</p>
                          <ol class="list-decimal pl-5 space-y-1 mt-2">
                              <li>If you've installed this as an app on your home screen:</li>
                              <li>Go to Settings > Safari > Advanced > Website Data</li>
                              <li>Find and delete data for this website</li>
                              <li>Close the app completely (swipe up from bottom)</li>
                              <li>Restart the app and grant permission when prompted</li>
                              <li>If that doesn't work, try uninstalling and reinstalling the app</li>
                          </ol>
                      </div>
                  `;
                } else if (isAndroid) {
                    permissionInstructions += `
                      <div class="text-left mt-4">
                          <p class="font-medium">On Android devices:</p>
                          <ol class="list-decimal pl-5 space-y-1 mt-2">
                              <li>Open Settings app</li>
                              <li>Tap on Apps or Application Manager</li>
                              <li>Find your browser (Chrome, Firefox, etc.)</li>
                              <li>Tap on Permissions</li>
                              <li>Make sure Camera is enabled</li>
                          </ol>
                      </div>
                      
                      <div class="text-left mt-4">
                          <p class="font-medium">For PWA on Android:</p>
                          <ol class="list-decimal pl-5 space-y-1 mt-2">
                              <li>If you've installed this as an app on your home screen:</li>
                              <li>Go to Settings > Apps > [This App Name]</li>
                              <li>Tap on Permissions > Camera</li>
                              <li>Select "Allow"</li>
                              <li>If not listed, go to Settings > Apps > Chrome > Site Settings</li>
                              <li>Find this website and reset permissions</li>
                              <li>You may need to clear app data or reinstall the PWA</li>
                          </ol>
                      </div>
                  `;
                }

                // Add general PWA instructions
                permissionInstructions += `
                  <div class="text-left mt-4">
                      <p class="font-medium">General PWA Troubleshooting:</p>
                      <ol class="list-decimal pl-5 space-y-1 mt-2">
                          <li>If using as an installed app (PWA), try these additional steps:</li>
                          <li>Close the app completely from recent apps</li>
                          <li>Clear browser cache and site data</li>
                          <li>Restart your device</li>
                          <li>If problems persist, uninstall the app and reinstall it</li>
                          <li>When reinstalling, make sure to allow camera permissions when prompted</li>
                      </ol>
                  </div>
              `;

                Swal.fire({
                    icon: 'info',
                    title: 'Enable Camera Access',
                    html: `
                      <div class="text-left">
                          <p>To use the scanner, you need to enable camera access in your browser settings.</p>
                          <p class="mt-2">Please follow these instructions:</p>
                          ${permissionInstructions}
                          <p class="mt-4">After enabling camera access, click "Try Again" to restart the scanner.</p>
                      </div>
                  `,
                    confirmButtonText: 'Try Again',
                    showCancelButton: true,
                    cancelButtonText: 'Cancel',
                    width: 'auto',
                    customClass: {
                        container: 'swal-wide',
                        popup: 'swal-wide-popup'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        // User clicked Try Again, reinitialize
                        scannerInitialized = false;
                        initializeScanner();
                    }
                });
            }

            // Function to initialize the scanner
            function initializeScanner() {
                if (scannerInitialized) return;
                scannerInitialized = true;

                // Hide the manual camera button initially
                document.getElementById('requestCameraBtn').classList.add('hidden');

                // Show loading indicator
                loadingDiv.classList.remove('hidden');

                // First check if mediaDevices is supported
                if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                    errorDiv.textContent = 'Your browser does not support camera access. Please try a different browser.';
                    errorDiv.style.display = 'block';
                    loadingDiv.classList.add('hidden');
                    return;
                }

                // Check if this is an iOS device
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

                // Check if this is a mobile device
                const isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

                // For mobile devices, try direct camera access with simpler constraints
                if (isMobile) {
                    console.log('Mobile device detected, using simplified camera access');

                    // For mobile, we'll try to start the camera directly with simplified constraints
                    const simpleConstraints = {
                        audio: false,
                        video: isIOS ? {
                            facingMode: 'environment'
                        } : true
                    };

                    navigator.mediaDevices.getUserMedia(simpleConstraints)
                        .then(function(stream) {
                            handleSuccessfulStream(stream);
                        })
                        .catch(function(error) {
                            console.error('Mobile camera access error:', error);

                            // If that fails, try an even simpler approach
                            navigator.mediaDevices.getUserMedia({
                                    video: true
                                })
                                .then(function(stream) {
                                    handleSuccessfulStream(stream);
                                })
                                .catch(function(finalError) {
                                    console.error('Final camera access error:', finalError);
                                    handleCameraError(finalError);
                                });
                        });

                    return;
                }

                // For desktop devices, proceed with normal flow
                navigator.mediaDevices.getUserMedia({
                        video: true
                    })
                    .then(function(stream) {
                        // Permission granted, now we can enumerate devices
                        stream.getTracks().forEach(track => track.stop()); // Stop the initial stream
                        getCameraDevices(); // Now get the actual camera devices
                    })
                    .catch(function(error) {
                        console.error('Initial camera permission error:', error);
                        handleCameraError(error);
                    });
            }

            // Helper function to handle a successful camera stream
            function handleSuccessfulStream(stream) {
                video.srcObject = stream;
                video.setAttribute('playsinline', true);
                video.setAttribute('autoplay', true);
                video.setAttribute('muted', true);

                video.onloadedmetadata = function() {
                    video.play()
                        .then(() => {
                            requestAnimationFrame(scanQRCode);
                            errorDiv.style.display = 'none';
                            loadingDiv.classList.add('hidden');
                        })
                        .catch(err => {
                            console.error('Error playing video:', err);
                            handleCameraError(err);
                        });
                };
            }

            // Access the device camera and start scanning
            async function startCamera(deviceId) {
                loadingDiv.classList.remove('hidden'); // Show loading indicator
                try {
                    console.log('Starting camera with device ID:', deviceId);

                    // Check if this is an iOS device
                    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;

                    // Configure constraints based on device
                    let constraints;

                    if (isIOS) {
                        // iOS needs simpler constraints
                        constraints = {
                            audio: false,
                            video: {
                                facingMode: 'environment'
                            }
                        };
                    } else {
                        // For other devices, use more specific constraints
                        constraints = deviceId ?
                            {
                                audio: false,
                                video: {
                                    deviceId: {
                                        exact: deviceId
                                    }
                                }
                            } :
                            {
                                audio: false,
                                video: {
                                    facingMode: {
                                        ideal: 'environment'
                                    },
                                    width: {
                                        ideal: 1280
                                    },
                                    height: {
                                        ideal: 720
                                    }
                                }
                            };
                    }

                    console.log('Using constraints:', JSON.stringify(constraints));

                    const stream = await navigator.mediaDevices.getUserMedia(constraints);
                    handleSuccessfulStream(stream);
                } catch (error) {
                    console.error('Camera start error:', error);

                    // If specific device ID fails, try with any camera
                    if (deviceId) {
                        try {
                            console.log('Trying with any available camera...');
                            const fallbackStream = await navigator.mediaDevices.getUserMedia({
                                video: true
                            });
                            handleSuccessfulStream(fallbackStream);
                        } catch (fallbackError) {
                            handleCameraError(fallbackError);
                        }
                    } else {
                        handleCameraError(error);
                    }
                } finally {
                    loadingDiv.classList.add('hidden'); // Hide loading indicator
                }
            }

            // Function to handle camera errors
            function handleCameraError(error) {
                console.error('Camera error:', error);
                errorDiv.textContent = 'Error accessing camera: ' + error.message;
                errorDiv.style.display = 'block';

                // Show manual camera access button for permission errors
                const requestCameraBtn = document.getElementById('requestCameraBtn');
                if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                    requestCameraBtn.classList.remove('hidden');
                }

                // Check for specific error messages
                const errorMessage = error.message.toLowerCase();
                const isVideoSourceError = errorMessage.includes('could not start video source') ||
                    errorMessage.includes('starting video failed');

                // Detect browser and OS for specific instructions
                const isChrome = /Chrome/.test(navigator.userAgent) && !/Edge|Edg/.test(navigator.userAgent);
                const isFirefox = /Firefox/.test(navigator.userAgent);
                const isSafari = /Safari/.test(navigator.userAgent) && !/Chrome/.test(navigator.userAgent);
                const isEdge = /Edge|Edg/.test(navigator.userAgent);
                const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent);
                const isAndroid = /Android/.test(navigator.userAgent);

                // Show a more user-friendly error message
                if (error.name === 'NotAllowedError' || error.name === 'PermissionDeniedError') {
                    // Create browser-specific instructions
                    let permissionInstructions = '';

                    if (isChrome) {
                        permissionInstructions = `
                          <div class="text-left mt-4">
                              <p class="font-medium">To enable camera access in Chrome:</p>
                              <ol class="list-decimal pl-5 space-y-1 mt-2">
                                  <li>Click the lock/info icon in the address bar</li>
                                  <li>Select "Site settings"</li>
                                  <li>Change Camera permission from "Block" to "Allow"</li>
                                  <li>Refresh this page</li>
                              </ol>
                          </div>
                      `;
                    } else if (isFirefox) {
                        permissionInstructions = `
                          <div class="text-left mt-4">
                              <p class="font-medium">To enable camera access in Firefox:</p>
                              <ol class="list-decimal pl-5 space-y-1 mt-2">
                                  <li>Click the lock/info icon in the address bar</li>
                                  <li>Click "Connection secure" or "Connection not secure"</li>
                                  <li>Click "More Information" and then "Permissions"</li>
                                  <li>Find "Use the Camera" and remove the current setting</li>
                                  <li>Refresh this page</li>
                              </ol>
                          </div>
                      `;
                    } else if (isSafari) {
                        permissionInstructions = `
                          <div class="text-left mt-4">
                              <p class="font-medium">To enable camera access in Safari:</p>
                              <ol class="list-decimal pl-5 space-y-1 mt-2">
                                  <li>Open Safari Preferences (click Safari in the menu bar, then Preferences)</li>
                                  <li>Go to the "Websites" tab</li>
                                  <li>Select "Camera" from the left sidebar</li>
                                  <li>Find this website and change permission to "Allow"</li>
                                  <li>Refresh this page</li>
                              </ol>
                          </div>
                      `;
                    } else if (isEdge) {
                        permissionInstructions = `
                          <div class="text-left mt-4">
                              <p class="font-medium">To enable camera access in Edge:</p>
                              <ol class="list-decimal pl-5 space-y-1 mt-2">
                                  <li>Click the lock/info icon in the address bar</li>
                                  <li>Click "Site permissions"</li>
                                  <li>Change Camera permission to "Allow"</li>
                                  <li>Refresh this page</li>
                              </ol>
                          </div>
                      `;
                    }

                    // Add mobile-specific instructions
                    if (isIOS) {
                        permissionInstructions += `
                          <div class="text-left mt-4">
                              <p class="font-medium">On iOS devices:</p>
                              <ol class="list-decimal pl-5 space-y-1 mt-2">
                                  <li>Open Settings app</li>
                                  <li>Scroll down and find your browser (Safari, Chrome, etc.)</li>
                                  <li>Tap on the browser and find "Camera"</li>
                                  <li>Make sure it's set to "Allow"</li>
                                  <li>If using PWA, go to Settings > Safari > Advanced > Website Data</li>
                                  <li>Find this website and tap "Remove" to reset permissions</li>
                              </ol>
                          </div>
                          
                          <div class="text-left mt-4">
                              <p class="font-medium">For PWA on iOS:</p>
                              <ol class="list-decimal pl-5 space-y-1 mt-2">
                                  <li>If you've installed this as an app on your home screen:</li>
                                  <li>Go to Settings > Safari > Advanced > Website Data</li>
                                  <li>Find and delete data for this website</li>
                                  <li>Close the app completely (swipe up from bottom)</li>
                                  <li>Restart the app and grant permission when prompted</li>
                                  <li>If that doesn't work, try uninstalling and reinstalling the app</li>
                              </ol>
                          </div>
                      `;
                    } else if (isAndroid) {
                        permissionInstructions += `
                          <div class="text-left mt-4">
                              <p class="font-medium">On Android devices:</p>
                              <ol class="list-decimal pl-5 space-y-1 mt-2">
                                  <li>Open Settings app</li>
                                  <li>Tap on Apps or Application Manager</li>
                                  <li>Find your browser (Chrome, Firefox, etc.)</li>
                                  <li>Tap on Permissions</li>
                                  <li>Make sure Camera is enabled</li>
                              </ol>
                          </div>
                          
                          <div class="text-left mt-4">
                              <p class="font-medium">For PWA on Android:</p>
                              <ol class="list-decimal pl-5 space-y-1 mt-2">
                                  <li>If you've installed this as an app on your home screen:</li>
                                  <li>Go to Settings > Apps > [This App Name]</li>
                                  <li>Tap on Permissions > Camera</li>
                                  <li>Select "Allow"</li>
                                  <li>If not listed, go to Settings > Apps > Chrome > Site Settings</li>
                                  <li>Find this website and reset permissions</li>
                                  <li>You may need to clear app data or reinstall the PWA</li>
                              </ol>
                          </div>
                      `;
                    }

                    // Add general PWA instructions
                    permissionInstructions += `
                      <div class="text-left mt-4">
                          <p class="font-medium">General PWA Troubleshooting:</p>
                          <ol class="list-decimal pl-5 space-y-1 mt-2">
                              <li>If using as an installed app (PWA), try these additional steps:</li>
                              <li>Close the app completely from recent apps</li>
                              <li>Clear browser cache and site data</li>
                              <li>Restart your device</li>
                              <li>If problems persist, uninstall the app and reinstall it</li>
                              <li>When reinstalling, make sure to allow camera permissions when prompted</li>
                          </ol>
                      </div>
                  `;

                    Swal.fire({
                        icon: 'error',
                        title: 'Camera Access Denied',
                        html: `
                          <div class="text-left">
                              <p>You've denied camera access which is required to scan QR codes.</p>
                              <p class="mt-2">Please follow these instructions to enable camera access:</p>
                              ${permissionInstructions}
                          </div>
                      `,
                        confirmButtonText: 'Try Again',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel',
                        width: 'auto',
                        customClass: {
                            container: 'swal-wide',
                            popup: 'swal-wide-popup'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // User clicked Try Again, reinitialize
                            scannerInitialized = false;
                            initializeScanner();
                        }
                    });
                } else if (error.name === 'NotFoundError') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Camera Not Found',
                        text: 'No camera was detected on your device. Please ensure your camera is connected and working properly.',
                        confirmButtonText: 'OK'
                    });
                } else if (isVideoSourceError) {
                    // Special handling for "could not start video source" error
                    Swal.fire({
                        icon: 'error',
                        title: 'Camera Start Failed',
                        html: `
                          <div class="text-left">
                              <p class="mb-3">There was a problem starting your camera. This could be due to:</p>
                              <ul class="list-disc pl-5 mb-3 space-y-1">
                                  <li>Another app is using your camera</li>
                                  <li>Your camera permissions need to be reset</li>
                                  <li>Your device needs to be restarted</li>
                              </ul>
                              <p>Try these steps:</p>
                              <ol class="list-decimal pl-5 space-y-1">
                                  <li>Close other apps that might be using the camera</li>
                                  <li>Check your browser settings and clear camera permissions</li>
                                  <li>Restart your device if the problem persists</li>
                              </ol>
                          </div>
                      `,
                        confirmButtonText: 'Try Again',
                        showCancelButton: true,
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // User clicked Try Again, reinitialize
                            scannerInitialized = false;
                            initializeScanner();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Camera Error',
                        text: 'There was a problem accessing your camera: ' + error.message,
                        confirmButtonText: 'OK'
                    });
                }

                loadingDiv.classList.add('hidden'); // Hide loading indicator
            }

            // Populate camera selection dropdown
            async function getCameraDevices() {
                try {
                    loadingDiv.classList.remove('hidden'); // Show loading indicator

                    // Clear existing options except the first one
                    while (cameraSelect.options.length > 1) {
                        cameraSelect.remove(1);
                    }

                    // Request permission first if needed
                    if (!defaultDeviceId) {
                        try {
                            const tempStream = await navigator.mediaDevices.getUserMedia({
                                video: true
                            });
                            tempStream.getTracks().forEach(track => track.stop());
                        } catch (permError) {
                            console.error('Permission error:', permError);
                            handleCameraError(permError);
                            return;
                        }
                    }

                    const devices = await navigator.mediaDevices.enumerateDevices();
                    console.log('Available devices:', devices);
                    let cameraCount = 0;
                    let hasBackCamera = false;
                    let backCameraId = '';

                    // First pass: identify back camera if available
                    devices.forEach(device => {
                        if (device.kind === 'videoinput') {
                            cameraCount++;
                            const label = device.label || `Camera ${cameraCount}`;

                            // Try to identify back camera by label
                            if (label.toLowerCase().includes('back') ||
                                label.toLowerCase().includes('rear') ||
                                label.toLowerCase().includes('environment')) {
                                hasBackCamera = true;
                                backCameraId = device.deviceId;
                            }
                        }
                    });

                    // Second pass: add all cameras to dropdown
                    devices.forEach(device => {
                        if (device.kind === 'videoinput') {
                            const option = document.createElement('option');
                            option.value = device.deviceId;
                            option.textContent = device.label || `Camera ${cameraSelect.options.length}`;

                            // If this is a back camera, mark it
                            if (device.deviceId === backCameraId) {
                                option.textContent += ' (Back)';
                            }

                            cameraSelect.appendChild(option);

                            // Set default device ID if not already set
                            // Prefer back camera if available
                            if (!defaultDeviceId || (hasBackCamera && device.deviceId === backCameraId)) {
                                defaultDeviceId = device.deviceId;
                                option.selected = true;
                            }
                        }
                    });

                    // Only show camera select if there are multiple cameras
                    if (cameraCount > 1) {
                        cameraSelect.classList.remove('hidden');
                    } else {
                        cameraSelect.classList.add('hidden');
                    }

                    // Start the camera with the default device
                    if (defaultDeviceId) {
                        console.log('Default camera selected:', defaultDeviceId);
                        startCamera(defaultDeviceId);
                    } else if (cameraCount === 0) {
                        // No cameras found, try to start with default camera
                        console.log('No cameras found, trying default camera');
                        startCamera('');
                    }
                } catch (error) {
                    console.error('Error enumerating devices:', error);
                    // Try to start with default camera as fallback
                    startCamera('');
                } finally {
                    loadingDiv.classList.add('hidden'); // Hide loading indicator
                }
            }

            // Scan QR code
            function scanQRCode() {
                if (video.readyState === video.HAVE_ENOUGH_DATA) {
                    const canvasElement = document.createElement('canvas');
                    const canvas = canvasElement.getContext('2d');
                    canvasElement.height = video.videoHeight;
                    canvasElement.width = video.videoWidth;
                    canvas.drawImage(video, 0, 0, canvasElement.width, canvasElement.height);
                    const imageData = canvas.getImageData(0, 0, canvasElement.width, canvasElement.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height);

                    if (code) {
                        const qrData = code.data;
                        resultDiv.textContent = 'QR Code Data: ' + qrData;
                        resultDiv.style.display = 'block'; // Show result
                        resultDiv.classList.remove('opacity-0');
                        resultDiv.classList.add('opacity-100');
                        console.log('QR Code Data:', qrData);

                        try {
                            // Extract ID from the QR code data (assuming it's a URL)
                            const urlParams = new URLSearchParams(new URL(qrData).search);
                            const id = urlParams.get('id');
                            // Check if ID is valid
                            if (!id) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Invalid QR Code',
                                    text: 'This is not a valid employee QR.'
                                }).then((result) => {
                                    if (result.isConfirmed || result.isDismissed) {
                                        // Continue scanning after alert is dismissed
                                        isAttendanceSent = false;
                                        requestAnimationFrame(scanQRCode);
                                    }
                                });
                                return;
                            }

                            // Check UID and send attendance request
                            if (id && !isAttendanceSent) {
                                isAttendanceSent = true; // Set flag to true to prevent further submissions
                                const uid = '<?php echo $userData['cust_uid']; ?>'; // Get UID from user data

                                $.ajax({
                                    url: '../attendance/checkUID.php',
                                    method: 'POST',
                                    data: {
                                        uid: uid
                                    },
                                    success: function(response) {
                                        if (response.success && response.exists) {
                                            // Send attendance request with encrypted employee ID
                                            $.ajax({
                                                url: '../attendance/addCustAttendance.php',
                                                method: 'POST',
                                                data: {
                                                    uid: uid.toUpperCase(),
                                                    note: 'Attendance recorded via QR scan',
                                                    emp_uid: id.toUpperCase(),
                                                    is_encrypted: false // Flag to indicate the ID is not encrypted
                                                },
                                                success: function(attendanceResponse) {
                                                    try {
                                                        attendanceResponse = JSON.parse(attendanceResponse);
                                                        if (attendanceResponse.success) {
                                                            // Show the clock out reminder in the main content
                                                            document.getElementById('clockOutReminder').classList.remove('hidden');

                                                            Swal.fire({
                                                                icon: 'success',
                                                                title: 'Attendance Recorded!',
                                                                html: `
                                                                <div class="text-left">
                                                                  <p class="mb-3">${attendanceResponse.message}</p>
                                                                  <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800 text-left">
                                                                    <p class="text-blue-700 dark:text-blue-300 font-medium flex items-start">
                                                                      <svg class="inline-block w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                      </svg>
                                                                      <span>Don't forget to clock out at the end of your session!</span>
                                                                    </p>
                                                                  </div>
                                                                </div>
                                                              `,
                                                                timer: 5000,
                                                                timerProgressBar: true,
                                                                showConfirmButton: false
                                                            }).then(() => {
                                                                isAttendanceSent = false; // Reset flag after alert is shown
                                                                document.getElementById('manualEmployeeId').value = ''; // Clear the input
                                                            });
                                                        } else {
                                                            Swal.fire({
                                                                icon: 'error',
                                                                title: 'Error',
                                                                text: attendanceResponse.message || 'Failed to record attendance. Please try again.'
                                                            }).then(() => {
                                                                isAttendanceSent = false; // Reset flag after alert is shown
                                                            });
                                                        }
                                                    } catch (e) {
                                                        Swal.fire({
                                                            icon: 'error',
                                                            title: 'Error',
                                                            text: 'Invalid server response. Please try again.'
                                                        }).then(() => {
                                                            isAttendanceSent = false; // Reset flag after alert is shown
                                                        });
                                                    }
                                                },
                                                error: function() {
                                                    Swal.fire({
                                                        icon: 'error',
                                                        title: 'Error',
                                                        text: 'Failed to record attendance. Please try again.'
                                                    }).then(() => {
                                                        isAttendanceSent = false; // Reset flag after alert is shown
                                                    });
                                                }
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Invalid UID',
                                                text: 'Please enter a valid UID'
                                            }).then(() => {
                                                isAttendanceSent = false; // Reset flag after alert is shown
                                            });
                                        }
                                    },
                                    error: function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Failed to verify UID. Please try again.'
                                        }).then(() => {
                                            isAttendanceSent = false; // Reset flag after alert is shown
                                        });
                                    }
                                });
                            }
                        } catch (e) {
                            console.error("Error processing QR code:", e);
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid QR Code Format',
                                text: 'The QR code does not contain a valid URL.'
                            }).then(() => {
                                isAttendanceSent = false;
                            });
                        }

                        // Highlight the detected QR code area
                        canvas.strokeStyle = 'red';
                        canvas.lineWidth = 4;
                        canvas.strokeRect(code.location.topLeftCorner.x, code.location.topLeftCorner.y, code.location.bottomRightCorner.x - code.location.topLeftCorner.x, code.location.bottomRightCorner.y - code.location.topLeftCorner.y);
                        errorDiv.textContent = ''; // Clear any previous errors
                        errorDiv.style.display = 'none'; // Hide error
                    } else {
                        resultDiv.classList.add('opacity-0');
                        resultDiv.style.display = 'none'; // Hide result
                        errorDiv.textContent = 'No QR code detected.';
                        errorDiv.style.display = 'block'; // Show error
                        errorDiv.classList.remove('opacity-0');
                    }
                }

                // Always continue scanning regardless of whether a QR code was found
                requestAnimationFrame(scanQRCode);
            }

            // Get available cameras on page load
            getCameraDevices();

            // Start camera on selection change
            cameraSelect.addEventListener('change', (event) => {
                const deviceId = event.target.value;
                console.log('Camera selection changed to:', deviceId);
                if (deviceId) {
                    startCamera(deviceId);
                }
            });

            // Add event listener for manual entry button
            document.getElementById('showManualEntryBtn').addEventListener('click', function() {
                const manualEntrySection = document.getElementById('manualEntrySection');
                manualEntrySection.classList.toggle('hidden');

                if (!manualEntrySection.classList.contains('hidden')) {
                    document.getElementById('manualEmployeeId').focus();
                }
            });

            // Add event listener for manual ID submission
            document.getElementById('submitManualId').addEventListener('click', function() {
                const employeeId = document.getElementById('manualEmployeeId').value.trim();

                if (!employeeId) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Empty Input',
                        text: 'Please enter an Employee ID'
                    });
                    return;
                }

                // Process the manual entry
                processManualEntry(employeeId);
            });

            // Also submit when Enter key is pressed in the input field
            document.getElementById('manualEmployeeId').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    document.getElementById('submitManualId').click();
                }
            });

            // Function to process manual entry
            function processManualEntry(employeeId) {
                if (!employeeId || isAttendanceSent) return;

                isAttendanceSent = true; // Set flag to true to prevent further submissions
                const uid = '<?php echo $userData['cust_uid']; ?>'; // Get UID from user data

                // MD5 encrypt the employee ID
                const encryptedEmployeeId = md5(employeeId.trim());

                // Show loading
                Swal.fire({
                    title: 'Processing...',
                    text: 'Recording your attendance',
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                $.ajax({
                    url: '../attendance/checkUID.php',
                    method: 'POST',
                    data: {
                        uid: uid
                    },
                    success: function(response) {
                        if (response.success && response.exists) {
                            // Send attendance request with encrypted employee ID
                            $.ajax({
                                url: '../attendance/addCustAttendance.php',
                                method: 'POST',
                                data: {
                                    uid: uid.toUpperCase(),
                                    note: 'Attendance recorded via manual entry',
                                    emp_uid: encryptedEmployeeId,
                                    is_encrypted: true // Flag to indicate the ID is encrypted
                                },
                                success: function(attendanceResponse) {
                                    try {
                                        attendanceResponse = JSON.parse(attendanceResponse);
                                        if (attendanceResponse.success) {
                                            // Show the clock out reminder in the main content
                                            document.getElementById('clockOutReminder').classList.remove('hidden');

                                            Swal.fire({
                                                icon: 'success',
                                                title: 'Attendance Recorded!',
                                                html: `
                                                <div class="text-left">
                                                  <p class="mb-3">${attendanceResponse.message}</p>
                                                  <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg border border-blue-200 dark:border-blue-800 text-left">
                                                    <p class="text-blue-700 dark:text-blue-300 font-medium flex items-start">
                                                      <svg class="inline-block w-5 h-5 mr-2 flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                      </svg>
                                                      <span>Don't forget to clock out at the end of your session!</span>
                                                    </p>
                                                  </div>
                                                </div>
                                              `,
                                                timer: 5000,
                                                timerProgressBar: true,
                                                showConfirmButton: false
                                            }).then(() => {
                                                isAttendanceSent = false; // Reset flag after alert is shown
                                                document.getElementById('manualEmployeeId').value = ''; // Clear the input
                                            });
                                        } else {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'Error',
                                                text: attendanceResponse.message || 'Failed to record attendance. Please try again.'
                                            }).then(() => {
                                                isAttendanceSent = false; // Reset flag after alert is shown
                                            });
                                        }
                                    } catch (e) {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Error',
                                            text: 'Invalid server response. Please try again.'
                                        }).then(() => {
                                            isAttendanceSent = false; // Reset flag after alert is shown
                                        });
                                    }
                                },
                                error: function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Error',
                                        text: 'Failed to record attendance. Please try again.'
                                    }).then(() => {
                                        isAttendanceSent = false; // Reset flag after alert is shown
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid UID',
                                text: 'Please enter a valid UID'
                            }).then(() => {
                                isAttendanceSent = false; // Reset flag after alert is shown
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to verify UID. Please try again.'
                        }).then(() => {
                            isAttendanceSent = false; // Reset flag after alert is shown
                        });
                    }
                });
            }
        }
    </script>
</body>

</html>