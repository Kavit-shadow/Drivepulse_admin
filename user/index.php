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
?>
   <!DOCTYPE html>
   <html lang="en" class="h-full">

   <head>
      <meta charset="UTF-8">
      <meta name="viewport" content="width=device-width, initial-scale=1.0">
      <title>DrivePulse - Session Expired</title>
      <!-- Add cache control meta tags -->
      <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
      <meta http-equiv="Pragma" content="no-cache">
      <meta http-equiv="Expires" content="0">
      <script src="https://cdn.tailwindcss.com"></script>
      <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
      <script>
         // Add page reload prevention for back button navigation
         window.addEventListener('pageshow', function(event) {
            if (event.persisted || (window.performance && window.performance.navigation.type === 2)) {
               // Page is loaded from cache (back/forward button)
               window.location.reload(true);
            }
         });
      </script>
   </head>

   <body class="bg-gray-50 dark:bg-gray-900 min-h-screen flex items-center justify-center">
      <div class="max-w-md w-full mx-4">
         <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8 text-center">
            <div class="mb-6">
               <svg class="w-16 h-16 mx-auto text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
               </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
               <?php echo isset($_COOKIE['pmds_user_token']) ? 'Session Expired' : 'Login Required'; ?>
            </h2>
            <p class="text-gray-600 dark:text-gray-400 mb-6">
               <?php echo isset($_COOKIE['pmds_user_token'])
                  ? 'Your login session has expired. Please scan your QR code again to continue.'
                  : 'Please visit our office to scan the QR code and log in to your account.';
               ?>
            </p>
            <div class="space-y-4">
               <a href="https://patelmotordrivingschool.com/#contact"
                  class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                  Contact Support
               </a>
               <?php if (isset($_COOKIE['pmds_user_token'])): ?>
                  <div class="text-sm text-gray-500 dark:text-gray-400">
                     For security reasons, you have been logged out. Please log in again.
                  </div>
               <?php endif; ?>
            </div>
         </div>
      </div>
   </body>

   </html>
<?php
   exit();
}

// If we get here, the cookie is valid and not expired
session_start();
$userData = json_decode(base64_decode($_COOKIE['pmds_user_token']), true);
$_SESSION['pmds_user'] = $userData;

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

?>





