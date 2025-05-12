<?php

include("../config.php");

echo doUsernameExist($_POST['username'],$conn);

function doUsernameExist($username,$conn){

        $sql = "SELECT * FROM users_db WHERE username='$username'";
        $result = mysqli_query( $conn, $sql );
    
        if ($result->num_rows > 0) {
            $response = array(
                'exists' => true,
                'message' => 'Username Not Available'
            );
    
            $jsonResponse = json_encode($response);
    
            header('Content-Type: application/json');
            return $jsonResponse;
        }

     
}
$conn->close();
?>