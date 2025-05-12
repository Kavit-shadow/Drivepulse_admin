<?php

function logActivity($logType, $who, $activity)
{
   date_default_timezone_set('Asia/Kolkata');

   $logFolder = 'logs/' . $logType;

   // Ensure log directory exists
   if (!file_exists($logFolder)) {
      mkdir($logFolder, 0755, true);
   }

   $logFile = $logFolder . '/logs.json';
   
   // Create empty array if file doesn't exist
   if (!file_exists($logFile)) {
      file_put_contents($logFile, '[]');
   }

   // Prepare the new log entry
   $logEntry = [
      'timestamp' => date('Y-m-d H:i:s'),
      'who' => $who,
      'activity' => $activity,
   ];

   // File locking to prevent concurrent writes
   $fp = fopen($logFile, 'r+');
   if (!$fp) {
      error_log("Failed to open log file: $logFile");
      return false;
   }

   // Exclusive lock
   if (flock($fp, LOCK_EX)) {
      try {
         // Read existing content
         $content = '';
         while (!feof($fp)) {
            $content .= fread($fp, 8192);
         }

         // Decode existing logs with error checking
         $existingLogs = json_decode($content, true);
         
         // Validate JSON structure
         if ($existingLogs === null && json_last_error() !== JSON_ERROR_NONE) {
            // JSON is corrupted, backup the file with timestamp
            $timestamp = date('Y-m-d_H-i-s');
            $backupFile = $logFolder . '/corrupted_logs_' . $timestamp . '.json';
            
            // Close the current file handle before renaming
            flock($fp, LOCK_UN);
            fclose($fp);
            
            // Backup the corrupted file
            if (rename($logFile, $backupFile)) {
                error_log("Corrupted log file backed up to: $backupFile");
            } else {
                error_log("Failed to backup corrupted log file");
            }
            
            // Create new logs.json file
            file_put_contents($logFile, '[]');
            
            // Reopen the new file
            $fp = fopen($logFile, 'r+');
            if (!$fp || !flock($fp, LOCK_EX)) {
                error_log("Failed to open new log file after backup");
                return false;
            }
            
            $existingLogs = [];
            error_log("Created new log file after detecting corruption");
         }

         // Ensure $existingLogs is an array
         if (!is_array($existingLogs)) {
            $existingLogs = [];
         }

         // Add new entry at the beginning
         array_unshift($existingLogs, $logEntry);

         // Truncate file and reset pointer
         ftruncate($fp, 0);
         rewind($fp);

         // Write updated content
         $jsonData = json_encode($existingLogs, JSON_PRETTY_PRINT);
         if ($jsonData === false) {
            error_log("JSON encoding failed: " . json_last_error_msg());
            return false;
         }

         fwrite($fp, $jsonData);
         
      } catch (Exception $e) {
         error_log("Error in logActivity: " . $e->getMessage());
         return false;
      }

      // Release the lock
      flock($fp, LOCK_UN);
   } else {
      error_log("Could not obtain lock on log file: $logFile");
      return false;
   }

   fclose($fp);
   return true;
}




?>


<?php
session_start();
ob_start(); // Start output buffering

@include 'config.php';

