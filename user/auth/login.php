<?php
include("../../config.php");

// Check if cookie already exists
if (isset($_COOKIE['pmds_user_token'])) {
    header('Location: ../');
    exit();
}

// Check if all required parameters are present in URL
$required_params = ['user_id','cust_uid', 'email', 'name', 'phone'];
$has_all_params = true;

foreach ($required_params as $param) {
    if (!isset($_GET[$param]) || empty($_GET[$param])) {
        $has_all_params = false;
        break;
    }
}

if ($has_all_params) {
    // Sanitize inputs
    $cust_uid = mysqli_real_escape_string($conn, $_GET['cust_uid']);
    $email = mysqli_real_escape_string($conn, $_GET['email']);
    $phone = mysqli_real_escape_string($conn, $_GET['phone']);
    
    // Check if customer exists in database
    $query = "SELECT * FROM cust_details WHERE cust_uid = '$cust_uid' AND email = '$email' AND phone = '$phone'";
    $result = mysqli_query($conn, $query);
    
    if (mysqli_num_rows($result) > 0) {
        // Customer exists, create token
        $user_data = [
            'user_id' => $_GET['user_id'],
            'cust_uid' => $_GET['cust_uid'],
            'email' => $_GET['email'],
            'name' => $_GET['name'],
            'phone' => $_GET['phone'],
            'timestamp' => time()
        ];
        
        // Convert to JSON and encode for cookie storage
        $token = base64_encode(json_encode($user_data));
        
        // Set cookie with 29 days expiry
        setcookie('pmds_user_token', $token, time() + (29 * 24 * 60 * 60), '/', '', true, true);
        
        // Redirect to index page
        header('Location: ../');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Patel Motor Driving School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    animation: {
                        'bounce-slow': 'bounce 2s infinite',
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap">
</head>
<body class="bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 min-h-screen flex items-center justify-center p-4">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <!-- Logo -->
            <img src="https://patelmotordrivingschool.com/storage/images/icons/icon-logo-512x512.png" 
                 alt="PMDS Logo" 
                 class="h-20 w-20 mx-auto mb-4 rounded-full shadow-lg animate-bounce-slow"
                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'currentColor\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\' class=\'text-gray-900\'%3E%3Ccircle cx=\'12\' cy=\'12\' r=\'10\'%3E%3C/circle%3E%3Cpath d=\'M8 14s1.5 2 4 2 4-2 4-2\'%3E%3C/path%3E%3Cline x1=\'9\' y1=\'9\' x2=\'9.01\' y2=\'9\'%3E%3C/line%3E%3Cline x1=\'15\' y1=\'9\' x2=\'15.01\' y2=\'9\'%3E%3C/line%3E%3C/svg%3E'">
            
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                Access Required
            </h2>
            <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                <?php if ($has_all_params): ?>
                    Invalid credentials. Please verify your details.
                <?php else: ?>
                    Please scan your QR code to access your account
                <?php endif; ?>
            </p>
        </div>

        <div class="bg-white dark:bg-gray-800 py-8 px-4 shadow-xl rounded-lg sm:px-10 border border-gray-200 dark:border-gray-700">
            <div class="space-y-6">
                <!-- Icon -->
                <div class="mx-auto w-16 h-16 flex items-center justify-center rounded-full bg-red-50 dark:bg-red-900/20">
                    <svg class="w-8 h-8 text-red-500 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                              d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                        </path>
                    </svg>
                </div>

                <!-- Message -->
                <div class="text-center">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-3">
                        <?php if ($has_all_params): ?>
                            Invalid Customer Details
                        <?php else: ?>
                            Invalid Access Attempt
                        <?php endif; ?>
                    </h3>
                    <p class="text-gray-500 dark:text-gray-400">
                        <?php if ($has_all_params): ?>
                            The provided customer details do not match our records. Please contact support for assistance.
                        <?php else: ?>
                            Please visit our office to obtain your unique QR code for secure access to your account dashboard.
                        <?php endif; ?>
                    </p>
                </div>

                <!-- Action Buttons -->
                <div class="mt-6 space-y-4">
                    <a href="https://patelmotordrivingschool.com/#contact" 
                       class="w-full flex justify-center py-3 px-4 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                        Contact Support
                    </a>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="mt-8 text-center">
            <p class="text-xs text-gray-500 dark:text-gray-400">
                &copy; <?php echo date('Y'); ?> Patel Motor Driving School. All rights reserved.
            </p>
        </div>
    </div>

    <script>
        // Check system dark mode preference
        if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
    </script>
</body>
</html>
