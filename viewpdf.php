<?php


@include 'config.php';

if(!isset($_GET['cust_uid'])){
include('./includes/authenticationAdminOrStaff.php');
authenticationAdminOrStaff();

}
$id = $_GET['id'];
$email = $_GET['email'];
$name = $_GET['name'];




$select = "SELECT * FROM `cust_details` WHERE phone = '$id' AND email = '$email' AND name = '$name'";

$result = mysqli_query($conn, $select);
// $row = mysqli_fetch_assoc($result);
// echo $row['name'];
// echo $row['phone'];
// echo $row['email'];
// echo $row['address'];

// if (mysqli_num_rows($result)) {
//   echo "yes";
// }else{
//   echo "no";
// }


if ($result) {

  $row = mysqli_fetch_assoc($result);


  if ($row) {

    // Personal Details
    $cust_uid = $row['cust_uid'];



    $name = $row['name'];
    $phone = $row['phone'];
    $email = $row['email'];
    $address = $row['address'];

    // Booking Details
    $date = $row['date'];
    $time = $row['time'];
    $days = $row['days'];
    $Tname = $row['trainername'];
    $Tnum = $row['trainerphone'];
    $totalA = $row['totalamount'];
    $paidA = $row['paidamount'];
    $dueA = $row['dueamount'];
    $timeslot = $row['timeslot'];



    $startedAT = $row['startedAT'];
    $endedAT = $row['endedAT'];



    $string = $row['vehicle'];
    $parts = explode("/", $string);

    // Split first part into vehicle type and name
    $firstPart = trim($parts[0]);
    $words = explode(" ", $firstPart);
    
    // Find position of "Wheeler" to separate vehicle type from name
    $wheelerPos = array_search('Wheeler', $words);
    
    if ($wheelerPos !== false) {
        // Join words up to and including "Wheeler" for var1
        $var1 = implode(" ", array_slice($words, 0, $wheelerPos + 1));
        // Join remaining words for var2
        $var2 = implode(" ", array_slice($words, $wheelerPos + 1));
    } else {
        // Fallback if "Wheeler" not found
        $var1 = $words[0];
        $var2 = implode(" ", array_slice($words, 1));
    }
    
    $firstPart = $var1; // Will contain "Four Wheeler"
    $VN = $var2; // Will contain "Toyota Liva"



  } else {
    // echo "No rows found.";
  }

  mysqli_free_result($result);
} else {
  echo "Error executing the query: " . mysqli_error($conn);
}


?>

<!DOCTYPE html>
<html>

