<?php

$imageFilePath = '../../../addmission_pdf/mailImage.png'; 


$uploadUrl = 'https://chaoschatroom.000webhostapp.com/api/upload.php'; 


$ch = curl_init($uploadUrl);


$imageFile = new CURLFile($imageFilePath, mime_content_type($imageFilePath), basename($imageFilePath));


$postData = [
    'image' => $imageFile,
];

curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);


curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


$response = curl_exec($ch);


if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    
    echo $response;
}


curl_close($ch);
?>

