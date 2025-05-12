<?php
include "../configWeb.php";
include "../config.php";
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

// Function to get remaining time until expiration
function getRemainingTime($token)
{
    $userData = json_decode(base64_decode($token), true);
    $expiryTime = $userData['timestamp'] + (29 * 24 * 60 * 60);
    $remainingSeconds = $expiryTime - time();

    if ($remainingSeconds <= 0) {
        return "Expired";
    }

    $days = floor($remainingSeconds / (24 * 60 * 60));
    $hours = floor(($remainingSeconds % (24 * 60 * 60)) / (60 * 60));

    return "{$days} days, {$hours} hours";
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
$remainingTime = getRemainingTime($_COOKIE['pmds_user_token']);

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
    <title>Patel Motor Driving School - Profile</title>
    <!-- Add cache control meta tags -->
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Add Fancybox CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
    <!-- Add SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@sweetalert2/theme-dark@4/dark.css">
    <!-- Add Tippy.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/dist/tippy.css" />
    <link rel="stylesheet" href="https://unpkg.com/tippy.js@6/themes/light.css" />
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

        /* Custom SweetAlert2 Responsive Styles */
        .swal2-popup {
            font-size: 0.9rem !important;
            border-radius: 1rem !important;
            padding: 1.5rem !important;
            width: auto !important;
            max-width: 90% !important;
            margin: 1rem !important;
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

        /* License Documents Responsive Grid */
        @media (max-width: 640px) {
            #documentItems .grid {
                grid-template-columns: 1fr !important;
                gap: 0.75rem !important;
            }

            #documentItems .group {
                padding: 0.75rem !important;
            }

            #documentItems .flex-1 {
                min-width: 0 !important;
            }

            #documentItems .group-hover\:opacity-100 {
                opacity: 1 !important;
            }

            #documentItems .space-x-2 {
                gap: 0.75rem !important;
            }

            #previewContainer {
                grid-template-columns: 1fr !important;
            }
        }

        /* Improved touch targets for mobile */
        @media (max-width: 640px) {

            #documentItems button,
            #documentItems a {
                padding: 0.5rem !important;
                margin: 0 0.25rem !important;
            }

            #documentItems .w-5 {
                width: 1.25rem !important;
                height: 1.25rem !important;
            }

            #dropZone {
                padding: 1rem !important;
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
                <a href="./profile" class="flex items-center space-x-3 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
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
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Profile</h2>
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
            <div id="infoBlock" class="flex items-center justify-center mb-4">
                <span class="text-gray-800 bg-blue-200 dark:text-white dark:bg-blue-900 rounded-md p-3 text-sm font-medium shadow-md flex items-center">
                    <svg class="w-4 h-4 mr-2 text-blue-600 dark:text-blue-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Click on the profile picture to upload or edit your profile.
                    <button onclick="removeInfoBlock()" class="ml-2 p-1 bg-red-500 text-white rounded-md hover:bg-red-600">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </span>
            </div>
            <script>
                function removeInfoBlock() {
                    const infoBlock = document.getElementById('infoBlock');
                    if (infoBlock) {
                        infoBlock.remove();
                    }
                }
            </script>
            <?php
            // Get user data from cust_details table using phone number from session
            $user_phone = $_SESSION['pmds_user']['phone'];
            $query = "SELECT * FROM cust_details WHERE phone = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $user_phone);
            $stmt->execute();
            $result = $stmt->get_result();
            $user_data = $result->fetch_assoc();

            if ($user_data) {
                // Calculate training duration progress
                $start = new DateTime($user_data['startedAT']);
                $end = new DateTime($user_data['endedAT']);
                $now = new DateTime();
                $progress = 0;

                if ($now > $end) {
                    $progress = 100;
                } elseif ($now > $start) {
                    $total = $start->diff($end)->days;
                    $elapsed = $start->diff($now)->days;
                    $progress = min(100, ($elapsed / $total) * 100);
                }
            ?>
                <!-- Profile Header -->
                <div class="mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                        <div class="flex flex-col md:flex-row items-center space-y-4 md:space-y-0 md:space-x-6">
                            <div class="relative group" id="profilePicContainer" data-tippy-content="Click or hover to change profile picture">
                                <div class="w-24 h-24 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center overflow-hidden">
                                    <?php
                                    $pfp_path = '../storage/uploads/customer_documents/' . $user_data['cust_uid'] . '/pfp.png';
                                    if (file_exists($pfp_path)) {
                                        echo '<img src="' . $pfp_path . '?v=' . time() . '" alt="Profile" class="w-full h-full object-cover">';
                                    } else {
                                        echo '<span class="text-3xl font-bold text-blue-600 dark:text-blue-300">' . strtoupper(substr($user_data['name'], 0, 2)) . '</span>';
                                    }
                                    ?>
                                </div>
                                <!-- Hover Overlay -->
                                <div class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <label for="pfpInput" class="cursor-pointer w-24 h-24 rounded-full bg-black bg-opacity-50 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        </svg>
                                    </label>
                                    <input type="file" id="pfpInput" accept="image/*" class="hidden" onchange="uploadProfilePicture(this)">
                                </div>
                            </div>
                            <div class="text-center md:text-left">
                                <h1 class="text-2xl font-bold text-gray-900 dark:text-white"><?php echo htmlspecialchars($user_data['name']); ?></h1>
                                <p class="text-gray-600 dark:text-gray-400">Customer ID: <?php echo htmlspecialchars($user_data['cust_uid']); ?></p>
                                <div class="mt-2 flex flex-wrap justify-center md:justify-start gap-2">
                                    <?php if ($user_data['dueamount'] > 0) { ?>
                                        <span class="px-3 py-1 text-sm bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 rounded-full">Payment Due</span>
                                    <?php } else { ?>
                                        <span class="px-3 py-1 text-sm bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded-full">Fully Paid</span>
                                    <?php } ?>
                                    <span class="px-3 py-1 text-sm bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 rounded-full"><?php echo htmlspecialchars($user_data['vehicle']); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add this after your profile header section -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm p-4 mb-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="text-sm text-gray-600 dark:text-gray-300">
                                <span class="font-medium">Account Expiration:</span>
                                <span class="<?php echo $remainingTime === "Expired" ? "text-red-500" : "text-green-500"; ?> ml-2">
                                    <?php echo $remainingTime; ?>
                                </span>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <span class="tooltip" data-tippy-content="Your account will automatically log out after 29 days of inactivity">
                                <i class="fas fa-info-circle"></i>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Initialize tooltip for the info icon -->
                <script>
                    tippy(".tooltip", {
                        theme: "light",
                        placement: "top"
                    });
                </script>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Personal Information Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Personal Information</h2>
                            <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                        </div>
                        <div class="space-y-4">
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Name</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo htmlspecialchars($user_data['name']); ?></p>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Email</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo htmlspecialchars($user_data['email']); ?></p>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Phone</label>
                                <p class="text-gray-800 dark:text-white font-medium">
                                    <a href="tel:<?php echo htmlspecialchars($user_data['phone']); ?>" class="hover:text-blue-600 dark:hover:text-blue-400">
                                        <?php echo htmlspecialchars($user_data['phone']); ?>
                                    </a>
                                </p>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Address</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo htmlspecialchars($user_data['address']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Payment Information Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Payment Information</h2>
                            <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="space-y-4">
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Amount</label>
                                <p class="text-xl font-bold text-gray-800 dark:text-white">₹<?php echo number_format($user_data['totalamount']); ?></p>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Paid Amount</label>
                                <p class="text-xl font-bold text-green-600 dark:text-green-400">₹<?php echo number_format($user_data['paidamount']); ?></p>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Due Amount</label>
                                <p class="text-xl font-bold text-red-600 dark:text-red-400">₹<?php echo number_format($user_data['dueamount']); ?></p>
                                <?php if ($user_data['dueamount'] > 0) { ?>
                                    <span class="mt-1 text-sm text-red-600 dark:text-red-400">Payment pending</span>
                                <?php } ?>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Payment Method</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo htmlspecialchars($user_data['payment_method']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Training Details Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Training Details</h2>
                            <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                        <div class="space-y-4">
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Vehicle</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo htmlspecialchars($user_data['vehicle']); ?></p>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Training Duration</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo htmlspecialchars($user_data['days']); ?> Days</p>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Time Slot</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo htmlspecialchars($user_data['timeslot']); ?></p>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">RTO Work</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo htmlspecialchars($user_data['newlicence']); ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- License Documents Card (New Section) -->
                    <?php if ($user_data['newlicence'] === 'Applied') { ?>
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 sm:p-6 hover:shadow-lg transition-shadow duration-300 col-span-full">
                            <div class="flex items-center justify-between mb-4">
                                <h2 class="text-lg sm:text-xl font-semibold text-gray-800 dark:text-white flex items-center">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Upload Documents
                                </h2>
                            </div>

                            <!-- Upload Form -->
                            <form id="uploadForm" action="../api_ajax/upload_documents.php" method="post" enctype="multipart/form-data" class="space-y-3 sm:space-y-4">
                                <input type="hidden" name="cust_uid" value="<?php echo htmlspecialchars($user_data['cust_uid']); ?>">
                                <div class="space-y-2">
                                    <div id="dropZone" class="relative border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-lg p-4 sm:p-6 transition-all duration-300 ease-in-out hover:border-blue-500 dark:hover:border-blue-400 group">
                                        <input type="file" id="fileInput" name="license_docs[]" multiple accept=".pdf,.jpg,.jpeg,.png"
                                            class="absolute inset-0 w-full h-full opacity-0 z-50 cursor-pointer">
                                        <div class="text-center">
                                            <div class="mx-auto h-12 w-12 sm:h-16 sm:w-16 text-blue-500 dark:text-blue-400 mb-3 sm:mb-4 transform transition-transform group-hover:scale-110">
                                                <svg stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                                    <path d="M28 8H12a4 4 0 00-4 4v20m0 0v4a4 4 0 004 4h20a4 4 0 004-4V28m-4-4h4a4 4 0 004-4V12a4 4 0 00-4-4h-4m-12 8v-4m0 4v4m0-4h12" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </div>
                                            <div class="mt-3 sm:mt-4 flex flex-col space-y-1 sm:space-y-2 text-sm text-gray-600 dark:text-gray-400">
                                                <div class="flex justify-center items-center space-x-1">
                                                    <span class="font-semibold text-blue-600 dark:text-blue-400 hover:text-blue-500">Click to upload</span>
                                                    <span class="hidden sm:inline">or drag and drop</span>
                                                </div>
                                                <p class="text-xs">PDF, JPG, JPEG, PNG up to 5MB each</p>
                                            </div>
                                        </div>
                                        <div id="previewContainer" class="mt-3 sm:mt-4 grid grid-cols-1 sm:grid-cols-2 gap-2 sm:gap-3"></div>
                                    </div>
                                </div>
                                <button type="submit"
                                    class="w-full flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-lg 
                                hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 
                                focus:ring-blue-500 dark:bg-blue-600 dark:hover:bg-blue-700 
                                dark:focus:ring-offset-gray-800 transition-all duration-300 transform hover:scale-[1.02]">
                                    <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                    </svg>
                                    Upload Documents
                                </button>
                            </form>

                            <!-- Document List -->
                            <div class="mt-4 sm:mt-6" id="documentList">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-white mb-2 sm:mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    Uploaded Documents
                                </h3>
                                <div class="space-y-2 sm:space-y-3 max-h-[300px] overflow-y-auto" id="documentItems">
                                    <!-- Documents will be loaded here via AJAX -->
                                    <div class="flex items-center justify-center p-4 text-sm text-gray-500 dark:text-gray-400">
                                        <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Loading documents...
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>

                    <!-- Trainer Information Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 hover:shadow-xl transition-shadow duration-300 border border-gray-200 dark:border-gray-700">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Trainer Information</h2>
                            <svg class="w-6 h-6 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="space-y-4">
                            <div class="flex flex-col">

                                <div class="flex items-center space-x-4">
                                    <?php
                                    // Fetch the trainer's photo from the employees table
                                    $trainerName = htmlspecialchars($user_data['trainername']);
                                    $trainerPhone = htmlspecialchars($user_data['trainerphone']);
                                    $resultPFP = mysqli_query($conn, "SELECT photo, photo_type FROM employees WHERE name = '$trainerName' AND phone = '$trainerPhone'");
                                    if ($resultPFP && mysqli_num_rows($resultPFP) > 0) {
                                        $row = mysqli_fetch_assoc($resultPFP);
                                        $trainerPhoto = $row['photo'];
                                        $photoType = $row['photo_type'];
                                    } else {
                                        $trainerPhoto = null;
                                    }
                                    if ($trainerPhoto): ?>
                                        <a href="data:<?php echo $photoType; ?>;base64,<?php echo base64_encode($trainerPhoto); ?>" data-fancybox="trainer-photo" data-caption="<?php echo htmlspecialchars($user_data['trainername']); ?>">
                                            <img src="data:<?php echo $photoType; ?>;base64,<?php echo base64_encode($trainerPhoto); ?>" alt="Trainer Photo" class="w-16 h-16 rounded-full object-cover border-2 border-blue-500 dark:border-blue-400">
                                        </a>
                                    <?php else: ?>
                                        <img src="../../assets/Default_Profile.png" alt="Default Trainer Photo" class="w-16 h-16 rounded-full object-cover border-2 border-blue-500 dark:border-blue-400">
                                    <?php endif; ?>

                                </div>
                            </div>
                            <div>
                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Trainer Name</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo htmlspecialchars($user_data['trainername']); ?></p>
                            </div>
                            <div class="flex flex-col">

                                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Trainer Phone</label>
                                <p class="text-gray-800 dark:text-white font-medium">
                                    <a href="tel:<?php echo htmlspecialchars($user_data['trainerphone']); ?>" class="hover:text-blue-600 dark:hover:text-blue-400 transition-colors">
                                        <?php echo htmlspecialchars($user_data['trainerphone']); ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Information Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Schedule Information</h2>
                            <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div class="space-y-4">
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Start Date</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo date('F j, Y', strtotime($user_data['startedAT'])); ?></p>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">End Date</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo date('F j, Y', strtotime($user_data['endedAT'])); ?></p>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Registration Date</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo date('F j, Y', strtotime($user_data['date'])); ?></p>
                            </div>
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Registration Time</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo date('g:i A', strtotime($user_data['time'])); ?></p>
                            </div>

                            <!-- Attendance Progress -->
                            <div class="mt-6 space-y-4" id="attendanceProgress">
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Attendance Progress</label>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400" id="attendanceStats">Loading...</span>
                                    </div>
                                </div>
                                <div class="relative">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                        <div id="attendanceProgressBar" class="bg-blue-600 h-2.5 rounded-full transition-all duration-500" style="width: 0%"></div>
                                    </div>
                                    <div class="mt-2 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span id="presentDays">-</span>
                                        <span id="totalDays">-</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Training Duration Progress -->
                            <div class="mt-6 space-y-4">
                                <div class="flex items-center justify-between">
                                    <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Training Duration</label>
                                    <div class="flex items-center space-x-2">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">
                                            <?php
                                            if ($progress == 100) {
                                                echo '<span class="text-green-600 dark:text-green-400">Completed</span>';
                                            } else {
                                                echo round($progress) . '% Complete';
                                            }
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <div class="relative">
                                    <div class="w-full bg-gray-200 rounded-full h-2.5 dark:bg-gray-700">
                                        <div class="<?php echo $progress == 100 ? 'bg-green-600' : 'bg-blue-600'; ?> h-2.5 rounded-full transition-all duration-500" style="width: <?php echo $progress; ?>%"></div>
                                    </div>
                                    <div class="mt-2 flex justify-between text-xs text-gray-500 dark:text-gray-400">
                                        <span><?php echo date('M j', strtotime($user_data['startedAT'])); ?></span>
                                        <span><?php echo date('M j', strtotime($user_data['endedAT'])); ?></span>
                                    </div>
                                </div>
                                <?php if ($progress < 100) { ?>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <?php
                                        $daysLeft = $now->diff($end)->days;
                                        echo $daysLeft > 0 ? $daysLeft . ' days remaining' : 'Last day of training';
                                        ?>
                                    </p>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Additional Information</h2>
                            <svg class="w-6 h-6 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="space-y-4">
                            <div class="flex flex-col">
                                <label class="text-sm font-medium text-gray-600 dark:text-gray-400">Form Filled By</label>
                                <p class="text-gray-800 dark:text-white font-medium"><?php echo htmlspecialchars($user_data['formfiller']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php
            } else {
                echo '<div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6">
                    <div class="text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No Data Found</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">We couldn\'t find any user data for your profile.</p>
                    </div>
                </div>';
            }
            ?>
        </main>

        <?php include './includes/footer.php'; ?>
    </div>

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
            if (e.target.closest('.theme-switch') || e.target.closest('a') || e.target.closest('button') || e.target.closest('#documentItems')) {
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
    </script>

    <!-- Add this before closing body tag -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Add Fancybox JS -->
    <script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
    <!-- Add SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <!-- Add Tippy.js -->
    <script src="https://unpkg.com/@popperjs/core@2"></script>
    <script src="https://unpkg.com/tippy.js@6"></script>

    <script>
        $(document).ready(function() {
            // Initialize Tippy.js
            tippy('#profilePicContainer', {
                content: (reference) => {
                    // Different messages for touch and non-touch devices
                    return window.matchMedia('(pointer: coarse)').matches ?
                        "Tap to change profile picture" :
                        "Click or hover to change profile picture";
                },
                placement: 'bottom',
                theme: 'light',
                animation: 'scale',
                arrow: true,
                touch: true, // Enable on touch devices
                trigger: window.matchMedia('(pointer: coarse)').matches ? 'click' : 'mouseenter focus',
                hideOnClick: false,
                duration: [300, 250], // [show, hide] duration in ms
                onShow(instance) {
                    // Show tooltip immediately on touch devices
                    if (window.matchMedia('(pointer: coarse)').matches) {
                        setTimeout(() => {
                            instance.hide();
                        }, 2000); // Hide after 2 seconds on mobile
                    } else {
                        setTimeout(() => {
                            instance.hide();
                        }, 3000); // Hide after 3 seconds on desktop
                    }
                },
                onMount(instance) {
                    // Show tooltip automatically on first view
                    const hasSeenTooltip = localStorage.getItem('hasSeenProfilePicTooltip');
                    if (!hasSeenTooltip) {
                        setTimeout(() => {
                            instance.show();
                            localStorage.setItem('hasSeenProfilePicTooltip', 'true');
                        }, 1000);
                    }
                }
            });

            // Initialize Fancybox
            Fancybox.bind("[data-fancybox]", {
                // Your custom options
            });

            // Function to handle profile picture upload
            window.uploadProfilePicture = function(input) {
                if (input.files && input.files[0]) {
                    const file = input.files[0];

                    // Validate file size (max 5MB)
                    if (file.size > 5 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File too large',
                            text: 'Profile picture must be less than 5MB'
                        });
                        return;
                    }

                    // Validate file type
                    if (!file.type.match('image.*')) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid file type',
                            text: 'Please upload an image file'
                        });
                        return;
                    }

                    // Show loading state
                    Swal.fire({
                        title: 'Uploading...',
                        text: 'Please wait while we upload your profile picture',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Create FormData
                    const formData = new FormData();
                    formData.append('profile_picture', file);
                    formData.append('cust_uid', '<?php echo $user_data['cust_uid']; ?>');

                    // Upload the file
                    $.ajax({
                        url: '../api_ajax/upload_profile_picture.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            try {
                                const data = typeof response === 'string' ? JSON.parse(response) : response;
                                if (data.success) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Success',
                                        text: 'Profile picture updated successfully',
                                        timer: 1500,
                                        showConfirmButton: false
                                    }).then(() => {
                                        // Reload the page to show new profile picture
                                        // Force a hard refresh to clear the cache
                                        window.location.href = window.location.href.split('?')[0] + '?refresh=' + new Date().getTime();
                                    });
                                } else {
                                    throw new Error(data.message || 'Error uploading profile picture');
                                }
                            } catch (error) {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: error.message || 'Error uploading profile picture'
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error uploading profile picture'
                            });
                        }
                    });
                }
            };

            // Get user ID from the session
            const userId = '<?php echo $user_data['cust_uid']; ?>';

            // Function to update attendance progress
            function updateAttendanceProgress() {
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

                            // Get all dates between first and last attendance
                            const allDates = [];
                            if (data.length > 0) {
                                const startDate = new Date(data[0].date);
                                const endDate = new Date(data[data.length - 1].date);

                                for (let d = new Date(startDate); d <= endDate; d.setDate(d.getDate() + 1)) {
                                    allDates.push(new Date(d));
                                }
                            }

                            // Calculate statistics
                            const presentCount = data.length;
                            const totalDays = parseInt('<?php echo $user_data['days']; ?>');
                            const progressPercentage = Math.min(100, (presentCount / totalDays) * 100);

                            // Update the UI
                            $('#attendanceProgressBar').css('width', progressPercentage + '%');
                            $('#attendanceStats').html(`${presentCount}/${totalDays} days`);
                            $('#presentDays').text(`Present: ${presentCount}`);
                            $('#totalDays').text(`Total: ${totalDays}`);

                            // Update progress bar color based on attendance
                            const progressBar = $('#attendanceProgressBar');
                            if (progressPercentage < 40) {
                                progressBar.removeClass('bg-blue-600 bg-green-600').addClass('bg-red-600');
                            } else if (progressPercentage < 75) {
                                progressBar.removeClass('bg-red-600 bg-green-600').addClass('bg-blue-600');
                            } else {
                                progressBar.removeClass('bg-red-600 bg-blue-600').addClass('bg-green-600');
                            }

                        } catch (error) {
                            console.error('Error processing attendance data:', error);
                            $('#attendanceStats').html('Error loading attendance');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching attendance:', error);
                        $('#attendanceStats').html('Error loading attendance');
                    }
                });
            }

            // Initial load
            updateAttendanceProgress();

            // Refresh every 5 minutes
            setInterval(updateAttendanceProgress, 5 * 60 * 1000);

            // Function to show loading state
            function showLoading(message = 'Processing...') {
                Swal.fire({
                    title: message,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            }

            // Function to show success message
            function showSuccess(message) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: message,
                    showConfirmButton: false,
                    timer: 2000
                });
            }

            // Function to show error message
            function showError(message) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: message
                });
            }

            // Handle form submission
            $('#uploadForm').on('submit', function(e) {
                e.preventDefault();

                const formData = new FormData(this);

                if (formData.getAll('license_docs[]').length === 0) {
                    showError('Please select at least one file to upload');
                    return;
                }

                showLoading('Uploading documents...');

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        try {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;

                            if (data.success) {
                                showSuccess('Documents uploaded successfully');
                                loadDocuments();
                                $('#previewContainer').empty();
                                $('#fileInput').val('');
                            } else {
                                showError(data.message || 'Error uploading documents');
                            }
                        } catch (error) {
                            showError('Error processing upload response');
                        }
                    },
                    error: function() {
                        showError('Error uploading documents');
                    }
                });
            });

            // Function to load documents
            function loadDocuments() {
                const custUid = '<?php echo $user_data['cust_uid']; ?>';

                $.ajax({
                    url: '../api_ajax/get_documents.php',
                    method: 'POST',
                    data: {
                        cust_uid: custUid
                    },
                    success: function(response) {
                        try {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            const documentItems = $('#documentItems');

                            if (data.length === 0) {
                                documentItems.html(`
                                <div class="text-center py-6">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No documents</h3>
                                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Upload your license documents to get started</p>
                                </div>
                            `);
                                return;
                            }

                            let html = '<div class="grid gap-3 sm:grid-cols-2">';
                            data.forEach(doc => {
                                const isImage = /\.(jpg|jpeg|png)$/i.test(doc.filename);
                                const fileIcon = isImage ?
                                    `<img src="${doc.url}" class="w-10 h-10 object-cover rounded" alt="${doc.filename}">` :
                                    `<svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>`;

                                html += `
                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700/50 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors group">
                                    <div class="flex items-center space-x-3 flex-1 min-w-0">
                                        <div class="flex-shrink-0">
                                            ${fileIcon}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${doc.filename}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">Uploaded on ${new Date(doc.upload_date).toLocaleDateString()}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        ${isImage ? 
                                            `<a href="${doc.url}" data-fancybox="gallery" class="p-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                                </svg>
                                            </a>` :
                                            `<a href="${doc.url}" target="_blank" class="p-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                                </svg>
                                            </a>`
                                        }
                                        <button onclick="renameDocument('${doc.id}', '${doc.filename}')" class="p-1 text-yellow-600 dark:text-yellow-400 hover:text-yellow-800 dark:hover:text-yellow-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </button>
                                        <button onclick="deleteDocument('${doc.id}')" class="p-1 text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            `;
                            });
                            html += '</div>';
                            documentItems.html(html);

                            // Reinitialize Fancybox
                            Fancybox.bind("[data-fancybox]");
                        } catch (error) {
                            console.error('Error processing documents:', error);
                            showError('Error loading documents');
                        }
                    },
                    error: function(xhr, status, error) {
                        console.error('Error fetching documents:', error);
                        showError('Error loading documents');
                    }
                });
            }

            // Function to delete document
            window.deleteDocument = function(docId) {
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
                        showLoading('Deleting document...');

                        $.ajax({
                            url: '../api_ajax/delete_document.php',
                            method: 'POST',
                            data: {
                                doc_id: docId
                            },
                            success: function(response) {
                                try {
                                    const data = typeof response === 'string' ? JSON.parse(response) : response;
                                    if (data.success) {
                                        showSuccess('Document deleted successfully');
                                        loadDocuments();
                                    } else {
                                        showError(data.message || 'Error deleting document');
                                    }
                                } catch (error) {
                                    showError('Error processing delete response');
                                }
                            },
                            error: function() {
                                showError('Error deleting document');
                            }
                        });
                    }
                });
            };

            // Function to rename document
            window.renameDocument = function(docId, currentName) {
                Swal.fire({
                    title: 'Rename Document',
                    input: 'text',
                    inputValue: currentName,
                    inputAttributes: {
                        maxlength: 100
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Rename',
                    showLoaderOnConfirm: true,
                    preConfirm: (newName) => {
                        if (!newName || newName.trim() === '') {
                            Swal.showValidationMessage('Please enter a valid name');
                            return false;
                        }
                        if (newName === currentName) {
                            Swal.showValidationMessage('New name must be different');
                            return false;
                        }

                        // Add file extension if not present
                        const currentExt = currentName.split('.').pop();
                        if (!newName.toLowerCase().endsWith('.' + currentExt.toLowerCase())) {
                            newName = newName + '.' + currentExt;
                        }

                        return $.ajax({
                            url: '../api_ajax/rename_document.php',
                            method: 'POST',
                            data: {
                                doc_id: docId,
                                new_name: newName
                            }
                        }).then(response => {
                            const data = typeof response === 'string' ? JSON.parse(response) : response;
                            if (!data.success) {
                                throw new Error(data.message || 'Error renaming document');
                            }
                            return data;
                        }).catch(error => {
                            Swal.showValidationMessage(error.message || 'Error renaming document');
                        });
                    },
                    allowOutsideClick: () => !Swal.isLoading()
                }).then((result) => {
                    if (result.isConfirmed) {
                        showSuccess('Document renamed successfully');
                        loadDocuments();
                    }
                });
            };

            // Initial load of documents if license type is Applied
            <?php if ($user_data['newlicence'] === 'Applied') { ?>
                loadDocuments();
            <?php } ?>
        });
    </script>

    <!-- Add this before the closing </body> tag -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.getElementById('dropZone');
            const fileInput = document.getElementById('fileInput');
            const previewContainer = document.getElementById('previewContainer');

            // Prevent default drag behaviors
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
                document.body.addEventListener(eventName, preventDefaults, false);
            });

            // Highlight drop zone when dragging over it
            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            // Handle dropped files
            dropZone.addEventListener('drop', handleDrop, false);
            fileInput.addEventListener('change', handleFiles, false);

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            function highlight(e) {
                dropZone.classList.add('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20', 'scale-[1.02]');
                dropZone.classList.add('transform', 'transition-all', 'duration-200');
            }

            function unhighlight(e) {
                dropZone.classList.remove('border-blue-500', 'bg-blue-50', 'dark:bg-blue-900/20', 'scale-[1.02]');
            }

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const files = dt.files;
                handleFiles({
                    target: {
                        files: files
                    }
                });
            }

            function handleFiles(e) {
                const files = [...e.target.files];
                previewContainer.innerHTML = ''; // Clear previous previews

                files.forEach(file => {
                    if (file.size > 5 * 1024 * 1024) {
                        Swal.fire({
                            icon: 'error',
                            title: 'File too large',
                            text: `${file.name} is too large. Maximum size is 5MB.`
                        });
                        return;
                    }

                    if (!['image/jpeg', 'image/png', 'application/pdf'].includes(file.type)) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Invalid file type',
                            text: `${file.name} is not a supported format.`
                        });
                        return;
                    }

                    const preview = document.createElement('div');
                    preview.className = 'relative p-3 bg-gray-50 dark:bg-gray-700 rounded-lg flex items-center space-x-3 group';

                    // Icon based on file type
                    const icon = document.createElement('div');
                    if (file.type === 'application/pdf') {
                        icon.innerHTML = `<svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>`;
                    } else {
                        const img = document.createElement('img');
                        img.src = URL.createObjectURL(file);
                        img.className = 'w-10 h-10 object-cover rounded';
                        icon.appendChild(img);
                    }

                    const details = document.createElement('div');
                    details.className = 'flex-1 min-w-0';
                    details.innerHTML = `
                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">${file.name}</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400">${formatFileSize(file.size)}</p>
                `;

                    // Remove button
                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'absolute top-2 right-2 p-1 rounded-full bg-red-100 text-red-600 opacity-0 group-hover:opacity-100 transition-opacity';
                    removeBtn.innerHTML = `
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                `;
                    removeBtn.onclick = function() {
                        preview.remove();
                        const dt = new DataTransfer();
                        const input = document.getElementById('fileInput');
                        const {
                            files
                        } = input;

                        for (let i = 0; i < files.length; i++) {
                            const f = files[i];
                            if (f !== file) dt.items.add(f);
                        }

                        input.files = dt.files;
                    };

                    preview.appendChild(icon);
                    preview.appendChild(details);
                    preview.appendChild(removeBtn);
                    previewContainer.appendChild(preview);
                });

                // Update the file input
                fileInput.files = e.target.files;
            }

            function formatFileSize(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
            }
        });
    </script>
</body>

</html>