<head>

  <link rel="shortcut icon" type="image/png" href="assets/logo.png" />

  <style>
    @page {
      size: A4 portrait;
      margin: 0;
      overflow: hidden;
    }

    html,
    body {
      width: 210mm;
      height: 297mm;
      margin: 0;
      padding: 0;
      position: relative;
      background-color: #252525;
    }

    .container {
      position: relative;
      z-index: 1;
      width: 100%;
      height: 100%;
    }

    .form-style {
      font-family: 'Poppins', sans-serif;
      width: 410px;
      padding: 10px;
      color: black;
      text-align: start;
      background-color: transparent;
    }

    .container .form-name {
      position: absolute;
      top: 186px;
      left: 154px;
    }

    .container .form-number {

      position: absolute;
      top: 224px;
      left: 217px;
    }

    .container .form-email {

      position: absolute;
      top: 258px;
      left: 111px;
      width: 600px;


    }

    .container .form-address {

      position: absolute;
      top: 298px;
      left: 144px;
      width: 550px;


    }

    .container .form-data-time {

      position: absolute;
      top: 413px;
      left: 135px;

    }

    .container .form-data {

      position: absolute;
      top: 421px;
      left: 142px;

    }

    .container .form-time {

      position: absolute;
      top: 422px;
      left: 449px;

    }

    .container .form-days {

      font-size: 20px;
      position: absolute;
      top: 657px;
      left: 83px;

    }

    .container .form-totalA {
      font-size: 15.5px;
      position: absolute;

      top: 755px;
      left: 129px;

    }

    .container .form-paidA {
      font-size: 15.5px;
      position: absolute;
      top: 755px;
      left: 370px;


    }

    .container .form-dueA {
      font-size: 15.5px;
      position: absolute;
      top: 755px;
      left: 604px;


    }

    .container .form-signature {

      position: absolute;
      top: 960px;
      left: 60px;

    }

    .container .form-signature img {
      width: 210px;
      height: 100px;
    }


    .container .form-Tname {
      position: absolute;
      top: 580px;
      left: 111px;

    }

    .container .form-Tnumber {
      position: absolute;
      top: 581px;
      left: 468px;
    }


    .container .form-Vname {
      position: absolute;
      top: 499px;
      left: 156px;

    }


    .container .form-Vdetails {
      position: absolute;
      font-size: 15px;
      top: 501px;
      left: 450px;
    }

    .background-image {
      position: absolute;
      top: 0;
      left: 0;
      z-index: 0;
      width: 100%;
      height: 100%;
      pointer-events: none;
    }

    .load {
      width: 500px;
      height: 500px;
      background-color: transparent;
      position: absolute;
      top: 349px;
      left: 999px;
      flex-direction: column;
      align-items: center;
      gap: 25px;
      display: flex;
    }



    /*  */

    /* Typewriter */
    .typewriter {
      --blue: #5C86FF;
      --blue-dark: #275EFE;
      --key: #fff;
      --paper: #EEF0FD;
      --text: #D3D4EC;
      --tool: #FBC56C;
      --duration: 3s;
      position: relative;
      -webkit-animation: bounce05 var(--duration) linear infinite;
      animation: bounce05 var(--duration) linear infinite;
      margin-right: 28px
    }

    .typewriter .slide {
      width: 92px;
      height: 20px;
      border-radius: 3px;
      margin-left: 14px;
      transform: translateX(14px);
      background: linear-gradient(var(--blue), var(--blue-dark));
      -webkit-animation: slide05 var(--duration) ease infinite;
      animation: slide05 var(--duration) ease infinite;
    }

    .typewriter .slide:before,
    .typewriter .slide:after,
    .typewriter .slide i:before {
      content: "";
      position: absolute;
      background: var(--tool);
    }

    .typewriter .slide:before {
      width: 2px;
      height: 8px;
      top: 6px;
      left: 100%;
    }

    .typewriter .slide:after {
      left: 94px;
      top: 3px;
      height: 14px;
      width: 6px;
      border-radius: 3px;
    }

    .typewriter .slide i {
      display: block;
      position: absolute;
      right: 100%;
      width: 6px;
      height: 4px;
      top: 4px;
      background: var(--tool);
    }

    .typewriter .slide i:before {
      right: 100%;
      top: -2px;
      width: 4px;
      border-radius: 2px;
      height: 14px;
    }

    .typewriter .paper {
      position: absolute;
      left: 24px;
      top: -26px;
      width: 40px;
      height: 46px;
      border-radius: 5px;
      background: var(--paper);
      transform: translateY(46px);
      -webkit-animation: paper05 var(--duration) linear infinite;
      animation: paper05 var(--duration) linear infinite;
    }

    .typewriter .paper:before {
      content: "";
      position: absolute;
      left: 6px;
      right: 6px;
      top: 7px;
      border-radius: 2px;
      height: 4px;
      transform: scaleY(0.8);
      background: var(--text);
      box-shadow: 0 12px 0 var(--text), 0 24px 0 var(--text), 0 36px 0 var(--text);
    }

    .typewriter .keyboard {
      width: 120px;
      height: 56px;
      margin-top: -10px;
      z-index: 1;
      position: relative;
    }

    .typewriter .keyboard:before,
    .typewriter .keyboard:after {
      content: "";
      position: absolute;
    }

    .typewriter .keyboard:before {
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      border-radius: 7px;
      background: linear-gradient(135deg, var(--blue), var(--blue-dark));
      transform: perspective(10px) rotateX(2deg);
      transform-origin: 50% 100%;
    }

    .typewriter .keyboard:after {
      left: 2px;
      top: 25px;
      width: 11px;
      height: 4px;
      border-radius: 2px;
      box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
      -webkit-animation: keyboard05 var(--duration) linear infinite;
      animation: keyboard05 var(--duration) linear infinite;
    }

    @keyframes bounce05 {

      85%,
      92%,
      100% {
        transform: translateY(0);
      }

      89% {
        transform: translateY(-4px);
      }

      95% {
        transform: translateY(2px);
      }
    }

    @keyframes slide05 {
      5% {
        transform: translateX(14px);
      }

      15%,
      30% {
        transform: translateX(6px);
      }

      40%,
      55% {
        transform: translateX(0);
      }

      65%,
      70% {
        transform: translateX(-4px);
      }

      80%,
      89% {
        transform: translateX(-12px);
      }

      100% {
        transform: translateX(14px);
      }
    }

    @keyframes paper05 {
      5% {
        transform: translateY(46px);
      }

      20%,
      30% {
        transform: translateY(34px);
      }

      40%,
      55% {
        transform: translateY(22px);
      }

      65%,
      70% {
        transform: translateY(10px);
      }

      80%,
      85% {
        transform: translateY(0);
      }

      92%,
      100% {
        transform: translateY(46px);
      }
    }

    @keyframes keyboard05 {

      5%,
      12%,
      21%,
      30%,
      39%,
      48%,
      57%,
      66%,
      75%,
      84% {
        box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
      }

      9% {
        box-shadow: 15px 2px 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
      }

      18% {
        box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 2px 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
      }

      27% {
        box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 12px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
      }

      36% {
        box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 12px 0 var(--key), 60px 12px 0 var(--key), 68px 12px 0 var(--key), 83px 10px 0 var(--key);
      }

      45% {
        box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 2px 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
      }

      54% {
        box-shadow: 15px 0 0 var(--key), 30px 2px 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
      }

      63% {
        box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 12px 0 var(--key);
      }

      72% {
        box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 2px 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 10px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
      }

      81% {
        box-shadow: 15px 0 0 var(--key), 30px 0 0 var(--key), 45px 0 0 var(--key), 60px 0 0 var(--key), 75px 0 0 var(--key), 90px 0 0 var(--key), 22px 10px 0 var(--key), 37px 12px 0 var(--key), 52px 10px 0 var(--key), 60px 10px 0 var(--key), 68px 10px 0 var(--key), 83px 10px 0 var(--key);
      }
    }

    /*  */

    .spinner {
      height: 50px;
      width: max-content;
      font-size: 18px;
      font-weight: 600;
      font-family: monospace;
      letter-spacing: 1em;
      color: #f5f5f5;
      filter: drop-shadow(0 0 10px);
      display: flex;
      justify-content: center;
      align-items: center;
    }

    .spinner span {
      animation: loading6454 1.75s ease infinite;
    }

    .spinner span:nth-child(2) {
      animation-delay: 0.25s;
    }

    .spinner span:nth-child(3) {
      animation-delay: 0.5s;
    }

    .spinner span:nth-child(4) {
      animation-delay: 0.75s;
    }

    .spinner span:nth-child(5) {
      animation-delay: 1s;
    }

    .spinner span:nth-child(6) {
      animation-delay: 1.25s;
    }

    .spinner span:nth-child(7) {
      animation-delay: 1.5s;
    }

    .spinner span:nth-child(8) {
      animation-delay: 1.75s;
    }

    .spinner span:nth-child(9) {
      animation-delay: 2s;
    }

    .spinner span:nth-child(10) {
      animation-delay: 2.25s;
    }

    @keyframes loading6454 {

      0%,
      100% {
        transform: translateY(0);
      }

      50% {
        transform: translateY(-10px);
      }
    }

    #redfont {
      color: red;
    }

    .vertical-text {
      writing-mode: vertical-rl;
      text-orientation: upright;
      white-space: nowrap;
    }

    .vert {
      width: 28mm;
      height: 297mm;
      position: absolute;
      background-color: gray;
      left: 792px;
      font-family: 'Impact', sans-serif;
      font-size: 32px;
      filter: drop-shadow(0 0 10px);
    }
  </style>
  <link href="https://fonts.googleapis.com/css?family=Impact&display=swap" rel="stylesheet">
