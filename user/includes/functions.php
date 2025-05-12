<?php

// Function to check if cookie is expired
function isCookieExpired($token)
{
   try {
      $userData = json_decode(base64_decode($token), true);
      if (!$userData || !isset($userData['timestamp'])) {
         return true;
      }

      // Check if more than 29 days have passed
      $expiryTime = $userData['timestamp'] + (29 * 24 * 60 * 60);
      return time() > $expiryTime;
   } catch (Exception $e) {
      return true;
   }
}






?>