<?php
include "../configWeb.php";
include "includes/functions.php";


if(!isset($_GET['id']) && !isset($_GET['email']) && !isset($_GET['name'])){
    header('Location: ./');
    exit();
}



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



<?php

require_once('../lib/dompdf/autoload.inc.php');

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('chroot', realpath(''));
$pdf = new Dompdf($options);


ob_start();

include('../viewpdf.php');

$htmlCode = ob_get_clean();


$pdf->loadHtml($htmlCode);

$pdf->setPaper('A4', 'portrait');

$pdf->render();

$pdfDataUri = 'data:application/pdf;base64,' . base64_encode($pdf->output());
error_reporting(0);
ini_set('display_errors', 0);

?>


<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt - Patel Motor Driving School</title>
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
                <a href="./attendance" class="flex items-center space-x-3 px-3 py-2 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    <span>View Attendance</span>
                </a>

                <a href="./receipt?id=<?php echo $userData['phone']; ?>&email=<?php echo $userData['email']; ?>&name=<?php echo $userData['name']; ?>&cust_uid=<?php echo $userData['cust_uid']; ?>" class="flex items-center space-x-3 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
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
                        <h2 class="text-xl font-semibold text-gray-800 dark:text-white">View Receipt</h2>
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
        <main class="p-4 sm:p-6 mt-16 min-h-screen">
            <!-- PDF Viewer Container -->
            <div class="max-w-5xl mx-auto">
                <!-- PDF Controls -->
                <div class="mb-4 sm:mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg p-3 sm:p-4">
                    <!-- Mobile View Controls -->
                    <div class="block sm:hidden space-y-3">
                        <div class="flex justify-between items-center">
                            <div class="flex items-center space-x-2">
                                <button id="prevPage" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 disabled:opacity-50">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                    </svg>
                                </button>
                                <span class="text-sm text-gray-600 dark:text-gray-300">Page <span id="pageNum">1</span> of <span id="pageCount">1</span></span>
                                <button id="nextPage" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200 disabled:opacity-50">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                    </svg>
                                </button>
                            </div>
                            <!--<button class="downloadPdf" class="inline-flex items-center justify-center p-2 text-blue-600 dark:text-blue-400">-->
                            <!--    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
                            <!--        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>-->
                            <!--    </svg>-->
                            <!--</button>-->
                        </div>
                        <div class="flex justify-center items-center space-x-4">
                            <button id="zoomOut" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <span id="zoomLevel" class="text-sm text-gray-600 dark:text-gray-300 min-w-[60px] text-center">100%</span>
                            <button id="zoomIn" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Desktop View Controls -->
                    <div class="hidden sm:flex flex-wrap items-center justify-between gap-4">
                        <div class="flex items-center space-x-4">
                            <button id="prevPage" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                Previous
                            </button>
                            <button id="nextPage" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-gray-200 dark:border-gray-600 dark:hover:bg-gray-600 disabled:opacity-50">
                                Next
                                <svg class="w-5 h-5 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center space-x-4">
                            <span class="text-sm text-gray-600 dark:text-gray-300">Page <span id="pageNum">1</span> of <span id="pageCount">1</span></span>
                            <div class="flex items-center space-x-2">
                                <button id="zoomOut" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <span id="zoomLevel" class="text-sm text-gray-600 dark:text-gray-300 min-w-[60px] text-center">100%</span>
                                <button id="zoomIn" class="p-2 text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-200">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>
                            <!--<button class="downloadPdf" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">-->
                            <!--    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">-->
                            <!--        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>-->
                            <!--    </svg>-->
                            <!--    Download-->
                            <!--</button>-->
                        </div>
                    </div>
                </div>

                <!-- PDF Viewer -->
                <div id="pdfContainer" class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-2 sm:p-4 mb-2 overflow-hidden">
                    <div id="pdfViewer" class="w-full min-h-[calc(100vh-16rem)] sm:min-h-[800px] relative">
                        <div id="loadingSpinner" class="absolute inset-0 flex items-center justify-center bg-gray-100/80 dark:bg-gray-900/80 backdrop-blur-sm">
                            <div class="animate-spin rounded-full h-12 w-12 border-4 border-blue-500 border-t-transparent"></div>
                        </div>
                        <div id="gestureArea" class="absolute inset-0 touch-none">
                            <!-- Zoom indicator -->
                            <div id="zoomIndicator" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 bg-black/75 text-white px-4 py-2 rounded-lg text-sm font-medium opacity-0 transition-opacity duration-200 pointer-events-none">
                                100%
                            </div>
                        </div>
                        <canvas id="pdfCanvas" class="mx-auto touch-none"></canvas>
                    </div>
                </div>

                <!-- Receipt Notice -->
                <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg p-4 mb-6">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-blue-500 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm text-blue-700 dark:text-blue-300">
                                This is a computer-generated receipt. No signature is required.
                            </p>
                        </div>
                        <div class="flex items-center">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-800 text-blue-800 dark:text-blue-200">
                                Digital Document
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </main>
       
        <?php include './includes/footer.php'; ?>
    </div>

    <!-- Add PDF.js library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script>
        // Initialize PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

        // PDF viewer variables
        let pdfDoc = null;
        let pageNum = 1;
        let scale = 1.0;
        const canvas = document.getElementById('pdfCanvas');
        const ctx = canvas.getContext('2d');
        const loadingSpinner = document.getElementById('loadingSpinner');
        const isMobile = window.innerWidth < 640;

        // Convert base64 to binary
        function base64ToUint8Array(base64) {
            const raw = atob(base64.split(',')[1]);
            const uint8Array = new Uint8Array(raw.length);
            for (let i = 0; i < raw.length; i++) {
                uint8Array[i] = raw.charCodeAt(i);
            }
            return uint8Array;
        }

        // Touch handling variables
        let touchStartX = 0;
        let touchStartY = 0;
        let touchStartDistance = 0;
        let initialScale = 1;
        let lastTapTime = 0;
        let isPinching = false;
        let isScrolling = false;
        let startScrollTop = 0;
        let startScrollLeft = 0;
        const DOUBLE_TAP_DELAY = 300;
        const MIN_PINCH_SCALE = 0.5;
        const MAX_PINCH_SCALE = 3.0;
        
        const gestureArea = document.getElementById('gestureArea');
        const zoomIndicator = document.getElementById('zoomIndicator');
        const pdfViewer = document.getElementById('pdfViewer');

        // Show zoom indicator
        function showZoomIndicator(zoomLevel) {
            zoomIndicator.textContent = `${Math.round(zoomLevel * 100)}%`;
            zoomIndicator.style.opacity = '1';
            clearTimeout(zoomIndicator.timeout);
            zoomIndicator.timeout = setTimeout(() => {
                zoomIndicator.style.opacity = '0';
            }, 1000);
        }

        // Handle touch start
        gestureArea.addEventListener('touchstart', (e) => {
            const touch = e.touches[0];
            touchStartX = touch.clientX;
            touchStartY = touch.clientY;
            startScrollTop = pdfViewer.scrollTop;
            startScrollLeft = pdfViewer.scrollLeft;

            if (e.touches.length === 2) {
                isPinching = true;
                isScrolling = false;
                e.preventDefault();
                
                // Calculate initial pinch distance
                touchStartDistance = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
                initialScale = scale;
            } else if (e.touches.length === 1) {
                // Handle double tap to reset zoom
                const currentTime = new Date().getTime();
                const tapLength = currentTime - lastTapTime;
                
                if (tapLength < DOUBLE_TAP_DELAY && tapLength > 0) {
                    e.preventDefault();
                    scale = 1.0;
                    renderPage(pageNum);
                    showZoomIndicator(scale);
                }
                lastTapTime = currentTime;
            }
        }, { passive: false });

        // Handle touch move
        gestureArea.addEventListener('touchmove', (e) => {
            if (e.touches.length === 2 && isPinching) {
                e.preventDefault();
                const currentDistance = Math.hypot(
                    e.touches[0].clientX - e.touches[1].clientX,
                    e.touches[0].clientY - e.touches[1].clientY
                );
                
                const newScale = initialScale * (currentDistance / touchStartDistance);
                if (newScale >= MIN_PINCH_SCALE && newScale <= MAX_PINCH_SCALE) {
                    scale = newScale;
                    renderPage(pageNum);
                    showZoomIndicator(scale);
                }
            } else if (e.touches.length === 1 && scale > 1.0) {
                // Only enable panning when zoomed in
                const touch = e.touches[0];
                const deltaX = touchStartX - touch.clientX;
                const deltaY = touchStartY - touch.clientY;
                
                if (!isScrolling && (Math.abs(deltaX) > 5 || Math.abs(deltaY) > 5)) {
                    isScrolling = true;
                }

                if (isScrolling) {
                    e.preventDefault();
                    pdfViewer.scrollTop = startScrollTop + deltaY;
                    pdfViewer.scrollLeft = startScrollLeft + deltaX;
                }
            }
        }, { passive: false });

        // Handle touch end
        gestureArea.addEventListener('touchend', (e) => {
            if (isPinching) {
                isPinching = false;
                e.preventDefault();
            }
            isScrolling = false;
        }, { passive: false });

        // Handle touch cancel
        gestureArea.addEventListener('touchcancel', (e) => {
            isPinching = false;
            isScrolling = false;
        }, { passive: false });

        // Update the renderPage function to handle panning
        async function renderPage(num) {
            loadingSpinner.style.display = 'flex';
            const page = await pdfDoc.getPage(num);
            
            // Calculate scale to fit width while maintaining aspect ratio
            const viewport = page.getViewport({ scale: 1 });
            const containerWidth = document.getElementById('pdfViewer').clientWidth - (isMobile ? 16 : 32);
            const scaleFactor = containerWidth / viewport.width;
            const finalScale = scale * scaleFactor;
            
            const finalViewport = page.getViewport({ scale: finalScale });
            canvas.height = finalViewport.height;
            canvas.width = finalViewport.width;

            // Center the canvas if smaller than container
            const containerHeight = pdfViewer.clientHeight;
            if (finalViewport.height < containerHeight) {
                canvas.style.marginTop = `${(containerHeight - finalViewport.height) / 2}px`;
            } else {
                canvas.style.marginTop = '0';
            }

            const renderContext = {
                canvasContext: ctx,
                viewport: finalViewport
            };

            try {
                await page.render(renderContext);
            } finally {
                loadingSpinner.style.display = 'none';
            }

            // Update UI elements
            document.getElementById('pageNum').textContent = num;
            document.getElementById('zoomLevel').textContent = `${Math.round(scale * 100)}%`;
            
            // Update button states
            const prevButton = document.getElementById('prevPage');
            const nextButton = document.getElementById('nextPage');
            prevButton.disabled = num <= 1;
            nextButton.disabled = num >= pdfDoc.numPages;
            prevButton.style.opacity = prevButton.disabled ? '0.5' : '1';
            nextButton.style.opacity = nextButton.disabled ? '0.5' : '1';

            // Enable/disable scrolling based on zoom level
            pdfViewer.style.overflowX = scale > 1.0 ? 'auto' : 'hidden';
            pdfViewer.style.overflowY = scale > 1.0 ? 'auto' : 'hidden';
        }

        // Add CSS styles for the PDF viewer container
        const style = document.createElement('style');
        style.textContent = `
            #pdfViewer {
                -webkit-overflow-scrolling: touch;
                scroll-behavior: smooth;
                overscroll-behavior: none;
            }
            #pdfViewer::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }
            #pdfViewer::-webkit-scrollbar-track {
                background: transparent;
            }
            #pdfViewer::-webkit-scrollbar-thumb {
                background-color: rgba(156, 163, 175, 0.5);
                border-radius: 4px;
            }
            .dark #pdfViewer::-webkit-scrollbar-thumb {
                background-color: rgba(75, 85, 99, 0.5);
            }
        `;
        document.head.appendChild(style);

        // Initialize PDF viewer
        async function initPdfViewer() {
            try {
                const pdfData = '<?php echo $pdfDataUri; ?>';
                const uint8Array = base64ToUint8Array(pdfData);
                pdfDoc = await pdfjsLib.getDocument({ data: uint8Array }).promise;
                
                document.getElementById('pageCount').textContent = pdfDoc.numPages;
                renderPage(pageNum);
            } catch (error) {
                console.error('Error loading PDF:', error);
                loadingSpinner.style.display = 'none';
            }
        }

        // Event listeners
        document.getElementById('prevPage').addEventListener('click', () => {
            if (pageNum > 1) {
                pageNum--;
                renderPage(pageNum);
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (pageNum < pdfDoc.numPages) {
                pageNum++;
                renderPage(pageNum);
            }
        });

        document.getElementById('zoomIn').addEventListener('click', () => {
            if (scale < 2.0) {
                scale += 0.1;
                renderPage(pageNum);
            }
        });

        document.getElementById('zoomOut').addEventListener('click', () => {
            if (scale > 0.5) {
                scale -= 0.1;
                renderPage(pageNum);
            }
        });

        // document.querySelector('.downloadPdf').addEventListener('click', () => {
        //     const pdfData = '<?php echo $pdfDataUri; ?>';
        //     const link = document.createElement('a');
        //     link.href = pdfData;
        //     link.download = '<?php echo $userData['name']; ?>-receipt.pdf';
        //     document.body.appendChild(link);
        //     link.click();
        //     document.body.removeChild(link);
        // });

        // Handle window resize with debouncing
        let resizeTimeout;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(() => {
                renderPage(pageNum);
            }, 200);
        });

        // Initialize viewer when page loads
        initPdfViewer();
    </script>

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
        // let touchStartX = 0;
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
                e.target.closest('.table-container') ||
                e.target.closest('#pdfViewer')
            ) { // Ignore touches in table container
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
                e.target.closest('.table-container') ||
                e.target.closest('#pdfViewer')
            ) {
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