</head>

<body>
  <div class="vert">
    <div class="vertical-text">
      <p style="color: #f5f5f5; filter: drop-shadow(0 0 10px);"><br><span style="color: red">PDF</span> Preview <span
          style="color: red">PDF</span> Preview <span style="color: red">PDF</span></p>
    </div>
  </div>
  <div class="load">
    <div class="typewriter">
      <div class="slide"><i></i></div>
      <div class="paper"></div>
      <div class="keyboard"></div>
    </div>
    <div class="spinner">
      <span>G</span>
      <span>E</span>
      <span>N</span>
      <span>E</span>
      <span>R</span>
      <span>A</span>
      <span>T</span>
      <span>I</span>
      <span>N</span>
      <span>G</span>
      <span id="redfont">P</span>
      <span id="redfont">D</span>
      <span id="redfont">F</span>
    </div>
  </div>
  </div>
  <div class="container">
    <strong class="form-style form-cust_uid" style="
    font-size: larger;
    left: 679px;
    top: 4px;
    position: absolute;
"><?php echo $cust_uid; ?>
    </strong>
    <strong class="form-style form-name">
      <?php echo $name; ?>
    </strong>
    <strong class="form-style form-number">
      <?php echo $phone; ?>
    </strong>
    <strong class="form-style form-email">
      <?php echo $email; ?>
    </strong>
    <strong class="form-style form-address">
      <?php echo $address; ?>
    </strong>
    <strong class="form-style form-data-time form-data">
      <?php echo $startedAT; ?>
    </strong>
    <strong class="form-style form-data-time form-time">
      <?php echo $timeslot; ?>
    </strong>
    <strong class="form-style form-days">
      <?php echo $days; ?>
    </strong>
    <strong class="form-style form-totalA">
      <?php echo $totalA; ?>
    </strong>
    <strong class="form-style form-paidA">
      <?php echo $paidA; ?>
    </strong>
    <strong class="form-style form-dueA">
      <?php echo $dueA; ?>
    </strong>
    <!-- <strong class="form-style form-signature"><img src="assets/signature.png"></strong> -->
    <strong class="form-style form-Tname">
      <?php echo $Tname; ?>
    </strong>
    <strong class="form-style form-Tnumber">
      <?php echo $Tnum; ?>
    </strong>
    <strong class="form-style form-Vname">
      <?php echo $firstPart; ?>
    </strong>
    <strong class="form-style form-Vdetails">
      <?php echo $VN; ?>
    </strong>


  </div>
  <img src="assets/form.png" class="background-image" style="image-resolution: 1000dpi;">


  <script>
    // let features = 'menubar=yes,location=yes,resizable=no,scrollbars=no,status=no';

    // function redirectAndClose() {
    //     let url = 'GeneratingPDF';
    //     let newWindow = window.open(url, '_blank', features);
    // }

    // var delay = 100;
    // setTimeout(redirectAndClose, delay);


    // function redirect() {
    //   window.location = "./viewGeneratedPDF.php?id=<?php echo $id; ?>&email=<?php echo $email; ?>&name=<?php echo $name; ?>&VN=<?php echo $VN; ?>&TT=<?php echo $TT; ?>&route=<?php echo urlencode((isset($_GET['route'])) ? $_GET['route'] : "admin/"); ?>&who=<?php echo (isset($_GET['who'])) ? $_GET['who'] : "0"; ?>";

    // }
    // var delay = 500;

    // setTimeout(redirect, delay);
  </script>



</body>

</html>