function handleLogin()
{
   global $conn;

   if (isset($_POST['submit'])) {
      $username = strtolower(mysqli_real_escape_string($conn, $_POST['username']));
      $password = $_POST['password'];
      $pass = md5($_POST['password']);

      $select = "SELECT * FROM users_db WHERE username = ? AND password = ?";
      $stmt = mysqli_prepare($conn, $select);
      mysqli_stmt_bind_param($stmt, "ss", $username, $pass);
      mysqli_stmt_execute($stmt);
      $result = mysqli_stmt_get_result($stmt);

      if (mysqli_num_rows($result) > 0) {
         $row = mysqli_fetch_array($result);

         // Handle remember me functionality
         if (!empty($_POST["remember"])) {
            setcookie("member_login", $username, time() + (4 * 24 * 60 * 60));
            setcookie("member_password", $password, time() + (4 * 24 * 60 * 60));
         } else {
            if (isset($_COOKIE["member_login"])) {
               setcookie("member_login", "", time() - 3600);
            }
            if (isset($_COOKIE["member_password"])) {
               setcookie("member_password", "", time() - 3600);
            }
         }

         // Handle different user permissions
         switch ($row['permissions']) {
            case 'admin':
               $_SESSION['admin_name'] = $row['name'];
               $_SESSION['admin_ID'] = $row['id'];
               logActivity('admin_logs', $_SESSION['admin_name'], 'Logged in');
               header('location:admin/');
               exit();
               break;

            case 'trainer':
               $_SESSION['trainer_name'] = $row['name'];
               $_SESSION['trainer_ID'] = $row['id'];
               logActivity('admin_logs', $_SESSION['trainer_name'], 'Logged in');
               header('location:trainer/');
               exit();
               break;

            case 'staff':
            case 'user':
               return [
                  'title' => 'Under Development',
                  'text' => 'This webpage is currently under development. Check back soon!',
                  'icon' => 'info'
               ];
               break;
         }
      } else {
         return [
            'title' => 'Error',
            'text' => 'Incorrect username or password!',
            'icon' => 'error'
         ];
      }
   }
   return null;
}

// Handle the login attempt
$alert = handleLogin();

// Now we can safely output any HTML and alerts
?>

<!DOCTYPE html>
<html lang="en">

<head>

   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>login</title>

   <!-- custom css file link  -->
   <link rel="shortcut icon" type="image/png" href="assets/logo.png" />
   <link rel="stylesheet" href="css/style.css">
   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   <style>
      /* @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600&display=swap');

*{
   font-family: 'Poppins', sans-serif;
  
 
} */

      #content {
         background-color: #333;
         height: fit-content;
         padding: 10px;
         width: 100%;
      }

      nav {
         display: flex;
         align-items: center;
         justify-content: space-between;
         padding: 0 20px;
         height: 100%;
         flex-wrap: wrap;
         gap: 10px;
      }

      .btn-s {
         display: flex;
         flex-wrap: wrap;
         align-items: center;
         justify-content: center;
         gap: 7px;
      }

      .text {
         color: white;
         text-align: center;
         flex: 1;
      }

      .text h3 {
         overflow: hidden;
         margin: 0;
         font-size: clamp(1rem, 4vw, 1.5rem);
         white-space: nowrap;
         text-overflow: ellipsis;
      }

      .profile img {
         width: 35px;
         height: 35px;
         border-radius: 50%;
      }

      .home-link {
         color: white;
         text-decoration: none;
         padding: 6px 12px;
         background-color: #0084ff;
         border-radius: 4px;
         transition: background-color 0.3s ease;
         font-size: 0.9rem;
         white-space: nowrap;
      }

      .home-link:hover {
         background-color: #0066cc;
      }

      .home-link:focus,
      .home-link:active {
         outline: none;
         background-color: #004499;
      }

      .home-link,
      .home-link:hover,
      .home-link:focus,
      .home-link:active {
         text-decoration: none;
      }

      @media screen and (max-width: 480px) {
         nav {
            padding: 0 10px;
            justify-content: center;
         }

         .text h3 {
            font-size: 1rem;
         }

         .home-link {
            padding: 4px 8px;
            font-size: 0.8rem;
         }

         .profile img {
            width: 30px;
            height: 30px;
         }
      }




      :root {
         --primary-color: #1e293b;
         /* Dark Blue-Gray */
         --secondary-color: #334155;
         /* Slate Gray */
         --accent-color: #64748b;
         /* Steel Blue */
         --background-color: #0f172a;
         /* Midnight Blue */
         --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
         /* Deeper shadow for darker theme */

         /* Additional background colors */
         --background-light: #1c2a39;
         /* Slightly lighter background for contrast */
         --background-dark: #0d1117;
         /* Ultra dark for headers/footers */
         --background-muted: #252f3f;
         /* Muted tone for sections */
         --background-accent: #16222e;
         /* Subtle accent background */
      }

      /* Navbar container */
      #content {
         position: sticky;
         top: 0;
         z-index: 1000;
         padding: 0;
         background-color: white;
         box-shadow: var(--card-shadow);
      }

      nav {
         padding: 1rem;
         background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
         /* border-radius: 0 0 1rem 1rem; */
      }

      .navbar-container {
         display: flex;
         justify-content: space-between;
         align-items: center;
         max-width: 1400px;
         margin: 0 auto;
         padding: 0 1rem;
      }

      .btn-s {
         display: flex;
         gap: 1rem;
      }

      .home-link {
         color: white;
         text-decoration: none;
         padding: 0.5rem 1rem;
         border-radius: 0.5rem;
         transition: all 0.3s ease;
         background-color: rgba(255, 255, 255, 0.1);
         backdrop-filter: blur(10px);
      }

      .home-link:hover {
         background-color: rgba(255, 255, 255, 0.2);
         transform: translateY(-2px);
      }

      .text h3 {
         color: white;
         margin: 0;
         font-size: 1.5rem;
         font-weight: 600;
      }


      @media (max-width: 768px) {
         .navbar-container {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
         }

         .btn-s {
            width: 100%;
            justify-content: center;
         }

      }

      @media (max-width: 480px) {
         .home-link {
            padding: 0.5rem;
            font-size: 0.875rem;
         }

         .text h3 {
            font-size: 1.25rem;
         }

         .profile {
            display: none;
         }
      }
   </style>