<!DOCTYPE html>
<html lang="en" class="h-full">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>DrivePulse - Dashboard</title>
   <link rel="manifest" href="./manifest.json">
   <meta name="theme-color" content="#3b82f6">
   <link rel="apple-touch-icon" href="https://patelmotordrivingschool.com/storage/images/icons/icon-logo-512x512.png">
   <!-- Add cache control meta tags -->
   <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
   <meta http-equiv="Pragma" content="no-cache">
   <meta http-equiv="Expires" content="0">
   <script src="https://cdn.tailwindcss.com"></script>
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
            <a href="./" class="flex items-center space-x-3 px-3 py-2 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-white">
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
                  <h2 class="text-xl font-semibold text-gray-800 dark:text-white">Dashboard</h2>
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
                              echo '<img src="' . $pfp_path_header   . '?v=' . time() . '" alt="Profile" class="w-full h-full object-cover">';
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

            <div id="offlineAlert" class="hidden mb-6 col-span-1 md:col-span-2 lg:col-span-3">
               <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg p-4">
                  <div class="flex items-center">
                     <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400 dark:text-red-300" viewBox="0 0 20 20" fill="currentColor">
                           <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                     </div>
                     <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800 dark:text-red-100">You are offline</h3>
                        <div class="mt-2 text-sm text-red-700 dark:text-red-200">
                           <p>Please check your internet connection to access all features.</p>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <script>
               // Check if browser supports online/offline events
               if ('onLine' in navigator) {
                  const offlineAlert = document.getElementById('offlineAlert');

                  // Function to update UI based on connection status
                  function updateOnlineStatus() {
                     if (navigator.onLine) {
                        offlineAlert.classList.add('hidden');
                     } else {
                        offlineAlert.classList.remove('hidden');
                     }
                  }

                  // Add event listeners for online/offline events
                  window.addEventListener('online', updateOnlineStatus);
                  window.addEventListener('offline', updateOnlineStatus);

                  // Initial check
                  updateOnlineStatus();
               }
            </script>


            <!-- Install PWA Section -->
            <div id="installContainer" class="hidden mt-4 col-span-1 md:col-span-2 lg:col-span-3">
               <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 text-center">
                  <h4 class="text-lg font-semibold text-gray-800 dark:text-white mb-2">Install Our App</h4>
                  <p class="text-gray-600 dark:text-gray-400 mb-4">For a better experience, install our app on your device.</p>
                  <button id="installBtn" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 mb-4">
                     <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                     </svg>
                     Install App
                  </button>
                  <div class="text-sm text-gray-500 dark:text-gray-400 mt-2 border-t border-gray-200 dark:border-gray-700 pt-3">
                     <p class="mb-2"><strong>Or create a shortcut manually:</strong></p>
                     <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-left">
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                           <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-1">On Desktop:</h5>
                           <ol class="list-decimal list-inside text-gray-600 dark:text-gray-400 text-xs space-y-1">
                              <li>Click the menu (⋮) in your browser</li>
                              <li>Select "More tools"</li>
                              <li>Choose "Create shortcut" or "Add to desktop"</li>
                              <li>Check "Open as window" for app-like experience</li>
                           </ol>
                        </div>
                        <div class="bg-gray-50 dark:bg-gray-700/50 p-3 rounded-lg">
                           <h5 class="font-medium text-gray-700 dark:text-gray-300 mb-1">On Mobile:</h5>
                           <ol class="list-decimal list-inside text-gray-600 dark:text-gray-400 text-xs space-y-1">
                              <li>Open in Safari (iOS) or Chrome (Android)</li>
                              <li>Tap the share icon (iOS) or menu (Android)</li>
                              <li>Select "Add to Home Screen"</li>
                              <li>Name your shortcut and tap "Add"</li>
                           </ol>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
            <!-- Welcome Card -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 flex flex-col items-center text-center col-span-1 md:col-span-2 lg:col-span-3">
               <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-logo-512x512.png" alt="Welcome" class="w-16 h-16 mb-4 rounded-full shadow-lg">
               <h3 class="text-xl font-semibold text-gray-800 dark:text-white mb-2">Welcome to Patel Motor Driving School!</h3>
               <p class="text-gray-600 dark:text-gray-400 mb-4">Manage your driving school activities efficiently and effectively.</p>
               <p class="text-gray-500 dark:text-gray-300 mb-4">This dashboard provides you with easy access to your profile, attendance records, and other essential features to enhance your learning experience.</p>
               <p class="text-gray-500 dark:text-gray-300 mb-4">Feel free to explore and make the most of your time with us!</p>
               <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4 mt-4">
                  <a href="./profile" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                     View Profile
                  </a>
                  <a href="./scanner" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-purple-600 hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                     Scan Attendance
                  </a>
                  <a href="./attendance" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                     Check Attendance
                  </a>
               </div>
            </div>


         </div>
      </main>

      <?php include './includes/footer.php'; ?>
   </div>

   <script>
      // PWA Installation
      let deferredPrompt;
      const installContainer = document.getElementById('installContainer');
      const installBtn = document.getElementById('installBtn');

      // Register Service Worker
      if ('serviceWorker' in navigator) {
         navigator.serviceWorker.register('../sw.js', {
               scope: './'
            })
            .then((registration) => {
               console.log('Service Worker registered with scope:', registration.scope);
            })
            .catch((error) => {
               console.error('Service Worker registration failed:', error);
            });
      }

      // Handle PWA Install Prompt
      window.addEventListener('beforeinstallprompt', (e) => {
         // Don't prevent default here - this was causing the banner not to show
         deferredPrompt = e;
         // Show our custom install UI
         installContainer.classList.remove('hidden');
         console.log('Install prompt captured and ready to use');
      });

      installBtn.addEventListener('click', async () => {
         if (!deferredPrompt) {
            console.log('No install prompt available');
            return;
         }

         // Show the browser install prompt
         deferredPrompt.prompt();

         // Wait for the user to respond to the prompt
         const {
            outcome
         } = await deferredPrompt.userChoice;
         console.log(`User response to install prompt: ${outcome}`);

         // Clear the saved prompt since it can't be used again
         deferredPrompt = null;

         // Hide our install UI regardless of outcome
         installContainer.classList.add('hidden');
      });

      window.addEventListener('appinstalled', () => {
         installContainer.classList.add('hidden');
         deferredPrompt = null;
      });

      // Check if the app is already installed
      if (window.matchMedia('(display-mode: standalone)').matches) {
         installContainer.classList.add('hidden');
      }

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
   </script>
</body>

</html>