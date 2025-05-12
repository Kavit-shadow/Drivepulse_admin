<?php
@include 'config.php';
session_start();

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



if (isset($_SESSION['admin_name'])) {
    logActivity('admin_logs', $_SESSION['admin_name'], 'Logged out');
} elseif (isset($_SESSION['trainer_name'])) {
    logActivity('admin_logs', $_SESSION['trainer_name'], 'Logged out');
} elseif (isset($_SESSION['staff_name'])) {
    logActivity('staff_logs', $_SESSION['staff_name'], 'Logged out');
}




session_unset();
session_destroy();

header('location:login_form.php');

?>