</head>

<body>

   <?php if ($alert): ?>
      <script>
         Swal.fire({
            title: '<?php echo $alert['title']; ?>',
            text: '<?php echo $alert['text']; ?>',
            icon: '<?php echo $alert['icon']; ?>',
            confirmButtonText: 'OK'
         });
      </script>
   <?php endif; ?>

   <?php

   include('includes/headerlvl1.php');

   ?>


   <style>
      :root {
         --primary-color: #1e293b;
         /* Dark Blue-Gray */
         --secondary-color: #334155;
         /* Slate Gray */
         --accent-color: #64748b;
         /* Steel Blue */
         --background-color: #0f172a;
         /* Midnight Blue */
         --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
         /* Deeper shadow for darker theme */

         /* Additional background colors */
         --background-light: #1c2a39;
         /* Slightly lighter background for contrast */
         --background-dark: #0d1117;
         /* Ultra dark for headers/footers */
         --background-muted: #252f3f;
         /* Muted tone for sections */
         --background-accent: #16222e;
         /* Subtle accent background */
      }

      /* Navbar container */
      #content {
         position: sticky;
         top: 0;
         z-index: 1000;
         padding: 0;
         background-color: white;
         box-shadow: var(--card-shadow);
      }

      nav {
         padding: 1rem;
         background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
         /* border-radius: 0 0 1rem 1rem; */
      }

      .navbar-container {
         display: flex;
         justify-content: space-between;
         align-items: center;
         max-width: 1400px;
         margin: 0 auto;
         padding: 0 1rem;
      }

      .btn-s {
         display: flex;
         gap: 1rem;
      }

      .home-link {
         color: white;
         text-decoration: none;
         padding: 0.5rem 1rem;
         border-radius: 0.5rem;
         transition: all 0.3s ease;
         background-color: rgba(255, 255, 255, 0.1);
         backdrop-filter: blur(10px);
      }

      .home-link:hover {
         background-color: rgba(255, 255, 255, 0.2);
         transform: translateY(-2px);
      }

      .text h3 {
         color: white;
         margin: 0;
         font-size: 1.5rem;
         font-weight: 600;
      }


      @media (max-width: 768px) {
         .navbar-container {
            flex-direction: column;
            gap: 1rem;
            text-align: center;
         }

         .btn-s {
            width: 100%;
            justify-content: center;
         }

      }

      @media (max-width: 480px) {
         .home-link {
            padding: 0.5rem;
            font-size: 0.875rem;
         }

         .text h3 {
            font-size: 1rem;
         }

         .profile {
            display: none;
         }
      }
   </style>



   <div class="form-container">

      <form action="" method="post">
         <h3>login now</h3>
         <?php
         if (isset($error)) {
            foreach ($error as $error) {
               echo '<span class="error-msg">' . $error . '</span>';
            };
         };
         ?>
         <input type="text" name="username" required placeholder="enter your username" value="<?php if (isset($_COOKIE["member_login"])) {
                                                                                                   echo $_COOKIE["member_login"];
                                                                                                } ?>">
         <input type="password" name="password" required placeholder="enter your password" value="<?php if (isset($_COOKIE["member_password"])) {
                                                                                                      echo $_COOKIE["member_password"];
                                                                                                   } ?>">
         <div class="remember" style=" 
                           display: flex;
                           flex-direction: row;
                           flex-wrap: wrap;
         ">
            <input style="width: 40px;" type="checkbox" name="remember" <?php if (isset($_COOKIE["member_login"])) { ?>
               checked <?php } ?> />
            <label for="remember-me">Remember me</label>
         </div>
         <input id="LoginBtn" type="submit" name="submit" value="login now" class="form-btn">
         <!-- <p>don't have an account? <a href="register_form.php">register now</a></p> -->
      </form>

   </div>

   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

   <script>
      function checkInternetConnection() {
         if (!navigator.onLine) {
            alert("Please connect to the internet.");
         } else {
            //   console.log("You are online.");
         }
      }


      window.onload = checkInternetConnection;


      window.addEventListener('online', checkInternetConnection);


      window.addEventListener('offline', function() {
         alert("You are offline. Please connect to the internet.");
         document.getElementById('LoginBtn').disabled = true;
         document.getElementById('LoginBtn').style.backgroundColor = 'gray';
         document.getElementById('LoginBtn').style.cursor = 'not-allowed';
      });
   </script>



   <script>
      // Initialize deferredPrompt for use later to show browser install prompt
      let deferredPrompt;

      // Custom install button container and styling
      const installContainer = document.createElement('div');
      installContainer.style.position = 'fixed';
      installContainer.style.bottom = '20px';
      installContainer.style.left = '50%';
      installContainer.style.transform = 'translateX(-50%)';
      installContainer.style.zIndex = '9999';
      installContainer.style.display = 'none';

      // Create custom install button
      const installButton = document.createElement('button');
      installButton.textContent = 'Install App';
      installButton.style.background = 'linear-gradient(45deg, #46abcc, #3d91ad)';
      installButton.style.color = 'white';
      installButton.style.padding = '12px 24px';
      installButton.style.border = 'none';
      installButton.style.borderRadius = '30px';
      installButton.style.fontSize = '16px';
      installButton.style.fontWeight = '600';
      installButton.style.cursor = 'pointer';
      installButton.style.boxShadow = '0 4px 15px rgba(70, 171, 204, 0.3)';
      installButton.style.transition = 'all 0.3s ease';

      // Add hover effects
      installButton.onmouseover = () => {
         installButton.style.background = 'linear-gradient(45deg, #3d91ad, #46abcc)';
         installButton.style.transform = 'translateY(-2px)';
         installButton.style.boxShadow = '0 6px 20px rgba(70, 171, 204, 0.5)';
      };

      installButton.onmouseout = () => {
         installButton.style.transform = 'translateY(0)';
         installButton.style.boxShadow = '0 4px 15px rgba(70, 171, 204, 0.3)';
      };

      installContainer.appendChild(installButton);
      document.body.appendChild(installContainer);

      window.addEventListener('beforeinstallprompt', (e) => {
         // Prevent Chrome 67 and earlier from automatically showing the prompt
         e.preventDefault();
         // Stash the event so it can be triggered later
         deferredPrompt = e;
         // Show the install button
         installContainer.style.display = 'block';
      });

      installButton.addEventListener('click', async () => {
         if (deferredPrompt) {
            // Show the install prompt
            deferredPrompt.prompt();
            // Wait for the user to respond to the prompt
            const {
               outcome
            } = await deferredPrompt.userChoice;
            // Clear the deferredPrompt variable
            deferredPrompt = null;
            // Hide the install button
            installContainer.style.display = 'none';
         }
      });

      // Hide button if app is already installed
      window.addEventListener('appinstalled', () => {
         installContainer.style.display = 'none';
         deferredPrompt = null;
      });
   </script>



   <script>
      // Add styles for the notification
      const orientationStyle = document.createElement('style');
      orientationStyle.textContent = `
    .orientation-notice {
        position: fixed;
        bottom: -300px;
        left: 50%;
        transform: translateX(-50%);
        background: linear-gradient(135deg, rgba(0,0,0,0.95), rgba(20,20,20,0.95));
        color: white;
        padding: 15px 25px; /* Reduced padding */
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 12px; /* Reduced gap */
        z-index: 9999;
        box-shadow: 0 8px 25px rgba(0,0,0,0.4);
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        font-family: 'Poppins', sans-serif;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.1);
        font-size: 14px; /* Added smaller font size */
    }

    .orientation-notice.show {
        bottom: 30px;
        animation: bounce 1.2s cubic-bezier(0.36, 0, 0.66, 1);
    }

    .close-btn {
        position: absolute;
        top: 8px; /* Adjusted position */
        right: 8px;
        background: none;
        border: none;
        color: white;
        cursor: pointer;
        padding: 4px; /* Reduced padding */
        font-size: 16px; /* Reduced font size */
        opacity: 0.7;
        transition: opacity 0.3s;
    }

    .close-btn:hover {
        opacity: 1;
    }

    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% {
            transform: translateX(-50%) translateY(0);
        }
        40% {
            transform: translateX(-50%) translateY(-12px); /* Reduced bounce height */
        }
        60% {
            transform: translateX(-50%) translateY(-6px);
        }
    }

    .rotate-icon {
        width: 28px; /* Reduced icon size */
        height: 28px;
        animation: rotate 2.5s infinite cubic-bezier(0.4, 0, 0.2, 1);
        filter: drop-shadow(0 0 8px rgba(255,255,255,0.3));
    }

    @keyframes rotate {
        0% { transform: rotate(0deg) scale(1); }
        50% { transform: rotate(90deg) scale(1.1); }
        100% { transform: rotate(0deg) scale(1); }
    }
`;
      document.head.appendChild(orientationStyle);

      // Create and add the notification element
      const notice = document.createElement('div');
      notice.className = 'orientation-notice';
      notice.innerHTML = `
    <svg class="rotate-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white">
        <path d="M16.48 2.52c3.27 1.55 5.61 4.72 5.97 8.48h1.5C23.44 4.84 18.29 0 12 0l-.66.03 3.81 3.81 1.33-1.32zm-6.25-.77c-.59-.59-1.54-.59-2.12 0L1.75 8.11c-.59.59-.59 1.54 0 2.12l12.02 12.02c.59.59 1.54.59 2.12 0l6.36-6.36c.59-.59.59-1.54 0-2.12L10.23 1.75zm4.6 19.44L2.81 9.17l6.36-6.36 12.02 12.02-6.36 6.36zm-7.31.29C4.25 19.94 1.91 16.76 1.55 13H.05C.56 19.16 5.71 24 12 24l.66-.03-3.81-3.81-1.33 1.32z"/>
    </svg>
    <span>Please rotate your device to landscape mode for better experience</span>
    <button class="close-btn">&times;</button>
`;
      document.body.appendChild(notice);

      // Check if notification was already shown and when
      const getLastShownTime = () => {
         return parseInt(localStorage.getItem('orientationNoticeLastShown') || '0');
      };

      // Show notification only on mobile devices in portrait mode
      function checkOrientation() {
         const currentTime = Date.now();
         const lastShownTime = getLastShownTime();
         const fiveMinutes = 5 * 60 * 1000; // 5 minutes in milliseconds
         //  const fiveMinutes = 10 * 1000; // 5 minutes in milliseconds

         if (window.innerWidth < 768 && window.innerHeight > window.innerWidth) {
            // Show if never shown or if 5 minutes have passed since last shown
            if (!lastShownTime || (currentTime - lastShownTime) >= fiveMinutes) {
               notice.classList.add('show');
               localStorage.setItem('orientationNoticeLastShown', currentTime.toString());
            }
         }
      }

      // Close button handler
      const closeBtn = notice.querySelector('.close-btn');
      closeBtn.addEventListener('click', () => {
         notice.classList.remove('show');
      });

      // Check on load and orientation change
      window.addEventListener('load', checkOrientation);
      window.addEventListener('orientationchange', checkOrientation);
   </script>


</body>

</html>