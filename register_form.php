<?php

@include 'config.php';

if (isset($_POST['submit'])) {

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $username = strtolower(mysqli_real_escape_string($conn, $_POST['username']));
   $pass = md5($_POST['password']);
   $cpass = md5($_POST['cpassword']);


   $select = " SELECT * FROM users_db WHERE username = '$username' && password = '$pass' ";

   $result = mysqli_query($conn, $select);

   if (mysqli_num_rows($result) > 0) {

      $error[] = 'user already exist!';
   } else {

      if ($pass != $cpass) {
         $error[] = 'password not matched!';
      } else {

         $insert = "INSERT INTO users_db (name, username, password, time) VALUES('$name','$username','$pass',current_timestamp())";
         mysqli_query($conn, $insert);
         header('location:login_form.php');
      }
   }
};


?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>register form</title>

   <!-- custom css file link  -->
   <link rel="shortcut icon" type="image/png" href="assets/logo.png" />
   <link rel="stylesheet" href="css/navbar.css">
   <link rel="stylesheet" href="css/style.css">

</head>

<body>
   <?php

   include('includes/headerlvl1.php');

   ?>

   <div class="form-container">

      <form action="" method="post" id="registerForm1">
         <h3>register now</h3>
         <?php
         if (isset($error)) {
            foreach ($error as $error) {
               echo '<span class="error-msg">' . $error . '</span>';
            }
         }

         ?>

         <span id="error-msg" class="error-msg" style='display:none'></span>


         <input type="text" name="name" required placeholder="enter your name">
         <input type="text" name="username" id="username" required placeholder="enter your username">
         <input type="password" name="password" id="password" required placeholder="enter your password">
         <input type="password" name="cpassword" id="cpassword" required placeholder="confirm your password">
         <input type="submit" name="submit" value="register now" class="form-btn">
         <p>already have an account? <a href="login_form.php">login now</a></p>
      </form>

   </div>

   <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
   <script>
      $(document).ready(function() {
         $('#username').keyup(function() {
            var username = $(this).val();
            $.ajax({
               url: './api_ajax/check_username.php',
               method: 'POST',
               data: {
                  'username': username
               },
               success: function(data) {

                  if (data.exists) {
                     $('#error-msg').text(data.message);
                     $('#error-msg').css('display', 'block');
                  } else {
                     $('#error-msg').text('');
                     $('#error-msg').css('display', 'none');
                  }

               },
               error: function(xhr, status, error) {
                  console.error('Error:', error);
               }
            });
         });
      });
   </script>

   <script>
  

      var passwordInput = document.getElementById("password");
      var cpasswordInput = document.getElementById("cpassword");


      function validatePassword() {
         var password = passwordInput.value;
         var cpassword = cpasswordInput.value;


         if (password === "" && cpassword === "") {
            passwordInput.removeAttribute("style");
            cpasswordInput.removeAttribute("style");
            return;
         }

         if (password && cpassword) {
            passwordInput.removeAttribute("style");
            cpasswordInput.removeAttribute("style");
         }

         
         if (password !== cpassword && cpassword !== "") {
            passwordInput.setAttribute("style", "border:2px solid red")
            cpasswordInput.setAttribute("style", "border:2px solid red")
         } else if (password == cpassword) {
            passwordInput.setAttribute("style", "border:2px solid green")
            cpasswordInput.setAttribute("style", "border:2px solid green")
         }
      }


      passwordInput.addEventListener("input", validatePassword);
      cpasswordInput.addEventListener("input", validatePassword);
   </script>



</body>

</html>